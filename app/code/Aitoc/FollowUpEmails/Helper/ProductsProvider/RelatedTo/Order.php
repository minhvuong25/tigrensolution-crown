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

use Aitoc\FollowUpEmails\Helper\ProductsProvider\BaseWithNestedEntities;
use Aitoc\FollowUpEmails\Helper\ProductsProvider\RelatedTo\OrderItem as RelatedToOrderItemProductsProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class Order extends BaseWithNestedEntities
{
    /**
     * @var RelatedToOrderItemProductsProvider
     */
    private $relatedToOrderItemProductsProvider;

    /**
     * Order constructor.
     * @param RelatedToOrderItemProductsProvider $relatedToOrderItemProductsProvider
     */
    public function __construct(RelatedToOrderItemProductsProvider $relatedToOrderItemProductsProvider)
    {
        $this->relatedToOrderItemProductsProvider = $relatedToOrderItemProductsProvider;
    }

    /**
     * Get nested entities
     *
     * @param OrderInterface $entityOrId
     * @return OrderItemInterface[]
     */
    protected function getNestedEntities($entityOrId)
    {
        return $entityOrId->getItems();
    }

    /**
     * Get nested entity products
     *
     * @param OrderItemInterface $nestedEntity
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    protected function getNestedEntityProducts($nestedEntity, $maxCount, $excludeIds, $checkAvailabilityInStock = false)
    {
        return $this->getOrderItemRelatedProducts($nestedEntity, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }

    /**
     * Get order item related products
     *
     * @param OrderItemInterface $orderItem
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    private function getOrderItemRelatedProducts($orderItem, $maxCount, $excludeIds, $checkAvailabilityInStock)
    {
        return $this->relatedToOrderItemProductsProvider->getProducts(
            $orderItem,
            $maxCount,
            $excludeIds,
            $checkAvailabilityInStock
        );
    }

    /**
     * Get belongs to entity or id products
     *
     * @param OrderInterface $entityOrId
     */
    protected function getBelongsToEntityOrIdProducts($entityOrId)
    {
        // TODO: Implement getBelongsToEntityOrIdProducts() method.
    }
}
