<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Block\Adminhtml\Thumbnail\Edit\Tab;

use Tigren\ProductThumbnail\Model\Status;

/**
 * Class Form
 * @package Tigren\ProductThumbnail\Block\Adminhtml\Thumbnail\Edit\Tab
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;

    /**
     * @var \Tigren\ProductThumbnail\Model\Thumbnail
     */

    protected $_productthumbnail;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Tigren\ProductThumbnail\Helper\Data
     */
    protected $_helper;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\DataObjectFactory $objectFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Tigren\ProductThumbnail\Model\Thumbnail $productthumbnail
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Tigren\ProductThumbnail\Model\Thumbnail $productthumbnail,
        \Tigren\ProductThumbnail\Helper\Data $helper,
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_productthumbnail = $productthumbnail;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * prepare layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());
        return $this;
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('productthumbnail');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('magic_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Thumbnail Information')]);

        if ($model->getId()) {
            $fieldset->addField('productthumbnail_id', 'hidden', ['name' => 'productthumbnail_id']);
        }

        $fieldset->addField('title', 'text',
            [
                'label' => __('Title'),
                'title' => __('Title'),
                'name'  => 'title',
                'required' => true,
            ]
        );

        $fieldset->addField('image', 'file',
            [
                'label' => __('Upload Image'),
                'title' => __('Upload Image'),
                'name'  => 'image',
                'required' => $model->getId() ? false : true,
                'after_element_html' => $this->getImageHtml('image', $model->getImage())
            ]
        );

        $fieldset->addField('status', 'select',
            [
                'label' => __('Active'),
                'title' => __('Active'),
                'name' => 'status',
                'value' => '1',
                'options' => Status::getAvailableStatuses(),
            ]
        );

        $form->addValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param $field
     * @param $image
     * @return string
     */
    protected function getImageHtml($field, $image)
    {
        $html = '';
        if ($image) {
            $html .= '<p style="margin-top: 5px">';
            $html .= '<image style="width:50px; height:50px;" src="' . $this->_helper->getImageUrl($image) . '" />';
            $html .= '<input type="hidden" value="' . $image . '" name="old_' . $field . '"/>';
            $html .= '</p>';
        }
        return $html;
    }
    /**
     * @return mixed
     */
    public function getProductThumbnail()
    {
        return $this->_coreRegistry->registry('productthumbnail');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getProductThumbnail()->getId()
            ? __("Edit Thumbnail '%1'", $this->escapeHtml($this->getProductThumbnail()->getTitle())) : __('New Thumbnail');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
