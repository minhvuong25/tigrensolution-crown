<?php

namespace Tigren\PrintOrder\Model\ResourceModel\GuestOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Tigren\PrintOrder\Model\ResourceModel\GuestOrder
 */
class Collection extends AbstractCollection
{
    /**
     * Clean expired guests orders.
     *
     * @return \Tigren\PrintOrder\Model\ResourceModel\GuestOrder\Collection
     */
    public function cleanExpiredGuestsOrders()
    {
        $this->addOrder('expired_at')
            ->addFieldToFilter(
                'expired_at',
                ['to' => date('Y-m-d H:i:s', time())]
            );
        \Zend_Debug::dump($this->getSelect()->__toString());
        $this->walk('delete');

        return $this;
    }

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Tigren\PrintOrder\Model\GuestOrder',
            'Tigren\PrintOrder\Model\ResourceModel\GuestOrder'
        );
    }
}
