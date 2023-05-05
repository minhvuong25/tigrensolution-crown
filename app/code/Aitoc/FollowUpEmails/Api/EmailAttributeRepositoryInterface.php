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

use Aitoc\FollowUpEmails\Api\Data\EmailAttributeInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailAttributeSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface EmailAttributeRepositoryInterface
{
    /**
     * Save
     *
     * @param EmailAttributeInterface $emailAttributesModel
     * @return EmailAttributeInterface
     */
    public function save(EmailAttributeInterface $emailAttributesModel);

    /**
     * Get
     *
     * @param int $entityId
     * @return EmailAttributeInterface
     */
    public function get($entityId);

    /**
     * Delete
     *
     * @param EmailAttributeInterface $emailAttributesModel
     * @return bool
     */
    public function delete(EmailAttributeInterface $emailAttributesModel);

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
     * @return EmailAttributeSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Get by filters
     *
     * @param array $filters
     * @return EmailAttributeInterface[]
     */
    public function getByFilters($filters);
}
