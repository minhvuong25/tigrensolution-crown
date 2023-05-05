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

namespace Magedelight\SubscribenowPro\Plugin\Magedelight\Subscribenow\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;

class AddToCartObserver
{
    /**
     * Constructor
     * @param Data $helper
     * @param Subscription $subscription
     * @param SubscriptionService $subscriptionService
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * from cart page, if subscription is removed, qty validation should not execute
     */
    public function aroundExecute($subject, $proceed, EventObserver $observer)
    {
        $callParentFunction = true;

        $item = $observer->getEvent()->getItem();
        if ($item->getProductId()) {
            $cartRequest = $this->request->getParam('cart', []);
            if ($cartRequest) {
                foreach ($cartRequest as $itemId => $post) {
                    if ($itemId == $item->getId()) {
                        $subscriptionItem = $post['subscription']['_1'] ?? false;

                        if ($subscriptionItem != 'subscription') {
                            $callParentFunction = false;
                        }
                    }
                }
            }
        }

        if ($callParentFunction) {
            $proceed($observer);
        }
    }
}
