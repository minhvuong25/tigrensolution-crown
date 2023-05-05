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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    const TBL_PASTREVENUE_DAILY = 'md_subscribenow_pastrevenue_aggregated_daily';
    const TBL_PASTREVENUE_MONTHLY = 'md_subscribenow_pastrevenue_aggregated_monthly';
    const TBL_PASTREVENUE_YEARLY = 'md_subscribenow_pastrevenue_aggregated_yearly';
    const VIEW_SUBSCRIPTION_CYCLE_AGGREGATED = 'md_view_subscribenow_subscription_cycle_aggregated';

    const TBL_FUTUREPRODUCTS_DAILY = 'md_subscribenow_futureproducts_aggregated_daily';
    const TBL_FUTUREPRODUCTS_MONTHLY = 'md_subscribenow_futureproducts_aggregated_monthly';
    const TBL_FUTUREPRODUCTS_YEARLY = 'md_subscribenow_futureproducts_aggregated_yearly';

    private $salesConnection = null;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->salesConnection = $this->resourceConnection->getConnection('sales');
        
        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $this->updateFutureProductsTables($setup);
            $this->createTblPastRevenueAggregated($setup);
        }

        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $this->createViewSubscriptionCycleAggregated($setup);
        }

        $setup->endSetup();
    }

    public function updateFutureProductsTables($setup)
    {
        $tablesToUpdate = [
            'daily' => self::TBL_FUTUREPRODUCTS_DAILY,
            'monthly' => self::TBL_FUTUREPRODUCTS_MONTHLY,
            'yearly' => self::TBL_FUTUREPRODUCTS_YEARLY
        ];

        foreach ($tablesToUpdate as $key => $tbl) {
            $this->salesConnection->addColumn(
                $setup->getTable($tbl),
                'row_total',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0.0000',
                    'comment' => 'Row Total'
                ]
            );
        }
    }

    protected function createTblPastRevenueAggregated($setup)
    {
        $tablesToCreate = [
            'daily' => self::TBL_PASTREVENUE_DAILY,
            'monthly' => self::TBL_PASTREVENUE_MONTHLY,
            'yearly' => self::TBL_PASTREVENUE_YEARLY
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
                'row_total',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Row Total'
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
                'Subscribenow Past Revenue Aggregated '.ucfirst($key)
            );

            $this->salesConnection->createTable($table);
        }
    }

    protected function createViewSubscriptionCycleAggregated($setup)
    {
        $viewTableSql = "DROP VIEW IF EXISTS ".$setup->getTable(self::VIEW_SUBSCRIPTION_CYCLE_AGGREGATED).";";

        $this->salesConnection->query($viewTableSql);

        $viewTableSql = "CREATE VIEW ".$setup->getTable(self::VIEW_SUBSCRIPTION_CYCLE_AGGREGATED)." AS
            SELECT DATE(created_at) AS period, store_id, MIN(product_name) AS product_name, product_sku, COUNT(subscription_id) AS subscription_qty, MIN(billing_period_label) AS billing_period_label, billing_period, billing_frequency
            FROM ".$setup->getTable('md_subscribenow_product_subscribers')."
            GROUP BY DATE(created_at), product_sku, billing_period, billing_frequency, store_id;";

        $this->salesConnection->query($viewTableSql);
    }
}
