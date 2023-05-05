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

use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface EmailRepositoryInterface
{
    /**
     * Save
     *
     * @param EmailInterface $emailModel
     * @return EmailInterface
     */
    public function save(EmailInterface $emailModel);

    /**
     * Get
     *
     * @param int $entityId
     * @return EmailInterface
     */
    public function get($entityId);

    /**
     * Get by unsubscribe code
     *
     * @param string $unsubscribeCode
     * @return EmailInterface|null
     */
    public function getByUnsubscribeCode($unsubscribeCode);

    /**
     * Delete
     *
     * @param EmailInterface $emailsModel
     * @return bool
     */
    public function delete(EmailInterface $emailsModel);

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
     * @return EmailSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
