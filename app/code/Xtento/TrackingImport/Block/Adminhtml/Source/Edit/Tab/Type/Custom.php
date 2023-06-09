<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2016-03-13T19:24:15+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Source/Edit/Tab/Type/Custom.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Source\Edit\Tab\Type;

class Custom extends AbstractType
{
    // Custom Type Configuration
    public function getFields(\Magento\Framework\Data\Form $form)
    {
        $fieldset = $form->addFieldset(
            'config_fieldset',
            [
                'legend' => __('Custom Type Configuration'),
                'class' => 'fieldset-wide'
            ]
        );

        $fieldset->addField(
            'custom_class',
            'text',
            [
                'label' => __('Custom Class Identifier'),
                'name' => 'custom_class',
                'note' => __(
                    'You can set up an own class in our (or another) module which gets called when importing. The loadFiles() function would be called in your class. If your class was called \Xtento\TrackingImport\Model\Source\Myclass then the identifier to enter here would be \Xtento\TrackingImport\Model\Source\Myclass<br/><br/>The loadFiles() function needs to return an array like this: array(array(\'source_id\' => $this->getSource()->getId(), \'filename\' => $filename, \'data\' => $fileContents))'
                ),
                'required' => true
            ]
        );
    }
}