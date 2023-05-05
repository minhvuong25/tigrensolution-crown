<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Model\ResourceModel;

/**
 * Class Thumbnail
 * @package Tigren\ProductThumbnail\Model\ResourceModel
 */
class Thumbnail extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('tigren_productthumbnail', 'productthumbnail_id');
    }
}
