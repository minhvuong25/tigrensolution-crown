<?php

namespace Magedelight\SubscribenowPro\Model;

use Exception;
use Magedelight\Subscribenow\Logger\Logger;
use Magedelight\Subscribenow\Model\ProductSubscribersFactory;
use Magedelight\Subscribenow\Model\ProductSubscriptionHistory;
use Magedelight\Subscribenow\Model\ResourceModel\ProductSubscribers\CollectionFactory;
use Magedelight\Subscribenow\Model\Service\EmailService;
use Magedelight\Subscribenow\Model\Service\EmailServiceFactory;
use Magedelight\Subscribenow\Model\Service\Order\Generate;
use Magedelight\Subscribenow\Model\Service\OrderService;
use Magedelight\Subscribenow\Model\Service\PaymentService;
use Magedelight\Subscribenow\Model\Source\ProfileStatus;
use Magedelight\SubscribenowPro\Helper\Data;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Db_Select;

class MergeOrder
{
    const DEBUG_CART_ITEMS = false;
    const SKIP_UPDATE_NEXT_OCCURANCE_DATE = false;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var CollectionFactory
     */
    protected $subscriptionCollectionFactory;

    /**
     * @var ProductSubscribersFactory
     */
    protected $productSubscribersFactory;

    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var Generate
     */
    protected $orderGenerateService;

    /**
     * @var CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var EmailServiceFactory
     */
    protected $emailServiceFactory;

    /**
     * @var EncryptorInterface
     */
    protected $encrypt;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Data $helper
     * @param Logger $logger
     * @param TimezoneInterface $timezone
     * @param ProductSubscribersFactory $productSubscribersFactory
     * @param OrderService $orderService
     * @param Generate $orderGenerateService
     * @param CartManagementInterface $cartManagement
     * @param CartRepositoryInterface $cartRepository
     * @param PaymentService $paymentService
     * @param ManagerInterface $eventManager
     * @param EmailServiceFactory $emailServiceFactory
     * @param EncryptorInterface $encrypt
     * @param CurrencyFactory $currencyFactory
     */

    public function __construct(
        StoreManagerInterface $storeManager,
        Registry $registry,
        Data $helper,
        Logger $logger,
        TimezoneInterface $timezone,
        CollectionFactory $subscriptionCollectionFactory,
        ProductSubscribersFactory $productSubscribersFactory,
        OrderService $orderService,
        Generate $orderGenerateService,
        CartManagementInterface $cartManagement,
        CartRepositoryInterface $cartRepository,
        PaymentService $paymentService,
        ManagerInterface $eventManager,
        EmailServiceFactory $emailServiceFactory,
        EncryptorInterface $encrypt,
        CurrencyFactory $currencyFactory
    ) {
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->timezone = $timezone;
        $this->subscriptionCollectionFactory = $subscriptionCollectionFactory;
        $this->productSubscribersFactory = $productSubscribersFactory;
        $this->orderService = $orderService;
        $this->orderGenerateService = $orderGenerateService;
        $this->cartManagement = $cartManagement;
        $this->cartRepository = $cartRepository;
        $this->paymentService = $paymentService;
        $this->eventManager = $eventManager;
        $this->emailServiceFactory = $emailServiceFactory;
        $this->encrypt = $encrypt;
        $this->currencyFactory = $currencyFactory;
    }

