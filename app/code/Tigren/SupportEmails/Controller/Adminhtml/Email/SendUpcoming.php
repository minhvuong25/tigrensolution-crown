<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\SupportEmails\Controller\Adminhtml\Email;

use Magento\Framework\Mail\Template\TransportBuilder;

/**
 * Class Send
 * @package Tigren\Callback\Controller\Request
 */
class SendUpcoming extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Tigren\SupportEmails\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * Send constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Tigren\SupportEmails\Helper\Data $helper
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Tigren\SupportEmails\Helper\Data $helper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function execute()
    {
        $emailDatas = $this->_helper->getEmailTest();
        if (!empty($emailDatas)) {
            foreach ($emailDatas as $emailData) {
                $emailSender = $this->_helper->getModuleConfig('support_email/sender');
                $template = $this->_helper->getModuleConfig('support_email/template_upcoming');
                $templateVars = [
                    'discount' => $emailData['discount'],
                    'code' => $emailData['code'],
                    'orderId' => $emailData['incrementId'],
                    'sku' => $emailData['sku'],
                    'price' => $emailData['price'],
                    'productName' => $emailData['name'],
                    'productImage' => $emailData['productImage'],
                    'shortDescription' => $emailData['shortDescription'],
                    'discountPrice' => $emailData['discountPrice'],
                    'productLink' => $emailData['productLink'],
                ];
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($template)
                    ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => 1])
                    ->setTemplateVars($templateVars)
                    ->setFrom($emailSender)
                    ->addTo($emailData['customerEmail'])
                    ->getTransport();
                $transport->sendMessage();
            }
        }
    }
}
