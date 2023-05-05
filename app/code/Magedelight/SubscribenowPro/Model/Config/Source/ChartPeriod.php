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

namespace Magedelight\SubscribenowPro\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ChartPeriod implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $res = [];
        
        $res[] = ['value' => 'last_7_days', 'label' => __('Last 7 Days')];
        $res[] = ['value' => 'last_30_days', 'label' => __('Last 30 Days')];
        $res[] = ['value' => 'current_month', 'label' => __('Current Month')];
        $res[] = ['value' => 'last_month', 'label' => __('Last Month')];
        $res[] = ['value' => 'current_quarter', 'label' => __('Current Quarter')];
        $res[] = ['value' => 'last_quarter', 'label' => __('Last Quarter')];
        $res[] = ['value' => 'current_year', 'label' => __('Current Year')];
        $res[] = ['value' => 'last_year', 'label' => __('Last Year')];

        return $res;
    }
}
