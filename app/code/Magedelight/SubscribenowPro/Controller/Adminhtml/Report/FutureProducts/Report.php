<?php

namespace Magedelight\SubscribenowPro\Controller\Adminhtml\Report\FutureProducts;

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
        return $this->_authorization->isAllowed('Magedelight_SubscribenowPro::report_futureproducts');
    }
    
    public function execute()
    {
        $this->_showLastExecutionTime(
            Flag::REPORT_SUBSCRIBENOW_FUTUREPRODUCTS_FLAG_CODE,
            'futureproducts_subscription'
        );

        $this->_initAction()->_setActiveMenu(
            //'Magedelight_SubscribenowPro::report_futureproducts'
            'Magedelight_Base::md_base_root'
        )->_addBreadcrumb(
            __('Future Products Report'),
            __('Future Products Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Future Products Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_reports_futureproducts_report.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
