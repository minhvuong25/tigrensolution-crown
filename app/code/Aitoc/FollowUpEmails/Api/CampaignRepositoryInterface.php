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
use Magento\Framework\Api\SearchCriteriaInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignSearchResultsInterface;

interface CampaignRepositoryInterface
{
    /**
     * Save
     *
     * @param CampaignInterface $campaignsModel
     * @return CampaignInterface
     */
    public function save(CampaignInterface $campaignsModel);

    /**
     * Get
     *
     * @param int $entityId
     * @return CampaignInterface
     */
    public function get($entityId);

    /**
     * Delete
     *
     * @param CampaignInterface $campaignsModel
     * @return bool
     */
    public function delete(CampaignInterface $campaignsModel);

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
     * @return CampaignSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
