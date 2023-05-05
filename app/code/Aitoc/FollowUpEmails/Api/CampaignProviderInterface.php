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

use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;

interface CampaignProviderInterface
{
    /**
     * Get campaigns by event code
     *
     * @param string $eventCode
     * @return CampaignInterface[]
     */
    public function getCampaignsByEventCode($eventCode);
}
