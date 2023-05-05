<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\Adminhtml\Event\Campaign;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('event_code')) {
            return $this->_redirect('followup/event/index');
        }
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
