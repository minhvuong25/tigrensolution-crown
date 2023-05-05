<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Model;

/**
 * Class Status
 * @package Tigren\ProductThumbnail\Model
 */
class Status
{
    const YES = 1;

    const NO = 0;

    /**
     * get available statuses.
     *
     * @return []
     */
    public static function getAvailableStatuses()
    {
        return [
            self::NO => __('No'),
            self::YES => __('Yes'),
        ];
    }
}
