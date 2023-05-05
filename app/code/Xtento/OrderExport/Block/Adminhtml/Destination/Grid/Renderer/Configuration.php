<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2016-01-08T13:39:10+00:00
 * File:          app/code/Xtento/OrderExport/Block/Adminhtml/Destination/Grid/Renderer/Configuration.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Block\Adminhtml\Destination\Grid\Renderer;

class Configuration extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render destination configuration
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $configuration = [];
        if ($row->getType() == \Xtento\OrderExport\Model\Destination::TYPE_LOCAL) {
            $configuration['directory'] = $row->getPath();
        }
        if ($row->getType() == \Xtento\OrderExport\Model\Destination::TYPE_FTP || $row->getType(
            ) == \Xtento\OrderExport\Model\Destination::TYPE_SFTP
        ) {
            $configuration['server'] = $row->getHostname() . ':' . $row->getPort();
            $configuration['username'] = $row->getUsername();
            $configuration['path'] = $row->getPath();
        }
        if ($row->getType() == \Xtento\OrderExport\Model\Destination::TYPE_EMAIL) {
            $configuration['from'] = $row->getEmailSender();
            $configuration['to'] = $row->getEmailRecipient();
            $configuration['subject'] = $row->getEmailSubject();
        }
        if ($row->getType() == \Xtento\OrderExport\Model\Destination::TYPE_CUSTOM) {
            $configuration['class'] = $row->getCustomClass();
        }
        if ($row->getType() == \Xtento\OrderExport\Model\Destination::TYPE_WEBSERVICE) {
            $configuration['class'] = 'Webservice';
            $configuration['function'] = $row->getCustomFunction();
        }
        if (!empty($configuration)) {
            $configurationHtml = '';
            foreach ($configuration as $key => $value) {
                $configurationHtml .= __(ucfirst($key)) . ': <i>' . $this->escapeHtml($value) . '</i><br/>';
            }
            return $configurationHtml;
        } else {
            return '---';
        }
    }
}