    public function generate()
    {
        $this->logger->info("MergeOrder: generate order cron started...");

        $this->helper->setProcessingMergeOrder();

        $mergeSubscriptionsCollection = $this->getMergeSubscriptions();
        $this->logger->info("MergeOrder: " . count($mergeSubscriptionsCollection) . " orders to be generated.");

        if (count($mergeSubscriptionsCollection)) {
            $_SERVER['HTTP_USER_AGENT'] = '';
            $_SERVER['HTTP_ACCEPT'] = '';

            foreach ($mergeSubscriptionsCollection as $subscriptionGroup) {
                $this->unsetPaymentRegistryData();

                $customerId = $subscriptionGroup['customer_id'];
                $subscriptionIdCsv = $subscriptionGroup['subscription_id_csv'];
                $profileIdCsv = $subscriptionGroup['profile_id_csv'];
                $totalSubscriptions = $subscriptionGroup['total_subscriptions'];

                // original cron execution
                if ($totalSubscriptions == 1) {
                    try {
                        $subscription = $this->getSubscriptionById($subscriptionIdCsv);

                        $this->logger->info("MergeOrder: Process start for Order with single profile: #" . $subscription->getProfileId());

                        $this->orderService->createSubscriptionOrder($subscription, ProductSubscriptionHistory::HISTORY_BY_CRON);
                    } catch (Exception $e) {
                        $this->logger->info("MergeOrder: Error: " . $e->getMessage());
                        $this->logger->info("MergeOrder: Process end with error for subscription profile # " . $subscription->getProfileId());
                    }
                } else {
                    $this->storeManager->setCurrentStore($this->getStore($subscriptionGroup));

                    $this->logger->info("MergeOrder: Process start for subscription profiles({$totalSubscriptions}): " . $profileIdCsv);

                    $subscriptionIds = explode(',', $subscriptionIdCsv);

                    $this->orderGenerateService->currentQuote = $this->createEmptyCart($customerId);

                    $failedSubscriptions = $this->addSubscriptionGroupToCart($subscriptionIds);

                    try {
                        if ($failedSubscriptions) {
                            $subscriptionId = array_keys($failedSubscriptions)[0];
                            $subscription = $this->getSubscriptionById($subscriptionId);

                            if ($this->helper->failOrderIfFailAddtocart()) {
                                throw new LocalizedException(__(implode(', ', $failedSubscriptions)));
                            }
                        } else {
                            $subscription = $this->getSubscriptionById($subscriptionIds[0]);
                        }

                        $order = $this->processCart();
                        if ($order) {
                            $this->orderGenerateService->sendOrderEmail($order);
                            foreach ($subscriptionIds as $subscriptionId) {
                                if (array_key_exists($subscriptionId, $failedSubscriptions)) {
                                    continue;
                                }

                                $subscription = $this->getSubscriptionById($subscriptionId);
                                $subscription->setModifiedBy(ProductSubscriptionHistory::HISTORY_BY_CRON);

                                $subscription->setOrderIncrementId($order->getIncrementId());

                                if (self::SKIP_UPDATE_NEXT_OCCURANCE_DATE) {
                                    $this->logger->info("MergeOrder: Skipped Updating next_occurance_date");
                                } else {
                                    $subscription->afterSubscriptionCreate();
                                }

                                $subscription->save();
                            }

                            $this->orderGenerateService->setCurrentQuoteNull();
                        }

                        $this->logger->info("MergeOrder: Process successfully end for subscription profiles: " . $profileIdCsv);
                    } catch (LocalizedException $e) {
                        $this->orderFailed($subscription, $failedSubscriptions, $subscriptionGroup, $e);
                    } catch (Exception $e) {
                        $this->orderFailed($subscription, $failedSubscriptions, $subscriptionGroup, $e);
                    }
                }
            }
        }

        $this->logger->info("MergeOrder: generate order cron finished.");
    }

