<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2016-03-13T19:40:24+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Source/Edit/Tab/Type/Httpdownload.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Source\Edit\Tab\Type;

class Httpdownload extends AbstractType
{
    // HTTP Configuration
    public function getFields(\Magento\Framework\Data\Form $form)
    {
        $fieldset = $form->addFieldset(
            'config_fieldset',
            [
                'legend' => __('HTTP Download Configuration'),
                'class' => 'fieldset-wide'
            ]
        );

        $fieldset->addField(
            'http_note',
            'note',
            [
                'text' => __(
                    '<b>Instructions</b>: This source is able to download files from a HTTP server. Please supply an URL in the following format: <b></b>http://www.url.com/file.csv</b> - It can be any url with any valid path/filename that exists on the remote webserver. To provide a username/password in the URL, please use: <b>http://username:password@www.url.com/file.csv</b>'
                )
            ]
        );

        $fieldset->addField(
            'custom_function',
            'text',
            [
                'label' => __('URL'),
                'name' => 'custom_function',
                'required' => true
            ]
        );
    }
}