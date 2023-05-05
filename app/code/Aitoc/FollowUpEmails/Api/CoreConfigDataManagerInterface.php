<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api;

use Magento\Framework\Exception\LocalizedException;

interface CoreConfigDataManagerInterface
{
    /**
     * Is exists
     *
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     * @return bool
     * @throws LocalizedException
     */
    public function isExists($path, $scopeType, $scopeId);

    /**
     * Get
     *
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     * @return string
     * @throws LocalizedException
     */
    public function get($path, $scopeType, $scopeId);

    /**
     * Set
     *
     * @param string $path
     * @param mixed $configValue
     * @param string $scopeType
     * @param int $scopeId
     * @return
     */
    public function set($path, $configValue, $scopeType, $scopeId);

    /**
     * Delete
     *
     * @param string $path
     * @param string $scopeType
     * @param int $scopeId
     */
    public function delete($path, $scopeType, $scopeId);
}
