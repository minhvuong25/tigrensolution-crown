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
use Aitoc\FollowUpEmails\Helper\ProductsProvider\RelatedTo\Order as RelatedToOrderIdProductsProvider;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class OrderId implements ProductsProviderInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var RelatedToOrderIdProductsProvider
     */
    private $relatedToOrderIdProductsProvider;

    /**
     * OrderId constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param RelatedToOrderIdProductsProvider $relatedToOrderIdProductsProvider
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        RelatedToOrderIdProductsProvider $relatedToOrderIdProductsProvider
    ) {
        $this->orderRepository = $orderRepository;
        $this->relatedToOrderIdProductsProvider = $relatedToOrderIdProductsProvider;
    }

    /**
     * Get products
     *
     * @param mixed $entityOrId
     * @param int $maxCount
     * @param array $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return array|\Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProducts($entityOrId, $maxCount = 0, $excludeIds = [], $checkAvailabilityInStock = false)
    {
        if (!$order = $this->getOrderById($entityOrId)) {
            return [];
        };

        return $this->getRelatedProductsByEntity($order, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }

    /**
     * Get order by id
     *
     * @param int $orderId
     * @return OrderInterface|Order
     */
    private function getOrderById($orderId)
    {
        return $this->orderRepository->get($orderId);
    }

    /**
     * Get related products by entity
     *
     * @param OrderInterface $entity
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return array
     */
    private function getRelatedProductsByEntity($entity, $maxCount, $excludeIds, $checkAvailabilityInStock)
    {
        return $this->relatedToOrderIdProductsProvider->getProducts(
            $entity,
            $maxCount,
            $excludeIds,
            $checkAvailabilityInStock
        );
    }
}
