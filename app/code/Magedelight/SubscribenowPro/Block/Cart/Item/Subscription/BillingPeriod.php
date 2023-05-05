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

class BillingPeriod extends Subscription
{
    public function isCustomerDefined()
    {
        return (bool) ($this->getProduct()->getBillingPeriodType() == 'customer');
    }

    public function getBillingPeriods()
    {
        return $this->subscriptionHelper->getSubscriptionInterval(true, 'interval_label');
    }

    public function getAdminDefinedBillingLabel()
    {
        return __(
            'Delivery Every: %1 %2',
            $this->getSubscription()->getBillingFrequency(),
            ucfirst($this->getSubscription()->getBillingPeriod())
        );
    }

    public function isSelected($period = 0)
    {
        if ($period == $this->getSelectedPeriod()) {
            return 'selected="selected"';
        }

        return '';
    }

    protected function getSelectedPeriod()
    {
        $buyRequest = $this->getBuyRequest();
        $selectedPeriod = $buyRequest['billing_period'] ?? false;

        return $selectedPeriod;
    }
}
