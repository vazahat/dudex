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
 * @package equestions.components
 */
class EQUESTIONS_CMP_Attachment extends OW_Component
{
    protected $oembed = array(), $uniqId;

    public function __construct( $oembed, $expandedView = false )
    {
        parent::__construct();

        $this->uniqId = uniqid('eqattachment');
        $this->assign('uniqId', $this->uniqId);

        $types = array('video', 'photo', 'link');

        $type = in_array($oembed['type'], $types) ? $oembed['type'] : 'link';
        
        if ( $type == "video" )
        {
            $oembed["html"] = EQUESTIONS_CLASS_VideoTools::addCodeParam($oembed["html"]);
            
            if ( !empty($oembed["thumbnail_url"]) )
            {
                $oembed["html"] = EQUESTIONS_CLASS_VideoTools::addAutoPlay($oembed["html"]);
            }
        }
        
        $this->oembed = $oembed;

        $plugin = OW::getPluginManager()->getPlugin('equestions');

        $this->setTemplate($plugin->getCmpViewDir() . 'att_' . $type . '.html');

        $this->assign('expandedView', $expandedView);
    }

    public function initJs( $delegate )
    {
        $js = UTIL_JsGenerator::newInstance();
        $js->newObject(array('CORE.ObjectRegistry', $this->uniqId), 'ATTACHMENTS.Attachment', array($this->uniqId, $delegate));

        OW::getDocument()->addOnloadScript($js);

        return $this->uniqId;
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $this->assign('data', $this->oembed);
    }
}
