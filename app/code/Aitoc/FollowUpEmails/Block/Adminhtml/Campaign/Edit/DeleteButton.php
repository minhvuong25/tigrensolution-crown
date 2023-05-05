<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Block\Adminhtml\Campaign\Edit;

use Aitoc\FollowUpEmails\Block\Adminhtml\Campaign\GenericButton;
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
        return ($campaignId = $this->getRequestedCampaignId())
            ? $this->getButtonDataByCampaignId($campaignId)
            : [];
    }

    /**
     * Get campaign id
     *
     * @return int|null
     */
    protected function getRequestedCampaignId()
    {
        return $this->getRequestedParam('id');
    }

    /**
     * Get params
     *
     * @param string $paramName
     * @return int|null
     */
    protected function getRequestedParam($paramName)
    {
        return $this->context->getRequest()->getParam($paramName);
    }

    /**
     * Get button data by campaign id
     *
     * @param int $campaignId
     * @return array
     */
    protected function getButtonDataByCampaignId($campaignId)
    {
        $questionMessage =  __('Are you sure you want to do this?');
        $deleteCampaignUrl = $this->getDeleteCampaignUrl($campaignId);

        return [
            'label' => __('Delete Campaign'),
            'class' => 'delete',
            'on_click' => "deleteConfirm('{$questionMessage}', '$deleteCampaignUrl')",
            'sort_order' => 20
        ];
    }

    /**
     * Get delete campaign url
     *
     * @param int $campaignId
     * @return string
     */
    protected function getDeleteCampaignUrl($campaignId)
    {
        return $this->urlBuilder->getUrl(
            '*/*/deletecampaign',
            ['id' => $campaignId]
        );
    }
}
