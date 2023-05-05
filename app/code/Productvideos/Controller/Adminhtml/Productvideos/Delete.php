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

class Delete extends \Magento\Backend\App\Action
{    
    protected $model;

    public function __construct(
        Action\Context $context,
        \FME\Productvideos\Model\Productvideos $model
    ) {
        $this->model = $model;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('video_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                $this->model->load($id);
                $title = $this->model->getTitle();
                $this->model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The video has been deleted.'));
                // go to grid
                $this->_eventManager->dispatch(
                    'adminhtml_videopage_on_delete',
                    ['title' => $title, 'status' => 'success']
                );
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_videopage_on_delete',
                    ['title' => $title, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['video_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a video to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
