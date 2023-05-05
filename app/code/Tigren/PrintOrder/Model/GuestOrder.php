<?php

namespace Tigren\PrintOrder\Model;

/**
 * Class GuestOrder
 * @package Tigren\PrintOrder\Model
 */
class GuestOrder extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initial model constructor.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Tigren\PrintOrder\Model\ResourceModel\GuestOrder');
    }
}
