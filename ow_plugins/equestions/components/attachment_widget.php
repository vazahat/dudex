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
abstract class EQUESTIONS_CMP_AttachmentWidget extends OW_Component
{
    private $uniqId, $label, $iconClass;

    /**
     *
     * @var EQUESTIONS_AttachmentWidgetDelegate
     */
    private $delegate;

    public function __construct( EQUESTIONS_AttachmentWidgetDelegate $delegate, $label, $iconClass = null )
    {
        parent::__construct();

        $this->uniqId = uniqid('attachmentWidget');

        $this->label = $label;
        $this->iconClass = $iconClass;
    }

    /**
     *
     * @return EQUESTIONS_AttachmentWidgetDelegate
     */
    public function getDelegate()
    {
        return $this->delegate;
    }

    public function getUniqId()
    {
        return $this->uniqId;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getIconClass()
    {
        return $this->iconClass;
    }

    public function init()
    {

    }
}

class EQUESTIONS_AttachmentWidgetDelegate
{
    private $rsp, $params, $uniqId;

    public function __construct( $rsp, $params )
    {
        $this->rsp = $rsp;
        $this->params = $params;

        $this->uniqId = uniqid('attachmentWidgetLoader');
    }

    public function init()
    {
        $js = UTIL_JsGenerator::newInstance();
        $js->newObject(array('ATTACHMENTS.ObjectRegistry', $this->uniqId) , $constructorName);

        OW::getDocument()->addOnloadScript($js);
    }
}