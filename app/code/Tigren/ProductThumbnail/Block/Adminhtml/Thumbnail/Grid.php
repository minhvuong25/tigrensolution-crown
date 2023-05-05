<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Block\Adminhtml\Thumbnail;

use Tigren\ProductThumbnail\Model\Status;

/**
 * Class Grid
 * @package Tigren\ProductThumbnail\Block\Adminhtml\Thumbnail
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail\CollectionFactory
     */
    protected $_thumbnailCollectionFactory;

    /**
     * construct.
     *
     * @param \Magento\Backend\Block\Template\Context                         $context
     * @param \Magento\Backend\Helper\Data                                    $backendHelper
     * @param \Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail\CollectionFactory $thumbnailCollectionFactory
     * @param array                                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail\CollectionFactory $thumbnailCollectionFactory,

        array $data = []
    ) {
        $this->_thumbnailCollectionFactory = $thumbnailCollectionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('thumbnailGrid');
        $this->setDefaultSort('productthumbnail_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $store = $this->getRequest()->getParam('store');
        $collection = $this->_thumbnailCollectionFactory->create();
        if($store) $collection->addFieldToFilter('stores',array( array('finset' => 0), array('finset' => $store)));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'type' => 'text',
                'index' => 'title',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name',
            ]
        );

        $this->addColumn(
            'image',
            [
                'header' => __('Image'),
                'class' => 'xxx',
                'width' => '50px',
                'filter' => false,
                'renderer' => 'Tigren\ProductThumbnail\Block\Adminhtml\Helper\Renderer\Grid\Image',
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Active'),
                'index' => 'status',
                'type' => 'options',
                'options' => Status::getAvailableStatuses(),
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => ['base' => '*/*/edit'],
                        'field' => 'productthumbnail_id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * get thumbnail vailable option
     *
     * @return array
     */

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('productthumbnail_id');
        $this->getMassactionBlock()->setFormFieldName('productthumbnail');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('productthumbnail/*/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );

        $this->getMassactionBlock()->addItem(
            'status',
                [
                'label'      => __('Change status'),
                'url'        => $this->getUrl('productthumbnail/*/massStatus', ['_current'=>true]),
                'additional' => [
                    'status' => [
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  =>__('Status'),
                        'values' => [
                            '1' => __('Enabled'),
                            '0' => __('Disabled'),
                        ]
                    ]
                ]
            ]
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * get row url
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['productthumbnail_id' => $row->getId()]
        );
    }
}
