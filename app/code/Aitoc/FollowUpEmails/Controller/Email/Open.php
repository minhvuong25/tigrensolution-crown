<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\Email;

use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Model\EmailRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Open extends Action
{
    const REQUESTED_PARAM_NAME_EMAIL_ID = 'id';

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var EmailRepository
     */
    private $emailsRepository;

    /**
     * Open constructor.
     *
     * @param Context $context
     * @param DateTime $date
     * @param EmailRepository $emailsRepository
     */
    public function __construct(
        Context $context,
        DateTime $date,
        EmailRepository $emailsRepository
    ) {
        $this->date = $date;
        $this->emailsRepository = $emailsRepository;
        return parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|ResultInterface|void
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $emailId = $this->getRequestedEmailId();

        if ($emailId) {
            $currentDateTime = $this->getCurrentDataTimeString();
            $email = $this->getEmailById($emailId);
            $email->setOpenedAt($currentDateTime);
            $this->saveEmail($email);
        }

        $this->showImage();
    }

    /**
     * Get requested email id
     *
     * @return mixed
     */
    private function getRequestedEmailId()
    {
        return $this->getRequestParam(self::REQUESTED_PARAM_NAME_EMAIL_ID);
    }

    /**
     * Get param
     *
     * @param string $paramName
     */
    private function getRequestParam($paramName)
    {
        $this->getRequest()->getParam($paramName);
    }

    /**
     * Get current data
     *
     * @return string
     */
    private function getCurrentDataTimeString()
    {
        return $this->date->gmtDate();
    }

    /**
     * Get email by id
     *
     * @param int $emailId
     * @return EmailInterface
     * @throws NoSuchEntityException
     */
    private function getEmailById($emailId)
    {
        return $this->emailsRepository->get($emailId);
    }

    /**
     * Save email
     *
     * @param EmailInterface $email
     * @throws CouldNotSaveException
     */
    private function saveEmail(EmailInterface $email)
    {
        $this->emailsRepository->save($email);
    }

    /**
     * Show image
     */
    private function showImage()
    {
        //todo: rewrite without usin gd (use pregenerated image)
        $img = imagecreate(1, 1);
        $color = imagecolorallocate($img, 48, 48, 48);
        imagesetpixel($img, 1, 1, $color);
        header('Content-Type: image/png');
        imagepng($img);
    }
}
