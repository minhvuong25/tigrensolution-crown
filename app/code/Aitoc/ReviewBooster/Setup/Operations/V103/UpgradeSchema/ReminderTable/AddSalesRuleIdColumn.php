<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V103\UpgradeSchema\ReminderTable;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Api\Setup\V103\ReminderTableInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddSalesRuleIdColumn implements UpgradeSchemaOperationInterface
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $reminderTableName = $setup->getTable(ReminderTableInterface::TABLE_NAME);
        $salesRuleIdColumnName = ReminderTableInterface::COLUMN_NAME_SALES_RULE_ID;
        $connection = $setup->getConnection();

        $connection->addColumn($reminderTableName, $salesRuleIdColumnName, 'int');
    }
}
