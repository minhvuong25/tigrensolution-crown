<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Block\Order;

use Aitoc\ReviewBooster\Service\Order\ReviewProducts;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Url;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Review\Helper\Data;
use Magento\Review\Model\RatingFactory;
use Magento\Framework\App\Http\Context as HttpContext;

class Form extends \Magento\Review\Block\Form
{
    /**
     * @var ReviewProducts
     */
    private $orderReviewProducts;

    /**
     * @param Context $context
     * @param EncoderInterface $urlEncoder
     * @param Data $reviewData
     * @param ProductRepositoryInterface $productRepository
     * @param RatingFactory $ratingFactory
     * @param ManagerInterface $messageManager
     * @param HttpContext $httpContext
     * @param Url $customerUrl
     * @param ReviewProducts $orderReviewProducts
     * @param array $data
     * @param Json|null $serializer
     */
    public function __construct(
        Context $context,
        EncoderInterface $urlEncoder,
        Data $reviewData,
        ProductRepositoryInterface $productRepository,
        RatingFactory $ratingFactory,
        ManagerInterface $messageManager,
        HttpContext $httpContext,
        Url $customerUrl,
        ReviewProducts $orderReviewProducts,
        array $data = [],
        ?Json $serializer = null
    ) {
        $this->orderReviewProducts = $orderReviewProducts;
        parent::__construct(
            $context,
            $urlEncoder,
            $reviewData,
            $productRepository,
            $ratingFactory,
            $messageManager,
            $httpContext,
            $customerUrl,
            $data,
            $serializer
        );
    }

    /**
     * Get product id
     *
     * @return int
     */
    protected function getProductId()
    {
        try {
            $productId = $this->getSaleOrderFirstProduct();
            // set first product id
            if ($productId) {
                $this->getRequest()->setParam('id', $productId);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return parent::getProductId();
    }

    /**
     * Get action without id
     *
     * @return string
     */
    public function getActionWithoutId()
    {
        return $this->getUrl(
            'review/product/post',
            [
                '_secure' => $this->getRequest()->isSecure(),
            ]
        );
    }

    /**
     * Get sale order first product
     *
     * @return int|false|void
     */
    private function getSaleOrderFirstProduct()
    {
        $request = $this->getRequest();
        $isReviewAvailable = $this->orderReviewProducts->isOrderReviewProductAvailable($request);
        if (!$isReviewAvailable) {
            return false;
        }

        $products = $this->orderReviewProducts->getOrderReviewProducts($request);
        if (!$products) {
            return false;
        }

        foreach ($products as $product) {
            if ($product->getId()) {
                return $product->getProductId();
            }
        }
    }
}
