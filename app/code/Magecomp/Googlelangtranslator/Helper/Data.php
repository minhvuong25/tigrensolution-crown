<?php
namespace Magecomp\Googlelangtranslator\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ENABLE = 'googlelangtranslator/general/enable';
    const GOOGLE_LANGUAGE = 'googlelangtranslator/general/googlelanguage';
    const GOOGLE_LAYOUT = 'googlelangtranslator/general/customlayout';
    const BASH_LANGUAGE = 'general/locale/code';
    protected $_storeManager;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::ENABLE,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function BashLanguage()
    {
        return $this->scopeConfig->getValue(self::BASH_LANGUAGE,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function SelectLanguage()
    {
        return $this->scopeConfig->getValue(self::GOOGLE_LANGUAGE,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function SelectLayout()
    {
        return $this->scopeConfig->getValue(self::GOOGLE_LAYOUT,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}