<?php

namespace Tigren\CheckoutCart\Block\Onepage;

use Magento\Checkout\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Link
 * @package Tigren\CheckoutCart\Block\Onepage
 */
class Link extends \Magento\Checkout\Block\Onepage\Link
{

    /**
     * @var \Tigren\CheckoutCart\Helper\Data
     */
    protected $_helper;

    /**
     * Link constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param Data $checkoutHelper
     * @param \Tigren\CheckoutCart\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Data $checkoutHelper,
        \Tigren\CheckoutCart\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $checkoutSession, $checkoutHelper, $data);
        $this->_helper = $helper;
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout') . '?t=' . $this->_helper->getCurrentTimestamp();
    }
}
