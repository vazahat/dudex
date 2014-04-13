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
 * @package uheader.components
 */
class UHEADER_CMP_CoverItem extends OW_Component
{
    const ITEM_WIDTH = 300;

    public function __construct( $userId )
    {
        parent::__construct();

        $cover = UHEADER_BOL_Service::getInstance()->findCoverByUserId($userId);
        
        if ( empty($cover) )
        {
            $this->setVisible(false);
            
            return;
        }
        
        UHEADER_CLASS_Plugin::getInstance()->includeStaticFile("uheader.css");

        $uniqId = uniqid('uheader-');
        $this->assign('uniqId', $uniqId);

        $js = UTIL_JsGenerator::newInstance()->jQueryEvent('#' . $uniqId, 'click',
            'OW.ajaxFloatBox("UHEADER_CMP_CoverView", [e.data.userId], {
                layout: "empty",
                top: 50
            });
            return false;'
        , array('e'), array(
            'userId' => $userId
        ));

        OW::getDocument()->addOnloadScript($js);

        $src = UHEADER_BOL_Service::getInstance()->getCoverUrl($cover);
        $this->assign('src', $src);

        $cavas = $cover->getCanvas(self::ITEM_WIDTH);
        
        $this->assign('imageCss', $cover->getCssString());
        $this->assign('height', $cavas["height"]);
    }

    private function scale( $x, $y, $toX )
    {
        return $y * $toX / $x;
    }
}