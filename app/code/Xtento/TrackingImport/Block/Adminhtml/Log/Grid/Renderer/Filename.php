<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2016-03-11T17:40:19+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Log/Grid/Renderer/Filename.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Log\Grid\Renderer;

class Filename extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
        $rowFiles = $row->getFiles();
        if (empty($rowFiles)) {
            return __('No files saved.');
        }
        $filenames = explode("|", $rowFiles);
        $baseFilenames = [];
        foreach ($filenames as $filename) {
            array_push($baseFilenames, basename($filename));
        }
        $baseFilenames = array_unique($baseFilenames);
        $rowText = "";
        foreach ($baseFilenames as $filename) {
            $rowText .= $filename . "<br>";
        }
        return $rowText;
    }
}
