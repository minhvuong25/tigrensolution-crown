<?php

namespace Magedelight\SubscribenowPro\Controller\Adminhtml\Report\FutureProducts;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magedelight\SubscribenowPro\Block\Adminhtml\Reports\Futureproducts;

class ExportReportExcel extends \Magento\Reports\Controller\Adminhtml\Report\Sales
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
    
    /**
     * Export bestsellers report grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'subscribenow_futureproducts.xml';
        $grid = $this->_view->getLayout()->createBlock(Futureproducts\Report\Grid::class);
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName), DirectoryList::VAR_DIR);
    }
}
