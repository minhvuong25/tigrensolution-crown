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

interface UnsubscribedEmailAddressInterface
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
     * Returns customer_email field
     *
     * @return string
     */
    public function getCustomerEmail();

    /**
     * Set customer email
     *
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Returns event_code field
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
     * Returns email_id field
     *
     * @return int
     */
    public function getEmailId();

    /**
     * Set email id
     *
     * @param int $emailId
     * @return $this
     */
    public function setEmailId($emailId);
}
