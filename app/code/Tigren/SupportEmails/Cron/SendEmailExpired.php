<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\SupportEmails\Cron;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Module\Manager;

/**
 * Class SendEmailExpired
 * @package Tigren\SupportEmails\Cron
 */
class SendEmailExpired
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Tigren\SupportEmails\Helper\Data
     */
    protected $_helper;

    /**
     * SendEmailExpired constructor.
     * @param Manager $moduleManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param TransportBuilder $transportBuilder
     * @param \Tigren\SupportEmails\Helper\Data $helper
     */
    public function __construct(
        Manager $moduleManager,
        \Psr\Log\LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        \Tigren\SupportEmails\Helper\Data $helper
    ) {
        $this->moduleManager = $moduleManager;
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
        $this->_helper = $helper;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function execute()
    {
        if ($this->_helper->getModuleConfig('support_email/enable') == 1) {
            try {
                $emailDatas = $this->_helper->getEmailExpired();
                if (!empty($emailDatas)) {
                    foreach ($emailDatas as $emailData) {
                        $emailSender = $this->_helper->getModuleConfig('support_email/sender');
                        $template = $this->_helper->getModuleConfig('support_email/template_past');
                        $templateVars = [
                            'code' => $emailData['code'],
                            'orderId' => $emailData['incrementId'],
                            'discount' => $emailData['discount'],
                            'sku' => $emailData['sku'],
                            'price' => $emailData['price'],
                            'productName' => $emailData['name'],
                            'productImage' => $emailData['productImage'],
                            'shortDescription' => $emailData['shortDescription'],
                            'discountPrice' => $emailData['discountPrice'],
                            'productLink' => $emailData['productLink'],
                        ];
                        $transport = $this->transportBuilder->setTemplateIdentifier($template)
                            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => 1])
                            ->setTemplateVars($templateVars)
                            ->setFrom($emailSender)
                            ->addTo($emailData['customerEmail'])
                            ->getTransport();
                        $transport->sendMessage();
                        $this->logger->info('Cron Works');
                    }
                }
            } catch (Exception $e) {
                $this->logger->error($e);
            }
        }
    }
}
