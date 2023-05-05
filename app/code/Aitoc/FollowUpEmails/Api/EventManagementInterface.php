<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api;

use Aitoc\FollowUpEmails\Api\Helper\EventEmailsGeneratorHelperInterface;

/**
 * Interface for managing events.
 * @api
 */
interface EventManagementInterface
{
    /**
     * Get attributes by event code
     *
     * @param string $eventCode
     * @return array
     */
    public function getAttributesByEventCode($eventCode);

    /**
     * Get all events
     *
     * @return array
     */
    public function getAllEvents();

    /**
     * Get active event codes
     *
     * @return string[]
     */
    public function getActiveEventsCodes();

    /**
     * Get active events
     *
     * @return array
     */
    public function getActiveEvents();

    /**
     * Get event email generator
     *
     * @param string $eventCode
     * @return EventEmailsGeneratorHelperInterface
     */
    public function getEventEmailGeneratorHelperByEventCode($eventCode);

    /**
     * Is event enabled
     *
     * @param string $eventCode
     * @return bool
     */
    public function isEventEnabled($eventCode);

    /**
     * Get event name by code
     *
     * @param string $eventCode
     * @return string
     */
    public function getEventNameByCode($eventCode);
}
