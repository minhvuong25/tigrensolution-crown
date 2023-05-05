<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\SupportEmails\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 * @package Tigren\Callback\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    protected $sortBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $_saleRule;

    /**
     * @var \Magento\SalesRule\Model\CouponGenerator
     */
    protected $couponGenerator;

    /**
     * @var \Magento\SalesRule\Model\Coupon
     */
    protected $_coupon;

    /**
     * Config constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SortOrderBuilder $sortBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\SalesRule\Model\Rule $saleRule
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SortOrderBuilder $sortBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\SalesRule\Model\CouponGenerator $couponGenerator,
        \Magento\SalesRule\Model\Coupon $coupon,
        \Magento\SalesRule\Model\Rule $saleRule
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->sortBuilder = $sortBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_productRepository = $productRepository;
        $this->couponGenerator = $couponGenerator;
        $this->_coupon = $coupon;
        $this->_saleRule = $saleRule;
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
        return $this->getGeneralConfig('tigren_support_email/' . $path, $storeId);
    }

    /**
     * @return array
     */
    public function getEmailExpired()
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $productOption = $this->getModuleConfig('support_email/product_option');
        $couponId = $this->getModuleConfig('support_email/coupon_code');
        $saleRules = $this->_saleRule->load($couponId);
        $data = [
            'rule_id' => $couponId,
            'qty' => '1',
            'length' => '12',
            'format' => 'alpha',
        ];
        $coupon = implode($this->couponGenerator->generateCodes($data));
        $this->_coupon->loadByCode($coupon)->setExpirationDate($this->getExpirationDate())->save();

        $currentTime = date("Y-m-d h:i:s");
        $from = strtotime('-13 month', strtotime($currentTime));
        $from = date('Y-m-d h:i:s', $from);
        $to = strtotime('-12 month', strtotime($currentTime));
        $to = date('Y-m-d h:i:s', $to);
        $collection = $this->_orderCollectionFactory->create()
            ->addFilter('status', 'complete', 'eq')
            ->setOrder('entity_id', 'DESC')
            ->addFieldToFilter('created_at', ['lteq' => $to])
            ->addFieldToFilter('created_at', ['gteq' => $from]);

        if (!empty($collection)) {
            $data = [];
            foreach ($collection as $order) {
                foreach ($order->getAllVisibleItems() as $item) {
                    $options = $item->getProductOptions();
                    if (isset($options['options']) && count($options['options']) > 0) {
                        foreach ($options['options'] as $option) {
                            if (isset($option['value']) && $option['value'] == $productOption) {
                                $product = $this->getProduct();
                                $data[] = [
                                    'code' => $coupon,
                                    'discount' => (int)$saleRules->getDiscountAmount(),
                                    'incrementId' => $order->getIncrementId(),
                                    'customerEmail' => $order->getCustomerEmail(),
                                    'sku' => $product->getSku(),
                                    'price' => $product->getFinalPrice(),
                                    'name' => $product->getName(),
                                    'productImage' => $mediaUrl . "catalog/product" . $product->getImage(),
                                    'shortDescription' => $product->getShortDescription(),
                                    'discountPrice' => $product->getFinalPrice() * ((100 - (int)$saleRules->getDiscountAmount()) / 100),
                                    'productLink' => $product->getUrlModel()->getUrl($product),
                                ];
                                break;
                            }
                        }
                    }
                }
            }
            return $data;
        }
    }
    /**
     * @return array
     */
    public function getEmailNearlyExpired()
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $productOption = $this->getModuleConfig('support_email/product_option');
        $couponId = $this->getModuleConfig('support_email/coupon_code');
        $saleRules = $this->_saleRule->load($couponId);
        $data = [
            'rule_id' => $couponId,
            'qty' => '1',
            'length' => '12',
            'format' => 'alpha',
        ];
        $coupon = implode($this->couponGenerator->generateCodes($data));
        $this->_coupon->loadByCode($coupon)->setExpirationDate($this->getExpirationDate())->save();

        $currentTime = date("Y-m-d h:i:s");
        $from = strtotime('-12 month', strtotime($currentTime));
        $from = date('Y-m-d h:i:s', $from);
        $to = strtotime('-11 month', strtotime($currentTime));
        $to = date('Y-m-d h:i:s', $to);
        $collection = $this->_orderCollectionFactory->create()
            ->addFilter('status', 'complete', 'eq')
            ->setOrder('entity_id', 'DESC')
            ->addFieldToFilter('created_at', ['lteq' => $to])
            ->addFieldToFilter('created_at', ['gteq' => $from]);

        if (!empty($collection)) {
            $data = [];
            foreach ($collection as $order) {
                foreach ($order->getAllVisibleItems() as $item) {
                    $options = $item->getProductOptions();
                    if (isset($options['options']) && count($options['options']) > 0) {
                        foreach ($options['options'] as $option) {
                            if (isset($option['value']) && $option['value'] == $productOption) {
                                $product = $this->getProduct();
                                $data[] = [
                                    'code' => $coupon,
                                    'discount' => (int)$saleRules->getDiscountAmount(),
                                    'incrementId' => $order->getIncrementId(),
                                    'customerEmail' => $order->getCustomerEmail(),
                                    'sku' => $product->getSku(),
                                    'price' => $product->getFinalPrice(),
                                    'name' => $product->getName(),
                                    'productImage' => $mediaUrl . "catalog/product" . $product->getImage(),
                                    'shortDescription' => $product->getShortDescription(),
                                    'discountPrice' => $product->getFinalPrice() * ((100 - (int)$saleRules->getDiscountAmount()) / 100),
                                    'productLink' => $product->getUrlModel()->getUrl($product),
                                ];
                                break;
                            }
                        }
                    }
                }
            }
            return $data;
        }
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct()
    {
        $couponId = $this->getModuleConfig('support_email/coupon_code');
        $saleRules = $this->_saleRule->load($couponId);
        $conditions = $saleRules->getActions()->getConditions();
        $condition = array_shift($conditions);
        return $this->_productRepository->get($condition->getValue());
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    protected function getExpirationDate()
    {
        $currentTime = date("Y-m-d h:i:s");
        $date = strtotime('+30 day', strtotime($currentTime));
        return date('Y-m-d h:i:s', $date);
    }

    /**
     * @return float|string|null
     */
    public function getEmailTest()
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $couponId = $this->getModuleConfig('support_email/coupon_code');
        $saleRules = $this->_saleRule->load($couponId);
        $data = [
            'rule_id' => $couponId,
            'qty' => '1',
            'length' => '12',
            'format' => 'alpha',
        ];
        $coupon = implode($this->couponGenerator->generateCodes($data));
        $this->_coupon->loadByCode($coupon)->setExpirationDate($this->getExpirationDate())->save();

        $orderId = $this->getModuleConfig('send_email/order_id');
        if (!empty($orderId)) {
            $order = $this->orderRepository->get($orderId);
            $product = $this->getProduct();
            $datas[] = [
                    'code' => $coupon,
                    'discount' => (int)$saleRules->getDiscountAmount(),
                    'incrementId' => $order->getIncrementId(),
                    'customerEmail' => $order->getCustomerEmail(),
                    'sku' => $product->getSku(),
                    'price' => $product->getFinalPrice(),
                    'name' => $product->getName(),
                    'productImage' => $mediaUrl . "catalog/product" . $product->getImage(),
                    'shortDescription' => $product->getShortDescription(),
                    'discountPrice' => $product->getFinalPrice() * ((100 - (int)$saleRules->getDiscountAmount()) / 100),
                    'productLink' => $product->getUrlModel()->getUrl($product),
                ];
        }
        return $datas;
    }
}
