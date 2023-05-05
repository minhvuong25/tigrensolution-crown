<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Block\Adminhtml\Email;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class PreviewForm extends Template
{
    /**
     * Get preview url
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('adminhtml/email_template/preview');
    }
}
