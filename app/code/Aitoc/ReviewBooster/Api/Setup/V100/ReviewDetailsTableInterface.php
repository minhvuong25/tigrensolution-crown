<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Setup\V100;

/**
 * Interface ReviewDetailsTableInterface
 */
interface ReviewDetailsTableInterface
{
    const TABLE_NAME = 'aitoc_review_booster_review_detail_extended';

    const COLUMN_NAME_ID = 'extended_id';

    const COLUMN_NAME_REVIEW_ID = 'review_id';
    const COLUMN_NAME_PRODUCT_ADVANTAGES = 'product_advantages';
    const COLUMN_NAME_PRODUCT_DISADVANTAGES = 'product_disadvantages';
    const COLUMN_NAME_REVIEW_HELPFUL = 'review_helpful';
    const COLUMN_NAME_REVIEW_UNHELPFUL = 'review_unhelpful';
    const COLUMN_NAME_REVIEW_REPORTED = 'review_reported';
    const COLUMN_NAME_CUSTOMER_VERIFIED = 'customer_verified';
}
