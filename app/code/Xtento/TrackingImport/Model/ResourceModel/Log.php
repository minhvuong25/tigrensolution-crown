<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2016-03-05T13:40:03+00:00
 * File:          app/code/Xtento/TrackingImport/Model/ResourceModel/Log.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\ResourceModel;

class Log extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('xtento_trackingimport_log', 'log_id');
    }
}
