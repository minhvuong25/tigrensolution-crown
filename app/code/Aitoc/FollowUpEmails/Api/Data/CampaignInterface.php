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

interface CampaignInterface
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
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

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

    /**
     * Get sender
     *
     * @return string
     */
    public function getSender();

    /**
     * Set sender
     *
     * @param string $sender
     * @return $this
     */
    public function setSender($sender);

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
     * Returns string
     *
     * @return array
     */
    public function getCustomerGroupIds();

    /**
     * Set customer groupd ids
     *
     * @param int[] $customerGroupIds
     * @return $this
     */
    public function setCustomerGroupIds($customerGroupIds);

    /**
     * Returns string
     *
     * @return array
     */
    public function getStoreIds();

    /**
     * Set store ids
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds);

    /**
     * Add data
     *
     * @param array $arr
     * @return $this
     */
    public function addData(array $arr);

    /**
     * Get data
     *
     * @return array
     */
    public function getData();
}
