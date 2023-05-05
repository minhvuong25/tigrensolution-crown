<?php
/**
 *
 *  @author    Tigren Solutions <info@tigren.com>
 *  @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\PriceDecimal\Plugin\Pricing\Render;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Amount
 * @package Tigren\PriceDecimal\Plugin\Pricing\Render
 */
class Amount
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
     * Format price value
     *
     * around function of Magento\Framework\Pricing\Render\Amount::formatCurrency()
     *
     * @param \Magento\Framework\Pricing\Render\Amount $subject
     * @param callable $proceed
     * @param $price
     * @param bool $includeContainer
     * @param int $precision
     * @return float
     */
    public function aroundFormatCurrency(
        \Magento\Framework\Pricing\Render\Amount $subject,
        callable $proceed,
        $price,
        $includeContainer = true,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        $priceNumber = floor($price);
        $fraction = $price - $priceNumber;
        if ($fraction > 0 && $fraction < 1) {
            //use default
        } else {
            $precision = 0;
        }

        return $this->priceCurrency->format($price, $includeContainer, $precision);
    }
}
