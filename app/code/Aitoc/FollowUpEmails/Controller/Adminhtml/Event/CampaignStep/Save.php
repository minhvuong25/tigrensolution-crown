<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\Adminhtml\Event\CampaignStep;

use Aitoc\FollowUpEmails\Api\Data\Source\CampaignStep\SaveSettingValuesInterface;
use Aitoc\FollowUpEmails\Api\EmailTemplateRepositoryInterface;
use Aitoc\FollowUpEmails\Helper\Campaign as CampaignHelper;
use Aitoc\FollowUpEmails\Model\CampaignRepository;
use Aitoc\FollowUpEmails\Model\CampaignStep;
use Aitoc\FollowUpEmails\Model\CampaignStepFactory;
use Aitoc\FollowUpEmails\Model\CampaignStepRepository;
use Aitoc\FollowUpEmails\Model\StatisticManagement;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Email\Model\BackendTemplate;
use Magento\Email\Model\BackendTemplateFactory;
use Magento\Email\Model\Template;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Mail\TemplateInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Save extends Action
{
    const SUCCESS_MESSAGE = 'You saved the email';

    const REQUEST_PARAM_CAMPAIGN_ID = 'campaign_id';
    const REQUEST_POST_PARAM_TEMPLATE_ID = 'template_id';
    const REQUEST_POST_PARAM_TEMPLATE_SUBJECT = 'template_subject';
    const REQUEST_POST_PARAM_TEMPLATE_CONTENT = 'template_content';
    const REQUEST_POST_PARAM_NEW_TEMPLATE_NAME = 'new_template_name';
    const REQUEST_PARAM_CAMPAIGN_STEP_ID = 'campaign_step_id';
    const REQUEST_PARAM_CAMPAIGN_EDIT_ID = 'entity_id';

    /**
     * @var Template
     */
    protected $emailTemplateModel;

    /**
     * @var CampaignStepRepository
     */
    protected $campaignStepsRepository;

    /**
     * @var CampaignStepFactory
     */
    protected $campaignStepFactory;

    /**
     * @var CampaignRepository
     */
    protected $campaignRepository;
    /**
     * @var EmailTemplateRepositoryInterface
     */
    protected $emailTemplateRepository;
    /**
     * @var DateTime
     */
    protected $dateTime;
    /**
     * @var BackendTemplateFactory
     */
    protected $backendTemplateFactory;
    /**
     * @var CampaignHelper
     */
    protected $campaignHelper;
    /**
     * @var StatisticManagement
     */
    private $statisticManagement;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param Template $emailTemplateModel
     * @param CampaignStepRepository $campaignStepsRepository
     * @param CampaignStepFactory $campaignStepFactory
     * @param CampaignRepository $campaignRepository
     * @param StatisticManagement $statisticManagement
     * @param EmailTemplateRepositoryInterface $emailTemplateRepository
     * @param DateTime $dateTime
     * @param BackendTemplateFactory $backendTemplateFactory
     * @param CampaignHelper $campaignHelper
     */
    public function __construct(
        Context $context,
        Template $emailTemplateModel,
        CampaignStepRepository $campaignStepsRepository,
        CampaignStepFactory $campaignStepFactory,
        CampaignRepository $campaignRepository,
        StatisticManagement $statisticManagement,
        EmailTemplateRepositoryInterface $emailTemplateRepository,
        DateTime $dateTime,
        BackendTemplateFactory $backendTemplateFactory,
        CampaignHelper $campaignHelper
    ) {
        $this->emailTemplateModel = $emailTemplateModel;
        $this->campaignStepsRepository = $campaignStepsRepository;
        $this->campaignStepFactory = $campaignStepFactory;
        $this->campaignRepository = $campaignRepository;
        $this->statisticManagement = $statisticManagement;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->dateTime = $dateTime;
        $this->backendTemplateFactory = $backendTemplateFactory;
        $this->campaignHelper = $campaignHelper;

        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $postData = $this->getRequestPostParams();
        $redirectResult = $this->createRedirectToEventCampaignsPage($postData);

        try {
            $this->validateRequestPostParams($postData);
            $emailTemplateId = $this->saveEmailTemplate($postData);
            $this->resetStatisticsIfRequired($postData);
            $this->updateOrCreateCampaignStep($postData, $emailTemplateId);
            $this->addSuccessMessage();
        } catch (Exception $e) {
            $this->addExceptionErrorMessage($e);
        }

        return $redirectResult;
    }

    /**
     * Get post params
     *
     * @return array
     */
    protected function getRequestPostParams()
    {
        return $this->getRequest()->getPostValue();
    }

    /**
     * Create redirect to campaign page
     *
     * @param array $postData
     * @return Redirect
     */
    protected function createRedirectToEventCampaignsPage($postData)
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        $campaignId = $postData[static::REQUEST_PARAM_CAMPAIGN_ID];
        $ruleId = $postData[static::REQUEST_PARAM_CAMPAIGN_EDIT_ID];
        $eventCode = $this->getEventCodeByCampaignId($campaignId);

        return $this->createRedirectToEventCampaignsByEventCode($eventCode, $campaignId, $ruleId, $redirectBack);
    }

    /**
     * Get event code by campaign id
     *
     * @param int $campaignId
     * @return string
     */
    protected function getEventCodeByCampaignId($campaignId)
    {
        return $this->campaignHelper->getEventCodeByCampaignId($campaignId);
    }

    /**
     * Create redirect to campaign
     *
     * @param string $eventCode
     * @param bool $campaignId
     * @param bool $ruleId
     * @param bool $redirectBack
     * @return Redirect
     */
    protected function createRedirectToEventCampaignsByEventCode(
        $eventCode,
        $campaignId = false,
        $ruleId = false,
        $redirectBack = false
    ) {
        $eventCampaignsRoute = $this->getEventCampaignsRoute($eventCode);

        if ($redirectBack) {
            if ($redirectBack == 'continue' && $ruleId) {
                $eventCampaignsRoute = $this->getUrl('*/*/edit', ['id' => $ruleId]);
            } elseif ($redirectBack === 'new') {
                $eventCampaignsRoute = $this->getUrl('*/*/edit', ['campaign_id' => $campaignId]);
            }
        }

        $redirectResult = $this->createRedirect();
        $redirectResult->setPath($eventCampaignsRoute);

        return $redirectResult;
    }

    /**
     * Create redirect
     *
     * @return Redirect
     */
    protected function createRedirect()
    {
        return $this->resultRedirectFactory->create();
    }

    /**
     * Get event campaign route
     *
     * @param string $eventCode
     * @return string
     */
    protected function getEventCampaignsRoute($eventCode)
    {
        return "followup/event_campaign/index/event_code/{$eventCode}";
    }

    /**
     * Validate post params
     *
     * @param array $postData
     * @throws ValidatorException
     */
    protected function validateRequestPostParams($postData)
    {
        if (!$postData) {
            throw new ValidatorException(__('Invalid request.'));
        }
    }

    /**
     * Save email template
     *
     * @param array $postData
     * @return int E-mail Template Id
     */
    private function saveEmailTemplate($postData)
    {
        $templateId = $postData[static::REQUEST_POST_PARAM_TEMPLATE_ID];
        $emailTemplateModel = $this->getEmailTemplateById($templateId);

        $templateSubject = $postData[static::REQUEST_POST_PARAM_TEMPLATE_SUBJECT];
        $templateText = $postData[static::REQUEST_POST_PARAM_TEMPLATE_CONTENT];
        $saveSettings = $postData['save_settings'];

        if ($saveSettings == SaveSettingValuesInterface::OVERWRITE_ORIGINAL) {
            $emailTemplateId = $this->overwriteOriginalTemplate($emailTemplateModel, $templateSubject, $templateText);
        } else {
            $newTemplateName = $postData[static::REQUEST_POST_PARAM_NEW_TEMPLATE_NAME];
            $emailTemplateId = $this->saveAsNew($emailTemplateModel, $templateSubject, $templateText, $newTemplateName);
        }

        return $emailTemplateId;
    }

    /**
     * Get email template by id
     *
     * @param int $emailTemplateId
     * @return TemplateInterface
     */
    protected function getEmailTemplateById($emailTemplateId)
    {
        return $this->emailTemplateRepository->get($emailTemplateId);
    }

    /**
     * Overwrite original template
     *
     * @param TemplateInterface|Template $emailTemplateModel
     * @param string $templateSubject
     * @param string $templateText
     * @return int
     */
    protected function overwriteOriginalTemplate($emailTemplateModel, $templateSubject, $templateText)
    {
        $modifiedAt = $this->dateTime->gmtDate();

        $emailTemplateModel
            ->setTemplateSubject($templateSubject)
            ->setTemplateText($templateText)
            ->setModifiedAt($modifiedAt);

        $this->saveEmailTemplateModel($emailTemplateModel);

        return $emailTemplateModel->getId();
    }

    /**
     * Save email template
     *
     * @param Template $emailTemplateModel
     */
    protected function saveEmailTemplateModel(Template $emailTemplateModel)
    {
        $this->emailTemplateRepository->save($emailTemplateModel);
    }

    /**
     * Save as new
     *
     * @param TemplateInterface|Template $emailTemplateModel
     * @param string $templateSubject
     * @param string $templateText
     * @param string $newTemplateName
     * @return int
     */
    private function saveAsNew($emailTemplateModel, $templateSubject, $templateText, $newTemplateName)
    {
        $backendTemplate = $this->createBackendTemplate();

        $backendTemplate
            ->setTemplateSubject($templateSubject)
            ->setTemplateCode($newTemplateName)
            ->setTemplateText($templateText)
            ->setTemplateStyles($emailTemplateModel->getTemplateStyles())
            ->setOrigTemplateCode($emailTemplateModel->getTemplateCode())
            ->setOrigTemplateVariables($emailTemplateModel->getOrigTemplateVariables())
            ->setTemplateType($emailTemplateModel->getTemplateType());

        $this->saveEmailTemplateModel($backendTemplate);

        return $backendTemplate->getId();
    }

    /**
     * Create backend template
     *
     * @return BackendTemplate
     */
    private function createBackendTemplate()
    {
        return $this->backendTemplateFactory->create();
    }

    /**
     * Reset statistics
     *
     * @param array $postData
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    protected function resetStatisticsIfRequired($postData)
    {
        $campaignStepId = $postData['entity_id'];
        $resetStatistics = $postData['reset_statistics'];

        if ($this->isResetStatisticsRequired($campaignStepId, $resetStatistics)) {
            $this->resetStatisticByCampaignStepId($campaignStepId);
        }
    }

    /**
     * Is reset statistics
     *
     * @param int $campaignStepId
     * @param int $resetStatistics
     * @return bool
     */
    protected function isResetStatisticsRequired($campaignStepId, $resetStatistics)
    {
        return $campaignStepId && $resetStatistics;
    }

    /**
     * Reset statistic by campaign step id
     *
     * @param int $campaignStepId
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    protected function resetStatisticByCampaignStepId($campaignStepId)
    {
        $this->statisticManagement->resetStatisticByCampaignStep($campaignStepId);
    }

    /**
     * Update or create campaign step
     *
     * @param array $postData
     * @param int $emailTemplateId
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    protected function updateOrCreateCampaignStep($postData, $emailTemplateId)
    {
        $campaignStepId = $postData['entity_id'];

        $campaignStep = $campaignStepId
            ? $this->updateCampaignStep($campaignStepId, $emailTemplateId, $postData)
            : $this->createCampaignStepByPostData($emailTemplateId, $postData);

        $this->saveCampaignStep($campaignStep);
    }

    /**
     * Update campaign step
     *
     * @param int $campaignStepId
     * @param int $emailTemplateId
     * @param array $postData
     * @return CampaignStep
     * @throws NoSuchEntityException
     */
    protected function updateCampaignStep($campaignStepId, $emailTemplateId, $postData)
    {
        $campaignStep = $this->getCampaignStepById($campaignStepId);
        $this->updateCampaignStepByPostData($campaignStep, $emailTemplateId, $postData);

        return $campaignStep;
    }

    /**
     * Get campaign step by id
     *
     * @param int $campaignStepId
     * @return CampaignStep
     * @throws NoSuchEntityException
     */
    protected function getCampaignStepById($campaignStepId)
    {
        return $this->campaignStepsRepository->get($campaignStepId);
    }

    /**
     * Update campaign steup by post data
     *
     * @param CampaignStep $campaignStep
     * @param int $emailTemplateId
     * @param array $postData
     * @return CampaignStep
     */
    protected function updateCampaignStepByPostData(CampaignStep $campaignStep, $emailTemplateId, $postData)
    {
        $campaignStepData = $campaignStep->getData();

        if ($campaignStepData) {
            $data = array_intersect_key($postData, $campaignStepData);
            $campaignStep->addData($data);
        } else {
            $campaignStep->setData($postData);
            $campaignStep->setId(null);
        }

        $campaignStep->setTemplateId($emailTemplateId);

        return $campaignStep;
    }

    /**
     * Create campaign step by post data
     *
     * @param int $emailTemplateId
     * @param array $postData
     * @return CampaignStep
     */
    protected function createCampaignStepByPostData($emailTemplateId, $postData)
    {
        $campaignStep = $this->createCampaignStep();
        $this->updateCampaignStepByPostData($campaignStep, $emailTemplateId, $postData);

        return $campaignStep;
    }

    /**
     * Create campaign step
     *
     * @return CampaignStep
     */
    protected function createCampaignStep()
    {
        return $this->campaignStepFactory->create();
    }

    /**
     * Save campaign step
     *
     * @param CampaignStep $campaignStep
     * @throws CouldNotSaveException
     */
    protected function saveCampaignStep(CampaignStep $campaignStep)
    {
        $this->campaignStepsRepository->save($campaignStep);
    }

    /**
     * Add success message
     */
    protected function addSuccessMessage()
    {
        $this->messageManager->addSuccessMessage(__(static::SUCCESS_MESSAGE));
    }

    /**
     * Add exception error message
     *
     * @param Exception $exception
     */
    protected function addExceptionErrorMessage(Exception $exception)
    {
        $this->messageManager->addErrorMessage($exception->getMessage());
    }

    /**
     * Get params
     *
     * @return array
     */
    protected function getRequestParams()
    {
        return $this->getRequest()->getParams();
    }
}
