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


class STARTPAGE_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    public function dept()
    {
        $content="";
        $this->setPageTitle(OW::getLanguage()->text('startpage', 'admin_deptx_title'));
        $this->setPageHeading(OW::getLanguage()->text('startpage', 'admin_deptx_heading'));    
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();//iss admin
        $curent_url=OW_URL_HOME;
        $config = OW::getConfig();
        $content="";
        if (!isset($_POST['save'])) $_POST['save']="";
        if ($is_admin AND $id_user>0 AND $_POST['save']=="besave"){



//            $config->saveConfig('startpage', 'backgrounc_cards_color', $_POST['c_backgrounc_cards_color']);
//            $config->saveConfig('startpage', 'border_cards_color', $_POST['c_border_cards_color']);

//            $config->saveConfig('startpage', 'show_startpage_maxitems', $_POST['c_show_startpage_maxitems']);
//            $config->saveConfig('startpage', 'show_startpage_only_friends', $_POST['c_show_startpage_only_friends']);
//            $config->saveConfig('startpage', 'show_profile_inwidget_startpage', $_POST['c_show_profile_inwidget_startpage']);
//            $config->saveConfig('startpage', 'show_small_startpage_list', $_POST['c_show_small_startpage_list']);

            $config->saveConfig('startpage', 'disable_startpage', $_POST['c_disable_startpage']);
            $config->saveConfig('startpage', 'curent_theme', $_POST['c_curent_theme']);
            $config->saveConfig('startpage', 'theme_header_width', $_POST['c_theme_header_width']);
            $config->saveConfig('startpage', 'theme_header_height', $_POST['c_theme_header_height']);
            $config->saveConfig('startpage', 'theme_center_column', $_POST['c_theme_center_column']);
            $config->saveConfig('startpage', 'theme_header_backgroundcolor', $_POST['c_theme_header_backgroundcolor']);
            $config->saveConfig('startpage', 'theme_slogan', $_POST['c_theme_slogan']);
            $config->saveConfig('startpage', 'theme_slogan_desc', $_POST['c_theme_slogan_desc']);

            $config->saveConfig('startpage', 'theme_seo_title', $_POST['c_theme_seo_title']);
            $config->saveConfig('startpage', 'theme_seo_keywords', $_POST['c_theme_seo_keywords']);
            $config->saveConfig('startpage', 'theme_seo_desc', $_POST['c_theme_seo_desc']);

            $config->saveConfig('startpage', 'force_for_guest', $_POST['c_force_for_guest']);
            $config->saveConfig('startpage', 'force_hide_homebutton', $_POST['c_force_hide_homebutton']);

            $config->saveConfig('startpage', 'hide_accouttype', $_POST['c_hide_accouttype']);
            $config->saveConfig('startpage', 'disable_force_imagechache', $_POST['c_disable_force_imagechache']);
            $config->saveConfig('startpage', 'logo_margin_left', $_POST['c_logo_margin_left']);

            $config->saveConfig('startpage', 'allow_upload_avatar', $_POST['c_allow_upload_avatar']);

            $config->saveConfig('startpage', 'try_use_mytheme', $_POST['c_try_use_mytheme']);

            $config->saveConfig('startpage', 'allow_show_captha', $_POST['c_allow_show_captha']);

            $config->saveConfig('startpage', 'show_gender', $_POST['c_show_gender']);
            $config->saveConfig('startpage', 'show_realname', $_POST['c_show_realname']);
            $config->saveConfig('startpage', 'show_eage', $_POST['c_show_eage']);

            $config->saveConfig('startpage', 'show_agree_newsletter', $_POST['c_show_agree_newsletter']);
            $config->saveConfig('startpage', 'show_agree_therm_of_use', $_POST['c_show_agree_therm_of_use']);
            $config->saveConfig('startpage', 'therm_of_use_url', $_POST['c_therm_of_use_url']);

            $config->saveConfig('startpage', 'after_login_backto', $_POST['c_after_login_backto']);

//            if (isset($_POST['w_widgetjavacode'])){
                $config->saveConfig('startpage', 'widgetjavacode', $_POST['w_widgetjavacode']);
                $config->saveConfig('startpage', 'toptitle', $_POST['w_toptitle']);
//            }

//    $config->addConfig('startpage', 'background_color', '#fff', '');
//    $config->addConfig('startpage', 'background_image', '', '');
//    $config->addConfig('startpage', 'background_image_pos', 'center center', '');
            $config->saveConfig('startpage', 'background_color', $_POST['c_background_color']);
            $config->saveConfig('startpage', 'background_image_pos', $_POST['c_background_image_pos']);

            $config->saveConfig('startpage', 'content_background', $_POST['c_content_background']);
            $config->saveConfig('startpage', 'content_text_color', $_POST['c_content_text_color']);
            $config->saveConfig('startpage', 'topbar_background', $_POST['c_topbar_background']);
            $config->saveConfig('startpage', 'topbar_text_color', $_POST['c_topbar_text_color']);








        $ct=OW::getConfig()->getValue('startpage', 'curent_theme');
        if (!$ct) $ct="default";
        $pluginStaticD=OW::getPluginManager()->getPlugin('startpage')->getStaticDir();
        $plname="startpage";
        $source=OW_DIR_PLUGIN.$plname. DS.'static'. DS;
        $pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.$plname.DS;

//echo $pluginStaticDir;exit;
/*
$imgp=$pluginStaticDir."themes".DS.$default_theme.DS."image.jpg";
$imgl=$pluginStaticDir."themes".DS.$default_theme.DS."logo.jpg";


$imgp_u=$pluginStaticDir."themes".DS.$default_theme.DS.OW::getConfig()->getValue('startpage', 'theme_image_cover');
$imgl_u=$pluginStaticDir."themes".DS.$default_theme.DS.OW::getConfig()->getValue('startpage', 'theme_image_top');
//echo $pluginStaticU;exit;
if (strlen(OW::getConfig()->getValue('startpage', 'theme_image_cover'))>4 AND is_file($imgp_u)){
    $imgp=$pluginStaticU."themes".DS.$default_theme.DS.OW::getConfig()->getValue('startpage', 'theme_image_cover');
}else{
    $imgp=$pluginStaticU."themes".DS.$default_theme.DS."image.jpg";
}
if (strlen(OW::getConfig()->getValue('startpage', 'theme_image_top'))>4 AND is_file($imgl_u)){
    $imgl=$pluginStaticU."themes".DS.$default_theme.DS.OW::getConfig()->getValue('startpage', 'theme_image_top');
}else{
    $imgl=$pluginStaticU."themes".DS.$default_theme.DS."logo.jpg";
}

*/
//print_r($_FILES);exit;
//echo $pluginStaticD;exit;
            if (isset($_POST['c_content_background_image_del'])){
                $dest=$pluginStaticDir."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'content_background_image');
                $storage = OW::getStorage();
                if ( $storage->fileExists($dest) ){
                    $storage->removeFile($dest);
                }
                $config->saveConfig('startpage', 'content_background_image', '');
            }
            if (isset($_FILES['c_content_background_image']) AND !$_FILES['c_content_background_image']['error'] AND $_FILES['c_content_background_image']['size']>0){
                $ext=substr($_FILES['c_content_background_image']['name'],-3);
                $dest=$pluginStaticDir."themes".DS.$ct.DS."image_content.".$ext;
                $storage = OW::getStorage();
                $storage->copyFile($_FILES['c_content_background_image']['tmp_name'],$dest);
                $config->saveConfig('startpage', 'content_background_image', 'image_content.'.$ext);
            }


            if (isset($_POST['c_topbar_background_image_del'])){
                $dest=$pluginStaticDir."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'topbar_background_image');
                $storage = OW::getStorage();
                if ( $storage->fileExists($dest) ){
                    $storage->removeFile($dest);
                }
                $config->saveConfig('startpage', 'topbar_background_image', '');
            }
            if (isset($_FILES['c_topbar_background_image']) AND !$_FILES['c_topbar_background_image']['error'] AND $_FILES['c_topbar_background_image']['size']>0){
                $ext=substr($_FILES['c_topbar_background_image']['name'],-3);
                $dest=$pluginStaticDir."themes".DS.$ct.DS."image_topbar.".$ext;
                $storage = OW::getStorage();
                $storage->copyFile($_FILES['c_topbar_background_image']['tmp_name'],$dest);
                $config->saveConfig('startpage', 'topbar_background_image', 'image_topbar.'.$ext);
            }


            if (isset($_POST['c_theme_image_top_del'])){
                $dest=$pluginStaticDir."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'theme_image_top');
                $storage = OW::getStorage();
                if ( $storage->fileExists($dest) ){
                    $storage->removeFile($dest);
                }
                $config->saveConfig('startpage', 'theme_image_top', '');
            }
            if (isset($_POST['c_theme_image_cover_del'])){
                $dest=$pluginStaticDir."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'theme_image_cover');
                $storage = OW::getStorage();
                if ( $storage->fileExists($dest) ){
                    $storage->removeFile($dest);
                }
                $config->saveConfig('startpage', 'theme_image_cover', '');
            }
            if (isset($_FILES['c_theme_image_top']) AND !$_FILES['c_theme_image_top']['error'] AND $_FILES['c_theme_image_top']['size']>0){
                $ext=substr($_FILES['c_theme_image_top']['name'],-3);
    //            $config->saveConfig('startpage', 'theme_image_top', $_POST['c_theme_image_top']);
                $dest=$pluginStaticDir."themes".DS.$ct.DS."logo_u.".$ext;
//                STARTPAGE_BOL_Service::STARTPAGE_BOL_Service->file_copy($_FILES['theme_image_top']['tmp_name'],$dest);
                $storage = OW::getStorage();
                $storage->copyFile($_FILES['c_theme_image_top']['tmp_name'],$dest);
//echo $_FILES['c_theme_image_top']['tmp_name']."--".$dest;
                $config->saveConfig('startpage', 'theme_image_top', 'logo_u.'.$ext);
            }
            if (isset($_FILES['c_theme_image_cover']) AND !$_FILES['c_theme_image_cover']['error'] AND $_FILES['c_theme_image_cover']['size']>0){
    //            $config->saveConfig('startpage', 'theme_image_cover', $_POST['c_theme_image_cover']);
                $ext=substr($_FILES['c_theme_image_cover']['name'],-3);
                $dest=$pluginStaticDir."themes".DS.$ct.DS."image_u.".$ext;
//                STARTPAGE_BOL_Service::STARTPAGE_BOL_Service->file_copy($_FILES['theme_image_cover']['tmp_name'],$dest);
                $storage = OW::getStorage();
                $storage->copyFile($_FILES['c_theme_image_cover']['tmp_name'],$dest);
                $config->saveConfig('startpage', 'theme_image_cover', 'image_u.'.$ext);
            }
//print_r($_FILES);
//exit;
            if (isset($_POST['c_background_image_del'])){
                $dest=$pluginStaticDir."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'background_image');
                $storage = OW::getStorage();
                if ( $storage->fileExists($dest) ){
                    $storage->removeFile($dest);
                }
                $config->saveConfig('startpage', 'background_image', '');
            }
            if (isset($_FILES['c_background_image']) AND !$_FILES['c_background_image']['error'] AND $_FILES['c_background_image']['size']>0){
                $ext=substr($_FILES['c_background_image']['name'],-3);
                $dest=$pluginStaticDir."themes".DS.$ct.DS."background.".$ext;
                $storage = OW::getStorage();
                $storage->copyFile($_FILES['c_background_image']['tmp_name'],$dest);
                $config->saveConfig('startpage', 'background_image', 'background.'.$ext);
            }

//print_r($_FILES);
//exit;
            
/*
            if ($_POST['c_protectkey']){
                $rss_protect_key=$_POST['c_protectkey'];
            }else{
                $rss_protect_key=substr(md5(date('d-m-Y H:i:s')),0,10);
            }
            if ($_POST['c_generateforusers']){
                $generateforusers=1;
            }else{
                $generateforusers=0;
            }
            $rss_max_items_eachplugin=$_POST['c_maxitemseachplugin'];
            if (!$rss_max_items_eachplugin) $rss_max_items_eachplugin=10;
            if ($rss_max_items_eachplugin>50) $rss_max_items_eachplugin=50;
            $rss_max_items_sitemap=$_POST['c_maxitemsforsitemap'];
            if (!$rss_max_items_sitemap) $rss_max_items_sitemap=1000;
            if ($rss_max_items_sitemap>9999) $rss_max_items_sitemap=9999;

            $config->saveConfig('rss', 'rss_protect_key', $rss_protect_key);
            $config->saveConfig('rss', 'rss_generateforusers', $generateforusers);
            $config->saveConfig('rss', 'rss_max_items_eachplugin', $rss_max_items_eachplugin);
            $config->saveConfig('rss', 'rss_max_items_sitemap', $rss_max_items_sitemap);
*/
/*
            if (!$_POST['c_wall_perpage']) $_POST['c_wall_perpage']=12;
            $config->saveConfig('wall', 'wall_perpage', $_POST['c_wall_perpage']);

            if (!$_POST['c_items_top10']) $_POST['c_items_top10']=0;
            $config->saveConfig('wall', 'items_top10', $_POST['c_items_top10']);
            if ($_POST['c_admin_membercanaddgame']=="") $_POST['c_admin_membercanaddgame']=0;
            $config->saveConfig('wall', 'admin_membercanaddgame', $_POST['c_admin_membercanaddgame']);

            if ($_POST['c_admin_show_gameinfo']=="") $_POST['c_admin_show_gameinfo']=0;
            $config->saveConfig('wall', 'admin_show_gameinfo', $_POST['c_admin_show_gameinfo']);

            if ($_POST['c_admin_show_latest']=="") $_POST['c_admin_show_latest']=0;
            $config->saveConfig('wall', 'admin_show_latest', $_POST['c_admin_show_latest']);
*/
            OW::getApplication()->redirect($curent_url."admin/plugins/startpage");
        }


$pluginStaticD=OW::getPluginManager()->getPlugin('startpage')->getStaticDir();
$content .="<script>";
$content .="
$(function () {
";
if (OW::getConfig()->getValue('startpage', 'disable_startpage')=="2"){
$content .="
$('.hide_once_x').hide();
";
}else{
$content .="
$('.hide_once_x').show();
";
}

$content .="
$('#c_disable_startpage').change(function() {
    if ($(this).val()==2){
        $('#more_sign').css('display','block');
        $('.hide_once_x').hide();
    }else{
        $('#more_sign').css('display','none');
        $('.hide_once_x').show();
    }
});
});
";
$content .="</script>";


//$content .="<div class=\"ow_content ow_table_1 ow_form ow_stdmargin\">";
        $content .="<form action=\"".$curent_url."admin/plugins/startpage\" method=\"post\" enctype=\"multipart/form-data\"  style=\"width:100%;\">";
        $content .="<input type=\"hidden\" name=\"save\" value=\"besave\">";
        $content .="<table style=\"width:100%;\" class=\"ow_table_3 ow_form ow_stdmargin\">";

        $content .="<th class=\"ow_name ow_txtleft\" colspan=\"2\">
            <span class=\"ow_section_icon ow_ic_gear_wheel\">".OW::getLanguage()->text('startpage', 'admin_dept_title')."</span>
        </th>";
/*
        $content .="<tr class=\"ow_alt1\">";
        $content .="<td class=\"ow_label\" style=\"width:50%;\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'disable_startpage').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'show_startpage_maxitems');
        if (!$curent) $curent=0;
        $content .="<input type=\"text\" name=\"c_show_startpage_maxitems\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">";
        $content .="</td>";
        $content .="</tr>";
*/
        $content .="<tr class=\"ow_alt1\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;vertical-align:top;\" valign=\"top\">";
        $content .="<b>".OW::getLanguage()->text('startpage','disable_startpage').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\" style=\"vertical-align:top;\" valign=\"top\">";
        $content .="<select id=\"c_disable_startpage\" name=\"c_disable_startpage\">";
        if (OW::getConfig()->getValue('startpage', 'disable_startpage')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'disable_startpage')=="0" OR OW::getConfig()->getValue('startpage', 'disable_startpage')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";

        if (OW::getConfig()->getValue('startpage', 'disable_startpage')=="2") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"2\">".OW::getLanguage()->text('startpage', 'force_run_signin_onfirstvisit')."</option>";

        $content .="</select>";

        if (OW::getConfig()->getValue('startpage', 'disable_startpage')=="2"){
            $content .="<div class=\"clearfix\" id=\"more_sign\" style=\"display:block;\">";
        }else{
            $content .="<div class=\"clearfix\" id=\"more_sign\" style=\"display:none;\">";
        }
            $content .="<table style=\"width:100%;margin:auto;\">";

                $content .="<tr class=\"ow_alt1\">";
                $content .="<td class=\"ow_label Xow_left\" colspan=\"2\" style=\"text-align:left;\">";
                $content .="<b>".OW::getLanguage()->text('startpage', 'toptitle').":</b>";
                $content .="</td>";
                $content .="</tr>";
                $content .="<tr>";
                $content .="<td class=\"ow_value\" colspan=\"2\">";
                $curent=OW::getConfig()->getValue('startpage', 'toptitle');
                $content .="<input type=\"text\" name=\"w_toptitle\" value=\"".$curent."\" style=\"display:inline-block;width:90%;\">";
                $content .="</td>";
                $content .="</tr>";

                $content .="<tr class=\"ow_alt1\">";
                $content .="<td class=\"ow_label Xow_left\" colspan=\"2\" style=\"text-align:left;\">";
                $content .="<b>".OW::getLanguage()->text('startpage', 'widget_javasctipt_code').":</b>";
                $content .="</td>";
                $content .="</tr>";
                $content .="<tr>";
                $content .="<td class=\"ow_value\" colspan=\"2\">";
                $curent=OW::getConfig()->getValue('startpage', 'widgetjavacode');
                $content .="<textarea name=\"w_widgetjavacode\" style=\"width:100%;margin:auto;height:150px;\">".$curent."</textarea>";
                $content .="</td>";
                $content .="</tr>";



/*
                $content .="<tr class=\"ow_alt1\">";
                $content .="<td class=\"ow_label\" style=\"width:50%;\">";
                $content .="<b>".OW::getLanguage()->text('startpage', 'logo_margin_left').":</b>";
                $content .="</td>";
                $content .="<td class=\"ow_value\">";
                $curent=OW::getConfig()->getValue('startpage', 'logo_margin_left');
                $content .="<input type=\"text\" name=\"c_logo_margin_left\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\"> px";
                $content .="</td>";
                $content .="</tr>";
*/
            $content .="</table>";
        $content .="</div>";

        $content .="</td>";
        $content .="</tr>";




        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
//        $content .="<b>".OW::getLanguage()->text('startpage','allow_show_captha').":</b>";
        $content .="<b>".OW::getLanguage()->text('startpage','after_login_backto').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_after_login_backto\">";
        if (OW::getConfig()->getValue('startpage', 'after_login_backto')=="index" OR OW::getConfig()->getValue('startpage', 'after_login_backto')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"index\">".OW::getLanguage()->text('startpage', 'page_index')."</option>";
        if (OW::getConfig()->getValue('startpage', 'after_login_backto')=="dashboard" ) $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"dashboard\">".OW::getLanguage()->text('startpage', 'page_dashboard')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";





        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','try_use_mytheme').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_try_use_mytheme\">";
        if (OW::getConfig()->getValue('startpage', 'try_use_mytheme')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'try_use_mytheme')=="0" OR OW::getConfig()->getValue('startpage', 'try_use_mytheme')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
//        $content .="<b>".OW::getLanguage()->text('startpage','allow_show_captha').":</b>";
        $content .="<b>".OW::getLanguage()->text('startpage','allow_show_captha_try').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_allow_show_captha\">";
        if (OW::getConfig()->getValue('startpage', 'allow_show_captha')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'allow_show_captha')=="0" OR OW::getConfig()->getValue('startpage', 'allow_show_captha')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','force_for_guest').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_force_for_guest\">";
        if (OW::getConfig()->getValue('startpage', 'force_for_guest')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'force_for_guest')=="0" OR OW::getConfig()->getValue('startpage', 'force_for_guest')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','force_hide_homebutton').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_force_hide_homebutton\">";
        if (OW::getConfig()->getValue('startpage', 'force_hide_homebutton')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'force_hide_homebutton')=="0" OR OW::getConfig()->getValue('startpage', 'force_hide_homebutton')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','hide_accouttype').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_hide_accouttype\">";
        if (OW::getConfig()->getValue('startpage', 'hide_accouttype')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'hide_accouttype')=="0" OR OW::getConfig()->getValue('startpage', 'hide_accouttype')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','disable_force_imagechache').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_disable_force_imagechache\">";
        if (OW::getConfig()->getValue('startpage', 'disable_force_imagechache')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'disable_force_imagechache')=="0" OR OW::getConfig()->getValue('startpage', 'disable_force_imagechache')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','allow_upload_avatar').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_allow_upload_avatar\">";
        if (OW::getConfig()->getValue('startpage', 'allow_upload_avatar')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'allow_upload_avatar')=="0" OR OW::getConfig()->getValue('startpage', 'allow_upload_avatar')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','show_gender').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_show_gender\">";
        if (OW::getConfig()->getValue('startpage', 'show_gender')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'show_gender')=="0" OR OW::getConfig()->getValue('startpage', 'show_gender')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>&nbsp;(Reguire question name: sex)";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','show_realname').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_show_realname\">";
        if (OW::getConfig()->getValue('startpage', 'show_realname')=="1" OR OW::getConfig()->getValue('startpage', 'show_realname')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'show_realname')=="0" ) $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>&nbsp;(Reguire question name: realname)";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','show_eage').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_show_eage\">";
        if (OW::getConfig()->getValue('startpage', 'show_eage')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'show_eage')=="0"  OR OW::getConfig()->getValue('startpage', 'show_eage')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>&nbsp;(Reguire question name: birthdate)";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','show_agree_newsletter1').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_show_agree_newsletter\">";
        if (OW::getConfig()->getValue('startpage', 'show_agree_newsletter')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'show_agree_newsletter')=="0" OR OW::getConfig()->getValue('startpage', 'show_agree_newsletter')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','show_agree_therm_of_use').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_show_agree_therm_of_use\">";
        if (OW::getConfig()->getValue('startpage', 'show_agree_therm_of_use')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'show_agree_therm_of_use')=="0" OR OW::getConfig()->getValue('startpage', 'show_agree_therm_of_use')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
            $curent=OW::getConfig()->getValue('startpage', 'therm_of_use_url');
            if (!$curent) $curent=$curent_url."terms-of-use";
            $content .="&nbsp;";
            $content .="<input type=\"text\" name=\"c_therm_of_use_url\" value=\"".$curent."\" style=\"display:inline-block;width:90%;\"><br/><i>Default: <b>".$curent_url."terms-of-use</b><i>";
        $content .="</td>";
        $content .="</tr>";



        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\" style=\"min-width:200px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage','curent_theme').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_curent_theme\">";
        $curent=OW::getConfig()->getValue('startpage', 'curent_theme');
        if (!$curent) $curent="default";
/*
        if (OW::getConfig()->getValue('startpage', 'curent_theme')=="default") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"default\">Default</option>";
        if (OW::getConfig()->getValue('startpage', 'curent_theme')=="twocolumn") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"twocolumn\">2 Columns</option>";
        if (OW::getConfig()->getValue('startpage', 'curent_theme')=="withouttb") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"withouttb\">Without top bar</option>";
        if (OW::getConfig()->getValue('startpage', 'curent_theme')=="withouttb_2col") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"withouttb_2col\">Without top bar, 2 Columns</option>";
        if (OW::getConfig()->getValue('startpage', 'curent_theme')=="2column_wlogin") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"2column_wlogin\">2 Columns with login</option>";
*/

    $dirr =$pluginStaticD."themes".DS;
    $dh = @opendir( $dirr );
    if( false === $dh ) {
        return false;
    }
    while( $file = readdir( $dh )) {
        if( "." == $file || ".." == $file ){
            continue;
        }
        if (is_file($dirr.$file.DS."theme.info")){
            if (OW::getConfig()->getValue('startpage', 'curent_theme')==$file) $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"".$file."\">".mb_strtoupper($file)."</option>";
        }
    }
    closedir($dh);


/*
        if (OW::getConfig()->getValue('startpage', 'curent_theme')=="2012") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"2012\">2012 NEW!</option>";
*/
/*
        if (OW::getConfig()->getValue('startpage', 'curent_theme')=="mobile") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"mobile\">mobile</option>";
*/
/*
        if (OW::getConfig()->getValue('startpage', 'curent_theme')=="modern_wlogin") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"modern_wlogin\">Modern theme 2013</option>";
*/
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";









        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('startpage','theme_image_top').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
//        $curent=OW::getConfig()->getValue('startpage', 'theme_center_column');
//        $content .="<textarea name=\"c_theme_center_column\" style=\"height:100px;width:100%;maegin:auto;\"></textarea>";
        $content .="<input type=\"file\" name=\"c_theme_image_top\" style=\"display:inline-block;min-width:100px;\">";

        $plname="startpage";
        $source=OW_DIR_PLUGIN.$plname. DS.'static'. DS;
        $pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.$plname.DS;
        $ct=OW::getConfig()->getValue('startpage', 'curent_theme');
        if (!$ct) $ct="default";
        $pluginStaticU=OW::getPluginManager()->getPlugin('startpage')->getStaticUrl();

        $imgl_u=$pluginStaticDir."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'theme_image_top');
//echo $pluginStaticU;exit;

        if (strlen(OW::getConfig()->getValue('startpage', 'theme_image_top'))>4 AND is_file($imgl_u)){
//            $imgl=$pluginStaticU."themes".DS.$default_theme.DS.OW::getConfig()->getValue('startpage', 'theme_image_top');
            $content .="<br/>";
            $content .="<img src=\"".$pluginStaticU."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'theme_image_top')."?fakecache=".rand(9999,999999)."\" style=\"max-width:200px;max-height:200px;\">";
            $content .="<br/>";
            $content .="<input type=\"checkbox\" name=\"c_theme_image_top_del\" valye=\"1\">&nbsp;<b style=\"color:#f00;\">".OW::getLanguage()->text('startpage','delete_image')."</b>";
        }

        $content .="</td>";
        $content .="</tr>";


        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'logo_margin_left').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'logo_margin_left');
        $content .="<input type=\"text\" name=\"c_logo_margin_left\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\"> px";
        $content .="</td>";
        $content .="</tr>";



        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('startpage','theme_image_cover').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
