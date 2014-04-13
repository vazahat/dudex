<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

class MCOMPOSE_Plugin
{
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return MCOMPOSE_Plugin
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct()
    {
    }

    public function isAvaliable()
    {
        return OW::getPluginManager()->isPluginActive('mailbox');
    }

    public function collectMailboxMenu( BASE_CLASS_EventCollector $event )
    {
        if ( !OW::getUser()->isAuthorized( 'mailbox', 'send_message' ) )
        {
            return;
        }

        $language = OW::getLanguage();

        $item = new BASE_MenuItem();
        $item->setLabel($language->text('mcompose', 'compose_btn'));
        $item->setIconClass('ow_ic_new');
        $item->setPrefix('mcompose');
        $item->setUrl(OW::getRouter()->urlForRoute("mcompose-index"));
        $item->setKey('compose');

        $item->setOrder(3);

        $event->add($item);

        $staticUrl = OW::getPluginManager()->getPlugin('mcompose')->getStaticUrl();
        OW::getDocument()->addStyleSheet($staticUrl . 'style.css');
    }

    public function collectAddNew( BASE_CLASS_EventCollector $event )
    {
        if ( !OW::getUser()->isAuthorized( 'mailbox', 'send_message' ) )
        {
            return;
        }

        $resultArray = array(
            BASE_CMP_AddNewContent::DATA_KEY_ICON_CLASS => 'ow_ic_mail',
            BASE_CMP_AddNewContent::DATA_KEY_URL => OW::getRouter()->urlForRoute('mcompose-index'),
            BASE_CMP_AddNewContent::DATA_KEY_LABEL => OW::getLanguage()->text('mcompose', 'add_new_message')
        );

        $event->add($resultArray);
    }

    public function collectAdminNotifications( BASE_CLASS_EventCollector $e )
    {
        $language = OW::getLanguage();
        $e->add($language->text('mcompose', 'admin_plugin_required_notification', array(
            'pluginUrl' => 'http://www.oxwall.org/store/item/10',
            'settingUrl' => OW::getRouter()->urlForRoute('mcompose-admin')
        )));
    }

    public function init()
    {
        OW::getRouter()->addRoute(new OW_Route('mcompose-admin', 'admin/plugins/mcompose', 'MCOMPOSE_CTRL_Admin', 'index'));

        if ( $this->isAvaliable() )
        {
            $this->fullInit();
        }
        else
        {
            OW::getEventManager()->bind('admin.add_admin_notification', array($this, 'collectAdminNotifications'));
        }
    }

    public function fullInit()
    {
        OW::getRouter()->addRoute(new OW_Route('mcompose-index', 'mailbox/compose', 'MCOMPOSE_CTRL_Compose', 'index'));

        OW::getEventManager()->bind('mailbox.collect_menu_items', array($this, 'collectMailboxMenu'));
        OW::getEventManager()->bind(BASE_CMP_AddNewContent::EVENT_NAME, array($this, 'collectAddNew'));
    }
}