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

namespace FME\Productvideos\Model\ResourceModel;

class Productvideos extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_store = null;
    protected $_date;
    protected $_storeManager;
    protected $dateTime;
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_objectManager = $objectManager;
    }
    public function _construct()
    {
        $this->_init('productvideos', 'video_id');
    }
    
    protected function _afterLoad(
        \Magento\Framework\Model\AbstractModel $object
    ) {
        $select = $this->getConnection()->select()
          ->from($this->getTable('productvideos_store'))
          ->where('productvideos_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $storesArray = [];
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }

            $object->setData('store_id', $storesArray);
        }
        $select = $this->getConnection()->select()
          ->from($this->getTable('productvideos_products'))
          ->where('productvideos_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $productsArray = [];
            foreach ($data as $row) {
                $productsArray[] = $row['product_id'];
            }
            $object->setData('product_id', $productsArray);
        }
        return parent::_afterLoad($object);
    }
    
    public function addCustomerGroupFilter($value)
    {
        $this->getSelect()
                ->join(
                    ['cg' => $this->getTable('productvideos_customer_group')],
                    'main_table.video_id = cg.video_id',
                    []
                )
                ->where('cg.customer_group_id = ?', new \Zend_Db_Expr($value));
        return $this;
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = $this->getConnection()->quoteInto('productvideos_id = ?', $object->getId());
        if (isset($object['product_id'])) {
            $links = $object['product_id'];
            $productIds = $links;
            $this->getConnection()->delete(
                $this->getTable('productvideos_products'),
                $condition
            );
            $this->getConnection()->delete(
                $this->getTable('productvideos_products'),
                $condition
            );
            foreach ($productIds as $_productId) {
                $productsArray = [];
                $productsArray['productvideos_id'] = $object->getId();
                $productsArray['product_id'] = $_productId;
                $this->getConnection()
                ->insert(
                    $this->getTable('productvideos_products'),
                    $productsArray
                );
            }
        }
        if (isset($object['store_id'])) {
            $this->getConnection()
            ->delete(
                $this->getTable('productvideos_store'),
                $condition
            );
            foreach ((array)$object['store_id'] as $store) {
                $storeArray = [];
                $storeArray['productvideos_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->getConnection()
                ->insert(
                    $this->getTable('productvideos_store'),
                    $storeArray
                );
            }
        }
        $condition = $this->getConnection()->quoteInto('video_id = ?', $object->getId());
        if (isset($object['customer_group_ids'])) {
            $this->getConnection()
            ->delete(
                $this->getTable('productvideos_customer_group'),
                $condition
            );
            foreach ((array)$object['customer_group_ids'] as $store) {
                $storeArray = [];
                $storeArray['video_id'] = $object->getId();
                $storeArray['customer_group_id'] = $store;
                $this->getConnection()
                ->insert(
                    $this->getTable('productvideos_customer_group'),
                    $storeArray
                );
            }
        }
        return parent::_afterSave($object);
    }
}
