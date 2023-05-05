<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Tigren\PriceDecimal\Ui\DataProvider\Product\Modifier;

/**
 * Modify product listing attributes
 */
class Attributes extends \Magento\Catalog\Ui\DataProvider\Product\Modifier\Attributes
{

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        if (!empty($data)) {
            foreach ($data['items'] as &$item) {
                if (!empty($item['special_price'])) {
                    $item['special_price'] = number_format((float)($item['special_price']), 2, '.', '');
                }
            }
        }
        return $data;
    }
}
