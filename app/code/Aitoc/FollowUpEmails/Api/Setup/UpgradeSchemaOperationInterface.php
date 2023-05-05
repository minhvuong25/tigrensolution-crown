<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;

interface UpgradeSchemaOperationInterface
{
    /**
     * Execute
     *
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup);
}
