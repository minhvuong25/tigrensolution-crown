<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V121\UpgradeData;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeDataOperationInterface;
use Aitoc\ReviewBooster\Api\Setup\V111\ReviewDetailsTableInterface;
use Aitoc\ReviewBooster\Api\Setup\V121\ReviewImageTableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class TransferImageData implements UpgradeDataOperationInterface
{
    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $imageTableName = $setup->getTable(ReviewImageTableInterface::TABLE_NAME);
        $reviewDetailsTableName = $setup->getTable(ReviewDetailsTableInterface::TABLE_NAME);

        $connection = $setup->getConnection();

        $select = $connection->select();
        $select->from($reviewDetailsTableName, ['review_id', 'image']);
        $select->where('image IS NOT NULL');

        $sql = $connection->insertFromSelect(
            $select,
            $imageTableName,
            ['review_id', 'image'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
        );

        $connection->query($sql);

        $connection->dropColumn(
            $reviewDetailsTableName,
            ReviewDetailsTableInterface::COLUMN_NAME_IMAGE
        );
    }
}
