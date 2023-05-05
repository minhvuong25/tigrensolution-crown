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

use Aitoc\FollowUpEmails\Api\RegisterDataProviderInterface;
use Magento\Framework\Registry;

class RegisterDataProvider implements RegisterDataProviderInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * RegisterDataProvider constructor.
     *
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Set current event code
     *
     * @param string $eventCode
     * @return self
     */
    public function setCurrentEventCode($eventCode)
    {
        return $this->setToRegistry(self::CURRENT_EVENT_CODE, $eventCode);
    }

    /**
     * Set to registry
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    protected function setToRegistry($key, $value)
    {
        $this->registry->register($key, $value);

        return $this;
    }

    /**
     * Get current event code
     *
     * @return string
     */
    public function getCurrentEventCode()
    {
        return $this->getFromRegistry(self::CURRENT_EVENT_CODE);
    }

    /**
     * Get from registry
     *
     * @param string $key
     * @return mixed
     */
    protected function getFromRegistry($key)
    {
        return $this->registry->registry($key);
    }

    /**
     * Get current campaign id
     *
     * @return int|mixed|null
     */
    public function getCurrentCampaignId()
    {
        return $this->getFromRegistry(self::CURRENT_CAMPAIGN_ID);
    }

    /**
     * Set current campaign id
     *
     * @param int $campaignId
     * @return $this|RegisterDataProviderInterface
     */
    public function setCurrentCampaignId($campaignId)
    {
        return $this->setToRegistry(self::CURRENT_CAMPAIGN_ID, $campaignId);
    }
}
