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


class MAP_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    public function dept()
    {
        $content="";
        $this->setPageTitle(OW::getLanguage()->text('map', 'admin_dept_title'));
        $this->setPageHeading(OW::getLanguage()->text('map', 'admin_dept_heading'));    
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();//iss admin
        $curent_url=OW_URL_HOME;
        $config = OW::getConfig();
        $content="";

        if (!isset($_POST['save'])) $_POST['save']="";
        if ($is_admin AND $id_user>0 AND $_POST['save']=="besave"){

            if (isset($_POST['c_tabdisable_shop'])){
                $config->saveConfig('map', 'tabdisable_shop', $_POST['c_tabdisable_shop']);
            }
            if (isset($_POST['c_tabdisable_fanpage'])){
                $config->saveConfig('map', 'tabdisable_fanpage', $_POST['c_tabdisable_fanpage']);
            }
            if (isset($_POST['c_tabdisable_event'])){
                $config->saveConfig('map', 'tabdisable_event', $_POST['c_tabdisable_event']);
            }
            if (isset($_POST['c_tabdisable_news'])){
                $config->saveConfig('map', 'tabdisable_news', $_POST['c_tabdisable_news']);
            }


//            $pluginStaticDir =OW::getPluginManager()->getPlugin('map')->getUserFilesDir();
//            $pluginStaticDir =OW::getPluginManager()->getPlugin('map')->getPluginFilesDir();
            $pluginStaticDir =OW::getPluginManager()->getPlugin('map')->getRootDir();
            if (is_file($pluginStaticDir."map_mobile.apk")){
                $config->saveConfig('map', 'support_mobile_app', $_POST['c_support_mobile_app']);
            }else{
                $config->saveConfig('map', 'support_mobile_app', '0');
            }
/*

            $config->saveConfig('map', 'backgrounc_cards_color', $_POST['c_backgrounc_cards_color']);
            $config->saveConfig('map', 'border_cards_color', $_POST['c_border_cards_color']);

            if (isset($_POST['c_tabdisable_shop'])){
                $config->saveConfig('map', 'tabdisable_shop', $_POST['c_tabdisable_shop']);
            }
            if (isset($_POST['c_tabdisable_photo'])){
                $config->saveConfig('map', 'tabdisable_photo', $_POST['c_tabdisable_photo']);
            }
            if (isset($_POST['c_tabdisable_blogs'])){
                $config->saveConfig('map', 'tabdisable_blogs', $_POST['c_tabdisable_blogs']);
            }
            if (isset($_POST['c_tabdisable_forum'])){
                $config->saveConfig('map', 'tabdisable_forum', $_POST['c_tabdisable_forum']);
            }
            if (isset($_POST['c_tabdisable_event'])){
                $config->saveConfig('map', 'tabdisable_event', $_POST['c_tabdisable_event']);
            }
            if (isset($_POST['c_tabdisable_groups'])){
                $config->saveConfig('map', 'tabdisable_groups', $_POST['c_tabdisable_groups']);
            }
            if (isset($_POST['c_tabdisable_video'])){
                $config->saveConfig('map', 'tabdisable_video', $_POST['c_tabdisable_video']);
            }
            if (isset($_POST['c_tabdisable_fanpage'])){
                $config->saveConfig('map', 'tabdisable_fanpage', $_POST['c_tabdisable_fanpage']);
            }


            $config->saveConfig('map', 'showpost_membersonly', $_POST['c_showpost_membersonly']);
*/
            $config->saveConfig('map', 'perpage', $_POST['c_perpage']);
            $config->saveConfig('map', 'show_owner', $_POST['c_show_owner']);

//echo OW::getConfig()->getValue('map', 'perpage');exit;
            
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
            if (!$_POST['c_map_perpage']) $_POST['c_map_perpage']=12;
            $config->saveConfig('map', 'map_perpage', $_POST['c_map_perpage']);

            if (!$_POST['c_items_top10']) $_POST['c_items_top10']=0;
            $config->saveConfig('map', 'items_top10', $_POST['c_items_top10']);
            if ($_POST['c_admin_membercanaddgame']=="") $_POST['c_admin_membercanaddgame']=0;
            $config->saveConfig('map', 'admin_membercanaddgame', $_POST['c_admin_membercanaddgame']);

            if ($_POST['c_admin_show_gameinfo']=="") $_POST['c_admin_show_gameinfo']=0;
            $config->saveConfig('map', 'admin_show_gameinfo', $_POST['c_admin_show_gameinfo']);

            if ($_POST['c_admin_show_latest']=="") $_POST['c_admin_show_latest']=0;
            $config->saveConfig('map', 'admin_show_latest', $_POST['c_admin_show_latest']);
*/
            OW::getApplication()->redirect($curent_url."admin/plugins/map");
        }

//$content .="<div class=\"ow_content ow_table_1 ow_form ow_stdmargin\">";
        $content .="<form action=\"".$curent_url."admin/plugins/map\" method=\"post\"  style=\"width:100%;\">";
        $content .="<input type=\"hidden\" name=\"save\" value=\"besave\">";
        $content .="<table style=\"width:100%;\" class=\"ow_table_1 ow_form ow_stdmargin\">";

        $content .="<th class=\"ow_name ow_txtleft\" colspan=\"3\">
            <span class=\"ow_section_icon ow_ic_gear_wheel\">".OW::getLanguage()->text('map', 'admin_dept_title')."</span>
        </th>";


        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('map', 'perpage').":</b>";
        $content .="</td>";
        $content .="<td >";
        $itelss=OW::getConfig()->getValue('map', 'perpage');
        if (!$itelss) $itelss="300";
        $content .="<input type=\"text\" name=\"c_perpage\" value=\"".$itelss."\" style=\"display:inline-block;width:100px;\">";
        $content .="</td>";
        $content .="</tr>";

            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'support_mobile_app').":</b>";
