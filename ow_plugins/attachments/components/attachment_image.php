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
class ATTACHMENTS_CMP_AttachmentImage extends OW_Component
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

        $static = OW::getPluginManager()->getPlugin('attachments')->getStaticUrl();

        $webCam = array(
            'swf' => $static . 'webcam/webcam.swf',
            'sound' => $static . 'webcam/shutter.mp3',
            'quality' => 100,
            'uploader' => OW::getRouter()->urlFor('ATTACHMENTS_CTRL_Attachments', 'webcamHandler')
        );

        $data = array(
            'delegate' => $delegate,
            'rsp' => OW::getRouter()->urlFor('ATTACHMENTS_CTRL_Attachments', 'rsp'),
            'uploader' => OW::getRouter()->urlFor('ATTACHMENTS_CTRL_Attachments', 'uploader'),
            'webcam' => $webCam
        );

        $js = UTIL_JsGenerator::newInstance()->newObject(
                array('ATTP.CORE.ObjectRegistry', $this->uniqId),
                'ATTP.ImagePanel',
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
            'uploadSave' => $language->text('attachments', 'attachments_upload_save_label'),
            'takeSave' => $language->text('attachments', 'attachments_take_save_label'),
            'cancel' => $language->text('attachments', 'attachments_cancel_label'),
            'close' => $language->text('attachments', 'attachments_close_label'),
            'chooseImage' => $language->text('attachments', 'attachments_choose_image_label')
        ));
    }
}
