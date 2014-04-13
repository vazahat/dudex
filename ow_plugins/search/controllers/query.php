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


class SEARCH_CTRL_Query extends OW_ActionController 
{ 

    public function ajax_search() 
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
                    $curent_url = 'http';
                    if (isset($_SERVER["HTTPS"])) {$curent_url .= "s";}
                    $curent_url .= "://";
                    $curent_url .= $_SERVER["SERVER_NAME"]."/";
$curent_url=OW_URL_HOME;
/*
$from_config=$curent_url;
$from_config=str_replace("https://","",$from_config);
$from_config=str_replace("http://","",$from_config);
$trash=explode($from_config,$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
$url_detect=$trash[1];
//print_r($trash);
//echo $url_detect;
*/
//        $maxnumresults=8;

//OW::getConfig()->getValue('search', 'hmanyitems_show_topsearchbarlist')
        $limit_results=OW::getConfig()->getValue('search', 'hmanyitems_show_topsearchbarlist');
        if (!$limit_results) $limit_results=0;
//        $limit_results=2;

        $maxnumresults=$limit_results;

        $maxallresults=OW::getConfig()->getValue('search', 'maxallitems_topsearchbarlist');
        if (!$maxallresults) $maxallresults=0;
        $curent_result=0;
//echo "ddd";exit;
        if (!$id_user){
            OW::getApplication()->redirect($curent_url);
            exit;
        }

        if ( OW::getPluginManager()->isPluginActive('cms') AND OW::getConfig()->getValue('search', 'turn_offplugin_cms')!="1"){
            $plunin_installed['cms']=true;
        }else{
            $plunin_installed['cms']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('forum') AND OW::getConfig()->getValue('search', 'turn_offplugin_forum')!="1"){
            $plunin_installed['forum']=true;
        }else{
            $plunin_installed['forum']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('map') AND OW::getConfig()->getValue('search', 'turn_offplugin_map')!="1"){
            $plunin_installed['map']=true;
        }else{
            $plunin_installed['map']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('news') AND OW::getConfig()->getValue('search', 'turn_offplugin_news')!="1"){
            $plunin_installed['news']=true;
        }else{
            $plunin_installed['news']=false;
        }

        if ( OW::getPluginManager()->isPluginActive('links') AND OW::getConfig()->getValue('search', 'turn_offplugin_links')!="1"){
            $plunin_installed['links']=true;
        }else{
            $plunin_installed['links']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('video') AND OW::getConfig()->getValue('search', 'turn_offplugin_video')!="1"){
            $plunin_installed['video']=true;
        }else{
            $plunin_installed['video']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('photo') AND OW::getConfig()->getValue('search', 'turn_offplugin_photo')!="1"){
            $plunin_installed['photo']=true;
        }else{
            $plunin_installed['photo']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('shoppro') AND OW::getConfig()->getValue('search', 'turn_offplugin_shoppro')!="1"){
            $plunin_installed['shoppro']=true;
        }else{
            $plunin_installed['shoppro']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('classifiedspro') AND OW::getConfig()->getValue('search', 'turn_offplugin_classifiedspro')!="1"){
            $plunin_installed['classifiedspro']=true;
        }else{
            $plunin_installed['classifiedspro']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('pages') AND OW::getConfig()->getValue('search', 'turn_offplugin_pages')!="1"){
            $plunin_installed['pages']=true;
        }else{
            $plunin_installed['pages']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('groups') AND OW::getConfig()->getValue('search', 'turn_offplugin_groups')!="1"){
            $plunin_installed['groups']=true;
        }else{
            $plunin_installed['groups']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('blogs') AND OW::getConfig()->getValue('search', 'turn_offplugin_blogs')!="1"){
            $plunin_installed['blogs']=true;
        }else{
            $plunin_installed['blogs']=false;
        }
//echo OW::getPluginManager()->isPluginActive('event');exit;
        if ( OW::getPluginManager()->isPluginActive('event') AND OW::getConfig()->getValue('search', 'turn_offplugin_event')!="1"){
            $plunin_installed['event']=true;
        }else{
            $plunin_installed['event']=false;
        }

        if ( OW::getPluginManager()->isPluginActive('fanpage') AND OW::getConfig()->getValue('search', 'turn_offplugin_fanpage')!="1"){
            $plunin_installed['fanpage']=true;
        }else{
            $plunin_installed['fanpage']=false;
        }

        if ( OW::getPluginManager()->isPluginActive('html') AND OW::getConfig()->getValue('search', 'turn_offplugin_html')!="1"){
            $plunin_installed['html']=true;
        }else{
            $plunin_installed['html']=false;
        }
//games
        if ( OW::getPluginManager()->isPluginActive('games') AND OW::getConfig()->getValue('search', 'turn_offplugin_games')!="1"){
            $plunin_installed['games']=true;
        }else{
            $plunin_installed['games']=false;
        }

//-------------------disable Adsense
/*
        if ( OW::getPluginManager()->isPluginActive('adsense') AND OW::getConfig()->getValue('search', 'turn_offplugin_adsense')!="1"){
            $plunin_installed['adsense']=true;
        }else{
            $plunin_installed['adsense']=false;
        }
*/
        $plunin_installed['adsense']=false;


//mochigames_item
        if ( OW::getPluginManager()->isPluginActive('mochigames') AND OW::getConfig()->getValue('search', 'turn_offplugin_mochigames')!="1"){
            $plunin_installed['mochigames']=true;
        }else{
            $plunin_installed['mochigames']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('userwiki') AND OW::getConfig()->getValue('search', 'turn_offplugin_wiki')!="1"){
            $plunin_installed['wiki']=true;
        }else{
            $plunin_installed['wiki']=false;
        }
/*
        if ( OW::getPluginManager()->isPluginActive('basepages') AND OW::getConfig()->getValue('search', 'turn_offplugin_basepages')!="1"){//TODO...
            $plunin_installed['basepages']=true;
        }else{
            $plunin_installed['basepages']=false;
        }
*/

        if ( OW::getPluginManager()->isPluginActive('adspro') AND OW::getConfig()->getValue('search', 'turn_offplugin_adspro')!="1"){
            $plunin_installed['adspro']=true;
        }else{
            $plunin_installed['adspro']=false;
        }

        $plunin_installed['basepages']=false;

$tabt="";
$tab="";
//echo "saa";
$query="";
if (!isset($_POST['query']))$_POST['query']="";

