<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\ViewModel\Order;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Aitoc\ReviewBooster\Service\Order\ReviewProducts;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use \Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class Products implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ReviewProducts
     */
    protected $orderReviewProducts;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param RequestInterface $request
     * @param ReviewProducts $orderReviewProducts
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        RequestInterface $request,
        ReviewProducts $orderReviewProducts,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->orderReviewProducts = $orderReviewProducts;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * Get order products
     *
     * @return array|false
     */
    public function getOrderProducts()
    {
        $isReviewAvailable = $this->orderReviewProducts->isOrderReviewProductAvailable($this->request);
        if (!$isReviewAvailable) {
            return false;
        }
        $orderProducts = $this->orderReviewProducts->getOrderReviewProducts($this->request);
        return $orderProducts;
    }

    /**
     * @param int $productId
     * @return false|\Magento\Catalog\Api\Data\ProductInterface
     * @throws NoSuchEntityException
     */
    public function getProductById($productId)
    {
        $product = $this->productRepository->getById($productId);
        /** @phpstan-ignore-next-line */
        if (!$product) {
            return false;
        }

        return $product;
    }

    /**
     * Get product image URL
     *
     * @param ProductRepositoryInterface $product
     * @return bool|string
     */
    public function getProductImageUrl($product)
    {
        try {
            $store = $this->storeManager->getStore();
            $productImageUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $productImageUrl .= 'catalog/product';
            $productImageUrl .= $product->getImage();

            return $productImageUrl;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return false;
    }
}
