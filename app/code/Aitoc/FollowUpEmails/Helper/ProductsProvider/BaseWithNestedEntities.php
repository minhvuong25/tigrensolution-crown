<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper\ProductsProvider;

use Magento\Catalog\Api\Data\ProductInterface;
use Aitoc\FollowUpEmails\Api\Helper\ProductsProviderInterface;
use Magento\Sales\Api\Data\OrderInterface;

abstract class BaseWithNestedEntities implements ProductsProviderInterface
{
    /**
     * Get nested entities
     *
     * @param OrderInterface $entityOrId
     * @return array
     */
    abstract protected function getNestedEntities($entityOrId);

    /**
     * Get nested entity products
     *
     * @param mixed $nestedEntity
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    abstract protected function getNestedEntityProducts(
        $nestedEntity,
        $maxCount,
        $excludeIds,
        $checkAvailabilityInStock = false
    );

    /**
     * Get products
     *
     * @param OrderInterface $entityOrId
     * @param int $maxCount
     * @param array $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    public function getProducts($entityOrId, $maxCount = 0, $excludeIds = [], $checkAvailabilityInStock = false)
    {
        $isLimited = (bool) $maxCount;

        $nestedEntities = $this->getNestedEntities($entityOrId);

        if (!$nestedEntities) {
            return [];
        }

        $entityRelatedProducts = [];

        foreach ($nestedEntities as $nestedEntity) {
            $nestedEntityRelatedProducts = $this->getNestedEntityProducts(
                $nestedEntity,
                $maxCount,
                $excludeIds,
                $checkAvailabilityInStock
            );
            $entityRelatedProducts = array_merge($entityRelatedProducts, $nestedEntityRelatedProducts);

            if ($isLimited) {
                $maxCount -= count($nestedEntityRelatedProducts);

                if ($maxCount <= 0) {
                    break;
                }
            }

            $excludeIds = $this->adjustExcludeIds($excludeIds, $nestedEntityRelatedProducts);
        }

        return $entityRelatedProducts;
    }

    /**
     * Adjust exclude ids
     *
     * @param int[] $excludeIds
     * @param ProductInterface[] $newProducts
     * @return int[]
     */
    private function adjustExcludeIds($excludeIds, $newProducts)
    {
        foreach ($newProducts as $newProduct) {
            $excludeIds[] = $newProduct->getId();
        }

        return $excludeIds;
    }
}
