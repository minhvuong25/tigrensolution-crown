<?php

namespace Tigren\PrintOrder\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Tigren\PrintOrder\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $disabledRegion = $setup->getTable('tigren_guests_orders');

        $setup->run("DROP TABLE IF EXISTS {$disabledRegion}");

        $table = $setup->getConnection()->newTable(
            $setup->getTable($disabledRegion)
        )->addColumn(
            'guests_order_id',
            Table::TYPE_INTEGER,
            null,
            [
                'auto_increment' => true,
                'primary' => true,
                'unsigned' => true,
                'nullable' => false
            ],
            'Guests Order Entity Id'
        )->addColumn(
            'hash',
            Table::TYPE_TEXT,
            null,
            [
                'default' => null
            ],
            'Hash of order'
        )->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Order Id'
        )->addColumn(
            'expired_at',
            Table::TYPE_DATETIME,
            null,
            [
                'nullable' => false
            ],
            'Date when hash is expired'
        );

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
