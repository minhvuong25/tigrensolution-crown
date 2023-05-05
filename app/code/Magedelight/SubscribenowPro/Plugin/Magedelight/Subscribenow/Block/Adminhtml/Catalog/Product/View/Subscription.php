<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_SubscribenowPro
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\SubscribenowPro\Plugin\Magedelight\Subscribenow\Block\Adminhtml\Catalog\Product\View;

class Subscription
{
    /**
     * @var \Magento\Quote\Model\Quote\Item
     */
    protected $quoteItem;

    /**
     * @var \Magento\Quote\Model\Quote\Item\Option
     */
    protected $quoteItemOption;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \Magento\Sales\Model\AdminOrder\Create
     */
    protected $adminOrderCreate;

    protected $store = null;

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param \Magento\Quote\Model\Quote\Item\Option $quoteItemOption
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Sales\Model\AdminOrder\Create $adminOrderCreate
     */
    public function __construct(
        \Magento\Quote\Model\Quote\Item $quoteItem,
        \Magento\Quote\Model\Quote\Item\Option $quoteItemOption,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Sales\Model\AdminOrder\Create $adminOrderCreate
    ) {
        $this->quoteItem = $quoteItem;
        $this->quoteItemOption = $quoteItemOption;
        $this->priceHelper = $priceHelper;
        $this->adminOrderCreate = $adminOrderCreate;
    }

    public function afterIsCartEdit($subject, $result)
    {
        if (!$result) {
            $handles = $subject->getLayout()->getUpdate()->getHandles();
            $result = in_array('CATALOG_PRODUCT_COMPOSITE_CONFIGURE', $handles);
        }

        return $result;
    }

    public function afterGetItemBuyRequest($subject, $result)
    {
        $itemId = $subject->getRequest()->getParam('id');
        if ($itemId) {
            $quoteItem = $this->quoteItem->load($itemId);
            /*
            if (!$quoteItem->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Quote item is not loaded.'));
            }
            */
            if ($quoteItem->getId()) {
                $optionCollection = $this->quoteItemOption
                    ->getCollection()
                    ->addFieldToFilter('code', 'info_buyRequest')
                    ->addItemFilter([$itemId]);

                $result = $optionCollection->getOptionsByItem($quoteItem)[0]->getValue();
            }
        }

        return $result;
    }

    /**
     * to get prices as per store when creating order from admin
     */
    public function aroundGetCurrency($subject, $proceed, $amount, $format = false)
    {
        $store = $this->getCurrentStore();
        return $this->priceHelper->currencyByStore($amount, $store, $format);
    }

    /**
     * get current store for admin area
     */
    protected function getCurrentStore()
    {
        if (empty($this->store)) {
            $this->store = $this->adminOrderCreate->getSession()->getStoreId();
        }

        return $this->store;
    }
}
