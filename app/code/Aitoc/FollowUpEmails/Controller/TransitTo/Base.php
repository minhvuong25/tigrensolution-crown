<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\TransitTo;

use Aitoc\FollowUpEmails\Api\Contoller\Account\Login\RequestParamNameInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\EmailRepositoryInterface;
use Aitoc\FollowUpEmails\Helper\WebsiteInterface as WebsiteServiceInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

abstract class Base extends Action
{
    const REQUEST_PARAM_EMAIL_ID = 'email_id';
    const REQUEST_PARAM_STORE_ID = 'store_id';
    const REQUEST_PARAM_ANCHOR = 'anchor';
    const REQUEST_PARAM_NAME_CODE = 'code';
    const REQUEST_PARAM_ID = 'id';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EmailRepositoryInterface
     */
    protected $emailsRepository;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var WebsiteServiceInterface
     */
    protected $websiteService;

    /**
     * Class constructor
     *
     * @param Context $context
     * @param EmailRepositoryInterface $emailRepository
     * @param CustomerSession $customerSession
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param DateTime $date
     * @param WebsiteServiceInterface $websiteService
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        EmailRepositoryInterface $emailRepository,
        CustomerSession $customerSession,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        CustomerRepositoryInterface $customerRepository,
        DateTime $date,
        WebsiteServiceInterface $websiteService,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);

        $this->emailsRepository = $emailRepository;
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->date = $date;
        $this->websiteService = $websiteService;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute
     *
     * @return ResponseInterface|ResultInterface
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute()
    {
        $request = $this->getRequest();
        $requestedCode = $this->getRequestedCode($request);

        if ($requestedCode) {
            $requestedEmailId = $this->getRequestedEmailId($request);

            if (!$requestedEmailId) {
                $requestedEmailId = $this->getRequest()->getParam(self::REQUEST_PARAM_ID);
            }

            $email = $this->getEmailById($requestedEmailId);

            if (!$email) {
                return $this->getResultRedirect($request);
            }

            $this->updateAndSaveEmailTransitedAt($email);

            if ($requestedCode == $email->getSecretCode()) {
                if ($this->loginCustomerIfRequired($email)) {
                    $this->afterAutoLogin($email);
                }
            }
        }

        $this->createAndSetPageCachePublicCookie();

        return $this->getResultRedirect($request);
    }

    /**
     * Get current store id
     *
     * @return int
     * @throws NoSuchEntityException
     */
    protected function getCurrentStoreId()
    {
        $store = $this->storeManager->getStore();

        return $store->getId();
    }

    /**
     * Get requested code
     *
     * @param RequestInterface $request
     * @return string
     */
    protected function getRequestedCode(RequestInterface $request)
    {
        return $request->getParam(self::REQUEST_PARAM_NAME_CODE);
    }

    /**
     * Get requested email id
     *
     * @param RequestInterface $request
     * @return int
     */
    protected function getRequestedEmailId(RequestInterface $request)
    {
        return $request->getParam(RequestParamNameInterface::EMAIL_ID);
    }

    /**
     * Get email by id
     *
     * @param int $emailId
     * @return EmailInterface
     */
    private function getEmailById($emailId)
    {
        return $this->emailsRepository->get($emailId);
    }

    /**
     * Update and save email transited at
     *
     * @param EmailInterface $email
     */
    private function updateAndSaveEmailTransitedAt(EmailInterface $email)
    {
        $currentDateTimeString = $this->getCurrentDateTimeString();

        $email->setTransitedAt($currentDateTimeString);
        $this->saveEmail($email);
    }

    /**
     * Get current datetime
     *
     * @return string
     */
    private function getCurrentDateTimeString()
    {
        return $this->date->gmtDate();
    }

    /**
     * Save email
     *
     * @param EmailInterface $email
     */
    private function saveEmail(EmailInterface $email)
    {
        $this->emailsRepository->save($email);
    }

    /**
     * Get requested store id
     *
     * @param RequestInterface $request
     * @return int|null
     */
    protected function getRequestedStoreId(RequestInterface $request)
    {
        return $request->getParam(self::REQUEST_PARAM_STORE_ID);
    }

