<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magedelight\SubscribeNowHideTC\Plugin\CheckoutAgreements\Model;

use Magedelight\Subscribenow\Helper\Data as DataHelper;
use Magedelight\Subscribenow\Helper\Shipping as ShippingHelper;
use Magento\Checkout\Model\Session;

class AgreementsConfigProvider
{
    /**
     * @var DataHelper
     */
    private $helper;
    /**
     * @var ShippingHelper
     */
    private $shippingHelper;
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Agreements constructor.
     * @param DataHelper $helper
     * @param ShippingHelper $shippingHelper
     * @param Session $checkoutSession
     */
    public function __construct(
        DataHelper $helper,
        ShippingHelper $shippingHelper,
        Session $checkoutSession
    )
    {

        $this->helper = $helper;
        $this->shippingHelper = $shippingHelper;
        $this->checkoutSession = $checkoutSession;
    }

    public function afterGetConfig(
        \Magento\CheckoutAgreements\Model\AgreementsConfigProvider $subject,
        $result
    ) {
        $quote = $this->checkoutSession->getQuote();
        $hasSubscriptionItem = false;
        $agreements = [];

        foreach ($quote->getAllItems() as $item) {
            $isSubscription = $item->getIsSubscription();
            if($isSubscription){
                $hasSubscriptionItem = true;
                continue;
            }
        }

        if(!$hasSubscriptionItem) {
            $agreements['checkoutAgreements'] = [];
            return $agreements;
        }
        return $result;
    }
}

