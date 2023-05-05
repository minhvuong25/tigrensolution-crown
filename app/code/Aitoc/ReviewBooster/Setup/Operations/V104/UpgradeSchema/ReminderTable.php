<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V104\UpgradeSchema;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Aitoc\ReviewBooster\Setup\Operations\V104\UpgradeSchema\ReminderTable\AddUnsubscribeCodeColumn as AddUnsubscribeCodeColumnOperation;
use Magento\Framework\Setup\SchemaSetupInterface;

class ReminderTable implements UpgradeSchemaOperationInterface
{
    /**
     * @var AddUnsubscribeCodeColumnOperation
     */
    private $addUnsubscribeCodeColumnOperation;

    /**
     * ReminderTable constructor.
     * @param AddUnsubscribeCodeColumnOperation $addUnsubscribeCodeColumnOperation
     */
    public function __construct(AddUnsubscribeCodeColumnOperation $addUnsubscribeCodeColumnOperation)
    {
        $this->addUnsubscribeCodeColumnOperation = $addUnsubscribeCodeColumnOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->addUnsubscribeCodeColumn($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addUnsubscribeCodeColumn(SchemaSetupInterface $setup)
    {
        $this->addUnsubscribeCodeColumnOperation->execute($setup);
    }
}
