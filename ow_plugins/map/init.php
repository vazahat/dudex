<?php

/***
 * This software is intended for use with Oxwall Free Community Software
 * http://www.oxwall.org/ and is a proprietary licensed product.
 * For more information see License.txt in the plugin folder.

 * =============================================================================
 * Copyright (c) 2012 by Aron. All rights reserved.
 * =============================================================================


 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.
 * Pass on to others in any form are not permitted provided.
 * Sale are not permitted provided.
 * Sale this product are not permitted provided.
 * Gift this product are not permitted provided.
 * This plugin should be bought from the developer by paying money to PayPal account: biuro@grafnet.pl
 * Legal purchase is possible only on the web page URL: http://www.oxwall.org/store
 * Modyfing of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * Modifying source code, all information like:copyright must remain.
 * Official website only: http://oxwall.a6.pl
 * Full license available at: http://oxwall.a6.pl


 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
***/


OW::getRouter()->addRoute(new OW_Route('map', 'map', "MAP_CTRL_Map", 'index'));
OW::getRouter()->addRoute(new OW_Route('map.index', 'map', "MAP_CTRL_Map", 'index'));
OW::getRouter()->addRoute(new OW_Route('map.searchtabcat', 'map/tabc/:ctab', "MAP_CTRL_Map", 'indexcat'));
OW::getRouter()->addRoute(new OW_Route('map.searchtab', 'map/tab/:ctab', "MAP_CTRL_Map", 'index'));
OW::getRouter()->addRoute(new OW_Route('map.search', 'map/search', "MAP_CTRL_Map", 'index'));
OW::getRouter()->addRoute(new OW_Route('map.edit', 'map/edit/:id_mark', "MAP_CTRL_Map", 'index'));
OW::getRouter()->addRoute(new OW_Route('map.del', 'map/del/:id_mark', "MAP_CTRL_Map", 'del'));
OW::getRouter()->addRoute(new OW_Route('map.zoom', 'map/zoom/:id_markz', "MAP_CTRL_Map", 'indexzoom'));

OW::getRouter()->addRoute(new OW_Route('map.showall', 'map/show/users/', "MAP_CTRL_Map", 'indexshowall'));
OW::getRouter()->addRoute(new OW_Route('map.showfrieds', 'map/show/friends/', "MAP_CTRL_Map", 'indexshowfriends'));
//OW::getRouter()->addRoute(new OW_Route('map.index2', 'map/:id_user/:id_page/:title', "MAP_CTRL_Map", 'index'));
///OW::getRouter()->addRoute(new OW_Route('map.index', 'map', "MAP_CTRL_Map", 'index'));
//OW::getRouter()->addRoute(new OW_Route('map.editpage', 'editpage/:id_editpage', "MAP_CTRL_Map", 'editpage'));
//OW::getRouter()->addRoute(new OW_Route('map.editpage2', 'map/editpage/:id_editpage', "MAP_CTRL_Map", 'editpage'));
OW::getRouter()->addRoute(new OW_Route('map.get', 'map/get/:id_mark/:ss/:pname', "MAP_CTRL_Map", 'index_ajax_showpage'));
//OW::getRouter()->addRoute(new OW_Route('map.page', 'page/page/:id_user/:id_page/:title', "MAP_CTRL_Map", 'index_ajax_showpage'));
OW::getRouter()->addRoute(new OW_Route('map.admin', 'admin/plugins/map', "MAP_CTRL_Admin", 'dept'));


OW::getRouter()->addRoute(new OW_Route('map.scan', 'map/scan', "MAP_CTRL_Map", 'indexscan'));
OW::getRouter()->addRoute(new OW_Route('map.gmap', 'map/gmap', "MAP_CTRL_Map", 'indexgmap'));
OW::getRouter()->addRoute(new OW_Route('map.saveprofile', 'map/saveprofile', "MAP_CTRL_Map", 'indexsaveprofile'));
OW::getRouter()->addRoute(new OW_Route('map.getprofile', 'map/getprofile/:idprof/:ss', "MAP_CTRL_Map", 'indexgetprofile'));
OW::getRouter()->addRoute(new OW_Route('map.ginfo', 'map/ginfo/:id_markz', "MAP_CTRL_Map", 'indexginfo'));
OW::getRouter()->addRoute(new OW_Route('map.confowner', 'map/confowner/:checkowner', "MAP_CTRL_Map", 'indexmapconf'));
OW::getRouter()->addRoute(new OW_Route('map.downloadapp', 'map/downloadapplication', "MAP_CTRL_Map", 'indexdownloadapp'));
OW::getRouter()->addRoute(new OW_Route('map.adsense', 'map/adsense', "MAP_CTRL_Map", 'indexadsense'));
OW::getRouter()->addRoute(new OW_Route('map.checkmobile', 'map/checkmobile', "MAP_CTRL_Map", 'indexcheckmobile'));


