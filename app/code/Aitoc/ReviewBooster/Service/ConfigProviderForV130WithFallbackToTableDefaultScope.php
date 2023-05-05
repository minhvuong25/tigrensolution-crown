<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Service;

/**
 * Class ConfigProviderForV130WithFallbackToDefault
 *
 * If value for websiteId is null, then value for default scope in 'core_config_data' returned.
 */
class ConfigProviderForV130WithFallbackToTableDefaultScope extends ConfigProviderForV130
{
    /**
     * @param string $path
     * @param int|null $websiteId
     * @return mixed
     */
    protected function getScopeConfigValue($path, $websiteId = null)
    {
        $value = parent::getScopeConfigValue($path, $websiteId);
        /** @phpstan-ignore-next-line */
        if (($websiteId === null) || ($value !== null)) {
            return $value;
        }
        /** @phpstan-ignore-next-line */
        return parent::getScopeConfigValue($path);
    }
}
