<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Api\Data\Source\Email;

interface StatusInterface
{
    const STATUS_PENDING = 1;
    const STATUS_SENT = 2;
    const STATUS_HOLD = 3;
    const STATUS_ERROR = 4;
}
