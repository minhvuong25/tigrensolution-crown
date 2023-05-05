<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_SubscribenowPro
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\SubscribenowPro\Plugin\Magento\Catalog\Model;

class Product
{
    /**
     * When ordering from admin panel,
     * this function will make product to open popup to choose subscription option,
     * instead of directly adding it to cart
     */
    public function afterCanConfigure($subject, $result)
    {
        if (!$result) {
            if ($subject->getData('is_subscription')) {
                return true;
            }
        }

        return $result;
    }
}
