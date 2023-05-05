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

class OrderReport extends AbstractReport
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
        $dateFieldExpression = $this->getAdminTimezoneDateSql('main_table.created_at');

        $collection = $this->collectionFactory->create();

        $collection->getSelect()
            ->join(
                ['sub_orders' => $collection->getResource()->getTable('md_subscribenow_product_associated_orders')],
                'sub_orders.order_id = main_table.increment_id',
                []
            )
            ->where("DATE(".$dateFieldExpression.") >= '$this->from'")
            ->where("DATE(".$dateFieldExpression.") <= '$this->to'");

        if ($this->group == 'day') {
            $collection
                ->getSelect()
                ->reset(\Zend_Db_Select::COLUMNS)
                ->columns(
                    [
                    "DATE(".$dateFieldExpression.") AS created_at",
                    'COUNT(main_table.entity_id) AS value'
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
                    'COUNT(main_table.entity_id) AS value'
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
                    'count(main_table.entity_id) AS value'
                    ]
                )
                ->group(new \Zend_Db_Expr("YEAR(".$dateFieldExpression.")"));
        }

        $collection->setOrder('created_at', 'ASC');
        $this->addStoreFilterToCollection($collection);
        $this->data = $collection->getData();
        return $this;
    }
}
