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
namespace FME\Productvideos\Controller\Adminhtml\Productvideos;

class Index extends \FME\Productvideos\Controller\Adminhtml\Productvideos
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Productvideos::productvideos');
        $resultPage->addBreadcrumb(__('Productvideos'), __('Productvideos'));
        $resultPage->addBreadcrumb(__('Manage Videos'), __('Manage Videos'));
        $resultPage->getConfig()->getTitle()->prepend(__('Videos'));
        return $resultPage;
    }
}
