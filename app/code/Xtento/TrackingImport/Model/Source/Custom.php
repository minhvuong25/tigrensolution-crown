<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2019-08-27T13:21:27+00:00
 * File:          app/code/Xtento/TrackingImport/Model/Source/Custom.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\Source;

use Magento\Framework\DataObject;

class Custom extends AbstractClass
{
    public function testConnection()
    {
        $this->initConnection();
        return $this->getTestResult();
    }

    public function initConnection()
    {
        $this->setSource($this->sourceFactory->create()->load($this->getSource()->getId()));
        $testResult = new DataObject();
        $this->setTestResult($testResult);
        $customClass = false;
        try {
            $customClass = $this->objectManager->create($this->getSource()->getCustomClass());
        } catch (\Exception $e) {}
        if (!$customClass) {
            $this->getTestResult()->setSuccess(false)->setMessage(__('Custom class NOT found.'));
            $this->getSource()->setLastResult($this->getTestResult()->getSuccess())->setLastResultMessage(
                $this->getTestResult()->getMessage()
            )->save();
            return false;
        } else {
            $this->getTestResult()->setSuccess(true)->setMessage(__('Custom class found and ready to use.'));
            $this->getSource()->setLastResult($this->getTestResult()->getSuccess())->setLastResultMessage(
                $this->getTestResult()->getMessage()
            )->save();
            return true;
        }
    }

    public function loadFiles()
    {
        // Init connection
        if (!$this->initConnection()) {
            return false;
        }
        // Call custom class
        $filesToProcess = $this->objectManager->create($this->getSource()->getCustomClass())->loadFiles();
        return $filesToProcess;
    }

    public function archiveFiles($filesToProcess, $forceDelete = false)
    {
        // Init connection
        if (!$this->initConnection()) {
            return false;
        }
        $this->objectManager->create($this->getSource()->getCustomClass())->archiveFiles($filesToProcess, $forceDelete);
    }
}