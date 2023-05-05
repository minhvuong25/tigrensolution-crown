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

interface CampaignStepSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get items
     *
     * @return CampaignStepInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param CampaignStepInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