//        $curent=OW::getConfig()->getValue('startpage', 'theme_center_column');
//        $content .="<textarea name=\"c_theme_center_column\" style=\"height:100px;width:100%;maegin:auto;\"></textarea>";
        $content .="<input type=\"file\" name=\"c_theme_image_cover\" style=\"display:inline-block;min-width:100px;\">";

        $plname="startpage";
        $source=OW_DIR_PLUGIN.$plname. DS.'static'. DS;
        $pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.$plname.DS;
        $ct=OW::getConfig()->getValue('startpage', 'curent_theme');
        if (!$ct) $ct="default";
        $pluginStaticU=OW::getPluginManager()->getPlugin('startpage')->getStaticUrl();

        $imgp_u=$pluginStaticDir."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'theme_image_cover');
//echo $pluginStaticU;exit;
        if (strlen(OW::getConfig()->getValue('startpage', 'theme_image_cover'))>4 AND is_file($imgp_u)){
//            $imgp=$pluginStaticU."themes".DS.$default_theme.DS.OW::getConfig()->getValue('startpage', 'theme_image_cover');
            $content .="<br/>";
            $content .="<img src=\"".$pluginStaticU."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'theme_image_cover')."?fakecache=".rand(9999,999999)."\" style=\"max-width:200px;max-height:200px;\">";
            $content .="<br/>";
            $content .="<input type=\"checkbox\" name=\"c_theme_image_cover_del\" valye=\"1\">&nbsp;<b style=\"color:#f00;\">".OW::getLanguage()->text('startpage','delete_image')."</b>";
        }

        $content .="</td>";
        $content .="</tr>";


        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'theme_header_width').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'theme_header_width');
        $content .="<input type=\"text\" name=\"c_theme_header_width\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'theme_header_height').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'theme_header_height');
        $content .="<input type=\"text\" name=\"c_theme_header_height\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'theme_header_backgroundcolor').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'theme_header_backgroundcolor');
        $content .="<input type=\"text\" name=\"c_theme_header_backgroundcolor\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">";
        $content .="</td>";
        $content .="</tr>";







        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('startpage','background_image').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
