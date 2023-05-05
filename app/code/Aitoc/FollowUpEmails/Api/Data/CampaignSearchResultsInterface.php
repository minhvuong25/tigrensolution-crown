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

interface CampaignSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get items
     *
     * @return CampaignInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param CampaignInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
