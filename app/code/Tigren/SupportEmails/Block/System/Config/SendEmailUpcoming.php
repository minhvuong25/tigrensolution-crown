<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\SupportEmails\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Send
 * @package Tigren\SupportEmails\Block\System\Config
 */
class SendEmailUpcoming extends Field
{
    /** @var UrlInterface */
    protected $_urlBuilder;

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        $this->_urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $data);
    }

    /**
     * Set template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Tigren_SupportEmails::system/config/send_email_upcoming.phtml');
    }

    /**
     * Generate button html
     *
     * @return string
     * @throws LocalizedException
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            Button::class
        )->setData(
            [
                'id' => 'send_button',
                'label' => __('Send Email')
            ]
        );

        return $button->toHtml();
    }

    /**
     * @return string
     */
    public function getAdminUrl()
    {
        return $this->_urlBuilder->getUrl(
            'supportemails/email/sendupcoming',
            ['store' => $this->_request->getParam('store')]
        );
    }

    /**
     * Render button
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
