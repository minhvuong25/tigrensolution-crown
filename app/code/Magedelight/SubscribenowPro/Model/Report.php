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

namespace Magedelight\SubscribenowPro\Model;

use Magedelight\SubscribenowPro\Model\Report as ProReport;

class Report
{
    protected $subscriptionReport;
    protected $orderReport;
    protected $amountReport;

    public function __construct(
        ProReport\SubscriptionReport $subscriptionReport,
        ProReport\OrderReport $orderReport,
        ProReport\AmountReport $amountReport
    ) {
        $this->subscriptionReport = $subscriptionReport;
        $this->orderReport = $orderReport;
        $this->amountReport = $amountReport;
    }

    public function getReportFromDateRange($params)
    {
        $axis = [
            'x' => [
                'key'  => 'created_at',
                'type' => 'string'
            ],
            'y' => [
                'key' => 'value',
                'type' => 'int'
            ]
        ];

        switch ($params['reportType']) {
            case 'subscription':
            default:
                $reportModel = $this->subscriptionReport;
                break;
            
            case 'order':
                $reportModel = $this->orderReport;
                break;

            case 'amount':
                $axis['y']['type'] = 'decimal';
                $axis['y']['point'] = 2;
                $reportModel = $this->amountReport;
                break;
        }

        $data = [];
        $reportModel->setPostParams($params)->getReport();
        if ($reportModel->getData()) {
            $data = $reportModel->formatEmptyColumns();
            $data = $reportModel->convertDataToAxisFormat($data, $axis);
        }

        return $data;
    }
}
