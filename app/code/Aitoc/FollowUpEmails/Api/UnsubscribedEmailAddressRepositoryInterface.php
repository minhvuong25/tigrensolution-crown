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

use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface UnsubscribedEmailAddressRepositoryInterface
{
    /**
     * Create
     *
     * @return UnsubscribedEmailAddressInterface
     */
    public function create();

    /**
     * Save
     *
     * @param UnsubscribedEmailAddressInterface $unsubscribedListModel
     * @return UnsubscribedEmailAddressInterface
     */
    public function save(UnsubscribedEmailAddressInterface $unsubscribedListModel);

    /**
     * Get
     *
     * @param int $entityId
     * @return UnsubscribedEmailAddressInterface
     */
    public function get($entityId);

    /**
     * Get by email address
     *
     * @param string $emailAddress
     * @return UnsubscribedEmailAddressInterface[]
     */
    public function getByEmailAddress($emailAddress);

    /**
     * Delete
     *
     * @param UnsubscribedEmailAddressInterface $unsubscribedListModel
     * @return bool
     */
    public function delete(UnsubscribedEmailAddressInterface $unsubscribedListModel);

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
     * @return UnsubscribedEmailAddressSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
