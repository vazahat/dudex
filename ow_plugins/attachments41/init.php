<?php

require_once dirname(__FILE__) . DS . 'plugin.php';

$plugin = OW::getPluginManager()->getPlugin('attachments');

OW::getRouter()->addRoute(new OW_Route('attachments-settings-page', 'admin/plugins/attachments', 'ATTACHMENTS_CTRL_Admin', 'index'));

if ( !class_exists('BASE_CMP_CommentsForm', false ) )
{
    include_once $plugin->getCmpDir() . 'comments_form.php';
}

if ( !class_exists('BASE_CMP_OembedAttachment', false ) )
{
    include_once $plugin->getCmpDir() . 'oembed_attachmet.php';
}

if ( OW::getPluginManager()->isPluginActive('newsfeed') && !class_exists('NEWSFEED_CMP_UpdateStatus', false) )
{
    include_once $plugin->getCmpDir() . 'newsfeed_status.php';
}

ATTACHMENTS_CLASS_EventHandler::getInstance()->init();

ATTACHMENTS_CLASS_LinksBridge::getInstance()->init();
ATTACHMENTS_CLASS_PhotoBridge::getInstance()->init();
ATTACHMENTS_CLASS_VideoBridge::getInstance()->init();

ATTACHMENTS_CLASS_NewsfeedBridge::getInstance()->init();