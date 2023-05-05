<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Productvideos
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Productvideos\Model\ResourceModel\Productvideos;

use Magento\Store\Model\Store;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $_idFieldName = 'video_id';

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_storeManager = $storeManager;
    }
    /**
     * _construct
     *
     */
    public function _construct()
    {
        
        $this->_init(
            '\FME\Productvideos\Model\Productvideos',
            '\FME\Productvideos\Model\ResourceModel\Productvideos'
        );
    }

    protected function _afterLoad()
    {
        $linkedIds = $this->getColumnValues('video_id');
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                ['productvideos_store' => $this->getTable('productvideos_store')]
            )->where(
                'productvideos_store.productvideos_id IN (?)',
                $linkedIds
            );
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData['productvideos_id']][] = $storeData['store_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData('video_id');
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $storeIdKey = array_search(
                        Store::DEFAULT_STORE_ID,
                        $storesData[$linkedId],
                        true
                    );
                    if ($storeIdKey !== false) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = current($storesData[$linkedId]);
                        $storeCode = $this->_storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }
    }

    /**
     * addStoreFilter
     * @param $store
     */
    public function addCustomerGroupFilter($value)
    {
       // echo "sada";exit;
        
        $this->getSelect()
                ->join(
                    ['cg' => $this->getTable('productvideos_customer_group')],
                    'main_table.video_id = cg.video_id',
                    []
                )
                ->where('cg.customer_group_id = ?', new \Zend_Db_Expr($value));
        return $this;
    }
    public function addStoreFilter($store)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        $this->getSelect()->join(
            ['store_table' => $this->getTable('productvideos_store')],
            'main_table.video_id = store_table.productvideos_id',
            []
        )
        ->where('store_table.store_id in (?)', [0,$store]);

        return $this;
    }
}
