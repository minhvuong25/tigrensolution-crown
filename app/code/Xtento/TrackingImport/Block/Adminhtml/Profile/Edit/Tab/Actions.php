<?php

/**
 * Product:       Xtento_TrackingImport
 * ID:            Ug2OpEISigut7u+7UERQ8S+N2PiXfSnW/KizAvVUI6w=
 * Last Modified: 2018-07-18T17:48:11+00:00
 * File:          app/code/Xtento/TrackingImport/Block/Adminhtml/Profile/Edit/Tab/Actions.php
 * Copyright:     Copyright (c) XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\TrackingImport\Block\Adminhtml\Profile\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Xtento\TrackingImport\Block\Adminhtml\Profile\Edit\Tab\Mapping\Action;
use Xtento\TrackingImport\Block\Adminhtml\Widget\Tab;

class Actions extends Tab implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var Action
     */
    protected $mappingAction;

    /**
     * Actions constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Action $mappingAction
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Action $mappingAction,
        array $data = []
    ) {
        $this->mappingAction = $mappingAction;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function getFormMessages()
    {
        $formMessages = [];
        $formMessages[] = [
            'type' => 'notice',
            'message' => __(
                'The actions set up below will be applied to all manual and automatic imports, there is no sort order.'
            )
        ];
        return $formMessages;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('trackingimport_profile');
        if (!$model->getId()) {
            return $this;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setValues($model->getConfiguration());
        $this->setForm($form);
        $this->setTemplate('Xtento_TrackingImport::profile/action.phtml');

        return parent::_prepareForm();
    }

    public function getActionHtml()
    {
        $model = $this->_coreRegistry->registry('trackingimport_profile');
        $form = $this->getForm();
        $mapping = $form->addField('action', 'text', ['label' => '', 'name' => 'action']);
        $form->setValues($model->getConfiguration());
        $block = $this->mappingAction;
        return $block->render($mapping);
    }

    /**
     * Prepare label for tab
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Actions');
    }

    /**
     * Prepare title for tab
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Actions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
