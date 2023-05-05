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
 * @package   FME_CustomMessages
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Productvideos\Block\Adminhtml\Productvideos\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Product extends \Magento\Backend\Block\Widget\Grid\Extended {

    protected $_coreRegistry = null;
    protected $_productFactory;
    protected $_eventFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $groupCollection,
        \FME\Productvideos\Model\Productvideos $eventFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->productVisibility = $productVisibility;
        $this->productStatus = $productStatus;
        $this->_groupCollection = $groupCollection;
        $this->_eventFactory = $eventFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_catalogConfig = $catalogConfig;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct() {
        parent::_construct();
        $this->setId('catalog_category_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(['in_category' => 1]);
        }
    }
    public function getCategory() {
        return $this->_coreRegistry->registry('productvideos_data');
    }

    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = $this->_productFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->addOrder('entity_id', 'asc');
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());  //    return $collection;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_category', [
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_category',
            'values' => $this->_getSelectedProducts(),
            'index' => 'entity_id',
            'header_css_class' => 'col-select col-massaction',
            'column_css_class' => 'col-select col-massaction',
            'use_index' => true
                ]
        );
        $this->addColumn(
            'entity_id', [
            'header' => __('ID'),
            'sortable' => true,
            'index' => 'entity_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
                ]
        );
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku'
        ]);
        $this->addColumn(
                'price', [
            'header' => __('Price'),
            'type' => 'currency',
            'currency_code' => (string) $this->_scopeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            'index' => 'price'
                ]
        );
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsGrid', ['_current' => true]);
    }

    // public function getGridUrl()
    // {
    //     return $this->getUrl('custommessages/attribute/grid', ['_current' => true]);
    // }

    protected function _getSelectedProducts() {
        $id = $this->getRequest()->getParam('id');
        if($id){
            $relatedProducts = $this->_eventFactory->getRelatedPro($id);
            if ($relatedProducts) {
                $relatedProducts = array_values($relatedProducts);
                return $relatedProducts;
            } else {
                return [];
            }
        }
        return [];
    }

}
