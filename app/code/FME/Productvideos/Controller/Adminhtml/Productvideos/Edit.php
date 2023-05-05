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

use Magento\Backend\App\Action;
 
class Edit extends \Magento\Backend\App\Action
{
    
    // const ADMIN_RESOURCE = 'FME_Articles::manage_videos';

    
    protected $_coreRegistry;
    protected $resultPageFactory;
    protected $model;
    
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \FME\Productvideos\Model\Productvideos $model,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->model = $model;
        parent::__construct($context);
    }
    
    protected function _initAction()
    {
    
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Productvideos::productvideos_videos')
            ->addBreadcrumb(__('VIDEOS'), __('VIDEOS'))
            ->addBreadcrumb(__('Manage Videos'), __('Manage Videos'));
        return $resultPage;
    }
        
    public function execute()
    {

        $id = $this->getRequest()->getParam('video_id');
        if ($id) {
            $this->model->load($id);
            if (!$this->model->getId()) {
                $this->messageManager
                ->addError(__('This video no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
      //  print_r($this->model->getData());exit;
        $this->_coreRegistry->register('productvideos_data', $this->model);

        $resultPage = $this->_initAction();

        $resultPage->addBreadcrumb(
            $id ? __('Edit Video') : __('New Video'),
            $id ? __('Edit Video') : __('New Video')
        );
        
        $resultPage->getConfig()->getTitle()->prepend(__('Videos'));
        $resultPage->getConfig()->getTitle()
            ->prepend($this->model->getId() ? $this->model->getTitle() : __('New Video'));

        return $resultPage;
    }
}
