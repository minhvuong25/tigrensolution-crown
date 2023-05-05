<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;

/**
 * Class Data
 * @package Tigren\ProductThumbnail\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Tigren\ProductThumbnail\Model\ThumbnailFactory
     */
    protected $_thumbnailFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Tigren\ProductThumbnail\Model\ThumbnailFactory $thumbnailFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Tigren\ProductThumbnail\Model\ThumbnailFactory $thumbnailFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $fileSystem,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_productFactory = $productFactory;
        $this->_thumbnailFactory = $thumbnailFactory;
        $this->_storeManager = $storeManager;
        $this->_fileSystem = $fileSystem;
        $this->logger = $logger;
    }

    /**
     * @param $productId
     * @return false|string[]
     */
    public function getThumbnailIds($productId)
    {
        $product = $this->_productFactory->create()->load($productId);
        if (!empty($product->getCustomAttribute('tigren_product_thumbnail'))) {
            $thumbnails = $product->getCustomAttribute('tigren_product_thumbnail')->getValue();
            if (!empty($thumbnails)) {
                return explode(',', $thumbnails);
            }
        }
        return false;
    }

    /**
     * @param $thumId
     * @return mixed
     */
    public function getThumbnail($thumId)
    {
        try {
            return $this->_thumbnailFactory->create()->load($thumId);
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getImageUrl($image)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . $image;
    }
}
