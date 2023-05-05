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

namespace Magedelight\SubscribenowPro\Model\Report;

use Magedelight\SubscribenowPro\Helper\Data as Helper;

abstract class AbstractReport
{
    protected $data = [];
    protected $helper;
    public $storeId = null;
    public $reportType;
    public $from;
    public $to;
    public $group;

    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    public function setPostParams($data)
    {
        $this->storeId = $data['storeId'];
        $this->reportType = $data['reportType'];
        $this->from = $data['from'];
        $this->to = $data['to'];
        $this->group = $data['group'];

        return $this;
    }

    abstract public function getReport();

    public function getAdminTimezoneDateSql($column = 'created_at')
    {
        $adminTimeZone = $this->helper->getConfig('general/locale/timezone');
        //return "CONVERT_TZ($column, 'GMT', '$adminTimeZone')";
        return "CONVERT_TZ($column, '+00:00', '".$this->getTimezoneOffsetFormat($adminTimeZone)."')";
    }

    public function getTimezoneOffsetFormat($adminTimeZone)
    {
        $offset = $this->getTimezoneOffset($adminTimeZone);
        $sign = '+';

        if ($offset < 0) {
            $sign = '-';
            $offset = $offset * -1;
        }

        $hour = floor($offset / 3600);
        $minutes = ($offset / 60) % 60;

        $format = $sign.sprintf('%02d', $hour).':'.sprintf('%02d', $minutes);

        return $format;
    }

    public function getTimezoneOffset($remoteTz, $originTz = null)
    {
        if ($originTz === null) {
            if (!is_string($originTz = date_default_timezone_get())) {
                return false; // A UTC timestamp was returned -- bail out!
            }
        }
        $originDtz = new \DateTimeZone($originTz);
        $remoteDtz = new \DateTimeZone($remoteTz);
        $originDt = new \DateTime("now", $originDtz);
        $remoteDt = new \DateTime("now", $remoteDtz);
        $offset = $remoteDtz->getOffset($remoteDt) - $originDtz->getOffset($originDt);
        
        return $offset;
    }
    
    public function formatEmptyColumns()
    {
        $result = [];

        foreach ($this->data as $row) {
            $result[$row['created_at']] = $row;
        }

        $initialKey = $this->getXAxisValueFromGroup();
        $loopCount = $this->getLoopCount();
        for ($i=1; $i<=$loopCount; $i++) {
            if (!isset($result[$initialKey])) {
                $result[$initialKey] = ['created_at' => $initialKey, 'value' => 0];
            }

            $initialKey = $this->getXAxisValueFromGroup($i);
        }

        ksort($result);
        $result = array_values($result);

        if ($result) {
            foreach ($result as &$row) {
                $row['created_at'] = $this->convertXAxisValue($row['created_at']);
            }
        }

        return $result;
    }

    public function convertDataToAxisFormat($data, $axis)
    {
        $result = [];
        if ($data) {
            foreach ($data as $row) {
                $x = $row[$axis['x']['key']];
                $y = $row[$axis['y']['key']];

                switch ($axis['y']['type']) {
                    case 'int':
                        $y = (int) $y;
                        break;

                    case 'decimal':
                        $y = (float) $y;
                        break;
                    default:
                        break;
                }

                $result[] = [
                    'x' => $x,
                    'y' => $y
                ];
            }
        }

        return $result;
    }

    public function getLoopCount()
    {
        $from = strtotime($this->from);
        $to = strtotime($this->to);

        switch ($this->group) {
            case 'day':
            default:
                $diff = (($to - $from) / (3600 * 24)) + 1;
                break;

            case 'month':
                $diff = (($to - $from) / (3600 * 24 * 31));
                break;

            case 'year':
                $diff = (($to - $from) / (3600 * 24 * 366));
                break;
        }

        return ceil($diff);
    }

    public function getXAxisValueFromGroup($i = 0)
    {
        switch ($this->group) {
            case 'day':
            default:
                $value = date('Y-m-d', strtotime($this->from.' +'.$i.' days'));
                break;
            
            case 'month':
                $value = date('Y-m', strtotime($this->from.' +'.$i.' months'));
                break;

            case 'year':
                $value = date('Y', strtotime($this->from.' +'.$i.' years'));
                break;
        }

        return $value;
    }

    public function convertXAxisValue($value)
    {
        switch ($this->group) {
            case 'day':
            default:
                $value = date('M-d', strtotime($value));
                break;
            
            case 'month':
                $value = date('M-Y', strtotime($value));
                break;

            case 'year':
                $value = date('Y', strtotime($value));
                break;
        }

        return $value;
    }

    public function addStoreFilterToCollection($collection)
    {
        if ($this->storeId) {
            $collection->addFieldToFilter('store_id', $this->storeId);
        }
    }

    public function getData()
    {
        return $this->data;
    }
}
