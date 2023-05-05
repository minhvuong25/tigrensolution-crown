<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Controller\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Aitoc\ReviewBooster\Service\Order\ReviewProducts;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;

class Items extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var ReviewProducts
     */
    protected $orderReviewProducts;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * Items constructor.
     * @param Context $context
     * @param ReviewProducts $orderReviewProducts
     * @param ResultFactory $resultFactory
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        ReviewProducts $orderReviewProducts,
        ResultFactory $resultFactory,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->orderReviewProducts = $orderReviewProducts;
        $this->resultFactory = $resultFactory;
        $this->pageFactory = $pageFactory;
    }

    /**
     * @return ResponseInterface|Forward|ResultInterface|Page
     */
    public function execute()
    {
        $isReviewAvailable = $this->orderReviewProducts->isOrderReviewProductAvailable($this->_request);
        if (!$isReviewAvailable) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');
            return $resultForward;
        }
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Order review'));
        return $resultPage;
    }
}