//        $curent=OW::getConfig()->getValue('startpage', 'theme_center_column');
//        $content .="<textarea name=\"c_theme_center_column\" style=\"height:100px;width:100%;maegin:auto;\"></textarea>";
        $content .="<input type=\"file\" name=\"c_background_image\" style=\"display:inline-block;min-width:100px;\">";

        $plname="startpage";
        $source=OW_DIR_PLUGIN.$plname. DS.'static'. DS;
        $pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.$plname.DS;
        $ct=OW::getConfig()->getValue('startpage', 'curent_theme');
        if (!$ct) $ct="default";
        $pluginStaticU=OW::getPluginManager()->getPlugin('startpage')->getStaticUrl();

        $imgp_u=$pluginStaticDir."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'background_image');
//echo $pluginStaticU;exit;
        if (strlen(OW::getConfig()->getValue('startpage', 'background_image'))>4 AND is_file($imgp_u)){
//            $imgp=$pluginStaticU."themes".DS.$default_theme.DS.OW::getConfig()->getValue('startpage', 'theme_image_cover');
            $content .="<br/>";
            $content .="<img src=\"".$pluginStaticU."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'background_image')."?fakecache=".rand(9999,999999)."\" style=\"max-width:200px;max-height:200px;\">";
            $content .="<br/>";
            $content .="<input type=\"checkbox\" name=\"c_background_image_del\" valye=\"1\">&nbsp;<b style=\"color:#f00;\">".OW::getLanguage()->text('startpage','delete_image')."</b>";
        }

        $content .="</td>";
        $content .="</tr>";
