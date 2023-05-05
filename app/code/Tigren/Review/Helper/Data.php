<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\Review\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Review\Observer\PredispatchReviewObserver;

/**
 * Class Data
 * @package Tigren\ProductThumbnail\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_date;

    protected $_registry;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_reviewFactory = $reviewFactory;
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
        $this->_date =  $date;
        $this->logger = $logger;
    }

    /**
     * @param $product
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRatingSummary($product)
    {
        $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        return $product->getRatingSummary();
    }

    /**
     * Review module availability
     *
     * @return string
     */
    public function isReviewEnabled(): string
    {
        return $this->_scopeConfig->getValue(
            PredispatchReviewObserver::XML_PATH_REVIEW_ACTIVE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->_date->date()->format('Y-m-d H:i:s');
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
}
