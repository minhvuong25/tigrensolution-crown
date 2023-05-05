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

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_THUMBS_WIDTH = 'productvideos/thumbsetting/thumb_width';
    const XML_PATH_THUMBS_HEIGHT = 'productvideos/thumbsetting/thumb_height';
    const XML_PATH_THUMBS_BORDER_EFFECT = 'productvideos/thumbsetting/thumb_border_effect';
    const XML_PATH_THUMBS_BORDER_WIDTH = 'productvideos/thumbsetting/thumb_border_width';
    const XML_PATH_THUMBS_BORDER_COLOR = 'productvideos/thumbsetting/thumb_border_color';
    const XML_PATH_THUMBS_OVER_BORDER_WIDTH = 'productvideos/thumbsetting/thumb_over_border_width';
    const XML_PATH_THUMBS_OVER_BORDER_COLOR = 'productvideos/thumbsetting/thumb_over_border_color';
    const XML_PATH_THUMBS_SELECTED_BORDER_WIDTH = 'productvideos/thumbsetting/thumb_selected_border_width';
    const XML_PATH_THUMBS_SELECTED_BORDER_COLOR = 'productvideos/thumbsetting/thumb_selected_border_color';
    const XML_PATH_THUMBS_ROUND_CORNER_RADIUS = 'productvideos/thumbsetting/thumb_round_corners_radius';
    const XML_PATH_THUMBS_COLOR_OVERLAY_EFFECT = 'productvideos/thumbsetting/thumb_color_overlay_effect';
    const XML_PATH_THUMBS_OVERLAY_COLOR = 'productvideos/thumbsetting/thumb_overlay_color';
    const XML_PATH_THUMBS_OVERLAY_OPACITY = 'productvideos/thumbsetting/thumb_overlay_opacity';
    const XML_PATH_THUMBS_OVERLAY_REVERSE = 'productvideos/thumbsetting/thumb_overlay_reverse';
    const XML_PATH_THUMBS_IMAGE_OVERLAY_EFFECT = 'productvideos/thumbsetting/thumb_image_overlay_effect';
    const XML_PATH_THUMBS_IMAGE_OVERLAY_TYPE = 'productvideos/thumbsetting/thumb_image_overlay_type';
    const XML_PATH_THUMBS_TRANSITION_DURATION = 'productvideos/thumbsetting/thumb_transition_duration';
    const XML_PATH_THUMBS_SHOW_LOADER = 'productvideos/thumbsetting/thumb_show_loader';
    const XML_PATH_THUMBS_LOADER_TYPE = 'productvideos/thumbsetting/thumb_loader_type';
    const XML_PATH_THUMBS_ENABLE_PB = 'productvideos/thumbsetting/slider_enable_play_button';
    const XML_PATH_SLIDER_TRANS = 'productvideos/slideoptions/slider_transition';
    const XML_PATH_SLIDER_TRANS_DUR = 'productvideos/slideoptions/slider_transition_speed';
    const XML_PATH_SLIDER_CONTROL_SWIPE = 'productvideos/slideoptions/slider_control_swipe';
    const XML_PATH_SLIDER_CONTROL_ZOOM = 'productvideos/slideoptions/slider_control_zoom';
    const XML_PATH_SLIDER_LOADER_TYPE = 'productvideos/slideoptions/slider_loader_type';
    const XML_PATH_SLIDER_LOADER_COLOR = 'productvideos/slideoptions/slider_loader_color';
    const XML_PATH_SLIDER_ENABLE_BULLET = 'productvideos/slideoptions/slider_enable_bullets1';
    const XML_PATH_SLIDER_BULLET_HOR = 'productvideos/slideoptions/slider_bullets_align_hor';
    const XML_PATH_SLIDER_BULLET_VER = 'productvideos/slideoptions/slider_bullets_align_vert';
    const XML_PATH_SLIDER_ENABLE_ARROWS = 'productvideos/slideoptions/slider_enable_arrows';
    const XML_PATH_SLIDER_ENABLE_P_INDICATOR = 'productvideos/slideoptions/slider_enable_progress_indicator';
    const XML_PATH_SLIDER_P_I_TYPE = 'productvideos/slideoptions/slider_progress_indicator_type';
    const XML_PATH_SLIDER_P_I_V_ALIGN = 'productvideos/slideoptions/slider_progress_indicator_align_vert';
    const XML_PATH_SLIDER_PB_COLOR = 'productvideos/slideoptions/slider_progressbar_color';
    const XML_PATH_SLIDER_PB_OPACITY = 'productvideos/slideoptions/slider_progressbar_opacity';
    const XML_PATH_SLIDER_PB_L_WIDTH = 'productvideos/slideoptions/slider_progressbar_line_width';
    const XML_PATH_SLIDER_PP_TYPE_FILL = 'productvideos/slideoptions/slider_progresspie_type_fill';
    const XML_PATH_SLIDER_PP_COLOR_1 = 'productvideos/slideoptions/slider_progresspie_color1';
    const XML_PATH_SLIDER_PP_COLOR_2 = 'productvideos/slideoptions/slider_progresspie_color2';
    const XML_PATH_SLIDER_PP_S_WIDTH = 'productvideos/slideoptions/slider_progresspie_stroke_width';
    const XML_PATH_SLIDER_PP_WIDTH = 'productvideos/slideoptions/slider_progresspie_width';
    const XML_PATH_SLIDER_PP_HEIGHT = 'productvideos/slideoptions/slider_progresspie_height';
    const XML_PATH_SLIDER_ENABLE_PB = 'productvideos/slideoptions/slider_enable_play_button';
    const XML_PATH_SLIDER_PB_H = 'productvideos/slideoptions/slider_play_button_align_hor';
    const XML_PATH_SLIDER_PB_V = 'productvideos/slideoptions/slider_play_button_align_vert';
    const XML_PATH_SLIDER_ENABLE_FS = 'productvideos/slideoptions/slider_enable_fullscreen_button';
    const XML_PATH_SLIDER_FS_H = 'productvideos/slideoptions/slider_fullscreen_button_align_hor';
    const XML_PATH_SLIDER_FS_V = 'productvideos/slideoptions/slider_fullscreen_button_align_vert';
    const XML_PATH_SLIDER_CONTROL_ALWAYS_ON= 'productvideos/slideoptions/slider_controls_always_on';
    const XML_PATH_SLIDER_CONTROL_APEAR_ONTAP= 'productvideos/slideoptions/slider_controls_appear_ontap';
    const XML_PATH_SLIDER_CONTROL_APEAR_DUR= 'productvideos/slideoptions/slider_controls_appear_duration';
    const XML_PATH_SLIDER_ENABLE_TEXT_PANEL= 'productvideos/slideoptions/slider_enable_text_panel';
    const XML_PATH_SLIDER_TEXT_PANEL_BGCOLOR= 'productvideos/slideoptions/slider_textpanel_bg_color';
    const XML_PATH_SLIDER_TEXT_PANEL_OPACITY= 'productvideos/slideoptions/slider_textpanel_bg_opacity';
    const XML_PATH_THEME= 'productvideos/compactview/view';
    const XML_PATH_GRID_POS= 'productvideos/compactview/gridpos';
    const XML_PATH_STRIP_POS= 'productvideos/compactview/strippos';
    const XML_PATH_YOUTUBE_API_KEY = 'catalog/product_video/youtube_api_key';
    const XML_PATH_MODULE_ENABLE = 'productvideos/general/enable_module';
    const XML_PATH_THUMB_WIDTH = 'productvideos/general/thumb_width';
    const XML_PATH_THUMB_HEIGHT = 'productvideos/general/thumb_height';
    const XML_PATH_TITLE = 'productvideos/general/title';
    const XML_PATH_COLOR = 'productvideos/general/bg_color';
    const XML_PATH_FRAME = 'productvideos/general/frame_thumb';
    const XML_PATH_ASPECTRATIO = 'productvideos/general/aspect_ration';
    const XML_COMP_POSITION = 'productvideos/compactview/composition';

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

    public function getNewTHeme()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THEME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewGridPos()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GRID_POS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewStripPos()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STRIP_POS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getNewSliderTransition()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_TRANS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderTransitionDuration()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_TRANS_DUR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderControlSwipe()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_CONTROL_SWIPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderControlZoom()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_CONTROL_ZOOM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderLoaderType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_LOADER_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function videoType($url)
    {
        if (strpos($url, 'youtube') !== FALSE) {
            return 'youtube';
        } elseif (strpos($url, 'vimeo')  !== FALSE) {
            return 'vimeo';
        } elseif (strpos($url, 'flv')  !== FALSE) {
            return 'flv';
        } else {
            return 'unknown';
        }
    }

    public function getNewSliderLoaderColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_LOADER_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderEnableBullet()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_BULLET,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderBulletHor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_BULLET_HOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderBulletVer()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_BULLET_VER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderEnableArrows()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_ARROWS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPIndicator()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_P_INDICATOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPIndicatorType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_P_I_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPIndicatorVAlign()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_P_I_V_ALIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getNewSliderPBarColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PB_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderPBarOpacity()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PB_OPACITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderPBLWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PB_L_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getNewSliderPPTYPEFill()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_TYPE_FILL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderPPColor1()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_COLOR_1,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewSliderPPColor2()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_COLOR_2,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPPSWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_S_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPPWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderPPHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PP_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderEnablePB()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_PB,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderEnablePBHor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PB_H,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderEnablePBVer()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_PB_V,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderEnableFS()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_FS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderEnableFSHor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_FS_H,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderEnableFSVer()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_FS_V,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderControlAlwaysOn()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_CONTROL_ALWAYS_ON,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewSliderControlAppearOntap()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_CONTROL_APEAR_ONTAP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderControlAppearDur()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_CONTROL_APEAR_DUR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderEnableTextpanel()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_ENABLE_TEXT_PANEL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderTextpanelBGColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_TEXT_PANEL_BGCOLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewSliderTextpanelOpacity()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SLIDER_TEXT_PANEL_OPACITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewGridEnableHandle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_HANDLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridHandleAlign()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HANDLE_ALIGN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridHandleOffset()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HANDLE_OFFSET,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridHandleBackColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HANDLE_BACK_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewGridHandlePanesDirection()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HANDLE_PANES_DIR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    
    public function getNewThumbEnablePB()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_ENABLE_PB,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewThumbsWidth()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNewThumbsHeight()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_HEIGHT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
 
    public function getNewThumbsBE()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_BORDER_EFFECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getNewThumbsBW()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_BORDER_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewThumbsBC()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_BORDER_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewThumbsOverBW()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_OVER_BORDER_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewThumbsOverBC()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_OVER_BORDER_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewThumbsSelectedBW()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_SELECTED_BORDER_WIDTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getNewThumbsSelectedBC()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_SELECTED_BORDER_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getNewThumbsRoundCorner()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_ROUND_CORNER_RADIUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsColorOverlayEffect()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_COLOR_OVERLAY_EFFECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsOVerlayColor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_OVERLAY_COLOR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsOVerlayOpacity()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_OVERLAY_OPACITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsOVerlayReverse()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_OVERLAY_REVERSE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsImageOverlayEffect()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_IMAGE_OVERLAY_EFFECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsImageOverlayType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_IMAGE_OVERLAY_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbsTransitionDuration()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_TRANSITION_DURATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbShowLoader()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_SHOW_LOADER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //
    public function getNewThumbLoaderType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THUMBS_LOADER_TYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    //End Thumbs Setting

    public function productVideosEnable()
    {
        $isEnabled = true;
        
        $enabled = $this->scopeConfig->getValue(self::XML_PATH_MODULE_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

    public function getThumbWidth()
    {
        return 250;
    }
    public function compositionPosition()
    {
        //
        return $this->scopeConfig->getValue(
            self::XML_COMP_POSITION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getColor()
    {
        return "#ffffff";
    }
    public function getAspectRatio()
    {
        return true;
    }
    public function getFrame()
    {
        return true;
    }
    public function getThumbHeight()
    {
        return 250;
    }

    public function getTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getYouTubeApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_YOUTUBE_API_KEY);
    }

    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
    }

    public function hexToRgb($hex, $alpha = false)
    {
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['0'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['1'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['2'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ($alpha) {
            $rgb['a'] = $alpha;
        }
        return $rgb;
    }

    public function resizeImage($imgUrl, $x = null, $y = null, $imagePath = null)
    {
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
        $baseScmsMediaURL = $mediaDirectory->getAbsolutePath();
        if ($x == null && $y == null) {
            $x = $this->getThumbWidth();
            $y = $this->getThumbHeight();
            if ($x == null && $y == null) {
                $x = 200;
                $y = 200;
            }
        }
        $imgPath = $this->splitImageValue($imgUrl, "path");
        $imgName = $this->splitImageValue($imgUrl, "name");
        $imgPath = str_replace("/", '/', $imgPath);
        $imgPathFull = $baseScmsMediaURL . $imgPath . '/' . $imgName;
        $width = $x;
        $y ? $height = $y : $height = $x;
        $resizeFolder = $width . "X" . $height;
        $imageResizedPath = $baseScmsMediaURL . $imgPath . '/' . $resizeFolder . '/' . $imgName;
        $colorArray = [];
        $color = "161,0,27";
        $colorArray = explode(",", $color);
        $bgColor = $this->getColor();
        $bgColorArray = $this->hexToRgb($bgColor);
        $keepRatio = false;
        $keepFrame = false;
        if ($this->getAspectRatio() == 1) {
            $keepRatio = true;
        } else {
            $keepRatio = false;
        }
        
        if ($this->getFrame() == 1) {
            $keepFrame = true;
        } else {
            $keepFrame = false;
        }
        if (!file_exists($imageResizedPath) && file_exists($imgPathFull)) :
            $imageObj = $this->_imageFactory->create($imgPathFull);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio($keepRatio);
            $imageObj->keepFrame($keepFrame);
            $imageObj->backgroundColor([intval($bgColorArray[0]),intval($bgColorArray[1]),intval($bgColorArray[2])]);
            $imageObj->resize($width, $height);
            $imageObj->save($imageResizedPath);
        endif;
        $imgUrl = str_replace('/', "/", $imgPath);
        return $this->getMediaUrl() . $imgUrl . "/". $imgName;
    }

    public function splitImageValue($imageValue, $attr = "name")
    {
        $imArray = explode("/", $imageValue);
        $name = $imArray[count($imArray) - 1];
        $path = implode("/", array_diff($imArray, [$name]));
        if ($attr == "path") {
            return $path;
        } else {
            return $name;
        }
    }

    public function videoinfo($url)
    {
        if (strpos($url, "youtube.com") !== false || strpos($url, "youtu.be") !== false) {
            $data = $this->getYouTubeInfo($url);
        } // End Youtube
        elseif (strpos($url, "vimeo.com") !== false) {
            $data = $this->getVimeoInfo($url);
        } // End Vimeo
        elseif (strpos($url, "dailymotion.com") !== false) {
            $data['video_type'] = 'dailymotion';
            $video_id = explode('dailymotion.com/video/', $url);
            $video_id = $video_id[1];
            $data['video_id'] = '//www.dailymotion.com/embed/video/' . $video_id . '?autoPlay=1';
            return $data;
        } //End Dailymotion
        else {
            $data = false;
        }
        return $data;
    }

    public function getYouTubeInfo($url)
    {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $video_id = $match[1];
        }
        $data['video_type'] = 'youtube';
        $data['video_id'] = $video_id;
        return $data;
    }

    public function getVimeoInfo($url)
    {
        $video_id = explode('vimeo.com/', $url);
        $video_id = $video_id[1];
        $data['video_type'] = 'vimeo';
        $data['video_id'] = $video_id;
        return $data;
    }

    public function getImageUrl($img)
    {
        if ($img != null) {
            if (strpos($img, 'https://') !== false) {
                       $img_url = $img;
            } else {
                $img_url = $this->resizeImage($img);
            }
        } else {
            $img = 'productvideos/no_img.jpg';
            $img_url = $this->resizeImage($img);
        }
        return $img_url;
    }

    public function getVideoData($_item)
    {
        $daysUploded = $this->timeFrame($_item['created_time']);
        $thumbb=$this->getMediaUrl().$_item['video_thumb'];
        if ($_item["video_type"] == "file") {
            $file = $this->getMediaUrl() . $_item['video_file'];
            $data = "data-toggle='jwplayer' data-diff='" . $daysUploded . "' data-title='" . $_item['title'] . "' data-content='" . $_item['content'] . "' data-target='#videoModal' data-video='" . $file . "' data-thumbb='".$thumbb . "'";
        } elseif ($_item["video_type"] == "url") {
            $videoURL = $_item["video_url"];
            $videoData = $this->videoinfo($_item["video_url"]);
            //For Video URL
            if ($videoData !== false) {
                $video_type = $videoData['video_type'];
                $video_id = $videoData['video_id'];
                if ($video_type == "vimeo") {
                    $videoURL = 'http://player.vimeo.com/video/' . $video_id . '?portrait=0&autoplay=1';
                    $data = "data-toggle='iframe' data-diff='" . $daysUploded . "' data-title='" . $_item['title'] . "' data-content='" . $_item['content'] . "' data-target='#videoModal' data-video='" . $videoURL . "'";
                } elseif ($video_type == "youtube") {
                    $videoURL = "http://www.youtube.com/embed/" . $video_id;
                    $data = "data-toggle='iframe' data-diff='" . $daysUploded . "' data-title='" . $_item['title'] . "' data-content='" . $_item['content'] . "' data-target='#videoModal' data-video='" . $videoURL . "'";
                } elseif ($video_type == "dailymotion") {
                    $videoURL = $video_id;
                    $data = "data-toggle='iframe' data-diff='" . $daysUploded . "' data-title='" . $_item['title'] . "' data-content='" . $_item['content'] . "' data-target='#videoModal' data-video='" . $videoURL . "'";
                }
            }
        }
        return $data;
    }

    public function timeFrame($from)
    {
        $day = 24 * 3600;
        $to = ((new \DateTime())->format('Y-m-d'));
        $from = strtotime($from);
        $to = strtotime($to) + $day;
        $diff = abs($to - $from);
        $weeks = floor($diff / $day / 7);
        $days = floor($diff / $day - $weeks * 7);
        $out = [];
        if ($weeks) {
            $out[] = "$weeks Week" . ($weeks > 1 ? 's' : '');
        }
        if ($days) {
            $out[] = "$days Day" . ($days > 1 ? 's' : '');
        }
        return implode(', ', $out);
    }
}
