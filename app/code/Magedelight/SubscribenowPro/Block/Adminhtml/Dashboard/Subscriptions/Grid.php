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

namespace Magedelight\SubscribenowPro\Block\Adminhtml\Dashboard\Subscriptions;

use Magedelight\Subscribenow\Model\ResourceModel\ProductSubscribers;

class Grid extends \Magento\Backend\Block\Dashboard\Grid
{
    /**
     * @var ProductSubscribers\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param ProductSubscribers\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        ProductSubscribers\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lastSubscriptionsGrid');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();

        $this->_eventManager->dispatch(
            'md_subscribenowpro_dashboard_last_subscriptions_grid_collection',
            [
                'collection' => $collection
            ]
        );
        
        $collection->setOrder('subscription_id', 'DESC');
        $this->addStoreFilterToCollection($collection);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepares page sizes for dashboard grid with last 5 subscriptions
     *
     * @return void
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'profile_id',
            [
                'header' => __('Profile ID'),
                'sortable' => false,
                'index' => 'profile_id'
            ]
        );

        $this->addColumn(
            'customer',
            [
                'header' => __('Subscriber'),
                'sortable' => false,
                'index' => 'subscriber_name',
                'default' => __('Unknown')
            ]
        );

        $this->setItemRow();

        /*
        $this->addColumn(
            'billing_period_label',
            [
                'header' => __('Period'),
                'sortable' => false,
                'index' => 'billing_period_label',
                'default' => __('Unknown')
            ]
        );
        */

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('subscribenow/productsubscribers/view', ['id' => $row->getId()]);
    }

    /**
     * created function for this, so that a plugin can be created for subscribenow single profile
     */
    public function setItemRow()
    {
        $this->addColumn(
            'item',
            [
                'header' => __('Item'),
                'sortable' => false,
                'index' => 'product_name'
            ]
        );
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
