<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V104\UpgradeSchema\ReminderTable;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Api\Setup\V104\ReminderTableInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddUnsubscribeCodeColumn implements UpgradeSchemaOperationInterface
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $reminderTable = $setup->getTable(ReminderTableInterface::TABLE_NAME);
        $unsubscribeCodeColumnName = ReminderTableInterface::COLUMN_NAME_UNSUBSCRIBE_CODE;
        $connection = $setup->getConnection();

        $connection->addColumn($reminderTable, $unsubscribeCodeColumnName, 'text');
    }
}