            if ($id_user>0 AND $limit_results>0){
                if ($_POST['action']=="search" AND strlen($_POST['query'])>1){
                    $query=$_POST['query'];
                        $limit=$limit_results;

    $add_query="";
    $add_users="";
    $add_users_lower="";
//    $add_query .=" AND (userss.username LIKE '".addslashes($_GET['qstat'])."' OR LOWER(userss.username) LIKE '".addslashes(strtolower($_GET['qstat']))."' ";
//    $add_query .=" OR (userssquestion.questionName='realname' AND (userssquestion.textValue LIKE '".addslashes($_GET['qstat'])."' OR LOWER(userssquestion.textValue) LIKE '".addslashes(strtolower($_GET['qstat']))."') ) ";
//    $add_query .=") ";    

        $add_users .=" uss.username LIKE '".addslashes($query)."%' OR  ";
        $add_users .=" uadd.textValue LIKE '".addslashes($query)."%' OR ";
        $add_users_lower .=" LOWER(uss.username) LIKE '".addslashes(strtolower($query))."%' OR  ";
        $add_users_lower .=" LOWER(uadd.textValue) LIKE '".addslashes(strtolower($query))."%' ";

    if (OW::getConfig()->getValue('search', 'search_force_users')==2){
        $add_query .=" AND (uadd.textValue LIKE '%".addslashes($query)."%' OR LOWER(uadd.textValue) LIKE '%".addslashes(strtolower($query))."%') ";    

    $add_users="";
    $add_users_lower="";
        $add_users .=" uss.username LIKE '%".addslashes($query)."%' OR  ";
        $add_users .=" uadd.textValue LIKE '%".addslashes($query)."%' OR ";
        $add_users_lower .=" LOWER(uss.username) LIKE '%".addslashes(strtolower($query))."%' OR  ";
        $add_users_lower .=" LOWER(uadd.textValue) LIKE '%".addslashes(strtolower($query))."%' ";
    }else if (OW::getConfig()->getValue('search', 'search_force_users')){
        $add_query .=" AND (uadd.textValue LIKE '".addslashes($query)."%' OR LOWER(uadd.textValue) LIKE '".addslashes(strtolower($query))."%') ";    
    }else{
        $add_query .=" AND uadd.questionName='realname' ";


    }

//                        LEFT JOIN " . OW_DB_PREFIX. "base_question_data uadd ON (uadd.userId=uss.id AND uadd.questionName='realname') 
                        $sql = "SELECT uss.*,uadd.textValue FROM " . OW_DB_PREFIX. "base_user uss 
                        LEFT JOIN " . OW_DB_PREFIX. "base_question_data uadd ON (uadd.userId=uss.id ".$add_query." ) 
                        WHERE 
                        (
                            ".$add_users."
                            ".$add_users_lower."
                        ) 
                        ORDER BY uss.joinIp DESC LIMIT ".$limit;
//echo $sql;exit;
                        $arr = OW::getDbo()->queryForList($sql);
                        $tabt="";
                        foreach ( $arr as $value )
                        {
//echo $value['id'];
                            if ($curent_result<$maxallresults){
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['id']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['id']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id']);
//                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                    $tabt .="<div class=\"ow_console_dropdown_hoverX clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
                                    if ($uimg){
                                        $tabt .="<a href=\"".$uurl."\" style=\"display:inline;;font-size:14px;font-weight:bold;\">";
                                        $tabt .="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                                        $tabt .="</a>";
                                    }else{
                                        $tabt .="<a href=\"".$uurl."\" style=\"display:inline;;font-size:14px;font-weight:bold;\">";
                                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                                        $tabt .="</a>";
                                    }
                                    $tabt .="</div>";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;;float:left;min-height:40px;min-width:230px;max-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$uurl."\" style=\"display:inline;;font-size:14px;font-weight:bold;\">";
                                    $tabt .=$dname;
                                    $tabt .="</a>";
                                    $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                        }
                        if ($tabt) {
//                            $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                            $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                            $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/user?query=".$query."\">";
                            $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_user')."</b>";
                            $tab .="</a>";
                                $tab .="<div style=\"float:right;margin-top:5px;\">";
                                    $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/user?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                $tab .="</div>";
                            $tab .="</div>";                         
                            $tab .=$tabt;
                        }




//                        $tab .=SEARCH_BOL_Service::getInstance()->make_adsfromadsense();
//                        $tab .=SEARCH_BOL_Service::getInstance()->make_adsfromadspro();
                        $tab .=SEARCH_BOL_Service::getInstance()->make_ads('top');



//echo $curent_result."--".$maxallresults;
                        if ($plunin_installed['pages'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "pages WHERE active='1' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY id DESC LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                                if ($curent_result<$maxallresults){
                                    $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                        $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                        $tabt .="<a href=\"".$curent_url."page/".$value['id']."/index.html\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                        $tabt .=$this->clear_text(stripslashes($value['title']),30);
                                        if (strlen(stripslashes($value['title'])>30)) $tabt .="...";
                                        $tabt .="</a>";
                                        $tabt .="</div>";
                                    $tabt .="</div>";
                                    $curent_result++;
                                }else{
                                    break;
                                }
                                

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/pages?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_pages')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/pages?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }

                        if ($plunin_installed['shoppro'] AND ($curent_result<$maxallresults)){
                            $limit=$limit_results;
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "shoppro_products WHERE active='1' AND items>'0' AND (name LIKE '%".addslashes($query)."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%') ORDER BY price LIMIT ".$limit;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "shoppro_products WHERE active='1' AND (name LIKE '%".addslashes($query)."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%') ORDER BY price LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                            $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";

                                $uimg_loc="./ow_userfiles/plugins/shoppro/images/product_".$value['id'].".jpg";
                                $uimg=$curent_url."ow_userfiles/plugins/shoppro/images/product_".$value['id'].".jpg";

                                if (is_file($uimg_loc)){
                                    $tabt .="<a href=\"".$curent_url."product/".$value['id']."/zoom/index.html\" >";
                                    $tabt .="<img src=\"".$uimg."\" title=\"".stripslashes($value['name'])."\" width=\"45px\" style=\"border:0;margin:5px;align:leftX;display:block;width:45px;\">";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                }
                                $tabt .="</div>";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;;float:left;min-height:40px;min-width:230px;max-width:230px;margin-left:20px;margin-top:20px;\">";
                                $tabt .="<a href=\"".$curent_url."product/".$value['id']."/zoom/index.html\" title=\"".stripslashes($value['name'])."\" style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                $tabt .=$this->clear_text(stripslashes($value['name']),30);
                                if (strlen(stripslashes($value['name'])>30)) $tabt .="...";
                                $tabt .="</a>";
                                $tabt .="</div>";
                            $tabt .="</div>";
                            $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/shoppro?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_shoppro')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/shoppro?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;

                            }
                        }

                        if ($plunin_installed['classifiedspro'] AND ($curent_result<$maxallresults)){
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "classifiedspro_products WHERE active='1' AND (name LIKE '%".addslashes($query)."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%') ORDER BY price LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                            $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";

                                $uimg_loc="./ow_userfiles/plugins/classifiedspro/images/product_".$value['id'].".jpg";
                                $uimg=$curent_url."ow_userfiles/plugins/classifiedspro/images/product_".$value['id'].".jpg";

                                if (is_file($uimg_loc)){
                                    $tabt .="<a href=\"".$curent_url."classifieds/".$value['id']."/zoom/index.html\" >";
                                    $tabt .="<img src=\"".$uimg."\" title=\"".stripslashes($value['name'])."\" width=\"45px\" style=\"border:0;margin:5px;align:leftX;display:block;width:45px;\">";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                }
                                $tabt .="</div>";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;;float:left;min-height:40px;min-width:230px;max-width:230px;margin-left:20px;margin-top:20px;\">";
                                $tabt .="<a href=\"".$curent_url."classifieds/".$value['id']."/zoom/index.html\" title=\"".stripslashes($value['name'])."\" style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                $tabt .=$this->clear_text(stripslashes($value['name']),30);
                                if (strlen(stripslashes($value['name'])>30)) $tabt .="...";
                                $tabt .="</a>";
                                $tabt .="</div>";
                            $tabt .="</div>";
                            $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/classifiedspro?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_classifiedspro')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/classifiedspro?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;

                            }
                        }

                        if ($plunin_installed['cms'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "cms_blocks WHERE (position='center' OR position='left' OR position='right') AND (name LIKE '%".addslashes($query)."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%') 
//                                        ORDER BY data_created DESC,id_block DESC LIMIT ".$limit;
                                $sql = "SELECT bl.* FROM " . OW_DB_PREFIX. "cms_blocks bl 
                                LEFT JOIN " . OW_DB_PREFIX. "cms_content cn ON (cn.id_block=bl.id_block) 
                                WHERE 
                        
                                (bl.position='center' OR bl.position='left' OR bl.position='right') AND 
                                (
                                    (bl.name LIKE '%".addslashes($query)."%' OR LOWER(bl.name) LIKE '%".addslashes(strtolower($query))."%') 
                                    OR
                                    (cn.content LIKE '%".addslashes($query)."%' OR LOWER(cn.content) LIKE '%".addslashes($query)."%')
                                ) 
                                ORDER BY bl.data_created DESC,bl.id_block DESC LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;text-align:left;\">";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                if ($value['id_page']>0){
                                    $tabt .="<a href=\"".$curent_url."pb/".$value['id_block']."/".$value['id_page']."/index.html\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                }else{
                                    $tabt .="<a href=\"".$curent_url."pg/".$value['id_block']."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                }
                                $tabt .=$this->clear_text(stripslashes($value['name']),30);
                                if (strlen(stripslashes($value['name'])>30)) $tabt .="...";
                                $tabt .="</a>";
                                $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/cms?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_cms')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/cms?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }

                        if ($plunin_installed['forum'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "forum_topic WHERE (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY viewCount DESC,lastPostId LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                $tabt .="<a href=\"".$curent_url."forum/topic/".$value['id']."\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                $tabt .=$this->clear_text(stripslashes($value['title']),30);
                                if (strlen(stripslashes($value['title'])>30)) $tabt .="...";
                                $tabt .="</a>";
                                $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/forum?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_forum')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/forum?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }

                        if ($plunin_installed['map'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "map WHERE (active='1' AND name LIKE '%".addslashes($query)."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%') ORDER BY data_addm DESC LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                $tabt .="<a href=\"".$curent_url."map/zoom/".$value['id']."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                $tabt .=$this->clear_text(stripslashes($value['name']),30);
                                if (strlen(stripslashes($value['name'])>30)) $tabt .="...";
                                $tabt .="</a>";
                                $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/map?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_map')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/map?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }

                        if ($plunin_installed['news'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "news WHERE (active='1' AND is_published='1' AND topic_name LIKE '%".addslashes($query)."%' OR LOWER(topic_name) LIKE '%".addslashes(strtolower($query))."%') ORDER BY data_added DESC LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
/*
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                $tabt .="<a href=\"".$curent_url."news/".$value['id']."/index.html\" title=\"".stripslashes($value['topic_name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                $tabt .=$this->clear_text(stripslashes($value['topic_name']),30);
                                if (strlen(stripslashes($value['topic_name'])>30)) $tabt .="...";
                                $tabt .="</a>";
                                $tabt .="</div>";
                                $tabt .="</div>";
*/
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['id_sender']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_sender']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_sender']);
//                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                    $tabt .="<div class=\"ow_console_dropdown_hoverX clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
                                    if ($uimg){
                                        $tabt .="<a href=\"".$uurl."\" style=\"display:inline;;font-size:14px;font-weight:bold;\">";
                                        $tabt .="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                                        $tabt .="</a>";
                                    }else{
                                        $tabt .="<a href=\"".$uurl."\" style=\"display:inline;;font-size:14px;font-weight:bold;\">";
                                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                                        $tabt .="</a>";
                                    }
                                    $tabt .="</div>";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;;float:left;min-height:40px;min-width:230px;max-width:230px;margin-left:20px;margin-top:20px;\">";
//                                    $tabt .="<a href=\"".$uurl."\" style=\"display:inline;;font-size:14px;font-weight:bold;\">";
//                                    $tabt .=$dname;
//                                    $tabt .="</a>";
                                    $tabt .="<a href=\"".$curent_url."news/".$value['id']."/index.html\" title=\"".stripslashes($value['topic_name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .=$this->clear_text(stripslashes($value['topic_name']),250);
                                    if (strlen(stripslashes($value['topic_name'])>250)) $tabt .="...";
                                    $tabt .="</a>";
                                    $tabt .="</div>";


                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/news?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_news')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/news?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }


                        if ($plunin_installed['links'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "links_link WHERE (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY timestamp DESC LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                            $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:8px;display:inline-block;float:left;width:45px;\">";

//                                $uimg_loc="./ow_userfiles/plugins/shoppro/images/product_".$value['id'].".jpg";
//                                $uimg="/ow_userfiles/plugins/shoppro/images/product_".$value['id'].".jpg";
//                                if (is_file($uimg_loc)){
//                                    $tabt .="<a href=\"".$curent_url."forum/topic/".$value['id']."/zoom/index.html\" >";
//                                    $tabt .="<img src=\"".$uimg."\" title=\"".stripslashes($value['name'])."\" width=\"45px\">";
//                                    $tabt .="</a>";
//                                }else{
/////                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                    $tabt .="".OW::getLanguage()->text('search', 'index_hasnotimage')."";
//                                }
                                $tabt .="</div>";

                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;;float:left;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                $tabt .="<a href=\"".$curent_url."link/".$value['id']."\" title=\"".stripslashes($value['title'])."\"   style=\"display:inline;;font-size:12px;font-weight:bold;\"  >";
                                $tabt .=$this->clear_text($value['title'],30);
                                if (strlen(stripslashes($value['title'])>30)) $tabt .="...";
                                $tabt .="</a>";
                                $tabt .="</div>";
                            $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/links?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_links')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/links?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }




                        if ($plunin_installed['links'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "links_link WHERE (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY timestamp DESC LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                            $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:8px;display:inline-block;float:left;width:45px;\">";

//                                $uimg_loc="./ow_userfiles/plugins/shoppro/images/product_".$value['id'].".jpg";
//                                $uimg="/ow_userfiles/plugins/shoppro/images/product_".$value['id'].".jpg";
//                                if (is_file($uimg_loc)){
//                                    $tabt .="<a href=\"".$curent_url."forum/topic/".$value['id']."/zoom/index.html\" >";
//                                    $tabt .="<img src=\"".$uimg."\" title=\"".stripslashes($value['name'])."\" width=\"45px\">";
//                                    $tabt .="</a>";
//                                }else{
/////                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                    $tabt .="".OW::getLanguage()->text('search', 'index_hasnotimage')."";
//                                }
                                $tabt .="</div>";

                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;;float:left;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                $tabt .="<a href=\"".$curent_url."link/".$value['id']."\" title=\"".stripslashes($value['title'])."\"   style=\"display:inline;;font-size:12px;font-weight:bold;\"  >";
                                $tabt .=$this->clear_text($value['title'],30);
                                if (strlen(stripslashes($value['title'])>30)) $tabt .="...";
                                $tabt .="</a>";
                                $tabt .="</div>";
                            $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/links?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_links')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/links?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }



                        if ($plunin_installed['video'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "video_clip WHERE (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY addDatetime DESC LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                    $uimg="";
                                    $unique_id="";
                                    preg_match_all('/(youtube.com\/embed\/)([a-z0-9\-_]+)/i',stripslashes($value['code']),$matches);
                                    if(isset($matches[2])){
                                        if (isset($matches[2][0])){
                                            $unique_id=$matches[2][0];
                                        }
                                    }
                                    $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:8px;display:inline-block;float:left;width:45px;\">";

                                    if ($unique_id){
                                        $tabt .="<a href=\"".$curent_url."video/view/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
                                        $tabt .="<img src=\"http://img.youtube.com/vi/".$unique_id."/default.jpg\" width=\"45px\"  style=\"border:0;margin:5px;align:leftX;display:block;width:45px;\" />";
                                        $tabt .="</a>";
                                    }else{
                                        $tabt .="".OW::getLanguage()->text('search', 'index_hasnotimage')."";
                                    }

                                $tabt .="</div>";

                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;;float:left;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                $tabt .="<a href=\"".$curent_url."video/view/".$value['id']."\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                $tabt .=$this->clear_text($value['title'],30);
                                if (strlen(stripslashes($value['title'])>30)) $tabt .="...";
                                $tabt .="</a>";
                                $tabt .="</div>";
                            $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/video?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_video')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/video?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }


                        if ($plunin_installed['photo'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "photo WHERE (description LIKE '%".addslashes($query)."%' OR LOWER(description) LIKE '%".addslashes(strtolower($query))."%') ORDER BY addDatetime DESC LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:8px;display:inline-block;float:left;width:45px;\">";
                                $uimg_loc="./ow_userfiles/plugins/photo/photo_preview_".$value['id'].".jpg";
                                $uimg=$curent_url."ow_userfiles/plugins/photo/photo_preview_".$value['id'].".jpg";
                                if (is_file($uimg_loc)){
                                    $tabt .="<a href=\"".$curent_url."photo/view/".$value['id']."\" >";
                                    $tabt .="<img src=\"".$uimg."\" title=\"".stripslashes($value['description'])."\" width=\"45px\"  style=\"border:0;margin:5px;align:leftX;display:block;width:45px;\"  >";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="".OW::getLanguage()->text('search', 'index_hasnotimage')."";
                                }
                                $tabt .="</div>";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;;float:left;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                $tabt .="<a href=\"".$curent_url."photo/view/".$value['id']."\" title=\"".stripslashes($value['description'])."\" style=\"display:inline;;font-size:12px;font-weight:bold;\">";
                                $tabt .=$this->clear_text($value['description'],30);
                                if (strlen(stripslashes($value['description'])>30)) $tabt .="...";
                                $tabt .="</a>";
                                $tabt .="</div>";
                            $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/photo?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_photo')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/photo?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }

                        if ($plunin_installed['groups'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "groups_group WHERE whoCanView='anyone' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY timeStamp DESC LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."groups/".$value['id']."\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .=$this->clear_text(stripslashes($value['title']),30);
                                    if (strlen(stripslashes($value['title'])>30)) $tabt .="...";
                                    $tabt .="</a>";
                                    $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/groups?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_groups')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/groups?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }

                        if ($plunin_installed['blogs'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "blogs_post WHERE privacy='everybody' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY timestamp DESC LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."blogs/".$value['id']."\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .=$this->clear_text(stripslashes($value['title']),30);
                                    if (strlen(stripslashes($value['title'])>30)) $tabt .="...";
                                    $tabt .="</a>";
                                    $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/blogs?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_blogs')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/blogs?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }

                        if ($plunin_installed['event'] AND ($curent_result<$maxallresults)) {

                            $limit=$limit_results;
//                            $timestamp_start=strtotime(date('Y-m-d H:i:s'))-1;
//                            $timestamp_end=$timestamp_start+1;
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND startTimeStamp<'".addslashes($timestamp_start)."' AND (endDateFlag='0' OR (endDateFlag='1' AND endTimeStamp>'".addslashes($timestamp_end)."')) AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY startTimeStamp DESC LIMIT ".$limit;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY startTimeStamp DESC LIMIT ".$limit;
//echo $sql;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."event/".$value['id']."\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .=$this->clear_text(stripslashes($value['title']),30);
                                    if (strlen(stripslashes($value['title'])>30)) $tabt .="...";
                                    $tabt .="</a>";
                                    $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/event?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_event')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/event?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }
//echo "----".$plunin_installed['fanpage'];
                        if ($plunin_installed['fanpage'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
//                            $timestamp_start=strtotime(date('Y-m-d H:i:s'))-100;
//                            $timestamp_end=$timestamp_start+100;                            
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND startTimeStamp<'".addslashes($timestamp_start)."' AND (endDateFlag='0' OR (endDateFlag='1' AND endTimeStamp>'".addslashes($timestamp_end)."')) AND title LIKE '%".addslashes($query)."%' ORDER BY startTimeStamp DESC LIMIT ".$limit;
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (tags LIKE '%".addslashes($query)."%' OR title_fan_page LIKE '%".addslashes($query)."%' OR fanpage_url_name LIKE '%".addslashes($query)."%') ORDER BY promotion_is_vip DESC, sortt DESC LIMIT ".$limit;


                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (title_fan_page LIKE '%".addslashes($query)."%' OR fanpage_url_name LIKE '%".addslashes($query)."%' OR LOWER(title_fan_page) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(fanpage_url_name) LIKE '%".addslashes(strtolower($query))."%') ORDER BY promotion_is_vip DESC, sortt DESC LIMIT ".$limit;
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND (title_fan_page LIKE '%".addslashes($query)."%' OR fanpage_url_name LIKE '%".addslashes($query)."%' OR LOWER(title_fan_page) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(fanpage_url_name) LIKE '%".addslashes(strtolower($query))."%') ORDER BY promotion_is_vip DESC, sortt DESC LIMIT ".$limit;
//echo $sql;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."fanpage/".$value['fanpage_url_name']."\" title=\"".stripslashes($value['title_fan_page'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .=$this->clear_text(stripslashes($value['title_fan_page']),30);
                                    if (strlen(stripslashes($value['title_fan_page'])>30)) $tabt .="...";
                                    $tabt .="</a>";
                                    $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/fanpage?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_fanpage')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/fanpage?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }


                        if ($plunin_installed['html'] AND ($curent_result<$maxallresults)) {

                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "html WHERE title LIKE '%".addslashes($query)."%' OR content LIKE '%".addslashes($query)."%' ORDER BY order_main DESC LIMIT ".$limit;
//echo $sql;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."html/".$value['id_owner']."/".$value['id']."/index.html\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .=$this->clear_text(stripslashes($value['title']),30);
                                    if (strlen(stripslashes($value['title'])>30)) $tabt .="...";
                                    $tabt .="</a>";
                                    $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/html?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_html')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/html?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }

                        if ($plunin_installed['games'] AND ($curent_result<$maxallresults)) {

                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "games WHERE tags LIKE '%".addslashes($query)."%' OR name LIKE '%".addslashes($query)."%' OR description LIKE '%".addslashes($query)."%' OR LOWER(tags) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(description) LIKE '%".addslashes(strtolower($query))."%' ORDER BY data_add DESC LIMIT ".$limit;
//echo $sql;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";

                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
                                if ($value['thumbal']){
                                    $tabt .="<a href=\"".$curent_url."games/".$value['id']."_".$value['id_cats']."/index.html\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .="<img src=\"".stripslashes($value['thumbal'])."\" title=\"".stripslashes($value['name'])."\" width=\"45px\"  style=\"border:0;margin:5px;align:leftX;display:block;width:45px;\" >";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                }
                                $tabt .="</div>";

//json_decode($this->data['control_scheme'], true);
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."games/".$value['id']."_".$value['id_cats']."/index.html\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .=$this->clear_text(stripslashes($value['name']),30);
                                    if (strlen(stripslashes($value['name'])>30)) $tabt .="...";
                                    $tabt .="</a>";
                                    $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/games?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_games')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/games?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }


                        if ($plunin_installed['adsense'] AND ($curent_result<$maxallresults)) {

                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "adsense WHERE name LIKE '%".addslashes($query)."%' OR description LIKE '%".addslashes($query)."%' ORDER BY data_add DESC LIMIT ".$limit;
//echo $sql;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."adsense\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .=$this->clear_text(stripslashes($value['name']),30);
                                    if (strlen(stripslashes($value['name'])>30)) $tabt .="...";
                                    $tabt .="</a>";
                                    $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."adsense\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_adsense')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."adsense\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";
                                $tab .=$tabt;
                            }
                        }


//echo $curent_result."--".$maxallresults;
                        if ($plunin_installed['mochigames'] AND ($curent_result<$maxallresults)) {

                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "mochigames_item WHERE name LIKE '%".addslashes($query)."%' OR description LIKE '%".addslashes($query)."%' ORDER BY timestamp DESC LIMIT ".$limit;
//echo $sql;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";

                                $addinfo = json_decode($value['json'], true);
//print_r($addinfo);exit;
//echo "--".$addinfo['games']['0']['thumbnail_url'];
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
                                if ($addinfo['games']['0']['thumbnail_url']){
                                    $tabt .="<a href=\"".$curent_url."mochigames/".$value['game_tag']."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .="<img src=\"".stripslashes($addinfo['games']['0']['thumbnail_url'])."\" title=\"".stripslashes($value['name'])."\" width=\"45px\"  style=\"border:0;margin:5px;align:leftX;display:block;width:45px;\" >";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                }
                                $tabt .="</div>";

//json_decode($this->data['control_scheme'], true);
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."mochigames/".$value['game_tag']."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .=$this->clear_text(stripslashes($value['name']),30);
                                    if (strlen(stripslashes($value['name'])>30)) $tabt .="...";
                                    $tabt .="</a>";
                                    $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
//                                    $tab .="<div style=\"float:left;width:250px;\">";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/mochigames?query=".$query."\">";
//                                $tab .="<a style=\"max-height:16px;width:auto;\" href=\"".$curent_url."query/mochigames?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_mochigames')."</b>";
                                $tab .="</a>";
//                                    $tab .="</div>";

                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/mochigames?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }

                        if ($plunin_installed['wiki'] AND ($curent_result<$maxallresults)) {

                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "userwiki_pages WHERE title LIKE '%".addslashes($query)."%' OR information LIKE '%".addslashes($query)."%' ORDER BY added DESC LIMIT ".$limit;
//echo $sql;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                            if ($curent_result<$maxallresults){
                                $tabt .="<div class=\"aron_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";

//                                $addinfo = json_decode($value['json'], true);
//print_r($addinfo);exit;
//echo "--".$addinfo['games']['0']['thumbnail_url'];
///                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
/*
                                if ($addinfo['games']['0']['thumbnail_url']){
                                    $tabt .="<a href=\"".$curent_url."userwiki/view/".$value['id']."\" title=\"".stripslashes($value['nam'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .="<img src=\"".stripslashes($addinfo['games']['0']['thumbnail_url'])."\" title=\"".stripslashes($value['name'])."\" width=\"45px\"  style=\"border:0;margin:5px;align:leftX;display:block;width:45px;\" >";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                }
*/
///                                $tabt .="</div>";


//json_decode($this->data['control_scheme'], true);
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."userwiki/view/".$value['id']."\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .=$this->clear_text(stripslashes($value['information']),250);
                                    if (strlen(stripslashes($value['information'])>250)) $tabt .="...";
                                    $tabt .="</a>";
                                    $tabt .="</div>";
                                $tabt .="</div>";
                                $curent_result++;
                            }else{
                                break;
                            }
                            

                            }
//echo $query.$tabt;
                            if ($tabt) {       
//                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;margin:auto;overflow:hidden;\"  >";
                                $tab .="<div class=\"ow_ipc_header clearfix\" style=\"border-bottom:1px solid #eee;padding:0px;padding-left:5px;margin:2px;border-left:2px solid #aaa;overflow:hidden;\"  >";
//                                    $tab .="<div style=\"float:left;width:250px;\">";
                                $tab .="<a style=\"float:left;max-height:22px;min-width:120px;text-align:left;\" href=\"".$curent_url."query/wiki?query=".$query."\">";
//                                $tab .="<a style=\"max-height:16px;width:auto;\" href=\"".$curent_url."query/mochigames?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_wiki')."</b>";
                                $tab .="</a>";
//                                    $tab .="</div>";

                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/wiki?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                    $tab .="</div>";
                                $tab .="</div>";                         
                                $tab .=$tabt;
                            }
                        }







                        if ($tab){
                            echo $tab;
                            $tab_end ="";
                            $tab_end .="<div class=\"clearfix\" style=\"width:100%;margin:auto;border-bottom:1px solid #eee;display:block;\">";
                                $tab_end .="<a id=\"search_submit_more\"  href=\"".$curent_url."query/search?query=".$query."\" style=\"display:inline;width:100%;margin:auto;\">";
                                    $tab_end .="<div  class=\"ow_ipc_header clearfix\" style=\"text-align:center;font-weight:bold;font-size:10px;display:block;min-height:40px;border-top:1px solid #ddd;margin-top:5px;\">";
                                    $tab_end .=OW::getLanguage()->text('search', 'index_seemoreresults_topsearch3', array('query' => $query, 'maxnumresults'=>$maxallresults));
                                    $tab_end .="</div>";
                                $tab_end .="</a>";
                            $tab_end .="</div>";
                            echo $tab_end;
                        }else{
//                            echo OW::getLanguage()->text('search', 'main_noresultsfound');
                            $tab_end="";
                            $tab_end .="<div class=\"clearfix\" style=\"width:100%;margin:auto;border-bottom:1px solid #eee;display:block;\">";
                                $tab_end .="<a id=\"search_submit_more\"  href=\"".$curent_url."query/search?query=".$query."\" style=\"display:inline;width:100%;margin:auto;\">";
                                    $tab_end .="<div  class=\"ow_ipc_header clearfix\" style=\"text-align:center;font-weight:bold;font-size:10px;display:block;min-height:20px;border-top:1px solid #ddd;margin-top:5px;\">";
                                    $tab_end .=OW::getLanguage()->text('search', 'main_noresultsfound');
                                    $tab_end .="</div>";
                                $tab_end .="</a>";
                            $tab_end .="</div>";
                            echo $tab_end;
                        }
                }
            }
    
exit;
    }


    public function make_seo_url($name,$lengthtext=100)
    {
        $seo_title=stripslashes($name);
        $seo_title=mb_substr($seo_title,0,$lengthtext);
        $seo_title=str_replace(" ","_",$seo_title);
        $seo_title=str_replace(chr(160),"_",$seo_title);
        $seo_title=str_replace("~","",$seo_title);
        $seo_title=str_replace("(","",$seo_title);
        $seo_title=str_replace(")","",$seo_title);
        $seo_title=str_replace("]","",$seo_title);
        $seo_title=str_replace("[","",$seo_title);
        $seo_title=str_replace("}","",$seo_title);
        $seo_title=str_replace("{","",$seo_title);
        $seo_title=str_replace("/","",$seo_title);
        $seo_title=str_replace("\\","",$seo_title);
        $seo_title=str_replace("+","",$seo_title);
        $seo_title=str_replace(":","",$seo_title);
        $seo_title=str_replace(";","",$seo_title);
        $seo_title=str_replace("\"","",$seo_title);
        $seo_title=str_replace("<","",$seo_title);
        $seo_title=str_replace(">","",$seo_title);
        $seo_title=str_replace("?","",$seo_title);
        $seo_title=str_replace(",",".",$seo_title);
        $seo_title=str_replace("!","",$seo_title);
        $seo_title=str_replace("`","",$seo_title);
        $seo_title=str_replace("'","",$seo_title);
        $seo_title=str_replace("@","",$seo_title);
        $seo_title=str_replace("#","",$seo_title);
        $seo_title=str_replace("$","",$seo_title);
        $seo_title=str_replace("%","",$seo_title);
        $seo_title=str_replace("^","",$seo_title);
        $seo_title=str_replace("&","",$seo_title);
        $seo_title=str_replace("*","",$seo_title);
        $seo_title=str_replace("|","",$seo_title);
        $seo_title=str_replace("=","",$seo_title);
        $seo_title=str_replace(" ","_",$seo_title);
        $seo_title=str_replace("/","",$seo_title);
        $seo_title=str_replace("?","_",$seo_title);
        $seo_title=str_replace("#","_",$seo_title);
        $seo_title=str_replace("=","_",$seo_title);
        $seo_title=str_replace("=","_",$seo_title);
        $seo_title=str_replace("&amp;","_",$seo_title);
        $seo_title=str_replace("__","_",$seo_title);
        $seo_title = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $seo_title);
        $seo_title =strtolower($seo_title);

        return $seo_title;
    }

    public function clear_text($name,$lengthtext=100)
    {
        $seo_title=stripslashes($name);
        $seo_title=mb_substr($seo_title,0,$lengthtext);
        $seo_title=str_replace("\r\n"," ",$seo_title);
        $seo_title=str_replace("\r"," ",$seo_title);
        $seo_title=str_replace("\n"," ",$seo_title);
        $seo_title=str_replace("<br/>","",$seo_title);
        $seo_title=str_replace("<br>","",$seo_title);
        $seo_title=str_replace("<br/>","",$seo_title);
        $seo_title=str_replace("  "," ",$seo_title);
        $seo_title=str_replace("  "," ",$seo_title);
        $seo_title=str_replace("  "," ",$seo_title);

        return $seo_title;
    }



    public function index($params) 
    { 



        $content="";
        $menu="";

        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
                    $curent_url = 'http';
                    if (isset($_SERVER["HTTPS"])) {$curent_url .= "s";}
                    $curent_url .= "://";
                    $curent_url .= $_SERVER["SERVER_NAME"]."/";
$curent_url=OW_URL_HOME;
/*
$from_config=$curent_url;
$from_config=str_replace("https://","",$from_config);
$from_config=str_replace("http://","",$from_config);
$trash=explode($from_config,$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
$url_detect=$trash[1];
//print_r($trash);
//echo $url_detect;
*/

        if (!$id_user){
            OW::getFeedback()->error(OW::getLanguage()->text('search', 'for_search_mastbe_login'));
//            OW::getApplication()->redirect($curent_url);
            $curent_full_url=$_SERVER["REQUEST_URI"];
            OW::getApplication()->redirect($curent_url."sign-in?back-uri=".urlencode($curent_full_url));

            exit;
        }

$add_paramurl="";
        $per_page=20;
        $start_form=0;
        if (isset($_GET['page']) AND $_GET['page']>1){
            $curent_page=$_GET['page'];
        }else{
            $curent_page=1;
        }
//echo $curent_page;
        $start_form=(($curent_page-1)*$per_page);
        if (!$start_form) $start_form=0;

        $prev_page=($curent_page-1)-1;
        if ($prev_page<0) $prev_page=0;

        $paging="";
//        $paging=$this->pagination($curent_page=0,$next_page=0,$url_pages="")
//        $paging=$this->pagination();



        $limit_all=10;
        $limit_single=$limit_all;
$global_found=false;


        if ( OW::getPluginManager()->isPluginActive('forum') AND OW::getConfig()->getValue('search', 'turn_offplugin_forum')!="1"){
            $plunin_installed['forum']=true;
        }else{
            $plunin_installed['forum']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('map') AND OW::getConfig()->getValue('search', 'turn_offplugin_map')!="1"){
            $plunin_installed['map']=true;
        }else{
            $plunin_installed['map']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('news') AND OW::getConfig()->getValue('search', 'turn_offplugin_news')!="1"){
            $plunin_installed['news']=true;
        }else{
            $plunin_installed['news']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('cms') AND OW::getConfig()->getValue('search', 'turn_offplugin_cms')!="1"){
            $plunin_installed['cms']=true;
        }else{
            $plunin_installed['cms']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('links') AND OW::getConfig()->getValue('search', 'turn_offplugin_links')!="1"){
            $plunin_installed['links']=true;
        }else{
            $plunin_installed['links']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('video') AND OW::getConfig()->getValue('search', 'turn_offplugin_video')!="1"){
            $plunin_installed['video']=true;
        }else{
            $plunin_installed['video']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('photo') AND OW::getConfig()->getValue('search', 'turn_offplugin_photo')!="1"){
            $plunin_installed['photo']=true;
        }else{
            $plunin_installed['photo']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('shoppro') AND OW::getConfig()->getValue('search', 'turn_offplugin_shoppro')!="1"){
            $plunin_installed['shoppro']=true;
        }else{
            $plunin_installed['shoppro']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('classifiedspro') AND OW::getConfig()->getValue('search', 'turn_offplugin_classifiedspro')!="1"){
            $plunin_installed['classifiedspro']=true;
        }else{
            $plunin_installed['classifiedspro']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('pages') AND OW::getConfig()->getValue('search', 'turn_offplugin_pages')!="1"){
            $plunin_installed['pages']=true;
        }else{
            $plunin_installed['pages']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('groups') AND OW::getConfig()->getValue('search', 'turn_offplugin_groups')!="1"){
            $plunin_installed['groups']=true;
        }else{
            $plunin_installed['groups']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('blogs') AND OW::getConfig()->getValue('search', 'turn_offplugin_blogs')!="1"){
            $plunin_installed['blogs']=true;
        }else{
            $plunin_installed['blogs']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('event') AND OW::getConfig()->getValue('search', 'turn_offplugin_event')!="1"){
            $plunin_installed['event']=true;
        }else{
            $plunin_installed['event']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('fanpage') AND OW::getConfig()->getValue('search', 'turn_offplugin_fanpage')!="1"){
            $plunin_installed['fanpage']=true;
        }else{
            $plunin_installed['fanpage']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('html') AND OW::getConfig()->getValue('search', 'turn_offplugin_html')!="1"){
            $plunin_installed['html']=true;
        }else{
            $plunin_installed['html']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('games') AND OW::getConfig()->getValue('search', 'turn_offplugin_games')!="1"){
            $plunin_installed['games']=true;
        }else{
            $plunin_installed['games']=false;
        }



//----------------------disable adsense
/*
        if ( OW::getPluginManager()->isPluginActive('adsense') AND OW::getConfig()->getValue('search', 'turn_offplugin_adsense')!="1"){
            $plunin_installed['adsense']=true;
        }else{
            $plunin_installed['adsense']=false;
        }
*/
        $plunin_installed['adsense']=false;




        if ( OW::getPluginManager()->isPluginActive('mochigames') AND OW::getConfig()->getValue('search', 'turn_offplugin_mochigames')!="1"){
            $plunin_installed['mochigames']=true;
        }else{
            $plunin_installed['mochigames']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('userwiki') AND OW::getConfig()->getValue('search', 'turn_offplugin_wiki')!="1"){
            $plunin_installed['wiki']=true;
        }else{
            $plunin_installed['wiki']=false;
        }





/*
        if ( OW::getPluginManager()->isPluginActive('basepages') AND OW::getConfig()->getValue('search', 'turn_offplugin_basepages')!="1"){//TODO...
            $plunin_installed['basepages']=true;
        }else{
            $plunin_installed['basepages']=false;
        }
*/
        $plunin_installed['basepages']=false;

$curent_bg=2;


        $option="";
        if (isset($params['option'])) $option=$params['option'];
//echo "------------------------------------".$option;
        $query="*";
        if (isset($_GET['query'])) $query=$_GET['query'];
        if (strlen($query)<2){
            $query ="*";
        }
//echo "--".$query;
        $header_add="";
        $tab ="";
        $header="";
        if ($query) $add="?query=".$query;
            else $add="";

//if ($query) $add_paramurl="&query=".$query;//disable

//if (strlen($query)<2 OR $query=="*"){
//    $add_paramurl=$_SERVER['REQUEST_URI'];
//    list($a,$b)=explode("?",$_SERVER['REQUEST_URI']);
//    list($a,$b)=explode("?",$_SERVER['QUERY']);
//}
//print_r( parse_url($_SERVER['REQUEST_URI']) );
//echo $a."--".$b;
//    if ($b) $add_paramurl="&".$b;
//    else if ($a) $add_paramurl="&".$a; 
$queryfromule=parse_url($_SERVER['REQUEST_URI']);
//$add_paramurl =$queryfromule['query'];
if (!isset($queryfromule['query'])) {
    $queryfromule['query']=array();
    $queryfromule['query']="";
}
$array_query=explode("&",$queryfromule['query']);
//print_r($array_query);
$add_paramurl_search="";


/*
for($i=0;$i<count($array_query);$i++){
    if (substr($array_query[$i],0,11)=="search_text" OR substr($array_query[$i],0,10)=="search_sel"){
        if ($add_paramurl_search) $add_paramurl_search .="&";
        $add_paramurl_search .=$array_query[$i];
    }  
}
*/
//&search_addparam_city=gd&search_addparam_street=&search_addparam_country=&search_addparam_category=
//query=*&search_text%5B9221d78a4201eac23c972e1d4aa2cee6%5D=&search_text%5Bc441a8a9b955647cdf4c81562d39068a%5D=&search_sel%5Bsex%5D=1&search_sel%5Brelationship%5D=
/*
for($i=0;$i<count($array_query);$i++){
    list($namepar)=explode("=",$array_query[$i]);
    if ($namepar=="search_text" OR $namepar=="search_sel" OR 
            $namepar=="search_addparam_city" OR 
            $namepar=="search_addparam_street" OR 
            $namepar=="search_addparam_country" OR
            $namepar=="search_addparam_category" 
    ){
        if ($add_paramurl_search) $add_paramurl_search .="&";
        $add_paramurl_search .=$array_query[$i];
    }  
}
*/
/*
for($i=0;$i<count($array_query);$i++){
    list($namepar)=explode("=",$array_query[$i]);
    if ($namepar=="search_text" OR $namepar=="search_sel"
    ){
        if ($add_paramurl_search) $add_paramurl_search .="&";
        $add_paramurl_search .=$array_query[$i];
    }  
}
*/
if (isset($queryfromule['query']) AND $queryfromule['query']){
    if ($add_paramurl_search) $add_paramurl_search .="&";
        $add_paramurl_search .=$queryfromule['query'];
}

//echo $add_paramurl_search;exit;
//echo $_SERVER['REQUEST_URI'];
//print_r($queryfromule);
//exit;
if ($add_paramurl_search){
    if ($add_paramurl) $add_paramurl .="&".$add_paramurl_search;
        else $add_paramurl ="&".$add_paramurl_search;
}
//print_r(parse_url($_GET));
//print_r($_SERVER['REQUEST_URI']);
//print_r($_SERVER['REQUEST_URI']);
//echo $_SERVER['REQUEST_URI'];

$foundsomething=false;

    if ($id_user>0){
        if (!$option OR $option=="search") $option="";

        if ($option=="user"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_user');
        }else if ($option=="cms"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_cms');
        }else if ($option=="forum"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_forum');
        }else if ($option=="map"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_map');
        }else if ($option=="links"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_links');
        }else if ($option=="video"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_video');
        }else if ($option=="photo"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_photo');
        }else if ($option=="shoppro"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_shoppro');
        }else if ($option=="classifiedspro"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_classifiedspro');
        }else if ($option=="pages"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_pages');
        }else if ($option=="groups"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_groups');
        }else if ($option=="blogs"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_blogs');
        }else if ($option=="event"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_event');
        }else if ($option=="fanpage"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_fanpage');
        }else if ($option=="html"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_html');
        }else if ($option=="games"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_games');
        }else if ($option=="adsense"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_adsense');
        }else if ($option=="mochigames"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_mochigames');
        }else if ($option=="wiki"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_wiki');
        }else if ($option=="basepages"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_basepages');
        }else{
            $header_add="";
        }

        $header="<b>".OW::getLanguage()->text('search', 'main_yousearching').":</b> <a href=\"".$curent_url."query".$add."\" class=\"ow_lbutton\"><b>".$query."</b></a>";
        if ($header_add){
            $header .="&nbsp;>&nbsp;<a href=\"".$curent_url."query".$add."\" class=\"ow_lbutton\">".$header_add."</a>";
        }

//        $this->setPageTitle("Contact Us"); 
//        $this->setPageHeading("Contact Us"); 
        $this->setPageTitle(OW::getLanguage()->text('search', 'index_page_title')); //title menu
        $this->setPageHeading(OW::getLanguage()->text('search', 'index_page_heading')); //title page

/*
        $query = "SELECT * FROM " . OW_DB_PREFIX. "base_user WHERE username LIKE '".addslashes($query)."%' ORDER BY joinIp DESC LIMIT 10";
        $arr = OW::getDbo()->queryForList($query);
        $mtab="";
        foreach ( $arr as $value )
        {
                $dname=BOL_UserService::getInstance()->getDisplayName($value['id']);
                $uurl=BOL_UserService::getInstance()->getUserUrl($value['id']);
                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id']);
                $mtab .="<tr>";
                $mtab .="<td>";
                if ($uimg){
                    $mtab .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\">";
                }else{
                    $mtab .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                }
//BOL_UserService::getInstance()->getDisplayName($userId)
//BOL_UserService::getInstance()->getUserUrl($userId)
                $mtab .="</td>";
                $mtab .="<td>";
                $mtab .=$value['id'].") ";
//                $tab .=$value['username'];
                $mtab .=$dname;
                $mtab .="</td>";
                $mtab .="</tr>";
        }
        if ($mtab){
            $menu .="<table style=\"width:100%;margin:auto;\">";
            $menu .=$mtab;
            $menu .="</table>";
        }
*/



//echo print_r($_GET['search_text']);
        if (strlen($query)>1 OR $query=="*" OR (isset($_GET['search_text']) AND $_GET['search_text']!="") OR (isset($_GET['search_sel']) AND $_GET['search_sel']!="")){
//            $content .=OW::getLanguage()->text('search', 'main_yousearching').": <b>".$query."</b>";
//            $content .="<hr/>";
            if (!$option OR $option=="user") {
                if (!$option) {
                    $limit=$limit_all;
                }else {
//                    $limit=$limit_single;
                    $limit=$start_form.",".$per_page;
                }
//echo $start_form;

//print_r($_POST['search_text']);
//print_r($_GET['search_text']);exit;

//base_question_data
$joinon ="";
$joinonleft="";
$joinonleft2="";
$joinon_select ="";
$joinonwhere="";

$joinonleft_all="";
$joinon_all="";
$joinonleft2_if_all="";
$joinonleft2_all="";

$for_haveing=0;
//if ($query){
    if (isset($_GET['search_text'])) $addsearch=$_GET['search_text'];
        else $addsearch="";

    if (isset($_GET['search_sel'])) $addsearch_sel=$_GET['search_sel'];
        else $addsearch_sel="";
//print_r($addsearch);
    if ($addsearch){
        foreach($addsearch as $name=> $valx){
            if ($name AND $valx AND strlen($valx)>1){
//echo "<hr>".$valx."<hr>";
//                if ($joinonwhere) $joinonwhere .=" AND ";
                if ($joinonwhere) $joinonwhere .=" OR ";
                $joinonwhere .=" ( ";
                    $joinonwhere .="(bd2.questionName = '".addslashes($name)."' AND (bd2.textValue LIKE '%".addslashes($valx)."%' OR LOWER(bd2.textValue) LIKE '%".addslashes(strtolower($valx))."%')) ";
                    if ($valx>0){
                        $joinonwhere .=" OR ";
                        $joinonwhere .=" (bd2.questionName = '".addslashes($name)."' AND bd2.intValue ='".addslashes($valx)."') ";
                    }
                $joinonwhere .=" ) ";


//                if ($joinonleft) $joinonleft .=" AND ";
                if ($joinonleft) $joinonleft .=" OR ";
                $joinonleft .=" (bd2.userId=uss.id AND (`bd2`.`questionName` = '".addslashes($name)."') AND (bd2.textValue LIKE '%".addslashes($valx)."%' OR LOWER(bd2.textValue) LIKE '%".addslashes(strtolower($valx))."%'))  ";

//                if (strlen($query)>1){
//                    if ($joinonleft2) $joinonleft2 .=" AND ";
//                    $joinonleft2 .=" (bd3.userId=uss.id AND (`bd3`.`questionName` = 'realname') AND (bd3.textValue LIKE '".addslashes($valx)."%' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($valx))."%'))  ";
//                }
                $for_haveing=$for_haveing+1;
            }
        }
    }



    if ($addsearch_sel){
        foreach($addsearch_sel as $name=> $valx){
            if ($name AND $valx){
                if (!$joinonwhere)  $joinonwhere="1";
/*
                if ($joinonwhere) $joinonwhere .=" AND ";
                $joinonwhere .=" (";
                    $joinonwhere .=" (bd.questionName = '".addslashes($name)."' AND bd.intValue= '".addslashes($valx)."') ";
                    if ($valx>0){
                        $joinonwhere .=" OR ";
                        $joinonwhere .=" (bd.questionName = '".addslashes($name)."' AND bd.textValue LIKE '%".addslashes($valx)."%') ";
                    }
                $joinonwhere .=" ) ";
*/
//                if ($joinonleft) $joinonleft .=" AND ";
//                $joinonleft .=" (bd2.userId=uss.id AND (`bd2`.`questionName` = '".addslashes($name)."') AND bd2.textValue LIKE '%".addslashes($valx)."%')  ";
                
//SELECT uss.*,bd.textValue,bd.intValue,bd.questionName FROM ow_base_user uss INNER JOIN `ow_base_question_data` `bd` ON ( bd.userId=uss.id AND (`bd`.`questionName` = 'sex' AND `bd`.`intValue` & '1' ) ) 
//LEFT JOIN `ow_base_question_data` `bd2` ON ( bd2.userId=uss.id AND (`bd2`.`questionName` = '7fd3b96ec84474824e77b40b4a596b38') ) 
//WHERE 1 ORDER BY uss.joinIp DESC LIMIT 0,20

                $joinon .=" AND (`bd`.`questionName` = '".addslashes($name)."' AND `bd`.`intValue` & '".addslashes($valx)."' ) ";
//                $joinon .=" OR (`bd`.`questionName` = '".addslashes($name)."' AND `bd`.`intValue` & '".addslashes($valx)."' ) ";
//                $joinon .=" AND (`bd2`.`questionName` = '".addslashes($name)."' AND `bd2`.`intValue` & '".addslashes($valx)."' ) ";

            }
        }
    }

    if ($joinonleft){
        $joinonleft_all ="LEFT JOIN `" . OW_DB_PREFIX. "base_question_data` `bd2` ON (".$joinonleft.") ";
//        $joinonleft ="LEFT JOIN `" . OW_DB_PREFIX. "base_question_data` `bd2` ON (".$joinonleft.") ";
        $joinonleft ="LEFT JOIN `" . OW_DB_PREFIX. "base_user` `uss` ON (".$joinonleft.") ";

    }




//$joinonwhere ="1";
    if ($joinonwhere){
        if ($joinon){
            $joinon_select =",bd.textValue,bd.intValue,bd.questionName";
//        $joinon_select =",bd2.textValue,bd.intValue,bd2.questionName";
        }


//        $joinon="LEFT JOIN " . OW_DB_PREFIX. "base_question_data bd ON (bd.userId=uss.id AND bd.questionName = '".addslashes($name)."') ";
//        $joinon="INNER JOIN `" . OW_DB_PREFIX. "base_question_data` `bd` ON ( bd.userId=uss.id  AND `bd`.`questionName` = '".addslashes($name)."' AND `bd`.`intValue` & '16' ) ";
        if ($joinon) {
            $joinon_all="INNER JOIN `" . OW_DB_PREFIX. "base_question_data` `bd` ON ( bd.userId=uss.id  ".$joinon." ) ";
//            $joinon="INNER JOIN `" . OW_DB_PREFIX. "base_question_data` `bd` ON ( bd.userId=uss.id  ".$joinon." ) ";
            $joinon="INNER JOIN `" . OW_DB_PREFIX. "base_question_data` `bd` ON ( bd.userId=bd2.userId  ".$joinon." ) ";
        }
        $joinonwhere =" (".$joinonwhere.") ";
    }   
/*
    if (OW::getConfig()->getValue('search', 'search_force_users')==2){
        $add_query .=" AND (uadd.textValue LIKE '%".addslashes($query)."%' OR LOWER(uadd.textValue) LIKE '%".addslashes(strtolower($query))."%') ";    

    $add_users="";
    $add_users_lower="";
        $add_users .=" uss.username LIKE '%".addslashes($query)."%' OR  ";
        $add_users .=" uadd.textValue LIKE '%".addslashes($query)."%' OR ";
        $add_users_lower .=" LOWER(uss.username) LIKE '%".addslashes(strtolower($query))."%' OR  ";
        $add_users_lower .=" LOWER(uadd.textValue) LIKE '%".addslashes(strtolower($query))."%' ";
*/

    $add_query_search="";
    if (strlen($query)>1){
        if ($joinonwhere) $joinonwhere .=" AND ";
        if (OW::getConfig()->getValue('search', 'search_force_users')==2){
            $add_query_search .=" (uss.username LIKE '%".addslashes($query)."%' OR LOWER(uss.username) LIKE '%".addslashes(strtolower($query))."%') ";
        }else{
            $add_query_search .=" (uss.username LIKE '".addslashes($query)."%' OR LOWER(uss.username) LIKE '".addslashes(strtolower($query))."%') ";
        }
    }else if ($query=="*" AND !$joinonwhere){
        $add_query_search .=" 1 ";
    }


//SELECT uss.* ,bd3.textValue FROM ow_base_user uss 
//LEFT JOIN `ow_base_question_data` `bd3` ON ( (bd3.userId=uss.id AND (`bd3`.`questionName` = 'realname') AND (bd3.textValue LIKE 'las v%' OR LOWER(bd3.textValue) LIKE 'las v%')) ) 
//WHERE ( ( (uss.username LIKE 'las v%' OR LOWER(uss.username) LIKE 'las v%') ) OR (bd3.textValue LIKE 'las v%' OR LOWER(bd3.textValue) LIKE 'las v%')) GROUP BY uss.id ORDER BY uss.joinIp DESC LIMIT 10


    if (strlen($query)>1){
        if (!$option OR $option=="search" OR $option=="user"){
            if (!is_array($addsearch_sel)){//jesli nie mam szukania po question
                if (OW::getConfig()->getValue('search', 'search_force_users')==2){
                    $joinonleft2_if_all  =" (bd3.userId=uss.id AND (bd3.textValue LIKE '%".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '%".addslashes(strtolower($query))."%') ) ";
                    $joinonleft2_if  =" (bd3.userId=bd2.userId AND (bd3.textValue LIKE '%".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '%".addslashes(strtolower($query))."%') ) ";
                }else{
                    $joinonleft2_if_all  =" (bd3.userId=uss.id AND (bd3.textValue LIKE '".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($query))."%') ) ";
                    $joinonleft2_if  =" (bd3.userId=bd2.userId AND (bd3.textValue LIKE '".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($query))."%') ) ";
                }
            }else{
                $joinonleft2_if_all  =" (bd3.userId=uss.id AND (bd3.textValue LIKE '".addslashes($query)."' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($query))."') ) ";
                $joinonleft2_if  =" (bd3.userId=bd2.userId AND (bd3.textValue LIKE '".addslashes($query)."' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($query))."') ) ";
            }
        }else{
            if (OW::getConfig()->getValue('search', 'search_force_users')==2){
                $joinonleft2_if_all  =" (bd3.userId=uss.id AND (`bd3`.`questionName` = 'realname') AND (bd3.textValue LIKE '%".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '%".addslashes(strtolower($query))."%')) ";
                $joinonleft2_if  =" (bd3.userId=bd2.userId AND (`bd3`.`questionName` = 'realname') AND (bd3.textValue LIKE '%".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '%".addslashes(strtolower($query))."%')) ";
            }else{
                $joinonleft2_if_all  =" (bd3.userId=uss.id AND (`bd3`.`questionName` = 'realname') AND (bd3.textValue LIKE '".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($query))."%')) ";
                $joinonleft2_if  =" (bd3.userId=bd2.userId AND (`bd3`.`questionName` = 'realname') AND (bd3.textValue LIKE '".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($query))."%')) ";
            }
        }

        $joinonleft2_all  ="LEFT JOIN `" . OW_DB_PREFIX. "base_question_data` `bd3` ON (".$joinonleft2_if_all.") ";
        $joinonleft2  ="LEFT JOIN `" . OW_DB_PREFIX. "base_question_data` `bd3` ON (".$joinonleft2_if.") ";
//        if ($joinon OR $joinonleft) $joinonleft2 =" AND ".$joinonleft2;

        $joinon_select .=" ,bd3.textValue ";

//        if ($add_query_search) $add_query_search .=" AND ";
        if (OW::getConfig()->getValue('search', 'search_force_users')==2){
            $add_query_search ="( (".$add_query_search.") OR (bd3.textValue LIKE '%".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '%".addslashes(strtolower($query))."%'))";
        }else{
            $add_query_search ="( (".$add_query_search.") OR (bd3.textValue LIKE '".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($query))."%'))";
        }
        
    }


    if ($joinonleft2 AND !$joinonleft){
        $joinonleft ="LEFT JOIN `" . OW_DB_PREFIX. "base_user` `uss` ON (uss.id=bd2.userId) ";

    }

//echo "-----------".is_array($addsearch_sel)."---".isset($addsearch_sel);

/*
//OK
SELECT DISTINCT `user`.id, `user`.`activityStamp` FROM `ow_base_user` `user` 
INNER JOIN `ow_base_question_data` `qd0` ON ( `user`.`id` = `qd0`.`userId` AND `qd0`.`questionName` = 'relationship' AND `qd0`.`intValue` & '16' ) 
LEFT JOIN `ow_base_user_suspend` as `s` ON( `user`.`id` = `s`.`userId` ) 
LEFT JOIN `ow_base_user_disapprove` as `d` ON( `user`.`id` = `d`.`userId` ) 
WHERE `s`.`id` IS NULL AND `d`.`id` IS NULL ORDER BY `user`.`activityStamp` DESC 
*/

//}

//                $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_user WHERE username LIKE '".addslashes($query)."%' ORDER BY joinIp DESC LIMIT ".$limit;
/*
                        $sql = "SELECT uss.*,uadd.textValue FROM " . OW_DB_PREFIX. "base_user uss 
                        LEFT JOIN " . OW_DB_PREFIX. "base_question_data uadd ON (uadd.userId=uss.id AND uadd.questionName='realname') 
                        WHERE (uss.username LIKE '".addslashes($query)."%' OR uadd.textValue LIKE '".addslashes($query)."%' OR LOWER(uss.username) LIKE '".addslashes(strtolower($query))."%' OR LOWER(uadd.textValue) LIKE '".addslashes(strtolower($query))."%') ORDER BY uss.joinIp DESC LIMIT ".$limit;
*/
//                        LEFT JOIN " . OW_DB_PREFIX. "base_question_data uadd ON (uadd.userId=uss.id AND uadd.questionName='realname') 
//                        $sql = "SELECT uss.*".$joinon_select." FROM " . OW_DB_PREFIX. "base_user uss 
//                        ".$joinon." 
//                        WHERE ".$joinonwhere." (uss.username LIKE '".addslashes($query)."%' OR LOWER(uss.username) LIKE '".addslashes(strtolower($query))."%') ORDER BY uss.joinIp DESC LIMIT ".$limit;

//                        LEFT JOIN " . OW_DB_PREFIX. "base_question_data uadd ON (uadd.userId=uss.id AND uadd.questionName='realname') 




/*
                        $sql = "SELECT uss.*".$joinon_select." FROM " . OW_DB_PREFIX. "base_user uss 
                        ".$joinon." ".$joinonleft." ".$joinonleft2." 
                        WHERE ".$joinonwhere." ".$add_query_search." GROUP BY uss.id ORDER BY uss.joinIp DESC LIMIT ".$limit;
*/
$havein="";
if ($for_haveing>0){
    $havein=" HAVING count( bd2.id ) >=".$for_haveing." ";
}else{
    $havein="";
}
                        if(!$joinonleft){
                            $joinonleft ="LEFT JOIN `" . OW_DB_PREFIX. "base_user` `uss` ON (uss.id=bd2.userId) ";
                        }
                        $sql = "SELECT uss.* ".$joinon_select." FROM " . OW_DB_PREFIX. "base_question_data bd2 
                        ".$joinon." ".$joinonleft." ".$joinonleft2." 
                        WHERE ".$joinonwhere." ".$add_query_search." GROUP BY uss.id ".$havein." ORDER BY uss.joinIp DESC LIMIT ".$limit;




//echo $sql;
//                        WHERE ".$joinonwhere." (uss.username LIKE '".addslashes($query)."%' OR uadd.textValue LIKE '".addslashes($query)."%' OR LOWER(uss.username) LIKE '".addslashes(strtolower($query))."%' OR LOWER(uadd.textValue) LIKE '".addslashes(strtolower($query))."%') ORDER BY uss.joinIp DESC LIMIT ".$limit;

//echo $sql;

//echo BOL_QuestionService::getInstance()->getQuestionData(array(OW::getUser()->getUserObject()->getId()), array('realname'));

//print_r(BOL_QuestionService::getInstance()->getQuestionData(array(OW::getUser()->getUserObject()->getId()), array('relationship')));
//print_r(BOL_QuestionService::getInstance()->getQuestionData(array(OW::getUser()->getUserObject()->getId())));

//echo $sql;
            if ($joinonwhere OR $add_query_search){

//--all s
//                        if (!$add_query_search) $add_query_search=" 1 ";
                        if (!$add_query_search) $add_query_search=" ";
                        $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "base_user uss 
                        ".$joinon_all." ".$joinonleft_all." ".$joinonleft2_all." 
                        WHERE ".$joinonwhere." ".$add_query_search;
//echo "|".$add_query_search."|";
//echo "|".$sqlll."|";exit;
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr = OW::getDbo()->queryForList($sql);
                $tabt="";
                foreach ( $arr as $value )
                {
                    $dname=BOL_UserService::getInstance()->getDisplayName($value['id']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['id']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id']);
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"width:45px;\">";
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
//BOL_UserService::getInstance()->getDisplayName($userId)
//BOL_UserService::getInstance()->getUserUrl($userId)
                    $tabt .="</td>";
                    $tabt .="<td style=\"margin:auto;\">";
                    $tabt .="<a href=\"".$uurl."\" title=\"".$dname."\" style=\"display:inline;\">";
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".$dname."</h1>";
                    $tabt .="</a>";
//--adds
/*
                        $sqla = "SELECT * FROM " . OW_DB_PREFIX. "base_question_data bqd 
                            LEFT JOIN " . OW_DB_PREFIX. "base_question bq ON (bq.name=bqd.questionName) 
                            LEFT JOIN " . OW_DB_PREFIX. "base_question_value bqv ON (bqv.questionName=bqd.questionName) 
                        WHERE bq.onView='1' AND bqd.userId='".addslashes($value['id'])."'  ";
//echo $sqla;exit;
                        $arra = OW::getDbo()->queryForList($sqla);
                        $tt="";
                        foreach ( $arra as $valuea )
                        {
                            if ($valuea['name'] AND $valuea['value']){
                                $tt .="<i>".OW::getLanguage()->text('base', 'questions_question_'.$valuea['name'].'_label').":</i>";
                                $tt .=" <b>".OW::getLanguage()->text('base', 'questions_question_'.$valuea['name'].'_value_'.$valuea['value'])."</b>";
                                $tt .="; ";
                            }
                            
                        }

*/


                        $tt=SEARCH_BOL_Service::getInstance()->get_all_questions($value['id']);




                        if ($tt){
                            $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\">".$tt."</div>";
                        }
/*
                $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_question WHERE onSearch='1' ORDER BY sortOrder ";
                $arr1 = OW::getDbo()->queryForList($sql);
                $enter=1;
                $inline=3;
                foreach ( $arr1 as $value )
                {
//                    $add_opt .=$value['name']."--".$value['type']."-".$value['presentation']."<hr>";
                    if ($value['type']=="select"){
                        if ($add_opt) $add_opt .="&nbsp; ";
$add_opt .="<div class=\"clearfix  ow_boxx\" style=\"padding-right:5px;margin:auto;min-width:150px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
                        $add_opt .="<b>".OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
                        $add_opt .="<select name=\"search_sel[".$value['name']."]\">";

//                        if (!isset($_GET['search_sel'][$value['name']]) AND !$_GET['search_sel'][$value['name']]) $sel=" selected ";
                        if (!isset($_GET['search_sel'][$value['name']]) OR (isset($_GET['search_sel'][$value['name']]) AND !$_GET['search_sel'][$value['name']] ) ) $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"\">-- ".OW::getLanguage()->text('search', 'select')." --</option>";

                        $sql2 = "SELECT * FROM " . OW_DB_PREFIX. "base_question_value WHERE questionName='".$value['name']."' ORDER BY sortOrder ";
                        $arr2 = OW::getDbo()->queryForList($sql2);
                        foreach ( $arr2 as $value2 )
                        {
                            if (isset($_GET['search_sel'][$value['name']]) AND $value2['value']==$_GET['search_sel'][$value['name']]) $sel=" selected ";
                                else $sel=" ";
                            $add_opt .="<option ".$sel." value=\"".$value2['value']."\">".OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_value_'.$value2['value'])."</option>";

                        }
                        $add_opt .="</select>";
$add_opt .="    </div>
</div>";
*/


//--adde


                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }
                }//for

                if ($tabt) {
$global_found=true;
//echo $option;
                    if (!$option){
                        $tab .="<tr >";
                        $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
                        $tab .="&nbsp;";
                        $tab .="</td>";
                        $tab .="</tr>";

                        $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_box_cap_empty ow_box_cap_body\" style=\"border:1px solid #ddd;\" colspan=\"2\" >";
//                    $tab .="<td style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                        $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\">";
                        $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\">";
                        $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_user')."</b>";
                        $tab .="</td>";
                        $tab .="</tr>";

                        $tab .="<tr >";
                        $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tab .="&nbsp;";
                        $tab .="</td>";
                        $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }
//echo $prev_page;
//echo "--".$tabt ;
//echo $curent_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
        //        $paging=$this->pagination($curent_page=0,$next_page=0,$url_pages="")
//                    $paging=$this->pagination($curent_page,$all_results,$prev_page,($curent_page+1),"user",$add_paramurl);
//$paging="ssss".$all_results;
//$paging="ssss".$sqlll;
//$paging="ssss".$add_paramurl;
//&query=ar&search_text%5B9221d78a4201eac23c972e1d4aa2cee6%5D=&search_text%5Bc441a8a9b955647cdf4c81562d39068a%5D=&search_sel%5Bsex%5D=&search_sel%5Brelationship%5D=
//$paging="ssss".$curent_page."--".$all_results."--".$per_page;
//$page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "?page=",$position="right"
//if ($curent_page==0) $curent_page=1;
//$paging ="";
//$paging .="<hr>".$curent_page."--".$all_results."--".$per_page."<hr>";
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/user?".$add_paramurl,"&page=");
                }else{
//                    $paging=$this->pagination($curent_page,$all_results,$prev_page,0,"user",$add_paramurl);
                    $paging=SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/user?".$add_paramurl,"&page=");
                }
//echo $paging;
            }//if $joinonwhere." ".$add_query_search


            }


if (!isset($addsearch_sel)) $addsearch_sel="";
//        if (strlen($query)>1){
        if (strlen($query)>1 OR ($query=="*" AND !is_array($addsearch_sel) ) ){
//if ($query=="*") $query="";








            if ($plunin_installed['cms'] AND (!$option OR $option=="cms")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }
//echo "------------";

                

                if (strlen($query)>1){
//                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "forum_topic WHERE (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY viewCount DESC,lastPostId LIMIT ".$limit;
                    $sql = "SELECT bl.*,cn.content FROM " . OW_DB_PREFIX. "cms_blocks bl 
                        LEFT JOIN " . OW_DB_PREFIX. "cms_content cn ON (cn.id_block=bl.id_block) 
                        WHERE 
                        
                        (bl.position='center' OR bl.position='left' OR bl.position='right') AND 
                        (
                            (bl.name LIKE '%".addslashes($query)."%' OR LOWER(bl.name) LIKE '%".addslashes(strtolower($query))."%') 
                            OR
                            (cn.content LIKE '%".addslashes($query)."%' OR LOWER(cn.content) LIKE '%".addslashes($query)."%')
                        ) AND bl.active='1'  
                        ORDER BY bl.data_created DESC,bl.id_block DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) AS allp FROM " . OW_DB_PREFIX. "cms_blocks bl 
                        LEFT JOIN " . OW_DB_PREFIX. "cms_content cn ON (cn.id_block=bl.id_block) 
                        WHERE 
                        
                        (bl.position='center' OR bl.position='left' OR bl.position='right') AND 
                        (
                            (bl.name LIKE '%".addslashes($query)."%' OR LOWER(bl.name) LIKE '%".addslashes(strtolower($query))."%') 
                            OR
                            (cn.content LIKE '%".addslashes($query)."%' OR LOWER(cn.content) LIKE '%".addslashes($query)."%')
                        ) AND bl.active='1'  
                        ";
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "cms_blocks WHERE 1 ORDER BY data_created DESC,id_block DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "cms_blocks WHERE 1 ";
                }

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

//                $query = "SELECT * FROM " . OW_DB_PREFIX. "forum_topic WHERE title LIKE '%".addslashes($query)."%' LIMIT ".$limit;
//                $query = "SELECT * FROM " . OW_DB_PREFIX. "base_user WHERE username LIKE '".addslashes($query)."%' ORDER BY joinIp DESC LIMIT ".$limit;
                $arr2 = OW::getDbo()->queryForList($sql);
//echo $query;exit;
//print_r($arr2);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                        $tabt .="<td style=\"\" colspan=\"2\" >";

/*
                    $dname=BOL_UserService::getInstance()->getDisplayName($value['id']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['id']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id']);
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
*/


//                    $tabt .="<a href=\"".$curent_url."forum/topic/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
                                if ($value['id_page']>0){
                                    $tabt .="<a href=\"".$curent_url."pb/".$value['id_block']."/".$value['id_page']."/index.html\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                }else{
                                    $tabt .="<a href=\"".$curent_url."pg/".$value['id_block']."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                }
//                    $tabt .=stripslashes($value['name']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['name'])."</h1>";
//                    $tabf .=stripslashes($value['id']);
                    $tabt .="</a>";
                    if (isset($value['content'])) {
                        $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['content']));
                    }else{
                        $contentx="";
                    }
                    if ($contentx){
                        $contentx=mb_substr($contentx,0,200)."...";    
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\">".$contentx."</div>";

                    $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".$value['data_created']."</div>";





                    $tabt .="</td>";
                    $tabt .="</tr>";
/*
                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";
*/

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }
                }
//echo $query;
                if ($tabt) {
$global_found=true;
                 if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_cms')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
//echo "--".$curent_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"cms",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/cms?".$add_paramurl,"&page=");
                    if (!$paging) $paging=" ";
//$paging =$all_results;
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"cms",$add_paramurl);
//                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/cms?".$add_paramurl,"&page=");
                    $paging="";
                }
//echo $paging;

            }

//---

            if ($plunin_installed['forum'] AND (!$option OR $option=="forum")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }
//echo "------------";
                if (strlen($query)>1){
                    $sql = "SELECT ft.*,fp.userId as puserId, fp.text,fp.createStamp  FROM " . OW_DB_PREFIX. "forum_topic ft 
                        LEFT JOIN " . OW_DB_PREFIX. "forum_post fp ON (fp.id=ft.lastPostId) 
                    WHERE (ft.title LIKE '%".addslashes($query)."%' OR LOWER(ft.title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY ft.viewCount DESC,ft.lastPostId LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "forum_topic WHERE (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ";
                }else{
                    $sql = "SELECT ft.*,fp.userId as puserId, fp.text,fp.createStamp FROM " . OW_DB_PREFIX. "forum_topic ft 
                        LEFT JOIN " . OW_DB_PREFIX. "forum_post fp ON (fp.id=ft.lastPostId) 
                    WHERE 1 ORDER BY ft.viewCount DESC,ft.lastPostId LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "forum_topic WHERE 1 ";
                }
//                $query = "SELECT * FROM " . OW_DB_PREFIX. "forum_topic WHERE title LIKE '%".addslashes($query)."%' LIMIT ".$limit;
//                $query = "SELECT * FROM " . OW_DB_PREFIX. "base_user WHERE username LIKE '".addslashes($query)."%' ORDER BY joinIp DESC LIMIT ".$limit;
                $arr2 = OW::getDbo()->queryForList($sql);

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

//echo $query;exit;
//print_r($arr2);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                        $tabt .="<td style=\"\" colspan=\"2\" >";


                    $dname=BOL_UserService::getInstance()->getDisplayName($value['puserId']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['puserId']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['puserId']);
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }



                    $tabt .="<a href=\"".$curent_url."forum/topic/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
                    //$tabt .=stripslashes($value['title']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['title'])."</h1>";
//                    $tabf .=stripslashes($value['id']);
                    $tabt .="</a>";

                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['text']));
                    if ($contentx){
                        $contentx=mb_substr($contentx,0,200)."...";    
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\">".$contentx."</div>";

                    $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['createStamp'])."</div>";

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }
                }
//echo $query;
                if ($tabt) {
$global_found=true;
                 if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_forum')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"forum",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/forum?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"forum",$add_paramurl);
//                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/forum?".$add_paramurl,"&page=");
                    $paging ="";
                }
//echo $paging;

            }

            if ($plunin_installed['map'] AND (!$option OR $option=="map")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

                $adds ="";
                if (isset($_GET['search_addparam_category']) AND $_GET['search_addparam_category']>0){
                    $adds .=" AND (id_cat='".addslashes($_GET['search_addparam_category'])."') ";
                }

//echo "------------";
                if (strlen($query)>1){
                    $sql = "SELECT mm.*  FROM " . OW_DB_PREFIX. "map mm 
                        LEFT JOIN " . OW_DB_PREFIX. "map_images mmi ON (mmi.id_map=mm.id) 
                    WHERE (mm.active='1' AND mm.name LIKE '%".addslashes($query)."%' OR LOWER(mm.name) LIKE '%".addslashes(strtolower($query))."%') 
                    ".$adds." 
                    GROUP BY mm.id 
                    ORDER BY mm.data_addm LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "map WHERE active='1' AND (name LIKE '%".addslashes($query)."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%') ".$adds;
                }else{
                    $sql = "SELECT mm.*  FROM " . OW_DB_PREFIX. "map mm 
                        LEFT JOIN " . OW_DB_PREFIX. "map_images mmi ON (mmi.id_map=mm.id) 
                    WHERE (mm.active='1') 
                    ".$adds." 
                    GROUP BY mm.id 
                    ORDER BY mm.data_addm LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "map WHERE active='1' ".$adds;

                }
//                $query = "SELECT * FROM " . OW_DB_PREFIX. "forum_topic WHERE title LIKE '%".addslashes($query)."%' LIMIT ".$limit;
//                $query = "SELECT * FROM " . OW_DB_PREFIX. "base_user WHERE username LIKE '".addslashes($query)."%' ORDER BY joinIp DESC LIMIT ".$limit;
                $arr2 = OW::getDbo()->queryForList($sql);

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

//echo $query;exit;
//print_r($arr2);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                        $tabt .="<td style=\"\" colspan=\"2\" >";


                    $dname=BOL_UserService::getInstance()->getDisplayName($value['id_owner']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_owner']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_owner']);
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }



