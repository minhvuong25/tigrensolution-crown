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

namespace Magedelight\SubscribenowPro\Plugin\Magedelight\Subscribenow\Model\Service;

class SubscriptionService
{
    /**
     * @var Magento\Sales\Model\AdminOrder\CreateFactory
     */
    protected $adminOrderCreate;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    protected $priceCurrency;

    protected $store = null;
    protected $currency = null;

    /**
     * @param \Magento\Sales\Model\AdminOrder\Create $adminOrderCreate
     * @param \Magento\Directory\Model\PriceCurrency $priceCurrency
     */
    public function __construct(
        \Magento\Sales\Model\AdminOrder\Create $adminOrderCreate,
        \Magento\Directory\Model\PriceCurrency $priceCurrency
    ) {
        $this->adminOrderCreate = $adminOrderCreate;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * to get current quote from admin, instead of checkout session
     */
    public function aroundGetConvertedPrice($subject, $proceed, $amount)
    {
        $store = $this->getCurrentStore();
        //$currency = $this->getCurrentStoreCurrency();
        return $this->priceCurrency->convert($amount, $store);
    }

    /**
     * get current store for admin area
     */
    protected function getCurrentStore()
    {
        if (empty($this->store)) {
            $this->store = $this->adminOrderCreate->getSession()->getStore();
        }

        return $this->store;
    }

    /**
     * get current store currency for admin area
     */
    protected function getCurrentStoreCurrency()
    {
        if (empty($this->currency)) {
            $this->currency = $this->adminOrderCreate->getSession()->getCurrencyId();
        }

        return $this->currency;
    }
}
