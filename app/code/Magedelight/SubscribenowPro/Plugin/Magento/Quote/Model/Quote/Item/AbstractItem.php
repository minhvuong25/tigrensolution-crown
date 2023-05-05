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

namespace Magedelight\SubscribenowPro\Plugin\Magento\Quote\Model\Quote\Item;

class AbstractItem
{
    /**
     * @var \Magedelight\Subscribenow\Helper\Data
     */
    protected $subscribenowHelper;

    /**
     * @var \Magedelight\Subscribenow\Model\Service\SubscriptionService
     */
    protected $subscriptionService;

    /**
     * @var \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    protected $productSubscribers;

    /**
     * @param \Magedelight\Subscribenow\Helper\Data $subscribenowHelper
     * @param \Magedelight\Subscribenow\Model\Service\SubscriptionService $subscriptionService
     * @param \Magedelight\Subscribenow\Model\Service\SubscriptionService $subscriptionService
     * @param \Magento\Catalog\Model\ProductFactory $productModelFactory
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     */
    public function __construct(
        \Magedelight\Subscribenow\Helper\Data $subscribenowHelper,
        \Magedelight\Subscribenow\Model\Service\SubscriptionService $subscriptionService,
        \Magedelight\Subscribenow\Model\ProductSubscribers $productSubscribers
    ) {
        $this->subscribenowHelper = $subscribenowHelper;
        $this->subscriptionService = $subscriptionService;
        $this->productSubscribers = $productSubscribers;
    }

    /**
     * when adding or updating cart item, check qty against max allowed subscription qty of item and validate
     */
    public function afterCheckData($subject, $result)
    {
        if ($this->subscribenowHelper->isModuleEnable()) {
            $buyRequest = $subject->getBuyRequest()->getData();
            if ($this->subscriptionService->checkProductRequest($buyRequest, $subject)) {
                $qty = $buyRequest['qty'] ?? 1;
                try {
                    $this->productSubscribers->validateQty($qty);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $subject->setHasError(true);
                    $subject->setMessage($e->getMessage());
                    $subject->getQuote()->setHasError(true)->addMessage($e->getMessage());
                }
            }
        }

        return $result;
    }
}
