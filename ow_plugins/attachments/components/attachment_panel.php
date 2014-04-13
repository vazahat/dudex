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
class ATTACHMENTS_CMP_AttachmentPanel extends OW_Component
{
    protected $uniqId;
    protected $widgets;

    public function __construct()
    {
        parent::__construct();

        $this->uniqId = uniqid('attachmentPanel');

        $plugin = OW::getPluginManager()->getPlugin('attachments');
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

        $rsp = OW::getRouter()->urlFor('ATTACHMENTS_CTRL_Attachments', 'rsp');

        $tabs = array();
        $js = UTIL_JsGenerator::newInstance();

        foreach ( $this->widgets as $uniqId => $widget )
        {
            $js->newObject(array('ATTP.ObjectRegistry', $uniqId) , 'ATTP.Loader', array($uniqId, $rsp, $widget['loader']));

            $onClick = "ATTP.ObjectRegistry.$this->uniqId.load('$uniqId');";

            $tabs[$uniqId] = array(
                'iconClass' => $widget['iconClass'],
                'label' => $widget['label'],
                'onClick' => $onClick,
                'id' => $uniqId
            );
        }

        $js->newObject(array('ATTP.ObjectRegistry', $this->uniqId) , 'ATTP.Panel', array($this->uniqId));

        ATTACHMENTS_Plugin::getInstance()->addJs($js);

        $this->assign('tabs', $tabs);
        $this->assign('uniqId', $this->uniqId);
    }
}