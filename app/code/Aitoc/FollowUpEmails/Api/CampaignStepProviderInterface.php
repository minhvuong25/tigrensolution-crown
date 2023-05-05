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

use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;

interface CampaignStepProviderInterface
{
    /**
     * Get campaign steps by campaign
     *
     * @param CampaignInterface $campaign
     * @return CampaignStepInterface[]
     */
    public function getCampaignStepsByCampaign(CampaignInterface $campaign);

    /**
     * Get campaign steps by campaign id
     *
     * @param int $campaignId
     * @return CampaignStepInterface[]
     */
    public function getCampaignStepsByCampaignId($campaignId);
}
