<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Helper;

interface ProductsToHtmlConverterInterface
{
    /**
     * Get products html
     *
     * @param array $products
     * @param int $emailId
     * @return string
     */
    public function getProductsHtml($products, $emailId);
}
