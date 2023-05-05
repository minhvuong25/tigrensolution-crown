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

namespace Magedelight\SubscribenowPro\Block\Adminhtml\Reports\Futureproducts\Report\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class SubscriptionCount extends AbstractRenderer
{
    public function render(DataObject $row)
    {
        $subscriptionIds = $row->getSubscriptionIds();
        $subscriptionIds = explode(',', $subscriptionIds);
        $subscriptionIds = array_unique($subscriptionIds);

        return count($subscriptionIds);
    }
}
