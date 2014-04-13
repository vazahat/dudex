<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

$plugin = OW::getPluginManager()->getPlugin('ynsocialstream');
$key = strtoupper($plugin->getKey());

//Back-end Route
OW::getRouter()->addRoute(new OW_Route('ynsocialstream-global-settings', 'admin/plugins/socialstream', "{$key}_CTRL_Admin", 'globalSettings'));
//OW::getRouter()->addRoute(new OW_Route('ynsocialstream.level_settings', 'admin/plugins/socialstream/level_settings', "{$key}_CTRL_Admin", 'levelSettings'));
//Front-end Route
OW::getRouter()->addRoute(new OW_Route('ynsocialbridge-stream-settings', 'socialstream/settings', "{$key}_CTRL_Socialstream", 'index'));

OW::getRouter()->addRoute(new OW_Route('ynsocialstream-get-feed', 'socialstream/get-feed', "{$key}_CTRL_Socialstream", 'getFeed'));
OW::getRouter()->addRoute(new OW_Route('ynsocialstream-connect', 'socialstream/connect', "{$key}_CTRL_Socialstream", 'connect'));


$eventHandler = YNSOCIALSTREAM_CLASS_EventHandler::getInstance();

if ( OW::getUser()->isAuthorized('ynsocialstream', 'get_feed') )
{
	$build = BOL_PluginService::getInstance()->findPluginByKey('ynsocialbridge')->build;
	if($build>1)
		OW::getEventManager()->bind(OW_EventManager::ON_APPLICATION_INIT, array($eventHandler, 'onApplicationInit'));
}
function ynsocialstream_deactive()
{
 if (OW::getPluginManager() -> isPluginActive('ynsocialbridge') == false)
 {
  BOL_PluginService::getInstance()->deactivate('ynsocialstream');
 }
 else 
 {
  $build = BOL_PluginService::getInstance()->findPluginByKey('ynsocialbridge')->build;
  if ($build < 2)
  {
   BOL_PluginService::getInstance()->deactivate('ynsocialstream');
  }  
 }
}
OW::getEventManager()->bind(OW_EventManager::ON_APPLICATION_INIT, 'ynsocialstream_deactive');


function ynsocialstream_addAdminNotification(BASE_CLASS_EventCollector $e)
{  
 if (OW::getPluginManager() -> isPluginActive('ynsocialbridge') == false )
 {
  $language = OW::getLanguage();
  $e->add($language->text('ynsocialstream', 'requires_configuration_message')); 
 } 
 else
 {
	$build = BOL_PluginService::getInstance()->findPluginByKey('ynsocialbridge')->build;
	if ($build < 2)
	{
		$language = OW::getLanguage();
		$e->add($language->text('ynsocialstream', 'requires_configuration_message')); 
	}
 }
}

OW::getEventManager()->bind('admin.add_admin_notification', 'ynsocialstream_addAdminNotification');

function ynsocialstream_add_auth_labels( BASE_CLASS_EventCollector $event )
{
    $language = OW::getLanguage();
    $event->add(
        array(
            'ynsocialstream' => array(
                'label' => $language->text('ynsocialstream', 'auth_group_label'),
                'actions' => array(
                    'get_feed' => $language->text('ynsocialstream', 'auth_action_label_get_feed')
                )
            )
        )
    );
}
OW::getEventManager()->bind('admin.add_auth_labels', 'ynsocialstream_add_auth_labels');
