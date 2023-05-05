<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_SubscribenowPro
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\SubscribenowPro\Controller\Adminhtml\Dashboard;

class Ajaxreport extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magedelight\SubscribenowPro\Model\Report $reportModel
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->reportModel = $reportModel;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store_id');
        $reportType = $this->getRequest()->getParam('report_type');
        $from = $this->getRequest()->getParam('from');
        $to = $this->getRequest()->getParam('to');
        $group = $this->getRequest()->getParam('group');

        $reportData = $this->reportModel->getReportFromDateRange([
            'storeId'   => $storeId,
            'reportType' => $reportType,
            'from'       => $from,
            'to'         => $to,
            'group'      => $group
        ]);

        $result = $this->resultJsonFactory->create();

        $result->setData([
            'from' => $this->getRequest()->getParam('from'),
            'to' => $this->getRequest()->getParam('to'),
            'reportData' => $reportData
        ]);

        return $result;
    }
}
