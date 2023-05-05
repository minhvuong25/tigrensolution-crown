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

namespace Magedelight\SubscribenowPro\Plugin\Magedelight\Subscribenow\Cron;

use Magedelight\SubscribenowPro\Helper\Data;
use Magedelight\SubscribenowPro\Model\MergeOrder;

class Generate
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var MergeOrder
     */
    protected $mergeOrder;

    /**
     * @param Data $helper
     * @param MergeOrder $mergeOrder
     */
    public function __construct(
        Data $helper,
        MergeOrder $mergeOrder
    ) {
        $this->helper = $helper;
        $this->mergeOrder = $mergeOrder;
    }

    public function aroundExecute($subject, callable $proceed)
    {
        if ($this->helper->isEnable()) {
            $this->mergeOrder->generate();
        } else {
            $proceed();
        }
    }
}
