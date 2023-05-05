<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api\Setup\V105;

use Aitoc\ReviewBooster\Api\Setup\V104\ReviewDetailsTableInterface as ReviewDetailsTableInterfaceV104;

interface ReviewDetailsTableInterface extends ReviewDetailsTableInterfaceV104
{
    const COLUMN_NAME_IMAGE = 'image';
}