/*
        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'background_image_pos').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'background_image_pos');
        $content .="<input type=\"text\" name=\"c_background_image_pos\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">";
        $content .="</td>";
        $content .="</tr>";
*/
        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'background_color').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'background_color');
        $content .="<input type=\"text\" name=\"c_background_color\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">";
        $content .="</td>";
        $content .="</tr>";










        $content .="<tr class=\"\">";
        $content .="<td class=\"ow_value\" style=\"\" colspan=\"2\">";
        $content .="<hr/>";
        $content .="</td>";
        $content .="</tr>";

//----
        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('startpage','content_background_image').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $content .="<input type=\"file\" name=\"c_content_background_image\" style=\"display:inline-block;min-width:100px;\">";

        $plname="startpage";
        $source=OW_DIR_PLUGIN.$plname. DS.'static'. DS;
        $pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.$plname.DS;
        $ct=OW::getConfig()->getValue('startpage', 'curent_theme');
        if (!$ct) $ct="default";
        $pluginStaticU=OW::getPluginManager()->getPlugin('startpage')->getStaticUrl();

        $imgp_u=$pluginStaticDir."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'content_background_image');
        if (strlen(OW::getConfig()->getValue('startpage', 'content_background_image'))>4 AND is_file($imgp_u)){
            $content .="<br/>";
            $content .="<img src=\"".$pluginStaticU."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'content_background_image')."?fakecache=".rand(9999,999999)."\" style=\"max-width:200px;max-height:200px;\">";
            $content .="<br/>";
            $content .="<input type=\"checkbox\" name=\"c_content_background_image_del\" valye=\"1\">&nbsp;<b style=\"color:#f00;\">".OW::getLanguage()->text('startpage','delete_image')."</b>";
        }

        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'content_background').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'content_background');
        $content .="<input type=\"text\" name=\"c_content_background\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">Default: #FFFFFF";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'content_text_color').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'content_text_color');
        $content .="<input type=\"text\" name=\"c_content_text_color\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">Default: #555555";
        $content .="</td>";
        $content .="</tr>";

