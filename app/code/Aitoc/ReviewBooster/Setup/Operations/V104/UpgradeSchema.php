<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V104;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Setup\Operations\V104\UpgradeSchema\ReminderTable as ReminderTableOperation;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaOperationInterface
{
    /**
     * @var ReminderTableOperation
     */
    private $reminderTableOperation;

    /**
     * UpgradeSchema constructor.
     * @param ReminderTableOperation $reminderTableOperation
     */
    public function __construct(ReminderTableOperation $reminderTableOperation)
    {
        $this->reminderTableOperation = $reminderTableOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->upgradeReminderTable($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function upgradeReminderTable(SchemaSetupInterface $setup)
    {
        $this->reminderTableOperation->execute($setup);
    }
}
