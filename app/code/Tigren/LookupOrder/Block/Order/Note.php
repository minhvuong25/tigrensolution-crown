<?php
/**
 * *
 * * @author    Tigren Solutions <info@tigren.com>
 * * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\LookupOrder\Block\Order;

/**
 * Class View
 * @package Tigren\LookupOrder\Block\Order
 */
class Note extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Tigren_LookupOrder::order/order_note.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
}
