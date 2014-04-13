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
 * @package attachments.components
 */
class ATTACHMENTS_CMP_Attachment extends OW_Component
{
    protected $oembed = array(), $uniqId, $type, $initJs = '';

    public function __construct( $oembed, $expandedView = false )
    {
        parent::__construct();

        $this->uniqId = uniqid('attachments');
        $this->assign('uniqId', $this->uniqId);

        $types = array('video', 'photo', 'link');
        $this->type = in_array($oembed['type'], $types) ? $oembed['type'] : 'link';
        
        if ( $this->type == "video" )
        {
            $oembed["html"] = ATTACHMENTS_CLASS_VideoTools::addCodeParam($oembed["html"]);
            
            if ( !empty($oembed["thumbnail_url"]) )
            {
                $oembed["html"] = ATTACHMENTS_CLASS_VideoTools::addAutoPlay($oembed["html"]);
            }
        }
        
        $this->oembed = $oembed;

        $plugin = OW::getPluginManager()->getPlugin('attachments');

        $this->setTemplate($plugin->getCmpViewDir() . 'att_' . $this->type . '.html');

        $this->assign('expandedView', $expandedView);
    }

    public function initJs( $delegate )
    {
        $js = UTIL_JsGenerator::newInstance();
        $js->newObject(array('ATTP.CORE.ObjectRegistry', $this->uniqId), 'ATTP.Attachment', array($this->uniqId, $delegate, $this->type));

        $this->initJs = $js->generateJs();

        return $this->uniqId;
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        ATTACHMENTS_Plugin::getInstance()->addStatic($this->initJs);

        $this->assign('data', $this->oembed);
    }
}
