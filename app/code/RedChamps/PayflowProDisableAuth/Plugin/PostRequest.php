<?php
namespace RedChamps\PayflowProDisableAuth\Plugin;

use Magento\Framework\DataObject;
use Magento\Payment\Model\Method\ConfigInterface;
use Magento\Paypal\Model\Payflowpro;

class PostRequest
{
    public function beforePostRequest(Payflowpro $subject, DataObject $request, ConfigInterface $config)
    {
        if ($request->getCreatesecuretoken() == 'Y' && $request->getSilenttran() == 'TRUE') {
            $request->setTrxtype(Payflowpro::TRXTYPE_SALE)
                ->setData('comment1', 'Token Request')
                ->setData('comment2', 'Ignore the "Invalid amount" Error');
        }
        return [$request, $config];
    }
}