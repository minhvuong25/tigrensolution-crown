<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\TransitTo\Base;

use Aitoc\FollowUpEmails\Api\Contoller\Account\Login\RequestParamNameInterface;
use Aitoc\FollowUpEmails\Api\EmailRepositoryInterface;
use Aitoc\FollowUpEmails\Controller\TransitTo\Base\Homepage as BaseTransitToHomepageAction;
use Aitoc\FollowUpEmails\Helper\WebsiteInterface as WebsiteServiceInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;

abstract class Product extends BaseTransitToHomepageAction
{
    const REQUEST_PARAM_PRODUCT_ID = 'product_id';

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Product constructor.
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
     * @param ProductRepositoryInterface $productRepository
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
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct(
            $context,
            $emailRepository,
            $customerSession,
            $cookieManager,
            $cookieMetadataFactory,
            $customerRepository,
            $date,
            $websiteService,
            $storeManager
        );

        $this->productRepository = $productRepository;
    }

    /**
     * Get result redirect
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NoSuchEntityException
     */
    protected function getResultRedirect(RequestInterface $request)
    {
        $requestedProductId = $this->getRequestedProductId($request);
        $requestedAnchor = $this->getRequestedAnchor($request);

        return ($requestedProductId)
            ? $this->createRedirectToProductPageByProductId($requestedProductId, $requestedAnchor)
            : $this->createRedirectToHomepage($requestedAnchor);
    }

    /**
     * Get requested product id
     *
     * @param RequestInterface $request
     * @return int
     */
    private function getRequestedProductId(RequestInterface $request)
    {
        return $request->getParam(RequestParamNameInterface::PRODUCT_ID);
    }

    /**
     * Create redirect to product page
     *
     * @param int $productId
     * @param string|null $anchor
     * @return ResponseInterface
     * @throws NoSuchEntityException
     */
    private function createRedirectToProductPageByProductId($productId, $anchor = null)
    {
        $productUrl = $this->getProductUrlByProductId($productId);

        if ($anchor) {
            $productUrl = $this->addAnchorToUrl($productUrl, $anchor);
        }

        return $this->_redirect($productUrl);
    }

    /**
     * Get product url by product id
     *
     * @param int $productId
     * @return string
     * @throws NoSuchEntityException
     */
    private function getProductUrlByProductId($productId)
    {
        $product = $this->getProductById($productId);

        return $product->getProductUrl();
    }

    /**
     * Get product by id
     *
     * @param int $productId
     * @return ProductInterface|ProductModel
     * @throws NoSuchEntityException
     */
    private function getProductById($productId)
    {
        return $this->productRepository->getById($productId);
    }
}
