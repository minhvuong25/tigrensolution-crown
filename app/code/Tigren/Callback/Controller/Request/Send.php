<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\Callback\Controller\Request;

use Magento\Framework\Mail\Template\TransportBuilder;

/**
 * Class Send
 * @package Tigren\Callback\Controller\Request
 */
class Send extends \Magento\Framework\App\Action\Action
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
     * @var \Tigren\Callback\Helper\Config
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
     * @param \Tigren\Callback\Helper\Config $helper
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Tigren\Callback\Helper\Config $helper,
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
        $post = $this->getRequest()->getPostValue();
        if (!empty($post)) {
            $data = [
                'name' => $this->getRequest()->getPost('name'),
                'phone' => $this->getRequest()->getPost('phone'),
            ];

            $emailSender = $this->_helper->getModuleConfig('admin_notify/sender');
            $emailTo = $this->_helper->getModuleConfig('admin_notify/email_to');
            $template = $this->_helper->getModuleConfig('admin_notify/template');
            if (!empty($emailTo)) {
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($template)
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )->setTemplateVars($data)
                    ->setFrom($emailSender)
                    ->addTo($emailTo)
                    ->getTransport();
                $transport->sendMessage();

            } else {
                $this->_redirect('/');
                return;
            }
        }
    }
}
