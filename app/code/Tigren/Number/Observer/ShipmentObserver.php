<?php

namespace Tigren\Number\Observer;

/**
 * Class ShipmentObserver
 * @package Tigren\Number\Observer
 */
class ShipmentObserver extends AbstractObserver
{

    /**
     * path for prefix config setting
     *
     * @var string
     */
    protected $prefixConfigPath = 'tigren_number/settings/shipmentprefix';

    /**
     * @param $order
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection
     */
    public function getCollection($order)
    {
        return $order->getShipmentsCollection();
    }

    /**
     * change the shipment increment to the order increment id
     * only affects shipments without id (=new shipments)
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->assignIncrement($observer->getShipment());
    }
}
