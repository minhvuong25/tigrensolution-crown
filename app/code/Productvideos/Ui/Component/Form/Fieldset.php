<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_Productvideos
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Productvideos\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\FieldFactory;
use Magento\Ui\Component\Form\Fieldset as BaseFieldset;

class Fieldset extends BaseFieldset
{
    private $fieldFactory;
    protected $helper;

    public function __construct(
        ContextInterface $context,
        \FME\Productvideos\Helper\Data $helper,
        FieldFactory $fieldFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->fieldFactory = $fieldFactory;
        $this->helper = $helper;
    }

    public function getChildComponents()
    {
        $fields = [
            [
                'label' => __('youtubekey'),
                'value' => __($this->helper->getYouTubeApiKey()),
                'formElement' => 'input',
            ]
        ];
        foreach ($fields as $k => $fieldConfig) {
            $fieldInstance = $this->fieldFactory->create();
            $name = 'youtube_key';
            $fieldInstance->setData(
                [
                    'config' => $fieldConfig,
                    'name' => $name
                ]
            );
            $fieldInstance->prepare();
            $this->addComponent($name, $fieldInstance);
        }
        return parent::getChildComponents();
    }
}
