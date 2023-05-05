<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2019-08-30T13:00:38+00:00
 * File:          app/code/Xtento/TrackingImport/Model/ResourceModel/Profile/Collection.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Model\ResourceModel\Profile;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Xtento\TrackingImport\Model\Profile', 'Xtento\TrackingImport\Model\ResourceModel\Profile');
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $item) {
            $configuration = $item->getData('configuration');
            if (!is_array($configuration)) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $utilsHelper = $objectManager->create('\Xtento\XtCore\Helper\Utils');
                if (version_compare($utilsHelper->getMagentoVersion(), '2.2', '>=')) {
                    $item->setData('configuration', json_decode($configuration));
                } else {
                    if (version_compare(phpversion(), '7.0.0', '>=')) {
                        $item->setData('configuration', unserialize($configuration, ['allowed_classes' => false]));
                    } else {
                        $item->setData('configuration', unserialize($configuration));
                    }
                }
                $item->setDataChanges(false);
            }
        }
        return $this;
    }
}