<?php
/**
 * *
 * * @author    Tigren Solutions <info@tigren.com>
 * * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\LookupOrder\Model\System\Config\Source\Customer;

/**
 * Class Group
 * @package Tigren\LookupOrder\Model\System\Config\Source\Customer
 */
class Group implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $_groupCollectionFactory;

    /**
     * @var
     */
    protected $_options;

    /**
     * Group constructor.
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
     */
    public function __construct(\Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory)
    {
        $this->_groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_groupCollectionFactory->create()->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
