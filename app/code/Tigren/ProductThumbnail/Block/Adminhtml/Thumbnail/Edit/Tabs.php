<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Block\Adminhtml\Thumbnail\Edit;

/**
 * Class Tabs
 * @package Tigren\ProductThumbnail\Block\Adminhtml\Thumbnail\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * construct.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('thumbnail_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Thumbnail Information'));
    }
}
