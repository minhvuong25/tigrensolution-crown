<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper;

use Magento\Customer\Model\Customer;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomersToCustomerInterfacesConverter
{
    /**
     * Convert
     *
     * @param Customer[] $customers
     * @return CustomerInterface[]
     */
    public function convert($customers)
    {
        $dataCustomers = [];

        foreach ($customers as $customer) {
            $customerId = $customer->getId();
            $dataCustomers[$customerId] = $customer->getDataModel();
        }

        return $dataCustomers;
    }
}
