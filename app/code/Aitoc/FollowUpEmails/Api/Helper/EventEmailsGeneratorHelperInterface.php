<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Helper;

use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailInterface;

interface EventEmailsGeneratorHelperInterface
{
    /**
     * Get unprocessed entities
     *
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param array $processedEntitiesIds
     * @return array
     */
    public function getUnprocessedEntities(
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        $processedEntitiesIds
    );

    /**
     * Get attribute code where entity id stored to get processed entities ids from generated emails.
     *
     * @return string
     */
    public function getEntityIdAttributeCode();

    /**
     * Get entity id attribute code
     *
     * @param int $entityId
     * @return mixed
     */
    public function getEntityById($entityId);

    /**
     * Get entity id by entity
     *
     * @param mixed $entity
     * @return int
     */
    public function getEntityIdByEntity($entity);

    /**
     * Get event timestamp by entity
     *
     * @param mixed $entity
     * @return mixed
     */
    public function getEventTimestampByEntity($entity);

    /**
     * Returns array that would be saved as email attributes.
     *
     * Should contains entity id and configured by submodule admin entity fields
     *
     * (for example order status for order entity).
     *
     * @param mixed $entity
     * @return array
     */
    public function getEmailAttributesByEntity($entity);

    /**
     * To know where to send email.
     *
     * @param mixed $entity
     * @return string
     */
    public function getCustomerEmailByEntity($entity);

    /**
     * To get Customer full name for email.
     *
     * @param mixed $entity
     * @return string|null
     */
    public function getCustomerFirstsNameByEntity($entity);

    /**
     * To get Customer full name for email.
     *
     * @param mixed $entity
     * @return string|null
     */
    public function getCustomerLastNameByEntity($entity);

    /**
     * To know:
     *  - for which websites generate cart price rules;
     *  - from which store send emails;
     *
     * @param mixed $entity
     * @return int
     */
    public function getStoreIdByEntity($entity);

    /**
     * Is send email to customer required
     *
     * @param CampaignStepInterface $campaignStep
     * @return bool
     */
    public function isSendEmailToCustomerRequired(CampaignStepInterface $campaignStep);

    /**
     * Get additional email addresses
     *
     * @param CampaignStepInterface $campaignStep
     * @return mixed
     */
    public function getAdditionalEmailAddresses(CampaignStepInterface $campaignStep);

    /**
     * Because generated emails not sent immediately after generation, we should
     *
     * Check by Campaign Step current settings and current entity state if already generated email can be sent.
     *
     * @param CampaignStepInterface $campaignStep
     * @param array $emailAttributes
     * @return bool
     */
    public function canSendEmail(CampaignStepInterface $campaignStep, $emailAttributes);

    /**
     * Get module data
     *
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param EmailInterface $email
     * @param array $emailAttributes
     * @return array
     */
    public function getModuleData(
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        EmailInterface $email,
        $emailAttributes
    );

    /**
     * Get entity statistic data
     *
     * @param CampaignStepInterface $campaignStep
     * @param array $emailAttributes
     * @return array|bool
     */
    public function getEntityStatisticData(CampaignStepInterface $campaignStep, $emailAttributes);
}
