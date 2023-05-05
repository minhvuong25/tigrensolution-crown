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

use Aitoc\FollowUpEmails\Api\CampaignRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\CampaignStepRepositoryInterface;
use Aitoc\FollowUpEmails\Api\EmailRepositoryInterface;

class Campaign
{
    /**
     * @var CampaignRepositoryInterface
     */
    protected $campaignsRepository;

    /**
     * @var CampaignStepRepositoryInterface
     */
    protected $campaignStepRepository;

    /**
     * @var EmailRepositoryInterface
     */
    protected $emailRepository;

    /**
     * Campaign constructor.
     *
     * @param CampaignRepositoryInterface $campaignsRepository
     * @param CampaignStepRepositoryInterface $campaignStepRepository
     * @param EmailRepositoryInterface $emailRepository
     */
    public function __construct(
        CampaignRepositoryInterface $campaignsRepository,
        CampaignStepRepositoryInterface $campaignStepRepository,
        EmailRepositoryInterface $emailRepository
    ) {
        $this->campaignsRepository = $campaignsRepository;
        $this->campaignStepRepository = $campaignStepRepository;
        $this->emailRepository = $emailRepository;
    }

    /**
     * Get event code by campaign id
     *
     * @param int $campaignId
     * @return string
     */
    public function getEventCodeByCampaignId($campaignId)
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
        return $this->campaignsRepository->get($campaignId);
    }

    /**
     * Get campaign step by email id
     *
     * @param int $emailId
     * @return \Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface
     */
    public function getCampaignStepByEmailId($emailId)
    {
        $email = $this->emailRepository->get($emailId);
        $campaignStepId = $email->getCampaignStepId();

        return $this->getCampaignStepById($campaignStepId);
    }

    /**
     * Get campaign step by id
     *
     * @param int $campaignStepId
     * @return \Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface
     */
    public function getCampaignStepById($campaignStepId)
    {
        return $this->campaignStepRepository->get($campaignStepId);
    }
}
