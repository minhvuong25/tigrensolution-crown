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

use Aitoc\FollowUpEmails\Api\CampaignRepositoryInterface;
use Aitoc\FollowUpEmails\Api\CampaignStepRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\RegisterDataProviderInterface;
use Aitoc\FollowUpEmails\Controller\Adminhtml\Event\CampaignBase\Edit as BaseEdit;
use Aitoc\FollowUpEmails\Model\EventManagement;
use Magento\Backend\App\Action;

class Edit extends BaseEdit
{
    const REQUEST_PARAM_CAMPAIGN_STEP_ID = 'id';
    const REQUEST_PARAM_CAMPAIGN_ID = 'campaign_id';

    /**
     * @var CampaignStepRepositoryInterface
     */
    protected $campaignStepsRepository;

    /**
     * @var CampaignRepositoryInterface
     */
    protected $campaignsRepository;

    /**
     * Edit constructor.
     *
     * @param Action\Context $context
     * @param EventManagement $eventManagement
     * @param RegisterDataProviderInterface $registerDataProvider
     * @param CampaignStepRepositoryInterface $campaignStepRepository
     * @param CampaignRepositoryInterface $campaignsRepository
     */
    public function __construct(
        Action\Context $context,
        EventManagement $eventManagement,
        RegisterDataProviderInterface $registerDataProvider,
        CampaignStepRepositoryInterface $campaignStepRepository,
        CampaignRepositoryInterface $campaignsRepository
    ) {
        parent::__construct($context, $eventManagement, $registerDataProvider);

        $this->campaignStepsRepository = $campaignStepRepository;
        $this->campaignsRepository = $campaignsRepository;
    }

    /**
     * Get event code by requested parent entity id
     *
     * @return string|null
     */
    protected function getEventCodeByRequestedParentEntityId()
    {
        $campaignId = $this->getRequestedCampaignId();
        $campaign = $this->getCampaignById($campaignId);

        return $campaign->getEventCode();
    }

    /**
     * Get requested campaign id
     *
     * @return string|null
     */
    protected function getRequestedCampaignId()
    {
        return $this->getRequestedParam(self::REQUEST_PARAM_CAMPAIGN_ID);
    }

    /**
     * Get campaign by id
     *
     * @param int $campaignId
     * @return CampaignInterface
     */
    protected function getCampaignById($campaignId)
    {
        return $this->campaignsRepository->get($campaignId);
    }

    /**
     * Get requested by subpage id
     *
     * @return int
     */
    protected function getRequestedSubpageId()
    {
        return $this->getRequestedCampaignStepId();
    }

    /**
     * Get requested campaign step id
     *
     * @return int
     */
    protected function getRequestedCampaignStepId()
    {
        return (int) $this->getRequestedParam(self::REQUEST_PARAM_CAMPAIGN_STEP_ID);
    }

    /**
     * Get event code by entity id
     *
     * @param int $entityId
     * @return string
     */
    protected function getEventCodeByEntityId($entityId)
    {
        return $this->getEventCodeByCampaignStepId($entityId);
    }

    /**
     * Get event code by campaign step id
     *
     * @param int $campaignId
     * @return string
     */
    protected function getEventCodeByCampaignStepId($campaignId)
    {
        $campaignStep = $this->getCampaignStepById($campaignId);
        $campaignId = $campaignStep->getCampaignId();
        $campaign = $this->getCampaignById($campaignId);

        return $campaign->getEventCode();
    }

    /**
     * Get campaign step by id
     *
     * @param int $campaignStepId
     * @return CampaignStepInterface
     */
    protected function getCampaignStepById($campaignStepId)
    {
        return $this->campaignStepsRepository->get($campaignStepId);
    }
}
