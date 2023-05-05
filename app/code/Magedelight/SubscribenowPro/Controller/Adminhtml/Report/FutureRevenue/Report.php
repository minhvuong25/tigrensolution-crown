<?php

namespace Magedelight\SubscribenowPro\Controller\Adminhtml\Report\FutureRevenue;

use Magedelight\SubscribenowPro\Model\Flag;

class Report extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Check is allowed for report.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magedelight_SubscribenowPro::report_futurerevenue');
    }
    
    public function execute()
    {
        $this->_showLastExecutionTime(
            Flag::REPORT_SUBSCRIBENOW_FUTUREPRODUCTS_FLAG_CODE,
            'futureproducts_subscription'
        );

        $this->_initAction()->_setActiveMenu(
            //'Magedelight_SubscribenowPro::report_futurerevenue'
            'Magedelight_Base::md_base_root'
        )->_addBreadcrumb(
            __('Future Revenue Report'),
            __('Future Revenue Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Future Revenue Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_reports_futurerevenue_report.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