    public function getMergeSubscriptions()
    {
        //$today = $this->timezone->date(null, null, false)->format('Y-m-d H:i:s');
        $today = $this->timezone->date(null, null, false)->format('Y-m-d');

        $collection = $this->subscriptionCollectionFactory->create()
            //->addFieldToFilter('next_occurrence_date', ['lteq' => $today])
            ->addFieldToFilter('subscription_status', ProfileStatus::ACTIVE_STATUS);

        $collection->getSelect()
            ->where("DATE(next_occurrence_date) <= '$today'")
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(
                [
                    'customer_id',
                    'GROUP_CONCAT(subscription_id) AS subscription_id_csv',
                    'GROUP_CONCAT(profile_id) AS profile_id_csv',
                    "GROUP_CONCAT(IFNULL(payment_token, '')) AS payment_token_csv",
                    //'COUNT(subscription_id) AS total_subscriptions',
                    'store_id',
                    'currency_code'
                ]
            )
            ->group(
                [
                    'customer_id',
                    'store_id',
                    /*'billing_address_id',*/  /** we are not considering for merger order at 1.2.7*/
                    'shipping_address_id',
                    /*'shipping_method_code',*/ /** we are not considering for merger order at 1.2.7*/
                    'payment_method_code',
                    //'payment_token',
                    'currency_code'
                ]
            );
        $query = $collection->getSelect()->__toString();
        //echo $query;exit;
        //$this->logger->info("MergeOrder: query:" . $query);

        $collection = $this->splitMergeSubscriptionByToken($collection);
        //echo '<pre>';print_r($collection);exit;
        return $collection;
    }

    /**
     * Since magento is encrypting token with new value each time, db has different encrypted token for same value, so we need to decrypt token once collection has been fetched
     */
    public function splitMergeSubscriptionByToken($collection)
    {
        $resultCollection = [];

        foreach ($collection as $group) {
            $subscriptionIdCsv = explode(',', $group->getSubscriptionIdCsv());
            $profileIdCsv = explode(',', $group->getProfileIdCsv());
            $paymentTokenCsv = explode(',', $group->getPaymentTokenCsv());

            $splitTokenCollection = [];
            foreach ($paymentTokenCsv as $key => $token) {
                if (!empty($token)) {
                    $token = $this->encrypt->decrypt($token);
                }

                if (!isset($splitTokenCollection[$token])) {
                    $splitTokenCollection[$token] = [
                        'customer_id'         => $group->getCustomerId(),
                        'store_id'            => $group->getStoreId(),
                        'currency_code'       => $group->getCurrencyCode(),
                        'total_subscriptions' => 0,

                        'subscription_id_csv' => [],
                        'profile_id_csv'      => []
                    ];
                }

                $splitTokenCollection[$token]['subscription_id_csv'][] = $subscriptionIdCsv[$key];
                $splitTokenCollection[$token]['profile_id_csv'][] = $profileIdCsv[$key];
                $splitTokenCollection[$token]['total_subscriptions']++;
            }

            foreach ($splitTokenCollection as $tokenCollection) {
                $tokenCollection['subscription_id_csv'] = implode(',', $tokenCollection['subscription_id_csv']);
                $tokenCollection['profile_id_csv'] = implode(',', $tokenCollection['profile_id_csv']);

                $resultCollection[] = $tokenCollection;
            }
        }

        return $resultCollection;
    }

    public function addSubscriptionGroupToCart($subscriptionIds)
    {
        $failedSubscriptions = [];
        foreach ($subscriptionIds as $subscriptionId) {
            $subscription = $this->getSubscriptionById($subscriptionId);

            $this->logger->info(__("MergeOrder: Process start for adding product(s) to cart for subscription profile #%1", $subscription->getProfileId()));

            try {
                $this->orderGenerateService->setProfile($subscription)->addProductToCart($this->orderGenerateService->getCurrentQuote());
            } catch (LocalizedException $e) {
                $failedSubscriptions[$subscriptionId] = $e->getMessage();
                $this->subscriptionFailed($subscription, $e);
            } catch (Exception $e) {
                $failedSubscriptions[$subscriptionId] = $e->getMessage();
                $this->subscriptionFailed($subscription, $e);
            }

            $this->orderGenerateService->getCurrentQuote()->save();
        }

        return $failedSubscriptions;
    }

    public function getSubscriptionById($subscriptionId)
    {
        return $this->productSubscribersFactory->create()->load($subscriptionId);
    }