                    $tabt .="<a href=\"".$curent_url."map/zoom/".$value['id']."\" title=\"".stripslashes($value['name'])."\" >";
                    //$tabt .=stripslashes($value['title']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['name'])."</h1>";
//                    $tabf .=stripslashes($value['id']);
                    $tabt .="</a>";

                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['desc']));
                    if ($contentx){
                        $contentx=mb_substr($contentx,0,200)."...";    
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\">".$contentx."</div>";

                    if ($value['data_addm']!="0000-00-00 00:00:00" AND $value['data_addm']!="" AND $value['data_addm']!=0){
//                        $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['data_addm'])."-".$value['data_addm']."-</div>";
                        $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".$value['data_addm']."</div>";
                    }

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }
                }
//echo $query;
                if ($tabt) {
$global_found=true;
                 if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_map')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"map",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/map?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"forum",$add_paramurl);
//                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/forum?".$add_paramurl,"&page=");
                    $paging ="";
                }
//echo $paging;

            }


            if ($plunin_installed['news'] AND (!$option OR $option=="news")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

                $adds ="";
                if (isset($_GET['search_addparam_category']) AND $_GET['search_addparam_category']>0){
                    $adds .=" AND (id_topic='".addslashes($_GET['search_addparam_category'])."') ";
                }

//echo "------------";
                if (strlen($query)>1){
                    $sql = "SELECT *  FROM " . OW_DB_PREFIX. "news mm 
                        LEFT JOIN " . OW_DB_PREFIX. "news_content mmi ON (mmi.id_news=mm.id) 
                    WHERE (mm.active='1' AND mm.is_published='1'  AND mm.topic_name LIKE '%".addslashes($query)."%' OR LOWER(mm.topic_name) LIKE '%".addslashes(strtolower($query))."%') 
                    ".$adds." 
                    ORDER BY mm.data_added DESC LIMIT ".$limit;

                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "news 
                    WHERE active='1' AND is_published='1' AND (topic_name LIKE '%".addslashes($query)."%' OR LOWER(topic_name) LIKE '%".addslashes(strtolower($query))."%') ".$adds;
                }else{
                    $sql = "SELECT *  FROM " . OW_DB_PREFIX. "news mm 
                        LEFT JOIN " . OW_DB_PREFIX. "news_content mmi ON (mmi.id_news=mm.id) 
                    WHERE (mm.active='1' AND mm.is_published='1' ) 
                    ".$adds." 
                    ORDER BY mm.data_added DESC LIMIT ".$limit;

                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "news WHERE active='1' AND is_published='1' ".$adds;
                }
                $arr2 = OW::getDbo()->queryForList($sql);

//--all s
                $arrll = OW::getDbo()->queryForList($sqlll);
                if (isset($arrll['0'])){
                    $all_results=$arrll['0']['allp'];
                }else{
                    $all_results=0;
                }
//--all e

//echo $query;exit;
//print_r($arr2);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                        $tabt .="<td style=\"\" colspan=\"2\" >";


                    $dname=BOL_UserService::getInstance()->getDisplayName($value['id_sender']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_sender']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_sender']);
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }



                    $tabt .="<a href=\"".$curent_url."news/".$value['id']."/index.html\" title=\"".stripslashes($value['topic_name'])."\" >";
                    //$tabt .=stripslashes($value['title']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['topic_name'])."</h1>";
