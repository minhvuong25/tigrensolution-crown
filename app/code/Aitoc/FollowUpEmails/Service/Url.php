<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Service;

use Magento\Backend\Model\UrlInterface;

class Url
{
    const ROUTE_PATH_UNSUBSCRIBE = 'followup/email/unsubscribe';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Url constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get unsubscribe url
     *
     * @return string
     */
    public function getUnsubscribeUrl()
    {
        return $this->getUrl(self::ROUTE_PATH_UNSUBSCRIBE);
    }

    /**
     * Get url
     *
     * @param string $routePath
     * @return string
     */
    private function getUrl($routePath)
    {
        return $this->urlBuilder->getUrl($routePath);
    }
}
