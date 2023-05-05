<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Api;

use Magento\Framework\Exception\LocalizedException;

interface CoreConfigDataManagerInterface
{
    /**
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     * @return bool
     * @throws LocalizedException
     */
    public function isExists($path, $scopeType, $scopeId);

    /**
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     * @return string
     * @throws LocalizedException
     */
    public function get($path, $scopeType, $scopeId);

    /**
     * @param string $path
     * @param string $configValue
     * @param string $scopeType
     * @param int $scopeId
     * @return mixed
     */
    public function set($path, $configValue, $scopeType, $scopeId);

    /**
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     */
    public function delete($path, $scopeType, $scopeId);
}