//                    $tabf .=stripslashes($value['id']);
                    $tabt .="</a>";

                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['content']));
                    if ($contentx){
                        $contentx=mb_substr($contentx,0,200)."...";    
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\">".$contentx."</div>";

                    if ($value['data_added']!="0000-00-00 00:00:00" AND $value['data_added']!="" AND $value['data_added']!=0){
//                        $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['data_addm'])."-".$value['data_addm']."-</div>";
                        $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['data_added'])."</div>";
                    }

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }
                }
//echo $query;
                if ($tabt) {
$global_found=true;
                 if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_news')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"map",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/news?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"forum",$add_paramurl);
//                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/forum?".$add_paramurl,"&page=");
                    $paging ="";
                }
//echo $paging;

            }




            if ($plunin_installed['links'] AND (!$option OR $option=="links")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

//echo "------------";
                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "links_link WHERE (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') 
                    AND privacy='everybody' 
                    ORDER BY timestamp DESC LIMIT ".$limit;

                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "links_link WHERE (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') 
                    AND privacy='everybody' ";
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "links_link 
                    WHERE privacy='everybody' 
                    ORDER BY timestamp DESC LIMIT ".$limit;

                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "links_link 
                    WHERE privacy='everybody'  ";
                }

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
//echo $query;exit;
//print_r($arr2);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
/*
                    $tabt .="<td style=\"width:100px;\" >";
                    $uimg="";

                    if ($uimg){
                        $tabt .="<a href=\"".$curent_url."link/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
                        $tabt .="<img src=\"".$uimg."\" title=\"".stripslashes($value['title'])."\" width=\"100px\">";
                        $tabt .="</a>";
                    }else{
                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                    }

                    $tabt .="";
                    $tabt .="</td>";                    
*/
                    $tabt .="<td style=\"\" colspan=\"2\">";

                    $dname=BOL_UserService::getInstance()->getDisplayName($value['userId']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['userId']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['userId']);
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }

                    $tabt .="<a href=\"".$curent_url."link/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
//                    $tabt .=stripslashes($value['title']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['title'])."</h1>";
//                    $tabf .=stripslashes($value['id']);
                    $tabt .="</a>";

                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['description']));
                    if ($contentx){
                        $contentx=mb_substr($contentx,0,200)."...";    
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\">".$contentx."</div>";

                    $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['timestamp'])."</div>";

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }
                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_links')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;

                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"links",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/links?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"links",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }


            if ($plunin_installed['video'] AND (!$option OR $option=="video")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

//echo "------------";
                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "video_clip WHERE (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') 
                    AND status='approved' AND privacy='everybody' 
                    ORDER BY addDatetime DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "video_clip WHERE (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') 
                    AND status='approved' AND privacy='everybody' ";
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "video_clip WHERE status='approved' AND privacy='everybody'  ORDER BY addDatetime DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "video_clip WHERE status='approved' AND privacy='everybody'  ";
                }

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
//echo $query;exit;
//print_r($arr2);
                $tabt="";
                foreach ( $arr2 as $value )
                {


                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"width:100px;\" >";



                    $uimg="";
                    $unique_id="";
//$unique_id="3xsdFmTxOAs";
//http://www.youtube.com/embed/3xsdFmTxOAs"
                    preg_match_all('/(youtube.com\/embed\/)([a-z0-9\-_]+)/i',stripslashes($value['code']),$matches);
//print_r($matches);exit;
//Array ( [0] => Array ( [0] => youtube.com/embed/UClQ2QgE27w ) [1] => Array ( [0] => youtube.com/embed/ ) [2] => Array ( [0] => UClQ2QgE27w ) )
                    if(isset($matches[2]) AND isset($matches[2][0])){
                        $unique_id=$matches[2][0];
                    }else{
                        $unique_id="";
                    }
            

                    if ($unique_id){
                        $tabt .="<a href=\"".$curent_url."video/view/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
//                        $tabt .="<img src=\"".$uimg."\" title=\"".stripslashes($value['title'])."\" width=\"100px\">";
                        $tabt .="<img src=\"http://img.youtube.com/vi/".$unique_id."/default.jpg\" />";
                        $tabt .="</a>";
                    }else{
                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                    }
                    $tabt .="</td>";                    
                    $tabt .="<td style=\"\" >";

                    $dname=BOL_UserService::getInstance()->getDisplayName($value['userId']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['userId']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['userId']);
                    $tabt .="<div class=\"clearfic ow_right\">";
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
                    $tabt .="</div>";

                    $tabt .="<a href=\"".$curent_url."video/view/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
//                    $tabt .=stripslashes($value['title']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['title'])."</h1>";
//                    $tabf .=stripslashes($value['id']);
                    $tabt .="</a>";

                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['description']));
                    if ($contentx){
                        $contentx=mb_substr($contentx,0,200)."...";
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\">".$contentx."</div>";

                    $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['addDatetime'])."</div>";

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }
                }
