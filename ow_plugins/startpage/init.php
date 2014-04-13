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


//OW::getRouter()->addRoute(new OW_Route('startpage', 'startpage/:id_user/:id_page/:title', "STARTPAGE_CTRL_Stratpage", 'index'));
OW::getRouter()->addRoute(new OW_Route('startpagex', 'start', "STARTPAGE_CTRL_Startpage", 'indexstrat'));
OW::getRouter()->addRoute(new OW_Route('startpagexwolang', 'startx', "STARTPAGE_CTRL_Startpage", 'index'));
//OW::getRouter()->addRoute(new OW_Route('startpagexwolangnone', 'startpage/nolang', "STARTPAGE_CTRL_Startpage", 'index'));
OW::getRouter()->addRoute(new OW_Route('startpage', 'startpage', "STARTPAGE_CTRL_Startpage", 'index'));
//OW::getRouter()->addRoute(new OW_Route('startpage.indextab', 'startpage/:selecttab', "STARTPAGE_CTRL_Stratpage", 'index'));
OW::getRouter()->addRoute(new OW_Route('startpage.checkx', 'startpage/check', "STARTPAGE_CTRL_Startpage", 'index_ajax_showpage'));
OW::getRouter()->addRoute(new OW_Route('startpage.checkxf', 'startpage/checkf', "STARTPAGE_CTRL_Startpage", 'index_ajax_showpagef'));
OW::getRouter()->addRoute(new OW_Route('startpage.admin', 'admin/plugins/startpage', "STARTPAGE_CTRL_Admin", 'dept'));

//OW::getRouter()->removeRoute('base_page_404');

