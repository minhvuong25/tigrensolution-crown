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

namespace Magedelight\SubscribenowPro\Block\Adminhtml\Dashboard\TopProducts;

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
        $collection = $this->getGridCollection();

        $this->_eventManager->dispatch(
            'md_subscribenowpro_dashboard_top_subscription_products_grid_collection',
            [
                'collection' => &$collection
            ]
        );

        $this->addStoreFilterToCollection($collection);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getGridCollection()
    {
        $collection = $this->collectionFactory->create();
        $collection->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'MAX(subscription_id) AS subscription_id',
                'product_id',
                'COUNT(product_id) AS no_of_subscription',
                'MIN(product_name) AS product_name',
                "MIN(product_sku) AS product_sku",

            ])
            ->group('product_id')
            ->order([
                'no_of_subscription DESC',
                'subscription_id ASC'
            ]);

        //$collection->setOrder('no_of_subscription', 'DESC');

        return $collection;
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
            'product_name',
            [
                'header' => __('Product'),
                'sortable' => false,
                'index' => 'product_name'
            ]
        );

        $this->addColumn(
            'product_sku',
            [
                'header' => __('SKU'),
                'sortable' => false,
                'index' => 'product_sku'
            ]
        );

        $this->addColumn(
            'subscription_count',
            [
                'header' => __('Subscriptions'),
                'sortable' => false,
                'index' => 'no_of_subscription'
            ]
        );

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('catalog/product/edit', ['id' => $row->getProductId()]);
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