//echo $query;
                if ($tabt) {
$global_found=true;
                 if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_video')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"video",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/video?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"video",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }


            if ($plunin_installed['photo'] AND (!$option OR $option=="photo")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

//echo "------------";
                if (strlen($query)>1){
                    $sql = "SELECT ph.*,al.userId,al.name FROM " . OW_DB_PREFIX. "photo ph 
                    LEFT JOIN " . OW_DB_PREFIX. "photo_album al ON (al.id=ph.albumId) 
                    WHERE (ph.description LIKE '%".addslashes($query)."%' OR LOWER(ph.description) LIKE '%".addslashes(strtolower($query))."%') AND ph.status='approved' AND ph.privacy='everybody' 
                    ORDER BY ph.addDatetime DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "photo WHERE (description LIKE '%".addslashes($query)."%' OR LOWER(description) LIKE '%".addslashes(strtolower($query))."%') AND status='approved' AND privacy='everybody'";
                }else{
                    $sql = "SELECT ph.*,al.userId,al.name FROM " . OW_DB_PREFIX. "photo ph 
                    LEFT JOIN " . OW_DB_PREFIX. "photo_album al ON (al.id=ph.albumId) 
                    WHERE ph.status='approved' AND ph.privacy='everybody' ORDER BY ph.addDatetime DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "photo WHERE status='approved' AND privacy='everybody' ";
                }

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
//echo $query;exit;
//print_r($arr2);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"width:100px;\" >";
                        $uimg_loc="./ow_userfiles/plugins/photo/photo_preview_".$value['id'].".jpg";
                        $uimg=$curent_url."ow_userfiles/plugins/photo/photo_preview_".$value['id'].".jpg";
                    if (is_file($uimg_loc)){
                        $tabt .="<a href=\"".$curent_url."photo/view/".$value['id']."\" >";
                        $tabt .="<img src=\"".$uimg."\" title=\"".stripslashes($value['description'])."\" width=\"100px\">";
                        $tabt .="</a>";
                    }else{
                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                    }
                    $tabt .="</td>";                    
                    $tabt .="<td style=\"\" >";

                    $dname=BOL_UserService::getInstance()->getDisplayName($value['userId']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['userId']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['userId']);
                    $tabt .="<div class=\"clearfic ow_right\">";
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
                    $tabt .="</div>";

                    $tabt .="<a href=\"".$curent_url."photo/view/".$value['id']."\" title=\"".stripslashes($value['description'])."\" >";
//                    $tabt .=stripslashes($value['description']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['description'])."</h1>";
                    $tabt .="</a>";

//                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['description']));
                    $contentx=stripslashes($value['name']);
                    if ($contentx){
//                        $contentx=mb_substr($contentx,0,200)."...";    
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\"><i>".$contentx."</i></div>";


                    $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['addDatetime'])."</div>";

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }
                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;border-right:2px solid #aaa;margin:auto;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
//                    $tab .="<div style=\"margin:10px;\">";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_photo')."</b>";
//                    $tab .="</div>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"photo",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/photo?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"photo",$add_paramurl);
                    $paging ="";
                }
//echo $paging;exit;

            }

            if ($plunin_installed['shoppro'] AND (!$option OR $option=="shoppro")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }
$adds ="";
if (isset($_GET['search_addparam_location']) AND $_GET['search_addparam_location']){
    $adds .=" AND (location LIKE '%".addslashes($_GET['search_addparam_location'])."%' OR LOWER(location) LIKE '%".addslashes(strtolower($_GET['search_addparam_location']))."%') ";
}
if (isset($_GET['search_addparam_condition']) AND ($_GET['search_addparam_condition']=="1" OR $_GET['search_addparam_condition']=="2") AND $_GET['search_addparam_condition']){
    $adds .=" AND (`condition`='".addslashes($_GET['search_addparam_condition'])."') ";
}
if (isset($_GET['search_addparam_pricef']) AND isset($_GET['search_addparam_pricet']) AND $_GET['search_addparam_pricef'] AND $_GET['search_addparam_pricet']){
    $_GET['search_addparam_pricef']=str_replace(",",".",$_GET['search_addparam_pricef']);
    $_GET['search_addparam_pricet']=str_replace(",",".",$_GET['search_addparam_pricet']);
    $adds .=" AND (price>='".addslashes($_GET['search_addparam_pricef'])."' AND price<='".addslashes($_GET['search_addparam_pricet'])."') ";
}else if (isset($_GET['search_addparam_pricef']) AND $_GET['search_addparam_pricef']){
    $_GET['search_addparam_pricef']=str_replace(",",".",$_GET['search_addparam_pricef']);
    $adds .=" AND (price>='".addslashes($_GET['search_addparam_pricef'])."') ";
}else if (isset($_GET['search_addparam_pricet']) AND $_GET['search_addparam_pricet']){
    $_GET['search_addparam_pricet']=str_replace(",",".",$_GET['search_addparam_pricet']);
    $adds .=" AND (price<='".addslashes($_GET['search_addparam_pricet'])."') ";
}
if (isset($_GET['search_addparam_typeads']) AND ($_GET['search_addparam_typeads'] OR $_GET['search_addparam_typeads']=="0")){
    $adds .=" AND (type_ads='".addslashes($_GET['search_addparam_typeads'])."') ";
}
//echo "------------";
//                $sql = "SELECT * FROM " . OW_DB_PREFIX. "shoppro_products WHERE active='1' AND items>'0' AND (name LIKE '%".addslashes($query)."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%') ORDER BY price LIMIT ".$limit;
                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "shoppro_products WHERE active='1' AND (name LIKE '%".addslashes($query)."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%') ".$adds." ORDER BY price LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "shoppro_products WHERE active='1' AND (name LIKE '%".addslashes($query)."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%') ".$adds;
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "shoppro_products WHERE active='1' ".$adds." ORDER BY price LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "shoppro_products WHERE active='1' ".$adds;
                }
//echo $sql;
//echo $sqlll;exit;
//exit;
//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
//echo $sql;exit;
//print_r($arr2);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"width:100px;\" >";
//                        $uimg_loc="./ow_userfiles/plugins/photo/photo_preview_".$value['id'].".jpg";
//                        $uimg="/ow_userfiles/plugins/photo/photo_preview_".$value['id'].".jpg";
//http://test.a6.pl/ow_userfiles/plugins/shoppro/images/product_4.jpg
                        $uimg_loc="./ow_userfiles/plugins/shoppro/images/product_".$value['id'].".jpg";
                        $uimg=$curent_url."ow_userfiles/plugins/shoppro/images/product_".$value['id'].".jpg";
                    if (is_file($uimg_loc)){
//                        $tabt .="<a href=\"".$curent_url."photo/view/".$value['id']."\" >";
                        $tabt .="<a href=\"".$curent_url."product/".$value['id']."/zoom/index.html\" >";
                        $tabt .="<img src=\"".$uimg."\" title=\"".stripslashes($value['name'])."\" width=\"100px\">";
                        $tabt .="</a>";
                    }else{
                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                    }
                    $tabt .="</td>";                    
                    $tabt .="<td style=\"\" >";

$pidow=$value['id_owner'];
$avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($pidow));
$avatar = $avatars[$pidow];

$tabt .="<div class=\"clearfix ow_right\">";

$tabt .="<div class=\"ow_my_avatar_widget clearfix\">
            <div class=\"ow_my_avatar_cont ow_center\">
                <span>".OW::getLanguage()->text('search', 'shop_seller').":</span>
            </div>
            <div class=\"ow_center ow_my_avatar_img\">
                <div class=\"ow_avatar\">
                    <a href=\"".$avatar['url']."\"><img alt=\"\" src=\"".$avatar['src']."\" style=\"max-width: 100%;\"></a>
                </div>
            </div>
            <div class=\"ow_my_avatar_cont ow_center\">
                <a href=\"".$avatar['url']."\" class=\"ow_my_avatar_username\"><span>".$avatar['title']."</span></a>
            </div>
        </div>";
$tabt .="</div>";

//        $tabt .="<div class=\"clearfix ow_left ow_box\" style=\"width:100%;margin:auto;\">";
//        $tabt .="<div class=\"clearfix ow_box ow_ipc_header\" style=\"margin:auto;\">";
//        $tabt .="<h3 class=\"clearfix ow_box ow_ipc_header\">";
//        $tabt .="<h3 class=\"clearfix ow_box \">";
//        $tabt .="<h3 class=\"clearfix \" style=\"margin-bottom:10px;\">";
//                    $tabt .="<a href=\"".$curent_url."photo/view/".$value['id']."\" title=\"".stripslashes($value['description'])."\" >";
                    $tabt .="<a href=\"".$curent_url."product/".$value['id']."/zoom/index.html\" title=\"".stripslashes($value['name'])."\" >";
//                    $tabt .=stripslashes($value['name']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['name'])."</h1>";
                    $tabt .="</a>";
//                    $tabt .="<hr/>";
//        $tabt .="</div>";
//        $tabt .="</h3>";





//$tabt .="<div class=\"clearfix ow_left\">";
if ($value['price']>0){
//        $tabt .="<div class=\"clearfix ow_left\">";
        $tabt .="<div class=\"\">";
        $tabt .="<i>".OW::getLanguage()->text('search', 'index_price')."</i>: <b>".number_format($value['price'],2, ',', ' ')."</b> ".$value['curency'];
        $tabt .="</div>";
}

if ($value['type_ads'] OR $value['type_ads']=="0"){//0,1,2
//        $tabt .="<div class=\"clearfix ow_left\">";
        $tabt .="<div class=\"\">";
        if ($value['type_ads']=="0"){
            $tabt .="<i>".OW::getLanguage()->text('search', 'index_adstype')."</i>: ".OW::getLanguage()->text('search', 'typeads_classifieds');
        }else if ($value['type_ads']=="1"){
            $tabt .="<i>".OW::getLanguage()->text('search', 'index_adstype')."</i>: ".OW::getLanguage()->text('search', 'typeads_paybycredit');
        }else if ($value['type_ads']=="2"){
            $tabt .="<i>".OW::getLanguage()->text('search', 'index_adstype')."</i>: ".OW::getLanguage()->text('search', 'typeads_shopmode');
        }
    
        $tabt .="</div>";
}
if ($value['condition']>0){//0,1
//    $tabt .="<div class=\"clearfix ow_left\">";
        $tabt .="<div class=\"\">";
    if ($value['condition']==1){
        $tabt .="<i>".OW::getLanguage()->text('search', 'index_condition')."</i>: ".OW::getLanguage()->text('search', 'index_new');
    }else if ($value['condition']==2){
        $tabt .="<i>".OW::getLanguage()->text('search', 'index_condition')."</i>: ".OW::getLanguage()->text('search', 'index_used');
    }
    $tabt .="</div>";
}
if ($value['location']){
//    $tabt .="<div class=\"clearfix ow_left\">";
        $tabt .="<div class=\"\">";
    $tabt .="<i>".OW::getLanguage()->text('search', 'index_location')."</i>: ".stripslashes($value['location']);
    $tabt .="</div>";
}
if ($value['classifieds_type']>0){//0,1,2
//    $tabt .="<div class=\"clearfix ow_left\">";
        $tabt .="<div class=\"\">";
    if ($value['classifieds_type']==1){
        $tabt .="<i>".OW::getLanguage()->text('search', 'index_classifiedtype')."</i>: ".OW::getLanguage()->text('search', 'index_avaiable');
    }else if ($value['classifieds_type']==2){
        $tabt .="<i>".OW::getLanguage()->text('search', 'index_classifiedtype')."</i>: ".OW::getLanguage()->text('search', 'index_wanted');
    }
    $tabt .="</div>";
}
//$tabt .="</div>";

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;border-right:2px solid #aaa;margin:auto;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
//                    $tab .="<div style=\"margin:10px;\">";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_shoppro')."</b>";
//                    $tab .="</div>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"shoppro",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/shoppro?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"shoppro",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }

            if ($plunin_installed['classifiedspro'] AND (!$option OR $option=="classifiedspro")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

//echo "------------";
                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "classifiedspro_products WHERE active='1' AND (name LIKE '%".addslashes($query)."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%') ORDER BY price LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "classifiedspro_products WHERE active='1' AND (name LIKE '%".addslashes($query)."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%') ";
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "classifiedspro_products WHERE active='1' ORDER BY price LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "classifiedspro_products WHERE active='1' ";
                }

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
//echo $query;exit;
//print_r($arr2);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"width:100px;\" >";
//                        $uimg_loc="./ow_userfiles/plugins/photo/photo_preview_".$value['id'].".jpg";
//                        $uimg="/ow_userfiles/plugins/photo/photo_preview_".$value['id'].".jpg";
//http://test.a6.pl/ow_userfiles/plugins/shoppro/images/product_4.jpg
                        $uimg_loc="./ow_userfiles/plugins/classifiedspro/images/product_".$value['id'].".jpg";
                        $uimg=$curent_url."ow_userfiles/plugins/classifiedspro/images/product_".$value['id'].".jpg";
                    if (is_file($uimg_loc)){
//                        $tabt .="<a href=\"".$curent_url."photo/view/".$value['id']."\" >";
                        $tabt .="<a href=\"".$curent_url."classifieds/".$value['id']."/zoom/index.html\" >";
                        $tabt .="<img src=\"".$uimg."\" title=\"".stripslashes($value['name'])."\" width=\"100px\">";
                        $tabt .="</a>";
                    }else{
                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                    }
                    $tabt .="</td>";                    
                    $tabt .="<td style=\"\" >";
//                    $tabt .="<a href=\"".$curent_url."photo/view/".$value['id']."\" title=\"".stripslashes($value['description'])."\" >";
                    $tabt .="<a href=\"".$curent_url."classifieds/".$value['id']."/zoom/index.html\" title=\"".stripslashes($value['name'])."\" >";
                    $tabt .=stripslashes($value['name']);
                    $tabt .="</a>";
                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;border-right:2px solid #aaa;margin:auto;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
//                    $tab .="<div style=\"margin:10px;\">";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_classifiedspro')."</b>";
//                    $tab .="</div>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"classifiedspro",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/classifiedspro?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"classifiedspro",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }


            if ($plunin_installed['pages'] AND (!$option OR $option=="pages")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

//echo "------------";
                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "pages WHERE active='1' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "pages WHERE active='1' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ";
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "pages WHERE active='1' LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "pages WHERE active='1' ";
                }

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
//echo $query;exit;
//print_r($arr2);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"\" colspan=\"2\">";
                    $tabt .="<a href=\"".$curent_url."page/".$value['id']."/index.html\" title=\"".stripslashes($value['title'])."\" >";
//                    $tabt .=stripslashes($value['title']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['title'])."</h1>";
                    $tabt .="</a>";

                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['content']));
//                    $contentx=stripslashes($value['name']);
                    if ($contentx){
                       $contentx=mb_substr($contentx,0,200)."...";
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\"><i>".$contentx."</i></div>";


///                    $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['addDatetime'])."</div>";

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;border-right:2px solid #aaa;margin:auto;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
//                    $tab .="<div style=\"margin:10px;\">";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_pages')."</b>";
//                    $tab .="</div>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"pages",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/classifiedspro?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"pages",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }


            if ($plunin_installed['groups'] AND (!$option OR $option=="groups")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }
            
                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "groups_group WHERE whoCanView='anyone' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') 
                    ORDER BY timeStamp DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "groups_group WHERE whoCanView='anyone' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ";
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "groups_group WHERE whoCanView='anyone' ORDER BY timeStamp DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "groups_group WHERE whoCanView='anyone' ";
                }

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"\" colspan=\"2\">";

                    $dname=BOL_UserService::getInstance()->getDisplayName($value['userId']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['userId']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['userId']);
                    $tabt .="<div class=\"clearfic ow_rightx\">";
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
                    $tabt .="</div>";

                    $tabt .="<a href=\"".$curent_url."groups/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
//                    $tabt .=stripslashes($value['title']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['title'])."</h1>";
                    $tabt .="</a>";

                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['description']));
//                    $contentx=stripslashes($value['name']);
                    if ($contentx){
                       $contentx=mb_substr($contentx,0,200)."...";
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\"><i>".$contentx."</i></div>";

                    $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['timeStamp'])."</div>";


                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_groups')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"groups",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/groups?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"groups",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }



            if ($plunin_installed['blogs'] AND (!$option OR $option=="blogs")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "blogs_post WHERE privacy='everybody' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY timestamp DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "blogs_post WHERE privacy='everybody' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ";
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "blogs_post WHERE privacy='everybody' ORDER BY timestamp DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "blogs_post WHERE privacy='everybody' ";
                }

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"\" colspan=\"2\">";

                    $dname=BOL_UserService::getInstance()->getDisplayName($value['authorId']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['authorId']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['authorId']);
                    $tabt .="<div class=\"clearfic ow_rightx\">";
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
                    $tabt .="</div>";

//                    $tabt .="<a href=\"".$curent_url."blogs/post/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
                    $tabt .="<a href=\"".$curent_url."blogs/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
//                    $tabt .=stripslashes($value['title']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['title'])."</h1>";
                    $tabt .="</a>";

                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['post']));
//                    $contentx=stripslashes($value['name']);
                    if ($contentx){
                       $contentx=mb_substr($contentx,0,200)."...";    
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\"><i>".$contentx."</i></div>";

                    $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['timestamp'])."</div>";

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_blogs')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"blogs",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/blogs?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"blogs",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }

            if ($plunin_installed['event'] AND (!$option OR $option=="event")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

//                $timestamp=strtotime(date('Y-m-d H:i:s'));
//                $sql = "SELECT * FROM " . OW_DB_PREFIX. "blogs_post WHERE privacy='everybody' AND title LIKE '%".addslashes($query)."%' ORDER BY timestamp DESC LIMIT ".$limit;
//                $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND startTimeStamp<'".addslashes($timestamp)."' AND endTimeStamp>'".addslashes($timestamp)."' AND title LIKE '%".addslashes($query)."%' ORDER BY startTimeStamp DESC LIMIT ".$limit;

//                            $timestamp_start=strtotime(date('Y-m-d H:i:s'))-100;
//                            $timestamp_end=$timestamp_start+100;                            
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND startTimeStamp<'".addslashes($timestamp_start)."' AND (endDateFlag='0' OR (endDateFlag='1' AND endTimeStamp>'".addslashes($timestamp_end)."')) AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY startTimeStamp DESC LIMIT ".$limit;
                    if (strlen($query)>1){
                        $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY startTimeStamp DESC LIMIT ".$limit;
                        $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ";
                    }else{
                        $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' ORDER BY startTimeStamp DESC LIMIT ".$limit;
                        $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "event_item WHERE status='1' ";
                    }
//echo $sql;

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"\" colspan=\"2\">";

                    $dname=BOL_UserService::getInstance()->getDisplayName($value['userId']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['userId']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['userId']);
                    $tabt .="<div class=\"clearfic ow_rightx\">";
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
                    $tabt .="</div>";

                    $tabt .="<a href=\"".$curent_url."event/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
//                    $tabt .=stripslashes($value['title']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['title'])."</h1>";
                    $tabt .="</a>";


                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['description']));
//                    $contentx=stripslashes($value['name']);
                    if ($contentx){
                       $contentx=mb_substr($contentx,0,200)."...";    
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\"><i>".$contentx."</i></div>";

                    if ($value['startTimeStamp']!=$value['endTimeStamp']){
                        $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['startTimeStamp'])."-".date("Y-m-d H:i:s",$value['endTimeStamp'])."</div>";
                    }else{
                        $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['startTimeStamp'])."</div>";
                    }

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_event')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"event",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/event?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"event",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }



            if ($plunin_installed['fanpage'] AND (!$option OR $option=="fanpage")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

                    $adds="";
                    if (isset($_GET['search_addparam_city']) AND $_GET['search_addparam_city']){
                        $adds .=" AND (a_city LIKE '".addslashes($_GET['search_addparam_city'])."%' OR LOWER(a_city) LIKE '".addslashes($_GET['search_addparam_city'])."%') ";
                    }
                    if (isset($_GET['search_addparam_street']) AND $_GET['search_addparam_street']){
                        $adds .=" AND (a_street LIKE '".addslashes($_GET['search_addparam_street'])."%' OR LOWER(a_street) LIKE '".addslashes($_GET['search_addparam_street'])."%') ";
                    }
                    if (isset($_GET['search_addparam_country']) AND $_GET['search_addparam_country']){
                        $adds .=" AND (a_country LIKE '".addslashes($_GET['search_addparam_country'])."%' OR LOWER(a_country) LIKE '".addslashes($_GET['search_addparam_country'])."%') ";
                    }
                    if (isset($_GET['search_addparam_category']) AND $_GET['search_addparam_category']>0){
                        $adds .=" AND (id_category='".addslashes($_GET['search_addparam_category'])."' OR id_category2='".addslashes($_GET['search_addparam_category'])."' OR id_caregory2='".addslashes($_GET['search_addparam_category'])."') ";
                    }
//search_addparam_location

//                $timestamp=strtotime(date('Y-m-d H:i:s'));
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND startTimeStamp<'".addslashes($timestamp_start)."' AND (endDateFlag='0' OR (endDateFlag='1' AND endTimeStamp>'".addslashes($timestamp_end)."')) AND title LIKE '%".addslashes($query)."%' ORDER BY startTimeStamp DESC LIMIT ".$limit;
//                $sql = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (tags LIKE '%".addslashes($query)."%' OR title_fan_page LIKE '%".addslashes($query)."%' OR fanpage_url_name LIKE '%".addslashes($query)."%') ORDER BY promotion_is_vip DESC, sortt DESC LIMIT ".$limit;

                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (title_fan_page LIKE '%".addslashes($query)."%' OR fanpage_url_name LIKE '%".addslashes($query)."%' OR LOWER(title_fan_page) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(fanpage_url_name) LIKE '%".addslashes(strtolower($query))."%') ".$adds." ORDER BY promotion_is_vip DESC, sortt DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (title_fan_page LIKE '%".addslashes($query)."%' OR fanpage_url_name LIKE '%".addslashes($query)."%' OR LOWER(title_fan_page) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(fanpage_url_name) LIKE '%".addslashes(strtolower($query))."%') ".$adds;
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' ".$adds." ORDER BY promotion_is_vip DESC, sortt DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' ".$adds;
                }