$content .="<br/><i>".OW::getLanguage()->text('map', 'mobile_info_admin')."</i>";
//$pluginStaticDir =OW::getPluginManager()->getPlugin('map')->getUserFilesDir();
$pluginStaticDir =OW::getPluginManager()->getPlugin('map')->getRootDir();
$isfileok=false;
if (is_file($pluginStaticDir."map_mobile.apk")){
    $content .="<br/><span style=\"color:#00ff00;\"><b>".OW::getLanguage()->text('map', 'status_application').":</b> ".OW::getLanguage()->text('map', 'you_have_appication')."</span>";
    $isfileok=true;
}else{
    $content .="<br/><span style=\"color:#ff0000;\"><b>".OW::getLanguage()->text('map', 'status_application').":</b> ".OW::getLanguage()->text('map', 'you_dont_have_application')."</span>";
    $isfileok=false;
}
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            if (!$isfileok) {
                $content .="<select name=\"c_support_mobile_app\" disabled>";
            }else{
                $content .="<select name=\"c_support_mobile_app\">";
            }
            if (OW::getConfig()->getValue('map', 'support_mobile_app')=="0" OR OW::getConfig()->getValue('map', 'support_mobile_app')=="" OR !$isfileok) $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'support_mobile_app')=="1" AND $isfileok) $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";


            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'admin_shwo_owneronthemap').":</b>";
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            $content .="<select name=\"c_show_owner\">";
            if (OW::getConfig()->getValue('map', 'show_owner')=="0" OR OW::getConfig()->getValue('map', 'show_owner')=="") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'show_owner')=="1") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";

        if (OW::getPluginManager()->isPluginActive('shoppro')){
            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'admin_disable_shoptab').":</b>";
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            $content .="<select name=\"c_tabdisable_shop\">";
            if (OW::getConfig()->getValue('map', 'tabdisable_shop')=="0" OR OW::getConfig()->getValue('map', 'tabdisable_shop')=="") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'tabdisable_shop')=="1") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }

        if (OW::getPluginManager()->isPluginActive('fanpage')){
            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'admin_disable_fanpagetab').":</b>";
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            $content .="<select name=\"c_tabdisable_fanpage\">";
            if (OW::getConfig()->getValue('map', 'tabdisable_fanpage')=="0" OR OW::getConfig()->getValue('map', 'tabdisable_fanpage')=="") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'tabdisable_fanpage')=="1") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }

        if (OW::getPluginManager()->isPluginActive('news')){
            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'admin_disable_newstab').":</b>";
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            $content .="<select name=\"c_tabdisable_news\">";
            if (OW::getConfig()->getValue('map', 'tabdisable_news')=="0" OR OW::getConfig()->getValue('map', 'tabdisable_news')=="") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'tabdisable_news')=="1") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }

