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

namespace FME\Productvideos\Block\Adminhtml\Productvideos\Edit;

class AssignProducts extends \Magento\Backend\Block\Template
{
    
    protected $_template = 'productvideos/assign_products.phtml';

    protected $blockGrid;

    protected $registry;

    protected $jsonEncoder;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'FME\Productvideos\Block\Adminhtml\Productvideos\Edit\Tab\Products',
                'category.product.grid'
            );
        }

        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    
    public function getGridHtml()
    {

        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $products = $this->getCategory()->getProductsPosition();
      //echo $products;
      //print_r($this->getCategory()->getData());//
        if (!empty($products)) {
            return $this->jsonEncoder->encode($products);
        }

        return '{}'; 
    }
    /**
     * Retrieve current category instance
     *
     * @return array|null
     */
    public function getCategory()
    {
        return $this->registry->registry('productvideos_data');
    }
}
