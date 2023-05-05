<?php

/**
 * Product:       Xtento_OrderExport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2015-08-17T13:41:32+00:00
 * File:          app/code/Xtento/OrderExport/Model/Output/OutputInterface.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Output;

interface OutputInterface
{
    public function convertData($exportArray);
}