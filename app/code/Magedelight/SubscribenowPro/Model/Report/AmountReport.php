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

namespace Magedelight\SubscribenowPro\Model\Report;

use Magedelight\SubscribenowPro\Helper\Data as Helper;

class AmountReport extends AbstractReport
{
    protected $collectionFactory;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        Helper $helper
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($helper);
    }

    public function getReport()
    {
        $dateFieldExpression = $this->getAdminTimezoneDateSql('view_order_amount.created_at');

        $collection = $this->collectionFactory->create();

        $collection->getSelect()
            ->joinRight(
                ['view_order_amount' => $collection->getResource()->getTable('md_view_subscribenow_order_amount_group')],
                'view_order_amount.order_id = main_table.entity_id',
                []
            )
            ->where("DATE(".$dateFieldExpression.") >= '$this->from'")
            ->where("DATE(".$dateFieldExpression.") <= '$this->to'");

        $collection->addFieldToFilter('main_table.state', [
            'in' => [
                'processing',
                'complete'
            ]
        ]);

        if ($this->group == 'day') {
            $collection
                ->getSelect()
                ->reset(\Zend_Db_Select::COLUMNS)
                ->columns(
                    [
                    "DATE(".$dateFieldExpression.") AS created_at",
                    'SUM(view_order_amount.total_amount) AS value'
                    ]
                )
                ->group(new \Zend_Db_Expr("DATE(".$dateFieldExpression.")"));
        } elseif ($this->group == 'month') {
            $collection
                ->getSelect()
                ->reset(\Zend_Db_Select::COLUMNS)
                ->columns(
                    [
                    "DATE_FORMAT(MIN(".$dateFieldExpression."), '%Y-%m') AS created_at",
                    'SUM(view_order_amount.total_amount) AS value'
                    ]
                )
                ->group([
                    new \Zend_Db_Expr("YEAR(".$dateFieldExpression.")"),
                    new \Zend_Db_Expr("MONTH(".$dateFieldExpression.")"),
                ]);
        } elseif ($this->group == 'year') {
            $collection
                ->getSelect()
                ->reset(\Zend_Db_Select::COLUMNS)
                ->columns(
                    [
                    "YEAR(MIN(".$dateFieldExpression.")) AS created_at",
                    'SUM(view_order_amount.total_amount) AS value'
                    ]
                )
                ->group(new \Zend_Db_Expr("YEAR(".$dateFieldExpression.")"));
        }

        $collection->setOrder('view_order_amount.created_at', 'ASC');
        $this->addStoreFilterToCollection($collection);
        $this->data = $collection->getData();
        return $this;
    }
}
