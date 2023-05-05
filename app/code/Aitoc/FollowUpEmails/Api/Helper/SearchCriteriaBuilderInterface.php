<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Helper;

use Magento\Framework\Api\SearchCriteria;

interface SearchCriteriaBuilderInterface
{
    /**
     * Create search criteria
     *
     * @param array $filters
     * @param array $sortOrders
     * @return SearchCriteria
     */
    public function createSearchCriteria($filters = [], $sortOrders = []);
}