    /**
     * Login customer if required
     *
     * @param EmailInterface $email
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function loginCustomerIfRequired(EmailInterface $email)
    {
        if ($this->isCustomerLoggedIn()) {
            return false;
        }

        $storeId = $this->getCurrentStoreId();
        $customerId = $this->getCustomerIdByEmailAndStoreId($email, $storeId);

        if (!$customerId) {
            return false;
        }

        $this->loginCustomerById($customerId);

        return true;
    }

    /**
     * Is customer logged in
     *
     * @return bool
     */
    private function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get customer id
     *
     * @param EmailInterface $email
     * @param int $storeId
     * @return int|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getCustomerIdByEmailAndStoreId(EmailInterface $email, $storeId = Store::DEFAULT_STORE_ID)
    {
        $websiteId = $this->getWebsiteIdByStoreId($storeId);
        $customerEmailAddress = $email->getEmailAddress();

        $customer = $this->getCustomerByEmailAddressAndWebsiteId($customerEmailAddress, $websiteId);

        return $customer ? $customer->getId() : null;
    }

    /**
     * Get website id by store id
     *
     * @param int $storeId
     * @return int
     * @throws NoSuchEntityException
     */
    private function getWebsiteIdByStoreId($storeId)
    {
        return $this->websiteService->getWebsiteIdByStoreId($storeId);
    }

    /**
     * Get customer by email address and website id
     *
     * @param string $customerEmail
     * @param int $websiteId
     * @return CustomerInterface
     * @throws LocalizedException
     */
    private function getCustomerByEmailAddressAndWebsiteId($customerEmail, $websiteId)
    {
        try {
            return $this->customerRepository->get($customerEmail, $websiteId);
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * Login customer by id
     *
     * @param int $customerId
     */
    private function loginCustomerById($customerId)
    {
        $this->customerSession->loginById($customerId);
    }

    /**
     * After auto login
     *
     * @param EmailInterface $email
     */
    protected function afterAutoLogin(EmailInterface $email)
    {
    }

    /**
     * Create and set page cache
     *
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     */
    private function createAndSetPageCachePublicCookie()
    {
        $publicCookieMetadata = $this->createPageCachePublicCookieMetadata();

        $version = $this->generateVersion();

        $this->setPublicCookie(
            Version::COOKIE_NAME,
            $version,
            $publicCookieMetadata
        );
    }

    /**
     * Create page cache
     *
     * @return PublicCookieMetadata
     */
    private function createPageCachePublicCookieMetadata()
    {
        return $this->createPublicCookieMetadata()
            ->setDuration(Version::COOKIE_PERIOD)
            ->setPath('/')
            ->setHttpOnly(false);
    }

    /**
     * Create public cookie meta data
     *
     * @return PublicCookieMetadata
     */
    private function createPublicCookieMetadata()
    {
        return $this->cookieMetadataFactory->createPublicCookieMetadata();
    }

    /**
     * Generate version
     *
     * @return string
     */
    private function generateVersion()
    {
        return hash('SHA256', rand() . time());
    }

    /**
     * Set public cookie
     *
     * @param string $name
     * @param mixed $value
     * @param PublicCookieMetadata|null $metadata
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws InputException
     */
    private function setPublicCookie($name, $value, PublicCookieMetadata $metadata = null)
    {
        $this->cookieManager->setPublicCookie($name, $value, $metadata);
    }

    /**
     * Get result redirect
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    abstract protected function getResultRedirect(RequestInterface $request);

    /**
     * Get requested anchor
     *
     * @param RequestInterface $request
     * @return string|null
     */
    protected function getRequestedAnchor(RequestInterface $request)
    {
        return $request->getParam(RequestParamNameInterface::ANCHOR);
    }

    /**
     * Add anchor to url
     *
     * @param string $url
     * @param string $anchor
     * @return string
     */
    protected function addAnchorToUrl($url, $anchor)
    {
        return "{$url}#{$anchor}";
    }
}
