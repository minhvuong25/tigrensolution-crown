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

namespace Magedelight\SubscribenowPro\Model\ResourceModel\Report\SubscriptionCycle;

class Collection extends \Magento\Sales\Model\ResourceModel\Report\Collection\AbstractCollection
{
    protected $_product_sku = null;
    /**
     * Selected columns
     *
     * @var array
     */
    protected $_selectedColumns = [];

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\ResourceModel\Report $resource
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\ResourceModel\Report $resource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $resource->init($this->getAggregatedTableName());
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $resource, $connection);
    }

    public function getAggregatedTableName()
    {
        return 'md_view_subscribenow_subscription_cycle_aggregated';
    }

    /**
     * Retrieve selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $connection = $this->getConnection();

        if (!$this->_selectedColumns) {
            if ($this->isTotals()) {
                $this->_selectedColumns = $this->getAggregatedColumns();
            } else {
                $this->_selectedColumns = [
                    'billing_period_label' => 'MAX(billing_period_label)',
                    'product_sku' => 'product_sku',
                    'product_name' => 'MAX(product_name)',
                    'subscription_qty' => 'SUM(subscription_qty)',
                ];
            }
        }
        return $this->_selectedColumns;
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _applyAggregatedTable()
    {
        $select = $this->getSelect();

        $mainTable = $this->getTable($this->getAggregatedTableName());
        $select->from($mainTable, $this->_getSelectedColumns());

        if (!$this->isTotals()) {
            $select->group([
                'billing_period',
                'billing_frequency',
                'product_sku'
            ]);
        }

        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $select = clone $this->getSelect();
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        return $this->getConnection()->select()->from($select, 'COUNT(*)');
    }

    /**
     * Set ids for store restrictions
     *
     * @param  int|int[] $storeIds
     * @return $this
     */
    public function addStoreRestrictions($storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }
        $currentStoreIds = $this->_storesIds;
        if (isset(
            $currentStoreIds
        ) && $currentStoreIds != \Magento\Store\Model\Store::DEFAULT_STORE_ID && $currentStoreIds != [
            \Magento\Store\Model\Store::DEFAULT_STORE_ID
        ]
        ) {
            if (!is_array($currentStoreIds)) {
                $currentStoreIds = [$currentStoreIds];
            }
            $this->_storesIds = array_intersect($currentStoreIds, $storeIds);
        } else {
            $this->_storesIds = $storeIds;
        }

        return $this;
    }

    /**
     * Redeclare parent method for applying filters after parent method
     * but before adding unions and calculating totals
     *
     * @return $this|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->_applyStoresFilter();
        $this->applyAdditionalFilters();

        return $this;
    }

    public function applyAdditionalFilters($select = null)
    {
        if (!$select) {
            $select = $this->getSelect();
        }
        if ($productSku = $this->getProductSku()) {
            $productSkuArray = explode(',', $productSku);
            $productSkuArray = array_map('trim', $productSkuArray);
            $productSkuArray = array_filter(array_unique($productSkuArray));
            if ($productSkuArray) {
                $select->where('product_sku IN (?)', $productSkuArray);
            }
        }

        return $this;
    }

    public function getProductSku()
    {
        return $this->_product_sku;
    }

    public function setProductSku($product_sku)
    {
        $this->_product_sku = $product_sku;
    }
}
