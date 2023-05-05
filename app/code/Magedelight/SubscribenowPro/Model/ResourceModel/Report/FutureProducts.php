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

use Magento\Store\Model\ScopeInterface;
use Magedelight\Subscribenow\Model\ResourceModel\ProductSubscribers\CollectionFactory;
use Magedelight\Subscribenow\Model\Source\ProfileStatus;
use Magedelight\Subscribenow\Helper\Data as SubscribenowHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Bestsellers report resource model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FutureProducts extends \Magento\Sales\Model\ResourceModel\Report\AbstractReport
{
    const AGGREGATION_DAILY = 'md_subscribenow_futureproducts_aggregated_daily';

    const AGGREGATION_MONTHLY = 'md_subscribenow_futureproducts_aggregated_monthly';

    const AGGREGATION_YEARLY = 'md_subscribenow_futureproducts_aggregated_yearly';

    protected $resource;
    protected $collectionFactory;
    protected $attributeRepositoryInterface;
    protected $timezone;
    protected $storeManager;
    protected $scopeConfig;
    protected $subscribenowHelper;
    protected $productRepository;
    protected $serialize;
    protected $dynamicProductPriceCache = [];

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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        SubscribenowHelper $subscribenowHelper,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
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
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->subscribenowHelper = $subscribenowHelper;
        $this->productRepository = $productRepository;
        $this->serialize = $serialize;
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

            $aggregated_product = [];

            $collection = $this->getProductSubscribersCollection();
            foreach ($collection as $subscription) {
                $dates = $this->getFutureDates($subscription);

                foreach ($dates as $date) {
                    $arrayElement = $aggregated_product
                        [$date]
                        [$subscription->getData('store_id')]
                        [$subscription->getData('product_sku')] ?? [];

                    if (!$arrayElement) {
                        $arrayElement['product_id'] = $subscription->getData('product_id');
                        $arrayElement['product_sku'] = $subscription->getData('product_sku');
                        $arrayElement['product_name'] = $subscription->getData('product_name');
                    }

                    $old_qty_ordered = $arrayElement['qty_ordered'] ?? 0;
                    $arrayElement['qty_ordered'] = (int) $old_qty_ordered + (int) $subscription->getData('qty_ordered');

                    $old_row_total = $arrayElement['row_total'] ?? 0;
                    $arrayElement['row_total'] = (float) $old_row_total + $this->getItemPrice($subscription);

                    $arrayElement['subscription_ids'][] = $subscription->getData('subscription_id');

                    $aggregated_product
                        [$date]
                        [$subscription->getData('store_id')]
                        [$subscription->getData('product_sku')] = $arrayElement;
                }
            }
            
            $insertBatches = [];
            foreach ($aggregated_product as $period => $aggregare_store) {
                foreach ($aggregare_store as $store_id => $aggregate_product) {
                    foreach ($aggregate_product as $product_sku => $info) {
                        $insertBatches[] = [
                            'period'             => $period,
                            'store_id'           => $store_id,
                            'product_id'         => $info['product_id'],
                            'product_sku'        => $info['product_sku'],
                            'product_name'       => $info['product_name'],
                            'qty_ordered'        => $info['qty_ordered'],
                            'subscription_ids'   => implode(',', array_unique($info['subscription_ids'])),
                            'row_total'          => $info['row_total']
                        ];
                    }
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
                $aggregated_product
            );
            $this->updateReportMonthlyYearlyCustom(
                $connection,
                'year',
                'qty_ordered',
                $mainTable,
                $this->getTable(self::AGGREGATION_YEARLY),
                $aggregated_product
            );
            
            $this->_setFlagData(\Magedelight\SubscribenowPro\Model\Flag::REPORT_SUBSCRIBENOW_FUTUREPRODUCTS_FLAG_CODE);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    public function getProductSubscribersCollection()
    {
        $collection = $this->collectionFactory->create();
        $resource = $collection->getResource();
        $collection
            ->addFieldToFilter('main_table.subscription_status', ProfileStatus::ACTIVE_STATUS);

        $collection
            ->getSelect()
            ->columns(
                [
                    'qty_ordered'  => 'main_table.qty_subscribed',
                    'row_total'    => new \Zend_Db_Expr('SUM(main_table.base_billing_amount)'),
                    'product_name' => new \Zend_Db_Expr("MIN(main_table.product_name)"),
                    'product_sku'  => 'main_table.product_sku',
                ]
            )
            ->group("main_table.subscription_id");
        
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

    public function getFutureDates($subscription)
    {
        $next_occurrence_date = $subscription->getData('next_occurrence_date');
        $next_occurrence_date_dateonly = date('Y-m-d', strtotime($next_occurrence_date));
        $dates = [$next_occurrence_date_dateonly];

        $is_trial = $subscription->getData('is_trial');
        $trial_bill_count = $subscription->getData('trial_count');
        $trial_billing_period = $subscription->getData('trial_period_unit');
        $trial_billing_frequency = $subscription->getData('trial_period_frequency');
        $trial_period_max_cycles = $subscription->getData('trial_period_max_cycle');
        if ($trial_period_max_cycles == 0) {
            $trial_period_max_cycles = 365;
        }

        $bill_count = $subscription->getData('total_bill_count');
        $billing_period = $subscription->getData('billing_period');
        $billing_frequency = $subscription->getData('billing_frequency');
        $period_max_cycles = $subscription->getData('period_max_cycles');
        if ($period_max_cycles == 0) {
            $period_max_cycles = 365;
        }

        /**
         * this is becuase we are already adding next_occurance_date as first date
         * so in case of trial and real billing, we have to skip first date
         */
        $skipped_firstdate = false;
        if ($is_trial) {
            for ($i=$trial_bill_count; $i<$trial_period_max_cycles; $i++) {
                if ($this->canAddNewDate($skipped_firstdate, $dates, $i, $trial_bill_count)) {
                    $this->pushNewDate($trial_billing_period, $trial_billing_frequency, $dates);
                }
            }
        }

        for ($i=$bill_count; $i<$period_max_cycles; $i++) {
            if ($this->canAddNewDate($skipped_firstdate, $dates, $i, $bill_count)) {
                $this->pushNewDate($billing_period, $billing_frequency, $dates);
            }
        }

        return $dates;
    }

    public function pushNewDate($billing_period, $billing_frequency, &$dates)
    {
        $previous_date = end($dates);

        switch ($billing_period) {
            //day
            case 1:
            default:
                $dates[] = date('Y-m-d', strtotime($previous_date. ' + '.($billing_frequency * 1).' days'));
                break;

            //week
            case 2:
                $dates[] = date('Y-m-d', strtotime($previous_date. ' + '.($billing_frequency * 7).' days'));
                break;

            //month
            case 3:
                $dates[] = date('Y-m-d', strtotime($previous_date. ' + '.($billing_frequency * 1).' months'));
                break;
        }
    }

    /**
     * this is becuase we are already adding next_occurance_date as first date
     * so in case of trial and real billing, we have to skip first date
     */
    public function canAddNewDate(&$skipped_firstdate, $dates, $i, $passed_occurance)
    {
        if (!$skipped_firstdate && count($dates) == 1 && ($i == $passed_occurance)) {
            $skipped_firstdate = true;
            return false;
        }

        return true;
    }
    
    public function updateReportMonthlyYearlyCustom(
        $connection,
        $type,
        $column,
        $mainTable,
        $aggregationTable,
        $aggregated_product
    ) {
        $insertBatches = [];
        foreach ($aggregated_product as $period => $aggregare_store) {
            foreach ($aggregare_store as $store_id => $aggregate_product) {
                foreach ($aggregate_product as $product_id => $info) {
                    switch ($type) {
                        case 'year':
                            $tablePeriod = date('Y-01-01', strtotime($period));
                            $key = $tablePeriod.'_'.$store_id.'_'.$product_id;
                            break;
                        
                        case 'month':
                        default:
                            $tablePeriod = date('Y-m-01', strtotime($period));
                            $key = $tablePeriod.'_'.$store_id.'_'.$product_id;
                            break;
                    }

                    $insertBatches[$key] = [
                        'period' => $tablePeriod,
                        'store_id' => $store_id,
                        'product_id' => $info['product_id'],
                        'product_sku' => $info['product_sku'],
                        'product_name' => $info['product_name'],
                        'qty_ordered' => ($insertBatches[$key]['qty_ordered'] ?? 0) + $info['qty_ordered'],
                        'subscription_ids' => $this->mergeSubscriptionIds(
                            ($insertBatches[$key]['subscription_ids'] ?? []),
                            $info['subscription_ids']
                        ),
                        'row_total' => ($insertBatches[$key]['row_total'] ?? 0) + $info['row_total'],
                    ];
                }
            }
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

    public function updateReportMonthlyYearlyDeprecated($connection, $type, $column, $mainTable, $aggregationTable)
    {
        $periodSubSelect = $connection->select();
        $ratingSubSelect = $connection->select();
        $ratingSelect = $connection->select();

        switch ($type) {
            case 'year':
                $periodCol = $connection->getDateFormatSql('t.period', '%Y-01-01');
                break;
            case 'month':
                $periodCol = $connection->getDateFormatSql('t.period', '%Y-%m-01');
                break;
            default:
                $periodCol = 't.period';
                break;
        }

        $columns = [
            'period' => 't.period',
            'store_id' => 't.store_id',
            'product_id' => 't.product_id',
            'product_sku' => 't.product_sku',
            'product_name' => 't.product_name',
        ];

        if ($type == 'day') {
            $columns['id'] = 't.id';  // to speed-up insert on duplicate key update
        }

        $cols = array_keys($columns);
        $cols['total_qty'] = new \Zend_Db_Expr('SUM(t.' . $column . ')');
        $cols['total_row_total'] = new \Zend_Db_Expr('SUM(t.row_total)');
        $cols['total_subscription'] = new \Zend_Db_Expr('SUM(t.subscription_count)');
        $periodSubSelect->from(
            ['t' => $mainTable],
            $cols
        )->group(
            ['t.store_id', $periodCol, 't.product_id']
        )->order(
            ['t.store_id', $periodCol, 'total_qty DESC']
        );

        $cols = $columns;
        $cols[$column] = 't.total_qty';
        $cols['row_total'] = 't.total_row_total';
        $cols['subscription_count'] = 't.total_subscription';
        
        $cols['prevStoreId'] = new \Zend_Db_Expr('(@prevStoreId := t.`store_id`)');
        $cols['prevPeriod'] = new \Zend_Db_Expr("(@prevPeriod := {$periodCol})");
        $ratingSubSelect->from($periodSubSelect, $cols);

        $cols = $columns;
        $cols['period'] = $periodCol;
        $cols[$column] = 't.' . $column;
        $cols['subscription_count'] = 't.subscription_count';
        
        $ratingSelect->from($ratingSubSelect, $cols);

        $sql = $ratingSelect->insertFromSelect($aggregationTable, array_keys($cols));
        $connection->query("SET @pos = 0, @prevStoreId = -1, @prevPeriod = '0000-00-00'");
        $connection->query($sql);

        return $this;
    }

    /**
     * to get item price based on subscribenow setting "Dynamic Price"
     */
    public function getItemPrice($subscription)
    {
        $productId = $subscription->getData('product_id');
        $storeId = $subscription->getData('store_id');
        $websiteCode = $this->storeManager->getStore($storeId)->getWebsite()->getCode();
        $isPriceDynamic = (bool) $this->scopeConfig->getValue('md_subscribenow/general/dynamic_price', ScopeInterface::SCOPE_WEBSITE, $websiteCode);
        if ($isPriceDynamic) {
            $cacheCode = sha1($productId.'_'.$subscription->getOrderItemInfo());
            $price = $this->dynamicProductPriceCache[$cacheCode] ?? null;

            if ($price === null) {
                $product = $this->productRepository->getById($productId, false, $storeId, true);
                $this->subscribenowHelper->setBuyRequest(
                    $product,
                    $this->serialize->unserialize($subscription->getOrderItemInfo())
                );
                
                $price = $product
                    ->setSkipValidateTrial(true)
                    ->setSkipFutureSubscriptionValidation(true)
                    ->getFinalPrice();

                $this->dynamicProductPriceCache[$cacheCode] = $price;
            }
            
            return $price;
        } else {
            return (float) $subscription->getData('row_total');
        }
    }
}
