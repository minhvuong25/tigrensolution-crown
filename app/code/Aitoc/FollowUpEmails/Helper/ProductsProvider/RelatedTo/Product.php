<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper\ProductsProvider\RelatedTo;

use Aitoc\FollowUpEmails\Api\Helper\ProductsProviderInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\DataObject;

class Product implements ProductsProviderInterface
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * Product constructor.
     *
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(StockRegistryInterface $stockRegistry)
    {
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * Get products
     *
     * @param ProductInterface|ProductModel $entityOrId
     * @param int $maxCount
     * @param array $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return array
     */
    public function getProducts($entityOrId, $maxCount = 0, $excludeIds = [], $checkAvailabilityInStock = false)
    {
        $products = $this->getRelatedProducts($entityOrId);

        return $this->applyRestrictions($products, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }

    /**
     * Get related products
     *
     * @param ProductInterface|ProductModel $product
     * @return DataObject[]|ProductInterface[]
     */
    private function getRelatedProducts(ProductInterface $product)
    {
        $relatedProductCollection = $product->getRelatedProductCollection();
        $relatedProductCollection->addFieldToSelect('name');

        return $relatedProductCollection->getItems();
    }

    /**
     * Apply restrictions
     *
     * @param array $products
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return array
     */
    private function applyRestrictions($products, $maxCount = 0, $excludeIds = [], $checkAvailabilityInStock = false)
    {
        if ($excludeIds) {
            $products = $this->filterByExcluded($products, $excludeIds);
        }

        if ($checkAvailabilityInStock) {
            $products = $this->filterByAvailabilityInStock($products);
        }

        if ($maxCount) {
            $products = $this->limitByCount($products, $maxCount);
        }

        return $products;
    }

    /**
     * Filter by excluded
     *
     * @param ProductInterface[] $products
     * @param int[] $excludeIds
     * @return array
     */
    private function filterByExcluded($products, $excludeIds)
    {
        $callback = function (ProductInterface $product) use ($excludeIds) {
            $productId = $product->getId();

            return !in_array($productId, $excludeIds);
        };

        return array_filter($products, $callback);
    }

    /**
     * Filter by availability in stock
     *
     * @param ProductInterface[] $products
     * @return ProductInterface[]
     */
    private function filterByAvailabilityInStock($products)
    {
        $callback = [$this, 'isAvailableInStock'];

        return array_filter($products, $callback);
    }

    /**
     * Limit by count
     *
     * @param ProductInterface[] $products
     * @param int $maxCount
     * @return ProductInterface[]
     */
    private function limitByCount($products, $maxCount)
    {
        return array_slice($products, 0, $maxCount);
    }

    /**
     * Is available in stock
     *
     * @param ProductInterface|ProductModel $productItem
     * @return bool
     *
     * Used as callback in @see filterByAvailabilityInStock()
     */
    private function isAvailableInStock(ProductInterface $productItem)
    {
        $productItemId = $productItem->getId();
        $productItemWebsiteId = $productItem->getStore()->getWebsiteId();

        $stockItem = $this->getStockItem($productItemId, $productItemWebsiteId);

        return $stockItem->getIsInStock();
    }

    /**
     * Get stock item
     *
     * @param int $productId
     * @param int $websiteId
     * @return StockItemInterface
     */
    private function getStockItem($productId, $websiteId)
    {
        return $this->stockRegistry->getStockItem($productId, $websiteId);
    }
}
