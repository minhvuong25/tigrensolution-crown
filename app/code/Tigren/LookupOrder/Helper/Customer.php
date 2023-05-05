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
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
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
     * @param App\Helper\Context $context
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
     * @param Order $order
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
        OrderRepositoryInterface $orderRepository = null,
        SearchCriteriaBuilder $searchCriteria = null
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->messageManager = $messageManager;
        $this->orderFactory = $orderFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->_addressCollectionFactory = $addressCollectionFactory;
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
        if (empty($post) && !$fromCookie) {
            return $this->resultRedirectFactory->create()->setPath('lookuporder/customer/form');
        }

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


                if ((!empty($post['oar_zip'])) ||
                    (!empty($post['oar_billing_lastname'])) ||
                    (!empty($post['oar_company'])) ||
                    (!empty($post['oar_email'])) ||
                    (!empty($post['oar_ordercomments'])) ||
                    (!empty($post['oar_phonenumber']))) {
                    $orderCollection = $this->_addressCollectionFactory->create();
                    if (!empty($post['oar_email'])) {
                        $orderCollection->addFieldToFilter('email', $post['oar_email']);
                    }
                    if (!empty($post['oar_zip'])) {
                        $orderCollection->addFieldToFilter('postcode', $post['oar_zip']);
                    }
                    if (!empty($post['oar_billing_lastname'])) {
                        $orderCollection->addFieldToFilter('lastname', $post['oar_billing_lastname']);
                    }
                    if (!empty($post['oar_company'])) {
                        $orderCollection->addFieldToFilter(
                            'company',
                            array('like' => '%' . $post['oar_company'] . '%')
                        );
                    }
                    if (!empty($post['oar_phonenumber'])) {
                        $orderCollection->addFieldToFilter('telephone', $post['oar_phonenumber']);
                    }
                    if (!empty($post['oar_ordercomments'])) {
                        $orderCollection->getSelect()
                            ->joinLeft(
                                ['sosh' => $orderCollection->getTable('sales_order_status_history')],
                                'sosh.parent_id = main_table.parent_id',
                                ['sosh.comment']
                            )->where("sosh.comment LIKE '%" . $post['oar_ordercomments'] . "%'");
                    }

                    if (!empty($orderCollection->getItems())) {
                        $items = $orderCollection->getItems();
                        $orderIds = [];
                        foreach ($items as $item) {
                            $orderIds[] = $item->getParentId();
                        }
                        $orders = [];
                        foreach (array_unique($orderIds) as $orderId) {
                            $orders[] = $this->orderRepository->get($orderId);
                        }
                        $this->coreRegistry->register('current_order', $orders);
                        return true;
                    } else {
                        $this->messageManager->addErrorMessage('You entered incorrect data. Please try again.');
                        return $this->resultRedirectFactory->create()->setPath('lookuporder/customer/form');
                    }
                } else {
                    $this->messageManager->addErrorMessage('You entered incorrect data. Please try again.');
                    return $this->resultRedirectFactory->create()->setPath('lookuporder/customer/form');
                }
            }
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('lookuporder/customer/form');
        }
    }

    /**
     * Get Breadcrumbs for current controller action
     *
     * @param Page $resultPage
     * @return void
     */
    public function getBreadcrumbs(Page $resultPage)
    {
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
    public function getGeneralConfig($path, $storeId = null)
    {
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
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->getGeneralConfig('tigren_lookup_order/' . $path, $storeId);
    }

    /**
     *
     */
    public function getCustomerGroupId()
    {
        return $this->customerSession->getCustomer()->getGroupId();
    }
}