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

namespace Magedelight\SubscribenowPro\Block\Adminhtml\Report\Filter;

use Magento\Backend\Block\Widget\Form\Generic;

class Form extends \Magento\Reports\Block\Adminhtml\Filter\Form
{
    protected function _prepareForm()
    {
        $actionUrl = $this->getUrl('*/*/sales');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'filter_form',
                    'action' => $actionUrl,
                    'method' => 'get'
                ]
            ]
        );

        $htmlIdPrefix = 'sales_report_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Filter')]);

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);

        $fieldset->addField('store_ids', 'hidden', ['name' => 'store_ids']);

        $fieldset->addField(
            'report_type',
            'select',
            [
                'name' => 'report_type',
                'options' => $this->_reportTypeOptions,
                'label' => __('Date Used')
            ]
        );

        $fieldset->addField(
            'period_type',
            'select',
            [
                'name' => 'period_type',
                'options' => ['day' => __('Day'), 'month' => __('Month'), 'year' => __('Year')],
                'label' => __('Period'),
                'title' => __('Period')
            ]
        );

        $fieldset->addField(
            'from',
            'date',
            [
                'name' => 'from',
                'date_format' => $dateFormat,
                'label' => __('From'),
                'title' => __('From'),
                'required' => true,
                'css_class' => 'admin__field-small',
                'class' => 'admin__control-text'
            ]
        );

        $fieldset->addField(
            'to',
            'date',
            [
                'name' => 'to',
                'date_format' => $dateFormat,
                'label' => __('To'),
                'title' => __('To'),
                'required' => true,
                'css_class' => 'admin__field-small',
                'class' => 'admin__control-text'
            ]
        );

        $fieldset->addField(
            'product_sku',
            'text',
            [
                'name' => 'product_sku',
                'label' => __('SKU'),
                'title' => __('SKU'),
                'filter_condition_callback' => 'filterProductSku',
                'css_class' => 'admin__field-full',
                'class' => 'admin__control-text',
                'note' => __('You can add multiple SKU in comma seprated format. For eg. sku1,sku2')
            ]
        );

        $fieldset->addField(
            'show_empty_rows',
            'select',
            [
                'name' => 'show_empty_rows',
                'options' => ['1' => __('Yes'), '0' => __('No')],
                'label' => __('Empty Rows'),
                'title' => __('Empty Rows')
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return Generic::_prepareForm();
    }
}