//OW::getRouter()->removeRoute('base_join');
//OW::getRouter()->removeRoute('join');
//OW::getRouter()->addRoute(new OW_Route('base_join', 'join346645645', 'STARTPAGE_CTRL_Stratpage', 'index'));
//OW::getRouter()->addRoute(new OW_Route('base_join', 'join', 'STARTPAGE_CTRL_Stratpage', 'index'));
//OW::getRouter()->addRoute(new OW_Route('base_join', 'join123', 'STARTPAGE_CTRL_Stratpage', 'index'));
/*
$config = OW::getConfig();
if ( !$config->configExists('startpage', 'after_login_backto') ){
    $config->addConfig('startpage', 'after_login_backto', 'index', '');
}
if ( !$config->configExists('startpage', 'show_agree_therm_of_use') ){
    $config->addConfig('startpage', 'show_agree_therm_of_use', '1', '');
}
if ( !$config->configExists('startpage', 'therm_of_use_url') ){
    $config->addConfig('startpage', 'therm_of_use_url', 'terms-of-use', '');
}
*/
/*
$config = OW::getConfig();
if ( !$config->configExists('startpage', 'try_use_mytheme') ){
    $config->addConfig('startpage', 'try_use_mytheme', '0', '');
}
*/
/*
$config = OW::getConfig();
if ( !$config->configExists('startpage', 'disable_force_imagechache') ){
    $config->addConfig('startpage', 'disable_force_imagechache', '0', '');
}
*/
/*
$config = OW::getConfig();
if ( !$config->configExists('startpage', 'allow_upload_avatar') ){
    $config->addConfig('startpage', 'allow_upload_avatar', '1', '');
}
if ( !$config->configExists('startpage', 'widgetjavacode') ){
    $config->addConfig('startpage', 'widgetjavacode', '', '');
}
if ( !$config->configExists('startpage', 'toptitle') ){
    $config->addConfig('startpage', 'toptitle', 'Welcome to our website...', '');
}
if ( !$config->configExists('startpage', 'force_hide_homebutton') ){
    $config->addConfig('startpage', 'force_hide_homebutton', '0', '');
}
if ( !$config->configExists('startpage', 'background_color') ){
    $config->addConfig('startpage', 'background_color', '#fff', '');
}
if ( !$config->configExists('startpage', 'background_image') ){
    $config->addConfig('startpage', 'background_image', '', '');
}
if ( !$config->configExists('startpage', 'background_image_pos') ){
    $config->addConfig('startpage', 'background_image_pos', 'center center', '');
}
*/
/*
$config = OW::getConfig();
if ( !$config->configExists('startpage', 'backgrounc_cards_color') ){
    $config->addConfig('startpage', 'backgrounc_cards_color', '', '');
}
if ( !$config->configExists('startpage', 'border_cards_color') ){
    $config->addConfig('startpage', 'border_cards_color', '', '');
}
*/
/*
$config = OW::getConfig();
if ( !$config->configExists('startpage', 'hide_accouttype') ){
    $config->addConfig('startpage', 'hide_accouttype', '0', '');
}
if ( !$config->configExists('startpage', 'logo_margin_left') ){
    $config->addConfig('startpage', 'logo_margin_left', '25', '');
}
*/
/*
$config = OW::getConfig();
if ( !$config->configExists('startpage', 'disable_startpage') ){
    $config->addConfig('startpage', 'disable_startpage', '0', '');
}
if ( !$config->configExists('startpage', 'theme_image_top') ){
    $config->addConfig('startpage', 'theme_image_top', '', '');
}
if ( !$config->configExists('startpage', 'theme_image_cover') ){
    $config->addConfig('startpage', 'theme_image_cover', '', '');
}
if ( !$config->configExists('startpage', 'theme_header_width') ){
    $config->addConfig('startpage', 'theme_header_width', '100%', '');
}
if ( !$config->configExists('startpage', 'theme_header_height') ){
    $config->addConfig('startpage', 'theme_header_height', '45px', '');
}
if ( !$config->configExists('startpage', 'theme_center_column') ){
    $config->addConfig('startpage', 'theme_center_column', '', '');
}
if ( !$config->configExists('startpage', 'theme_header_backgroundcolor') ){
    $config->addConfig('startpage', 'theme_header_backgroundcolor', '#009de0', '');
}
if ( !$config->configExists('startpage', 'theme_slogan') ){
    $config->addConfig('startpage', 'theme_slogan', '', '');
}
if ( !$config->configExists('startpage', 'theme_slogan_desc') ){
    $config->addConfig('startpage', 'theme_slogan_desc', '', '');
}
if ( !$config->configExists('startpage', 'curent_theme') ){
    $config->addConfig('startpage', 'curent_theme', 'default', '');
}
if ( !$config->configExists('startpage', 'theme_seo_title') ){
    $config->addConfig('startpage', 'theme_seo_title', 'Join Us', '');
}
if ( !$config->configExists('startpage', 'theme_seo_keywords') ){
    $config->addConfig('startpage', 'theme_seo_keywords', '', '');
}
if ( !$config->configExists('startpage', 'theme_seo_desc') ){
    $config->addConfig('startpage', 'theme_seo_desc', 'Join Us', '');
}
*/
//print_r(OW::getConfig());exit;
    function startpage_set_action_tool( )
    {
//        OW::getDocument()->addScript(OW_URL_HOME.'ow_static/plugins/gallery/jquery.prettyPhoto.js');
//        OW::getDocument()->addStyleSheet(OW_URL_HOME.'ow_static/plugins/gallery/prettyPhoto.css');

//        OW::getDocument()->addScript(OW_URL_HOME.'ow_static/plugins/gallery/ext/gallery.js');
//        OW::getDocument()->addStyleSheet(OW_URL_HOME.'ow_static/plugins/gallery/ext/gallery.css');

//    OW::getDocument()->addScript(OW_URL_HOME.'ow_static/plugins/gallery/extr/jquery.raty.min.js');
//    OW::getDocument()->addStyleSheet(OW_URL_HOME.'ow_static/plugins/gallery/extr/raty.css');

    
//    OW::getDocument()->addScript(OW_URL_HOME.'ow_static/plugins/gallery/extr/jquery.raty.min.js');
//    OW::getDocument()->addStyleSheet(OW_URL_HOME.'ow_static/plugins/gallery/extr/raty.css');


$script  = "$(document).ready(function(){
$('#startpage_pop').click(function(){
    $('#startpage_overlay_form').fadeIn(1000);
        $('#startpage_overlay_form').css({'display':'block'});
        startpage_positionPopup();
    });
 
    $('#startpage_close').click(function(){
//        $('#startpage_overlay_form').css({'display':'none'});
        $('#startpage_overlay_form').fadeOut(500);
    });
});

