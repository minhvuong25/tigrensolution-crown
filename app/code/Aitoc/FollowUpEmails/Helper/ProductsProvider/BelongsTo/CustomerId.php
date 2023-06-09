<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper\ProductsProvider\BelongsTo;

use Aitoc\FollowUpEmails\Api\Helper\SearchCriteriaBuilderInterface;
use Aitoc\FollowUpEmails\Helper\ProductsProvider\BaseWithNestedEntities;
use Aitoc\FollowUpEmails\Helper\ProductsProvider\BelongsTo\Order as BelongsToOrderProductsProvider;
use Aitoc\FollowUpEmails\Helper\SearchCriteriaBuilder as SearchCriteriaBuilderHelper;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class CustomerId extends BaseWithNestedEntities
{
    /**
     * @var SearchCriteriaBuilderInterface
     */
    private $searchCriteriaBuilderHelper;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var BelongsToOrderProductsProvider
     */
    private $belongsToOrderProductsProvider;

    /**
     * Customer constructor.
     *
     * @param SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper
     * @param OrderRepositoryInterface $orderRepository
     * @param BelongsToOrderProductsProvider $belongsToOrderProductsProvider
     */
    public function __construct(
        SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper,
        OrderRepositoryInterface $orderRepository,
        BelongsToOrderProductsProvider $belongsToOrderProductsProvider
    ) {
        $this->searchCriteriaBuilderHelper = $searchCriteriaBuilderHelper;
        $this->orderRepository = $orderRepository;
        $this->belongsToOrderProductsProvider = $belongsToOrderProductsProvider;
    }

    /**
     * Get nested entities
     *
     * @param OrderInterface $entityOrId
     * @return OrderInterface[]
     */
    protected function getNestedEntities($entityOrId)
    {
        return $orders = $this->getOrdersByCustomerIdOrderByUpdatedAtDesc($entityOrId);
    }

    /**
     * Get orders by customer id
     *
     * @param int $customerId
     * @return OrderInterface[]
     */
    private function getOrdersByCustomerIdOrderByUpdatedAtDesc($customerId)
    {
        $sortOrders = [
            'updated_at' => SortOrder::SORT_DESC,
        ];

        $filters = [
            ['customer_id', $customerId],
        ];

        $searchCriteria = $this->createSearchCriteria($filters, $sortOrders);

        return $this->getOrdersBySearchCriteria($searchCriteria);
    }

    /**
     * Create search criteria
     *
     * @param array $filters
     * @param array $orders
     * @return SearchCriteria
     */
    private function createSearchCriteria($filters = [], $orders = [])
    {
        return $this->searchCriteriaBuilderHelper->createSearchCriteria($filters, $orders);
    }

    /**
     * Get order by search criteria
     *
     * @param SearchCriteria $searchCriteria
     * @return OrderInterface[]
     */
    private function getOrdersBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $orderSearchResults = $this->getOrderSearchResults($searchCriteria);

        return $orderSearchResults->getItems();
    }

    /**
     * Get order search results
     *
     * @param SearchCriteria $searchCriteria
     * @return OrderSearchResultInterface
     */
    private function getOrderSearchResults(SearchCriteria $searchCriteria)
    {
        return $this->orderRepository->getList($searchCriteria);
    }

    /**
     * Get nested entity products
     *
     * @param OrderInterface $nestedEntity
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    protected function getNestedEntityProducts($nestedEntity, $maxCount, $excludeIds, $checkAvailabilityInStock = false)
    {
        return $this->belongsToOrderProductsProvider->getProducts($nestedEntity, $maxCount, $excludeIds);
    }
}
