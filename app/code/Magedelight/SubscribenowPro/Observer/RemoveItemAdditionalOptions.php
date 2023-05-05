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

namespace Magedelight\SubscribenowPro\Observer;

class RemoveItemAdditionalOptions implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magedelight\Subscribenow\Helper\Data
     */
    protected $subscriberHelper;

    /**
     * @param \Magedelight\Subscribenow\Helper\Data $subscriberHelper
     */
    public function __construct(
        \Magedelight\Subscribenow\Helper\Data $subscriberHelper
    ) {
        $this->subscriberHelper = $subscriberHelper;
    }

    /**
     * This will remove additional_options to cart item when ordering from admin panel which is default magento adding
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->subscriberHelper->isModuleEnable()) {
            $productOptions = $observer->getDataObject()->getProductOptions();
            $productOptionsAdditional = $productOptions['additional_options'] ?? false;
            if ($productOptions && $productOptionsAdditional) {
                $additional_options = $productOptions['additional_options'];
                foreach ($productOptions['additional_options'] as $addition_option_key => $addition_option) {
                    $option_code = $addition_option['code'] ?? false;
                    if (in_array($option_code, [
                        'billing_period_title',
                        'billing_cycle_title',
                        'init_amount',
                        'trial_amount',
                        'trial_period_title',
                        'trial_cycle_title',
                        'md_sub_start_date'
                    ])) {
                        unset($additional_options[$addition_option_key]);
                    }
                }

                if ($additional_options) {
                    $productOptions['additional_options'] = $additional_options;
                } else {
                    unset($productOptions['additional_options']);
                }
            }
            $observer->getDataObject()->setProductOptions($productOptions);
        }

        return $this;
    }
}
