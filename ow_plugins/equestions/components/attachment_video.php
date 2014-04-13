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
class EQUESTIONS_CMP_AttachmentVideo extends OW_Component
{
    private $uniqId;

    public function __construct()
    {
        parent::__construct();

        $this->uniqId = uniqid('videoPanel');
    }

    public function initJs( $delegate )
    {
        $data = array(
            'rsp' => OW::getRouter()->urlFor('EQUESTIONS_CTRL_Attachments', 'rsp'),
            'delegate' => $delegate
        );

        $js = UTIL_JsGenerator::newInstance()->newObject(
                array('CORE.ObjectRegistry', $this->uniqId),
                'ATTACHMENTS.VideoPanel',
                array(
                    $this->uniqId,
                    $data
                ));

        OW::getDocument()->addOnloadScript($js);
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $bridge = EQUESTIONS_CLASS_VideoBridge::getInstance();
        $this->assign('videoActive', $bridge->isActive());

        $this->assign('uniqId', $this->uniqId);

        $language = OW::getLanguage();
        $this->assign('langs', array(
            'cancel' => $language->text('equestions', 'attachments_cancel_label'),
            'close' => $language->text('equestions', 'attachments_close_label'),
            'addEmbed' => $language->text('equestions', 'attachments_add_embed_label'),
            'search' => $language->text('equestions', 'attachments_add_search_label'),
            'addVideo' => $language->text('equestions', 'attachments_add_video_label'),
            'chooseMy' => $language->text('equestions', 'attachments_choose_my_video_label')
        ));
    }
}
