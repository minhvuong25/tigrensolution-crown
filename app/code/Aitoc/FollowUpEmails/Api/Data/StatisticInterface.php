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

interface StatisticInterface
{
    const SENT = 'sent';
    const OPENED = 'opened';
    const TRANSITED = 'transited';
    const UNSUBSCRIBED = 'unsubscribed';
    const SALES = 'sales';
    const WEEK = 'week';
    const MONTH = 'month';
    const HALF_YEAR = 'half_year';
    const YEAR = 'year';

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
     * Returns campaign_id field
     *
     * @return int
     */
    public function getCampaignId();

    /**
     * Set campaign id
     *
     * @param int $campaignId
     * @return $this
     */
    public function setCampaignId($campaignId);

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
     * Returns key field
     *
     * @return string
     */
    public function getKey();

    /**
     * Set key
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key);

    /**
     * Returns value field
     *
     * @return int
     */
    public function getValue();

    /**
     * Set value
     *
     * @param int $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Returns updated_at field
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get event code
     *
     * @return string
     */
    public function getEventCode();

    /**
     * Set event code
     *
     * @param string $eventCode
     * @return $this
     */
    public function setEventCode($eventCode);
}
