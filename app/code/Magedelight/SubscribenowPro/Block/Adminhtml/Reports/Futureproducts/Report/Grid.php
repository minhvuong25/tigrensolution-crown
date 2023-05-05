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

namespace Magedelight\SubscribenowPro\Block\Adminhtml\Reports\Futureproducts\Report;

use Magedelight\SubscribenowPro\Block\Adminhtml\Reports\Futureproducts\Report\Renderer\SubscriptionCount;

class Grid extends \Magento\Reports\Block\Adminhtml\Grid\AbstractGrid
{
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnGroupBy = 'period';

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
        return \Magedelight\SubscribenowPro\Model\ResourceModel\Report\FutureProducts\Collection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'period',
            [
                'header' => __('Interval'),
                'index' => 'period',
                'sortable' => false,
                'period_type' => $this->getPeriodType(),
                'renderer' => \Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date::class,
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

        $this->addColumn(
            'qty_ordered',
            [
                'header' => __('Order Quantity'),
                'index' => 'qty_ordered',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-qty',
                'column_css_class' => 'col-qty'
            ]
        );

        $this->addColumn(
            'subscription_count',
            [
                'header' => __('No of Subscription'),
                'index' => 'subscription_ids',
                'type' => 'string',
                'renderer'  => SubscriptionCount::class,
                'sortable' => false,
                'totals_label' => false
            ]
        );

        $this->addExportType('*/*/exportReportCsv', __('CSV'));
        $this->addExportType('*/*/exportReportExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

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
}
