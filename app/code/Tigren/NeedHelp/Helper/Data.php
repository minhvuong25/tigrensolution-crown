<?php
/**
 * @Author: nguyen
 * @Date:   2020-02-12 14:01:01
 * @Last Modified by:   Alex Dong
 * @Last Modified time: 2020-04-25 20:39:04
 */

namespace Tigren\NeedHelp\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $entityAttribute;

    protected $_storeManager;

    public function __construct(
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->entityAttribute = $entityAttribute;
        $this->_storeManager = $storeManager;
    }

    /**
     * Load attribute data by code
     * @return  \Magento\Eav\Model\Entity\Attribute
     */
    public function getAttributeInfo($attributeCode)
    {
        return $this->entityAttribute->loadByCode('catalog_product', $attributeCode);
    }

    /**
     *
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
}
