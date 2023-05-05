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
 * Class Edit
 * @package Tigren\ProductThumbnail\Controller\Adminhtml\Thumbnail
 */
class Edit extends \Tigren\ProductThumbnail\Controller\Adminhtml\Action
{

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('productthumbnail_id');
        $storeViewId = $this->getRequest()->getParam('store');
        $model = $this->_thumbnailFactory->create();

        if ($id) {
            $model->setStoreViewId($storeViewId)->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This thumbnail no longer exists.'));
                $resultRedirect = $this->_resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('productthumbnail', $model);

        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }
}
