<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2016-01-05 10:40:51
 * @@Modify Date: 2020-04-26 17:20:42
 * @@Function:
 */

namespace Magiccart\Lookbook\Block\Widget;

class Product extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

     /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    protected $_productCollectionFactory;

    protected $_lookbook;

    protected $_typeId = '1';

    /**
     * @var \Magiccart\Lookbook\Model\LookbookFactory
     */
    protected $lookbookFactory;

    /**
     * @param Context $context
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magiccart\Lookbook\Model\LookbookFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magiccart\Lookbook\Model\LookbookFactory $lookbookFactory,
        array $data = []
    ) {
        $this->urlHelper    = $urlHelper;
        $this->backendUrl   = $backendUrl;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->lookbookFactory = $lookbookFactory;
        parent::__construct( $context, $data );
    }

    protected function _construct()
    {
        $store = $this->_storeManager->getStore()->getStoreId();
        $identifier = $this->getIdentifier();
        $collection = $this->lookbookFactory->create()->getCollection()->addFieldToSelect('*')
                        ->addFieldToFilter('identifier', $identifier)
                        ->addFieldToFilter('type_id', $this->_typeId)
                        ->addFieldToFilter('stores',array( array('finset' => 0), array('finset' => $store)));
        $collection->getSelect()->order('order','ASC');

        $this->_lookbook = $collection->getFirstItem();
        if(!$this->_lookbook){
            echo '<div class="message-error error message">Identifier "'. $identifier . '" not exist.</div> ';          
            return;
        }
        $data = $this->_lookbook->getData();
        $this->addData($data);
        parent::_construct();
    }

    public function getLookbook()
    {
        return $this->_lookbook;
    }

    public function getAdminUrl($adminPath, $routeParams=[], $storeCode = 'default' ) 
    {
        $routeParams[] = [ '_nosid' => true, '_query' => ['___store' => $storeCode]];
        return $this->backendUrl->getUrl($adminPath, $routeParams);
    }

    public function getQuickedit()
    {
        $lookbook = $this->getLookbook();
        if($lookbook){
            $id = $lookbook->getId();
            $routeParams = [
                'lookbook_id' => $id
            ];
            $class      = basename(__FILE__, ".php");
            $adminPath  = 'lookbook/' . strtolower($class) . '/edit';
            $editUrl    = $this->getAdminUrl($adminPath, $routeParams);
            $moduleName = $this->getModuleName();
            $moduleName = str_replace('_', ' > ', $moduleName);
            $quickedit  = [
                [
                    'title' => __('%1 > %2 Id is: %3', $moduleName, $class, $id),
                    'url'   => $editUrl
                ],
                [
                    'title' => __('%1 > %2 Identifier is: %3', $moduleName, $class, $lookbook->getIdentifier()),
                    'url'   => $editUrl
                ],
                [
                    'title' => __('Edit'),
                    'url'   => $editUrl
                ]
            ];
        }

        return $quickedit;      
    }

    public function getProductCollection($producIds)
    {
        $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $producIds));
        return $productCollection;
     
     }

    public function getPinImageUrl($file)
    {
       $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $file;
        return $resizedURL;
    }
    
    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

}
