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

namespace FME\Productvideos\Controller\Adminhtml;

use \Magento\Framework\View\Result\LayoutFactory;

abstract class Productvideos extends \Magento\Backend\App\Action
{
    protected $_resultLayoutFactory;
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

    protected function _initProductProductvideos()
    {
        $productvideos = $this->_objectManager->create('FME\Productvideos\Model\Productvideos');
        $productvideosId = (int) $this->getRequest()->getParam('video_id');
        if ($productvideosId) {
            $productvideos->load($productvideosId);
        }
        $this->_objectManager->get(
            'Magento\Framework\Registry'
        )->register(
            'current_productvideos_products',
            $productvideos
        );
        return $productvideos;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
