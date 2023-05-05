<?php
/**
 * *
 * * @author    Tigren Solutions <info@tigren.com>
 * * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\LookupOrder\Controller\Customer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class View
 * @package Tigren\LookupOrder\Controller\Customer
 */
class View extends Action\Action implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * @var \Tigren\LookupOrder\Helper\Customer
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CustomerSession|null
     */
    private $customerSession;

    /**
     * @var Validator|mixed|null
     */
    private $formKeyValidator;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Tigren\LookupOrder\Helper\Customer $helper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Validator|null $formKeyValidator
     * @param CustomerSession|null $customerSession
     */
    public function __construct(
        Action\Context $context,
        \Tigren\LookupOrder\Helper\Customer $helper,
        PageFactory $resultPageFactory,
        Validator $formKeyValidator = null,
        CustomerSession $customerSession = null
    ) {
        $this->_helper = $helper;
        $this->resultPageFactory = $resultPageFactory;
        $this->formKeyValidator = $formKeyValidator ?: ObjectManager::getInstance()->get(Validator::class);
        $this->customerSession = $customerSession ?: ObjectManager::getInstance()->get(CustomerSession::class);
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage('Invalid Form Key. Please refresh the page');
            return $this->resultRedirectFactory->create()->setPath('lookuporder/customer/form');
        }

        $groupId=$this->_helper->getModuleConfig('lookup_order/customer_groups');
        $groupId=explode(',', $groupId);
        $customerGroupId=$this->_helper->getCustomerGroupId();
        if ($this->customerSession->isLoggedIn()) {
            if (is_array($groupId) && in_array($customerGroupId, $groupId)) {
                $result = $this->_helper->loadValidOrder($this->getRequest());
                if ($result instanceof ResultInterface) {
                    return $result;
                }
                /** @var \Magento\Framework\View\Result\Page $resultPage */
                $resultPage = $this->resultPageFactory->create();
                $this->_helper->getBreadcrumbs($resultPage);
                return $resultPage;
            } else {
                $this->_redirect('/');
                return;
            }
        } else {
            $this->messageManager->addError('Please login to use.');
            $this->_redirect('customer/account/login');
            return;
        }
    }
}
