<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Setup\V100;

interface EmailAttributeTableInterface
{
    const TABLE_NAME = 'aitoc_follow_up_emails_email_attributes';

    const COLUMN_NAME_ENTITY_ID = 'entity_id';
    const COLUMN_NAME_EMAIL_ID = 'email_id';
    const COLUMN_NAME_ATTRIBUTE_CODE = 'attribute_code';
    const COLUMN_NAME_VALUE = 'value';
}
