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
class EQUESTIONS_CMP_AttachmentPanel extends OW_Component
{
    protected $uniqId;
    protected $widgets;

    public function __construct()
    {
        parent::__construct();

        $this->uniqId = uniqid('attachmentPanel');

        $plugin = OW::getPluginManager()->getPlugin('equestions');
        $this->setTemplate($plugin->getCmpViewDir() . 'attachment_panel.html');
    }

    public function getUniqId()
    {
        return $this->uniqId;
    }

    public function addWidget( $label, $iconClass, array $loader )
    {
        $widgetLoaderId = uniqid('widgetLoader');

        $this->widgets[$widgetLoaderId] = array(
            'iconClass' => $iconClass,
            'label' => $label,
            'loader' => $loader
        );
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $rsp = OW::getRouter()->urlFor('EQUESTIONS_CTRL_Attachments', 'rsp');

        $tabs = array();
        $js = UTIL_JsGenerator::newInstance();

        foreach ( $this->widgets as $uniqId => $widget )
        {
            $js->newObject(array('ATTACHMENTS.ObjectRegistry', $uniqId) , 'ATTACHMENTS.Loader', array($uniqId, $rsp, $widget['loader']));

            $onClick = "ATTACHMENTS.ObjectRegistry.$this->uniqId.load('$uniqId');";

            $tabs[$uniqId] = array(
                'iconClass' => $widget['iconClass'],
                'label' => $widget['label'],
                'onClick' => $onClick,
                'id' => $uniqId
            );
        }

        $js->newObject(array('ATTACHMENTS.ObjectRegistry', $this->uniqId) , 'ATTACHMENTS.Panel', array($this->uniqId));

        OW::getDocument()->addOnloadScript($js);

        $this->assign('tabs', $tabs);
        $this->assign('uniqId', $this->uniqId);
    }
}