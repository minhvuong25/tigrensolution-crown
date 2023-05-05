<?php
/**
 * *
 * * @author    Tigren Solutions <info@tigren.com>
 * * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\LookupOrder\Block\Order;

use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

/**
 * Class View
 * @package Tigren\LookupOrder\Block\Order
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Tigren_LookupOrder::order/view.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Payment\Helper\Data $paymentHelper,
        AddressRenderer $addressRenderer,
        array $data = []
    ) {
        $this->_paymentHelper = $paymentHelper;
        $this->_coreRegistry = $registry;
        $this->addressRenderer = $addressRenderer;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * @return View
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Orders Information'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getCurrentOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * @param Address $address
     * @return string|null
     */
    public function getFormattedAddress(Address $address)
    {
        return $this->addressRenderer->format($address, 'html');
    }
}
