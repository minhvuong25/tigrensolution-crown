<?php
/**
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Controller\Adminhtml;

/**
 * Class Action
 * @package Tigren\ProductThumbnail\Controller\Adminhtml
 */
abstract class Action extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $_jsHelper;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * A factory that knows how to create a "page" result
     * Requires an instance of controller action in order to impose page type,
     * which is by convention is determined from the controller action class.
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Tigren\ProductThumbnail\Model\ThumbnailFactory
     */
    protected $_thumbnailFactory;

    /**
     * @var \Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail\CollectionFactory
     */
    protected $_thumbnailCollectionFactory;

    /**
     * Registry object.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * File Factory.
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * Action constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Tigren\ProductThumbnail\Model\ThumbnailFactory $thumbnailFactory
     * @param \Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail\CollectionFactory $thumbnailCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Backend\Helper\Js $jsHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Tigren\ProductThumbnail\Model\ThumbnailFactory $thumbnailFactory,
        \Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail\CollectionFactory $thumbnailCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Backend\Helper\Js $jsHelper
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_jsHelper = $jsHelper;

        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultForwardFactory = $resultForwardFactory;
		$this->_resultRedirectFactory = $context->getResultRedirectFactory();

        $this->_thumbnailFactory = $thumbnailFactory;
        $this->_thumbnailCollectionFactory = $thumbnailCollectionFactory;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        $namespace = (new \ReflectionObject($this))->getNamespaceName();
        $string = strtolower(str_replace(__NAMESPACE__ . '\\','', $namespace));
        $action =  explode('\\', $string);
        $action =  array_shift($action);
        return $this->_authorization->isAllowed("Tigren_ProductThumbnail::productthumbnail_$action");
    }
}
