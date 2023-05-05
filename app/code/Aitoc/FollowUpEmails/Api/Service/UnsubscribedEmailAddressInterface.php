<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Service;

interface UnsubscribedEmailAddressInterface
{
    /**
     * Update unsubscribed events for email
     *
     * @param string $emailAddress
     * @param string[] $newUnsubscribedEventsCodes
     * @param int|null $emailId
     */
    public function updateUnsubscribedEventsForEmail($emailAddress, $newUnsubscribedEventsCodes, $emailId = null);
}
