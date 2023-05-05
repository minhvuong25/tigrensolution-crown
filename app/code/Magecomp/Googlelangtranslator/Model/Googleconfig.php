<?php
namespace Magecomp\Googlelangtranslator\Model;

class Googleconfig implements \Magecomp\Googlelangtranslator\Api\GoogletranslateInterface
{
    protected $helper;
    public function __construct(
        \Magecomp\Googlelangtranslator\Helper\Data $helper
    ) {
        $this->helper = $helper; 
    }
    public function GoogleTranslate(){

        try{
            if($this->helper->isEnabled()){               
                $response = [   
                    'status' => true,
                    'message' => __('Google Language Translate is Enabled.'),
                    'selectedLanguage' => $this->helper->SelectLanguage(),
                    'layout' => $this->helper->SelectLayout()                    
                ]; 
            }else{
                $response = [   
                    'status' => false,
                    'message' => __('Google Language Translate is Disabled.')
                ]; 

            }

            return json_encode($response);
        }
        catch (\Exception $e)
        {
            throw new AuthenticationException(__($e->getMessage()));
        }

        }

    
}
