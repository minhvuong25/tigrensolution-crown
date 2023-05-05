<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Service;

use Aitoc\FollowUpEmails\Api\CampaignRepositoryInterface;
use Aitoc\FollowUpEmails\Api\CampaignStepRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailAttributeInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailAttributeSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailInterfaceFactory;
use Aitoc\FollowUpEmails\Api\Data\EmailSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Campaign\StatusInterface as CampaignStatusInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\CampaignStep\StatusInterface as CampaignStepStatusInterface;
use Aitoc\FollowUpEmails\Api\Data\Source\Email\StatusInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\EmailAttributeRepositoryInterface;
use Aitoc\FollowUpEmails\Api\EmailRepositoryInterface;
use Aitoc\FollowUpEmails\Api\EventManagementInterface;
use Aitoc\FollowUpEmails\Api\Helper\EmailInterface as EmailHelperInterface;
use Aitoc\FollowUpEmails\Api\Helper\EventEmailsGeneratorHelperInterface;
use Aitoc\FollowUpEmails\Api\Helper\SearchCriteriaBuilderInterface;
use Aitoc\FollowUpEmails\Api\Service\CampaignStepInterface as CampaignStepServiceInterface;
use Aitoc\FollowUpEmails\Api\Service\CustomerGroupInterface as CustomerGroupServiceInterface;
use Aitoc\FollowUpEmails\Api\Service\Email\UnsubscribeCodeInterface as UnsubscribeCodeServiceInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignStepTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailAttributeTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\UnsubscribedEmailAddressTableInterface;
use Aitoc\FollowUpEmails\Api\UnsubscribedEmailAddressRepositoryInterface;
use Aitoc\FollowUpEmails\Helper\WebsiteInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\Area;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\SalesRule\Api\Data\CouponInterfaceFactory;
use Magento\SalesRule\Api\Data\CouponSearchResultInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\Data\RuleInterfaceFactory;
use Magento\SalesRule\Helper\Coupon as CouponHelper;
use Magento\SalesRule\Model\Coupon\Massgenerator;
use Magento\SalesRule\Model\RuleRepository;
use Aitoc\FollowUpEmails\Ui\Component\Listing\Column\DiscountTypeOptions;
use Magento\Directory\Model\CurrencyFactory;

class EmailManagement extends AbstractModel
{
    const CART_PRICE_RULE_NAME = 'Individual sales rule for %s';
    const CART_PRICE_RULE_COUPON_CODE_LENGTH = 12;

    const COUPON_MASS_GENERATOR_DATA_KEY_LENGTH = 'length';

    const ATTRIBUTE_CODE_RULE_ID = 'rule_id';

    const AITOC_FOLLOW_UP_IDENTIFIER = 'is_aitfollowup';

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var EmailInterfaceFactory
     */
    private $emailFactory;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignsRepository;

    /**
     * @var CampaignStepRepositoryInterface
     */
    private $campaignStepsRepository;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var EventManagementInterface
     */
    private $eventManagement;

    /**
     * @var EmailRepositoryInterface
     */
    private $emailsRepository;

    /**
     * @var UnsubscribedEmailAddressRepositoryInterface
     */
    private $unsubscribedListRepository;

    /**
     * @var EmailAttributeRepositoryInterface
     */
    private $emailAttributesRepository;

    /**
     * @var Massgenerator
     */
    private $couponMassGenerator;

    /**
     * @var CouponInterfaceFactory
     */
    private $couponFactory;

    /**
     * @var CouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleFactory;

    /**
     * @var SearchCriteriaBuilderInterface
     */
    private $searchCriteriaBuilderHelper;

    /**
     * @var WebsiteInterface
     */
    private $websiteHelper;

    /**
     * @var EmailHelperInterface
     */
    private $emailHelper;

    /**
     * @var CustomerGroupServiceInterface
     */
    private $customerGroupService;

    /**
     * @var UnsubscribeCodeServiceInterface
     */
    private $unsubscribeCodeService;

    /**
     * @var CampaignStepServiceInterface
     */
    private $campaignStepService;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    /**
     * EmailManagement constructor.
     *
     * @param Context $context
     * @param EventManagementInterface $eventManagement
     * @param CampaignRepositoryInterface $campaignsRepository
     * @param CampaignStepRepositoryInterface $campaignStepsRepository
     * @param EmailInterfaceFactory $emailFactory
     * @param EmailRepositoryInterface $emailsRepository
     * @param EmailAttributeRepositoryInterface $emailAttributesRepository
     * @param Registry $registry
     * @param DateTime $date
     * @param TransportBuilder $transportBuilder
     * @param SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper
     * @param UnsubscribedEmailAddressRepositoryInterface $unsubscribedListRepository
     * @param Massgenerator $couponMassGenerator
     * @param CouponInterfaceFactory $couponFactory
     * @param CouponRepositoryInterface $couponRepository
     * @param RuleInterfaceFactory $ruleFactory
     * @param RuleRepository $ruleRepository
     * @param WebsiteInterface $websiteHelper
     * @param EmailHelperInterface $emailHelper
     * @param CustomerGroupServiceInterface $customerGroupService
     * @param UnsubscribeCodeServiceInterface $unsubscribeCodeService
     * @param CampaignStepServiceInterface $campaignStepService
     * @param CurrencyFactory $currencyFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        EventManagementInterface $eventManagement,
        CampaignRepositoryInterface $campaignsRepository,
        CampaignStepRepositoryInterface $campaignStepsRepository,
        EmailInterfaceFactory $emailFactory,
        EmailRepositoryInterface $emailsRepository,
        EmailAttributeRepositoryInterface $emailAttributesRepository,
        Registry $registry,
        DateTime $date,
        TransportBuilder $transportBuilder,
        SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper,
        UnsubscribedEmailAddressRepositoryInterface $unsubscribedListRepository,
        Massgenerator $couponMassGenerator,
        CouponInterfaceFactory $couponFactory,
        CouponRepositoryInterface $couponRepository,
        RuleInterfaceFactory $ruleFactory,
        RuleRepository $ruleRepository,
        WebsiteInterface $websiteHelper,
        EmailHelperInterface $emailHelper,
        CustomerGroupServiceInterface $customerGroupService,
        UnsubscribeCodeServiceInterface $unsubscribeCodeService,
        CampaignStepServiceInterface $campaignStepService,
        CurrencyFactory $currencyFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->date = $date;
        $this->emailFactory = $emailFactory;
        $this->campaignsRepository = $campaignsRepository;
        $this->campaignStepsRepository = $campaignStepsRepository;
        $this->transportBuilder = $transportBuilder;
        $this->searchCriteriaBuilderHelper = $searchCriteriaBuilderHelper;
        $this->eventManagement = $eventManagement;
        $this->emailsRepository = $emailsRepository;
        $this->unsubscribedListRepository = $unsubscribedListRepository;
        $this->emailAttributesRepository = $emailAttributesRepository;
        $this->couponMassGenerator = $couponMassGenerator;
        $this->couponFactory = $couponFactory;
        $this->couponRepository = $couponRepository;
        $this->ruleRepository = $ruleRepository;
        $this->websiteHelper = $websiteHelper;
        $this->ruleFactory = $ruleFactory;
        $this->emailHelper = $emailHelper;
        $this->customerGroupService = $customerGroupService;
        $this->unsubscribeCodeService = $unsubscribeCodeService;
        $this->campaignStepService = $campaignStepService;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * Prepare emails for generate
     *
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function generateEmails()
    {
        $eventCodes = $this->getActiveEventsCodes();
        $campaigns = $this->getEnabledCampaignsByEventsCodes($eventCodes);

        $this->generateCampaignsEmails($campaigns);
    }

    /**
     * Get active events codes
     *
     * @return array
     */
    private function getActiveEventsCodes()
    {
        return $this->eventManagement->getActiveEventsCodes();
    }