//----
        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('startpage','topbar_background_image').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $content .="<input type=\"file\" name=\"c_topbar_background_image\" style=\"display:inline-block;min-width:100px;\">";

        $plname="startpage";
        $source=OW_DIR_PLUGIN.$plname. DS.'static'. DS;
        $pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.$plname.DS;
        $ct=OW::getConfig()->getValue('startpage', 'curent_theme');
        if (!$ct) $ct="default";
        $pluginStaticU=OW::getPluginManager()->getPlugin('startpage')->getStaticUrl();

        $imgp_u=$pluginStaticDir."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'topbar_background_image');
        if (strlen(OW::getConfig()->getValue('startpage', 'topbar_background_image'))>4 AND is_file($imgp_u)){
            $content .="<br/>";
            $content .="<img src=\"".$pluginStaticU."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'topbar_background_image')."?fakecache=".rand(9999,999999)."\" style=\"max-width:200px;max-height:200px;\">";
            $content .="<br/>";
            $content .="<input type=\"checkbox\" name=\"c_topbar_background_image_del\" valye=\"1\">&nbsp;<b style=\"color:#f00;\">".OW::getLanguage()->text('startpage','delete_image')."</b>";
        }

        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'topbar_background').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'topbar_background');
        $content .="<input type=\"text\" name=\"c_topbar_background\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">Default: #FFFFFF";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'topbar_text_color').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'topbar_text_color');
        $content .="<input type=\"text\" name=\"c_topbar_text_color\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">Default: #555555";
        $content .="</td>";
        $content .="</tr>";


        $content .="<tr class=\"\">";
        $content .="<td class=\"ow_labelX\" style=\"\" colspan=\"2\">";
        $content .="<hr/>";
        $content .="</td>";
        $content .="</tr>";



















        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'theme_slogan').":</b>";
        $content .="<br/><i>".OW::getLanguage()->text('startpage', 'info_slogan')."</i>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'theme_slogan');
        $content .="<input type=\"text\" name=\"c_theme_slogan\" value=\"".$curent."\" style=\"display:inline-block;width:90%;\">";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'theme_slogan_desc').":</b>";
        $content .="<br/><i>".OW::getLanguage()->text('startpage', 'info_slogan')."</i>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'theme_slogan_desc');
        $content .="<input type=\"text\" name=\"c_theme_slogan_desc\" value=\"".$curent."\" style=\"display:inline-block;width:90%;\">";
        $content .="</td>";
        $content .="</tr>";





        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('startpage','theme_center_column').":</b>";
        $content .="<br/><i>".OW::getLanguage()->text('startpage', 'info_slogan')."</i>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'theme_center_column');
        $content .="<textarea class=\"html\" name=\"c_theme_center_column\" style=\"height:100px;width:100%;maegin:auto;\">".$curent."</textarea>";
        $content .="</td>";
        $content .="</tr>";




        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'theme_seo_title').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'theme_seo_title');
        $content .="<input type=\"text\" name=\"c_theme_seo_title\" value=\"".$curent."\" style=\"display:inline-block;width:90%;\">";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'theme_seo_keywords').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'theme_seo_keywords');
        $content .="<input type=\"text\" name=\"c_theme_seo_keywords\" value=\"".$curent."\" style=\"display:inline-block;width:90%;\">";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1 hide_once_x\">";
        $content .="<td class=\"ow_label\" style=\"\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'theme_seo_desc').":</b>";
        $content .="</td>";
        $content .="<td class=\"ow_value\">";
        $curent=OW::getConfig()->getValue('startpage', 'theme_seo_desc');
        $content .="<input type=\"text\" name=\"c_theme_seo_desc\" value=\"".$curent."\" style=\"display:inline-block;width:90%;\">";
        $content .="</td>";
        $content .="</tr>";


