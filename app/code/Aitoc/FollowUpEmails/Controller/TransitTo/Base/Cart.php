<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\TransitTo\Base;

use Aitoc\FollowUpEmails\Controller\TransitTo\Base as BaseTransitToAction;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

abstract class Cart extends BaseTransitToAction
{
    const ROUTE_PATH_CHECKOUT_CART = 'checkout/cart';

    /**
     * Get result redirect
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    protected function getResultRedirect(RequestInterface $request)
    {
        return $this->redirectToCheckoutCart();
    }

    /**
     * Redirect to checkout cart
     *
     * @return ResponseInterface
     */
    private function redirectToCheckoutCart()
    {
        return $this->_redirect(self::ROUTE_PATH_CHECKOUT_CART);
    }
}
