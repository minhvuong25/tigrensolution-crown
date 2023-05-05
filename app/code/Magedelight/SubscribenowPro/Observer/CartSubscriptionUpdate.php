<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_SubscribenowPro
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\SubscribenowPro\Observer;

use Magento\Framework\Exception\LocalizedException;

class CartSubscriptionUpdate implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magedelight\Subscribenow\Helper\Data
     */
    protected $subscriberHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @param \Magedelight\Subscribenow\Helper\Data $subscriberHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magedelight\Subscribenow\Helper\Data $subscriberHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->subscriberHelper = $subscriberHelper;
        $this->messageManager = $messageManager;
        $this->productRepository = $productRepository;
    }

    /**
     * This will remove additional_options to cart item when ordering from admin panel which is default magento adding
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->subscriberHelper->isModuleEnable()) {
            try {
                $cart = $observer->getCart();
                $infoDataObject = $observer->getInfo()->getData();

                foreach ($infoDataObject as $itemId => $itemInfo) {
                    $item = $cart->getQuote()->getItemById($itemId);
                    if (!$item) {
                        continue;
                    }

                    if ($this->isUpdateNeeded($item, $itemInfo)) {
                        $cart->removeItem($itemId);

                        $productId = $item->getProduct()->getId();
                        $product = $this->productRepository->getById($productId, false, null, true);
                        $params = $this->prepareParams($item, $itemInfo);
                        $cart->addProduct($product, $params);

                        $this->messageManager->addSuccess(__('Shopping Cart Updated'));
                    }
                }
            } catch (LocalizedException $e) {
                throw new LocalizedException(__($e->getMessage()));
            } catch (\Exception $e) {
                throw new LocalizedException(__($e->getMessage()));
            }
        }

        return $this;
    }

    protected function isUpdateNeeded($item, $data) : bool
    {
        $buyRequest = $item->getBuyRequest()->getData();

        $itemSubscribed = $buyRequest['options']['_1'] ?? false;
        $subscriptionPostData = $data['subscription'] ?? [];
        $postDataSubscriptionOption = $subscriptionPostData['_1'] ?? false;
        $postDataBillingPeriod = $subscriptionPostData['billing_period'] ?? false;
        $postDataStartDate = $subscriptionPostData['subscription_start_date'] ?? false;

        if ($subscriptionPostData) {
            if (!$itemSubscribed) {
                return true;
            }

            if ($postDataSubscriptionOption
                && $buyRequest['options']['_1'] != $postDataSubscriptionOption) {
                return true;
            }

            if ($postDataBillingPeriod
                && $buyRequest['billing_period'] != $postDataBillingPeriod) {
                return true;
            }

            if ($postDataStartDate
                && $buyRequest['subscription_start_date'] != $postDataStartDate) {
                return true;
            }
        }

        return false;
    }

    protected function prepareParams($item, $data) : array
    {
        $params = $item->getBuyRequest()->getData();

        if ($data['subscription']['_1'] == 'subscription') {
            $params['options']['_1'] = $data['subscription']['_1'];

            if (isset($data['subscription']['billing_period'])) {
                $params['billing_period'] = $data['subscription']['billing_period'];
            }

            if (isset($data['subscription']['subscription_start_date'])) {
                $params['subscription_start_date'] = $data['subscription']['subscription_start_date'];
            }
        } else {
            if (isset($params['options']['_1'])) {
                unset($params['options']['_1']);
            }

            if (isset($params['billing_period'])) {
                unset($params['billing_period']);
            }

            if (isset($params['subscription_start_date'])) {
                unset($params['subscription_start_date']);
            }
        }

        return $params;
    }
}
