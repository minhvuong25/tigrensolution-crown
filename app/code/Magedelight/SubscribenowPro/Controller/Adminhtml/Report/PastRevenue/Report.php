<?php

namespace Magedelight\SubscribenowPro\Controller\Adminhtml\Report\PastRevenue;

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
        return $this->_authorization->isAllowed('Magedelight_SubscribenowPro::report_pastrevenue');
    }
    
    public function execute()
    {
        $this->_showLastExecutionTime(
            Flag::REPORT_SUBSCRIBENOW_PASTREVENUE_FLAG_CODE,
            'subscribenow_pastrevenue'
        );

        $this->_initAction()->_setActiveMenu(
            //'Magedelight_SubscribenowPro::report_pastrevenue'
            'Magedelight_Base::md_base_root'
        )->_addBreadcrumb(
            __('Past Revenue Report'),
            __('Past Revenue Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Past Revenue Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_reports_pastrevenue_report.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
