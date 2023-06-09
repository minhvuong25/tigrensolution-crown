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

use Aitoc\FollowUpEmails\Api\Data\EmailTemplateSearchResultsInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Mail\TemplateInterface;

interface EmailTemplateRepositoryInterface
{
    /**
     * Get
     *
     * @param int $emailTemplateId
     * @return TemplateInterface
     */
    public function get($emailTemplateId);

    /**
     * Save
     *
     * @param TemplateInterface $emailTemplate
     * @return TemplateInterface
     */
    public function save(TemplateInterface $emailTemplate);

    /**
     * Get list
     *
     * @param SearchCriteria $searchCriteria
     * @return EmailTemplateSearchResultsInterface
     */
    public function getList(SearchCriteria $searchCriteria);
}
