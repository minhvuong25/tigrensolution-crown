<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Ui\Component\Listing\DataProvider\Event;

use Magento\Framework\UrlInterface;
use Magento\Framework\Registry;
use Magento\Framework\AuthorizationInterface;
use Magento\Ui\Component;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class Email implements ModifierInterface
{
    /**
     * @var UrlInterface
     * @since 101.0.0
     */
    protected $urlBuilder;

    /**
     * @var Registry
     * @since 101.0.0
     */
    protected $registry;

    /**
     * @var LocatorInterface
     * @since 101.0.0
     */
    protected $locator;

    /**
     * @var AuthorizationInterface
     * @since 101.0.0
     */
    protected $authorization;

    /**
     * Email constructor.
     *
     * @param UrlInterface $urlBuilder
     * @param Registry $registry
     * @param AuthorizationInterface $authorization
     * @param LocatorInterface $locator
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Registry $registry,
        AuthorizationInterface $authorization,
        LocatorInterface $locator
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->registry = $registry;
        $this->authorization = $authorization;
        $this->locator = $locator;
    }

    /**
     * Modify data
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
