<?php
/**
 * *
 * * @author    Tigren Solutions <info@tigren.com>
 * * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

declare(strict_types=1);

namespace Tigren\LookupOrder\Controller\Customer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Tigren\LookupOrder\Helper\Customer;

/**
 * Class Form
 */
class Form extends \Magento\Framework\App\Action\Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var Customer
     */
    private $_helper;

    /**
     * Form constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CustomerSession $customerSession
     * @param Customer $helper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CustomerSession $customerSession = null,
        \Tigren\LookupOrder\Helper\Customer $helper = null
    ) {

        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession ?: ObjectManager::getInstance()->get(CustomerSession::class);
        $this->_helper = $helper ?: ObjectManager::getInstance()->get(Customer::class);
    }

    /**
     * Order view form page
     *
     * @return Redirect|Page
     */
    public function execute()
    {
        $groupId=$this->_helper->getModuleConfig('lookup_order/customer_groups');
        $groupId=explode(',', $groupId);
        $customerGroupId=$this->_helper->getCustomerGroupId();
        if ($this->customerSession->isLoggedIn()) {
            if (is_array($groupId) && in_array($customerGroupId, $groupId)) {
                $resultPage = $this->resultPageFactory->create();

                $resultPage->getConfig()->getTitle()->set(__('Orders and Returns'));
                $this->_helper->getBreadcrumbs($resultPage);

                return $resultPage;
            } else {
                $this->messageManager->addError(__('You are not in this customer group.'));
                return $this->_redirect('customer/account/');
            }
        } else {
            $this->messageManager->addError(__('Please login to use.'));
            return $this->_redirect('customer/account/login');
        }
    }
}
