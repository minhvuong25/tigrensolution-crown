<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;

class ReviewDetails extends AbstractDb
{
    /**
     * Review detail extended table
     */
    const TABLE = 'aitoc_review_booster_review_detail_extended';

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(self::TABLE, ReviewDetailsInterface::EXTENDED_ID);
    }
}