/*
        $content .="<tr class=\"ow_alt1\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'show_small_startpage_list').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_show_small_startpage_list\">";
        if (OW::getConfig()->getValue('startpage', 'show_small_startpage_list')=="1") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'show_small_startpage_list')=="0" OR OW::getConfig()->getValue('startpage', 'show_small_startpage_list')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";


        $content .="<tr class=\"ow_alt1\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'show_profile_inwidget_startpage').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_show_profile_inwidget_startpage\">";
        if (OW::getConfig()->getValue('startpage', 'show_profile_inwidget_startpage')=="1" OR OW::getConfig()->getValue('startpage', 'show_profile_inwidget_startpage')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'show_profile_inwidget_startpage')=="0" ) $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";
*/

/*
        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('startpage', 'admin_border_cart_color').":</b>";
        $content .="</td>";
        $content .="<td >";
        $curent=OW::getConfig()->getValue('startpage', 'border_cards_color');
        $content .="<input type=\"text\" name=\"c_border_cards_color\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('startpage', 'admin_background_cart_color').":</b>";
        $content .="</td>";
        $content .="<td >";
        $curent=OW::getConfig()->getValue('startpage', 'backgrounc_cards_color');
        $content .="<input type=\"text\" name=\"c_backgrounc_cards_color\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">";
        $content .="</td>";
        $content .="</tr>";
*/

