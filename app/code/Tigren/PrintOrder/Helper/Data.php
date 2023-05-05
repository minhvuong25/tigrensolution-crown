<?php

namespace Tigren\PrintOrder\Helper;

/**
 * Class Data
 * @package Tigren\PrintOrder\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     */
    const DEFAULT_GUESTORDER_AVAILABILITY_PERIOD = 3600; // 1 hour (60 seconds * 60 minutes)

    /**
     * @var \Tigren\PrintOrder\Model\GuestOrder
     */
    protected $_guestOrder;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_config;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @param \Tigren\PrintOrder\Model\GuestOrder $guestOrder
     * @param \Magento\Sales\Model\Order\Config $config
     * @param \Magento\Sales\Model\Order $order
     */
    public function __construct(
        \Tigren\PrintOrder\Model\GuestOrder $guestOrder,
        \Magento\Sales\Model\Order\Config $config,
        \Magento\Sales\Model\Order $order
    ) {
        $this->_guestOrder = $guestOrder;
        $this->_config = $config;
        $this->_order = $order;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Tigren\PrintOrder\Model\GuestOrder
     * @throws \Tigren\PrintOrder\Model\Exception
     */
    public function createFromOrder(\Magento\Sales\Model\Order $order)
    {
        if (!$order->getId()) {
            throw new \Tigren\PrintOrder\Model\Exception('Can not create GuestOrder from given Order');
        }

        $this->_guestOrder->setHash($this->_generateHashForGuestOrder($order))
            ->setOrderId($order->getId())
            ->setExpiredAt($this->_generateExpiredAtForGuestOrder());

        return $this->_guestOrder;
    }

    /**
     * Generate hash string for GuestOrder using Order values.
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    protected function _generateHashForGuestOrder(\Magento\Sales\Model\Order $order)
    {
        return hash('sha256', $order->getIncrementId());
    }

    /**
     * Generate expired at date for GuestOrder.
     *
     * @return int
     */
    protected function _generateExpiredAtForGuestOrder()
    {
        return date('Y-m-d H:i:s', (time() + self::DEFAULT_GUESTORDER_AVAILABILITY_PERIOD));
    }

    /**
     * @param $guestOrderHash
     * @return mixed
     * @throws \Tigren\PrintOrder\Model\Exception
     */
    public function getGuestOrderByHash($guestOrderHash)
    {
        $guestOrder = $this->_guestOrder->load($guestOrderHash, 'hash');

        if (!$guestOrder->getId() || !$guestOrder->getOrderId()
            || false == $this->getIsGuestOrderActive($guestOrder)
        ) {
            throw new \Tigren\PrintOrder\Model\Exception('Corrupted Guest Order');
        }

        return $guestOrder;
    }

    /**
     * Check if GuestOrder is still active for guest.
     *
     * @param \Tigren\PrintOrder\Model\GuestOrder $guestOrder
     *
     * @return bool
     */
    public function getIsGuestOrderActive(\Tigren\PrintOrder\Model\GuestOrder $guestOrder)
    {
        if ($guestOrder->getId() && strtotime($guestOrder->getExpiredAt()) > time()) {
            return true;
        }

        return false;
    }

    /**
     * @param $orderId
     * @return mixed
     * @throws \Tigren\PrintOrder\Model\Exception
     */
    public function getOrderById($orderId)
    {
        $order = $this->_order->load($orderId);
        if (!$order->getId() || false == $this->canViewOrder($order)) {
            throw new \Tigren\PrintOrder\Model\Exception('Corrupted Guest Order');
        }

        return $order;
    }

    /**
     * Check if order can be viewed.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    public function canViewOrder(\Magento\Sales\Model\Order $order)
    {
        $availableStatuses = $this->_config->getVisibleOnFrontStatuses();
        if (in_array($order->getStatus(), $availableStatuses, true)) {
            return true;
        }

        return false;
    }
}
