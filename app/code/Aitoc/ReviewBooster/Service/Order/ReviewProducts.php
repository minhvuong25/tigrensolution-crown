<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Service\Order;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as SalesOrderCollectionFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class ReviewProducts extends AbstractModel
{
    /**
     * @var SalesOrderCollectionFactory
     */
    protected $salesOrderCollectionFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * ReviewProducts constructor.
     *
     * @param SalesOrderCollectionFactory $salesOrderCollectionFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param StoreManagerInterface $storeManager
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        SalesOrderCollectionFactory $salesOrderCollectionFactory,
        OrderRepositoryInterface $orderRepository,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager
    ) {
        $this->salesOrderCollectionFactory = $salesOrderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
    }

    /**
     * Check if the order review product available
     *
     * @param RequestInterface $request
     * @return bool
     */
    public function isOrderReviewProductAvailable($request): bool
    {
        if (!$this->isRequestContainRequiredOptions($request)) {
            return false;
        }

        return $this->isOrderExistByRequest($request);
    }

    /**
     * Get order review products
     *
     * @param RequestInterface $request
     * @return array|false
     */
    public function getOrderReviewProducts($request)
    {
        $salesOrderCollection = $this->getOrderByRequest($request)->getFirstItem();
        /** @phpstan-ignore-next-line */
        if (!$salesOrderCollection) {
            return false;
        }

        $order = $this->orderRepository->get($salesOrderCollection->getEntityId());
        /** @phpstan-ignore-next-line */
        if (!$order || !$order->getAllVisibleItems()) {
            return false;
        }

        return $order->getAllVisibleItems();
    }

    /**
     * Get order review page URL
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderReviewPageUrl($orderId)
    {
        $orderReviewPageUrl = '';
        try {
            $salesOrderCollection = $this->salesOrderCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', (string) $orderId);

            $order = $salesOrderCollection->getFirstItem();
            /** @phpstan-ignore-next-line */
            if ($order
                && $order->getData('review_booster_guid')
                && $order->getData('customer_email')
            ) {
                $orderReviewPageUrl = $this->getPageUrl(
                    $order->getData('customer_email'),
                    $orderId,
                    $order->getData('review_booster_guid'),
                    $order->getData('store_id')
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $orderReviewPageUrl;
    }

    /**
     * Get page URL
     *
     * @param string $email
     * @param int $orderId
     * @param int $guid
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    private function getPageUrl($email, $orderId, $guid, $storeId)
    {
        $baseUrl = $this->storeManager->getStore($storeId)->getBaseUrl();
        $email = rawurlencode(trim($email));
        return "{$baseUrl}order-review/product/items/id/{$orderId}/GUID/{$guid}/email/{$email}";
    }

    /**
     * Check if the order exists by request
     *
     * @param RequestInterface $request
     * @return bool
     */
    private function isOrderExistByRequest($request): bool
    {
        $salesOrderCollection = $this->getOrderByRequest($request);

        return (bool)$salesOrderCollection->getSize();
    }

    /**
     * Get order by request
     *
     * @param RequestInterface $request
     * @return OrderCollection
     */
    private function getOrderByRequest($request)
    {
        $salesOrderCollection = $this->salesOrderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', $request->getParam('id'))
            ->addFieldToFilter('review_booster_guid', $request->getParam('GUID'))
            ->addFieldToFilter('customer_email', $request->getParam('email'));

        return $salesOrderCollection;
    }

    /**
     * Check if the request contains the required options
     *
     * @param RequestInterface $request
     * @return bool
     */
    private function isRequestContainRequiredOptions($request): bool
    {
        if ($request->getParam('id')
            && $request->getParam('GUID')
            && $request->getParam('email')
        ) {
            return true;
        }

        return false;
    }
}
