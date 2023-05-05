<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\Adminhtml\Event\CampaignBase;

use Aitoc\FollowUpEmails\Api\RegisterDataProviderInterface;
use Aitoc\FollowUpEmails\Model\EventManagement;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;

abstract class Edit extends Action
{
    const REQUEST_PARAM_EVENT_CODE = 'event_code';

    /**
     * Get requested subpage id
     *
     * @return int
     */
    abstract protected function getRequestedSubpageId();

    /**
     * Get event code by entity id
     *
     * @param int $entityId
     * @return string
     */
    abstract protected function getEventCodeByEntityId($entityId);

    /**
     * Get event code by requested parent entity id
     *
     * @return string|null
     */
    abstract protected function getEventCodeByRequestedParentEntityId();

    /**
     * @var Registry
     */
    protected $registerDataProvider;
    /**
     * @var EventManagement
     */
    private $eventManagement;

    /**
     * Edit constructor.
     *
     * @param Action\Context $context
     * @param EventManagement $eventManagement
     * @param RegisterDataProviderInterface $registerDataProvider
     */
    public function __construct(
        Action\Context $context,
        EventManagement $eventManagement,
        RegisterDataProviderInterface $registerDataProvider
    ) {
        $this->eventManagement = $eventManagement;
        $this->registerDataProvider = $registerDataProvider;

        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $this->getByRequestAndSetRegistryCurrentCampaignId();
        $this->getAndSetRegistryCurrentEventCode();

        return $this->createResultPage();
    }

    /**
     * Get request and set registry
     */
    private function getByRequestAndSetRegistryCurrentCampaignId()
    {
        $campaignId = $this->getRequestedParam('id');
        $this->setRegistryCurrentCampaignId($campaignId);
    }

    /**
     * Set registry
     *
     * @param int $campaignId
     */
    private function setRegistryCurrentCampaignId($campaignId)
    {
        $this->registerDataProvider->setCurrentCampaignId($campaignId);
    }

    /**
     * Get and set registry
     */
    protected function getAndSetRegistryCurrentEventCode()
    {
        $eventCode = $this->getCurrentEventCode();
        $this->setRegistryCurrentEventCode($eventCode);
    }

    /**
     * Get current event code
     *
     * @return string
     */
    protected function getCurrentEventCode()
    {
        return ($campaignId = $this->getRequestedSubpageId())
            ? $this->getEventCodeByEntityId($campaignId)
            : $this->getEventCodeByRequestedParentEntityId();
    }

    /**
     * Get params
     *
     * @param string $paramName
     * @return string|null
     */
    protected function getRequestedParam($paramName)
    {
        return $this->getRequest()->getParam($paramName);
    }

    /**
     * Set registry
     *
     * @param string $eventCode
     */
    protected function setRegistryCurrentEventCode($eventCode)
    {
        $this->registerDataProvider->setCurrentEventCode($eventCode);
    }

    /**
     * Create result page
     *
     * @return ResultInterface
     */
    protected function createResultPage()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
