<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper;

use Aitoc\FollowUpEmails\Api\Helper\EmailInterface as EmailHelperInterface;
use Aitoc\FollowUpEmails\Api\Helper\EventEmailsGeneratorHelperInterface;

class Email implements EmailHelperInterface
{
    /**
     * Get store id by email attributes
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param array $emailAttributes
     * @return int
     */
    public function getStoreIdByEmailAttributes(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        $emailAttributes
    ) {
        $entity = $this->getEntityByEmailAttributes($eventEmailsGeneratorHelper, $emailAttributes);

        return $eventEmailsGeneratorHelper->getStoreIdByEntity($entity);
    }

    /**
     * Get entity by email attributes
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param array $emailAttributes
     * @return mixed
     */
    public function getEntityByEmailAttributes(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        $emailAttributes
    ) {
        $entityId  = $this->getEntityIdByAttributes($eventEmailsGeneratorHelper, $emailAttributes);

        return $eventEmailsGeneratorHelper->getEntityById($entityId);
    }

    /**
     * Get entity id by attributes
     *
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param array $emailAttributes
     * @return int
     */
    private function getEntityIdByAttributes(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        $emailAttributes
    ) {
        $entityAttributeCode = $eventEmailsGeneratorHelper->getEntityIdAttributeCode();

        return $emailAttributes[$entityAttributeCode];
    }
}
