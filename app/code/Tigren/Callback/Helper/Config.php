<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\Callback\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 * @package Tigren\Callback\Helper
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Config constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_registry = $registry;
        $this->_storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public function getGeneralConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->getGeneralConfig('tigren_callback/' . $path, $storeId);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return mixed|null
     */
    public function getHelpLinks()
    {
        $data = [];
        $product = $this->_registry->registry('current_product');
        $categoryIds = $product->getData('category_ids');
        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryRepository->get($categoryId);
            $data[] = $category->getData('tigren_need_help');
        }
        return $data;
    }
}
