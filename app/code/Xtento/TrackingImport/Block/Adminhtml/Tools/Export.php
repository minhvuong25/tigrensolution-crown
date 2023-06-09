<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2016-03-11T17:40:19+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Tools/Export.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Tools;

class Export extends \Magento\Backend\Block\Template
{
    /**
     * @var \Xtento\TrackingImport\Model\ResourceModel\Source\CollectionFactory
     */
    protected $sourceCollectionFactory;

    /**
     * @var \Xtento\TrackingImport\Model\ResourceModel\Profile\CollectionFactory
     */
    protected $profileCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Xtento\TrackingImport\Model\ResourceModel\Source\CollectionFactory $sourceCollectionFactory
     * @param \Xtento\TrackingImport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Xtento\TrackingImport\Model\ResourceModel\Source\CollectionFactory $sourceCollectionFactory,
        \Xtento\TrackingImport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sourceCollectionFactory = $sourceCollectionFactory;
        $this->profileCollectionFactory = $profileCollectionFactory;
    }

    public function getProfiles()
    {
        $profileCollection = $this->profileCollectionFactory->create();
        $profileCollection->getSelect()->order('name ASC');
        return $profileCollection;
    }

    public function getSources()
    {
        $sourceCollection = $this->sourceCollectionFactory->create();
        $sourceCollection->getSelect()->order('name ASC');
        return $sourceCollection;
    }
}
