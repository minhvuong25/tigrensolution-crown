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

namespace Magedelight\SubscribenowPro\Block\Cart\Item\Subscription;

use Magedelight\SubscribenowPro\Block\Cart\Item\Subscription;

class BillingDate extends Subscription
{
    public function isCustomerDefined()
    {
        return (bool) ($this->getProduct()->getDefineStartFrom() == 'defined_by_customer');
    }

    public function getSubscriptionDate()
    {
        return $this->getSubscription()->getSubscriptionStartDate();
    }

    public function getSubscriptionSelectedDate()
    {
        $buyRequest = $this->getBuyRequest();
        
        $subscriptionStartDate = $buyRequest['subscription_start_date'] ?? false;

        if ($subscriptionStartDate) {
            return date('Y/m/d', strtotime($subscriptionStartDate));
        }
        
        return $this->timezone->date()->format('Y/m/d');
    }

    public function getCurrentDate()
    {
        return $this->timezone->date()->format('Y/m/d');
    }
}
