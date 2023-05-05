<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Controller\Adminhtml\Event\Campaign;

use Aitoc\FollowUpEmails\Api\CampaignRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\RegisterDataProviderInterface;
use Aitoc\FollowUpEmails\Controller\Adminhtml\Event\CampaignBase\Edit as BaseEdit;
use Aitoc\FollowUpEmails\Model\EventManagement;
use Magento\Backend\App\Action;

class Edit extends BaseEdit
{
    const REQUEST_PARAM_CAMPAIGN_ID = 'id';

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * Edit constructor.
     *
     * @param Action\Context $context
     * @param EventManagement $eventManagement
     * @param RegisterDataProviderInterface $registerDataProvider
     * @param CampaignRepositoryInterface $campaignRepository
     */
    public function __construct(
        Action\Context $context,
        EventManagement $eventManagement,
        RegisterDataProviderInterface $registerDataProvider,
        CampaignRepositoryInterface $campaignRepository
    ) {
        parent::__construct($context, $eventManagement, $registerDataProvider);

        $this->campaignRepository = $campaignRepository;
    }

    /**
     * Get requested sub page id
     *
     * @return int
     */
    protected function getRequestedSubpageId()
    {
        return $this->getRequestedCampaignId();
    }

    /**
     * Get requested campaign id
     *
     * @return int
     */
    protected function getRequestedCampaignId()
    {
        return (int) $this->getRequestedParam(self::REQUEST_PARAM_CAMPAIGN_ID);
    }

    /**
     * Get event code by entity id
     *
     * @param int $entityId
     * @return string
     */
    protected function getEventCodeByEntityId($entityId)
    {
        return $this->getEventCodeByCampaignId($entityId);
    }

    /**
     * Get event code by campaign id
     *
     * @param int $campaignId
     * @return string
     */
    protected function getEventCodeByCampaignId($campaignId)
    {
        $campaign = $this->getCampaignById($campaignId);

        return $campaign->getEventCode();
    }

    /**
     * Get campaign by id
     *
     * @param int $campaignId
     * @return CampaignInterface
     */
    protected function getCampaignById($campaignId)
    {
        return $this->campaignRepository->get($campaignId);
    }

    /**
     * Get event code by requested parent entity id
     *
     * @return string|null
     */
    protected function getEventCodeByRequestedParentEntityId()
    {
        return $this->getRequestedEventCode();
    }

    /**
     * Get requested event code
     *
     * @return string|null
     */
    protected function getRequestedEventCode()
    {
        return $this->getRequestedParam(self::REQUEST_PARAM_EVENT_CODE);
    }
}
