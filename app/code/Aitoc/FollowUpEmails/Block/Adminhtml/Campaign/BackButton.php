<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Block\Adminhtml\Campaign;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $backUrl = $this->getBackUrl();

        return [
            'label' => __('Back'),
            'on_click' => "location.href = '{$backUrl}';",
            'class' => 'back',
            'sort_order' => 10,
        ];
    }

    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getEventsUrl();
    }

    /**
     * Get event url
     *
     * @return string
     */
    protected function getEventsUrl()
    {
        return $this->getUrl('followup/event/index');
    }
}
