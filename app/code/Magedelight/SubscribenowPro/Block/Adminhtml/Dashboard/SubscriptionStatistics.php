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

namespace Magedelight\SubscribenowPro\Block\Adminhtml\Dashboard;

use Magedelight\Subscribenow\Model\ResourceModel\ProductSubscribers\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magedelight\Subscribenow\Model\Source\ProfileStatus;

class SubscriptionStatistics extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magedelight_SubscribenowPro::dashboard/subscription_statistics.phtml';

    protected $subscriptionStatistics = [];
    protected $statusLabels = [];

    protected $collectionFactory;
    protected $orderCollectionFactory;
    protected $profileStatus;
    protected $serialize;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        CollectionFactory $collectionFactory,
        OrderCollectionFactory $orderCollectionFactory,
        ProfileStatus $profileStatus,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->collectionFactory = $collectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->profileStatus = $profileStatus;
        $this->serialize = $serialize;
    }

    /**
     * @return $this|void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $collection = $this->collectionFactory->create();
        $collection
            ->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'subscription_status',
                'COUNT(subscription_status) AS total_subscriptions'
            ])
            ->group('subscription_status');
        $this->addStoreFilterToCollection($collection);
        $collection->load();

        $this->addSubscriptionStatistics(__('Lifetime'), $this->getLifetimeSubscriptions());

        $statuses = $this->getStatusesToDisplay();

        $this->setStatusLabels();

        foreach ($statuses as $statusCode) {
            $this->subscriptionStatistics[] = $this->getStatisticsFromCollection($collection, $statusCode);
        }

        $this->addSubscriptionStatistics(__('Active Subscribers'), $this->getActiveSubscribers());

        $trial_params = [
            'deeplink' => [
                'redirect' => $this->getUrl('subscribenow/productsubscribers/index'),
                'url' => $this->getUrl('mui/bookmark/save'),
                'namespace' => 'subscribenow_product_subscriber',
                'form_key' => $this->getFormKey(),
                'data' => [
                    'current' => [
                        'filters' => [
                            'applied' => [
                                'placeholder' => true,
                                'subscription_status' => [(string) ProfileStatus::ACTIVE_STATUS],
                                'is_trial' => ['1']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        if ($this->getRequest()->getParam('store')) {
            $storeId = (string) $this->getRequest()->getParam('store');
            $trial_params['deeplink']['data']['current']['filters']['applied']['store_id'] = $storeId;
        }
        
        $this->addSubscriptionStatistics(__('Active Trials'), $this->getActiveTrials(), $trial_params);

        $this->addOrderStatistics();
    }

    public function addSubscriptionStatistics($label, $value, $params = null)
    {
        $element = ['label' => $label, 'value' => $value];

        if ($params) {
            foreach ($params as $key => $value) {
                $element[$key] = $value;
            }
        }

        return $this->subscriptionStatistics[] = $element;
    }

    public function getSubscriptionStatistics()
    {
        return $this->subscriptionStatistics;
    }

    protected function getStatisticsFromCollection($collection, $statusCode)
    {
        $result = [
            'label' => $this->getStatusLabel($statusCode),
            'value' => 0,
            'deeplink' => [
                'redirect' => $this->getUrl('subscribenow/productsubscribers/index'),
                'url' => $this->getUrl('mui/bookmark/save'),
                'namespace' => 'subscribenow_product_subscriber',
                'form_key' => $this->getFormKey(),
                'data' => [
                    'current' => [
                        'filters' => [
                            'applied' => [
                                'placeholder' => true,
                                'subscription_status' => [(string) $statusCode]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        if ($this->getRequest()->getParam('store')) {
            $storeId = (string) $this->getRequest()->getParam('store');
            $result['deeplink']['data']['current']['filters']['applied']['store_id'] = $storeId;
        }

        foreach ($collection as $statusRow) {
            if ($statusRow->getSubscriptionStatus() == $statusCode) {
                $result['value'] = $statusRow->getTotalSubscriptions();
            }
        }

        return $result;
    }

    public function getStatusesToDisplay()
    {
        return [
            ProfileStatus::ACTIVE_STATUS,
            ProfileStatus::PAUSE_STATUS,
            ProfileStatus::COMPLETED_STATUS
        ];
    }

    public function setStatusLabels()
    {
        $this->statusLabels = $this->profileStatus->getOptions();
        return $this;
    }

    public function getStatusLabel($statusCode)
    {
        return $this->statusLabels[$statusCode];
    }

    public function getLifetimeSubscriptions()
    {
        $collection = $this->collectionFactory->create();
        $this->addStoreFilterToCollection($collection);
        return $collection->getSize();
    }

    public function getActiveSubscribers()
    {
        $collection = $this->collectionFactory->create();
        $collection
            ->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'COUNT(DISTINCT(customer_id)) AS total_customers'
            ])
            ->where('subscription_status', ProfileStatus::ACTIVE_STATUS);
        $this->addStoreFilterToCollection($collection);
        $collection->load();

        return $collection->getFirstItem()->getTotalCustomers();
    }

    public function getActiveTrials()
    {
        $collection = $this->collectionFactory->create();
        $collection
            ->addFieldToFilter('is_trial', '1')
            ->addFieldToFilter('subscription_status', ProfileStatus::ACTIVE_STATUS)
            ->addFieldToFilter('trial_count', ['lt' => new \Zend_Db_Expr('trial_period_max_cycle')]);
        $this->addStoreFilterToCollection($collection);
        return $collection->count();
    }

    public function addOrderStatistics()
    {
        //$this->addSubscriptionStatistics(__('Orders (All)'), $this->getAllOrders());
        //$this->addSubscriptionStatistics(__('Orders (Subscription)'), $this->getSubscriptionOrders());

        $this->addSubscriptionStatistics(__('Orders'), $this->getSubscriptionOrders() . ' / ' . $this->getAllOrders());
    }

    public function getAllOrders()
    {
        $collection = $this->orderCollectionFactory->create();
        $this->addStoreFilterToCollection($collection);
        return $collection->getSize();
    }

    public function getSubscriptionOrders()
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->getSelect()
            ->join(
                ['sub_orders' => $collection->getResource()->getTable('md_subscribenow_product_associated_orders')],
                'sub_orders.order_id = main_table.increment_id',
                []
            );
        $this->addStoreFilterToCollection($collection);
        return $collection->getSize();
    }

    public function convertJson($data)
    {
        return $this->serialize->serialize($data);
    }

    public function addStoreFilterToCollection($collection)
    {
        if ($this->getRequest()->getParam('store')) {
            $collection->addFieldToFilter('store_id', $this->getRequest()->getParam('store'));
        } elseif ($this->getRequest()->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        } elseif ($this->getRequest()->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        }
    }
}
