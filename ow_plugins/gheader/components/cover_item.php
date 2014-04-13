<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package gheader.components
 */
class GHEADER_CMP_CoverItem extends OW_Component
{
    const ITEM_WIDTH = 300;

    public function __construct( $groupId )
    {
        parent::__construct();

        $cover = GHEADER_BOL_Service::getInstance()->findCoverByGroupId($groupId);
        
        if ( $cover === null )
        {
            $this->setVisible(false);
            
            return;
        }
        
        $staticUrl = OW::getPluginManager()->getPlugin('gheader')->getStaticUrl();
        OW::getDocument()->addStyleSheet($staticUrl . 'gheader.css');

        $uniqId = uniqid('gheader-');
        $this->assign('uniqId', $uniqId);

        $js = UTIL_JsGenerator::newInstance()->jQueryEvent('#' . $uniqId, 'click',
            'OW.ajaxFloatBox("GHEADER_CMP_CoverView", [e.data.groupId], {
                layout: "empty",
                top: 50
            });
            return false;'
        , array('e'), array(
            'groupId' => $groupId
        ));

        OW::getDocument()->addOnloadScript($js);

        $src = GHEADER_BOL_Service::getInstance()->getCoverUrl($cover);
        $this->assign('src', $src);

        $settings = $cover->getSettings();

        $canvasHeight = $settings['canvas']['height'];
        $canvasWidth = $settings['canvas']['width'];
        $imageHeight = $settings['dimensions']['height'];
        $imageWidth = $settings['dimensions']['width'];
        $itemCanvasHeight = $canvasHeight * self::ITEM_WIDTH / $canvasWidth;

        $tmp = ( $canvasWidth * $imageHeight ) / $imageWidth;
        $css = $settings['css'];

        if ( $tmp >= $canvasHeight )
        {
            $itemHeight = $this->scale($imageWidth, $imageHeight, self::ITEM_WIDTH);
            $coverHeight = $this->scale($settings['dimensions']['width'], $settings['dimensions']['height'], $canvasWidth);
            $k = $coverHeight / $itemHeight;
            $css['top'] = ($settings['position']['top'] / $k ) . 'px';
        }
        else
        {
            $itemWidth = $this->scale($imageHeight, $imageWidth, $itemCanvasHeight);
            $coverWidth = $this->scale($imageHeight, $imageWidth, $canvasHeight);

            $k = $coverWidth / $itemWidth;
            $css['left'] = ($settings['position']['left'] / $k) . 'px';
        }

        $cssStr = '';
        foreach ( $css as $k => $v )
        {
            $cssStr .= $k . ': ' . $v  . '; ';
        }

        $this->assign('imageCss', $cssStr);
        $this->assign('height', $itemCanvasHeight);
    }

    private function scale( $x, $y, $toX )
    {
        return $y * $toX / $x;
    }
}