$('#save_status').click(function(){
    $('#startpage_overlay_form').submit();
});
 
function startpage_positionPopup(){
    $('#startpage_overlay_form').css('display','block');

    $('#startpage_overlay_form').css({
//        left: -$('#startpage_overlay_form').width() /2  ,
//        'margin-left': $('#startpage_pop').position().left,
//        'margin-left': $('#aron_my_avatar_widget').width() /2 -$('#startpage_overlay_form').width() /2  ,
        'left': $('#aron_my_avatar_widget').width() /2 -$('#startpage_overlay_form').width() /2  ,
        top: -$('#startpage_overlay_form').height() / 7,
        position:'absolute',
        'z-index':'9999'
    });
}
 
";
        OW::getDocument()->addOnloadScript($script);

$add_style="
<style>
#startpage_overlay_form{
position: absolute;
border: 3px solid gray;
padding: 6px;
padding-bottom:12px;
background: white;
max-width: 200px;
width: 180px;
height: 110px;
float:left;
}



</style>
";
//background: none repeat scroll 0% 0% rgb(255, 255, 255);
//border: 1px solid rgb(231, 231, 231);


        OW::getDocument()->appendBody($add_style);
    }

    if ( !defined('OW_CRON') ){
        OW::getEventManager()->bind('core.app_init', 'startpage_set_action_tool');
    }




//------------delete balow


function open_user_activity( )
{
    if (OW::getUser()->getId()>0 ){return;}

//    OW::getDocument()->addScript(OW_URL_HOME.'ow_static/plugins/startpage/jquery.validate.js');
//    OW::getDocument()->addStyleSheet(OW_URL_HOME.'ow_static/plugins/startpage/.css');

    STARTPAGE_BOL_Service::getInstance()->check_user_activity();

}
//OW::getDocument()->setTemplate(OW::getThemeManager()->getMasterPageTemplate('club'));
//echo OW::getThemeManager()->getMasterPageTemplate('club');exit;
//echo OW::getThemeManager()->getSelectedTheme()->getDto()->getName();
//OW::getThemeManager()->themeService->getThemeObjectByName(BOL_ThemeService::DEFAULT_THEME);
//OW_ThemeManager::getInstance()->themeService->getThemeObjectByName('club');


OW::getEventManager()->bind('core.app_init', 'open_user_activity');
//OW::getEventManager()->bind('feed.on_item_render', 'open_user_activity');

//OW::getEventManager()->bind(OW_EventManager::ON_USER_REGISTER, 'open_user_activity');
//OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_USER_REGISTER, 'open_user_activity');
//OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_USER_LOGIN, 'open_user_activity');
//OW::getEventManager()->bind(OW_EventManager::ON_BEFORE_USER_LOGIN, 'open_user_activity');

function force_login(){
//echo $_SERVER["HTTP_REFERER"];exit;
//    if ($_SERVER["HTTP_REFERER"]) return;
    if (OW::getUser()->getId()>0 ){return;}
    STARTPAGE_BOL_Service::getInstance()->check_user_activity_log();
/*
$script ="";
//$script .= '<script type="text/javascript">';
    //$('#console_item_51a239a755e59').click(function(){new OW_FloatBox({ $contents: $('#base_cmp_floatbox_ajax_signin')});});
$script .= "
new OW_FloatBox({ \$contents: \$('#base_cmp_floatbox_ajax_signin')});
//(function(_scope) {
//        new OW_FloatBox({\$contents:\$('#base_cmp_floatbox_ajax_signin')});
//})(window);
";

//$script .= "</script>";
//if (!OW::getUser()->getId()){
 OW::getDocument()->addOnloadScript($script);      
//}
*/
//    OW::getDocument()->appendBody($script);    
}
if ( !defined('OW_CRON') ){
    OW::getEventManager()->bind('core.finalize', 'force_login');
}














//

