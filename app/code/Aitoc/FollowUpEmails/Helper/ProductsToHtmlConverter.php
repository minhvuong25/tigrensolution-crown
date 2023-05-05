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

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Aitoc\FollowUpEmails\Api\Helper\ProductsToHtmlConverterInterface;

class ProductsToHtmlConverter implements ProductsToHtmlConverterInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ProductsToHtmlConverter constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        UrlInterface $urlBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get products html
     *
     * @param array $products
     * @param int $emailId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductsHtml($products, $emailId)
    {
        if (!$products) {
            return '';
        }

        $productsHtml = '<table style="font-family: \'Roboto\', sans-serif;" border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr>';

        foreach ($products as $product) {
            $productsHtml .= $this->getProductHtml($emailId, $product);
        }

        $productsHtml .= '</tr></table>';

        return $productsHtml;
    }

    /**
     * Get product html
     *
     * @param int $emailId
     * @param array $productArray
     * @return string
     * @throws NoSuchEntityException
     */
    private function getProductHtml($emailId, $productArray)
    {
        $productId = $productArray['id'];
        $product = $this->getProductById($productId);

        $baseUrl = $this->getBaseUrl();
        $productTrackedLoginUrl = $this->getProductTrackedLoginUrl($baseUrl, $productId, $emailId);
        $escapedProductTrackedLoginUrl = htmlspecialchars($productTrackedLoginUrl);

        $productImages = $this->getProductImages($product);
        $productImage = reset($productImages);
        $productImageUrl = $productImage->getUrl();
        $productName = $productArray['name'];
        $escapedProductName = htmlspecialchars($productName);

        return <<<EOT
<td style="text-align: center;">
    <div><a href="{$escapedProductTrackedLoginUrl}"><img style="width: 80%;" src="{$productImageUrl}"/></a></div>
    <br/>
    <div style="margin-right: 15px; margin-left: 15px; text-align: center;"><a href="{$escapedProductTrackedLoginUrl}">{$escapedProductName}</a></div>
</td>
EOT;
    }

    /**
     * Get product by id
     *
     * @param int $productId
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    private function getProductById($productId)
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * Get product images
     *
     * @param ProductInterface|Product $product
     * @return DataObject[]
     */
    private function getProductImages(ProductInterface $product)
    {
        return $product->getMediaGalleryImages()->getItems();
    }

    /**
     * Get base url
     *
     * @return string
     */
    private function getBaseUrl()
    {
        return $this->urlBuilder->getBaseUrl();
    }

    /**
     * Get product tracked login url
     *
     * @param string $baseUrl
     * @param int $productId
     * @param string $emailId
     * @return string
     */
    private function getProductTrackedLoginUrl($baseUrl, $productId, $emailId)
    {
        return "{$baseUrl}followup/account/login/product_id/{$productId}/email_id/{$emailId}";
    }
}
