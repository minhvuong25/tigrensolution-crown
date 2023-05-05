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

interface CampaignStepInterface
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
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

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
     * Returns template_id field
     *
     * @return string
     */
    public function getTemplateId();

    /**
     * Set template id
     *
     * @param string $templateId
     * @return $this
     */
    public function setTemplateId($templateId);

    /**
     * Returns delay_period field
     *
     * @return int
     */
    public function getDelayPeriod();

    /**
     * Set delay period
     *
     * @param int $delayPeriod
     * @return $this
     */
    public function setDelayPeriod($delayPeriod);

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
     * Get delay unit
     *
     * @return string
     */
    public function getDelayUnit();

    /**
     * Set delay unit
     *
     * @param string $unit
     * @return $this
     */
    public function setDelayUnit($unit);

    /**
     * Get discount status
     *
     * @return int
     */
    public function getDiscountStatus();

    /**
     * Set discount status
     *
     * @param int $discountStatus
     * @return $this
     */
    public function setDiscountStatus($discountStatus);

    /**
     * Get discount percent
     *
     * @return int
     */
    public function getDiscountPercent();

    /**
     * Set discount percent
     *
     * @param int $discountPercent
     * @return $this
     */
    public function setDiscountPercent($discountPercent);

    /**
     * Get discount period
     *
     * @return int
     */
    public function getDiscountPeriod();

    /**
     * Set discount period
     *
     * @param int $discountPeriod
     * @return $this
     */
    public function setDiscountPeriod($discountPeriod);

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions($options);
}
