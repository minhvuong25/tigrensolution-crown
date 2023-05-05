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
use Magento\Framework\Api\SearchCriteriaInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepSearchResultsInterface;

interface CampaignStepRepositoryInterface
{
    /**
     * Save
     *
     * @param CampaignStepInterface $campaignStepsModel
     * @return CampaignStepInterface
     */
    public function save(CampaignStepInterface $campaignStepsModel);

    /**
     * Get
     *
     * @param int $entityId
     * @return CampaignStepInterface
     */
    public function get($entityId);

    /**
     * Delete
     *
     * @param CampaignStepInterface $campaignStepsModel
     * @return bool
     */
    public function delete(CampaignStepInterface $campaignStepsModel);

    /**
     * Delete by id
     *
     * @param int $entityId
     * @return bool
     */
    public function deleteById($entityId);

    /**
     * Get list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CampaignStepSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
