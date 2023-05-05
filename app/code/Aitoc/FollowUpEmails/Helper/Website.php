<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class Website implements WebsiteInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Website constructor.
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Get website id by store id
     *
     * @param int $storeId
     * @return int
     * @throws NoSuchEntityException
     */
    public function getWebsiteIdByStoreId($storeId)
    {
        $store = $this->getStoreByStoreId($storeId);
        $storeGroupId = $store->getStoreGroupId();
        $storeGroup = $this->getStoreGroupById($storeGroupId);

        return $storeGroup->getWebsiteId();
    }

    /**
     * Get store groupd by id
     *
     * @param int $storeGroupId
     * @return GroupInterface
     */
    private function getStoreGroupById($storeGroupId)
    {
        return $this->storeManager->getGroup($storeGroupId);
    }

    /**
     * Get store by store id
     *
     * @param int $storeId
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStoreByStoreId($storeId)
    {
        return $this->storeManager->getStore($storeId);
    }
}
