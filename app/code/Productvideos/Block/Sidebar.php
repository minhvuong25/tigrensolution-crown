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
namespace FME\Productvideos\Block;

class Sidebar extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry;
    protected $_productvideos;
    public $_helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \FME\Productvideos\Model\Productvideos $model,
        \FME\Productvideos\Helper\Data $helper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_productvideos = $model;
        $this->_helper = $helper;
        parent::__construct($context, $data);
        $this->setTabTitle();
    }

    public function getProductId()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product ? $product->getId() : null;
    }

    public function setTabTitle()
    {
        $title = $this->_helper->getTitle();
        if ($title == null) {
            $title = "Product Videos";
        }
        $this->setTitle(__($title));
    }

    public function getProductVideos()
    {
        $collection = $this->_productvideos->getProductRelatedVideosAll();
        return $collection;
    }
}
