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

interface CampaignStepInterface
{
    /**
     * Get active campaign steps ids
     *
     * @return int[]
     */
    public function getActiveCampaignStepsIds();
}
