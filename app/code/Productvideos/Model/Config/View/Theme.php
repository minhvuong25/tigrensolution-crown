<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Productvideos
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Productvideos\Model\Config\View;

class Theme implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return [['value' => 'slider', 'label' => __('Slider')],
                ['value' => 'nanogallery', 'label' => __('Nano Gallery')],
                ['value' => 'rnt', 'label' => __('Right with No Thumb')],
                ['value' => 'rwt', 'label' => __('Right with Thumb')],
                ['value' => 'rto', 'label' => __('Right Title Only')]
            ];
    }
}