    public function getStore($subscriptionGroup)
    {
        $currency = $this->currencyFactory->create()->load($subscriptionGroup['currency_code']);

        return $this->storeManager
            ->getStore($subscriptionGroup['store_id'])
            ->setCurrentCurrency($currency)
            ->setCurrentCurrencyCode($subscriptionGroup['currency_code']);
    }

    public function processCart()
    {
        $this->removeItemsWithErrorFromCart();
        $this->debugCartItems();

        $cart = $this->orderGenerateService->getCurrentQuote();
        $cart
            ->setCustomer($this->orderGenerateService->getCustomer()->getDataModel())
            ->setCustomerEmail($this->orderGenerateService->getCustomer()->getEmail());

        $cart->getBillingAddress()->addData($this->orderGenerateService->getProfileBillingAddress());
        $cart->getShippingAddress()->addData($this->orderGenerateService->getProfileShippingAddress());

        if (!$cart->isVirtual()) {
            $cart->getShippingAddress()
                ->setShippingMethod($this->orderGenerateService->getProfile()->getShippingMethodCode())
                ->setCollectShippingRates(true);
        }

        $cart->setPaymentMethod($this->orderGenerateService->getProfile()->getPaymentMethodCode());

        $cart->setSubscriptionParentId($this->orderGenerateService->getProfile()->getId());

        $payment = $this->paymentService->getBySubscription($this->orderGenerateService->getProfile());

        $this->eventManager->dispatch(
            'subscribenow_subscription_recurrence_before_submit',
            ['quote' => $cart, 'profile' => $this->orderGenerateService->getProfile(), 'product' => $this->orderGenerateService->getProduct()]
        );

        $cart->collectTotals()->save();

        if ($this->orderGenerateService->getProfile()->getPaymentMethodCode() == 'magedelight_ewallet') {
            if (!$payment->checkBalance($cart->getGrandTotal())) {
                throw new LocalizedException(__('Insufficient funds in wallet'));
            }

            $this->orderGenerateService->deductAmountFromWallet($cart);
        }

        $cart->getPayment()->importData($payment->getImportData());

        $order = $this->cartManagement->submit($cart);

        if (null == $order) {
            throw new LocalizedException(__('An error occurred on placing the order.'));
        }

        return $order;
    }

    public function removeItemsWithErrorFromCart()
    {
        $cart = $this->orderGenerateService->getCurrentQuote();
        $items = $this->orderGenerateService->getCurrentQuote()->getAllVisibleItems();

        if ($items) {
            foreach ($items as $item) {
                if ($item->getHasError()) {
                    //$item->delete();
                    $cart->removeItem($item->getId())->save();
                }
            }
        }

        $cart->setHasError(false)->save();
    }

    public function debugCartItems()
    {
        if (self::DEBUG_CART_ITEMS) {
            $this->logger->info('Cart ID: ' . $this->orderGenerateService->getCurrentQuote()->getId());
            $items = $this->orderGenerateService->getCurrentQuote()->getAllVisibleItems();
            if ($items) {
                foreach ($items as $item) {
                    $this->logger->info('Cart Item Product Name: ' . $item->getName());
                }
            }
        }
    }

    public function createEmptyCart($customerId)
    {
        $cartId = $this->cartManagement->createEmptyCart($customerId);
        return $this->cartRepository->get($cartId);
    }

    public function subscriptionFailed($failedSubscription, $e)
    {
        if (!$this->helper->failOrderIfFailAddtocart()) {
            $modifiedBy = ProductSubscriptionHistory::HISTORY_BY_CRON;

            $this->logger->info(__("MergeOrder: There was an error adding subscription product(s) to cart for profile #%1", $failedSubscription->getProfileId()));
            $this->logger->info("MergeOrder: Error: " . $e->getMessage());
            $this->logger->info("MergeOrder: Process end with error for subscription profile: " . $failedSubscription->getProfileId());

            $comment = __("There was an error adding subscription product(s) to cart for profile #%1. ", $failedSubscription->getProfileId());
            $comment .= "Error: " . $e->getMessage();
            $failedSubscription->addHistory($modifiedBy, $comment);
            $failedSubscription->updateSubscriptionFailedCount();

            $this->sendSubscriptionFailedEmail($failedSubscription, [], $comment);
        }

        $this->eventManager->dispatch(
            'subscription_failed_addtocart',
            [
                'subscription' => $failedSubscription,
                'quote' => $this->orderGenerateService->getCurrentQuote()
            ]
        );
    }

