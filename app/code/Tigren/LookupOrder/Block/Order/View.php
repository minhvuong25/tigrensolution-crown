<?php
/**
 * *
 * * @author    Tigren Solutions <info@tigren.com>
 * * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\LookupOrder\Block\Order;

use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory ;
use Magento\Customer\Model\Session;
/**
 * Class View
 * @package Tigren\LookupOrder\Block\Order
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Tigren_LookupOrder::order/view.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;
    protected $collectionFactory;
    protected $session;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Payment\Helper\Data $paymentHelper,
        AddressRenderer $addressRenderer,
        CollectionFactory $collectionFactory,
        Session $session,
        RequestInterface $request,
        array $data = []
    ) {
        $this->session =$session ;
        $this->request = $request;
        $this->_paymentHelper = $paymentHelper;
        $this->_coreRegistry = $registry;
        $this->addressRenderer = $addressRenderer;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * @return View
     */
    protected function _prepareLayout()
    {
        ;
        $this->pageConfig->getTitle()->set(__('Orders Information'));
        $page_size = $this->getPagerCount();

        if ($this->getCustomData()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'custom.pager.name'
            )
                ->setAvailableLimit($page_size)
                ->setShowPerPage(false)
                ->setCollection($this->getCustomData());
            $this->setChild('pager', $pager);
            $this->getCustomData()->load();

        }
        return  $this ;
    }
    public function getCustomData()
    {
        // get param values
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 1; // set minimum records
        // get custom collection
        $orderIds = $this->session->getListId();
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('entity_id',['in'=>$orderIds]);
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }
    public function getPagerCount()
    {
        // get collection
        $minimum_show = 1; // set minimum records
        $page_array = [];
        $orderIds = $this->_coreRegistry->registry('current_order');
        $list_data = $this->collectionFactory->create();
        $list_data->addFieldToFilter('entity_id',['in'=>$orderIds])->getItems();
        $list_count = ceil(count($list_data->getData()));
        $show_count = $minimum_show + 1;
        if (count($list_data->getData()) >= $show_count) {
            $list_count = $list_count / $minimum_show;
            $page_nu = $total = $minimum_show;
            $page_array[$minimum_show] = $minimum_show;
            for ($x = 0; $x <= $list_count; $x++) {
                $total = $total + $page_nu;
                $page_array[$total] = $total;
            }
        } else {
            $page_array[$minimum_show] = $minimum_show;
            $minimum_show = $minimum_show + $minimum_show;
            $page_array[$minimum_show] = $minimum_show;
        }
        return $page_array;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getCurrentOrder()
    {
        $orderIds = $this->_coreRegistry->registry('current_order');

        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter('entity_id',['in'=>$orderIds])->getItems();
        return $collection ;
    }


    public function getPagerHtml()
    {

        return $this->getChildHtml('pager');
    }

    /**
     * @param Address $address
     * @return string|null
     */
    public function getFormattedAddress(Address $address)
    {
        return $this->addressRenderer->format($address, 'html');
    }
}
