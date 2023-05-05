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
namespace FME\Productvideos\Block\Adminhtml\Productvideos\Edit;

class AssignAttribute extends \Magento\Backend\Block\Template {

    //protected $_template = 'FME_Productvideos::productvideos/assign_products.phtml';
    protected $_template = 'FME_Productvideos::catalog/category/edit/assign_products.phtml';

    protected $blockGrid;
    protected $registry;
    protected $jsonEncoder;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Json\EncoderInterface $jsonEncoder, array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    public function getBlockGrid() {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                    'FME\Productvideos\Block\Adminhtml\Productvideos\Edit\Tab\Products', 'related.products.gridx'
            );
        }
        return $this->blockGrid;
    }

    public function getGridHtml() {
        return $this->getBlockGrid()->toHtml();
    }
    public function getProductsJson()
    {
        $products = $this->getCategory()->getProductsPosition();
        if (!empty($products)) {
            return $this->jsonEncoder->encode($products);
        }
        return '{}'; 
    }

    public function getCategory()
    {
        return $this->registry->registry('productvideos_data');
    }
    // public function getProductsJson() {
    //      $products = $this->getCategory();
    //      if (!empty($products->getData())) {
    //          return $this->jsonEncoder->encode($products->getData('pro_id'));
    //      }
    //     return '{}';
    // }
    // public function getCategory()
    // {
    //     return $this->registry->registry('productvideos_data');
    // }
    // public function getCategory() {
    //     return $this->registry->registry('fme_custommessages');
    // }

}
