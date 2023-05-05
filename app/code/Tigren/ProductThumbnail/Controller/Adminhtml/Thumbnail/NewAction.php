<?php
/**
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Controller\Adminhtml\Thumbnail;

/**
 * Class NewAction
 * @package Tigren\ProductThumbnail\Controller\Adminhtml\Thumbnail
 */
class NewAction extends \Tigren\ProductThumbnail\Controller\Adminhtml\Action
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
