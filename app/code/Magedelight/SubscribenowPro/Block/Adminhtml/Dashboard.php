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

namespace Magedelight\SubscribenowPro\Block\Adminhtml;

class Dashboard extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Magedelight_SubscribenowPro::dashboard/index.phtml';

    protected $helper;
    protected $chartModel;
    protected $chartPeriodModel;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magedelight\SubscribenowPro\Helper\Data $helper,
        \Magedelight\SubscribenowPro\Model\Config\Source\Charts $chartModel,
        \Magedelight\SubscribenowPro\Model\Config\Source\ChartPeriod $chartPeriodModel,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->helper = $helper;
        $this->chartModel = $chartModel;
        $this->chartPeriodModel = $chartPeriodModel;
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'subscription_statistics',
            \Magedelight\SubscribenowPro\Block\Adminhtml\Dashboard\SubscriptionStatistics::class
        );

        $this->addChild(
            'lastSubscriptions',
            \Magedelight\SubscribenowPro\Block\Adminhtml\Dashboard\Subscriptions\Grid::class
        );

        $this->addChild(
            'topSubscriptionProducts',
            \Magedelight\SubscribenowPro\Block\Adminhtml\Dashboard\TopProducts\Grid::class
        );

        parent::_prepareLayout();
    }

    public function getReportUrl()
    {
        return $this->getUrl('subscribenow/dashboard/ajaxreport');
    }

    public function getAvailableCharts()
    {
        return $this->chartModel->toOptionArray();
    }

    public function getDefaultChart()
    {
        return $this->helper->getDefaultChart();
    }

    public function getDefaultPeriod()
    {
        return $this->helper->getDefaultPeriod();
    }

    public function isChartMultiColor()
    {
        return $this->helper->isChartMultiColor();
    }

    public function getDefaultChartColor()
    {
        return $this->helper->getDefaultChartColor();
    }

    public function isChartToTable()
    {
        return $this->helper->isChartToTable();
    }

    public function getStoreId()
    {
        if ($this->getRequest()->getParam('store')) {
            return $this->getRequest()->getParam('store');
        }

        return null;
    }
}