/*
        $content .="<tr class=\"ow_alt1\">";
        $content .="<td class=\"ow_label\" style=\"min-width:450px;\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'admin_start_page').":</b>";
        $content .="</td>";
        $content .="<td nowrap=\"nowrap\" class=\"ow_value\">";

        $startpage=OW::getConfig()->getValue('startpage', 'startpage');
        if (!$startpage) $startpage=0;
        $content .="<select name=\"pf_startpage\" id=\"pf_startpage\">";
        if (!$startpage){
            $content .="<option selected value=\"0\">--- ".OW::getLanguage()->text('startpage', 'select_startpage')." --- </option>";
        }
        $content .=WALL_BOL_Service::getInstance()->make_pageslist($startpage);
        $content .="</select>";

        $content .="</td>";
        $content .="</tr>";
*/
/*
        $content .="<tr class=\"ow_alt1\">";
        $content .="<td class=\"ow_label\" >";
        $content .="<b>".OW::getLanguage()->text('startpage', 'admin_howmanyutems_fortop10').":</b>";
        $content .="</td>";
        $content .="<td nowrap=\"nowrap\"  class=\"ow_value\">";
        $content .="<input type=\"text\" name=\"c_items_top10\" value=\"".OW::getConfig()->getValue('startpage', 'items_top10')."\" style=\"display:inline-block;width:50px;\">";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'admin_membercanaddstartpage').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_admin_membercanaddgame\">";
        if (OW::getConfig()->getValue('startpage', 'admin_membercanaddgame')=="1" OR OW::getConfig()->getValue('startpage', 'admin_membercanaddgame')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'admin_membercanaddgame')=="0") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'admin_doshowgameinfo').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_admin_show_gameinfo\">";
        if (OW::getConfig()->getValue('startpage', 'admin_show_gameinfo')=="1" OR OW::getConfig()->getValue('startpage', 'admin_show_gameinfo')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'admin_show_gameinfo')=="0") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('startpage', 'admin_doshowlateststartpage').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_admin_show_latest\">";
        if (OW::getConfig()->getValue('startpage', 'admin_show_latest')=="1" OR OW::getConfig()->getValue('startpage', 'admin_show_latest')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('startpage', 'yes')."</option>";
        if (OW::getConfig()->getValue('startpage', 'admin_show_latest')=="0") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('startpage', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";
*/