//                $sql = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND (title_fan_page LIKE '%".addslashes($query)."%' OR fanpage_url_name LIKE '%".addslashes($query)."%' OR LOWER(title_fan_page) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(fanpage_url_name) LIKE '%".addslashes(strtolower($query))."%') ORDER BY promotion_is_vip DESC, sortt DESC LIMIT ".$limit;
//echo $sql;

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
                $tabt="";
                foreach ( $arr2 as $value )
                {

                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"width:100px;\" >";

                    $dname=BOL_UserService::getInstance()->getDisplayName($value['id']);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['id']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id']);
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
                    $tabt .="</td>";                    

//                    $tabt .="<td style=\"\" colspan=\"2\">";
                    $tabt .="<td style=\"\" >";
                    $tabt .="<a href=\"".$curent_url."fanpage/".$value['fanpage_url_name']."\" title=\"".stripslashes($value['title_fan_page'])."\" >";
//                    $tabt .=stripslashes($value['title_fan_page']);
//                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['title_fan_page'])."</h1>";
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;overflow: hidden;white-space: nowrap;max-width: 435px;\">".stripslashes($value['title_fan_page'])."</h1>";
                    $tabt .="</a>";

//                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['description']));
//                    $contentx=stripslashes($value['name']);
//                    if ($contentx){
//                       $contentx=mb_substr($contentx,0,200)."...";    
//                    }
//                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\"><i>".$contentx."</i></div>";


                    $content_contact="";
//                    if ($value['a_city)
            if (isset($value['a_city']) AND $value['a_city']){
                $content_contact .="<b>".OW::getLanguage()->text('search', 'city').":</b> ";
                if (isset($value['a_postcode']) AND $value['a_postcode']){
                    $content_contact .=stripslashes($value['a_postcode'])." ";
                }
                $content_contact .=stripslashes($value['a_city']);
                $content_contact .=";<br/>";
            }
            if (isset($value['a_street']) AND $value['a_street']){
                $content_contact .="<b>".OW::getLanguage()->text('search', 'street').":</b> ";
                $content_contact .=stripslashes($value['a_street']);
                $content_contact .=";<br/>";
            }
            if (isset($value['a_phone']) AND $value['a_phone']){
                $content_contact .="<b>".OW::getLanguage()->text('search', 'phone').":</b> ";
                $content_contact .=stripslashes($value['a_phone']);
                $content_contact .=";<br/>";
            }
            if (isset($value['a_fax']) AND $value['a_fax']){
                $content_contact .="<b>".OW::getLanguage()->text('search', 'fax').":</b> ";
                $content_contact .=stripslashes($value['a_fax']);
                $content_contact .=";<br/>";
            }
/*
            if (isset($value['a_email']) AND $value['a_email']){
                $content_contact .="<b>".OW::getLanguage()->text('search', 'email').":</b> ";
                $content_contact .=stripslashes($value['a_email']);
                $content_contact .=";<br/>";
            }
*/
            if (isset($value['a_url']) AND $value['a_url']){
                $content_contact .="<b>".OW::getLanguage()->text('search', 'url').":</b> ";
                $content_contact .=stripslashes($value['a_url']);
                $content_contact .=";<br/>";
            }

            if (isset($value['map_lan']) AND $value['map_lan'] AND isset($value['map_lat']) AND $value['map_lat']){
                $content_contact .="<b>".OW::getLanguage()->text('search', 'map').":</b> ";
                $content_contact .="<a href=\"http://maps.google.com/maps?q=".stripslashes($value['map_lat']).",".stripslashes($value['map_lan'])."\" target=\"_blank\">".OW::getLanguage()->text('search', 'openmap')."</a>";
                $content_contact .=";<br/>";
            }


                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\">".$content_contact."</div>";

                    if ($value['publish_date']!="" AND $value['publish_date']!="0"){
                        $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['publish_date'])."</div>";
                    }

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_fanpage')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"fanpage",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/fanpage?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"fanpage",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }


            if ($plunin_installed['html'] AND (!$option OR $option=="html")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

//                $timestamp=strtotime(date('Y-m-d H:i:s'));
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND startTimeStamp<'".addslashes($timestamp_start)."' AND (endDateFlag='0' OR (endDateFlag='1' AND endTimeStamp>'".addslashes($timestamp_end)."')) AND title LIKE '%".addslashes($query)."%' ORDER BY startTimeStamp DESC LIMIT ".$limit;
                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "html WHERE title LIKE '%".addslashes($query)."%' OR content LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(content) LIKE '%".addslashes(strtolower($query))."%' ORDER BY order_main DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "html WHERE title LIKE '%".addslashes($query)."%' OR content LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(content) LIKE '%".addslashes(strtolower($query))."%' ";
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "html WHERE 1 ORDER BY order_main DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "html WHERE 1 ";
                }
//echo $sql;

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
                $tabt="";
                foreach ( $arr2 as $value )
                {

                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"width:100px;\" >";

                    $dname=BOL_UserService::getInstance()->getDisplayName($value['id_owner']);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_owner']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_owner']);
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
                    $tabt .="</td>";                    

//                    $tabt .="<td style=\"\" colspan=\"2\">";
                    $tabt .="<td style=\"\" >";
                    $tabt .="<a href=\"".$curent_url."html/".$value['id_owner']."/".$value['id']."/index.html\" title=\"".stripslashes($value['title'])."\" >";
//                    $tabt .=stripslashes($value['title']);
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['title'])."</h1>";
                    $tabt .="</a>";

                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['content']));
                    if ($contentx){
                        $contentx=mb_substr($contentx,0,200)."...";    
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\"><i>".$contentx."</i></div>";

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_html')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"html",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/html?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"html",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }



            if ($plunin_installed['games'] AND (!$option OR $option=="games")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "games WHERE tags LIKE '%".addslashes($query)."%' OR name LIKE '%".addslashes($query)."%' OR description LIKE '%".addslashes($query)."%' OR LOWER(tags) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(description) LIKE '%".addslashes(strtolower($query))."%' ORDER BY data_add DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "games WHERE tags LIKE '%".addslashes($query)."%' OR name LIKE '%".addslashes($query)."%' OR description LIKE '%".addslashes($query)."%' OR LOWER(tags) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(name) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(description) LIKE '%".addslashes(strtolower($query))."%' ";
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "games WHERE 1 ORDER BY data_add DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "games WHERE 1 ";
                }
//echo $sql;

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
                $tabt="";
                foreach ( $arr2 as $value )
                {

                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"width:100px;\" >";

///                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
                                if ($value['thumbal']){
                                    $tabt .="<a href=\"".$curent_url."games/".$value['id']."_".$value['id_cats']."/index.html\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .="<img src=\"".stripslashes($value['thumbal'])."\" title=\"".stripslashes($value['name'])."\" width=\"45px\">";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                }

                    $tabt .="</td>";                    

//                    $tabt .="<td style=\"\" colspan=\"2\">";
                    $tabt .="<td style=\"\" >";

                    $dname=BOL_UserService::getInstance()->getDisplayName($value['id_owner']);
$dname=str_replace("_"," ",$dname);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_owner']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_owner']);
                    $tabt .="<div class=\"clearfic ow_right\">";
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
                    $tabt .="</div>";

                    $tabt .="<a href=\"".$curent_url."games/".$value['id']."_".$value['id_cats']."/index.html\" title=\"".stripslashes($value['name'])."\" >";
//                    $tabt .="<b>".stripslashes($value['name'])."</b>";
                    $tabt .="<h1 class=\"floatbox_title item_title\" style=\"font-size:12px;float:none;\">".stripslashes($value['name'])."</h1>";
                    $tabt .="</a>";
/*
                    $tabt .="<br/>";
                    $tabt .="<i>";
                    $tabt .=mb_substr(stripslashes($value['description']),0,50)."...";
                    $tabt .="</i>";
*/
                    $contentx=SEARCH_BOL_Service::getInstance()->html2txt(stripslashes($value['description']));
                    if ($contentx){
                        $contentx=mb_substr($contentx,0,200)."...";    
                    }
                    $tabt .="<div class=\"clearfix ow_remark\" style=\"font-size:11px;\"><i>".$contentx."</i></div>";

                    $tabt .="<div class=\"ow_right clearfix ow_remark\" style=\"font-size:11px;\">".date("Y-m-d H:i:s",$value['data_add'])."</div>";

                    $tabt .="<div class=\"ow_left clearfix ow_remark\" style=\"font-size:11px;\">".$value['tags']."</div>";

                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_games')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"games",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/games?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"games",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }







            if ($plunin_installed['adsense'] AND (!$option OR $option=="adsense")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }

//                $timestamp=strtotime(date('Y-m-d H:i:s'));
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND startTimeStamp<'".addslashes($timestamp_start)."' AND (endDateFlag='0' OR (endDateFlag='1' AND endTimeStamp>'".addslashes($timestamp_end)."')) AND title LIKE '%".addslashes($query)."%' ORDER BY startTimeStamp DESC LIMIT ".$limit;
//                $sql = "SELECT * FROM " . OW_DB_PREFIX. "html WHERE title LIKE '%".addslashes($query)."%' OR content LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(content) LIKE '%".addslashes(strtolower($query))."%' ORDER BY order_main DESC LIMIT ".$limit;
                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "adsense WHERE name LIKE '%".addslashes($query)."%' OR description LIKE '%".addslashes($query)."%' ORDER BY data_add DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "adsense WHERE name LIKE '%".addslashes($query)."%' OR description LIKE '%".addslashes($query)."%' ";

            $sql = "SELECT gm.*,gc.cname,gmads.* FROM " . OW_DB_PREFIX. "adsense gm
            LEFT JOIN " . OW_DB_PREFIX. "adsense_ads gmads ON (gmads.id_ads=gm.id)
            LEFT JOIN " . OW_DB_PREFIX. "adsense_cat gc ON (gc.id=gm.id_cats)
            WHERE  gm.name LIKE '%".addslashes($query)."%' OR gm.description LIKE '%".addslashes($query)."%' ORDER BY gm.data_add DESC LIMIT ".$limit;
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "adsense WHERE 1 ORDER BY data_add DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "adsense WHERE 1 ";
            $query = "SELECT gm.*,gc.cname,gmads.* FROM " . OW_DB_PREFIX. "adsense gm
            LEFT JOIN " . OW_DB_PREFIX. "adsense_ads gmads ON (gmads.id_ads=gm.id)
            LEFT JOIN " . OW_DB_PREFIX. "adsense_cat gc ON (gc.id=gm.id_cats)
            WHERE  1 ORDER BY gm.data_add DESC LIMIT ".$limit;
                }

                
//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

//echo $sql;
                $arr2 = OW::getDbo()->queryForList($sql);
                $tabt="";
                foreach ( $arr2 as $value )
                {

                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
/*
                    $tabt .="<td style=\"width:100px;\" >";
//                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
                                if ($addinfo['games']['0']['thumbnail_url']){
                                    $tabt .="<a href=\"".$curent_url."adsense/".$value['game_tag']."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .="<img src=\"".stripslashes($addinfo['games']['0']['thumbnail_url'])."\" title=\"".stripslashes($value['name'])."\" width=\"45px\">";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                }

                    $tabt .="</td>";                    
*/

                    $tabt .="<td style=\"\" colspan=\"2\">";
//                    $tabt .="<td style=\"\" >";
//                    $tabt .="<a href=\"".$curent_url."adsense/".$value['game_tag']."\" title=\"".stripslashes($value['name'])."\" >";
                    $tabt .="<a href=\"".$curent_url."adsense\" title=\"".stripslashes($value['name'])."\" >";
                    $tabt .="<b>".stripslashes($value['name'])."</b>";
                    $tabt .="</a>";
                    if ($value['description']){
                        $tabt .="<br/>";
                        $tabt .="<i>";
                        $tabt .=mb_substr(stripslashes($value['description']),0,50)."...";
                        $tabt .="</i>";
                    }
//--ifr start                    
/*
if ($row['ads_position']=="left" OR $row['ads_position']=="right") {//sidebar
    $config_width=$config_image_width_sudebar;
    $config_height=$config_image_height_sudebar;
}else{
    $config_width=$config_image_width;
    $config_height=$config_image_height;
}

if ($row['image_width_ads']<$config_width) $config_width=$row['image_width_ads'];
if ($row['image_height_ads']<$config_height) $config_height=$row['image_height_ads'];


    $image_width_ads=$config_width;
    $image_height_ads=$config_height;
*/    
$gname=stripslashes($value['name']);
//if ($value['ads_position']=="left" OR $value['ads_position']=="right" OR  $value['ads_position']==""){
    $tabt .="<iframe  border=\"0\" framespacing=\"0\" frameborder=\"0\" scrolling=\"auto\" style=\"border-width:0;width:100%;margin:auto;padding:0;border:0;overflow-y: scroll;overflow-x: hidden;\" src=\"".$curent_url."adsense/show/".$value['id']."_".$value['id_cats']."/".substr(session_id(),6,5)."/".ADSENSE_BOL_Service::getInstance()->make_seo_url($gname,100).".html\"></iframe>";
//}else{
//    $tabt .="<iframe  border=\"0\" framespacing=\"0\" frameborder=\"0\" scrolling=\"no\" style=\"border-width:0;width:100%;margin:auto;padding:0;border:0;overflow-y: scroll;overflow-x: hidden;\" src=\"".$curent_url."adsense/show/".$value['id']."_".$value['id_cats']."/".substr(session_id(),6,5)."/".ADSENSE_BOL_Service::getInstance()->make_seo_url($gname,100).".html\"></iframe>";
//}

//--ifr end
                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_adsense')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"adsense",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/adsense?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"adsense",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }













            if ($plunin_installed['mochigames'] AND (!$option OR $option=="mochigames")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }
//echo $query;exit;
//                $timestamp=strtotime(date('Y-m-d H:i:s'));
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND startTimeStamp<'".addslashes($timestamp_start)."' AND (endDateFlag='0' OR (endDateFlag='1' AND endTimeStamp>'".addslashes($timestamp_end)."')) AND title LIKE '%".addslashes($query)."%' ORDER BY startTimeStamp DESC LIMIT ".$limit;
//                $sql = "SELECT * FROM " . OW_DB_PREFIX. "html WHERE title LIKE '%".addslashes($query)."%' OR content LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(content) LIKE '%".addslashes(strtolower($query))."%' ORDER BY order_main DESC LIMIT ".$limit;
                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "mochigames_item WHERE name LIKE '%".addslashes($query)."%' OR description LIKE '%".addslashes($query)."%' ORDER BY timestamp DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "mochigames_item WHERE name LIKE '%".addslashes($query)."%' OR description LIKE '%".addslashes($query)."%' ";
//echo $sql;exit;
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "mochigames_item WHERE 1 ORDER BY timestamp DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "mochigames_item WHERE 1 ";
//echo $sql;exit;
                }

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
                $tabt="";
                foreach ( $arr2 as $value )
                {

                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"width:100px;\" >";

                                $addinfo = json_decode($value['json'], true);
//print_r($addinfo);exit;
//echo "--".$addinfo['games']['0']['thumbnail_url'];
//////                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
                                if ($addinfo['games']['0']['thumbnail_url']){
                                    $tabt .="<a href=\"".$curent_url."mochigames/".$value['game_tag']."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .="<img src=\"".stripslashes($addinfo['games']['0']['thumbnail_url'])."\" title=\"".stripslashes($value['name'])."\" width=\"45px\">";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                }
/*
//                    $dname=BOL_UserService::getInstance()->getDisplayName($value['id_owner']);
//                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_owner']);
//                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_owner']);
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
*/

                    $tabt .="</td>";                    

//                    $tabt .="<td style=\"\" colspan=\"2\">";
                    $tabt .="<td style=\"\" >";
                    $tabt .="<a href=\"".$curent_url."mochigames/".$value['game_tag']."\" title=\"".stripslashes($value['name'])."\" >";
                    $tabt .="<b>".stripslashes($value['name'])."</b>";
                    $tabt .="</a>";
                    $tabt .="<br/>";
                    $tabt .="<i>";
                    $tabt .=mb_substr(stripslashes($value['description']),0,50)."...";
                    $tabt .="</i>";
                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_mochigames')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"mochigames",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/mochigames?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"mochigames",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }


            if ($plunin_installed['wiki'] AND (!$option OR $option=="wiki")) {
                if (!$option) {
                    $limit=$limit_all;
//                    else $limit=$limit_single;
                }else {
                    $limit=$start_form.",".$per_page;
                }
//echo $query;exit;
//                $timestamp=strtotime(date('Y-m-d H:i:s'));
//                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' AND startTimeStamp<'".addslashes($timestamp_start)."' AND (endDateFlag='0' OR (endDateFlag='1' AND endTimeStamp>'".addslashes($timestamp_end)."')) AND title LIKE '%".addslashes($query)."%' ORDER BY startTimeStamp DESC LIMIT ".$limit;
//                $sql = "SELECT * FROM " . OW_DB_PREFIX. "html WHERE title LIKE '%".addslashes($query)."%' OR content LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%' OR LOWER(content) LIKE '%".addslashes(strtolower($query))."%' ORDER BY order_main DESC LIMIT ".$limit;
                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "userwiki_pages WHERE title LIKE '%".addslashes($query)."%' OR information LIKE '%".addslashes($query)."%' ORDER BY added DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "userwiki_pages  WHERE title LIKE '%".addslashes($query)."%' OR information LIKE '%".addslashes($query)."%' ";
//echo $sql;exit;
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "userwiki_pages  WHERE 1 ORDER BY added DESC LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "userwiki_pages WHERE 1 ";
//echo $sql;exit;
                }
//echo $sql;exit;

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
                $tabt="";
                foreach ( $arr2 as $value )
                {

                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tabt .="<td style=\"width:100px;\" >";
/*
                                $addinfo = json_decode($value['json'], true);
//print_r($addinfo);exit;
//echo "--".$addinfo['games']['0']['thumbnail_url'];
//////                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
                                if ($addinfo['games']['0']['thumbnail_url']){
                                    $tabt .="<a href=\"".$curent_url."mochigames/".$value['game_tag']."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                    $tabt .="<img src=\"".stripslashes($addinfo['games']['0']['thumbnail_url'])."\" title=\"".stripslashes($value['name'])."\" width=\"45px\">";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                }
*/
/*
//                    $dname=BOL_UserService::getInstance()->getDisplayName($value['id_owner']);
//                    $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_owner']);
//                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_owner']);
                    if ($uimg){
                        $tabt .="<a href=\"".$uurl."\">";
                        $tabt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $tabt .="<a href=\"".$uurl."\"  >";
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
*/

//                    $tabt .="</td>";                    

//                    $tabt .="<td style=\"\" colspan=\"2\">";
                    $tabt .="<td style=\"\" colspan=\"2\">";
                    $tabt .="<a href=\"".$curent_url."userwiki/view/".$value['id']."\" title=\"".stripslashes($value['title'])."\" >";
                    $tabt .="<b>".stripslashes($value['title'])."</b>";
                    $tabt .="</a>";
                    $tabt .="<br/>";
                    $tabt .="<i>";
                    $tabt .=mb_substr(stripslashes($value['information']),0,50)."...";
                    $tabt .="</i>";
                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    if (!$option){
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_wiki')."</b>";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    }

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

//echo $prev_page;
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
//                    $paging=$this->pagination($curent_page,0,$prev_page,($curent_page+1),"mochigames",$add_paramurl);
                    $paging =SEARCH_BOL_Service::getInstance()->makePagination($curent_page, $all_results, $per_page, 1, $curent_url."query/wiki?".$add_paramurl,"&page=");
                    if (!$paging) $paging =" ";
                }else{
//                    $paging=$this->pagination($curent_page,0,$prev_page,0,"mochigames",$add_paramurl);
                    $paging ="";
                }
//echo $paging;

            }

























            if ($plunin_installed['basepages'] AND (!$option OR $option=="basepages")) {
                if (!$option) $limit=$limit_all;
                    else $limit=$limit_single;
//echo "------------";
                if (strlen($query)>1){
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_component_setting WHERE name='content' AND (value LIKE '%".addslashes($query)."%' OR LOWER(value) LIKE '%".addslashes(strtolower($query))."%') LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "base_component_setting WHERE name='content' AND (value LIKE '%".addslashes($query)."%' OR LOWER(value) LIKE '%".addslashes(strtolower($query))."%') ";
                }else{
                    $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_component_setting WHERE 1 LIMIT ".$limit;
                    $sqlll = "SELECT COUNT(*) as allp FROM " . OW_DB_PREFIX. "base_component_setting WHERE 1 ";
                }

//--all s
                        $arrll = OW::getDbo()->queryForList($sqlll);
                        if (isset($arrll['0'])){
                            $all_results=$arrll['0']['allp'];
                        }else{
                            $all_results=0;
                        }
//--all e

                $arr2 = OW::getDbo()->queryForList($sql);
//echo $query;exit;
//print_r($arr2);


                $tabt="";
                foreach ( $arr2 as $value )
                {
                    $content=stripslashes($value['value']);
//                    $content=mb_substr($content,0,300);

                    $tabt .="<tr class=\"ow_alt".$curent_bg."\">";
                    $tabt .="<td style=\"\" colspan=\"2\">";
//                    $tabt .="<a href=\"".$curent_url."page/".$value['id']."/index.html\" title=\"".$content."\" >";
                    $tabt .=$content;
//                    $tabt .="</a>";
                    $tabt .="</td>";
                    $tabt .="</tr>";

                    $tabt .="<tr >";
                    $tabt .="<td style=\"height:3px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tabt .="</td>";
                    $tabt .="</tr>";


                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                }
//echo $query;
                if ($tabt) {
$global_found=true;
                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $tab .="<tr>";
                    $tab .="<td style=\"\" colspan=\"2\" >";
                    $tab .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";
                    $tab .="<tr class=\"ow_alt".$curent_bg."\">";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"border:1px solid #ddd;border-bottom:2px solid #aaa;border-left:2px solid #aaa;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"border-left:2px solid #aaa;border-right:2px solid #aaa;margin:auto;\" colspan=\"2\" >";
//                    $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\" >";
                    $tab .="<td class=\"ow_ipc_header\" style=\"margin:auto;\" colspan=\"2\" >";
//                    $tab .="<div style=\"margin:10px;\">";
                    $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_basepages')."</b>";
//                    $tab .="</div>";
                    $tab .="</td>";
                    $tab .="</tr>";


                    $tab .="<tr >";
                    $tab .="<td style=\"height:5px;\" colspan=\"2\" >";
//                    $tabt .="&nbsp;";
                    $tab .="</td>";
                    $tab .="</tr>";

                    $curent_bg=$curent_bg+1;
                    if ($curent_bg>2){
                        $curent_bg=1;
                    }

                    $tab .=$tabt;
                }

            }




//            }else{//if $_GET['query'] NOT
//                $foundsomething=false;
            }//end if $_GET['query'] 



//                        $content .=SEARCH_BOL_Service::getInstance()->make_adsfromadsense();
//                        $content .=SEARCH_BOL_Service::getInstance()->make_adsfromadspro();
            $content .=SEARCH_BOL_Service::getInstance()->make_ads('full');

            if ($tab){
                $content .="<table style=\"width:100%;margin:auto;\">";
                $content .=$tab;
                $content .="</table>";
            }
                        
