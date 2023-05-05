<?php

namespace Tigren\Number\Observer;

/**
 * Class InvoiceObserver
 * @package Tigren\Number\Observer
 */
class InvoiceObserver extends AbstractObserver
{

    /**
     * path for prefix config setting
     *
     * @var string
     */
    protected $prefixConfigPath = 'tigren_number/settings/invoiceprefix';

    /**
     * @param $order
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Invoice\Collection
     */
    public function getCollection($order)
    {
        return $order->getInvoiceCollection();
    }

    /**
     * change the invoice increment to the order increment id
     * only affects invoices without id (=new invoices)
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->assignIncrement($observer->getInvoice());
    }
}
