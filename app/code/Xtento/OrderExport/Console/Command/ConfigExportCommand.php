<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2019-11-21T14:40:27+00:00
 * File:          app/code/Xtento/OrderExport/Console/Command/ConfigExportCommand.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Console\Command;

use Magento\Framework\App\State as AppState;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigExportCommand extends Command
{
    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var \Xtento\OrderExport\Helper\Tools
     */
    protected $toolsHelper;

    /**
     * ConfigExportCommand constructor.
     *
     * @param AppState $appState
     * @param \Xtento\OrderExport\Helper\Tools $toolsHelper
     */
    public function __construct(
        AppState $appState,
        \Xtento\OrderExport\Helper\Tools $toolsHelper
    ) {
        $this->appState = $appState;
        $this->toolsHelper = $toolsHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('xtento:orderexport:config:export')
            ->setDescription('Export "XTENTO order export module" configuration as JSON file (functionality in admin: Sales Export > Tools)')
            ->setDefinition(
                [
                    new InputArgument(
                        'file',
                        InputArgument::REQUIRED,
                        'File to save settings in. Example: /tmp/settings.json'
                    ),
                    new InputOption(
                        'profiles',
                        '-p',
                        InputOption::VALUE_OPTIONAL,
                        'Profile IDs to export (multiple IDs: comma-separated, no spaces)'
                    ),
                    new InputOption(
                        'destinations',
                        '-d',
                        InputOption::VALUE_OPTIONAL,
                        'Destination IDs to export (multiple IDs: comma-separated, no spaces)'
                    )
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('adminhtml');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        }
        echo(sprintf("[Debug] App Area: %s\n", $this->appState->getAreaCode())); // Required to avoid "area code not set" error

        $outputFile = $input->getArgument('file');
        $profileIds = explode(",", $input->getOption('profiles'));
        $profileIds = array_filter($profileIds, function($value) { return $value !== ''; });
        $destinationIds = explode(",", $input->getOption('destinations'));
        $destinationIds = array_filter($destinationIds, function($value) { return $value !== ''; });

        if (empty($profileIds) && empty($destinationIds)) {
            $output->writeln("<error>Profile and destination IDs missing. One of the two must be specified so something can be exported.</error>");
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        if (!empty($profileIds)) {
            $output->writeln(sprintf("<info>Profile IDs: %s</info>", implode(", ", $profileIds)));
        }
        if (!empty($destinationIds)) {
            $output->writeln(sprintf("<info>Destination IDs: %s</info>", implode(", ", $destinationIds)));
        }

        $jsonConfiguration = $this->toolsHelper->exportSettingsAsJson($profileIds, $destinationIds);
        if (!file_put_contents($outputFile, $jsonConfiguration)) {
            $output->writeln(sprintf("<error>Could not write JSON configuration into file %s. File permissions?</error>", $outputFile));
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        $output->writeln(sprintf("<info>Finished export of configuration into file %s</info>", $outputFile));
        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