//                        $content .=SEARCH_BOL_Service::getInstance()->make_adsfromadsense();
//                        $content .=SEARCH_BOL_Service::getInstance()->make_adsfromadspro();
//            $content .=SEARCH_BOL_Service::getInstance()->make_ads('full');



//--------------------------------

//        if (strlen($query)>1){
        if (strlen($query)>1 OR ($query=="*" AND !is_array($addsearch_sel) ) ){
            if (!$option) $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                else  $sel="";
            $menu .="<div class=\"clearfix\"  style=\"margin:auto;\">";
            $menu .="<div class=\"ow_ipc_header clearfix\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
//            $menu .="<div class=\"ow_ipc_header clearfix\" style=\"border-right:1px solid #aaa;margin:2px;\" >";
//            $menu .="<div class=\"console_item clearfix\" style=\"border-right:1px solid #aaa;margin:2px;\" >";
//            $menu .="<div class=\"ow_console_body clearfix\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
            $menu .="<a href=\"".$curent_url."query".$add."\">";
            $menu .=OW::getLanguage()->text('search', 'menu_results_all');
            $menu .="</a>";
            $menu .="</div>";
            $menu .="</div>";
        }

            if ($option=="user") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                else  $sel="";
            $menu .="<div class=\"clearfix\"  style=\"margin:auto;\">";
            $menu .="<div class=\"ow_ipc_header clearfix\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
//echo $add."--".$query."--";
//if (!$add AND $query) $add="?query=*";
if ($add=="" AND !$query) $add="?query=*";
//else $add="xx";
            $menu .="<a href=\"".$curent_url."query/user".$add."\">";
            $menu .=OW::getLanguage()->text('search', 'menu_results_user');
            $menu .="</a>";
            $menu .="</div>";
            $menu .="</div>";
        

//        if (strlen($query)>1){
//        if (strlen($query)>1 OR (strlen($query)>0 AND !is_array($addsearch_sel) ) ){




//        if (strlen($query)>1 OR ($query=="*" AND !is_array($addsearch_sel) ) ){

            if ($plunin_installed['cms']){
                if ($option=="cms") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\"  style=\"margin:auto;\">";
                $menu .="<div class=\"ow_ipc_header clearfix\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/cms".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_cms');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['forum']){
                if ($option=="forum") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\"  style=\"margin:auto;\">";
                $menu .="<div class=\"ow_ipc_header clearfix\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/forum".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_forum');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['map']){
                if ($option=="map") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\"  style=\"margin:auto;\">";
                $menu .="<div class=\"ow_ipc_header clearfix\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/map".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_map');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['news']){
                if ($option=="news") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\"  style=\"margin:auto;\">";
                $menu .="<div class=\"ow_ipc_header clearfix\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/news".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_news');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['links']){
                if ($option=="links") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;\">";
                $menu .="<div class=\"ow_ipc_header clearfix\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/links".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_links');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['video']){
                if ($option=="video") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;\">";
                $menu .="<div class=\"ow_ipc_header clearfix\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/video".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_video');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['photo']){
                if ($option=="photo") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/photo".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_photo');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }


            if ($plunin_installed['shoppro']){
                if ($option=="shoppro") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/shoppro".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_shoppro');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['classifiedspro']){
                if ($option=="classifiedspro") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/classifiedspro".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_classifiedspro');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['pages']){
                if ($option=="pages") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/pages".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_pages');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['groups']){
                if ($option=="groups") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/groups".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_groups');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['blogs']){
                if ($option=="blogs") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/blogs".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_blogs');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }
            if ($plunin_installed['event']){
                if ($option=="event") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/event".$add."\">";
//                $menu .="<span class=\"ow_button \">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_event');
//                $menu .="</span>";
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['fanpage']){
                if ($option=="fanpage") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/fanpage".$add."\">";
//                $menu .="<span class=\"ow_button \">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_fanpage');
//                $menu .="</span>";
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['html']){
                if ($option=="html") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/html".$add."\">";
//                $menu .="<span class=\"ow_button \">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_html');
//                $menu .="</span>";
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['games']){
                if ($option=="games") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/games".$add."\">";
//                $menu .="<span class=\"ow_button \">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_games');
//                $menu .="</span>";
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['adsense']){
                if ($option=="adsense") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/adsense".$add."\">";
//                $menu .="<span class=\"ow_button \">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_adsense');
//                $menu .="</span>";
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['mochigames']){
                if ($option=="mochigames") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/mochigames".$add."\">";
//                $menu .="<span class=\"ow_button \">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_mochigames');
//                $menu .="</span>";
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }
            if ($plunin_installed['wiki']){
                if ($option=="mochigames") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/wiki".$add."\">";
//                $menu .="<span class=\"ow_button \">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_wiki');
//                $menu .="</span>";
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

            if ($plunin_installed['basepages']){
                if ($option=="basepages") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                    else  $sel="";
                $menu .="<div class=\"clearfix\" style=\"margin:auto;display:block;\">";
                $menu .="<div class=\"ow_ipc_header clearfixx\" style=\"border-right:1px solid #aaa;margin:2px;".$sel."\" >";
                $menu .="<a href=\"".$curent_url."query/basepages".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_basepages');
                $menu .="</a>";
                $menu .="</div>";
                $menu .="</div>";
            }

//$menu .="<a href=\"http://test.a6.pl/ordershop/approval?deny=6_6c2be\" style=\"display:inline;;font-size:14px;font-weight:bold;\"><span class=\"ow_button ow_ic_delete ow_positive\"><span><input type=\"button\" value=\"Wyłącz\" class=\"ow_ic_delete ow_positive\"></span></span></a>";













/*

            $menu .="<table style=\"margin:auto; border-spacing:10px;\" >";
            $menu .="<tr>";
            $menu .="<td style=\"width:16px;border-bottom:1px solid #eee;\">";
            $menu .="[+]";
            $menu .="</td>";
            if (!$option) $sel="border-left:2px solid #555;font-weight:bold;";
                else  $sel="";
//            $menu .="<td style=\"border-bottom:1px solid #eee;".$sel."\">";
            $menu .="<td class=\"ow_ipc_header clearfix\" style=\"border-right:1px solid #aaa;margin:auto;\" colspan=\"2\" >";
            $menu .="<a href=\"/query".$add."\">";
            $menu .=OW::getLanguage()->text('search', 'menu_results_all');
            $menu .="</a>";
            $menu .="</td>";
            $menu .="</tr>";

            $menu .="<tr>";
            $menu .="<td style=\"width:16px;border-bottom:1px solid #eee;\">";
            $menu .="[+]";
            $menu .="</td>";
            if ($option=="user") $sel="border-left:2px solid #555;font-weight:bold;";
                else  $sel="";
//            $menu .="<td style=\"border-bottom:1px solid #eee;".$sel."\">";
            $menu .="<td class=\"ow_ipc_header clearfix\" style=\"border-right:1px solid #aaa;margin:auto;\" colspan=\"2\" >";
            $menu .="<a href=\"/query/user".$add."\">";
            $menu .=OW::getLanguage()->text('search', 'menu_results_user');
            $menu .="</a>";
            $menu .="</td>";
            $menu .="</tr>";

            if ($plunin_installed['forum']){
                $menu .="<tr>";
                $menu .="<td style=\"width:16px;border-bottom:1px solid #eee;\">";
                $menu .="[+]";
                $menu .="</td>";
                if ($option=="forum") $sel="border-left:2px solid #555;font-weight:bold;";
                    else  $sel="";
                $menu .="<td style=\"border-bottom:1px solid #eee;".$sel."\">";
                $menu .="<a href=\"/query/forum".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_forum');
                $menu .="</a>";
                $menu .="</td>";
                $menu .="</tr>";
            }

            if ($plunin_installed['links']){
                $menu .="<tr>";
                $menu .="<td style=\"width:16px;border-bottom:1px solid #eee;\">";
                $menu .="[+]";
                $menu .="</td>";
                if ($option=="links") $sel="border-left:2px solid #555;font-weight:bold;";
                    else  $sel="";
                $menu .="<td style=\"border-bottom:1px solid #eee;".$sel."\">";
                $menu .="<a href=\"/query/links".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_links');
                $menu .="</a>";
                $menu .="</td>";
                $menu .="</tr>";
            }

            if ($plunin_installed['video']){
                $menu .="<tr>";
                $menu .="<td style=\"width:16px;border-bottom:1px solid #eee;\">";
                $menu .="[+]";
                $menu .="</td>";
                if ($option=="video") $sel="border-left:2px solid #555;font-weight:bold;";
                    else  $sel="";
                $menu .="<td style=\"border-bottom:1px solid #eee;".$sel."\">";
                $menu .="<a href=\"/query/video".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_video');
                $menu .="</a>";
                $menu .="</td>";
                $menu .="</tr>";
            }

            if ($plunin_installed['photo']){
                $menu .="<tr>";
                $menu .="<td style=\"width:16px;border-bottom:1px solid #eee;\">";
                $menu .="[+]";
                $menu .="</td>";
                if ($option=="photo") $sel="border-left:2px solid #555;font-weight:bold;";
                    else  $sel="";
                $menu .="<td style=\"border-bottom:1px solid #eee;".$sel."\">";
                $menu .="<a href=\"/query/photo".$add."\">";
                $menu .=OW::getLanguage()->text('search', 'menu_results_photo');
                $menu .="</a>";
                $menu .="</td>";
                $menu .="</tr>";
            }
            
            $menu .="</table>";
//print_r($params);exit;
*/
//        }//if (strlen($query)>1 OR ($query=="*" AND !is_array($addsearch_sel) ) ){

//echo "adasD";exit;

    

            $foundsomething=true;
        }else{
            $foundsomething=false;
            $content .=OW::getLanguage()->text('search', 'main_noresultsfound');

            $content.="<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\">";
            $content .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
            $content .=$add_opt;
//            $content .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
//            $content .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
            $content .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
            $content .="</form>";
        }

        


    }//if user
//echo "--".$foundsomething."------".$global_found;exit;
//echo $paging."------".$option;exit;
        if ($foundsomething){
            $tabs=$this->make_tabs("search",$plunin_installed,$option);
            if (!$paging AND $option!=""){
                if ($paging==true) {
                    $this->assign('paging1',"");
                }else{
                    if (!$global_found){
                        $this->assign('paging1',"<h2 class=\"clearfix ow_center\" style=\"height:35px;\">".OW::getLanguage()->text('search', 'menu_results_nofound')."</h2>");
                    }else{
                        $this->assign('paging1',"");
                    }
                }
                $this->assign('paging2',"");
            }else{
                if ($content){
                    $this->assign('paging1',$paging);
                    $this->assign('paging2',$paging);                
                }else{
                    $this->assign('paging1',$paging);
                    if ($paging==true) {
                        $this->assign('paging2',"");
                    }else{
//                        $this->assign('paging2',OW::getLanguage()->text('search', 'menu_results_nofound'));
                        $this->assign('paging2',"<h2 class=\"clearfix ow_center\" style=\"height:35px;\">".OW::getLanguage()->text('search', 'menu_results_nofound')."</h2>");
                    }
                }
            }
            $this->assign('tabs',$tabs);
            $this->assign('header',$header);
            $this->assign('menu', $menu);
            $this->assign('content', $content);
        }else{
            $this->assign('paging1',OW::getLanguage()->text('search', 'menu_results_nofound'));
            $this->assign('paging2',"");
            $this->assign('tabs',"");
            $this->assign('header',"");
            $this->assign('menu', $menu);
            $this->assign('content', $content);
        }

    } 





    public function make_tabs($selected=1,$plunin_installed="",$option="")
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
$content="";
//echo "--------------".$option;

        if (!isset($option)){
            $option="";
        }

        if (!isset($plunin_installed)){
            $plunin_installed=array();
        }       

        $query="";
        if (isset($_GET['query'])) $query=$_GET['query'];
        if ($query) $add="?query=".$query;
            else $add="";

        if (strlen($query)<2){   
            $query="*";
        }

$content_f="";
$content_t ="";
            $content_t .="<div class=\"ow_content\" style=\"min-height:0;\">";
/*
            $page_on_top_shop=OW::getConfig()->getValue('search', 'config_page_on_top_shop');
            if ($page_on_top_shop!=""){
                $content_t .="<div class=\"ow_content_menu_wrap\">";
                if (OW::getConfig()->getValue('search', 'admin_replace_btobr')==1){
                    $page_on_top_shop=SEARCH_BOL_Service::getInstance()->ntobr($page_on_top_shop);
                }
                $page_top_template="";
$page_top_template .="<div class=\"ow_dnd_widget index-PAGES_CMP_MenuWidget\">";

    if (OW::getConfig()->getValue('search', 'config_page_on_top_shop_title')!=""){
    $page_top_template .="<div class=\"ow_box_cap_empty ow_dnd_configurable_component clearfix\">
        <div class=\"ow_box_cap_right\">
            <div class=\"ow_box_cap_body\">
                <h3 class=\"ow_ic_info\">".OW::getConfig()->getValue('search', 'config_page_on_top_shop_title')."</h3>
            </div>
        </div>
    </div>";
    }

    $page_top_template .="<div class=\"ow_box_empty ow_stdmargin clearfix index-PAGES_CMP_MenuWidget ow_no_cap ow_break_word\" style=\"\">
        <div class=\"clearfix ow_left\" style=\"margin:10px;\">
            ".$page_on_top_shop."
        </div>
    </div>";

$page_top_template .="</div>";

            
                $content_t .=$page_top_template;
                $content_t .="</div>";
            }
*/

$content_t .="<div class=\"ow_content_menu_wrap\">";
$content_t .="<ul class=\"ow_content_menu clearfix\">";









        $sel=" active ";
        if ($option=="user"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_user');
        }else if ($option=="forum"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_forum');
        }else if ($option=="map"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_map');
        }else if ($option=="news"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_news');
        }else if ($option=="cms"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_cms');
        }else if ($option=="links"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_links');
        }else if ($option=="video"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_video');
        }else if ($option=="photo"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_photo');
        }else if ($option=="shoppro"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_shoppro');
        }else if ($option=="classifiedspro"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_classifiedspro');
        }else if ($option=="pages"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_pages');
        }else if ($option=="groups"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_groups');
        }else if ($option=="blogs"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_blogs');
        }else if ($option=="event"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_event');
        }else if ($option=="fanpage"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_fanpage');
        }else if ($option=="html"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_html');
        }else if ($option=="games"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_games');
        }else if ($option=="adsense"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_adsense');
        }else if ($option=="mochigames"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_mochigames');
        }else if ($option=="wiki"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_wiki');
        }else if ($option=="basepages"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_basepages');
        }else{
            $header_add="";
            $sel="";
        }

        if (!$header_add) $sel=" active ";
            else $sel="";
        $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."query".$add."\"><span class=\"ow_ic_lens\">".OW::getLanguage()->text('search', 'main_menu_item')."</span></a></li>";//moje zamówienia
//$content_f="";

        $add_opt="";
        if ($header_add AND $option){
            $sel=" active ";
//            $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."basket/showbasket\"><span class=\"ow_ic_plugin\">".$header_add."</span></a></li>";
            $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."query/".$option.$add."\"><span class=\"ow_ic_right_arrow\">".$header_add."</span></a></li>";

                            //questions_question_relationship_label
                            //questions_question_relationship_description
            


//$content_f="";


//aaaaa

            if ($option=="user"){//-------------------------members
                $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_question WHERE onSearch='1' ORDER BY sortOrder ";
                $arr1 = OW::getDbo()->queryForList($sql);
                $enter=1;
                $inline=3;
                foreach ( $arr1 as $value )
                {
//                    $add_opt .=$value['name']."--".$value['type']."-".$value['presentation']."<hr>";
                    if ($value['type']=="select"){
                        if ($add_opt) $add_opt .="&nbsp; ";
$add_opt .="<div class=\"clearfix  ow_boxx\" style=\"padding-right:5px;margin:auto;min-width:150px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
                        $add_opt .="<b>".OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
                        $add_opt .="<select name=\"search_sel[".$value['name']."]\">";

//                        if (!isset($_GET['search_sel'][$value['name']]) AND !$_GET['search_sel'][$value['name']]) $sel=" selected ";
                        if (!isset($_GET['search_sel'][$value['name']]) OR (isset($_GET['search_sel'][$value['name']]) AND !$_GET['search_sel'][$value['name']] ) ) $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"\">-- ".OW::getLanguage()->text('search', 'select')." --</option>";

                        $sql2 = "SELECT * FROM " . OW_DB_PREFIX. "base_question_value WHERE questionName='".$value['name']."' ORDER BY sortOrder ";
                        $arr2 = OW::getDbo()->queryForList($sql2);
                        foreach ( $arr2 as $value2 )
                        {
                            if (isset($_GET['search_sel'][$value['name']]) AND $value2['value']==$_GET['search_sel'][$value['name']]) $sel=" selected ";
                                else $sel=" ";
                            $add_opt .="<option ".$sel." value=\"".$value2['value']."\">".OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_value_'.$value2['value'])."</option>";

                        }
                        $add_opt .="</select>";
$add_opt .="    </div>
</div>";

/*
                            $enter++;
                            if ($enter>$inline){
                                $enter=1;
                                $add_opt .="<br/>";
                            }
*/
                    }
                        if ($value['type']=="text"){

                        if ($add_opt) $add_opt .="&nbsp; ";
//                        $add_opt .=OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').": ";
//                        $add_opt .="<input type=\"text\" name=\"search_text[".$value['name']."]\" value=\"".$_GET['search_text'][$value['name']]."\" style=\"width:150px;\">";


$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right:5px;margin:auto;min-width:150px;display:inline-block;\">";
     $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
//        <a href=\"http://test.a6.pl/query?query=ar\">";
        $add_opt .="<b>".OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (isset($_GET['search_text'][$value['name']])){
            $add_opt .="<input type=\"text\" name=\"search_text[".$value['name']."]\" value=\"".$_GET['search_text'][$value['name']]."\" style=\"width:150px;\">";
        }else{
            $add_opt .="<input type=\"text\" name=\"search_text[".$value['name']."]\" value=\"\" style=\"width:150px;\">";
        }
//$add_opt .="</a>
$add_opt .="    </div>
</div>";


/*
                            $enter++;
                            if ($enter>$inline){
                                $enter=1;
                                $add_opt .="<br/>";
                            }
*/
                    }
                }//for

                $content_f="";
                if ($add_opt){
                    $content_f .="<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\">";
                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
                    $content_f .="&nbsp;";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<br/>";
                    $content_f .="<div clas=\"clearfix\" style=\"margin-top:10px;min-height:0;\">".$add_opt."</div>";
                    $content_f .="<hr/>";
                    $content_f .="</form>";
                }
                $add_opt =$content_f;
            }else if ($option=="shoppro"){//------------shoppro

                        if ($add_opt) $add_opt .="&nbsp; ";
//                        $add_opt .=OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').": ";
//                        $add_opt .="<input type=\"text\" name=\"search_text[".$value['name']."]\" value=\"".$_GET['search_text'][$value['name']]."\" style=\"width:150px;\">";

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_price').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_pricef'])) $_GET['search_addparam_pricef'] ="";
        if (!isset($_GET['search_addparam_pricet'])) $_GET['search_addparam_pricet'] ="";
$add_opt .=OW::getLanguage()->text('search', 'addparam_adstype_from').":";
        $add_opt .="<input type=\"text\" name=\"search_addparam_pricef\" value=\"".$_GET['search_addparam_pricef']."\" style=\"width:60px;\">";
//        $add_opt .="&nbsp;-&nbsp;";
$add_opt .=",&nbsp;";
$add_opt .=OW::getLanguage()->text('search', 'addparam_adstype_to').":";
        $add_opt .="<input type=\"text\" name=\"search_addparam_pricet\" value=\"".$_GET['search_addparam_pricet']."\" style=\"width:60px;\">";
//$add_opt .="</a>
$add_opt .="    </div>
</div>";

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_adstype').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_typeads'])) $_GET['search_addparam_typeads'] ="";
                        $add_opt .="<select name=\"search_addparam_typeads\">";

                        if (!isset($_GET['search_addparam_typeads']) OR !$_GET['search_addparam_typeads']) $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"\">-- ".OW::getLanguage()->text('search', 'typeads_selec')." --</option>";

                        if (isset($_GET['search_addparam_typeads']) AND $_GET['search_addparam_typeads']=="0") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'typeads_classifieds')."</option>";

                        if (isset($_GET['search_addparam_typeads']) AND $_GET['search_addparam_typeads']=="2") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"2\">".OW::getLanguage()->text('search', 'typeads_paybycredit')."</option>";

                        if (isset($_GET['search_addparam_typeads']) AND $_GET['search_addparam_typeads']=="1") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'typeads_shopmode')."</option>";

                        $add_opt .="</select>";
//$add_opt .="</a>
$add_opt .="    </div>
</div>";


$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_location').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_location'])) $_GET['search_addparam_location'] ="";
        $add_opt .="<input type=\"text\" name=\"search_addparam_location\" value=\"".$_GET['search_addparam_location']."\" style=\"width:150px;\">";
//$add_opt .="</a>
$add_opt .="    </div>
</div>";

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_condition').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_condition'])) $_GET['search_addparam_condition'] ="0";
    //    $add_opt .="<input type=\"text\" name=\"search_addparam_location\" value=\"".$_GET['search_addparam_location']."\" style=\"width:150px;\">";
                        $add_opt .="<select name=\"search_addparam_condition\">";

                        if ($_GET['search_addparam_condition']=="0") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"0\">-- ".OW::getLanguage()->text('search', 'condition_select')." --</option>";
                        if ($_GET['search_addparam_condition']=="1") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'condition_new')."</option>";
                        if ($_GET['search_addparam_condition']=="2") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"2\">".OW::getLanguage()->text('search', 'condition_used')."</option>";
                        $add_opt .="</select>";
//$add_opt .="</a>
$add_opt .="    </div>
</div>";


                $content_f="";
                if ($add_opt){
                    $content_f .="<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\">";
                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
                    $content_f .="&nbsp;";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<br/>";
//                    $content_f .=$add_opt;
                    $content_f .="<div clas=\"clearfix\" style=\"margin-top:10px;min-height:0;\">".$add_opt."</div>";
                    $content_f .="<hr/>";
                    $content_f .="</form>";
                }
                $add_opt =$content_f;

            }else if ($option=="fanpage"){//------------fanpage

                        if ($add_opt) $add_opt .="&nbsp; ";
//                        $add_opt .=OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').": ";
//                        $add_opt .="<input type=\"text\" name=\"search_text[".$value['name']."]\" value=\"".$_GET['search_text'][$value['name']]."\" style=\"width:150px;\">";


//a_city  a_postcode      a_street        a_email a_url   a_phone a_fax   a_country

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_city').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_city'])) $_GET['search_addparam_city'] ="";
        $add_opt .="<input type=\"text\" name=\"search_addparam_city\" value=\"".$_GET['search_addparam_city']."\" style=\"width:150px;\">";
