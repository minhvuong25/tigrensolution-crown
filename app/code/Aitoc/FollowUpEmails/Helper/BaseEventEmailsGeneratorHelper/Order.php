<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper;

use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Helper\EmailInterface as EmailHelperInterface;
use Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTimeHelper;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

abstract class Order extends BaseEventEmailsGeneratorHelper
{
    const ATTRIBUTE_CODE_ORDER_ID = 'order_id';
    const ATTRIBUTE_CODE_ORDER_STATUS = 'order_status';
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Cart constructor.
     *
     * @param EmailHelperInterface $emailHelper
     * @param DateTimeHelper $dateTimeHelperTimeHelperTimeHelper
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        EmailHelperInterface $emailHelper,
        DateTimeHelper $dateTimeHelperTimeHelperTimeHelper,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($emailHelper, $dateTimeHelperTimeHelperTimeHelper);
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get entity id attribute code
     *
     * @return string
     */
    public function getEntityIdAttributeCode()
    {
        return self::ATTRIBUTE_CODE_ORDER_ID;
    }

    /**
     * Get entity id by entity
     *
     * @param OrderInterface $entity
     * @return int
     */
    public function getEntityIdByEntity($entity)
    {
        return $entity->getEntityId();
    }

    /**
     * Get event timestamp
     *
     * @param OrderInterface $entity
     * @return int
     */
    public function getEventTimestampByEntity($entity)
    {
        $updatedAt = $entity->getUpdatedAt();

        return $this->convertToTimestamp($updatedAt);
    }

    /**
     * Get entity by id
     *
     * @param int $entityId
     * @return OrderInterface
     */
    public function getEntityById($entityId)
    {
        return $this->getOrderById($entityId);
    }

    /**
     * Get order by id
     *
     * @param int $orderId
     * @return OrderInterface
     */
    protected function getOrderById($orderId)
    {
        return $this->orderRepository->get($orderId);
    }

    /**
     * Get customer firstname by entity
     *
     * @param OrderInterface $entity
     * @return string|null
     */
    public function getCustomerFirstsNameByEntity($entity)
    {
        return !$entity->getCustomerIsGuest()
            ? $entity->getCustomerFirstname()
            : $entity->getBillingAddress()->getFirstname()
        ;
    }

    /**
     * Get customer lastname by entity
     *
     * @param OrderInterface $entity
     * @return string|null
     */
    public function getCustomerLastNameByEntity($entity)
    {
        return !$entity->getCustomerIsGuest()
            ? $entity->getCustomerLastname()
            : $entity->getBillingAddress()->getLastname();
    }

    /**
     * Get customer email by entity
     *
     * @param OrderInterface $entity
     * @return string
     */
    public function getCustomerEmailByEntity($entity)
    {
        return $entity->getCustomerEmail();
    }

    /**
     * Get store id by entity
     *
     * @param OrderInterface $entity
     * @return int
     */
    public function getStoreIdByEntity($entity)
    {
        return $entity->getStoreId();
    }

    /**
     * Get entity statistic data
     *
     * @param CampaignStepInterface $campaignStep
     * @param array $emailAttributes
     * @return array|bool
     */
    public function getEntityStatisticData(CampaignStepInterface $campaignStep, $emailAttributes)
    {
        $order = $this->getEntityByEmailAttributes($emailAttributes);
        $orderStatus = $order->getStatus();
        $allowedStatuses = $this->getAllowedOrderStatuses($campaignStep);

        if (!in_array($orderStatus, $allowedStatuses)) {
            return false;
        }

        $grandTotal = $order->getGrandTotal();
        $createdAt = $order->getCreatedAt();

        return [
            'grandTotal' => $grandTotal,
            'createdAt' => $createdAt
        ];
    }

    /**
     * Get list of allowed statuses to send reminders
     *
     * @param CampaignStepInterface $campaignStep
     * @return array
     */
    abstract protected function getAllowedOrderStatuses(CampaignStepInterface $campaignStep);
}
