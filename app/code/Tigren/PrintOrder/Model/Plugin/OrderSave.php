<?php

namespace Tigren\PrintOrder\Model\Plugin;

use Tigren\PrintOrder\Helper\Data as PrintOrderHelper;
use Tigren\PrintOrder\Model\GuestOrder;

/**
 * Class OrderSave
 * @package Tigren\PrintOrder\Model\Plugin
 */
class OrderSave
{
    /**
     * @var \Tigren\PrintOrder\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Tigren\PrintOrder\Model\GuestOrder
     */
    protected $_guestOrder;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @param GuestOrder $guestOrder
     * @param PrintOrderHelper $helper
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        GuestOrder $guestOrder,
        PrintOrderHelper $helper,
        \Magento\Framework\App\State $state
    ) {
        $this->_guestOrder = $guestOrder;
        $this->_helper = $helper;
        $this->_appState = $state;
    }

    /**
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        try {
            /** @var $guestOrder \Tigren\PrintOrder\Model\GuestOrder */
            $guestOrder = $this->_helper->createFromOrder($order);
            $guestOrder->save();
        } catch (\Tigren\PrintOrder\Model\Exception $e) {
            // if DeveloperMode enabled throw Exception, otherwise skip saving of such object
            //if ($this->_appState->getMode() == \Magento\Framework\App\State::MODE_DEVELOPER) {
            //    throw new \Tigren\PrintOrder\Model\Exception(__($e->getMessage()));
            //}
        }

        return $order;
    }
}
