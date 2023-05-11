<?php
/**
 * *
 * * @author    Tigren Solutions <info@tigren.com>
 * * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\LookupOrder\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App as App;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory as HistoryCollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Api\OrderStatusHistoryRepositoryInterface;
use RuntimeException;


/**
 * Class Customer
 * @package Tigren\LookupOrder\Helper
 */
class Customer extends AbstractHelper
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Cookie key for customer view
     */
    const COOKIE_NAME = 'customer-view';

    /**
     * Cookie path
     */
    const COOKIE_PATH = '/';

    /**
     * Cookie lifetime value
     */
    const COOKIE_LIFETIME = 600;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    private $inputExceptionMessage = 'You entered incorrect data. Please try again.';

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var CollectionFactory
     */
    protected $_addressCollectionFactory;

    /**
     * @var
     */
    protected $filterBuilder;
    /**
     * @var
     */
    protected $OrderAddressRepositoryInterface;
    /**
     * @var OrderAddressRepositoryInterface
     */
    private $orderAddressRepositoryInterface;
    /**
     * @var
     */
    private $OrderStatusHistoryRepositoryInterface;
    /**
     * @var OrderStatusHistoryRepositoryInterface
     */
    private $orderStatusHistoryRepositoryInterface;
    protected $historyCollectionFactory;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Registry $coreRegistry
     * @param Session $customerSession
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param ManagerInterface $messageManager
     * @param OrderFactory $orderFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param CollectionFactory $addressCollectionFactory
     * @param OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepositoryInterface
     * @param OrderAddressRepositoryInterface $OrderAddressRepositoryInterface
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteria
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        App\Helper\Context $context,
        StoreManagerInterface $storeManager,
        Registry $coreRegistry,
        Session $customerSession,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        ManagerInterface $messageManager,
        OrderFactory $orderFactory,
        RedirectFactory $resultRedirectFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        CollectionFactory $addressCollectionFactory,
        OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepositoryInterface,
        OrderAddressRepositoryInterface $OrderAddressRepositoryInterface,
        OrderRepositoryInterface $orderRepository = null,
        SearchCriteriaBuilder $searchCriteria = null,
        FilterBuilder $filterBuilder,
        HistoryCollectionFactory $historyCollectionFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->messageManager = $messageManager;
        $this->orderFactory = $orderFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderAddressRepositoryInterface = $OrderAddressRepositoryInterface;
        $this->orderStatusHistoryRepositoryInterface = $orderStatusHistoryRepositoryInterface;
        $this->_addressCollectionFactory = $addressCollectionFactory;
        $this->filterBuilder = $filterBuilder;
        $this->orderRepository = $orderRepository ?: ObjectManager::getInstance()
            ->get(OrderRepositoryInterface::class);
        $this->searchCriteriaBuilder = $searchCriteria ?: ObjectManager::getInstance()
            ->get(SearchCriteriaBuilder::class);
        parent::__construct(
            $context
        );
    }

    /**
     * Try to load valid order by $_POST or $_COOKIE
     *
     * @param App\RequestInterface $request
     * @return Redirect|bool
     * @throws RuntimeException
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function loadValidOrder(App\RequestInterface $request)
    {
        $post = $request->getPostValue();
        $fromCookie = $this->cookieManager->getCookie(self::COOKIE_NAME);
        //        if (empty($post) && !$fromCookie) {
        //            return $this->resultRedirectFactory->create()->setPath('lookuporder/customer/form');
        //        }
        try {
            if (!empty($post['oar_order_id'])) {
                $orders = $this->orderRepository->getList(
                    $this->searchCriteriaBuilder
                        ->addFilter('increment_id', $post['oar_order_id'])
                        ->addFilter('store_id', $this->storeManager->getStore()->getId())
                        ->create()
                );
                if (!empty($orders->getData())) {
                    $items = $orders->getItems();
                    $this->coreRegistry->register('current_order', $items);
                    return true;
                } else {
                    $this->messageManager->addErrorMessage('You entered incorrect data. Please try again.');
                    return $this->resultRedirectFactory->create()->setPath('lookuporder/customer/form');
                }
            } else {
                if (!empty($post['oar_ordercomments']) || !empty($post['oar_email']) || !empty($post['oar_billing_lastname']) || !empty($post['oar_zip']) || !empty($post['oar_phonenumber']) || !empty($post['company'])) {
                    $orderSearchByComment = $this->orderStatusHistoryRepositoryInterface->getList(
                        $this->searchCriteriaBuilder
                            ->addFilter('comment', '%' . $post['oar_ordercomments'] . '%', 'like')
                            ->create())->getItems();

                    if (!empty($post['oar_email'])) {
                        $this->searchCriteriaBuilder
                            ->addFilter('email', $post['oar_email']);
                    }
                    if (!empty($post['oar_billing_lastname'])) {
                        $this->searchCriteriaBuilder
                            ->addFilter('lastname', $post['oar_billing_lastname']);
                    }
                    if (!empty($post['oar_zip'])) {
                        $this->searchCriteriaBuilder
                            ->addFilter('postcode', $post['oar_zip']);
                    }
                    if (!empty($post['oar_phonenumber'])) {
                        $this->searchCriteriaBuilder
                            ->addFilter('telephone', $post['oar_phonenumber']);
                    }
                    if (!empty($post['company'])) {
                        $this->searchCriteriaBuilder
                            ->addFilter('company', '%' . $post['company'] . '%', 'like');
                    }

                    $orderSearchbyAddress = $this->orderAddressRepositoryInterface->getList(
                        $this->searchCriteriaBuilder
                            ->create())->getItems();

                    $CommentIds = [];
                    $ordersIds = [];
                    if (!empty($orderSearchByComment)) {
                        foreach ($orderSearchByComment as $commentid) {
                            $CommentIds[] = $commentid->getParentId();
                        }
                    }

                    if (!empty($orderSearchbyAddress)) {
                        foreach ($orderSearchbyAddress as $commentid) {
                            $ordersIds[] = $commentid->getParentId();
                        }
                    }

                    $ordersId = array_intersect($CommentIds, $ordersIds);

                    $ordersearch = $this->orderRepository
                        ->getList($this->searchCriteriaBuilder
                            ->addFilter('entity_id', $ordersId, 'in')
                            ->create());

                    if (!empty($ordersearch->getItems())) {
                        $items = $ordersearch->getItems();

                        $orderIds = [];

                        foreach ($items as $item) {
                            $orderIds[] = $item->getEntityId();

                        }
                        $orders = [];
                        foreach (array_unique($orderIds) as $orderId) {
                            //                                $orders[] = $this->orderRepository->get($orderId);
                            $orders[] = $orderId;
                        }

                        $this->customerSession->setListId( $orders);
//                        $this->coreRegistry->register('current_order', $orders);
                    } else {
                        $this->messageManager->addErrorMessage('You entered incorrect data. Please try again.');
                        return $this->resultRedirectFactory->create()->setPath('lookuporder/customer/form');
                    }

                }

            }

        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('lookuporder/customer/form');
        }
        return true;
    }

    /**
     * Get Breadcrumbs for current controller action
     *
     * @param Page $resultPage
     * @return void
     */
    public
    function getBreadcrumbs(
        Page $resultPage
    ) {
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        if (!$breadcrumbs) {
            return;
        }
        $breadcrumbs->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->storeManager->getStore()->getBaseUrl()
            ]
        );
        $breadcrumbs->addCrumb(
            'cms_page',
            ['label' => __('Order Information'), 'title' => __('Order Information')]
        );
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public
    function getGeneralConfig(
        $path,
        $storeId = null
    ) {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public
    function getModuleConfig(
        $path,
        $storeId = null
    ) {
        return $this->getGeneralConfig('tigren_lookup_order/' . $path, $storeId);
    }

    /**
     *
     */
    public
    function getCustomerGroupId()
    {
        return $this->customerSession->getCustomer()->getGroupId();
    }
}