    /**
     * Get enabled campaigns by event codes
     *
     * @param $eventsCodes
     * @return CampaignInterface[]
     */
    private function getEnabledCampaignsByEventsCodes($eventsCodes)
    {
        $filters = [
            [CampaignTableInterface::COLUMN_NAME_STATUS, CampaignStatusInterface::ENABLED],
            [CampaignTableInterface::COLUMN_NAME_EVENT_CODE, $eventsCodes, 'in']
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        return $this->getCampaignsBySearchCriteria($searchCriteria);
    }

    /**
     * Create search criteria
     *
     * @param array $filters
     * @param array $sortOrders
     * @return SearchCriteria
     */
    private function createSearchCriteria($filters = [], $sortOrders = [])
    {
        return $this->searchCriteriaBuilderHelper->createSearchCriteria($filters, $sortOrders);
    }

    /**
     * Get campains by search criteria
     *
     * @param SearchCriteria $searchCriteria
     * @return CampaignInterface[]
     */
    private function getCampaignsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $campaignSearchResult = $this->getCampaignSearchResultBySearchCriteria($searchCriteria);

        return $campaignSearchResult->getItems();
    }

    /**
     * Get campaign search result by search criteria
     *
     * @param SearchCriteria $searchCriteria
     * @return CampaignSearchResultsInterface
     */
    private function getCampaignSearchResultBySearchCriteria(SearchCriteria $searchCriteria)
    {
        return $this->campaignsRepository->getList($searchCriteria);
    }

    /**
     * Generate campaign emails
     *
     * @param $campaigns
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateCampaignsEmails($campaigns)
    {
        foreach ($campaigns as $campaign) {
            $this->generateCampaignEmails($campaign);
        }
    }

    /**
     * Generate campaign emails
     *
     * @param CampaignInterface $campaign
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateCampaignEmails(CampaignInterface $campaign)
    {
        $eventCode = $campaign->getEventCode();
        $eventEmailGeneratorHelper = $this->getEventEmailsGeneratorHelperByEventCode($eventCode);

        if (!$eventEmailGeneratorHelper) {
            return;
        }

        $campaignId = $campaign->getEntityId();
        $campaignSteps = $this->getEnabledCampaignStepsByCampaignId($campaignId);

        $this->generateCampaignStepsEmails($eventEmailGeneratorHelper, $campaign, $campaignSteps);
    }

    /**
     * Get event emails generator
     *
     * @param string $eventCode
     * @return EventEmailsGeneratorHelperInterface
     */
    private function getEventEmailsGeneratorHelperByEventCode($eventCode)
    {
        return $this->eventManagement->getEventEmailGeneratorHelperByEventCode($eventCode);
    }

    /**
     * Get enabled campaign steps by campaign id
     *
     * @param int $campaignId
     * @return CampaignStepInterface[]
     */
    private function getEnabledCampaignStepsByCampaignId($campaignId)
    {
        $filters = [
            [CampaignStepTableInterface::COLUMN_NAME_CAMPAIGN_ID, $campaignId],
            [CampaignStepTableInterface::COLUMN_NAME_STATUS, CampaignStepStatusInterface::ENABLED]
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        return $this->getCampaignStepsBySearchCriteria($searchCriteria);
    }

    /**
     * Get campaign steps by search criteria
     *
     * @param SearchCriteria $searchCriteria
     * @return CampaignStepInterface[]
     */
    private function getCampaignStepsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $campaignStepSearchResults = $this->getCampaignStepSearchResultsBySearchCriteria($searchCriteria);

        return $campaignStepSearchResults->getItems();
    }

