<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Ui\DataProvider\Form;

use Aitoc\FollowUpEmails\Api\CampaignRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\EventManagementInterface;
use Aitoc\FollowUpEmails\Api\RegisterDataProviderInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\Campaign\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class LoadCampaignForm extends AbstractDataProvider
{
    /**
     * @var RegisterDataProviderInterface
     */
    protected $registerDataProvider;

    /**
     * @var EventManagementInterface
     */
    protected $eventManagement;

    /**
     * @var array
     */
    private $loadedData;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * LoadCampaignForm constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $campaignsCollectionFactory
     * @param RegisterDataProviderInterface $registerDataProvider
     * @param EventManagementInterface $eventManagement
     * @param CampaignRepositoryInterface $campaignRepository
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $campaignsCollectionFactory,
        RegisterDataProviderInterface $registerDataProvider,
        EventManagementInterface $eventManagement,
        CampaignRepositoryInterface $campaignRepository,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $campaignsCollectionFactory->create();
        $this->registerDataProvider = $registerDataProvider;
        $this->eventManagement = $eventManagement;
        $this->campaignRepository = $campaignRepository;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!isset($this->loadedData)) {
            $this->loadedData = $this->getCampaignsData();
        }

        return $this->loadedData;
    }

    /**
     * Get campaigns data
     *
     * @return array
     */
    protected function getCampaignsData()
    {
        $campaignId = $this->registerDataProvider->getCurrentCampaignId();

        if (!$campaignId) {
            return null;
        }

        $campaign = $this->getCampaignById($campaignId);
        $campaignData = $campaign->getData();

        return [$campaignId => $campaignData];
    }

    /**
     * Get campaign by id
     *
     * @param int $campaignId
     * @return CampaignInterface
     */
    private function getCampaignById($campaignId)
    {
        return $this->campaignRepository->get($campaignId);
    }

    /**
     * Get meta
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        return $this->addEventCodeOptions($meta);
    }

    /**
     * Add event code options
     *
     * @param array $meta
     * @return array
     */
    protected function addEventCodeOptions($meta)
    {
        $eventCodeOptions = $this->getEventCodeOptions();

        return $this->setEventCodeOptions($meta, $eventCodeOptions);
    }

    /**
     * Get event code options
     *
     * @return array
     */
    protected function getEventCodeOptions()
    {
        $event = $this->getCurrentEvent();

        return $this->eventToEventCodeOptions($event);
    }

    /**
     * Get current event
     *
     * @return array
     */
    protected function getCurrentEvent()
    {
        $eventCode = $this->getCurrentEventCode();

        return $this->getEventAttributesByCode($eventCode);
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
     * Get event attributes by code
     *
     * @param string $eventCode
     * @return array
     */
    protected function getEventAttributesByCode($eventCode)
    {
        return $this->eventManagement->getAttributesByEventCode($eventCode);
    }

    /**
     * Event to event code options
     *
     * @param array $eventAttributes
     * @return array
     */
    protected function eventToEventCodeOptions($eventAttributes)
    {
        return [
            [
                'value' => $eventAttributes['code'],
                'label' => $eventAttributes['name']
            ]
        ];
    }

    /**
     * Set event code options
     *
     * @param array $meta
     * @param array $eventCodeOptions
     * @return array
     */
    protected function setEventCodeOptions($meta, $eventCodeOptions)
    {
        $meta['general']['children']['event_code']['arguments']['data']['options'] = $eventCodeOptions;

        return $meta;
    }
}
