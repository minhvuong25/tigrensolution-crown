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

namespace Magedelight\SubscribenowPro\Model;

class Flag extends \Magento\Reports\Model\Flag
{
    const REPORT_SUBSCRIBENOW_FUTUREPRODUCTS_FLAG_CODE = 'report_subscribenow_futureproducts_aggregated';
    const REPORT_SUBSCRIBENOW_PASTREVENUE_FLAG_CODE = 'report_subscribenow_pastrevenue_aggregated';
}
