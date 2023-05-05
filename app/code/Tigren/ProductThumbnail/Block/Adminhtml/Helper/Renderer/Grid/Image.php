<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Block\Adminhtml\Helper\Renderer\Grid;

/**
 * Class Image
 * @package Tigren\ProductThumbnail\Block\Adminhtml\Helper\Renderer\Grid
 */
class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * ThumbnailFactory factory.
     *
     * @var \Tigren\ProductThumbnail\Model\ThumbnailFactory
     */
    protected $_thumbnailFactory;

    /**
     * Image constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Tigren\ProductThumbnail\Model\ThumbnailFactory $thumbnailFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Tigren\ProductThumbnail\Model\ThumbnailFactory $thumbnailFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_thumbnailFactory  = $thumbnailFactory;
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $storeViewId = $this->getRequest()->getParam('store');
        $thumbnail = $this->_thumbnailFactory->create()->setStoreViewId($storeViewId)->load($row->getId());
        $srcImage = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . $thumbnail->getImage();

        return '<image width="50" height="50" src ="'.$srcImage.'" alt="'.$thumbnail->getImage().'" >';
    }
}
