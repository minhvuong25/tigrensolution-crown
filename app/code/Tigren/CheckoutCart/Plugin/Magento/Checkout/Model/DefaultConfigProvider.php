<?php

namespace Tigren\CheckoutCart\Plugin\Magento\Checkout\Model;

use Magento\Framework\UrlInterface;
use Tigren\CheckoutCart\Helper\Data;

/**
 * Class DefaultConfigProvider
 * @package Tigren\CheckoutCart\Plugin\Magento\Checkout\Model
 */
class DefaultConfigProvider
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
     * DefaultConfigProvider constructor.
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
    public function afterGetCheckoutUrl(\Magento\Checkout\Model\DefaultConfigProvider $subject, $result)
    {
        return $this->urlBuilder->getUrl('checkout') . '?t=' . $this->_helper->getCurrentTimestamp();
    }
}