$config = OW::getConfig();
if ( !$config->configExists('map', 'support_mobile_app') ){
    $config->addConfig('map', 'support_mobile_app', '0', '');
}




/*
//OW::getPluginManager()->addPluginSettingsRouteName('map', 'map.admin');
$config = OW::getConfig();
if ( !$config->configExists('map', 'tabdisable_events') ){
    $config->addConfig('map', 'tabdisable_events', '0', '');
}
if ( !$config->configExists('map', 'tabdisable_news') ){
    $config->addConfig('map', 'tabdisable_news', '0', '');
}
*/
/*
$config = OW::getConfig();
if ( !$config->configExists('map', 'perpage') ){
    $config->addConfig('map', 'perpage', '300', '');
}
if ( !$config->configExists('map', 'tabdisable_fanpage') ){
    $config->addConfig('map', 'tabdisable_fanpage', '0', '');
}
if ( !$config->configExists('map', 'tabdisable_shop') ){
    $config->addConfig('map', 'tabdisable_shop', '0', '');
}$for_user
if ( !$config->configExists('map', 'show_owner') ){
    $config->addConfig('map', 'show_owner', '1', '');
}
*/


function map_add_userlist( BASE_CLASS_EventCollector $event )
{
    if (OW::getUser()->getId()){
        $event->add(
        array(
            'label' => OW::getLanguage()->text('map', 'mapp_menu_users'),
            'url' => OW::getRouter()->urlForRoute('map.showall')."#mapx",
            'iconClass' => 'ow_ic_places',
            'key' => 'mapu',
            'order' => 6
        )
        );
    }else{
        $event->add(
        array(
            'label' => OW::getLanguage()->text('map', 'mapp_menu_users'),
            'url' => OW::getRouter()->urlForRoute('static_sign_in'),
            'iconClass' => 'ow_ic_places',
            'key' => 'mapu',
            'order' => 6
        )
        );
    }

 if ( OW::getPluginManager()->isPluginActive('friends')){
    if (OW::getUser()->getId()){
        $event->add(
        array(
            'label' => OW::getLanguage()->text('map', 'mapp_menu_friends'),
            'url' => OW::getRouter()->urlForRoute('map.showfrieds')."#mapx",
            'iconClass' => 'ow_ic_places',
            'key' => 'mapf',
            'order' => 7
        )
        );
    }else{
        $event->add(
        array(
            'label' => OW::getLanguage()->text('map', 'mapp_menu_friends'),
            'url' => OW::getRouter()->urlForRoute('static_sign_in'),
            'iconClass' => 'ow_ic_places',
            'key' => 'mapf',
            'order' => 7
        )
        );
    }
 }

}
OW::getEventManager()->bind('base.add_user_list', 'map_add_userlist');



function map_set_credits_action_tool( BASE_CLASS_EventCollector $event )
{
//echo "--".OW::getUser()->isAuthorized('fanpage');
//    if ( !OW::getUser()->isAuthorized('fanpage') ){return;}
//echo "--".$params['userId'];exit;    
    $params = $event->getParams();
//print_r($params);
//return;
    if ( empty($params['userId']) ){
        return;
    }else if (!OW::getUser()->getId()){
        return;
    }else if (!isset($params['userId']) OR $params['userId']!=OW::getUser()->getId()) {
        return;
    }
//echo $params['userId'];
//    echo MAP_BOL_Service::getInstance()->is_file_application();exit;
    if (OW::getConfig()->getValue('map', 'support_mobile_app')==1 AND MAP_BOL_Service::getInstance()->is_file_application()){
//    if (OW::getConfig()->getValue('map', 'support_mobile_app')==1 AND is_file(OW::getPluginManager()->getPlugin('map')->getRootDir()."map_mobile.apk")){
        $linkId = 'fp' . rand(1001, 1000000);
        $user = BOL_UserService::getInstance()->getUserName((int)$params['userId']);

        $resultArray = array(
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LABEL => OW::getLanguage()->text('map', 'setup_mobile_app'),
                BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_HREF => OW_URL_HOME.'map/confowner/'.$params['userId'],
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_ID => $linkId
        );

        $event->add($resultArray);
    }

}
OW::getEventManager()->bind(BASE_CMP_ProfileActionToolbar::EVENT_NAME, 'map_set_credits_action_tool');


