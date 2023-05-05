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

namespace Magedelight\SubscribenowPro\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{
    const TBL_FUTUREPRODUCTS_DAILY = 'md_subscribenow_futureproducts_aggregated_daily';
    const TBL_FUTUREPRODUCTS_MONTHLY = 'md_subscribenow_futureproducts_aggregated_monthly';
    const TBL_FUTUREPRODUCTS_YEARLY = 'md_subscribenow_futureproducts_aggregated_yearly';

    const VIEW_ORDER_AMOUNT_GROUP = 'md_view_subscribenow_order_amount_group';

    private $salesConnection = null;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->salesConnection = $this->resourceConnection->getConnection('sales');
        
        $this->createTblFutureProductAggregated($setup);
        $this->createSalesOrderView($setup);

        $setup->endSetup();
    }

    protected function createTblFutureProductAggregated($setup)
    {
        $tablesToCreate = [
            'daily' => self::TBL_FUTUREPRODUCTS_DAILY,
            'monthly' => self::TBL_FUTUREPRODUCTS_MONTHLY,
            'yearly' => self::TBL_FUTUREPRODUCTS_YEARLY
        ];

        foreach ($tablesToCreate as $key => $tbl) {
            $table = $this->salesConnection->newTable(
                $setup->getTable($tbl)
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'period',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                [],
                'Period'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Store Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Product Id'
            )->addColumn(
                'product_sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Product SKU'
            )->addColumn(
                'product_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Product Name'
            )->addColumn(
                'qty_ordered',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Qty Ordered'
            )->addColumn(
                'subscription_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Associated Subscription IDs'
            )->addIndex(
                $setup->getIdxName(
                    $tbl,
                    ['period', 'store_id', 'product_sku'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['period', 'store_id', 'product_sku'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName($tbl, ['store_id']),
                ['store_id']
            )->addIndex(
                $setup->getIdxName($tbl, ['product_id']),
                ['product_id']
            )->setComment(
                'Subscribenow Future Product Aggregated '.ucfirst($key)
            );

            $this->salesConnection->createTable($table);
        }
    }

    protected function createSalesOrderView($setup)
    {
        $viewTableSql = "DROP VIEW IF EXISTS ".$setup->getTable(self::VIEW_ORDER_AMOUNT_GROUP).";";

        $this->salesConnection->query($viewTableSql);

        $viewTableSql = "CREATE VIEW ".$setup->getTable(self::VIEW_ORDER_AMOUNT_GROUP)." AS
            SELECT
                MIN(sales_order.created_at) AS created_at,
                MIN(sales_order_item.order_id) AS order_id,
                SUM(sales_order_item.base_row_total) + IFNULL(MAX(sales_order.base_subscribenow_trial_amount), 0) + IFNULL(MAX(sales_order.base_subscribenow_init_amount), 0) AS total_amount
            FROM ".$setup->getTable('sales_order_item')." AS sales_order_item
            JOIN ".$setup->getTable('sales_order')." AS sales_order
                ON sales_order.entity_id = sales_order_item.order_id
            WHERE
                sales_order_item.parent_item_id IS NULL
                AND sales_order_item.is_subscription = '1'
            GROUP BY sales_order_item.order_id;";

        $this->salesConnection->query($viewTableSql);
    }
}