/*
        if (OW::getPluginManager()->isPluginActive('event')){
            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'admin_disable_eventtab').":</b>";
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            $content .="<select name=\"c_tabdisable_event\">";
            if (OW::getConfig()->getValue('map', 'tabdisable_event')=="0" OR OW::getConfig()->getValue('map', 'tabdisable_event')=="") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'tabdisable_event')=="1") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }
*/

/*
        if (OW::getPluginManager()->isPluginActive('photo')){
            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'admin_disable_phototab').":</b>";
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            $content .="<select name=\"c_tabdisable_photo\">";
            if (OW::getConfig()->getValue('map', 'tabdisable_photo')=="0" OR OW::getConfig()->getValue('map', 'tabdisable_photo')=="") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'tabdisable_photo')=="1") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }

        if (OW::getPluginManager()->isPluginActive('video')){
            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'admin_disable_videotab').":</b>";
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            $content .="<select name=\"c_tabdisable_video\">";
            if (OW::getConfig()->getValue('map', 'tabdisable_video')=="0" OR OW::getConfig()->getValue('map', 'tabdisable_video')=="") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'tabdisable_video')=="1") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }

        if (OW::getPluginManager()->isPluginActive('blogs')){
            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'admin_disable_blogstab').":</b>";
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            $content .="<select name=\"c_tabdisable_blogs\">";
            if (OW::getConfig()->getValue('map', 'tabdisable_blogs')=="0" OR OW::getConfig()->getValue('map', 'tabdisable_blogs')=="") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'tabdisable_blogs')=="1") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }

        if (OW::getPluginManager()->isPluginActive('forum')){
            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'admin_disable_forumtab').":</b>";
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            $content .="<select name=\"c_tabdisable_forum\">";
            if (OW::getConfig()->getValue('map', 'tabdisable_forum')=="0" OR OW::getConfig()->getValue('map', 'tabdisable_forum')=="") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'tabdisable_forum')=="1") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }

        if (OW::getPluginManager()->isPluginActive('event')){
            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'admin_disable_eventtab').":</b>";
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            $content .="<select name=\"c_tabdisable_event\">";
            if (OW::getConfig()->getValue('map', 'tabdisable_event')=="0" OR OW::getConfig()->getValue('map', 'tabdisable_event')=="") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'tabdisable_event')=="1") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }

        if (OW::getPluginManager()->isPluginActive('groups')){
            $content .="<tr class=\"ow_alt1x\">";
            $content .="<td  class=\"ow_labelx\">";
            $content .="<b>".OW::getLanguage()->text('map', 'admin_disable_groupstab').":</b>";
            $content .="</td>";
            $content .="<td  class=\"ow_valuex\">";
            $content .="<select name=\"c_tabdisable_groups\">";
            if (OW::getConfig()->getValue('map', 'tabdisable_groups')=="0" OR OW::getConfig()->getValue('map', 'tabdisable_groups')=="") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
            if (OW::getConfig()->getValue('map', 'tabdisable_groups')=="1") $sel=" selected ";
                else  $sel=" ";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }

        $content .="<tr>";
        $content .="<td valign=\"top\">";
        $content .="<b>".OW::getLanguage()->text('map', 'admin_border_cart_color').":</b>";
        $content .="<br/>";
            $content .="Set the color of the line only when then in Your Theme is necessary! (For example, when the boxes are unreadable). <b>By default, leave the field empty.</b> Entered Color as HTML format eg.: #FF0000, #00FF00, #0000FF";

        $content .="</td>";
        $content .="<td d valign=\"top\">";
        $curent=OW::getConfig()->getValue('map', 'border_cards_color');
        $content .="<input type=\"text\" name=\"c_border_cards_color\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr>";
        $content .="<td d valign=\"top\">";
        $content .="<b>".OW::getLanguage()->text('map', 'admin_background_cart_color').":</b>";
        $content .="<br/>";
            $content .="Set the background box color only when the Your Theme necessary and Your theme set boxes as \"transparent\"! (For example, when the boxes are unreadable). <b>By default, leave the field empty.</b> Entered Color as HTML format eg.: #FF0000, #00FF00, #0000FF";

        $content .="</td>";
        $content .="<td d valign=\"top\">";
        $curent=OW::getConfig()->getValue('map', 'backgrounc_cards_color');
        $content .="<input type=\"text\" name=\"c_backgrounc_cards_color\" value=\"".$curent."\" style=\"display:inline-block;width:100px;\">";
        $content .="</td>";
        $content .="</tr>";
*/

