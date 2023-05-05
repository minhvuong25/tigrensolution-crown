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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTimeHelper;

abstract class Cart extends BaseEventEmailsGeneratorHelper
{
    const EMAIL_ATTRIBUTE_CODE_QUOTE_ID = 'quote_id';

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Cart constructor.
     *
     * @param EmailHelperInterface $emailHelper
     * @param CartRepositoryInterface $orderRepository
     * @param DateTimeHelper $dateTimeHelperTimeHelper
     */
    public function __construct(
        EmailHelperInterface $emailHelper,
        CartRepositoryInterface $orderRepository,
        DateTimeHelper $dateTimeHelperTimeHelper
    ) {
        parent::__construct($emailHelper, $dateTimeHelperTimeHelper);
        $this->quoteRepository = $orderRepository;
    }

    /**
     * Get entity id by entity
     *
     * @param CartInterface $entity
     * @return int
     */
    public function getEntityIdByEntity($entity)
    {
        return $entity->getId();
    }

    /**
     * Get entity id attribute code
     *
     * @return string
     */
    public function getEntityIdAttributeCode()
    {
        return self::EMAIL_ATTRIBUTE_CODE_QUOTE_ID;
    }

    /**
     * Get entity
     *
     * @param int $entityId
     * @return CartInterface
     * @throws NoSuchEntityException
     */
    public function getEntityById($entityId)
    {
        return $this->getQuoteById($entityId);
    }

    /**
     * Get event timestamp
     *
     * @param CartInterface $entity
     * @return mixed
     */
    public function getEventTimestampByEntity($entity)
    {
        $updatedAt = $entity->getUpdatedAt();

        return $this->convertToTimestamp($updatedAt);
    }

    /**
     * Get customer firstname by entity
     *
     * @param CartInterface $entity
     * @return string|null
     */
    public function getCustomerFirstsNameByEntity($entity)
    {
        return ($customer = $entity->getCustomer())
            ? $customer->getFirstname()
            : $entity->getBillingAddress()->getFirstname();
    }

    /**
     * Get customer lastname by entity
     *
     * @param CartInterface $entity
     * @return string|null
     */
    public function getCustomerLastNameByEntity($entity)
    {
        return ($customer = $entity->getCustomer())
            ? $customer->getLastname()
            : $entity->getBillingAddress()->getLastname();
    }

    /**
     * Get customer email by entity
     *
     * @param CartInterface|Quote $entity
     * @return string
     */
    public function getCustomerEmailByEntity($entity)
    {
        return $entity->getCustomerEmail();
    }

    /**
     * Get quote by id
     *
     * @param int $quoteId
     * @return CartInterface
     * @throws NoSuchEntityException
     */
    protected function getQuoteById($quoteId)
    {
        return $this->quoteRepository->get($quoteId);
    }

    /**
     * Get store id by entity
     *
     * @param CartInterface $entity
     * @return int
     */
    public function getStoreIdByEntity($entity)
    {
        return $entity->getStoreId();
    }

    /**
     * Can send email
     *
     * @param CampaignStepInterface $campaignStep
     * @param array $emailAttributes
     * @return bool
     */
    public function canSendEmail(CampaignStepInterface $campaignStep, $emailAttributes)
    {
        $quoteModel = $this->getEntityByEmailAttributes($emailAttributes);

        return (bool) $quoteModel->getIsActive();
    }
}
