<?php

namespace Tigren\CheckoutCart\Plugin\Magento\Checkout\Block\Cart;

use Magento\Framework\UrlInterface;
use Tigren\CheckoutCart\Helper\Data;

/**
 * Class Sidebar
 * @package Tigren\CheckoutCart\Plugin\Magento\Checkout\Block\Cart
 */
class Sidebar
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * Sidebar constructor.
     * @param UrlInterface $urlBuilder
     * @param Data $helper
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Data $helper
    ) {
        $this->_helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param $result
     * @return mixed
     */
    public function afterGetCheckoutUrl(\Magento\Checkout\Block\Cart\Sidebar $subject, $result)
    {
        return $this->urlBuilder->getUrl('checkout') . '?t=' . $this->_helper->getCurrentTimestamp();
    }
}
