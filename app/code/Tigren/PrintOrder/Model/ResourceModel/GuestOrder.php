<?php

namespace Tigren\PrintOrder\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class GuestOrder
 * @package Tigren\PrintOrder\Model\ResourceModel
 */
class GuestOrder extends AbstractDb
{
    /**
     * Define main table.
     */
    protected function _construct()
    {
        $this->_init('tigren_guests_orders', 'guests_order_id');
    }
}
