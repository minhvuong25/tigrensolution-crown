<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Controller\Adminhtml\Email;

use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Email\StatusInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailTableInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\Email\Collection;
use Aitoc\FollowUpEmails\Model\ResourceModel\Email\CollectionFactory;
use Aitoc\FollowUpEmails\Service\EmailManagement;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;

class MassSend extends Action
{
    const SUCCESS_MESSAGE = 'A total of %1 record(s) have been sent.';
    const FUE_INDEX_PAGE_ROUTE_PATH = 'followup/*/index';

    /**
     * Massactions filter
     *
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var EmailManagement
     */
    private $emailManagement;

    /**
     * MassSend constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param EmailManagement $emailManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        EmailManagement $emailManagement
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->emailManagement = $emailManagement;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $emails = $this->getRequestedEmails();
        $emailStatusStatistic = $this->sendOrHoldEmails($emails);
        $sentCount = $emailStatusStatistic[StatusInterface::STATUS_SENT];
        $this->addSentSuccessMessage($sentCount);

        return $this->createRedirectToIndexPage();
    }

    /**
     * Get requested emails
     *
     * @return DataObject[]|EmailInterface[]
     * @throws LocalizedException
     */
    private function getRequestedEmails()
    {
        $emailCollection = $this->createEmailCollection();
        $emailCollection = $this->filter->getCollection($emailCollection);
        $emailCollection->addFieldToFilter(
            EmailTableInterface::COLUMN_NAME_STATUS,
            StatusInterface::STATUS_PENDING
        );

        return $emailCollection->getItems();
    }

    /**
     * Create email collection
     *
     * @return Collection
     */
    private function createEmailCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * Send or hold emails
     *
     * @param EmailInterface[] $emails
     * @return array
     * @throws LocalizedException
     */
    private function sendOrHoldEmails($emails)
    {
        return $this->emailManagement->sendOrHoldOrSkipEmails($emails);
    }

    /**
     * Add sent success message
     *
     * @param int $sentCount
     */
    private function addSentSuccessMessage($sentCount)
    {
        $successMessage = $this->getSuccessMessage($sentCount);
        $this->addSuccessMessage($successMessage);
    }

    /**
     * Get success message
     *
     * @param int $sentCount
     * @return Phrase
     */
    private function getSuccessMessage($sentCount)
    {
        return __(self::SUCCESS_MESSAGE, $sentCount);
    }

    /**
     * Add success message
     *
     * @param Phrase $successMessage
     */
    private function addSuccessMessage(Phrase $successMessage)
    {
        $this->messageManager->addSuccessMessage($successMessage);
    }

    /**
     * Create redirect to index
     *
     * @return ResultInterface
     */
    private function createRedirectToIndexPage()
    {
        $redirect = $this->createRedirect();
        $redirect->setPath(self::FUE_INDEX_PAGE_ROUTE_PATH);

        return $redirect;
    }

    /**
     * Create redirect
     *
     * @return ResultInterface
     */
    private function createRedirect()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    }
}
