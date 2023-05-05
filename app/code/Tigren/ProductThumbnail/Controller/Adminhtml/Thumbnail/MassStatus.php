<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Controller\Adminhtml\Thumbnail;

/**
 * Class MassStatus
 * @package Tigren\ProductThumbnail\Controller\Adminhtml\Thumbnail
 */
class MassStatus extends \Tigren\ProductThumbnail\Controller\Adminhtml\Action
{

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $thumbnailId = $this->getRequest()->getParam('productthumbnail');
        $status = $this->getRequest()->getParam('status');
        $storeViewId = $this->getRequest()->getParam('store');
        if (!is_array($thumbnailId) || empty($thumbnailId)) {
            $this->messageManager->addError(__('Please select Thumbnail(s).'));
        } else {
            $collection = $this->_thumbnailCollectionFactory->create()
                ->addFieldToFilter('productthumbnail_id', ['in' => $thumbnailId]);
            try {
                foreach ($collection as $item) {
                    $item->setStoreViewId($storeViewId)
                        ->setStatus($status)
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been changed status.', count($thumbnailId))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/', ['store' => $this->getRequest()->getParam('store')]);
    }
}
