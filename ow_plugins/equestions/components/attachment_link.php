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
class EQUESTIONS_CMP_AttachmentLink extends OW_Component
{
    private $uniqId;

    public function __construct()
    {
        parent::__construct();

        $this->uniqId = uniqid('linkPanel');
    }

    public function initJs( $delegate )
    {
        $data = array();

        $data = array(
            'delegate' => $delegate,
            'rsp' => OW::getRouter()->urlFor('EQUESTIONS_CTRL_Attachments', 'rsp')
        );

        $js = UTIL_JsGenerator::newInstance()->newObject(
                array('CORE.ObjectRegistry', $this->uniqId),
                'ATTACHMENTS.LinkPanel',
                array(
                    $this->uniqId,
                    $data
                ));

        OW::getDocument()->addOnloadScript($js);
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $this->assign('uniqId', $this->uniqId);
    }
}
