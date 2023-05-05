<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface StatisticSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get items
     *
     * @return StatisticInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param StatisticInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
