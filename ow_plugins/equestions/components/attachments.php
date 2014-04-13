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
class EQUESTIONS_CMP_Attachments extends OW_Component
{

    private $uniqId;
    private $types = array('image', 'video', 'link');

    public function __construct( $types = null )
    {
        parent::__construct();

        $this->uniqId = uniqid('attachment');

        EQUESTIONS_Plugin::getInstance()->addStatic();

        if ( $types !== null )
        {
            $this->types = $types;
        }
    }

    public function getUniqId()
    {
        return $this->uniqId;
    }

    public function initJs()
    {
        $js = UTIL_JsGenerator::newInstance()->newObject(
                array('CORE.ObjectRegistry', $this->uniqId),
                'ATTACHMENTS.Attachments',
                array(
                    $this->uniqId
                ));

        OW::getDocument()->addOnloadScript($js);
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        if ( in_array('image', $this->types) )
        {
            $image = new EQUESTIONS_CMP_AttachmentImage();
            $image->initJs($this->uniqId);

            $this->addComponent('image', $image);
        }

        if ( in_array('link', $this->types) )
        {
            $link = new EQUESTIONS_CMP_AttachmentLink();
            $link->initJs($this->uniqId);

            $this->addComponent('link', $link);
        }

        if ( in_array('video', $this->types) )
        {
            $video = new EQUESTIONS_CMP_AttachmentVideo();
            $video->initJs($this->uniqId);

            $this->addComponent('video', $video);
        }

        $this->assign('uniqId', $this->uniqId);
    }
}