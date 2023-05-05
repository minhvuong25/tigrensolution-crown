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

namespace Magedelight\SubscribenowPro\Block\Adminhtml\Reports\Subscriptioncycle\Report;

class Grid extends \Magento\Reports\Block\Adminhtml\Grid\AbstractGrid
{
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnGroupBy = 'billing_period_label';

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getResourceCollectionName()
    {
        return \Magedelight\SubscribenowPro\Model\ResourceModel\Report\SubscriptionCycle\Collection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'billing_period_label',
            [
                'header' => __('Billing Period'),
                'index' => 'billing_period_label',
                'sortable' => false,
                'totals_label' => __('Total'),
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );

        $this->addColumn(
            'product_sku_column',
            [
                'header' => __('SKU'),
                'index' => 'product_sku',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

        $this->addColumn(
            'product_name',
            [
                'header' => __('Product'),
                'index' => 'product_name',
                'type' => 'string',
                'sortable' => false,
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }

        $this->addSubscriptionQtyColumn();

        $this->addExportType('*/*/exportReportCsv', __('CSV'));
        $this->addExportType('*/*/exportReportExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /*
    protected function _addCustomFilter($collection, $filterData)
    {
        $collection->setProductSku($filterData->getData('product_sku'));
        return $this;
    }
    */

    /**
     * This is magento bug, magento does not call _addCustomFilter at all places.
     * Magento\Reports\Block\Adminhtml\Grid\AbstractGrid does not call _addCustomFilter in getCountTotals function
     * which results export csv not working correctly
     * so commented above function and set sku in this function
     */
    protected function _addOrderStatusFilter($collection, $filterData)
    {
        parent::_addOrderStatusFilter($collection, $filterData);
        $collection->setProductSku($filterData->getData('product_sku'));
        return $this;
    }

    /**
     * Becuase in SubscribenowSingleProfile
     * we need to remmove totals_label
     * as it will be inappropriate
     */
    public function addSubscriptionQtyColumn()
    {
        $this->addColumn(
            'subscription_qty',
            [
                'header' => __('Subscription Qty'),
                'index' => 'subscription_qty',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-qty',
                'column_css_class' => 'col-qty'
            ]
        );
    }
}
