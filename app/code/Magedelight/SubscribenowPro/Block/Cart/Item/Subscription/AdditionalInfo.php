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
use Magedelight\Subscribenow\Model\Source\SubscriptionStart;
use Magedelight\Subscribenow\Model\Source\BillingPeriodBy;

class AdditionalInfo extends Subscription
{
    protected $info;
    
    public function hasAdditionalInfo()
    {
        return count($this->getAdditionalInfo());
    }

    public function getAdditionalInfo()
    {
        $this->info = [];

        $this->assignTableFormat();

        if ($this->getDiscountAmount()) {
            $this->info['Discount Amount'] = $this->getDiscountAmount(true);
        }

        if ($this->getInitialAmount()) {
            $this->info['Initial Fee'] = $this->getInitialAmount(true);
        }

        $this->getTrialInfo();
        return $this->info;
    }

    public function getTrialInfo()
    {
        if (!$this->getProduct()->getAllowTrial()) {
            return;
        }

        $this->info['Trial Max Cycle'] = $this->subscriptionService->getTrialMaxCycleLabel();
        $this->info['Trial Amount'] = $this->getTrialAmount(true);
    }

    public function assignTableFormat()
    {
        if ($this->getProduct()->getBillingPeriodType() == BillingPeriodBy::ADMIN) {
            $label = (string) __('Delivery');
            $this->info[$label] = $this->getAdminDefinedBillingLabel();
        }

        if ($this->getProduct()->getDefineStartFrom() != SubscriptionStart::DEFINE_BY_CUSTOMER) {
            $label = (string) __('Start Date');
            $this->info[$label] = $this->getSubscription()->getSubscriptionStartDate();
        }

        $label = (string) __('Billing Max Cycle');
        $this->info[$label] = $this->subscriptionService->getBillingMaxCyclesLabel();
    }

    public function getAdminDefinedBillingLabel()
    {
        return __(
            'Every %1 %2',
            $this->getSubscription()->getBillingFrequency(),
            ucfirst($this->getSubscription()->getBillingPeriod())
        );
    }
}
