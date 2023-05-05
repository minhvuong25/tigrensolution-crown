<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Model\ResourceModel\Email\Collection;

use Aitoc\FollowUpEmails\Model\ResourceModel\Email\Collection\Processor\Filter as EmailFilterProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor;

/**
 * Class Processor
 */
class Processor extends CollectionProcessor
{
    /**
     * Processor constructor.
     * @param EmailFilterProcessor $emailFilterProcessor
     */
    public function __construct(EmailFilterProcessor $emailFilterProcessor)
    {
        $processors = ['filters' => $emailFilterProcessor];

        parent::__construct($processors);
    }
}
