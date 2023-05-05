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

interface UnsubscribedEmailAddressSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get items
     *
     * @return UnsubscribedEmailAddressInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param UnsubscribedEmailAddressInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
