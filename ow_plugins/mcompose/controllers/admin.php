<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

require_once dirname(dirname(__FILE__)) . DS . 'plugin.php';

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package mcompose.controllers
 */
class MCOMPOSE_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    /**
     *
     * @var MCOMPOSE_Plugin
     */
    private $plugin;

    public function __construct()
    {
        parent::__construct();

        $this->plugin = MCOMPOSE_Plugin::getInstance();
    }

    public function index()
    {
        $language = OW::getLanguage();

        OW::getDocument()->setHeading($language->text('mcompose', 'heading_configuration'));
        OW::getDocument()->setHeadingIconClass('ow_ic_gear_wheel');

        $this->assign('pluginUrl', 'http://www.oxwall.org/store/item/10');

        if ( !$this->plugin->isAvaliable() )
        {
            $this->assign('avaliable', false);
            return;
        }

        $this->assign('avaliable', true);

        $settingUrl = OW::getRouter()->urlForRoute('mailbox_admin_config');
        $this->assign('settingsUrl', $settingUrl);

        $configs = OW::getConfig()->getValues('mcompose');
        $features = array();
        $features["friends"] = MCOMPOSE_CLASS_FriendsBridge::getInstance()->isActive();
        $features["groups"] = MCOMPOSE_CLASS_GroupsBridge::getInstance()->isActive();
        $features["events"] = MCOMPOSE_CLASS_EventsBridge::getInstance()->isActive();
        
        
        $form = new MCOMPOSE_ConfigForm($configs, $features);

        $this->addForm($form);
        
        $this->assign("configs", $configs);
        
        
        
        $this->assign("features", $features);
        $this->assign("activeFeatures", array_filter($features));

        if ( OW::getRequest()->isPost() && $form->isValid($_POST) )
        {
            if ( $form->process($_POST) )
            {
                OW::getFeedback()->info($language->text('mcompose', 'admin_settings_updated'));
                $this->redirect(OW::getRouter()->urlForRoute('mcompose-admin'));
            }
        }
    }
}

class MCOMPOSE_ConfigForm extends Form
{
    private $configs = array();

    public function __construct( $configs, $features )
    {
        parent::__construct('MCOMPOSE_ConfigForm');

        $this->configs = $configs;

        $language = OW::getLanguage();

        $field = new TextField('max_users');
        $field->setRequired();
        $field->setValue($configs['max_users']);
        $this->addElement($field);
        
        if ( $features["friends"] )
        {
            $field = new CheckboxField('friends_enabled');
            $field->setValue($configs['friends_enabled']);
            $this->addElement($field);
        }
        
        if ( $features["groups"] ) 
        {
            $field = new CheckboxField('groups_enabled');
            $field->setValue($configs['groups_enabled']);
            $this->addElement($field);
        }
        
        if ( $features["events"] )
        {
            $field = new CheckboxField('events_enabled');
            $field->setValue($configs['events_enabled']);
            $this->addElement($field);
        }

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('mcompose', 'admin_save_btn'));
        $this->addElement($submit);
    }

    public function process( $data )
    {
        $config = OW::getConfig();

        foreach ( $this->configs as $k => $v )
        {
            $element = $this->getElement($k);

            if ( $element !== null )
            {
                $v = $element->getValue();
                $config->saveConfig('mcompose', $k, $v === null ? 0 : $v);
            }
        }

        return true;
    }
}
