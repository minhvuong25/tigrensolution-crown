<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use \Magento\SalesRule\Model\Data\Rule;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;

class SaleRuleOptions implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * SaleRuleOptions constructor.
     *
     * @param CollectionFactory $ruleCollectionFactory
     */
    public function __construct(
        CollectionFactory $ruleCollectionFactory
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    /**
     * Get options
     *
     * @return array[]
     */
    public function toOptionArray()
    {
        $options = [
            ['label' => __('--Please Select--'), 'value' => '']
        ];

        $ruleCollection = $this->ruleCollectionFactory->create()
            ->addFieldToFilter(
                \Magento\SalesRule\Model\Data\Rule::KEY_COUPON_TYPE,
                ['neq' => \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON]
            );
        foreach ($ruleCollection as $rule) {
            $options[] = ['label' => $rule->getName(), 'value' => $rule->getId()];
        }

        return $options;
    }
}
