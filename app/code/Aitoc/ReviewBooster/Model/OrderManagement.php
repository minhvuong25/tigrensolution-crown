<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Model;

use Aitoc\ReviewBooster\Model\ResourceModel\Order\Collection as OrderCollection;
use Aitoc\ReviewBooster\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class OrderManagement
{
    const FIELD_NAME_PRODUCT_ID = 'product_id';
    const FIELD_NAME_CUSTOMER_ID = 'customer_id';

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * OrderManagement constructor.
     * @param OrderCollectionFactory $orderCollectionFactory
     */
    public function __construct(OrderCollectionFactory $orderCollectionFactory)
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Has customer purchased product
     *
     * @param int $customerId
     * @param int $productId
     * @return bool
     */
    public function isCustomerPurchasedProduct($customerId, $productId)
    {
        $salesOrdersCollection = $this->createOrderCollection();
        $salesOrdersCollection->joinSalesOrderTable();
        /** @phpstan-ignore-next-line */
        $salesOrdersCollection->addFieldToFilter(self::FIELD_NAME_PRODUCT_ID, $productId);
        /** @phpstan-ignore-next-line */
        $salesOrdersCollection->addFieldToFilter(self::FIELD_NAME_CUSTOMER_ID, $customerId)
        ;

        return (bool) $salesOrdersCollection->getSize();
    }

    /**
     * @return OrderCollection
     */
    private function createOrderCollection()
    {
        return $this->orderCollectionFactory->create();
    }
}
