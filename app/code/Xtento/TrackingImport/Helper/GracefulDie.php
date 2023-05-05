<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2019-05-14T12:38:32+00:00
 * File:          app/code/Xtento/TrackingImport/Helper/GracefulDie.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Helper;

use Xtento\TrackingImport\Model\Log;

class GracefulDie
{
    protected static $isInitialized = false;
    protected static $isEnabled = false;

    public static function enable()
    {
        self::$isEnabled = true;
        if (!self::$isInitialized) {
            register_shutdown_function(['\Xtento\TrackingImport\Helper\GracefulDie', 'beforeDieFromShutdown']); // Fatal error or similar
            self::$isInitialized = true;
        }
    }

    public static function disable()
    {
        self::$isEnabled = false;
    }

    /**
     * @param null $message
     * @param bool $exit
     */
    public static function beforeDie($message = null, $exit = false)
    {
        if (self::$isEnabled) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $registry = $objectManager->get('\Magento\Framework\Registry');
            $logEntry = $registry->registry('trackingimport_log');
            if ($logEntry && $logEntry->getId()) {
                $logEntry->setResult(Log::RESULT_FAILED);
                $logEntry->addResultMessage($message);
                $logEntry->setResultMessage($logEntry->getResultMessages());
                $logEntry->save();
            }
            /*if ($exit) {
                exit;
            }*/
        }
    }

    public static function beforeDieFromShutdown()
    {
        $message = 'Shutdown/Crash: ' . print_r(error_get_last(), true);
        //'Stack Trace: ' . PHP_EOL . (new \Exception())->__toString();

        self::beforeDie($message, false);
    }
}