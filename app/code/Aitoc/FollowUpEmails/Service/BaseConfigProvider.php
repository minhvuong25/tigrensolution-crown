<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

abstract class BaseConfigProvider extends AbstractHelper
{
    /**
     * Get scopd config value
     *
     * @param string $path
     * @param null $websiteId
     * @return string
     */
    protected function getScopeConfigValue($path, $websiteId = null)
    {
        $scopeType = $websiteId ? ScopeInterface::SCOPE_WEBSITES : ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        $scopeCode = $websiteId ? $websiteId : null;

        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }
}
