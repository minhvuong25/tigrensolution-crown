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

use Aitoc\FollowUpEmails\Api\Data\StatisticInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aitoc\FollowUpEmails\Api\Data\StatisticSearchResultsInterface;

interface StatisticRepositoryInterface
{
    /**
     * Save
     *
     * @param StatisticInterface $statisticsModel
     * @return StatisticInterface
     */
    public function save(StatisticInterface $statisticsModel);

    /**
     * Get
     *
     * @param int $entityId
     * @return StatisticInterface
     */
    public function get($entityId);

    /**
     * Delete
     *
     * @param StatisticInterface $statisticsModel
     * @return bool
     */
    public function delete(StatisticInterface $statisticsModel);

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
     * @return StatisticSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
