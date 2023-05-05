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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveReviewBlocks implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        $remove = $this->scopeConfig->getValue(
            'review_booster/review_images/review_mode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($remove) {
            /** @var \Magento\Framework\View\Layout $layout */
            $layout = $observer->getLayout();
            $linkBlock = $layout->getBlock('product.info.review');
            $tabBlock = $layout->getBlock('reviews.tab');

            if ($linkBlock) {
                $layout->unsetElement('product.info.review');
            }
            if ($tabBlock) {
                $layout->unsetElement('reviews.tab');
            }
        }
    }
}
