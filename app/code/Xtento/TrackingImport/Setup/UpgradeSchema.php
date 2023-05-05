<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2019-11-25T12:35:59+00:00
 * File:          app/code/Xtento/TrackingImport/Setup/UpgradeSchema.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '2.6.3', '<')) {
            $connection->changeColumn(
                $setup->getTable('xtento_trackingimport_source'), 'port', 'port',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 5,
                    'unsigned' => true,
                    'nullable' => true,
                    'comment' => 'Port'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.7.4', '<')) {
            // Move cronjobs into separate cron group
            $connection->query(
                "UPDATE " . $setup->getTable('core_config_data') . " 
                    SET path = REPLACE(path, 'crontab/default/jobs/" . \Xtento\TrackingImport\Cron\Import::CRON_GROUP . "', 'crontab/" . \Xtento\TrackingImport\Cron\Import::CRON_GROUP . "/jobs/" . \Xtento\TrackingImport\Cron\Import::CRON_GROUP . "')
                    WHERE path LIKE 'crontab/default/jobs/" . \Xtento\TrackingImport\Cron\Import::CRON_GROUP . "%'"
            );
        }

        if (version_compare($context->getVersion(), '2.7.8', '<')) {
            $connection->addColumn(
                $setup->getTable('xtento_trackingimport_source'),
                'ftp_ignorepasvaddress',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => false,
                    'length' => 1,
                    'comment' => 'FTP Ignore PASV Address'
                ]
            );
        }

        $setup->endSetup();
    }
}
