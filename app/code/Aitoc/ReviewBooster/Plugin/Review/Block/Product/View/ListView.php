<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Plugin\Review\Block\Product\View;

use Magento\Review\Block\Product\View\ListView as MagentoListView;

class ListView
{
    /**
     * Rewrite template
     *
     * @param MagentoListView $listView
     */
    public function beforeToHtml(MagentoListView $listView)
    {
        $listView->setTemplate('Aitoc_ReviewBooster::rewrite/review/view/frontend/templates/product/view/list.phtml');
    }
}
