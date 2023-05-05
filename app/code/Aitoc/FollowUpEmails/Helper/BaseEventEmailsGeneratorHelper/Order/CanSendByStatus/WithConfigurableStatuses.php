<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper\Order\CanSendByStatus;

use Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper\Order\CanSendByStatus as CanSendByStatusEmailGeneratorHelper;
use Magento\Sales\Api\Data\OrderInterface;

abstract class WithConfigurableStatuses extends CanSendByStatusEmailGeneratorHelper
{
    /**
     * Get email attributes by entity
     *
     * @param OrderInterface $entity
     * @return array
     */
    public function getEmailAttributesByEntity($entity)
    {
        $attributes = parent::getEmailAttributesByEntity($entity);
        $attributes[self::ATTRIBUTE_CODE_ORDER_STATUS] = $entity->getStatus();

        return $attributes;
    }
}
