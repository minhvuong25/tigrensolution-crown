<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magedelight\SubscribenowPro\Model\AdminOrder;

class Create extends \Magento\Sales\Model\AdminOrder\Create
{
    /**
     * Prepare options array for info buy request
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return array
     */
    protected function _prepareOptionsForRequest($item)
    {
        $newInfoOptions = parent::_prepareOptionsForRequest($item);
        
        $infoBuyRequest = $item->getBuyRequest()->getData();
        $subscriptionOption = $infoBuyRequest['options']['_1'] ?? false;
        if ($subscriptionOption) {
            $newInfoOptions['_1'] = $subscriptionOption;
        }
        
        return $newInfoOptions;
    }
}
