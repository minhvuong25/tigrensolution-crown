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
namespace FME\Productvideos\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;

class DataUpgrade extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_NANO_GALLAYOUT_LAYOUT           = 'productvideos/nanogalllerySetting/gallerylayout/layouts';
    const XML_PATH_NANO_GALLAYOUT_GRID_WIDTH           = 'productvideos/nanogalllerySetting/gallerylayout/width';
    const XML_PATH_NANO_GALLAYOUT_GRID_HEIGHT            = 'productvideos/nanogalllerySetting/gallerylayout/height';
    const XML_PATH_NANO_GALLAYOUT_JUST_HEIGHT          = 'productvideos/nanogalllerySetting/gallerylayout/justheight';
    const XML_PATH_NANO_GALLAYOUT_CASCADING_WIDTH          = 'productvideos/nanogalllerySetting/gallerylayout/cascadingwidth';
    const XML_PATH_NANO_GALLAYOUT_MOSAIC_WIDTH        = 'productvideos/nanogalllerySetting/gallerylayout/mosaicwidth';
    const XML_PATH_NANO_GALLAYOUT_MOSAIC_HEIGHT          = 'productvideos/nanogalllerySetting/gallerylayout/mosaicheight';
    const XML_PATH_NANO_GALLAYOUT_MOSAIC_GALLERY         = 'productvideos/nanogalllerySetting/gallerylayout/mosaictextarea';
    const XML_PATH_NANO_GALLAYOUT_THUMB_TBH         = 'productvideos/nanogalllerySetting/gallerylayout/thumbnailBorderHorizontal';
    const XML_PATH_NANO_GALLAYOUT_THUMB_TBV        = 'productvideos/nanogalllerySetting/gallerylayout/thumbnailBorderVertical';
    const XML_PATH_NANO_GALLAYOUT_THUMB_TGW        = 'productvideos/nanogalllerySetting/gallerylayout/thumbnailGutterWidth';
    const XML_PATH_NANO_GALLAYOUT_THUMB_TGH        = 'productvideos/nanogalllerySetting/gallerylayout/thumbnailGutterHeight';
    const XML_PATH_NANO_GALLAYOUT_THUMB_ALIGN        = 'productvideos/nanogalllerySetting/gallerylayout/thumnsallign';
    const XML_PATH_NANO_GALLAYOUT_THUMB_DIS_INT        = 'productvideos/nanogalllerySetting/gallerylayout/thumbnailDisplayInterval';
    const XML_PATH_NANO_GALLAYOUT_THUMB_DIS_TRN       = 'productvideos/nanogalllerySetting/gallerylayout/thumbnailDisplayTransition';
    const XML_PATH_NANO_GALLAYOUT_THUMB_DIS_TRN_DUR        = 'productvideos/nanogalllerySetting/gallerylayout/thumbnailDisplayTransitionDuration';
    const XML_PATH_NANO_GALLAYOUT_THUMB_DIS_COLOR       = 'productvideos/nanogalllerySetting/gallerylayout/bg_color';
    const XML_PATH_NANO_GALLAYOUT_LABEL_POS       = 'productvideos/nanogalllerySetting/labelSetting/position';
    const XML_PATH_NANO_GALLAYOUT_LABEL_DISPLAY       = 'productvideos/nanogalllerySetting/labelSetting/display';
    const XML_PATH_NANO_GALLAYOUT_LABEL_ALIGN      = 'productvideos/nanogalllerySetting/labelSetting/align';
    const XML_PATH_NANO_GALLAYOUT_TOOL_TL      = 'productvideos/nanogalllerySetting/thumbnailtools/topLeft';
    const XML_PATH_NANO_GALLAYOUT_TOOL_TR      = 'productvideos/nanogalllerySetting/thumbnailtools/topRight';
    const XML_PATH_NANO_GALLAYOUT_TOOL_BL      = 'productvideos/nanogalllerySetting/thumbnailtools/bottomLeft';
    const XML_PATH_NANO_GALLAYOUT_TOOL_BR     = 'productvideos/nanogalllerySetting/thumbnailtools/bottomRight';
    const XML_PATH_NANO_GALLAYOUT_HE_THE     = 'productvideos/nanogalllerySetting/hovereffect/thumbnailHoverEffect2';
    const XML_PATH_NANO_GALLAYOUT_LIGHTBOX_TL     = 'productvideos/nanogalllerySetting/lightBox/topLeft';
    const XML_PATH_NANO_GALLAYOUT_LIGHTBOX_TR     = 'productvideos/nanogalllerySetting/lightBox/topRight';
    const XML_PATH_NANO_GALLAYOUT_LIGHTBOX_VTS     = 'productvideos/nanogalllerySetting/lightBox/viewerToolbarstandard';
    const XML_PATH_NANO_GALLAYOUT_LIGHTBOX_VTB     = 'productvideos/nanogalllerySetting/lightBox/viewerToolbarminimize';
    const XML_PATH_NANO_GALLAYOUT_THUMB_LASTFILL      = 'productvideos/nanogalllerySetting/paginitionsettings/galleryLastRowFull';
    const XML_PATH_NANO_GALLAYOUT_PAGINITION      = 'productvideos/nanogalllerySetting/paginitionsettings/paginitionType';
    const XML_PATH_NANO_GALLAYOUT_MULTI_ROW_ALLOW     = 'productvideos/nanogalllerySetting/paginitionsettings/allowgalleryMaxRows';
    const XML_PATH_NANO_GALLAYOUT_MULTI_ROW    = 'productvideos/nanogalllerySetting/paginitionsettings/galleryMaxRows';
    const XML_PATH_NANO_GALLAYOUT_MULTI_ROW_DOT    = 'productvideos/nanogalllerySetting/paginitionsettings/dotgalleryMaxRows';
    const XML_PATH_NANO_GALLAYOUT_MULTI_ROW_NUM    = 'productvideos/nanogalllerySetting/paginitionsettings/numgalleryMaxRows';
    const XML_PATH_NANO_GALLAYOUT_MULTI_ROW_RECT    = 'productvideos/nanogalllerySetting/paginitionsettings/rectgalleryMaxRows';
    const XML_PATH_NANO_GALLAYOUT_PAGE_MORESTEP    = 'productvideos/nanogalllerySetting/paginitionsettings/galleryDisplayMoreStep';
        
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \FME\Productvideos\Model\ProductvideosFactory $productvideosFactory,
        \FME\Productvideos\Model\Productvideos $productvideos,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\App\ResourceConnection $coreResource
    ) {
        $this->_productvideosFactory = $productvideosFactory;
        $this->_productvideos = $productvideos;
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_eventManager = $context->getEventManager();
        $this->_imageFactory = $imageFactory;
        $this->_resource = $coreResource;
        parent::__construct($context);
    }

    public function getNewSliderTransition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_TRANS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryPaginitionRowsRect()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_MULTI_ROW_RECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryPaginitionRowsNums()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_MULTI_ROW_NUM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryPaginitionRowsDots()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_MULTI_ROW_DOT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryPaginitionMoreStep()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_PAGE_MORESTEP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getGalleryPaginition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_PAGINITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMaxRows()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_MULTI_ROW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isAllowMaxRows()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_PATH_NANO_GALLAYOUT_MULTI_ROW_ALLOW, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

    public function getGalleryThumbLastFill()
    {
        $isEnabled = 'true';
         $enabled = $this->scopeConfig->getValue(self::XML_PATH_NANO_GALLAYOUT_THUMB_LASTFILL, ScopeInterface::SCOPE_STORE);
         if ($enabled == null || $enabled == '0') {
             $isEnabled = 'false';
         }
         return $isEnabled;

    }
    
    public function getGalleryBackgroundColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_DIS_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoLightBOXVTB()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LIGHTBOX_VTB,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoLightBOXVTS()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LIGHTBOX_VTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoLightBOXTR()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LIGHTBOX_TR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoLightBOXTL()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LIGHTBOX_TL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isLabelDisplay()
    {
        $isEnabled = 'true';
        $enabled = $this->scopeConfig->getValue(self::XML_PATH_NANO_GALLAYOUT_LABEL_DISPLAY, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = 'false';
        }
        return $isEnabled;
    }

    public function getNanoThumbHoverEffect()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_HE_THE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbToolBR()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_TOOL_BR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbToolBL()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_TOOL_BL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbToolTR()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_TOOL_TR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbToolTL()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_TOOL_TL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbLabelAlign()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LABEL_ALIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbGalleryLabelPosition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LABEL_POS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbDisplayTransitionDuration()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_DIS_TRN_DUR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbDisplayTransition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_DIS_TRN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbDisplayInterval()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_DIS_INT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbAlign()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_ALIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbTGH()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_TGH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbTGW()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_TGW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbTBV()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_TBV,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoThumbTBH()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_THUMB_TBH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanolayout()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoGridWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_GRID_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoGridHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_GRID_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoJustHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_JUST_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoCascadingWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_CASCADING_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoMosacSettings()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_MOSAIC_GALLERY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoMosacWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_GRID_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNanoMosacHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NANO_GALLAYOUT_GRID_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getOverallWidth()
    {
        if($this->getNanolayout()=='grid')
        {
            return $this->getNanoGridWidth();
        }
        elseif($this->getNanolayout()=='justified')
        {
            return "'"."auto"."'";;
        }
        elseif($this->getNanolayout()=='cascading')
        {
            return $this->getNanoCascadingWidth();
        }
        elseif($this->getNanolayout()=='mosaic')
        {
            return $this->getNanoMosacWidth();
        }
        return 200;
    }

    public function getOverallHeight()
    {
        if($this->getNanolayout()=='grid')
        {
            return $this->getNanoGridHeight();
        }
        elseif($this->getNanolayout()=='justified')
        {
            return $this->getNanoJustHeight();
        }
        elseif($this->getNanolayout()=='cascading')
        {
            return "'"."auto"."'";
        }
        elseif($this->getNanolayout()=='mosaic')
        {
            return $this->getNanoMosacHeight();
        }
        return 200;
    }
}
