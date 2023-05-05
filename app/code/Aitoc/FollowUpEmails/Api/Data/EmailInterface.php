<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Api\Data;

interface EmailInterface
{
    /**
     * Returns entity_id field
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set entity id
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Returns campaign_step_id field
     *
     * @return int
     */
    public function getCampaignStepId();

    /**
     * Set campaign step id
     *
     * @param int $campaignStepId
     * @return $this
     */
    public function setCampaignStepId($campaignStepId);
    
    /**
     * Returns created_at field
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Returns scheduled_at field
     *
     * @return string
     */
    public function getScheduledAt();

    /**
     * Set scheduled at
     *
     * @param string $scheduledAt
     * @return $this
     */
    public function setScheduledAt($scheduledAt);

    /**
     * Returns created_at field
     *
     * @return string
     */
    public function getSentAt();

    /**
     * Set sent at
     *
     * @param string $sentAt
     * @return $this
     */
    public function setSentAt($sentAt);

    /**
     * Returns customer_email field
     *
     * @return string
     */
    public function getEmailAddress();

    /**
     * Set customer email
     *
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Returns unsubscribe_code field
     *
     * @return string
     */
    public function getSecretCode();

    /**
     * Set secret code
     *
     * @param string $unsubscribeCode
     * @return $this
     */
    public function setSecretCode($unsubscribeCode);

    /**
     * Returns opened_at field
     *
     * @return string
     */
    public function getOpenedAt();

    /**
     * Set opened at
     *
     * @param string $openedAt
     * @return $this
     */
    public function setOpenedAt($openedAt);

    /**
     * Returns transited_at field
     *
     * @return string
     */
    public function getTransitedAt();

    /**
     * Set transited at
     *
     * @param string $transitedAt
     * @return $this
     */
    public function setTransitedAt($transitedAt);

    /**
     * Get email attributes
     *
     * @return array
     */
    public function getEmailAttributes();

    /**
     * Set email attributes
     *
     * @param array $attributes
     * @return $this
     */
    public function setEmailAttributes($attributes);
}
