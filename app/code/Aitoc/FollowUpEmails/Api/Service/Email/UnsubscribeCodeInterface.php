<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Service\Email;

interface UnsubscribeCodeInterface
{
    /**
     * Generate unsubscribe code
     *
     * @return string
     */
    public function generateUnsubscribeCode();
}
