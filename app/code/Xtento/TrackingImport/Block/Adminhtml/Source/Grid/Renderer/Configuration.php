<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2016-05-07T11:31:02+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Source/Grid/Renderer/Configuration.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Source\Grid\Renderer;

use Xtento\TrackingImport\Model\Source;

class Configuration extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render source configuration
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $configuration = [];
        if ($row->getType() == Source::TYPE_LOCAL) {
            $configuration['directory'] = $row->getPath();
        }
        if ($row->getType() == Source::TYPE_FTP || $row->getType() == Source::TYPE_SFTP) {
            $configuration['server'] = $row->getHostname() . ':' . $row->getPort();
            $configuration['username'] = $row->getUsername();
            $configuration['path'] = $row->getPath();
        }
        if ($row->getType() == Source::TYPE_CUSTOM) {
            $configuration['class'] = $row->getCustomClass();
        }
        if ($row->getType() == Source::TYPE_HTTPDOWNLOAD) {
            $configuration['link'] = $row->getCustomFunction();
        }
        if ($row->getType() == Source::TYPE_WEBSERVICE) {
            $configuration['class'] = __('Webservice');
            $configuration['function'] = $row->getCustomFunction();
        }
        if ($row->getType() == Source::TYPE_HTTP) {
            $configuration['class'] = __('HTTP Server (Custom)');
            $configuration['function'] = $row->getCustomFunction();
        }
        if ($row->getType() == Source::TYPE_LOCAL || $row->getType() == Source::TYPE_FTP || $row->getType() == Source::TYPE_SFTP) {
            if ($row->getFilenamePattern() !== '') {
                $configuration['filename pattern'] = $row->getFilenamePattern();
            }
            if ($row->getArchivePath() !== '') {
                $configuration['archive path'] = $row->getArchivePath();
            }
            if ($row->getDeleteImportedFiles() !== '') {
                if ($row->getDeleteImportedFiles()) {
                    $configuration['delete files'] = __('Yes');
                } else {
                    $configuration['delete files'] = __('No');
                }
            }
        }
        if (!empty($configuration)) {
            $configurationHtml = '';
            foreach ($configuration as $key => $value) {
                $configurationHtml .= __(ucwords($key)) . ': <i>' . $this->escapeHtml($value) . '</i><br/>';
            }
            return $configurationHtml;
        } else {
            return '---';
        }
    }
}
