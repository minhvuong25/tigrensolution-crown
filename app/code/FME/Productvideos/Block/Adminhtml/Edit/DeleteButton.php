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
namespace FME\Productvideos\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getBlockId()) {
            $data = [
                     'label'      => __('Delete Block'),
                     'class'      => 'delete',
                    'on_click'   => 'deleteConfirm(\''.__(
                        'Are you sure you want to do this?'
                    ).'\', \''.$this->getDeleteUrl().'\')',
                     'sort_order' => 20,
                    ];
        }

        return $data;
    }//end getButtonData()
    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['video_id' => $this->getBlockId()]);
    }//end getDeleteUrl()
}//end class
