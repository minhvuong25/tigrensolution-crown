<?php
/**
 *
 *  @author    Tigren Solutions <info@tigren.com>
 *  @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\PriceDecimal\Plugin\Block\Item\Price;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem as QuoteItem;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Class Renderer
 * @package Tigren\PriceDecimal\Plugin\Block\Item\Price
 */
class Renderer
{
    /** @var \Magento\Framework\Pricing\PriceCurrencyInterface  */
    protected $priceCurrency;

    /**
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency  = $priceCurrency;
    }

    /**
     * Format price
     *
     * around function Magento\Tax\Block\Item\Price\Renderer::formatPrice
     *
     * @param \Magento\Tax\Block\Item\Price\Renderer $subject
     * @param callable $proceed
     * @param float $price
     * @return string
     */
    public function aroundFormatPrice(
        \Magento\Tax\Block\Item\Price\Renderer $subject,
        callable $proceed,
        $price
    ) {
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION;

        $priceNumber = floor($price);
        $fraction = $price - $priceNumber;
        if ($fraction > 0 && $fraction < 1) {
            //use default
        } else {
            $precision = 0;
        }

        $item = $subject->getItem();
        if ($item instanceof QuoteItem) {
            return $this->priceCurrency->format(
                $price,
                true,
                $precision,
                $item->getStore()
            );
        } elseif ($item instanceof OrderItem) {
            return $item->getOrder()->formatPrice($price);
        } else {
            return $item->getOrderItem()->getOrder()->formatPrice($price);
        }
    }
}
