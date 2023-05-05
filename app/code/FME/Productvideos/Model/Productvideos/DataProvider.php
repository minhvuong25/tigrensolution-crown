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
namespace FME\Productvideos\Model\Productvideos;

use FME\Productvideos\Model\ResourceModel\Productvideos\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\Collection
     */
    protected $collection;
    public $_storeManager;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;
    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $blockCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $blockCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\ResourceConnection $rcollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $blockCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->rcollection=$rcollection;
        $this->_storeManager=$storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $baseurl =  $this->_storeManager->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Magento\Cms\Model\Block $block */
        foreach ($items as $block) {
            $this->loadedData[$block->getId()] = $block->getData();
             $temp = $block->getData();
             $temp1 = $block->getData();
                $img = [];
            if ($temp['video_file']!='') {
                $img[0]['name'] = $temp['video_file'];
                $img[0]['url'] = $baseurl.$temp['video_file'];
                $temp['video_file'] = $img;
            }

               $thumb = [];
                $thumb[0]['name'] = $temp['video_thumb'];
            if (strpos($temp['video_thumb'], 'https://') !== false) {
                $thumb[0]['url'] = $temp['video_thumb'];
            } else {
                $thumb[0]['url'] = $baseurl.$temp['video_thumb'];
            }
               
               $temp['video_thumb'] = $thumb;
               $select = $this->rcollection->getConnection()->select()
               ->from($this->rcollection->getTableName('productvideos_customer_group'))
               ->where('video_id = ?', $block->getId());
               $storesArray = [];
               if ($data = $this->rcollection->getConnection()->fetchAll($select)) {
                   $storesArray = [];
                   foreach ($data as $row) {
                       $storesArray[] = $row['customer_group_id'];
                   }
       
               }
               $temp['customer_group_ids'] = $storesArray;
               $temp1['customer_group_ids'] = $storesArray;



        }
        $data = $this->dataPersistor->get('productvideos');
        //print_r($data->getData() );exit;
        if (!empty($data)) {
            $block = $this->collection->getNewEmptyItem();
            $block->setData($data);
            $this->loadedData[$block->getId()] = $block->getData();
             
            $this->dataPersistor->clear('productvideos');
        }

        if (empty($this->loadedData)) {
            return $this->loadedData;
        } else {
            if ($block->getData('video_file') != null || $block->getData('video_thumb') != null) {
                
                $t2[$block->getId()] = $temp;
                return $t2;
            } else {

                $t2[$block->getId()] = $temp1;
                return $t2;
            }
        }
    }
}
