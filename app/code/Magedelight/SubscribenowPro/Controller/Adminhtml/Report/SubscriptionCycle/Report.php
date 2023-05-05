<?php

namespace Magedelight\SubscribenowPro\Controller\Adminhtml\Report\SubscriptionCycle;

class Report extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Check is allowed for report.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magedelight_SubscribenowPro::report_subscriptioncycle');
    }
    
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            //'Magedelight_SubscribenowPro::report_subscriptioncycle'
            'Magedelight_Base::md_base_root'
        )->_addBreadcrumb(
            __('Subscription Cycle Report'),
            __('Subscription Cycle Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Subscription Cycle Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_reports_subscriptioncycle_report.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
