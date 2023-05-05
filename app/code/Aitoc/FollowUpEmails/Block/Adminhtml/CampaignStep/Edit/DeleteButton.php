<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Block\Adminhtml\CampaignStep\Edit;

use Aitoc\FollowUpEmails\Block\Adminhtml\CampaignStep\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $campaignId = $this->getCampaignStepId();
        if ($campaignId) {
            $data = [
                'label' => __('Delete Email'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' .
                    __('Are you sure you want to do this?') .
                    '\', \'' .
                    $this->urlBuilder->getUrl('*/*/delete', ['id' => $campaignId]) . '\')',
                'sort_order' => 20
            ];
        }

        return $data;
    }
}
