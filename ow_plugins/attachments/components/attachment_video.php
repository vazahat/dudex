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
class ATTACHMENTS_CMP_AttachmentVideo extends OW_Component
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
            'rsp' => OW::getRouter()->urlFor('ATTACHMENTS_CTRL_Attachments', 'rsp'),
            'delegate' => $delegate
        );

        $js = UTIL_JsGenerator::newInstance()->newObject(
                array('ATTP.CORE.ObjectRegistry', $this->uniqId),
                'ATTP.VideoPanel',
                array(
                    $this->uniqId,
                    $data
                ));

        ATTACHMENTS_Plugin::getInstance()->addJs($js);
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $this->assign('uniqId', $this->uniqId);

        $language = OW::getLanguage();
        $this->assign('langs', array(
            'cancel' => $language->text('attachments', 'attachments_cancel_label'),
            'close' => $language->text('attachments', 'attachments_close_label'),
            'addEmbed' => $language->text('attachments', 'attachments_add_embed_label'),
            'search' => $language->text('attachments', 'attachments_add_search_label'),
            'addVideo' => $language->text('attachments', 'attachments_add_video_label')
        ));
    }
}
