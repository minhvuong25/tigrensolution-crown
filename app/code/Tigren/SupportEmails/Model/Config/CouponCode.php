<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\SupportEmails\Model\Config;

/**
 * Class CouponCode
 * @package Tigren\SupportEmails\Model\Config
 */
class CouponCode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $arr = [];
        $couponCollection = $om->create('Magento\SalesRule\Model\Rule')->getCollection();

        foreach ($couponCollection as $rule) {
            $arr[] = ["value" => $rule->getId(), "label" => __($rule->getName()) ];
        }

        return $arr;
    }
}