$add_opt .="</div>
</div>";

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_street').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_street'])) $_GET['search_addparam_street'] ="";
        $add_opt .="<input type=\"text\" name=\"search_addparam_street\" value=\"".$_GET['search_addparam_street']."\" style=\"width:150px;\">";
$add_opt .="</div>
</div>";

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_country').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_country'])) $_GET['search_addparam_country'] ="";
        $add_opt .="<input type=\"text\" name=\"search_addparam_country\" value=\"".$_GET['search_addparam_country']."\" style=\"width:150px;\">";
$add_opt .="</div>
</div>";


$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_category').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_category'])) $_GET['search_addparam_category'] ="";
//        if (
//        $add_opt .="<input type=\"text\" name=\"search_addparam_category\" value=\"".$_GET['search_addparam_category']."\" style=\"width:150px;\">";
                        $add_opt .="<select name=\"search_addparam_category\">";

                       if (!isset($_GET['search_addparam_category']) OR !$_GET['search_addparam_category'] OR $_GET['search_addparam_category']=="") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"\">-- ".OW::getLanguage()->text('search', 'select')." --</option>";

                        $sql2 = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_categories WHERE active='1' AND id2='0' ORDER BY name ";
                        $arr2 = OW::getDbo()->queryForList($sql2);
                        foreach ( $arr2 as $value2 )
                        {
                            if ($_GET['search_addparam_category']>0 AND $value2['id']==$_GET['search_addparam_category']) $sel=" selected ";
                                else $sel=" ";
                            $add_opt .="<option ".$sel." value=\"".$value2['id']."\">".stripslashes($value2['name'])."</option>";
                                $sql3 = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_categories WHERE active='1' AND id2='".addslashes($value2['id'])."' ORDER BY name ";
                                $arr3 = OW::getDbo()->queryForList($sql3);
                                foreach ( $arr3 as $value3 )
                                {
                                    if ($_GET['search_addparam_category']>0 AND $value3['id']==$_GET['search_addparam_category']) $sel=" selected ";
                                        else $sel=" ";
                                    $add_opt .="<option ".$sel." value=\"".$value3['id']."\">&nbsp;&nbsp;&nbsp;-".stripslashes($value3['name'])."</option>";
                                }



                        }
                        $add_opt .="</select>";

$add_opt .="</div>
</div>";

/*
$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_location').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_location'])) $_GET['search_addparam_location'] ="";
        $add_opt .="<input type=\"text\" name=\"search_addparam_location\" value=\"".$_GET['search_addparam_location']."\" style=\"width:150px;\">";
$add_opt .="</div>
</div>";
*/

/*
$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_city').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
$add_opt .=OW::getLanguage()->text('search', 'addparam_adstype_from').":";
        $add_opt .="<input type=\"text\" name=\"search_addparam_pricef\" value=\"".$_GET['search_addparam_pricef']."\" style=\"width:60px;\">";
//        $add_opt .="&nbsp;-&nbsp;";
$add_opt .=",&nbsp;";
$add_opt .=OW::getLanguage()->text('search', 'addparam_adstype_to').":";
        $add_opt .="<input type=\"text\" name=\"search_addparam_pricet\" value=\"".$_GET['search_addparam_pricet']."\" style=\"width:60px;\">";
//$add_opt .="</a>
$add_opt .="    </div>
</div>";

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_adstype').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_typeads'])) $_GET['search_addparam_typeads'] ="";
                        $add_opt .="<select name=\"search_addparam_typeads\">";

                        if (!isset($_GET['search_addparam_typeads']) OR !$_GET['search_addparam_typeads']) $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"\">-- ".OW::getLanguage()->text('search', 'typeads_selec')." --</option>";

                        if (isset($_GET['search_addparam_typeads']) AND $_GET['search_addparam_typeads']=="0") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('search', 'typeads_classifieds')."</option>";

                        if (isset($_GET['search_addparam_condition']) AND $_GET['search_addparam_condition']=="2") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"2\">".OW::getLanguage()->text('search', 'typeads_paybycredit')."</option>";

                        if (isset($_GET['search_addparam_condition']) AND $_GET['search_addparam_condition']=="1") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'typeads_shopmode')."</option>";

                        $add_opt .="</select>";
//$add_opt .="</a>
$add_opt .="    </div>
</div>";


$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_location').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_location'])) $_GET['search_addparam_location'] ="";
        $add_opt .="<input type=\"text\" name=\"search_addparam_location\" value=\"".$_GET['search_addparam_location']."\" style=\"width:150px;\">";
//$add_opt .="</a>
$add_opt .="    </div>
</div>";

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_condition').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_condition'])) $_GET['search_addparam_condition'] ="0";
    //    $add_opt .="<input type=\"text\" name=\"search_addparam_location\" value=\"".$_GET['search_addparam_location']."\" style=\"width:150px;\">";
                        $add_opt .="<select name=\"search_addparam_condition\">";

                        if ($_GET['search_addparam_condition']=="0") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"0\">-- ".OW::getLanguage()->text('search', 'condition_select')." --</option>";
                        if ($_GET['search_addparam_condition']=="1") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('search', 'condition_new')."</option>";
                        if ($_GET['search_addparam_condition']=="2") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"2\">".OW::getLanguage()->text('search', 'condition_used')."</option>";
                        $add_opt .="</select>";
//$add_opt .="</a>
$add_opt .="    </div>
</div>";
*/

                $content_f="";
                if ($add_opt){
                    $content_f .="<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\">";
                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
                    $content_f .="&nbsp;";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<br/>";
//                    $content_f .=$add_opt;
                    $content_f .="<div clas=\"clearfix\" style=\"margin-top:10px;min-height:0;\">".$add_opt."</div>";
                    $content_f .="<hr/>";
                    $content_f .="</form>";
                }
                $add_opt =$content_f;

            }else if ($option=="map"){//------------maps

                        if ($add_opt) $add_opt .="&nbsp; ";

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_category').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_category'])) $_GET['search_addparam_category'] ="";
//        if (
//        $add_opt .="<input type=\"text\" name=\"search_addparam_category\" value=\"".$_GET['search_addparam_category']."\" style=\"width:150px;\">";
                        $add_opt .="<select name=\"search_addparam_category\">";

                       if (!isset($_GET['search_addparam_category']) OR !$_GET['search_addparam_category'] OR $_GET['search_addparam_category']=="") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"\">-- ".OW::getLanguage()->text('search', 'select')." --</option>";

                        $sql2 = "SELECT * FROM " . OW_DB_PREFIX. "map_category WHERE active='1' ORDER BY name ";
                        $arr2 = OW::getDbo()->queryForList($sql2);
                        foreach ( $arr2 as $value2 )
                        {
                            if ($_GET['search_addparam_category']>0 AND $value2['id']==$_GET['search_addparam_category']) $sel=" selected ";
                                else $sel=" ";
                            $add_opt .="<option ".$sel." value=\"".$value2['id']."\">".stripslashes($value2['name'])."</option>";

                                $sql3 = "SELECT * FROM " . OW_DB_PREFIX. "map_category WHERE active='1' AND id2='".addslashes($value2['id'])."' ORDER BY name ";
                                $arr3 = OW::getDbo()->queryForList($sql3);
                                foreach ( $arr3 as $value3 )
                                {
                                    if ($_GET['search_addparam_category']>0 AND $value3['id']==$_GET['search_addparam_category']) $sel=" selected ";
                                        else $sel=" ";
                                    $add_opt .="<option ".$sel." value=\"".$value3['id']."\">&nbsp;&nbsp;&nbsp;-".stripslashes($value3['name'])."</option>";
                                }



                        }
                        $add_opt .="</select>";

$add_opt .="</div>
</div>";






                $content_f="";
                if ($add_opt){
                    $content_f .="<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\">";
                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
                    $content_f .="&nbsp;";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<br/>";
//                    $content_f .=$add_opt;
                    $content_f .="<div clas=\"clearfix\" style=\"margin-top:10px;min-height:0;\">".$add_opt."</div>";
                    $content_f .="<hr/>";
                    $content_f .="</form>";
                }
                $add_opt =$content_f;

            }else if ($option=="news"){//------------news

                        if ($add_opt) $add_opt .="&nbsp; ";

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_category').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_category'])) $_GET['search_addparam_category'] ="";
//        if (
//        $add_opt .="<input type=\"text\" name=\"search_addparam_category\" value=\"".$_GET['search_addparam_category']."\" style=\"width:150px;\">";
                        $add_opt .="<select name=\"search_addparam_category\">";

                       if (!isset($_GET['search_addparam_category']) OR !$_GET['search_addparam_category'] OR $_GET['search_addparam_category']=="") $sel=" selected ";
                            else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"\">-- ".OW::getLanguage()->text('search', 'select')." --</option>";

                        $sql2 = "SELECT * FROM " . OW_DB_PREFIX. "news_topics WHERE active='1' ORDER BY t_name ";
                        $arr2 = OW::getDbo()->queryForList($sql2);
                        foreach ( $arr2 as $value2 )
                        {
                            if ($_GET['search_addparam_category']>0 AND $value2['idt']==$_GET['search_addparam_category']) $sel=" selected ";
                                else $sel=" ";
                            $add_opt .="<option ".$sel." value=\"".$value2['idt']."\">".stripslashes($value2['t_name'])."</option>";
/*
                                $sql3 = "SELECT * FROM " . OW_DB_PREFIX. "news_topics WHERE active='1' AND id2='".addslashes($value2['idt'])."' ORDER BY name ";
                                $arr3 = OW::getDbo()->queryForList($sql3);
                                foreach ( $arr3 as $value3 )
                                {
                                    if ($_GET['search_addparam_category']>0 AND $value3['idt']==$_GET['search_addparam_category']) $sel=" selected ";
                                        else $sel=" ";
                                    $add_opt .="<option ".$sel." value=\"".$value3['idt']."\">&nbsp;&nbsp;&nbsp;-".stripslashes($value3['t_name'])."</option>";
                                }
*/


                        }
                        $add_opt .="</select>";

$add_opt .="</div>
</div>";



                $content_f="";
                if ($add_opt){
                    $content_f .="<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\">";
                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
                    $content_f .="&nbsp;";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<br/>";
//                    $content_f .=$add_opt;
                    $content_f .="<div clas=\"clearfix\" style=\"margin-top:10px;min-height:0;\">".$add_opt."</div>";
                    $content_f .="<hr/>";
                    $content_f .="</form>";
                }
                $add_opt =$content_f;

/*
            }else if ($option=="map"){//------------map

                        if ($add_opt) $add_opt .="&nbsp; ";
//                        $add_opt .=OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').": ";
//                        $add_opt .="<input type=\"text\" name=\"search_text[".$value['name']."]\" value=\"".$_GET['search_text'][$value['name']]."\" style=\"width:150px;\">";


//a_city  a_postcode      a_street        a_email a_url   a_phone a_fax   a_country

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_city').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_city'])) $_GET['search_addparam_city'] ="";
        $add_opt .="<input type=\"text\" name=\"search_addparam_city\" value=\"".$_GET['search_addparam_city']."\" style=\"width:150px;\">";
$add_opt .="</div>
</div>";

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_street').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_street'])) $_GET['search_addparam_street'] ="";
        $add_opt .="<input type=\"text\" name=\"search_addparam_street\" value=\"".$_GET['search_addparam_street']."\" style=\"width:150px;\">";
$add_opt .="</div>
</div>";

$add_opt .="<div class=\"clearfix ow_boxx\" style=\"padding-right: 5px;margin:auto;min-width:100px;display:inline-block;\">";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .="<b>".OW::getLanguage()->text('search', 'addparam_country').":</b><br/>";
    $add_opt .="</div>";
    $add_opt .="<div class=\"ow_ipc_header clearfix\" style=\"min-height: 29px;\">";
        if (!isset($_GET['search_addparam_country'])) $_GET['search_addparam_country'] ="";
        $add_opt .="<input type=\"text\" name=\"search_addparam_country\" value=\"".$_GET['search_addparam_country']."\" style=\"width:150px;\">";
$add_opt .="</div>
</div>";


                $content_f="";
                if ($add_opt){
                    $content_f .="<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\">";
                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
                    $content_f .="&nbsp;";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
                    $content_f .="<hr/>";
                    $content_f .=$add_opt;
                    $content_f .="<hr/>";
                    $content_f .="</form>";
                }
                $add_opt =$content_f;

*/

            }else{//-----------------------------------all other without params
//                    $content_f="";
                    $content_f .="<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\">";
                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
                    $content_f .="&nbsp;";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<hr/>";
//                    $content_f .=$add_opt;
                    $content_f .="<div clas=\"clearfix\" style=\"margin-top:10px;min-height:0;\">&nbsp;</div>";
//                    $content_f .="<hr/>";
                    $content_f .="</form>";
                $add_opt =$content_f;
            }

        }else{//if header_add
//                    $content_f="";
                    $content_f .="<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\">";
                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
                    $content_f .="&nbsp;";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<hr/>";
//                    $content_f .=$add_opt;
                    $content_f .="<div clas=\"clearfix\" style=\"margin-top:10px;min-height:0;\">&nbsp;</div>";
//                    $content_f .="<hr/>";
                    $content_f .="</form>";
                $add_opt =$content_f;
        }




/*
        if ($id_user>0 OR $is_admin){
//        if ($is_admin){
            if ($selected=="basket") $sel=" active ";
                else $sel="";
    $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."basket/showbasket\"><span class=\"ow_ic_cart\">".OW::getLanguage()->text('search', 'product_table_showbasket')."</span></a></li>";//moje zamówienia
        }
*/

/*
        if ($id_user>0 OR $is_admin){
            if ($selected=="myitems") $sel=" active ";
                else $sel="";
    $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."shopmy/show\"><span class=\"ow_ic_plugin\">".OW::getLanguage()->text('search', 'product_table_showmyitems')."</span></a></li>";//moje zamówienia
        }
*/

//        $content_t .="<li class=\"_plugin \"><a href=\"http://www.oxwall.org/store\"><span class=\"ow_ic_plugin\">Plugins</span></a></li>";
//        $content_t .="<li class=\"_theme \"><a href=\"http://www.oxwall.org/store/themes\"><span class=\"ow_ic_plugin\">Themes</span></a></li>";
//        $content_t .="<li class=\"_store_purchase_list \"><a href=\"http://www.oxwall.org/store/granted-list\"><span class=\"ow_ic_cart\">My purchases</span></a></li>";
//        $content_t .="<li class=\"_store_my_items  active\"><a href=\"http://www.oxwall.org/store/list/my-items\"><span class=\"ow_ic_plugin\">My items</span></a></li>";
//        $content_t .="<li class=\"_store_dev_tools \"><a href=\"http://www.oxwall.org/store/dev-tools\"><span class=\"ow_ic_gear_wheel\">Developer tools</span></a></li>";


/*
//        if (($id_user>0 AND OW::getConfig()->getValue('search', 'mode_membercanshell')==1) OR $is_admin){
        if ($id_user>0 AND $is_admin){
            if ($selected==10 OR $selected=="admin") $sel=" active ";
                else $sel="";
            $content_t .="<li class=\"_store_dev_tools ".$sel."\"><a href=\"".$curent_url."ordershop/showorder\"><span class=\"ow_ic_gear_wheel\">".OW::getLanguage()->text('search', 'product_table_showorder')."</span></a></li>";
        }
*/


/*
        if (OW::getConfig()->getValue('search', 'mode_ads_approval') AND $is_admin AND $is_admin){
//            if ($id_user>0 AND $is_admin){
                if ($selected==11 OR $selected=="approval") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_dev_tools ".$sel."\"><a href=\"".$curent_url."ordershop/approval\"><span class=\"ow_ic_gear_wheel\">".OW::getLanguage()->text('search', 'product_table_approvedlist')."</span></a></li>";
//            }
        }
*/


//                                if ($is_admin OR ($id_user>0 AND OW::getConfig()->getValue('search', 'mode_membercanshell')==1) ){
//                                    $content .="&nbsp;|&nbsp;";
/*                
            if ($selected=="addnewproduct") $sel=" active ";
                else $sel="";
            $content_t .="<li class=\"_store_dev_tools ".$sel."\"><a href=\"".$curent_url."product/0/add\"><span class=\"ow_ic_add\">".OW::getLanguage()->text('search', 'product_table_selyourproduct')."</span></a></li>";
*/

/*
                                    $content .="<a href=\"".$curent_url."product/0/add\" title=\"".OW::getLanguage()->text('search', 'product_table_addnewproduct')."\">";
//                                    $content .= "[+]";
                                $content .= "<strong>";
                                $content .= "[+]";
                                $content .=OW::getLanguage()->text('search', 'product_table_selyourproduct'); 
                                $content .="</strong>";
                                    $content .="</a>";
*/
//                                }


$content_t .="</ul>";
$content_t .="</div>";
        $content_t .=$content;
        $content_t .="</div>";

        if ($add_opt){
            $content_t .="<div class=\"ow_content\" style=\"min-height:0;vertical-align:top;\">";
/*
<form id="searchform" metod="get" action="http://test.a6.pl/query" style="display:inline;">
<input style="position:relative;display:inline-block;float:left;left:3px;top:1px;width:310px;font-size:120%;" type="text" id="query" name="query" value="Search people, places, and more..." onblur="if(this.value == '') { this.value='Search people, places, and more...'};" onfocus="if (this.value == 'Search people, places, and more...') {this.value=''};" autocomplete="off" spellcheck="false">
</form>
*/
            $content_t .=$add_opt;

            $content_t .="</div>";
        }
        return $content_t;
    }



    public function pagination($curent_page=0,$allpages=0,$prev_page=0,$next_page=0,$module="",$url_pages=""){

        $curent_url=OW_URL_HOME."query".DS;

//        if (!isset($url_pages)) $url_pages="";
//            return SEARCH_BOL_Service::getInstance()->makePagination(($curent_page+1), $allpages, OW::getConfig()->getValue('shoppro', 'mode_perpage'), 1, $curent_url."shop?".$url_pages,"&page=");



        $pagination ="";
    if (isset($module)){
    $pagination .="<div class=\"clearfix\" >";

$pagination .="<table style=\"float:right;\"  style=\"\">";
$pagination .="<tr>";
$pagination .="<td style=\"width:45px;height:20px;\">";
if ($prev_page>0){
//    $pagination .="<a style=\"width:100%;\" class=\"ow_add_content ow_alt1 ow_ic_left_arrow\" href=\"".$curent_url."shop?page=".$prev_page.$url_pages."\" title=\"".OW::getLanguage()->text('shoppro', 'product_table_prev')."\"></a>";
//--b start
    $pagination .="<div class=\"ow_stdmargin  clearfix\"  style=\"height:15px;margin-bottom:30px;padding:0;\">";
    $pagination .="<a style=\"padding: 5px;margin:5px;width:45px;height:25px;display:inline-block;background-position: center center;\" class=\"ow_add_content ow_ic_left_arrow\" href=\"".$curent_url.$module."?page=".$prev_page.$url_pages."\" title=\"".OW::getLanguage()->text('search', 'product_table_prev')."\"></a>";
    $pagination .="</div>";
//--b end
}else{
//    $pagination .="<a style=\"width:100%;\" disabled=\"true\" class=\"ow_add_content ow_alt2 ow_ic_left_arrow\" href=\"#\" title=\"".OW::getLanguage()->text('shoppro', 'product_table_prev')."\"></a>";
//--b start
    $pagination .="<div class=\"ow_stdmargin  clearfix\"  style=\"height:15px;margin-bottom:30px;padding:0;\">";
    $pagination .="<a style=\"padding: 5px;margin:5px;width:45px;height:25px;display:inline-block;background-position: center center;\" class=\"ow_add_content ow_ic_left_arrow ow_center\" href=\"".$curent_url.$module."?page=0".$url_pages."\" title=\"".OW::getLanguage()->text('search', 'product_table_prev')."\"></a>";
    $pagination .="</div>";
//--b end
}



$pagination .="</td>";

$pagination .="<td style=\"min-width:5px;height:20px;text-align:center;padding:0 5px 10px 5px;vertical-align: middle;\" valign=\"middle\">";
$pagination .="-".($curent_page+1)."-";
$pagination .="</td>";

$pagination .="<td style=\"width:45px;height:20px;\">";
if ($next_page>0){
//    $pagination .="<a style=\"width:100%;\" class=\"ow_add_content ow_alt1 ow_ic_right_arrow\" href=\"".$curent_url."shop?page=".$next_page.$url_pages."\" title=\"".OW::getLanguage()->text('shoppro', 'product_table_next')."\"></a>";
//--b start
    $pagination .="<div class=\"ow_stdmargin  clearfix\" style=\"height:15px;margin-bottom:30px;padding:0;\">";
    $pagination .="<a style=\"padding: 5px;margin:5px;width:45px;height:25px;display:inline-block;background-position: center center;\" class=\"ow_add_content ow_ic_right_arrow\" href=\"".$curent_url.$module."?page=".$next_page.$url_pages."\" title=\"".OW::getLanguage()->text('search', 'product_table_next')."\"></a>";
    $pagination .="</div>";
//--b end
}else{
//    $pagination .="<a style=\"width:100%;\" class=\"ow_add_content ow_alt1 ow_ic_right_arrow\" href=\"#\" title=\"".OW::getLanguage()->text('shoppro', 'product_table_next')."\"></a>";
//--b start
    $pagination .="<div class=\"ow_stdmargin  clearfix\"  style=\"height:15px;margin-bottom:30px;padding:0;\">";
    $pagination .="<a style=\"padding: 5px;margin:5px;width:45px;height:25px;display:inline-block;background-position: center center;\" class=\"ow_add_content ow_ic_right_arrow\" href=\"#\" title=\"".OW::getLanguage()->text('search', 'product_table_next')."\"></a>";
    $pagination .="</div>";
//--b end
}

$pagination .="</td>";
$pagination .="</tr>";
$pagination .="</table>";

    $pagination .="</div>";
        }//if $module
        return $pagination;
    }
















}