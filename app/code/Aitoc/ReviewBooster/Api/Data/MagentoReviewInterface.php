<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Data;

interface MagentoReviewInterface
{
    const TABLE_NAME = 'review';

    const COLUMN_NAME_ID = 'review_id';
    const COLUMN_NAME_CUSTOMER_ID = 'customer_id';
    const COLUMN_NAME_STORE_ID = 'store_id';
    const COLUMN_NAME_ENTITY_PK_VALUE = 'entity_pk_value';
}
