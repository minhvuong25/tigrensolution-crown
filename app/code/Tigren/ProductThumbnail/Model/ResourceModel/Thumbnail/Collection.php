<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail;

/**
 * Class Collection
 * @package Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Tigren\ProductThumbnail\Model\Thumbnail', 'Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail');
    }
}
