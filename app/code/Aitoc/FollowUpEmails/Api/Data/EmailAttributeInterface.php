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

interface EmailAttributeInterface
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

    /**
     * Returns attribute_code field
     *
     * @return string
     */
    public function getAttributeCode();

    /**
     * Set attribute code
     *
     * @param string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode);

    /**
     * Returns value field
     *
     * @return string
     */
    public function getValue();

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Add data
     *
     * @param array $arr
     * @return $this
     */
    public function addData(array $arr);
}
