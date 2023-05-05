<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Service;

use Aitoc\FollowUpEmails\Api\Service\CustomerGroupInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroupCollection;

class CustomerGroup implements CustomerGroupInterface
{
    /**
     * @var CustomerGroupCollection
     */
    private $customerGroupCollection;

    /**
     * CustomerGroup constructor.
     *
     * @param CustomerGroupCollection $customerGroupCollection
     */
    public function __construct(
        CustomerGroupCollection $customerGroupCollection
    ) {
        $this->customerGroupCollection = $customerGroupCollection;
    }

    /**
     * Get customer groupd ids
     *
     * @return array
     */
    public function getCustomerGroupsIds()
    {
        $customerGroupIds = [];
        $customerGroups = $this->getCustomerGroupOptionArray();

        foreach ($customerGroups as $customerGroup) {
            $customerGroupIds[] = $customerGroup['value'];
        }

        return $customerGroupIds;
    }

    /**
     * Get customer groupd option array
     *
     * @return array
     */
    private function getCustomerGroupOptionArray()
    {
        return $this->customerGroupCollection->toOptionArray();
    }
}