/*
        $content .="<tr class=\"ow_alt1\">";
        $content .="<td class=\"ow_label\" style=\"min-width:450px;\">";
        $content .="<b>".OW::getLanguage()->text('map', 'admin_start_page').":</b>";
        $content .="</td>";
        $content .="<td nowrap=\"nowrap\" class=\"ow_value\">";

        $startpage=OW::getConfig()->getValue('map', 'startpage');
        if (!$startpage) $startpage=0;
        $content .="<select name=\"pf_startpage\" id=\"pf_startpage\">";
        if (!$startpage){
            $content .="<option selected value=\"0\">--- ".OW::getLanguage()->text('map', 'select_startpage')." --- </option>";
        }
        $content .=map_BOL_Service::getInstance()->make_pageslist($startpage);
        $content .="</select>";

        $content .="</td>";
        $content .="</tr>";
*/
/*
        $content .="<tr class=\"ow_alt1\">";
        $content .="<td class=\"ow_label\" >";
        $content .="<b>".OW::getLanguage()->text('map', 'admin_howmanyutems_fortop10').":</b>";
        $content .="</td>";
        $content .="<td nowrap=\"nowrap\"  class=\"ow_value\">";
        $content .="<input type=\"text\" name=\"c_items_top10\" value=\"".OW::getConfig()->getValue('map', 'items_top10')."\" style=\"display:inline-block;width:50px;\">";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('map', 'admin_membercanaddmap').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_admin_membercanaddgame\">";
        if (OW::getConfig()->getValue('map', 'admin_membercanaddgame')=="1" OR OW::getConfig()->getValue('map', 'admin_membercanaddgame')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
        if (OW::getConfig()->getValue('map', 'admin_membercanaddgame')=="0") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('map', 'admin_doshowgameinfo').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_admin_show_gameinfo\">";
        if (OW::getConfig()->getValue('map', 'admin_show_gameinfo')=="1" OR OW::getConfig()->getValue('map', 'admin_show_gameinfo')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
        if (OW::getConfig()->getValue('map', 'admin_show_gameinfo')=="0") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr class=\"ow_alt1\">";
        $content .="<td  class=\"ow_label\">";
        $content .="<b>".OW::getLanguage()->text('map', 'admin_doshowlatestmap').":</b>";
        $content .="</td>";
        $content .="<td  class=\"ow_value\">";
        $content .="<select name=\"c_admin_show_latest\">";
        if (OW::getConfig()->getValue('map', 'admin_show_latest')=="1" OR OW::getConfig()->getValue('map', 'admin_show_latest')=="") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
        if (OW::getConfig()->getValue('map', 'admin_show_latest')=="0") $sel=" selected ";
            else  $sel=" ";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
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
/*

        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('map', 'admin_show_newsfeedads_frommemberonly').":</b>";
$content .="<br/>";
$content .="<b>".OW::getLanguage()->text('map', 'your_user_id').":</b> ".$id_user;
        $content .="</td>";
        $content .="<td >";
        $itelss=OW::getConfig()->getValue('map', 'showpost_membersonly');
        if (!$itelss) $itelss="";
        $content .="<input type=\"text\" name=\"c_showpost_membersonly\" value=\"".$itelss."\" style=\"display:inline-block;width:100px;\">";
        $content .="&nbsp;".OW::getLanguage()->text('map', 'admin_show_newsfeedads_frommemberonly_info')."";
        $content .="</td>";
        $content .="</tr>";
*/


        $content .="<tr>";
        $content .="<td colspan=\"2\">";
//        $content .="<input type=\"submit\" name=\"dosave\" value=\"".OW::getLanguage()->text('map', 'admin_save')."\">";


        $content .="<div class=\"clearfix ow_submit ow_smallmargin\">
                <div class=\"ow_center\">
                    <span class=\"ow_button\">
                        <span class=\"ow_positive\">
                            <input type=\"submit\" name=\"saveb\" value=\"".OW::getLanguage()->text('map', 'save')."\" class=\"ow_ic_save ow_positive\">
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
