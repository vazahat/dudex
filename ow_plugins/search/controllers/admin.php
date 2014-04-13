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
 * Official website only: http://test.a6.pl
 * Full license available at: http://test.a6.pl


 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
***/


class SEARCH_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    public function dept()
    {
        $content="";
//        $this->setPageTitle(OW::getLanguage()->text('search', 'admin_dept_title'));
        $this->setPageTitle(OW::getLanguage()->text('search', 'admin_dept_heading'));
        $this->setPageHeading(OW::getLanguage()->text('search', 'admin_dept_heading'));    
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();//iss admin
        $curent_url=OW_URL_HOME;
        $config = OW::getConfig();
        $content="";
        if (!isset($_POST['save'])) $_POST['save']="";
        if ($is_admin AND $id_user>0 AND $_POST['save']=="besave"){
            if ($_POST['c_horizontal_position']!=""){
                $horizontal_position=$_POST['c_horizontal_position'];
            }else{
                $horizontal_position=-300;
            }
            $config->saveConfig('search', 'horizontal_position', $horizontal_position);

            if ($_POST['c_vertical_position']!=""){
                $vertical_position=$_POST['c_vertical_position'];
            }else{
                $vertical_position=0;
            }
            $config->saveConfig('search', 'vertical_position', $vertical_position);

            if (!$_POST['c_zindex_position']) $zindex_position=0;
                else $zindex_position=$_POST['c_zindex_position'];
            $config->saveConfig('search', 'zindex_position', $zindex_position);


            if ( OW::getPluginManager()->isPluginActive('adsense') ){
                $config->saveConfig('search', 'allow_ads_adsense', $_POST['c_allow_ads_adsense']);
            }
            if ( OW::getPluginManager()->isPluginActive('adspro') ){
                $config->saveConfig('search', 'allow_ads_adspro', $_POST['c_allow_ads_adspro']);
            }
            if ( OW::getPluginManager()->isPluginActive('ads') ){
                $config->saveConfig('search', 'allow_ads_ads', $_POST['c_allow_ads_ads']);
            }


            $config->saveConfig('search', 'width_topsearchbar', $_POST['c_width_topsearchbar']);
            $config->saveConfig('search', 'height_topsearchbar', $_POST['c_height_topsearchbar']);

            $config->saveConfig('search', 'turn_off_topsearchbar', $_POST['c_turn_off_topsearchbar']);

            $config->saveConfig('search', 'hmanyitems_show_topsearchbarlist', $_POST['c_hmanyitems_show_topsearchbarlist']);

            $config->saveConfig('search', 'maxallitems_topsearchbarlist', $_POST['c_maxallitems_topsearchbarlist']);
            $config->saveConfig('search', 'search_force_users', $_POST['c_search_force_users']);

$config->saveConfig('search', 'search_position', $_POST['c_search_position']);

            if (isset($_POST['c_turn_offplugin_cms'])){
                $config->saveConfig('search', 'turn_offplugin_cms', $_POST['c_turn_offplugin_cms']);
            }
            if (isset($_POST['c_turn_offplugin_forum'])){
                $config->saveConfig('search', 'turn_offplugin_forum', $_POST['c_turn_offplugin_forum']);
            }
            if (isset($_POST['c_turn_offplugin_links'])){
                $config->saveConfig('search', 'turn_offplugin_links', $_POST['c_turn_offplugin_links']);
            }
            if (isset($_POST['c_turn_offplugin_video'])){
                $config->saveConfig('search', 'turn_offplugin_video', $_POST['c_turn_offplugin_video']);
            }
            if (isset($_POST['c_turn_offplugin_photo'])){
                $config->saveConfig('search', 'turn_offplugin_photo', $_POST['c_turn_offplugin_photo']);
            }
            if (isset($_POST['c_turn_offplugin_shoppro'])){
                $config->saveConfig('search', 'turn_offplugin_shoppro', $_POST['c_turn_offplugin_shoppro']);
            }
            if (isset($_POST['c_turn_offplugin_classifiedspro'])){
                $config->saveConfig('search', 'turn_offplugin_classifiedspro', $_POST['c_turn_offplugin_classifiedspro']);
            }
            if (isset($_POST['c_turn_offplugin_pages'])){
                $config->saveConfig('search', 'turn_offplugin_pages', $_POST['c_turn_offplugin_pages']);
            }
            if (isset($_POST['c_turn_offplugin_groups'])){
                $config->saveConfig('search', 'turn_offplugin_groups', $_POST['c_turn_offplugin_groups']);
            }
            if (isset($_POST['c_turn_offplugin_blogs'])){
                $config->saveConfig('search', 'turn_offplugin_blogs', $_POST['c_turn_offplugin_blogs']);
            }
            if (isset($_POST['c_turn_offplugin_event'])){
                $config->saveConfig('search', 'turn_offplugin_event', $_POST['c_turn_offplugin_event']);
            }
            if (isset($_POST['c_turn_offplugin_fanpage'])){
                $config->saveConfig('search', 'turn_offplugin_fanpage', $_POST['c_turn_offplugin_fanpage']);
            }
            if (isset($_POST['c_turn_offplugin_html'])){
                $config->saveConfig('search', 'turn_offplugin_html', $_POST['c_turn_offplugin_html']);
            }
            if (isset($_POST['c_turn_offplugin_games'])){
                $config->saveConfig('search', 'turn_offplugin_games', $_POST['c_turn_offplugin_games']);
            }
            if (isset($_POST['c_turn_offplugin_adsense'])){
                $config->saveConfig('search', 'turn_offplugin_adsense', $_POST['c_turn_offplugin_adsense']);
            }
            if (isset($_POST['c_turn_offplugin_mochigames'])){
                $config->saveConfig('search', 'turn_offplugin_mochigames', $_POST['c_turn_offplugin_mochigames']);
            }
            if (isset($_POST['c_turn_offplugin_basepages'])){
                $config->saveConfig('search', 'turn_offplugin_basepages', $_POST['c_turn_offplugin_basepages']);
            }
            if (isset($_POST['c_turn_offplugin_adspro'])){
                $config->saveConfig('search', 'turn_offplugin_adspro', $_POST['c_turn_offplugin_adspro']);
            }
            if (isset($_POST['c_turn_offplugin_map'])){
                $config->saveConfig('search', 'turn_offplugin_map', $_POST['c_turn_offplugin_map']);
            }
            if (isset($_POST['c_turn_offplugin_wiki'])){
                $config->saveConfig('search', 'turn_offplugin_wiki', $_POST['c_turn_offplugin_wiki']);
            }

            if (isset($_POST['c_turn_offplugin_news'])){
                $config->saveConfig('search', 'turn_offplugin_news', $_POST['c_turn_offplugin_news']);
            }

            $config->saveConfig('search', 'bg_results_topsearchbar', $_POST['c_bg_results_topsearchbar']);


            OW::getApplication()->redirect($curent_url."admin/plugins/search");
        }

        $content .="<form action=\"".$curent_url."admin/plugins/search\" method=\"post\">";
        $content .="<input type=\"hidden\" name=\"save\" value=\"besave\">";
        $content .="<table style=\"width:auto;\">";


        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff').":</b>";
        $content .="</td>";
        $content .="<td>";

        $mode=$config->getValue('search', 'turn_off_topsearchbar');
        $content .="<select name=\"c_turn_off_topsearchbar\" >";
        if ($mode=="0" OR !$mode) $sel=" selected ";
            else $sel="";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
        if ($mode=="1")  $sel=" selected ";
            else $sel="";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";





        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('search', 'config_hmanyitems_showperplugin').":</b>";
        $content .="</td>";
        $content .="<td>";

        $mode=$config->getValue('search', 'hmanyitems_show_topsearchbarlist');
        $content .="<input type=\"text\" name=\"c_hmanyitems_show_topsearchbarlist\" value=\"".$mode."\" style=\"display:inline-block;width:45px;text-align:center;font-weight:bold;\">&nbsp;<b>def=6px, disable=0</b>";
        
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('search', 'config_maxalitemson_dropdownsearchbar').":</b>";
        $content .="</td>";
        $content .="<td>";

        $mode=$config->getValue('search', 'maxallitems_topsearchbarlist');
        $content .="<input type=\"text\" name=\"c_maxallitems_topsearchbarlist\" value=\"".$mode."\" style=\"display:inline-block;width:45px;text-align:center;font-weight:bold;\">&nbsp;<b>def=12</b>";
        
        $content .="</td>";
        $content .="</tr>";


        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('search', 'config_search_exactlybyusers').":</b>";
        $content .="</td>";
        $content .="<td>";

        $mode=$config->getValue('search', 'search_force_users');
        $content .="<select name=\"c_search_force_users\" >";
        if ($mode=="0" OR !$mode) $sel=" selected ";
            else $sel="";
        $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
        if ($mode=="1")  $sel=" selected ";
            else $sel="";
        $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
        if ($mode=="2")  $sel=" selected ";
            else $sel="";
        $content .="<option ".$sel." value=\"2\">".OW::getLanguage()->text('search', 'config_yes')." (".OW::getLanguage()->text('search', 'config_yes_precisely').")</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";


        $content .="<tr>";
        $content .="<td colspan=\"2\">";
        $content .="<hr/>";
        $content .="</td>";
        $content .="</tr>";

        if ( OW::getPluginManager()->isPluginActive('adsense') ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnon_ads_adsense').":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'allow_ads_adsense');
            $content .="<select name=\"c_allow_ads_adsense\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }


        if ( OW::getPluginManager()->isPluginActive('adspro') ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnon_ads_adspro').":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'allow_ads_adspro');
            $content .="<select name=\"c_allow_ads_adspro\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }

        if ( OW::getPluginManager()->isPluginActive('ads') ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnon_ads_ads').":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'allow_ads_ads');
            $content .="<select name=\"c_allow_ads_ads\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";
        }


        if (OW::getPluginManager()->isPluginActive('adsense') OR OW::getPluginManager()->isPluginActive('adspro') OR OW::getPluginManager()->isPluginActive('ads') ){
            $content .="<tr>";
            $content .="<td colspan=\"2\">";
            $content .="<hr/>";
            $content .="</td>";
            $content .="</tr>";
        }


        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('search', 'config_search_search_position').":</b>";
        $content .="</td>";
        $content .="<td>";

        $mode=$config->getValue('search', 'search_position');
        $content .="<select name=\"c_search_position\" >";
        if ($mode=="oxwall15" OR !$mode) $sel=" selected ";
            else $sel="";
        $content .="<option ".$sel." value=\"oxwall15\">".OW::getLanguage()->text('search', 'config_oxwall15')."</option>";
        if ($mode=="absolute") $sel=" selected ";
            else $sel="";
        $content .="<option ".$sel." value=\"absolute\">".OW::getLanguage()->text('search', 'config_absolute')."</option>";
        if ($mode=="realtive")  $sel=" selected ";
            else $sel="";
        $content .="<option ".$sel." value=\"realtive\">".OW::getLanguage()->text('search', 'config_relative')."</option>";
        if ($mode=="fixed")  $sel=" selected ";
            else $sel="";
        $content .="<option ".$sel." value=\"fixed\">".OW::getLanguage()->text('search', 'config_fixed')."</option>";
        $content .="</select>";
        $content .="</td>";
        $content .="</tr>";


        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('search', 'admin_top_searchbar_resultsbackground').":</b>";
        $content .="</td>";
        $content .="<td nowrap=\"nowrap\">";
        $bg_results_topsearchbar=OW::getConfig()->getValue('search', 'bg_results_topsearchbar');
        if (!$bg_results_topsearchbar) $bg_results_topsearchbar="";
        $content .="<input type=\"text\" name=\"c_bg_results_topsearchbar\" value=\"".$bg_results_topsearchbar."\" style=\"display:inline-block;width:45px;color:#f00;text-align:center;font-weight:bold;\">&nbsp;<b>default: empty</b>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('search', 'admin_width_topsearchbar').":</b>";
        $content .="</td>";
        $content .="<td nowrap=\"nowrap\">";
        $width_topsearchbar=OW::getConfig()->getValue('search', 'width_topsearchbar');
        if ($width_topsearchbar=="" oR $width_topsearchbar==0) $width_topsearchbar="250";
        $content .="<input type=\"text\" name=\"c_width_topsearchbar\" value=\"".$width_topsearchbar."\" style=\"display:inline-block;width:45px;color:#f00;text-align:center;font-weight:bold;\">&nbsp;<b>def=250px</b>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('search', 'admin_height_topsearchbar').":</b>";
        $content .="</td>";
        $content .="<td nowrap=\"nowrap\">";
        $height_topsearchbar=OW::getConfig()->getValue('search', 'height_topsearchbar');
        if ($height_topsearchbar=="" OR $height_topsearchbar==0) $height_topsearchbar="24";
        $content .="<input type=\"text\" name=\"c_height_topsearchbar\" value=\"".$height_topsearchbar."\" style=\"display:inline-block;width:45px;color:#f00;text-align:center;font-weight:bold;\">&nbsp;<b>def=24px</b>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('search', 'admin_horizontal_position').":</b>";
        $content .="</td>";
        $content .="<td nowrap=\"nowrap\">";
        $horizontal_position=OW::getConfig()->getValue('search', 'horizontal_position');
        if (!$horizontal_position) $horizontal_position="0";
        $content .="<b>[-]</b>&nbsp;<input type=\"text\" name=\"c_horizontal_position\" value=\"".$horizontal_position."\" style=\"display:inline-block;width:45px;color:#f00;text-align:center;font-weight:bold;\">&nbsp;<b>[+]</b>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr>";
        $content .="<td colspan=\"2\" wrap=\"wrap\" style=\"background-color:#eee;\">";
        $content .=OW::getLanguage()->text('search', 'admin_horizontal_position_info');
        $content .="</td>";
        $content .="</tr>";


        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('search', 'admin_vertical_position').":</b>";
        $content .="</td>";
        $content .="<td nowrap=\"nowrap\">";
        $vertical_position=OW::getConfig()->getValue('search', 'vertical_position');
        if (!$vertical_position) $vertical_position="0";
        $content .="<b>[-]</b>&nbsp;<input type=\"text\" name=\"c_vertical_position\" value=\"".$vertical_position."\" style=\"display:inline-block;width:45px;color:#f00;text-align:center;font-weight:bold;\">&nbsp;<b>[+]</b>";
        $content .="</td>";
        $content .="</tr>";

        $content .="<tr>";
        $content .="<td >";
        $content .="<b>".OW::getLanguage()->text('search', 'admin_zindex_position').":</b>";
        $content .="</td>";
        $content .="<td nowrap=\"nowrap\">";
        $zindex_position=OW::getConfig()->getValue('search', 'zindex_position');
        if ($zindex_position=="") $zindex_position="101";
        $content .="<input type=\"text\" name=\"c_zindex_position\" value=\"".$zindex_position."\" style=\"display:inline-block;width:45px;color:#f00;text-align:center;font-weight:bold;\">&nbsp;<b>def=101</b>";
        $content .="</td>";
        $content .="</tr>";


        $content .="<tr>";
        $content .="<td colspan=\"2\">";
        $content .="<hr/>";
        $content .="</td>";
        $content .="</tr>";



        $plnam="cms";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="forum";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="map";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="links";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="video";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="photo";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="shoppro";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="classifiedspro";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="pages";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="groups";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="blogs";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="event";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="fanpage";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="html";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
        $plnam="games";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
/*
//-------------------disable Adsense
        $plnam="adsense";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
*/
//-------------------disable Adsense
/*
        if ( OW::getPluginManager()->isPluginActive('adsense') ){
            $plunin_installed['adsense']=true;
        }else{
            $plunin_installed['adsense']=false;
        }
*/
        $plunin_installed['adsense']=false;
        $plnam="mochigames";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }

        $plnam="wiki";
//        if ( OW::getPluginManager()->isPluginActive($plnam) ){
        if ( OW::getPluginManager()->isPluginActive('userwiki') ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }

/*
        $plnam="basepages";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
*/
/*
        if ( OW::getPluginManager()->isPluginActive('basepages') ){//TODO...
            $plunin_installed['basepages']=true;
        }else{
            $plunin_installed['basepages']=false;
        }
*/
/*
        $plnam="adspro";
        if ( OW::getPluginManager()->isPluginActive($plnam) ){
            $content .="<tr>";
            $content .="<td >";
            $content .="<b>".OW::getLanguage()->text('search', 'config_turnoff_'.$plnam).":</b>";
            $content .="</td>";
            $content .="<td>";

            $mode=$config->getValue('search', 'turn_offplugin_'.$plnam);
            $content .="<select name=\"c_turn_offplugin_".$plnam."\" >";
            if ($mode=="0" OR !$mode) $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'config_no')."</option>";
            if ($mode=="1")  $sel=" selected ";
                else $sel="";
            $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'config_yes')."</option>";
            $content .="</select>";
            $content .="</td>";
            $content .="</tr>";            
        }
*/




        $content .="<tr>";
        $content .="<td colspan=\"2\">";
        $content .="<hr/>";
        $content .="</td>";
        $content .="</tr>";


        $content .="<tr>";
        $content .="<td colspan=\"2\">";
//        $content .="<input type=\"submit\" name=\"dosave\" value=\"".OW::getLanguage()->text('search', 'admin_save')."\">";

        $content .="<div class=\"clearfix ow_submit ow_smallmargin\">
                <div class=\"ow_center\">
                    <span class=\"ow_button\">
                        <span class=\" ow_ic_save ow_positive\">
                            <input type=\"submit\" name=\"dosave\" value=\"".OW::getLanguage()->text('search', 'admin_save')."\" class=\"ow_ic_save ow_positive\">
                        </span>
                    </span>
                </div>
            </div>";

        $content .="</td>";
        $content .="</tr>";
        $content .="</table>";
        $content .="</form>";
        $this->assign('content', $content);
    }

    public function delete( $params )
    {
        $this->redirect(OW::getRouter()->urlForRoute('search.admin'));
    }
}
