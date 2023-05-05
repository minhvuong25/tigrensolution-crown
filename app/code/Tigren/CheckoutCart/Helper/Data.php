<?php

namespace Tigren\CheckoutCart\Helper;

/**
 * Class Data
 * @package Tigren\CheckoutCart\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var int
     */
    private $currentTimeStamp;

    /**
     * @return int
     */
    public function getCurrentTimestamp()
    {
        if (!isset($this->currentTimeStamp)) {
            $this->currentTimeStamp = $this->_getRequest()->getParam('t') ?: time();
        }

        return $this->currentTimeStamp;
    }
}
