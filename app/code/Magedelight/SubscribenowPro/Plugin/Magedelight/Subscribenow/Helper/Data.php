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

namespace Magedelight\SubscribenowPro\Plugin\Magedelight\Subscribenow\Helper;

class Data
{
    /**
     * @var Magento\Sales\Model\AdminOrder\CreateFactory
     */
    protected $adminOrderCreate;

    /**
     * @param \Magento\Sales\Model\AdminOrder\Create $adminOrderCreate
     */
    public function __construct(
        \Magento\Sales\Model\AdminOrder\Create $adminOrderCreate
    ) {
        $this->adminOrderCreate = $adminOrderCreate;
    }

    /**
     * to get current quote from admin, instead of checkout session
     */
    public function aroundGetCurrentQuote($subject, $proceed, $quote = null)
    {
        if ($quote) {
            return $quote;
        }
        
        $quote = $this->adminOrderCreate->getQuote();
        
        return $quote;
    }
}
