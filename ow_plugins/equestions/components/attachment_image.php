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
class EQUESTIONS_CMP_AttachmentImage extends OW_Component
{
    private $uniqId;

    public function __construct()
    {
        parent::__construct();

        $this->uniqId = uniqid('imagePanel');
    }

    public function initJs( $delegate )
    {
        $data = array();

        $static = OW::getPluginManager()->getPlugin('equestions')->getStaticUrl();

        $webCam = array(
            'swf' => $static . 'webcam/webcam.swf',
            'sound' => $static . 'webcam/shutter.mp3',
            'quality' => 100,
            'uploader' => OW::getRouter()->urlFor('EQUESTIONS_CTRL_Attachments', 'webcamHandler')
        );

        $data = array(
            'delegate' => $delegate,
            'rsp' => OW::getRouter()->urlFor('EQUESTIONS_CTRL_Attachments', 'rsp'),
            'uploader' => OW::getRouter()->urlFor('EQUESTIONS_CTRL_Attachments', 'uploader'),
            'webcam' => $webCam
        );

        $js = UTIL_JsGenerator::newInstance()->newObject(
                array('CORE.ObjectRegistry', $this->uniqId),
                'ATTACHMENTS.ImagePanel',
                array(
                    $this->uniqId,
                    $data
                ));

        OW::getDocument()->addOnloadScript($js);
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $bridge = EQUESTIONS_CLASS_PhotoBridge::getInstance();
        $this->assign('photoActive', $bridge->isActive());

        $this->assign('uniqId', $this->uniqId);

        $language = OW::getLanguage();
        $this->assign('langs', array(
            'uploadSave' => $language->text('equestions', 'attachments_upload_save_label'),
            'takeSave' => $language->text('equestions', 'attachments_take_save_label'),
            'cancel' => $language->text('equestions', 'attachments_cancel_label'),
            'close' => $language->text('equestions', 'attachments_close_label'),
            'chooseImage' => $language->text('equestions', 'attachments_choose_image_label'),
            'chooseMy' => $language->text('equestions', 'attachments_choose_my_image_label')
        ));
    }
}
