<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Review\Plugin\Block\Product;
/**
 * Class View
 * @package Tigren\Review\Plugin\Block\Product
 */
class View
{
    /**
     * @param $subject
     * @param $result
     * @return int
     */
    public function afterGetProductDefaultQty($subject, $result)
    {
        if (empty($result)) {
            $result = 1;
        }
        return $result;
    }
}
