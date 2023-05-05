<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Cron;

use Aitoc\FollowUpEmails\Service\EmailManagement;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class EmailProcess
{
    /**
     * @var EmailManagement
     */
    private $emailManagement;

    /**
     * @param EmailManagement $emailManagement
     */
    public function __construct(EmailManagement $emailManagement)
    {
        $this->emailManagement = $emailManagement;
    }

    /**
     * Generate emails
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws InputException
     */
    public function generateEmails()
    {
        $this->emailManagement->generateEmails();
    }

    /**
     * Send emails
     *
     * @throws LocalizedException
     */
    public function sendEmails()
    {
        $this->emailManagement->sendOrHoldPendingToNowEmails();
    }
}
