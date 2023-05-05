<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Helper;

interface EmailInterface
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
    );

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
    );
}
