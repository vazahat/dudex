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
class EQUESTIONS_CMP_Tabs extends OW_Component
{
    private $tabs = array();

    public function __construct()
    {
        parent::__construct();

        static $count = 0;
        $count++;

        $uniqId = 'gtabs-' . $count;

        EQUESTIONS_Plugin::getInstance()->addStatic();

        $js = UTIL_JsGenerator::newInstance()->newObject('questionsTabs', 'QUESTIONS_Tabs', array($uniqId));
        OW::getDocument()->addOnloadScript($js);

        $this->assign('uniqId', $uniqId);
    }

    public function addTab( $label, OW_Component $cmp, $icon = 'ow_ic_files' )
    {
        $this->tabs[] = array(
            'label' => $label,
            'cmp' => $cmp,
            'icon' => $icon
        );
    }

    public function render()
    {
        $tplTabs = array();

        foreach ( $this->tabs as $item )
        {
            $tplTabs[] = array(
                'label' => $item['label'],
                'content' => $item['cmp']->render(),
                'icon' => $item['icon'],
                'active' => false
            );
        }

        $tplTabs[0]['active'] = true;

        $this->assign('tabs', $tplTabs);

        return parent::render();
    }
}