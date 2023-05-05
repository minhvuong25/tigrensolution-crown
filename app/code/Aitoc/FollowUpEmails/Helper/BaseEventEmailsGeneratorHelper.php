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

use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Helper\EmailInterface as EmailHelperInterface;
use Aitoc\FollowUpEmails\Api\Helper\EventEmailsGeneratorHelperInterface;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTimeHelper;

abstract class BaseEventEmailsGeneratorHelper implements EventEmailsGeneratorHelperInterface
{
    /**
     * @var EmailHelperInterface
     */
    protected $emailHelper;

    /**
     * @var DateTimeHelper
     */
    protected $dateTimeHelper;

    /**
     * BaseEventEmailsGeneratorHelper constructor.
     *
     * @param EmailHelperInterface $emailHelper
     * @param DateTimeHelper $dateTimeHelperTimeHelper
     */
    public function __construct(
        EmailHelperInterface $emailHelper,
        DateTimeHelper $dateTimeHelperTimeHelper
    ) {
        $this->emailHelper = $emailHelper;
        $this->dateTimeHelper = $dateTimeHelperTimeHelper;
    }

    /**
     * Get additional email addresses
     *
     * @param CampaignStepInterface $campaignStep
     * @return array
     */
    public function getAdditionalEmailAddresses(CampaignStepInterface $campaignStep)
    {
        return [];
    }

    /**
     * Get entity by email attributes
     *
     * @param array $emailAttributes
     * @return mixed
     */
    protected function getEntityByEmailAttributes($emailAttributes)
    {
        return $this->emailHelper->getEntityByEmailAttributes($this, $emailAttributes);
    }

    /**
     * Get entity id by email attributes
     *
     * @param array $emailAttributes
     * @return int
     */
    protected function getEntityIdByEmailAttributes($emailAttributes)
    {
        $entityAttributeCode = $this->getEntityIdAttributeCode();

        return $emailAttributes[$entityAttributeCode];
    }

    /**
     * Is send email to customer required
     *
     * @param CampaignStepInterface $campaignStep
     * @return bool
     */
    public function isSendEmailToCustomerRequired(CampaignStepInterface $campaignStep)
    {
        return true;
    }

    /**
     * Get email attributes by entity
     *
     * @param mixed $entity
     * @return array
     */
    public function getEmailAttributesByEntity($entity)
    {
        return [
            $this->getEntityIdAttributeCode() => $this->getEntityIdByEntity($entity),
        ];
    }

    /**
     * Convert to timestamp
     *
     * @param string $string
     * @return int
     */
    protected function convertToTimestamp($string)
    {
        return $this->dateTimeHelper->timestamp($string);
    }
}
