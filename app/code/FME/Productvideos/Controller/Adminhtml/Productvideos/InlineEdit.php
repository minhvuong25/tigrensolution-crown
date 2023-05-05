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
namespace FME\Productvideos\Controller\Adminhtml\Productvideos;

use Magento\Backend\App\Action\Context;
use FME\Productvideos\Model\Productvideos as Productvideos;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var Productvideos
     */
    protected $productvideos;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context           $context
     * @param PostDataProcessor $dataProcessor
     * @param Productvideos     $productvideos
     * @param JsonFactory       $jsonFactory
     */
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        Productvideos $productvideos,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->productvideos = $productvideos;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /**
 * @var \Magento\Framework\Controller\Result\Json $resultJson
*/
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                [
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
                ]
            );
        }

        foreach (array_keys($postItems) as $productvideosId) {
            /**
 * @var \Magento\Productvideos\Model\Productvideos $productvideos
*/
            $productvideos = $this->productvideos->load($productvideosId);
            try {
                $productvideosData = $this->dataProcessor->filter(
                    $postItems[$productvideosId]
                );
                $productvideos->setData($productvideosData);
                $productvideos->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithVideoId(
                    $productvideos,
                    $e->getMessage()
                );
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithVideoId(
                    $productvideos,
                    $e->getMessage()
                );
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithVideoId(
                    $productvideos,
                    __('Something went wrong while saving the productvideos.')
                );
                $error = true;
            }
        }

        return $resultJson->setData(
            [
            'messages' => $messages,
            'error' => $error
            ]
        );
    }

    /**
     * Add productvideos title to error message
     *
     * @param  ProductvideosInterface $productvideos
     * @param  string                 $errorText
     * @return string
     */
    protected function getErrorWithVideoId(
        Productvideos $productvideos,
        $errorText
    ) {
        return '[Productvideos ID: ' . $productvideos->getVideoId() . '] ' . $errorText;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
