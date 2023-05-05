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

namespace Magedelight\SubscribenowPro\Model\ResourceModel\Report;

use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;
use Magedelight\Subscribenow\Model\Source\ProfileStatus;

/**
 * Bestsellers report resource model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PastRevenue extends \Magento\Sales\Model\ResourceModel\Report\AbstractReport
{
    const AGGREGATION_DAILY = 'md_subscribenow_pastrevenue_aggregated_daily';

    const AGGREGATION_MONTHLY = 'md_subscribenow_pastrevenue_aggregated_monthly';

    const AGGREGATION_YEARLY = 'md_subscribenow_pastrevenue_aggregated_yearly';

    protected $resource;
    protected $collectionFactory;
    protected $attributeRepositoryInterface;
    protected $timezone;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Reports\Model\FlagFactory $reportsFlagFactory
     * @param \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param array $ignoredProductTypes
     * @param string $connectionName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Reports\Model\FlagFactory $reportsFlagFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\ResourceConnection $resource,
        CollectionFactory $collectionFactory,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepositoryInterface,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        $connectionName = null
    ) {
        if (!$connectionName) {
            $connectionName = 'sales';
        }
        
        parent::__construct(
            $context,
            $logger,
            $localeDate,
            $reportsFlagFactory,
            $timezoneValidator,
            $dateTime,
            $connectionName
        );

        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->attributeRepositoryInterface = $attributeRepositoryInterface;
        $this->timezone = $timezone;
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::AGGREGATION_DAILY, 'id');
    }

    /**
     * Aggregate Orders data by order created at
     *
     * @param string|int|\DateTime|array|null $from
     * @param string|int|\DateTime|array|null $to
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function aggregate($from = null, $to = null)
    {
        $mainTable = $this->getMainTable();
        $connection = $this->resource->getConnection('sales');
        //$this->getConnection()->beginTransaction();

        try {
            $this->truncateTable();

            $collection = $this->getSalesItemCollection();
            
            $insertBatches = [];
            if ($collection) {
                foreach ($collection as $item) {
                    $insertBatches[] = [
                        'period'             => $item->getCreatedAt(),
                        'store_id'           => $item->getStoreId(),
                        'product_id'         => $item->getProductId(),
                        'product_sku'        => $item->getProductSku(),
                        'product_name'       => $item->getProductName(),
                        'qty_ordered'        => (float) $item->getQtyOrdered(),
                        'row_total'          => (float) $item->getRowTotal(),
                        'subscription_ids'   => $item->getSubscriptionIds()
                    ];
                }
            }

            $tableName = $this->resource->getTableName(self::AGGREGATION_DAILY);
            foreach (array_chunk($insertBatches, 100) as $batch) {
                $connection->insertMultiple($tableName, $batch);
            }

            $this->updateReportMonthlyYearlyCustom(
                $connection,
                'month',
                'qty_ordered',
                $mainTable,
                $this->getTable(self::AGGREGATION_MONTHLY),
                $collection
            );
            $this->updateReportMonthlyYearlyCustom(
                $connection,
                'year',
                'qty_ordered',
                $mainTable,
                $this->getTable(self::AGGREGATION_YEARLY),
                $collection
            );
            
            $this->_setFlagData(\Magedelight\SubscribenowPro\Model\Flag::REPORT_SUBSCRIBENOW_PASTREVENUE_FLAG_CODE);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    public function getSalesItemCollection()
    {
        $collection = $this->collectionFactory->create();
        $resource = $collection->getResource();
        
        $collection->addFieldToFilter('sales_order.state', [
            'in' => [
                'processing',
                'complete'
            ]
        ]);

        $collection
            ->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(
                [
                    'created_at' => new \Zend_Db_Expr("DATE(sales_order.created_at)"),
                    'store_id' => new \Zend_Db_Expr('MIN(sales_order.store_id)'),
                    'product_id' => new \Zend_Db_Expr('MIN(main_table.product_id)'),
                    'product_sku' => 'main_table.sku',
                    'product_name' => new \Zend_Db_Expr("MIN(main_table.name)"),
                    'qty_ordered' => new \Zend_Db_Expr("SUM(main_table.qty_ordered)"),
                    'row_total' => new \Zend_Db_Expr("SUM(main_table.base_row_total)"),
                    'subscription_ids' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT(md_associate.subscription_id))"),
                ]
            )
            ->join(
                ['sales_order' => $resource->getTable('sales_order')],
                'sales_order.entity_id = main_table.order_id',
                []
            )
            ->join(
                ['md_associate' => $resource->getTable('md_subscribenow_product_associated_orders')],
                'md_associate.order_id = sales_order.increment_id',
                []
            )
            ->where('main_table.is_subscription', '1')
            ->group([
                'DATE(sales_order.created_at)',
                'main_table.sku',
                'sales_order.store_id'
            ]);

        return $collection;
    }

    public function truncateTable()
    {
        $connection = $this->resource->getConnection('sales');

        $tables = [
            $this->resource->getTableName(self::AGGREGATION_DAILY),
            $this->resource->getTableName(self::AGGREGATION_MONTHLY),
            $this->resource->getTableName(self::AGGREGATION_YEARLY),
        ];

        foreach ($tables as $table) {
            $connection->truncateTable($table);
        }
    }
    
    public function updateReportMonthlyYearlyCustom(
        $connection,
        $type,
        $column,
        $mainTable,
        $aggregationTable,
        $collection
    ) {
        $insertBatches = [];
        foreach ($collection as $item) {
            $storeId = $item->getStoreId();
            $productSku = $item->getProductSku();
            switch ($type) {
                case 'year':
                    $tablePeriod = date('Y-01-01', strtotime($item->getCreatedAt()));
                    $key = $tablePeriod.'_'.$storeId.'_'.$productSku;
                    break;
                
                case 'month':
                default:
                    $tablePeriod = date('Y-m-01', strtotime($item->getCreatedAt()));
                    $key = $tablePeriod.'_'.$storeId.'_'.$productSku;
                    break;
            }

            $insertBatches[$key] = [
                'period' => $tablePeriod,
                'store_id' => $storeId,
                'product_id' => $item->getProductId(),
                'product_sku' => $productSku,
                'product_name' => $item->getProductName(),
                'qty_ordered' => ($insertBatches[$key]['qty_ordered'] ?? 0) + (float) $item->getQtyOrdered(),
                'row_total' => ($insertBatches[$key]['row_total'] ?? 0) + (float) $item->getRowTotal(),
                'subscription_ids' => $this->mergeSubscriptionIds(
                    ($insertBatches[$key]['subscription_ids'] ?? []),
                    explode(',', $item->getSubscriptionIds())
                )
            ];
        }

        foreach (array_chunk($insertBatches, 100) as $batch) {
            foreach ($batch as &$single) {
                $single['subscription_ids'] = implode(',', array_unique(($single['subscription_ids'])));
            }
            
            $connection->insertMultiple($aggregationTable, $batch);
        }
    }

    protected function mergeSubscriptionIds($existingIds, $newIds)
    {
        return array_merge($existingIds, $newIds);
    }
}
