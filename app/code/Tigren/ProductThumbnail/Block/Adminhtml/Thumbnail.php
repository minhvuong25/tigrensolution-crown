<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Block\Adminhtml;

/**
 * Class Thumbnail
 * @package Tigren\ProductThumbnail\Block\Adminhtml
 */
class Thumbnail extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_Thumbnail';
        $this->_blockGroup = 'Tigren_ProductThumbnail';
        $this->_headerText = __('Thumbnail');
        $this->_addButtonLabel = __('Add New Thumbnail');
        parent::_construct();
    }
}