/*
        $content .="<tr>";
        $content .="<td colspan=\"2\" wrap=\"wrap\" style=\"background-color:#eee;\">";
        $content .=OW::getLanguage()->text('rss', 'admin_protectkey_informationofuse');
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('rss', 'admin_userprofiledata').":</b>";
        $content .="</td>";
        $content .="<td >";
        $content .="<select name=\"c_generateforusers\">";
        if (OW::getConfig()->getValue('rss', 'rss_generateforusers') OR !OW::getConfig()->getValue('rss', 'rss_generateforusers')) $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('rss', 'admin_dontgeneratememberstoo')."</option>";
        if (OW::getConfig()->getValue('rss', 'rss_generateforusers')==1) $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('rss', 'admin_generatememberstoo')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('rss', 'admin_maxitemseachplugin').":</b>";
        $content .="</td>";
        $content .="<td >";
        $itels=OW::getConfig()->getValue('rss', 'rss_max_items_eachplugin');
        if (!$itels) $itels=10;
        $content .="<input type=\"text\" name=\"c_maxitemseachplugin\" value=\"".$itels."\" style=\"display:inline-block;width:100px;\">";
        $content .="&nbsp;".OW::getLanguage()->text('rss', 'admin_maxitemseachplugin_info')."";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('rss', 'admin_maxitemsforsitemap').":</b>";
        $content .="</td>";
        $content .="<td >";
        $itelss=OW::getConfig()->getValue('rss', 'rss_max_items_sitemap');
        if (!$itelss) $itelss=1000;
        $content .="<input type=\"text\" name=\"c_maxitemsforsitemap\" value=\"".$itelss."\" style=\"display:inline-block;width:100px;\">";
        $content .="&nbsp;".OW::getLanguage()->text('rss', 'admin_maxitemsforsitemap_info')."";
        $content .="</td>";
        $content .="</tr>";
*/
        $content .="<tr>";
        $content .="<td colspan=\"2\">";
//        $content .="<input type=\"submit\" name=\"dosave\" value=\"".OW::getLanguage()->text('startpage', 'admin_save')."\">";
/*
        $content .="<div class=\"ow_right\">
            <span class=\"ow_button ow_ic_save ow_positive\">
                <span>
                    <input type=\"submit\" value=\"Save\" id=\"input_50903321\" class=\"ow_ic_save ow_positive\" name=\"dosave\"  value=\"".OW::getLanguage()->text('startpage', 'admin_save')."\">
                </span>
            </span>
        </div>";
*/
        $content .="<div class=\"clearfix ow_submit ow_smallmargin\">
                <div class=\"ow_center\">
                    <span class=\"ow_button\">
                        <span class=\"ow_positive\">
                            <input type=\"submit\" value=\"Save\" name=\"".OW::getLanguage()->text('startpage', 'save')."\" class=\"ow_ic_save ow_positive\">
                        </span>
                    </span>
                </div>
            </div>";

        $content .="</td>";
        $content .="</tr>";


        $content .="</table>";
        $content .="</form>";
//$content .="</div>";

        $this->assign('content', $content);
    }

    public function delete( $params )
    {
        $this->redirect(OW::getRouter()->urlForRoute('rss.admin'));
    }
}