    /**
     * Get campaign step search result by search criteria
     *
     * @param SearchCriteria $searchCriteria
     * @return CampaignStepSearchResultsInterface
     */
    private function getCampaignStepSearchResultsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        return $this->campaignStepsRepository->getList($searchCriteria);
    }

    /**
     * Generate campaign steps emails
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface[] $campaignSteps
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateCampaignStepsEmails(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        $campaignSteps
    ) {
        foreach ($campaignSteps as $campaignStep) {
            $this->generateCampaignStepEmails($eventEmailsGeneratorHelper, $campaign, $campaignStep);
        }
    }

    /**
     * Generated campaign step emails
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateCampaignStepEmails(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep
    ) {
        $unprocessedEntities = $this->getUnprocessedEntities(
            $eventEmailsGeneratorHelper,
            $campaign,
            $campaignStep
        );

        if (!$unprocessedEntities) {
            return;
        }

        $this->generateEntitiesEmails(
            $eventEmailsGeneratorHelper,
            $campaign,
            $campaignStep,
            $unprocessedEntities
        );
    }

    /**
     * Get unprocessed entities
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGenerationHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @return OrderInterface[]
     */
    private function getUnprocessedEntities(
        EventEmailsGeneratorHelperInterface $eventEmailsGenerationHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep
    ) {
        $campaignStepId = $campaignStep->getEntityId();
        $processedEntitiesIds = $this->getProcessedEntitiesIds($campaignStepId, $eventEmailsGenerationHelper);

        return $eventEmailsGenerationHelper->getUnprocessedEntities(
            $campaign,
            $campaignStep,
            $processedEntitiesIds
        );
    }

    /**
     * Get processed entities ids
     *
     * @param int $campaignStepId
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @return array
     */
    private function getProcessedEntitiesIds($campaignStepId, $eventEmailsGeneratorHelper)
    {
        $mainEntityAttributeCode = $eventEmailsGeneratorHelper->getEntityIdAttributeCode();

        return $this->getEmailsAttributeValuesByCampaignStepId($campaignStepId, $mainEntityAttributeCode);
    }

    /**
     * Get emails attribute values by campaign step id
     *
     * @param int $campaignStepId
     * @param string $mainEntityAttributeCode
     * @return array
     */
    private function getEmailsAttributeValuesByCampaignStepId($campaignStepId, $mainEntityAttributeCode)
    {
        $emails = $this->getEmailsByCampaignStepId($campaignStepId);

        return $this->getUniqueEmailsAttributeValuesByEmails($emails, $mainEntityAttributeCode);
    }

    /**
     * Get emails by campaign step id
     *
     * @param int $campaignStepId
     * @return EmailInterface[]
     */
    private function getEmailsByCampaignStepId($campaignStepId)
    {
        $filters = [
            [EmailTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID, $campaignStepId]
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $emailsList = $this->getEmailSearchResult($searchCriteria);

        return $emailsList->getItems();
    }

    /**
     * Get email search result
     *
     * @param SearchCriteria $searchCriteria
     * @return EmailSearchResultsInterface
     */
    private function getEmailSearchResult(SearchCriteria $searchCriteria)
    {
        return $this->emailsRepository->getList($searchCriteria);
    }

    /**
     * Get unique email attribute values
     *
     * @param EmailInterface[] $emails
     * @param string $attributeCode
     * @return array
     */
    private function getUniqueEmailsAttributeValuesByEmails($emails, $attributeCode)
    {
        $processedAttributeIds = [];

        foreach ($emails as $email) {
            $processedAttributeIds[] = $this->getEmailAttributeValue($email, $attributeCode);
        }

        return array_unique($processedAttributeIds);
    }

    /**
     * Get email attribute values
     *
     * @param EmailInterface $emailsItem
     * @param string $attributeCode
     * @return int|string
     */
    private function getEmailAttributeValue(EmailInterface $emailsItem, $attributeCode)
    {
        $emailId = $emailsItem->getEntityId();
        $emailAttribute = $this->getEmailAttributeByEmailIdAndAttributeCode($emailId, $attributeCode);

        return $emailAttribute->getValue();
    }

    /**
     * Get email attribute by email id and attribute code
     *
     * @param int $emailId
     * @param string $attributeCode
     * @return EmailAttributeInterface|bool
     */
    private function getEmailAttributeByEmailIdAndAttributeCode($emailId, $attributeCode)
    {
        $filters = [
            [EmailAttributeTableInterface::COLUMN_NAME_EMAIL_ID, $emailId],
            [EmailAttributeTableInterface::COLUMN_NAME_ATTRIBUTE_CODE, $attributeCode]
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $emailAttributeList = $this->getEmailAttributeSearchResult($searchCriteria);
        $emailAttributes = $emailAttributeList->getItems();

        return reset($emailAttributes);
    }

    /**
     * Get email attribute search result
     *
     * @param SearchCriteria $searchCriteria
     * @return EmailAttributeSearchResultsInterface
     */
    private function getEmailAttributeSearchResult(SearchCriteria $searchCriteria)
    {
        return $this->emailAttributesRepository->getList($searchCriteria);
    }

    /**
     * Generate entities emails
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param $unprocessedEntities
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateEntitiesEmails(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        $unprocessedEntities
    ) {
        foreach ($unprocessedEntities as $entity) {
            $this->generateEntityEmails($eventEmailsGeneratorHelper, $campaign, $campaignStep, $entity);
        }
    }

    /**
     * Generate entity emails
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param mixed $entity
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateEntityEmails(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        $entity
    ) {
        $baseEmail = $this->createEmail();

        $this->updateEmailForBase($baseEmail, $eventEmailsGeneratorHelper, $campaignStep, $entity);

        $this->updateAndSaveCustomerEmailIfRequired(
            $eventEmailsGeneratorHelper,
            $campaign,
            $campaignStep,
            $entity,
            $baseEmail
        );
        $this->updateAndSaveAdditionalEmailsIfRequired($eventEmailsGeneratorHelper, $campaignStep, $baseEmail);
    }

    /**
     * Create email
     *
     * @return EmailInterface
     */
    private function createEmail()
    {
        return $this->emailFactory->create();
    }

    /**
     * Update email for base
     *
     * @param EmailInterface $email
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignStepInterface $campaignStep
     * @param mixed $entity
     * @return void
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function updateEmailForBase(
        EmailInterface $email,
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignStepInterface $campaignStep,
        $entity
    ) {
        $customerEmail = $eventEmailsGeneratorHelper->getCustomerEmailByEntity($entity);
        $campaignStepId = $campaignStep->getEntityId();
        $scheduleAt = $this->getScheduleAtDatetimeStringByContext($eventEmailsGeneratorHelper, $campaignStep, $entity);

        $emailAttributesData = $this->getEmailAttributesData(
            $eventEmailsGeneratorHelper,
            $campaignStep,
            $entity
        );

        $email
            ->setCustomerEmail($customerEmail)
            ->setCampaignStepId($campaignStepId)
            ->setScheduledAt($scheduleAt)
            ->setStatus(StatusInterface::STATUS_PENDING)
            ->setEmailAttributes($emailAttributesData)
        ;
    }

    /**
     * Get schedule at datetime
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignStepInterface $campaignStep
     * @param mixed $entity
     * @return int|null
     */
    protected function getScheduleAtDatetimeStringByContext(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignStepInterface $campaignStep,
        $entity
    ) {
        $eventTimestamp = $eventEmailsGeneratorHelper->getEventTimestampByEntity($entity);

        if ($eventTimestamp === null) {
            return null;
        }

        $campaignStepsDelayPeriod = $campaignStep->getDelayPeriod();
        $unit = $campaignStep->getDelayUnit();

        return $this->getScheduleAtDatetimeString($eventTimestamp, $campaignStepsDelayPeriod, $unit);
    }

    /**
     * Get schedule at datetime
     *
     * @param string $eventTimestamp
     * @param int $campaignStepsDelayPeriod
     * @param string $unit
     * @return int
     */
    private function getScheduleAtDatetimeString($eventTimestamp, $campaignStepsDelayPeriod, $unit)
    {
        $eventDateTimeString = date('Y-m-d H:i:s', $eventTimestamp);
        $scheduleAtTimestamp = strtotime("{$eventDateTimeString} + {$campaignStepsDelayPeriod} {$unit}");

        return $scheduleAtTimestamp;
    }

    /**
     * Get email attribute data
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignStepInterface $campaignStep
     * @param mixed $entity
     * @return mixed
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getEmailAttributesData(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignStepInterface $campaignStep,
        $entity
    ) {
        $emailAttributesData = $eventEmailsGeneratorHelper->getEmailAttributesByEntity($entity);

        $cartPriceRuleId = $this->generateIndividualCartPriceRuleIfRequired(
            $campaignStep,
            $eventEmailsGeneratorHelper,
            $entity
        );

        if ($cartPriceRuleId) {
            $emailAttributesData[self::ATTRIBUTE_CODE_RULE_ID] = $cartPriceRuleId;
        }

        return $emailAttributesData;
    }

    /**
     * Generate individual cart price rule
     *
     * @param CampaignStepInterface $campaignStep
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param mixed $entity
     * @return int|null
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function generateIndividualCartPriceRuleIfRequired(
        CampaignStepInterface $campaignStep,
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        $entity
    ) {
        $discountStatus = $campaignStep->getDiscountStatus();

        if (!$discountStatus) {
            return null;
        }

        $discountType = $campaignStep->getDiscountType();

        if ($discountType == DiscountTypeOptions::ACTION_TYPE_RULE_OPTION) {
            return $campaignStep->getSalesRuleId();
        } else {
            $customerEmail = $eventEmailsGeneratorHelper->getCustomerEmailByEntity($entity);
            $websiteId = $this->getWebsiteIdByEntity($eventEmailsGeneratorHelper, $entity);
            $discountAmount = $campaignStep->getDiscountPercent();
            $discountPeriod = $campaignStep->getDiscountPeriod();

            $rule = $this->createAndSaveIndividualRuleWithCouponCode(
                $customerEmail,
                $websiteId,
                $discountAmount,
                $discountPeriod,
                $discountType
            );

            return $rule->getRuleId();
        }
    }

    /**
     * Get website id by entity
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param mixed $entity
     * @return int
     * @throws NoSuchEntityException
     */
    private function getWebsiteIdByEntity(EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper, $entity)
    {
        $storeId = $eventEmailsGeneratorHelper->getStoreIdByEntity($entity);

        return $this->getWebsiteIdByStoreId($storeId);
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
        return $this->websiteHelper->getWebsiteIdByStoreId($storeId);
    }

    /**
     * Create and save individual rule with coupon code
     *
     * @param $customerEmail
     * @param $websiteId
     * @param $discountPercent
     * @param $discountPeriod
     * @param $discountType
     * @return RuleInterface
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function createAndSaveIndividualRuleWithCouponCode(
        $customerEmail,
        $websiteId,
        $discountAmount,
        $discountPeriod,
        $discountType
    ) {
        $toDate = $this->getDiscountToDataString($discountPeriod);

        $rule = $this->createAndSaveCartPriceRule($customerEmail, $websiteId, $discountAmount, $toDate, $discountType);
        $ruleId = $rule->getRuleId();

        $this->createAndSaveCartPriceRuleCoupon($ruleId);

        return $rule;
    }

    /**
     * Get discount data
     *
     * @param int $discountPeriodInDays
     * @return false|string
     */
    private function getDiscountToDataString($discountPeriodInDays)
    {
        $currentDateTime = $this->getCurrentDateTimeAsString();
        $discountToDataTimestamp = strtotime("{$currentDateTime} + {$discountPeriodInDays} days");

        return date('Y-m-d', $discountToDataTimestamp);
    }

    /**
     * Get current datetime
     *
     * @return string
     */
    private function getCurrentDateTimeAsString()
    {
        return $this->date->gmtDate();
    }

    /**
     * Create and save cart price rule
     *
     * @param $customerEmail
     * @param $websiteId
     * @param $discountAmount
     * @param $toDate
     * @param $discountType
     * @return RuleInterface
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function createAndSaveCartPriceRule($customerEmail, $websiteId, $discountAmount, $toDate, $discountType)
    {
        $cartPriceRule = $this->createCartPriceRule();
        $this->updateCartPriceRuleByContext(
            $cartPriceRule,
            $customerEmail,
            $websiteId,
            $discountAmount,
            $toDate,
            $discountType
        );

        return $this->saveCartPriceRule($cartPriceRule);
    }

    /**
     * Create cart price rule
     *
     * @return RuleInterface
     */
    private function createCartPriceRule()
    {
        return $this->ruleFactory->create();
    }

    /**
     * Update cart price rule by context
     *
     * @param RuleInterface $ruleModel
     * @param $customerEmail
     * @param $websiteId
     * @param $discountAmount
     * @param string $toDate
     * @param $discountType
     */
    private function updateCartPriceRuleByContext(
        RuleInterface $ruleModel,
        $customerEmail,
        $websiteId,
        $discountAmount,
        $toDate,
        $discountType
    ) {
        $cartPriceRuleName = $this->getCartPriceRuleNameByCustomerEmail($customerEmail);

        $ruleModel
            ->setName($cartPriceRuleName)
            ->setSimpleAction(
                $discountType == DiscountTypeOptions::ACTION_TYPE_PERCENT_OPTION
                    ? RuleInterface::DISCOUNT_ACTION_BY_PERCENT : RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT
            )
            ->setDiscountAmount($discountAmount)
            ->setUsesPerCustomer(1)
            ->setUsesPerCoupon(1)
            ->setCouponType(RuleInterface::COUPON_TYPE_SPECIFIC_COUPON)
            ->setUseAutoGeneration(1)
            ->setWebsiteIds([$websiteId])
            ->setCustomerGroupIds($this->getCustomerGroupsIds())
            ->setToDate($toDate)
            ->setIsActive(true);
    }

    /**
     * Get cart price rule name by customer email
     *
     * @param string $customerEmail
     * @return string
     */
    private function getCartPriceRuleNameByCustomerEmail($customerEmail)
    {
        return sprintf(self::CART_PRICE_RULE_NAME, $customerEmail);
    }

    /**
     * Get customer groups ids
     *
     * @return array
     */
    private function getCustomerGroupsIds()
    {
        return $this->customerGroupService->getCustomerGroupsIds();
    }

    /**
     * Save cart price rule
     *
     * @param $cartPriceRule
     * @return RuleInterface
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function saveCartPriceRule($cartPriceRule)
    {
        return $this->ruleRepository->save($cartPriceRule);
    }

    /**
     * Create and save cart price rule coupon
     *
     * @param int $ruleId
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function createAndSaveCartPriceRuleCoupon($ruleId)
    {
        $coupon = $this->createCartPriceRuleCoupon();
        $this->updateCartPriceRuleCouponByContext($coupon, $ruleId);
        $this->saveCartPriceRuleCoupon($coupon);
    }

    /**
     * Create cart price rule coupon
     *
     * @return CouponInterface
     */
    private function createCartPriceRuleCoupon()
    {
        return $this->couponFactory->create();
    }

    /**
     * Update cart price rule coupon by context
     *
     * @param CouponInterface $coupon
     * @param int $ruleId
     */
    private function updateCartPriceRuleCouponByContext(CouponInterface $coupon, $ruleId)
    {
        $code = $this->generateCartPriceRuleCouponCode();

        $coupon
            ->setRuleId($ruleId)
            ->setCode($code)
            ->setUsageLimit(1)
            ->setUsagePerCustomer(1)
            ->setIsPrimary(false)
            ->setType(CouponHelper::COUPON_TYPE_SPECIFIC_AUTOGENERATED);
    }

    /**
     * Generate cart price rule coupon code
     *
     * @return string
     */
    private function generateCartPriceRuleCouponCode()
    {
        return $this->couponMassGenerator
            ->setData(self::COUPON_MASS_GENERATOR_DATA_KEY_LENGTH, static::CART_PRICE_RULE_COUPON_CODE_LENGTH)
            ->generateCode();
    }

    /**
     * Save cart price rule coupon
     *
     * @param CouponInterface $coupon
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws InputException
     */
    private function saveCartPriceRuleCoupon(CouponInterface $coupon)
    {
        $this->couponRepository->save($coupon);
    }

    /**
     * Update and save customer email if required
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param mixed $entity
     * @param EmailInterface $baseEmail
     */
    private function updateAndSaveCustomerEmailIfRequired(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        $entity,
        EmailInterface $baseEmail
    ) {
        if (!$this->isSendEmailToCustomerRequired($eventEmailsGeneratorHelper, $campaign, $campaignStep, $entity)) {
            return;
        }

        $email = clone $baseEmail;
        $this->updateEmailForCustomer($email);

        $this->saveEmail($email);
    }

    /**
     * Is send email to customer required
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param mixed $entity
     * @return bool
     */
    private function isSendEmailToCustomerRequired(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        $entity
    ) {
        if (!$eventEmailsGeneratorHelper->isSendEmailToCustomerRequired($campaignStep)) {
            return false;
        }

        $customerEmail = $eventEmailsGeneratorHelper->getCustomerEmailByEntity($entity);
        $eventCode = $campaign->getEventCode();

        return $this->isCustomerSubscribedToEvent($customerEmail, $eventCode);
    }

    /**
     * Is customer subscribed to event
     *
     * @param string $customerEmail
     * @param string $eventCode
     * @return bool
     */
    private function isCustomerSubscribedToEvent($customerEmail, $eventCode)
    {
        $filters = [
            [UnsubscribedEmailAddressTableInterface::COLUMN_NAME_EVENT_CODE, $eventCode],
            [UnsubscribedEmailAddressTableInterface::COLUMN_NAME_CUSTOMER_EMAIL, $customerEmail],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        $unsubscribedList = $this->getUnsubscribedListSearchResult($searchCriteria);

        return !$unsubscribedList->getTotalCount();
    }

    /**
     * Get unsubscribed list search result
     *
     * @param SearchCriteria $searchCriteria
     * @return UnsubscribedEmailAddressSearchResultsInterface
     */
    private function getUnsubscribedListSearchResult(SearchCriteria $searchCriteria)
    {
        return $this->unsubscribedListRepository->getList($searchCriteria);
    }

    /**
     * Update email for customer
     *
     * @param EmailInterface $email
     */
    private function updateEmailForCustomer(
        EmailInterface $email
    ) {
        $unsubscribeCode = $this->generateUnsubscribeCode();

        $email
            ->setSecretCode($unsubscribeCode)
        ;
    }

    /**
     * Generate unsubscribe code
     *
     * @return string
     */
    private function generateUnsubscribeCode()
    {
        return $this->unsubscribeCodeService->generateUnsubscribeCode();
    }

    /**
     * Save email
     *
     * @param EmailInterface $email
     * @return EmailInterface
     */
    private function saveEmail(EmailInterface $email)
    {
        return $this->emailsRepository->save($email);
    }

    /**
     * Update and save additional emails if required
     *
     * @param EventEmailsGeneratorHelperInterface $object
     * @param CampaignStepInterface $campaignStep
     * @param EmailInterface $baseEmail
     */
    private function updateAndSaveAdditionalEmailsIfRequired(
        EventEmailsGeneratorHelperInterface $object,
        CampaignStepInterface $campaignStep,
        EmailInterface $baseEmail
    ) {
        $emailAddresses = $object->getAdditionalEmailAddresses($campaignStep);

        if (!$emailAddresses) {
            return;
        }

        foreach ($emailAddresses as $email) {
            $this->updateAndSaveAdditionalEmail($email, $baseEmail);
        }
    }

    /**
     * Update and save additional email
     *
     * @param string $emailAddress
     * @param EmailInterface $baseEmail
     * @return EmailInterface
     */
    private function updateAndSaveAdditionalEmail($emailAddress, EmailInterface $baseEmail)
    {
        $email = clone $baseEmail;
        $email->setCustomerEmail($emailAddress);

        return $this->saveEmail($email);
    }

    /**
     * Prepare emails for send
     *
     * @throws LocalizedException
     */
    public function sendOrHoldPendingToNowEmails()
    {
        $pendingToNowEmails = $this->getPendingToNowEmails();

        if (!$pendingToNowEmails) {
            return;
        }

        $this->sendOrHoldOrSkipEmails($pendingToNowEmails);
    }

    /**
     * Get pending to now emails
     *
     * @return EmailInterface[]
     */
    private function getPendingToNowEmails()
    {
        $currentDateTime = $this->getCurrentDateTimeAsString();
        $enabledCampaignStepsIds = $this->getEnabledCampaignStepsIds();

        $filters = [
            [
                [EmailTableInterface::COLUMN_NAME_SCHEDULED_AT, $currentDateTime, 'lteq'],
                [EmailTableInterface::COLUMN_NAME_SCHEDULED_AT, true, 'null']
            ],
            [EmailTableInterface::COLUMN_NAME_STATUS, StatusInterface::STATUS_PENDING],
            [EmailTableInterface::COLUMN_NAME_CAMPAIGN_STEP_ID, $enabledCampaignStepsIds, 'in']
        ];

        $sortOrders = [
            EmailTableInterface::COLUMN_NAME_SCHEDULED_AT => SortOrder::SORT_ASC
        ];

        $searchCriteria = $this->createSearchCriteria($filters, $sortOrders);

        $emailsList = $this->getEmailSearchResult($searchCriteria);

        return $emailsList->getItems();
    }

    /**
     * Get enabled campaign steps ids
     *
     * @return int[]
     */
    private function getEnabledCampaignStepsIds()
    {
        return $this->campaignStepService->getActiveCampaignStepsIds();
    }

    /**
     * Send or hold or skip emails
     *
     * @param EmailInterface[] $emails
     * @return array
     * @throws LocalizedException
     */
    public function sendOrHoldOrSkipEmails($emails)
    {
        $emailStatusStatistic = $this->getInitialEmailStatusStatisticArray();

        foreach ($emails as $email) {
            $this->sendOrHoldOrSkipEmail($email);

            $emailStatusStatistic = $this->updateStatusStatistic($email, $emailStatusStatistic);
        }

        return $emailStatusStatistic;
    }

    /**
     * Get initial email status
     *
     * @return array
     */
    private function getInitialEmailStatusStatisticArray()
    {
        return [
            StatusInterface::STATUS_PENDING => 0,
            StatusInterface::STATUS_SENT => 0,
            StatusInterface::STATUS_HOLD => 0,
            StatusInterface::STATUS_ERROR => 0,
        ];
    }

    /**
     * Send or hold or skip email
     *
     * @param EmailInterface $email
     * @return EmailInterface
     * @throws LocalizedException
     */
    private function sendOrHoldOrSkipEmail(EmailInterface $email)
    {
        $campaignStep = $this->getCampaignStepByEmail($email);
        $campaign = $this->getCampaignByCampaignStep($campaignStep);
        $eventCode = $campaign->getEventCode();
        $eventsEmailGeneratorHelper = $this->getEventEmailsGeneratorHelperByEventCode($eventCode);

        if (!$eventsEmailGeneratorHelper) {
            return $email;
        }

        $emailAttributes = $this->getEmailAttributesValuesByEmail($email);

        if (!$this->isEventEnabled($eventCode)) {
            return $email;
        }

        if (!$this->canSendEmail($email, $eventsEmailGeneratorHelper, $campaignStep)) {
            $this->setAndSaveEmailStatusHold($email);

            return $email;
        }

        $eventCode = $campaign->getEventCode();

        if (!$this->isEmailCustomerSubscribed($email, $eventCode)) {
            return $email;
        }

        $this->sendEmailByEmail(
            $eventsEmailGeneratorHelper,
            $campaign,
            $campaignStep,
            $email,
            $emailAttributes
        );

        return $email;
    }

    /**
     * Get campaign step by email
     *
     * @param EmailInterface $email
     * @return CampaignStepInterface
     */
    private function getCampaignStepByEmail(EmailInterface $email)
    {
        $campaignStepId = $email->getCampaignStepId();

        return $this->getCampaignStepById($campaignStepId);
    }

    /**
     * Get campaign step by id
     *
     * @param int $campaignStepId
     * @return CampaignStepInterface
     */
    private function getCampaignStepById($campaignStepId)
    {
        return $this->campaignStepsRepository->get($campaignStepId);
    }

    /**
     * Get campaign by campaign step
     *
     * @param CampaignStepInterface $campaignStep
     * @return CampaignInterface
     */
    private function getCampaignByCampaignStep(CampaignStepInterface $campaignStep)
    {
        $campaignId = $campaignStep->getCampaignId();

        return $this->getCampaignById($campaignId);
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
     * Get email attribute values by email
     *
     * @param EmailInterface $email
     * @return array
     */
    private function getEmailAttributesValuesByEmail(EmailInterface $email)
    {
        $emailAttributes = $this->getEmailAttributesByEmail($email);

        return $this->getEmailAttributesValues($emailAttributes);
    }

    /**
     * Get email attributes by email
     *
     * @param EmailInterface $email
     * @return EmailAttributeInterface[]
     */
    private function getEmailAttributesByEmail(EmailInterface $email)
    {
        $emailId = $email->getEntityId();

        return $this->getEmailAttributesByEmailId($emailId);
    }

    /**
     * Get email attributes by email id
     *
     * @param int $emailId
     * @return EmailAttributeInterface[]
     */
    private function getEmailAttributesByEmailId($emailId)
    {
        $filters = [
            [EmailAttributeTableInterface::COLUMN_NAME_EMAIL_ID, $emailId],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);
        $emailAttributesSearchResults = $this->getEmailAttributeSearchResult($searchCriteria);

        return $emailAttributesSearchResults->getItems();
    }

    /**
     * Get email attributes values
     *
     * @param EmailAttributeInterface[] $emailAttributes
     * @return array
     */
    private function getEmailAttributesValues($emailAttributes)
    {
        $emailAttributesValues = [];

        foreach ($emailAttributes as $emailAttribute) {
            $attributeCode = $emailAttribute->getAttributeCode();
            $attributeValue = $emailAttribute->getValue();
            $emailAttributesValues[$attributeCode] = $attributeValue;
        }

        return $emailAttributesValues;
    }

    /**
     * Is event enabled
     *
     * @param string $eventCode
     * @return bool
     */
    private function isEventEnabled($eventCode)
    {
        return $this->eventManagement->isEventEnabled($eventCode);
    }

    /**
     * Can send email
     *
     * @param EmailInterface $email
     * @param EventEmailsGeneratorHelperInterface $eventEmailGeneratorHelper
     * @param CampaignStepInterface $campaignStep
     * @return bool
     */
    private function canSendEmail(
        EmailInterface $email,
        EventEmailsGeneratorHelperInterface $eventEmailGeneratorHelper,
        CampaignStepInterface $campaignStep
    ) {
        $emailAttributesValues = $this->getEmailAttributeValuesByEmail($email);

        return $eventEmailGeneratorHelper->canSendEmail($campaignStep, $emailAttributesValues);
    }

    /**
     * Get email attribute values by email
     *
     * @param EmailInterface $email
     * @return array
     */
    private function getEmailAttributeValuesByEmail(EmailInterface $email)
    {
        $emailId = $email->getEntityId();

        return $this->getEmailAttributeValuesByEmailId($emailId);
    }

    /**
     * Get email attribute values by email id
     *
     * @param int $emailId
     * @return array
     */
    private function getEmailAttributeValuesByEmailId($emailId)
    {
        $emailAttributes = $this->getEmailAttributesByEmailId($emailId);

        return $this->getEmailAttributesValues($emailAttributes);
    }

    /**
     * Set and save email status hold
     *
     * @param EmailInterface $email
     */
    private function setAndSaveEmailStatusHold(EmailInterface $email)
    {
        $email->setStatus(StatusInterface::STATUS_HOLD);
        $this->saveEmail($email);
    }

    /**
     * Is email customer subscribed
     *
     * @param EmailInterface $email
     * @param string $eventCode
     * @return bool
     */
    private function isEmailCustomerSubscribed(EmailInterface $email, $eventCode)
    {
        $toEmail = $email->getEmailAddress();

        return $this->isCustomerSubscribedToEvent($toEmail, $eventCode);
    }

    /**
     * Send email by email
     *
     * @param EventEmailsGeneratorHelperInterface $emailGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param EmailInterface $email
     * @param array $emailAttributes
     * @return $this
     * @throws LocalizedException
     */
    private function sendEmailByEmail(
        EventEmailsGeneratorHelperInterface $emailGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        EmailInterface $email,
        $emailAttributes
    ) {
        $campaignStepTemplateId = $campaignStep->getTemplateId();
        $storeId = $this->getStoreId($emailGeneratorHelper, $email);
        $emailSenderContact = $campaign->getSender();
        $templateVars = $this->getEmailTemplateVars(
            $emailGeneratorHelper,
            $campaign,
            $campaignStep,
            $email,
            $emailAttributes
        );

        $toEmail = $email->getEmailAddress();
        $recipientName = $this->getCustomerNameByEmailAttributes($emailGeneratorHelper, $emailAttributes);

        $transport = $this
            ->transportBuilder
            ->setTemplateIdentifier($campaignStepTemplateId)
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
            ->setFrom($emailSenderContact)
            ->setTemplateVars($templateVars)
            ->addTo($toEmail, $recipientName)
            ->getTransport();

        try {
            $transport->sendMessage();
            $this->updateEmailOnSendSuccess($email);
        } catch (MailException $e) {
            $this->updateEmailOnSendError($email);
            $this->logCritical($e);
        }

        return $this;
    }

    /**
     * Get store id
     *
     * @param EventEmailsGeneratorHelperInterface $emailGeneratorHelper
     * @param EmailInterface $email
     * @return int
     */
    private function getStoreId(EventEmailsGeneratorHelperInterface $emailGeneratorHelper, EmailInterface $email)
    {
        $emailAttributesValues = $this->getEmailAttributesValuesByEmail($email);

        $entity = $this->getEntityByEmailAttributes($emailGeneratorHelper, $emailAttributesValues);

        return $emailGeneratorHelper->getStoreIdByEntity($entity);
    }

    /**
     * Get entity by email attributes
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param array $emailAttributes
     * @return mixed
     */
    private function getEntityByEmailAttributes(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        $emailAttributes
    ) {
        return $this->emailHelper->getEntityByEmailAttributes($eventEmailsGeneratorHelper, $emailAttributes);
    }

    /**
     * Get email template vars
     *
     * @param EventEmailsGeneratorHelperInterface $emailGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param EmailInterface $email
     * @param array $emailAttributes
     * @return array
     * @throws LocalizedException
     */
    private function getEmailTemplateVars(
        EventEmailsGeneratorHelperInterface $emailGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        EmailInterface $email,
        $emailAttributes
    ) {
        $emailId = $email->getEntityId();
        $storeId = $this->getStoreIdByEmailAttributes($emailGeneratorHelper, $emailAttributes);
        $customerName = $this->getCustomerNameByEmailAttributes($emailGeneratorHelper, $emailAttributes);
        $moduleData = $this->getModuleData($emailGeneratorHelper, $campaign, $campaignStep, $email, $emailAttributes);
        $secretCode = $email->getSecretCode();
        $coupon = $this->getCouponByEmail($campaignStep, $emailAttributes, $storeId);

        $templateVars = [
            'store_id' => $storeId,
            'email_id' => $emailId,
            'customer_name' => $customerName,
            'secret_code' => $secretCode,
            'unsubscribe_code' => $secretCode,//for backward compatibility
            'coupon' => $coupon,
            'module_data' => $moduleData,
            self::AITOC_FOLLOW_UP_IDENTIFIER => true
        ];

        $templateVars = array_merge($templateVars, $emailAttributes);

        return $templateVars;
    }

    /**
     * Get store id by email attributes
     *
     * @param EventEmailsGeneratorHelperInterface $emailGeneratorHelper
     * @param array $emailAttributes
     * @return int
     */
    private function getStoreIdByEmailAttributes(
        EventEmailsGeneratorHelperInterface $emailGeneratorHelper,
        $emailAttributes
    ) {
        return $this->emailHelper->getStoreIdByEmailAttributes($emailGeneratorHelper, $emailAttributes);
    }

    /**
     * Get customer name by email attributes
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param EmailAttributeInterface[] $emailAttributes
     * @return string
     */
    private function getCustomerNameByEmailAttributes(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        $emailAttributes
    ) {
        $entity = $this->getEntityByEmailAttributes($eventEmailsGeneratorHelper, $emailAttributes);

        $firstName = $eventEmailsGeneratorHelper->getCustomerFirstsNameByEntity($entity);
        $lastName = $eventEmailsGeneratorHelper->getCustomerLastNameByEntity($entity);

        return $this->getCustomerEmailName($firstName, $lastName);
    }

    /**
     * Get customer email name
     *
     * @param string|null $firstName
     * @param string|null $lastName
     * @return string
     */
    private function getCustomerEmailName($firstName, $lastName)
    {
        if (!$firstName && !$lastName) {
            return "Guest";
        }

        return "{$firstName} {$lastName}";
    }

    /**
     * Get module data
     *
     * @param EventEmailsGeneratorHelperInterface $emailGeneratorHelper
     * @param CampaignInterface $campaign
     * @param CampaignStepInterface $campaignStep
     * @param EmailInterface $email
     * @param array $emailAttributes
     * @return DataObject
     */
    private function getModuleData(
        EventEmailsGeneratorHelperInterface $emailGeneratorHelper,
        CampaignInterface $campaign,
        CampaignStepInterface $campaignStep,
        EmailInterface $email,
        $emailAttributes
    ) {
        $moduleDataArray = $emailGeneratorHelper->getModuleData($campaign, $campaignStep, $email, $emailAttributes);

        $moduleData = new DataObject();
        $moduleData->addData($moduleDataArray);

        return $moduleData;
    }

    /**
     * Get coupon by email
     *
     * @param CampaignStepInterface $campaignStep
     * @param array $emailAttributes
     * @param int $storeId
     * @return DataObject
     * @throws LocalizedException
     */
    private function getCouponByEmail(CampaignStepInterface $campaignStep, $emailAttributes, $storeId)
    {
        $coupon = new DataObject();

        if (!$this->isCouponDataRequired($campaignStep, $emailAttributes)) {
            return $coupon;
        }

        $couponData = $this->getCouponData($campaignStep, $emailAttributes, $storeId);
        $coupon->addData($couponData);

        return $coupon;
    }

    /**
     * Is coupon data required
     *
     * @param CampaignStepInterface $campaignStep
     * @param array $emailAttributes
     * @return bool
     * @throws LocalizedException
     */
    private function isCouponDataRequired(CampaignStepInterface $campaignStep, $emailAttributes)
    {
        if (!$campaignStep->getDiscountStatus()) {
            return false;
        }

        $couponCode = $this->getCouponCodeByEmailAttributes($emailAttributes);

        if (!$couponCode) {
            return false;
        }

        return true;
    }

    /**
     * Get coupon code by email attributes
     *
     * @param array $emailAttributes
     * @return null|string
     * @throws LocalizedException
     */
    private function getCouponCodeByEmailAttributes($emailAttributes)
    {
        $salesRuleId = isset($emailAttributes[self::ATTRIBUTE_CODE_RULE_ID]) ? $emailAttributes[self::ATTRIBUTE_CODE_RULE_ID] : null;

        $couponCode = $salesRuleId ? $this->getFirstCouponCodeByCartPriceRuleId($salesRuleId) : null;

        return $couponCode;
    }

    /**
     * Get first coupon code by cart price rule id
     *
     * @param int $salesRuleId
     * @return null|string
     * @throws LocalizedException
     */
    private function getFirstCouponCodeByCartPriceRuleId($salesRuleId)
    {
        $filters = [
            [self::ATTRIBUTE_CODE_RULE_ID, $salesRuleId]
        ];

        $coupons = $this->getCouponsByFilters($filters);
        $coupon = $coupons ? reset($coupons) : null;

        return  $coupon ? $coupon->getCode() : null;
    }

    /**
     * Get coupon by filters
     *
     * @param array $filters
     * @return CouponInterface[]
     * @throws LocalizedException
     */
    private function getCouponsByFilters($filters)
    {
        $searchCriteria = $this->createSearchCriteria($filters);

        return $this->getCouponsBySearchCriteria($searchCriteria);
    }

    /**
     * Get coupons by search criteria
     *
     * @param SearchCriteria $searchCriteria
     * @return CouponInterface[]
     * @throws LocalizedException
     */
    private function getCouponsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        return $this->getCouponSearchResult($searchCriteria)->getItems();
    }

    /**
     * Get coupon search result
     *
     * @param SearchCriteria $searchCriteria
     * @return CouponSearchResultInterface
     * @throws LocalizedException
     */
    private function getCouponSearchResult(SearchCriteria $searchCriteria)
    {
        return $this->couponRepository->getList($searchCriteria);
    }

    /**
     * Get coupon data
     *
     * @param CampaignStepInterface $campaignStep
     * @param $emailAttributes
     * @param int $storeId
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getCouponData(CampaignStepInterface $campaignStep, $emailAttributes, $storeId)
    {
        $couponCode = $this->getCouponCodeByEmailAttributes($emailAttributes);
        $store = $this->websiteHelper->getStoreByStoreId($storeId);
        $currency = $this->currencyFactory->create()->load($store->getBaseCurrency()->getCode());
        $currencySymbol = $currency->getCurrencySymbol();

        if ($campaignStep->getDiscountType() == DiscountTypeOptions::ACTION_TYPE_RULE_OPTION) {
            $salesRule = $this->ruleRepository->getById($campaignStep->getSalesRuleId());
            switch ($salesRule->getSimpleAction()) {
                case \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION:
                    $discountAmountString = $salesRule->getDiscountAmount() .  $currencySymbol;
                    break;
                case \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION:
                    $discountAmountString = $salesRule->getDiscountAmount() .  '%';
                    break;
                default:
                    $discountAmountString = '';
            }
            $discountPeriod = $salesRule->getToDate();
        } else {
            $isFixed = $campaignStep->getDiscountType() == DiscountTypeOptions::ACTION_TYPE_FIXED_OPTION;
            $discountAmount = (int) $campaignStep->getDiscountPercent();
            $discountAmountString = "{$discountAmount}" . ($isFixed ? $currencySymbol : '%');
            $discountPeriod = $campaignStep->getDiscountPeriod();
        }

        return [
            'coupon_code' => $couponCode,
            'discount_amount' => $discountAmountString,
            'expiry_days' => $discountPeriod
        ];
    }

    /**
     * Update email on send success
     *
     * @param EmailInterface $email
     */
    private function updateEmailOnSendSuccess(EmailInterface $email)
    {
        $email->setStatus(StatusInterface::STATUS_SENT);
        $currentDateTime = $this->getCurrentDateTimeAsString();
        $email->setSentAt($currentDateTime);

        $this->saveEmail($email);
    }

    /**
     * Update email on send error
     *
     * @param EmailInterface $email
     */
    private function updateEmailOnSendError(EmailInterface $email)
    {
        $email->setStatus(StatusInterface::STATUS_ERROR);
        $this->saveEmail($email);
    }

    /**
     * Log critical issue
     *
     * @param $e
     */
    private function logCritical($e)
    {
        $this->_logger->critical($e);
    }

    /**
     * Update status statistic
     *
     * @param EmailInterface $email
     * @param array $emailStatusStatistic
     * @return array
     */
    private function updateStatusStatistic(EmailInterface $email, $emailStatusStatistic)
    {
        $emailStatus = $email->getStatus();
        $emailStatusStatistic[$emailStatus]++;

        return $emailStatusStatistic;
    }
}
