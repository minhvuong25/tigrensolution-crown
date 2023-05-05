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

namespace Magedelight\SubscribenowPro\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $processingMergeOrder = false;
    const XML_PATH_MD_SUBSCRIBENOWPRO_DEFAULT_CHART = 'md_subscribenow/pro/default_chart';
    const XML_PATH_MD_SUBSCRIBENOWPRO_DEFAULT_PERIOD = 'md_subscribenow/pro/default_period';
    const XML_PATH_MD_SUBSCRIBENOWPRO_CHART_MULTICOLOR = 'md_subscribenow/pro/is_chart_multicolor';
    const XML_PATH_MD_SUBSCRIBENOWPRO_DEFAULT_CHART_COLOR = 'md_subscribenow/pro/default_chart_color';
    const XML_PATH_MD_SUBSCRIBENOWPRO_CHART_TO_TABLE = 'md_subscribenow/pro/chart_to_table';
    /*OrderMerge*/
    const XML_PATH_MD_SUBSCRIBENOWMERGEORDER_ENABLE = 'md_subscribenow/merge_order/enable';
    const XML_PATH_MD_SUBSCRIBENOWMERGEORDER_VALIDATE_QTY = 'md_subscribenow/merge_order/validate_qty';
    const XML_PATH_MD_SUBSCRIBENOWMERGEORDER_FAIL_ORDER_FAIL_ADDTOCART = 'md_subscribenow/merge_order/fail_order_if_fail_addtocart';
    /**
     * @param $config
     * @return mixed
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue($config, ScopeInterface::SCOPE_STORE);
    }

    public function getDefaultChart()
    {
        return $this->getConfig(self::XML_PATH_MD_SUBSCRIBENOWPRO_DEFAULT_CHART);
    }

    public function getDefaultPeriod()
    {
        return $this->getConfig(self::XML_PATH_MD_SUBSCRIBENOWPRO_DEFAULT_PERIOD);
    }

    public function isChartMultiColor()
    {
        return (bool) $this->getConfig(self::XML_PATH_MD_SUBSCRIBENOWPRO_CHART_MULTICOLOR);
    }

    public function getDefaultChartColor()
    {
        return $this->getConfig(self::XML_PATH_MD_SUBSCRIBENOWPRO_DEFAULT_CHART_COLOR);
    }

    public function isChartToTable()
    {
        //return (bool) $this->getConfig(self::XML_PATH_MD_SUBSCRIBENOWPRO_CHART_TO_TABLE);
        return false;
    }
    /*Merge Order Methods*/
    public function isEnable()
    {
        return (bool) $this->getConfig(self::XML_PATH_MD_SUBSCRIBENOWMERGEORDER_ENABLE);
    }

    public function isValidateQty()
    {
        return (bool) $this->getConfig(self::XML_PATH_MD_SUBSCRIBENOWMERGEORDER_VALIDATE_QTY);
    }

    public function failOrderIfFailAddtocart()
    {
        return (bool) $this->getConfig(self::XML_PATH_MD_SUBSCRIBENOWMERGEORDER_FAIL_ORDER_FAIL_ADDTOCART);
    }

    public function setProcessingMergeOrder()
    {
        $this->processingMergeOrder = true;
    }

    public function getProcessingMergeOrder()
    {
        return $this->processingMergeOrder;
    }
}