    public function orderFailed($failedSubscription, $failedSubscriptions, $subscriptionGroup, $e)
    {
        $modifiedBy = ProductSubscriptionHistory::HISTORY_BY_CRON;

        $subscriptionIdCsv = $subscriptionGroup['subscription_id_csv'];
        $profileIdCsv = $subscriptionGroup['profile_id_csv'];

        $this->logger->info(__("MergeOrder: There was an error when generating subscription order for profile #%1", $failedSubscription->getProfileId()));
        $this->logger->info("MergeOrder: Error: " . $e->getMessage());
        $this->logger->info("MergeOrder: Process end with error for subscription profiles: " . $profileIdCsv);

        $subscriptionIds = explode(',', $subscriptionIdCsv);
        foreach ($subscriptionIds as $subscriptionId) {
            if (!$this->helper->failOrderIfFailAddtocart()) {
                if (array_key_exists($subscriptionId, $failedSubscriptions)) {
                    continue;
                }
            }

            $subscription = $this->getSubscriptionById($subscriptionId);
            $comment = __("There was an error when generating subscription order for profile #%1. ", $failedSubscription->getProfileId());
            $comment .= __("There was an error when generating subscription order for profiles %1. ", implode(', ', explode(',', $profileIdCsv)));
            $comment .= "Error: " . $e->getMessage();
            $subscription->addHistory($modifiedBy, $comment);
            $subscription->updateSubscriptionFailedCount();
        }

        $this->removeCurrentQuote();
        if ($this->helper->failOrderIfFailAddtocart()) {
            $this->sendSubscriptionFailedEmail($failedSubscription, $subscriptionGroup, $comment);
        }
    }

    public function removeCurrentQuote()
    {
        $quote = $this->orderGenerateService->getCurrentQuote();
        if ($quote && $quote->getId()) {
            try {
                $this->orderGenerateService->setCurrentQuoteNull();
                $quote->delete();
            } catch (Exception $ex) {
                $this->logger->info("MergeOrder: quote is not delete " . $ex->getMessage());
            }
        }
    }

    public function sendSubscriptionFailedEmail($failedSubscription, $subscriptionGroup, $errorMessage)
    {
        $subscriptionGroup['profile_id_csv'] = $subscriptionGroup['profile_id_csv'] ?? false;

        try {
            $generatedTime = $this->timezone->date()->format('r');
            $emailVariables = [
                'placed_on'    => $generatedTime,
                'subscription' => $failedSubscription,
                'store_id'     => $failedSubscription->getStoreId(),
                'failmessage'  => $errorMessage,
            ];

            $emailService = $this->emailServiceFactory->create();
            $emailService->setStoreId($failedSubscription->getStoreId());
            $emailService->setTemplateVars($emailVariables);
            $emailService->setType(EmailService::EMAIL_PAYMENT_FAILED);
            $emailService->setSendTo($failedSubscription->getSubscriberEmail());
            $emailService->send();

            $this->logger->info("MergeOrder: Email Success for Subscription Failed Profile: {$failedSubscription->getProfileId()}, Group Profiles: {$subscriptionGroup['profile_id_csv']}");
        } catch (Exception $e) {
            $this->logger->info("MergeOrder: Email Failed for Subscription Failed Profile: {$failedSubscription->getProfileId()}, Group Profiles: {$subscriptionGroup['profile_id_csv']}, Reason: " . $e->getMessage());
        }
    }

    public function unsetPaymentRegistryData()
    {
        // for cybersource payment gateway
        $this->registry->unregister('postdata');
    }
}
