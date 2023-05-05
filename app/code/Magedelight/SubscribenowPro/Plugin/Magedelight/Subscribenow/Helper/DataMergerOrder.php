<?php

namespace Magedelight\SubscribenowPro\Plugin\Magedelight\Subscribenow\Helper;

class DataMergerOrder
{
    /**
     * @var \Magedelight\SubscribenowPro\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magedelight\SubscribenowPro\Helper\Data $helper
     */
    public function __construct(
        \Magedelight\SubscribenowPro\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    public function afterGetMaxAllowedQty($subject, $result)
    {
        if ($this->helper->isEnable() && !$this->helper->isValidateQty() && $this->helper->getProcessingMergeOrder()) {
            $result = 99999;
        }

        return $result;
    }
}
