<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper;

use Aitoc\FollowUpEmails\Api\Data\Source\EmailTemplate\TableColumnNameInterface;
use Aitoc\FollowUpEmails\Api\EmailTemplateRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Helper\BackendTemplateHelperInterface;
use Magento\Email\Model\Template;
use Magento\Email\Model\TemplateFactory;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class BackendTemplateHelper implements BackendTemplateHelperInterface
{
    /**
     * @var TemplateFactory
     */
    private $emailTemplateModelFactory;

    /**
     * @var EmailTemplateRepositoryInterface
     */
    private $emailTemplateRepository;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * BackendTemplateHelper constructor.
     *
     * @param TemplateFactory $emailTemplateFactory
     * @param EmailTemplateRepositoryInterface $emailTemplateRepository
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        TemplateFactory $emailTemplateFactory,
        EmailTemplateRepositoryInterface $emailTemplateRepository,
        JsonSerializer $jsonSerializer
    ) {
        $this->emailTemplateModelFactory = $emailTemplateFactory;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Create and save by code and name
     *
     * @param string $templateCode
     * @param string $templateName
     * @param string $styles
     * @return int|null
     * @throws MailException
     */
    public function createAndSaveByCodeAndName($templateCode, $templateName, $styles = null)
    {
        $templateModel = $this->createBackendTemplateByConfigTemplateCode($templateCode, $templateName, $styles);
        $this->saveBackendTemplate($templateModel);

        return $templateModel->getId();
    }

    /**
     * Create backend template by config tempate code
     *
     * @param string $templateCode
     * @param string $templateName
     * @param string $styles
     * @return Template
     * @throws MailException
     */
    private function createBackendTemplateByConfigTemplateCode($templateCode, $templateName, $styles = '')
    {
        $templateModel = $this->createTemplateModel();
        $templateModel->setForcedArea($templateCode);
        $templateModel->loadDefault($templateCode);
        $templateModel->setData(TableColumnNameInterface::ORIG_TEMPLATE_CODE, $templateCode);

        $variablesOptionArray = $templateModel->getVariablesOptionArray(true);
        $serializedVariablesOptionArray = $this->serialize($variablesOptionArray);

        $templateModel->setData(TableColumnNameInterface::TEMPLATE_VARIABLES, $serializedVariablesOptionArray);
        $templateModel->setTemplateCode($templateName);
//        $templateBlock = $this->_view->getLayout()->createBlock(\Magento\Email\Block\Adminhtml\Template\Edit::class);
//        $template->setData('orig_template_currently_used_for', $templateBlock->getCurrentlyUsedForPaths(false));
        $templateModel->setId(null);
        $templateModel->setTemplateStyles($styles);

        return $templateModel;
    }

    /**
     * Create template model
     *
     * @return Template
     */
    private function createTemplateModel()
    {
        return $this->emailTemplateModelFactory->create();
    }

    /**
     * Serialize
     *
     * @param array $value
     * @return bool|false|string
     */
    private function serialize($value)
    {
        return $this->jsonSerializer->serialize($value);
    }

    /**
     * Save backend template
     *
     * @param Template $template
     */
    private function saveBackendTemplate(Template $template)
    {
        $this->emailTemplateRepository->save($template);
    }
}
