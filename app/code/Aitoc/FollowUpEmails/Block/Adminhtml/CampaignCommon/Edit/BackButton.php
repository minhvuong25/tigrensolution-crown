<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Block\Adminhtml\CampaignCommon\Edit;

use Aitoc\FollowUpEmails\Api\RegisterDataProviderInterface;
use Aitoc\FollowUpEmails\Block\Adminhtml\Campaign\GenericButton;
use Aitoc\FollowUpEmails\Model\Campaign;
use Aitoc\FollowUpEmails\Model\CampaignRepository;
use Aitoc\FollowUpEmails\Model\CampaignStepRepository;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var CampaignStepRepository
     */
    protected $campaignStepsRepository;
    /**
     * @var CampaignRepository
     */
    protected $campaignRepository;

    /**
     * @var RegisterDataProviderInterface
     */
    protected $registerDataProvider;

    /**
     * BackButton constructor.
     *
     * @param Context $context
     * @param CampaignStepRepository $campaignStepsRepository
     * @param CampaignRepository $campaignRepository
     * @param RegisterDataProviderInterface $registerDataProvider
     */
    public function __construct(
        Context $context,
        CampaignStepRepository $campaignStepsRepository,
        CampaignRepository $campaignRepository,
        RegisterDataProviderInterface $registerDataProvider
    ) {
        $this->campaignStepsRepository = $campaignStepsRepository;
        $this->campaignRepository = $campaignRepository;
        $this->registerDataProvider = $registerDataProvider;
        parent::__construct($context);
    }

    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $backUrl = $this->getBackUrl();

        return [
            'label' => __('Back'),
            'on_click' => "location.href = '{$backUrl}';",
            'class' => 'back',
            'sort_order' => 10,
        ];
    }

    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $eventCode = $this->getCurrentEventCode();

        return $this->getEventCampaignUrl($eventCode);
    }

    /**
     * Get current event code
     *
     * @return string
     */
    protected function getCurrentEventCode()
    {
        return $this->registerDataProvider->getCurrentEventCode();
    }

    /**
     * Get event campaign url
     *
     * @param string $eventCode
     * @return string
     */
    protected function getEventCampaignUrl($eventCode)
    {
        //rf: use `event_code` as params
        return $this->getUrl('followup/event_campaign/index/event_code/' . $eventCode);
    }

    /**
     * Get campaign id
     *
     * @return null|string
     */
    protected function getRequestedCampaignId()
    {
        $requestedParam = $this->getRequestedParam('campaign_id');

        return $requestedParam ? (int) $requestedParam : $requestedParam;
    }

    /**
     * Get params
     *
     * @param string $paramName
     * @return string|null
     */
    protected function getRequestedParam($paramName)
    {
        return $this->context->getRequest()->getParam($paramName);
    }

    /**
     * Get event code by campaign id
     *
     * @param int $campaignId
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getEventCodeByCampaignId($campaignId)
    {
        $campaign = $this->getCampaignById($campaignId);

        return $campaign->getEventCode();
    }

    /**
     * Get campaign id
     *
     * @param int $campaignId
     * @return Campaign
     * @throws NoSuchEntityException
     */
    protected function getCampaignById($campaignId)
    {
        return $this->campaignRepository->get($campaignId);
    }

    /**
     * Get event code
     *
     * @return string|null
     */
    protected function getRequestedEventCode()
    {
        return $this->getRequestedParam('event_code');
    }
}
