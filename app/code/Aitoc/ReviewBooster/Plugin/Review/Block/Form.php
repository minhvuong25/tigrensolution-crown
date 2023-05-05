<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Plugin\Review\Block;

use Magento\Review\Block\Form as ReviewForm;
use Magento\Framework\App\Request\Http;

class Form
{
    /**
     * @var Http
     */
    protected $request;

    /**
     * @param Http $request
     */
    public function __construct(
        Http $request
    ) {
        $this->request = $request;
    }

    /**
     * Rewrite template
     *
     * @param ReviewForm $form
     */
    public function beforeToHtml(ReviewForm $form)
    {
        $templateName = 'Aitoc_ReviewBooster::rewrite/review/view/frontend/templates/form.phtml';
        if ($this->request->getFullActionName()
            && $this->request->getFullActionName() === 'reviewboosterorder_product_items'
        ) {
            $templateName = 'Aitoc_ReviewBooster::order/form.phtml';
        }

        $form->setTemplate($templateName);
    }
}
