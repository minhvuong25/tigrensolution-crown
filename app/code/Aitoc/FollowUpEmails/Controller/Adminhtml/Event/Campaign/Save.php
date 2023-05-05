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
use Aitoc\FollowUpEmails\Api\Data\CampaignInterfaceFactory;
use Aitoc\FollowUpEmails\Model\StatisticManagement;
use InvalidArgumentException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\PhpEnvironment\Request;

class Save extends Action
{
    const REQUEST_PARAM_NAME_CAMPAIGN_ID = 'entity_id';
    const REQUEST_PARAM_NAME_RESET_CAMPAIGN_STATISTIC = 'reset';
    const REQUEST_PARAM_NAME_CUSTOMER_GROUP_IDS = 'customer_group_ids';
    const REQUEST_PARAM_NAME_STORE_IDS = 'store_ids';
    const REQUEST_PARAM_NAME_EVENT_CODE = 'event_code';

    const SUCCESS_MESSAGE = 'You saved the campaign.';

    /**
     * @var CampaignInterfaceFactory
     */
    private $campaignFactory;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignsRepository;

    /**
     * @var StatisticManagement
     */
    private $statisticManagement;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param CampaignInterfaceFactory $campaignFactory
     * @param CampaignRepositoryInterface $campaignsRepository
     * @param StatisticManagement $statisticManagement
     */
    public function __construct(
        Context $context,
        CampaignInterfaceFactory $campaignFactory,
        CampaignRepositoryInterface $campaignsRepository,
        StatisticManagement $statisticManagement
    ) {
        $this->campaignFactory = $campaignFactory;
        $this->campaignsRepository = $campaignsRepository;
        $this->statisticManagement = $statisticManagement;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|ResultInterface
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $data = $this->getRequestPostValues();
        $this->validatePostValues($data);
        $requestedCampaignId = $this->getRequestedCampaignId($data);

        if ($requestedCampaignId) {
            $campaign = $this->getCampaignById($requestedCampaignId);

            $requestedIsResetCampaignStatistic = $this->getRequestedResetCampaignStatistic($data);

            if ($requestedIsResetCampaignStatistic) {
                $this->resetStatisticByCampaignId($requestedCampaignId);
            }
        } else {
            unset($data['entity_id']);
            $campaign = $this->createCampaign();
        }

        $this->updateAndSaveCampaignByRequestedValues($campaign, $data);
        $this->addSuccessMessage();

        $eventCode = $this->getRequestedEventCode($data);

        return $this->createRedirectToEventPage($eventCode);
    }

    /**
     * Get requested post values
     *
     * @return array
     */
    private function getRequestPostValues()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        return $request->getPostValue();
    }

    /**
     * Validate post values
     *
     * @param array $postValues
     */
    private function validatePostValues($postValues)
    {
        if (!$postValues) {
            throw new InvalidArgumentException();
        }
    }

    /**
     * Get requested campaign id
     *
     * @param array $postValues
     * @return int|null
     */
    private function getRequestedCampaignId($postValues)
    {
        return $postValues[self::REQUEST_PARAM_NAME_CAMPAIGN_ID];
    }

    /**
     * Get campaign by id
     *
     * @param int $campaignId
     * @return CampaignInterface
     */
    private function getCampaignById($campaignId)
    {
        return $this->campaignsRepository->get($campaignId);
    }

    /**
     * Get requested reset statistic
     *
     * @param array $postValues
     * @return bool
     */
    private function getRequestedResetCampaignStatistic($postValues)
    {
        return (bool) $postValues[self::REQUEST_PARAM_NAME_RESET_CAMPAIGN_STATISTIC];
    }

    /**
     * Reset campaign statistics by id
     *
     * @param int $campaignId
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    private function resetStatisticByCampaignId($campaignId)
    {
        $this->statisticManagement->resetStatisticByCampaign($campaignId);
    }

    /**
     * Create campaign
     *
     * @return CampaignInterface
     */
    private function createCampaign()
    {
        return $this->campaignFactory->create();
    }

    /**
     * Update and save campaign values
     *
     * @param CampaignInterface $campaign
     * @param array $data
     */
    private function updateAndSaveCampaignByRequestedValues(CampaignInterface $campaign, $data)
    {
        $this->updateCampaignByRequestedValues($campaign, $data);
        $this->saveCampaign($campaign);
    }

    /**
     * Update campaign values
     *
     * @param CampaignInterface $campaign
     * @param array $postData
     */
    private function updateCampaignByRequestedValues(CampaignInterface $campaign, $postData)
    {
        $requestedCustomerGroupIds = $this->getRequestedCustomerGroupIds($postData);
        $requestedStoreIds = $this->getRequestedStoreIds($postData);

        $campaign->addData($postData);//todo: set data by campaign methods
        $campaign->setCustomerGroupIds($requestedCustomerGroupIds);
        $campaign->setStoreIds($requestedStoreIds);
    }

    /**
     * Get requested customer group ids
     *
     * @param array $postValues
     * @return mixed
     */
    private function getRequestedCustomerGroupIds($postValues)
    {
        return $postValues[self::REQUEST_PARAM_NAME_CUSTOMER_GROUP_IDS];
    }

    /**
     * Get requested store ids
     *
     * @param array $postValues
     * @return mixed
     */
    private function getRequestedStoreIds($postValues)
    {
        return $postValues[self::REQUEST_PARAM_NAME_STORE_IDS];
    }

    /**
     * Save campaign
     *
     * @param CampaignInterface $campaign
     * @return CampaignInterface
     */
    private function saveCampaign(CampaignInterface $campaign)
    {
        return $this->campaignsRepository->save($campaign);
    }

    /**
     * Add success message
     */
    private function addSuccessMessage()
    {
        $this->messageManager->addSuccessMessage(__(self::SUCCESS_MESSAGE));
    }

    /**
     * Get requested event code
     *
     * @param array $postData
     * @return mixed
     */
    private function getRequestedEventCode($postData)
    {
        return $postData[self::REQUEST_PARAM_NAME_EVENT_CODE];
    }

    /**
     * Create redirect to event page
     *
     * @param string $eventCode
     * @return ResponseInterface
     */
    private function createRedirectToEventPage($eventCode)
    {
        return $this->_redirect('followup/event_campaign/index/event_code/' . $eventCode);
    }
}
