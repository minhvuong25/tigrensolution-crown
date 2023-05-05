<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddGuidToOrder implements ObserverInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $order->setReviewBoosterGuid($this->createGUID());
    }

    /**
     * Create GUID
     *
     * @return string
     */
    private function createGUID()
    {
        return uniqid('', true);
    }
}
