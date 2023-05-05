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

interface RegisterDataProviderInterface
{
    const CURRENT_EVENT_CODE = 'aitoc_fue_current_event_code';
    const CURRENT_CAMPAIGN_ID = 'aitoc_fue_current_campaign_id';

    /**
     * Get current event code
     *
     * @return string|null
     */
    public function getCurrentEventCode();

    /**
     * Set current event code
     *
     * @param string $eventCode
     * @return self
     */
    public function setCurrentEventCode($eventCode);

    /**
     * Get current campaign id
     *
     * @return null|int
     */
    public function getCurrentCampaignId();

    /**
     * Set current campaign id
     *
     * @param int $campaignId
     * @return self
     */
    public function setCurrentCampaignId($campaignId);
}
