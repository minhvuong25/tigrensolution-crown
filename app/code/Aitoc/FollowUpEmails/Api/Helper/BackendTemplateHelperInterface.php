<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Helper;

interface BackendTemplateHelperInterface
{
    /**
     * Create and save by code and name
     *
     * @param string $templateCode
     * @param string $templateName
     * @param string $styles
     * @return int Backend Email Template Id
     */
    public function createAndSaveByCodeAndName($templateCode, $templateName, $styles = null);
}
