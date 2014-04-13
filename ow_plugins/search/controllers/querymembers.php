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


class SEARCH_CTRL_Querymembers extends OW_ActionController 
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

        if ( OW::getPluginManager()->isPluginActive('cms') ){
            $plunin_installed['cms']=true;
        }else{
            $plunin_installed['cms']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('forum') ){
            $plunin_installed['forum']=true;
        }else{
            $plunin_installed['forum']=false;
        }

        if ( OW::getPluginManager()->isPluginActive('links') ){
            $plunin_installed['links']=true;
        }else{
            $plunin_installed['links']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('video') ){
            $plunin_installed['video']=true;
        }else{
            $plunin_installed['video']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('photo') ){
            $plunin_installed['photo']=true;
        }else{
            $plunin_installed['photo']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('shoppro') ){
            $plunin_installed['shoppro']=true;
        }else{
            $plunin_installed['shoppro']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('classifiedspro') ){
            $plunin_installed['classifiedspro']=true;
        }else{
            $plunin_installed['classifiedspro']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('pages') ){
            $plunin_installed['pages']=true;
        }else{
            $plunin_installed['pages']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('groups') ){
            $plunin_installed['groups']=true;
        }else{
            $plunin_installed['groups']=false;
        }
        if ( OW::getPluginManager()->isPluginActive('blogs') ){
            $plunin_installed['blogs']=true;
        }else{
            $plunin_installed['blogs']=false;
        }
//echo OW::getPluginManager()->isPluginActive('event');exit;
        if ( OW::getPluginManager()->isPluginActive('event') ){
            $plunin_installed['event']=true;
        }else{
            $plunin_installed['event']=false;
        }

        if ( OW::getPluginManager()->isPluginActive('fanpage') ){
            $plunin_installed['fanpage']=true;
        }else{
            $plunin_installed['fanpage']=false;
        }

        if ( OW::getPluginManager()->isPluginActive('html') ){
            $plunin_installed['html']=true;
        }else{
            $plunin_installed['html']=false;
        }
//games
        if ( OW::getPluginManager()->isPluginActive('games') ){
            $plunin_installed['games']=true;
        }else{
            $plunin_installed['games']=false;
        }

        if ( OW::getPluginManager()->isPluginActive('adsense') ){
            $plunin_installed['adsense']=true;
        }else{
            $plunin_installed['adsense']=false;
        }

//mochigames_item
        if ( OW::getPluginManager()->isPluginActive('mochigames') ){
            $plunin_installed['mochigames']=true;
        }else{
            $plunin_installed['mochigames']=false;
        }
/*
        if ( OW::getPluginManager()->isPluginActive('basepages') ){//TODO...
            $plunin_installed['basepages']=true;
        }else{
            $plunin_installed['basepages']=false;
        }
*/

        if ( OW::getPluginManager()->isPluginActive('adspro') ){
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                    $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
                                    if ($uimg){
                                        $tabt .="<a href=\"".$uurl."\" style=\"display:inline;color:#008;font-size:14px;font-weight:bold;\">";
                                        $tabt .="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                                        $tabt .="</a>";
                                    }else{
                                        $tabt .="<a href=\"".$uurl."\" style=\"display:inline;color:#008;font-size:14px;font-weight:bold;\">";
                                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                                        $tabt .="</a>";
                                    }
                                    $tabt .="</div>";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;;float:left;min-height:40px;min-width:230px;max-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$uurl."\" style=\"display:inline;color:#008;font-size:14px;font-weight:bold;\">";
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
                            $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/user?query=".$query."\">";
                            $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_user')."</b>";
                            $tab .="</a>";
                                $tab .="<div style=\"float:right;margin-top:5px;\">";
                                    $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/user?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
                                $tab .="</div>";
                            $tab .="</div>";                         
                            $tab .=$tabt;
                        }


//echo $curent_result."--".$maxallresults;
                        if ($plunin_installed['pages'] AND ($curent_result<$maxallresults)) {
                            $limit=$limit_results;
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "pages WHERE active='1' AND (title LIKE '%".addslashes($query)."%' OR LOWER(title) LIKE '%".addslashes(strtolower($query))."%') ORDER BY id DESC LIMIT ".$limit;
                            $arr2 = OW::getDbo()->queryForList($sql);
                            $tabt="";
                            foreach ( $arr2 as $value )
                            {
                                if ($curent_result<$maxallresults){
                                    $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                        $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                        $tabt .="<a href=\"".$curent_url."page/".$value['id']."/index.html\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/pages?query=".$query."\">";
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
                            $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
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
                                $tabt .="<a href=\"".$curent_url."product/".$value['id']."/zoom/index.html\" title=\"".stripslashes($value['name'])."\" style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/shoppro?query=".$query."\">";
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
                            $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
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
                                $tabt .="<a href=\"".$curent_url."classifieds/".$value['id']."/zoom/index.html\" title=\"".stripslashes($value['name'])."\" style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/classifiedspro?query=".$query."\">";
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;text-align:left;\">";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                if ($value['id_page']>0){
                                    $tabt .="<a href=\"".$curent_url."pb/".$value['id_block']."/".$value['id_page']."/index.html\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
                                }else{
                                    $tabt .="<a href=\"".$curent_url."pg/".$value['id_block']."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/cms?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_cms')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/forum?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                $tabt .="<a href=\"".$curent_url."forum/topic/".$value['id']."\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/forum?query=".$query."\">";
                                $tab .="<b>".OW::getLanguage()->text('search', 'main_yousearchoption_forum')."</b>";
                                $tab .="</a>";
                                    $tab .="<div style=\"float:right;margin-top:5px;\">";
                                        $tab .="<a style=\"display: inline;height:10px;font-size: 8px;margin:0 2px 0 2px;padding:0 3px 0px 3px;\" class=\"ow_lbutton\" href=\"".$curent_url."query/forum?query=".$query."\">".OW::getLanguage()->text('search', 'more')."</a>";
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
                            $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
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
                                $tabt .="<a href=\"".$curent_url."link/".$value['id']."\" title=\"".stripslashes($value['title'])."\"   style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\"  >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/links?query=".$query."\">";
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
                                        $unique_id=$matches[2][0];
                                    }
                                    $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
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
                                $tabt .="<a href=\"".$curent_url."video/view/".$value['id']."\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/video?query=".$query."\">";
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
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
                                $tabt .="<a href=\"".$curent_url."photo/view/".$value['id']."\" title=\"".stripslashes($value['description'])."\" style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\">";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/photo?query=".$query."\">";
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."groups/".$value['id']."\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/groups?query=".$query."\">";
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."blogs/".$value['id']."\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/blogs?query=".$query."\">";
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."event/".$value['id']."\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/event?query=".$query."\">";
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."fanpage/".$value['fanpage_url_name']."\" title=\"".stripslashes($value['title_fan_page'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/fanpage?query=".$query."\">";
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."html/".$value['id_owner']."/".$value['id']."/index.html\" title=\"".stripslashes($value['title'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/html?query=".$query."\">";
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";

                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
                                if ($value['thumbal']){
                                    $tabt .="<a href=\"".$curent_url."games/".$value['id']."_".$value['id_cats']."/index.html\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
                                    $tabt .="<img src=\"".stripslashes($value['thumbal'])."\" title=\"".stripslashes($value['name'])."\" width=\"45px\"  style=\"border:0;margin:5px;align:leftX;display:block;width:45px;\" >";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                }
                                $tabt .="</div>";

//json_decode($this->data['control_scheme'], true);
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."games/".$value['id']."_".$value['id_cats']."/index.html\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/games?query=".$query."\">";
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."adsense\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."adsense\">";
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
                                $tabt .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"margin:3px;width:300px;border-bottom:1px solid #fee;display:block;height:50px;\">";

                                $addinfo = json_decode($value['json'], true);
//print_r($addinfo);exit;
//echo "--".$addinfo['games']['0']['thumbnail_url'];
                                $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";
                                if ($addinfo['games']['0']['thumbnail_url']){
                                    $tabt .="<a href=\"".$curent_url."mochigames/".$value['game_tag']."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
                                    $tabt .="<img src=\"".stripslashes($addinfo['games']['0']['thumbnail_url'])."\" title=\"".stripslashes($value['name'])."\" width=\"45px\"  style=\"border:0;margin:5px;align:leftX;display:block;width:45px;\" >";
                                    $tabt .="</a>";
                                }else{
                                    $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                                }
                                $tabt .="</div>";

//json_decode($this->data['control_scheme'], true);
                                    $tabt .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $tabt .="<a href=\"".$curent_url."mochigames/".$value['game_tag']."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;color:#008;font-size:12px;font-weight:bold;\" >";
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
                                $tab .="<a style=\"float:left;max-height:22px;\" href=\"".$curent_url."query/mochigames?query=".$query."\">";
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







                        if ($tab){
                            echo $tab;
                            $tab_end ="";
                            $tab_end .="<div class=\"clearfix\" style=\"width:100%;margin:auto;border-bottom:1px solid #eee;display:block;\">";
                                $tab_end .="<a id=\"search_submit_more\"  href=\"".$curent_url."query/search?query=".$query."\" style=\"display:inline;width:100%;margin:auto;color:#000;\">";
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
                                $tab_end .="<a id=\"search_submit_more\"  href=\"".$curent_url."query/search?query=".$query."\" style=\"display:inline;width:100%;margin:auto;color:#000;\">";
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
        if (isset($_GET['page']) AND $_GET['page']>0){
            $curent_page=$_GET['page'];
        }else{
            $curent_page=0;
        }
//echo $curent_page;
        $start_form=($curent_page*$per_page);
        if (!$start_form) $start_form=0;

        $prev_page=$curent_page-1;
        if ($prev_page<0) $prev_page=0;

        $paging="";
//        $paging=$this->pagination($curent_page=0,$next_page=0,$url_pages="")
//        $paging=$this->pagination();



        $limit_all=10;
        $limit_single=$limit_all;





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

if ($query) $add_paramurl="&query=".$query;

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
for($i=0;$i<count($array_query);$i++){
    if (substr($array_query[$i],0,11)=="search_text" OR substr($array_query[$i],0,10)=="search_sel"){
        if ($add_paramurl_search) $add_paramurl_search .="&";
        $add_paramurl_search .=$array_query[$i];
    }  
}

if ($add_paramurl_search){
    if ($add_paramurl) $add_paramurl .="&".$add_paramurl_search;
        else $add_paramurl ="&".$add_paramurl_search;
}
//print_r(parse_url($_GET));exit;
//print_r($_SERVER['REQUEST_URI']);
//print_r($_SERVER['REQUEST_URI']);
//echo $_SERVER['REQUEST_URI'];

$foundsomething=false;

    if ($id_user>0){
        if (!$option OR $option=="search") $option="user";

        if ($option=="user"){
            $header_add=OW::getLanguage()->text('search', 'main_yousearchoption_user');
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
//if ($query){
    if (isset($_GET['search_text'])) $addsearch=$_GET['search_text'];
        else $addsearch="";

    if (isset($_GET['search_sel'])) $addsearch_sel=$_GET['search_sel'];
        else $addsearch_sel="";

    if ($addsearch){
        foreach($addsearch as $name=> $valx){
            if ($name AND $valx){
                if ($joinonwhere) $joinonwhere .=" AND ";
                $joinonwhere .=" ( ";
                    $joinonwhere .="(bd2.questionName = '".addslashes($name)."' AND (bd2.textValue LIKE '%".addslashes($valx)."%' OR LOWER(bd2.textValue) LIKE '%".addslashes(strtolower($valx))."%')) ";
                    if ($valx>0){
                        $joinonwhere .=" OR ";
                        $joinonwhere .=" (bd2.questionName = '".addslashes($name)."' AND bd2.intValue ='".addslashes($valx)."') ";
                    }
                $joinonwhere .=" ) ";


                if ($joinonleft) $joinonleft .=" AND ";
                $joinonleft .=" (bd2.userId=uss.id AND (`bd2`.`questionName` = '".addslashes($name)."') AND (bd2.textValue LIKE '%".addslashes($valx)."%' OR LOWER(bd2.textValue) LIKE '%".addslashes(strtolower($valx))."%'))  ";

//                if (strlen($query)>1){
//                    if ($joinonleft2) $joinonleft2 .=" AND ";
//                    $joinonleft2 .=" (bd3.userId=uss.id AND (`bd3`.`questionName` = 'realname') AND (bd3.textValue LIKE '".addslashes($valx)."%' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($valx))."%'))  ";
//                }

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
//                $joinon .=" AND (`bd2`.`questionName` = '".addslashes($name)."' AND `bd2`.`intValue` & '".addslashes($valx)."' ) ";

            }
        }
    }

    if ($joinonleft){
        $joinonleft ="LEFT JOIN `" . OW_DB_PREFIX. "base_question_data` `bd2` ON (".$joinonleft.") ";
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
            $joinon="INNER JOIN `" . OW_DB_PREFIX. "base_question_data` `bd` ON ( bd.userId=uss.id  ".$joinon." ) ";
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
                    $joinonleft2_if  =" (bd3.userId=uss.id AND (bd3.textValue LIKE '%".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '%".addslashes(strtolower($query))."%') ) ";
                }else{
                    $joinonleft2_if  =" (bd3.userId=uss.id AND (bd3.textValue LIKE '".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($query))."%') ) ";
                }
            }else{
                $joinonleft2_if  =" (bd3.userId=uss.id AND (bd3.textValue LIKE '".addslashes($query)."' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($query))."') ) ";
            }
        }else{
            if (OW::getConfig()->getValue('search', 'search_force_users')==2){
                $joinonleft2_if  =" (bd3.userId=uss.id AND (`bd3`.`questionName` = 'realname') AND (bd3.textValue LIKE '%".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '%".addslashes(strtolower($query))."%')) ";
            }else{
                $joinonleft2_if  =" (bd3.userId=uss.id AND (`bd3`.`questionName` = 'realname') AND (bd3.textValue LIKE '".addslashes($query)."%' OR LOWER(bd3.textValue) LIKE '".addslashes(strtolower($query))."%')) ";
            }
        }

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

                        $sql = "SELECT uss.*".$joinon_select." FROM " . OW_DB_PREFIX. "base_user uss 
                        ".$joinon." ".$joinonleft." ".$joinonleft2." 
                        WHERE ".$joinonwhere." ".$add_query_search." GROUP BY uss.id ORDER BY uss.joinIp DESC LIMIT ".$limit;
//echo $sql;
//                        WHERE ".$joinonwhere." (uss.username LIKE '".addslashes($query)."%' OR uadd.textValue LIKE '".addslashes($query)."%' OR LOWER(uss.username) LIKE '".addslashes(strtolower($query))."%' OR LOWER(uadd.textValue) LIKE '".addslashes(strtolower($query))."%') ORDER BY uss.joinIp DESC LIMIT ".$limit;

//echo $sql;

//echo BOL_QuestionService::getInstance()->getQuestionData(array(OW::getUser()->getUserObject()->getId()), array('realname'));

//print_r(BOL_QuestionService::getInstance()->getQuestionData(array(OW::getUser()->getUserObject()->getId()), array('relationship')));
//print_r(BOL_QuestionService::getInstance()->getQuestionData(array(OW::getUser()->getUserObject()->getId())));

//echo $sql;
            if ($joinonwhere OR $add_query_search){
                $arr = OW::getDbo()->queryForList($sql);
                $tabt="";
                foreach ( $arr as $value )
                {
                    $dname=BOL_UserService::getInstance()->getDisplayName($value['id']);
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
                        $tabt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                        $tabt .="</a>";
                    }
//BOL_UserService::getInstance()->getDisplayName($userId)
//BOL_UserService::getInstance()->getUserUrl($userId)
                    $tabt .="</td>";
                    $tabt .="<td style=\"margin:auto;\">";
                    $tabt .="<a href=\"".$uurl."\" title=\"".$dname."\" style=\"display:inline;\">";
                    $tabt .=$dname;
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
                if ($tabt) {
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
                        $tab .="<td class=\"ow_ipc_header clearfix\" style=\"margin:auto;\" colspan=\"2\">";
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
                if (!$option){
                    $paging="";
                }else if (!$tabt AND (!$curent_page OR $curent_page==0)) {
                    $paging="";
                }else if ($tabt) {
        //        $paging=$this->pagination($curent_page=0,$next_page=0,$url_pages="")
                    $paging=$this->pagination($curent_page,$prev_page,($curent_page+1),"user",$add_paramurl);
                }else{
                    $paging=$this->pagination($curent_page,$prev_page,0,"user",$add_paramurl);
                }
//echo $paging;
            }//if $joinonwhere." ".$add_query_search


            }



            if ($tab){
                $content .="<table style=\"width:100%;margin:auto;\">";
                $content .=$tab;
                $content .="</table>";
            }





//--------------------------------menu start

//        if (strlen($query)>1){
/*
        if (strlen($query)>1 OR ($query=="*" AND !is_array($addsearch_sel) ) ){
            if (!$option) $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                else  $sel="";
            $menu .="<div class=\"clearfix\"  style=\"margin:auto;\">";
            $menu .="<div class=\"ow_ipc_header clearfix\" style=\"border-right:2px solid #aaa;margin:2px;".$sel."\" >";
            $menu .="<a href=\"".$curent_url."query".$add."\">";
            $menu .=OW::getLanguage()->text('search', 'menu_results_all');
            $menu .="</a>";
            $menu .="</div>";
            $menu .="</div>";
        }
*/
/*
            if ($option=="user") $sel="border-left:4px solid #aaa;font-weight:bold;border-bottom:2px solid #aaa;";
                else  $sel="";
            $menu .="<div class=\"clearfix\"  style=\"margin:auto;\">";
            $menu .="<div class=\"ow_ipc_header clearfix\" style=\"border-right:2px solid #aaa;margin:2px;".$sel."\" >";
//echo $add."--".$query."--";
//if (!$add AND $query) $add="?query=*";
if ($add=="" AND !$query) $add="?query=*";
//else $add="xx";
            $menu .="<a href=\"".$curent_url."query/user".$add."\">";
            $menu .=OW::getLanguage()->text('search', 'menu_results_user');
            $menu .="</a>";
            $menu .="</div>";
            $menu .="</div>";
*/


//        }//if if (strlen($query)>1){
//--------------------------------menu end
//echo "adasD";exit;

    

            $foundsomething=true;
        }else{
            $foundsomething=false;
            $content .=OW::getLanguage()->text('search', 'main_noresultsfound');

//            $content.="<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\">";
///            $content .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
            $content .=$add_opt;
//            $content .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
//            $content .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
            $content .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//            $content .="</form>";
        }

        


    }//if user

            $this->assign('post_url',$curent_url."members/".$option.$add);

$menu=$this->make_options("search","",$option);
        if ($foundsomething){
            if (!isset($plunin_installed)) $plunin_installed="";
            $tabs=$this->make_tabs("search",$plunin_installed,$option);
            if (!$paging AND $option!="" ){
                $this->assign('paging1',OW::getLanguage()->text('search', 'menu_results_nofound'));
                $this->assign('paging2',"");
            }else{
                if ($content){
                    $this->assign('paging1',$paging);
                    $this->assign('paging2',$paging);
                }else{
                $this->assign('paging1',$paging);
                $this->assign('paging2',OW::getLanguage()->text('search', 'menu_results_nofound'));
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
//return;
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
            $query="";
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

//echo "sdfsdF";exit;



                $content_f="";
//                if ($add_opt){
//                    $content_f .="<form metod=\"get\" action=\"".$curent_url."members/".$option.$add."\">";
                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
                    $content_f .="&nbsp;";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<hr/>";
//                    $content_f .=$add_opt;
//                    $content_f .="<hr/>";
//                    $content_f .="</form>";
//                }
                $add_opt =$content_f;

//            }

        }else{//if header_add
//                    $content_f="";
//                    $content_f .="<form metod=\"get\" action=\"".$curent_url."members/".$option.$add."\">";
                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
                    $content_f .="&nbsp;";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<hr/>";
//                    $content_f .=$add_opt;
//                    $content_f .="<hr/>";
//                    $content_f .="</form>";
                $add_opt =$content_f;
        }




$content_t .="</ul>";
$content_t .="</div>";

        $content_t .=$content;
        $content_t .="</div>";

        if ($add_opt){
            $content_t .="<div class=\"ow_content\" style=\"min-height:0;\">";
//            $content_t .="<form metod=\"get\" action=\"".$curent_url."members/".$option.$add."\">";
/*
<form id="searchform" metod="get" action="http://test.a6.pl/query" style="display:inline;">
<input style="position:relative;display:inline-block;float:left;left:3px;top:1px;width:310px;font-size:120%;" type="text" id="query" name="query" value="Search people, places, and more..." onblur="if(this.value == '') { this.value='Search people, places, and more...'};" onfocus="if (this.value == 'Search people, places, and more...') {this.value=''};" autocomplete="off" spellcheck="false">
</form>
*/
            $content_t .=$add_opt;
//            $content_t .="</form>";
            $content_t .="</div>";
        }
        return $content_t;
    }




    public function make_options($selected=1,$plunin_installed="",$option="")
    {
//return;
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
$content="";
$add_opt="";
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
//            $content_t .="<div class=\"ow_content\" style=\"min-height:0;\">";


//$content_t .="<div class=\"ow_content_menu_wrap\">";
//echo "sdfsdF";exit;


//            if ($option=="user"){
                $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_question WHERE onSearch='1' ORDER BY sortOrder ";
                $arr1 = OW::getDbo()->queryForList($sql);
                $enter=1;
                $inline=3;
                foreach ( $arr1 as $value )
                {
//                    $add_opt .=$value['name']."--".$value['type']."-".$value['presentation']."<hr>";
                    if ($value['type']=="select"){
                        if ($add_opt) $add_opt .="&nbsp; ";
$add_opt .="<div class=\"clearfix\" style=\"margin:auto;min-width:100px;display:inline-block;width:100%;\">
    <div class=\"ow_ipc_header clearfix\" style=\"\">";
                        $add_opt .=OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').":<br/>";
                        $add_opt .="<select name=\"search_sel[".$value['name']."]\" style=\"min-width:100px;width:100%;\">";

//                        if (!$_GET['search_sel'][$value['name']]) {
                        if (!isset($_GET['search_sel'][$value['name']])) {
                            $_GET['search_sel'][$value['name']]="";
                            $sel=" selected ";
                        }else $sel=" ";
                        $add_opt .="<option ".$sel." value=\"\">-- ".OW::getLanguage()->text('search', 'select')." --</option>";

                        $sql2 = "SELECT * FROM " . OW_DB_PREFIX. "base_question_value WHERE questionName='".$value['name']."' ORDER BY sortOrder ";
                        $arr2 = OW::getDbo()->queryForList($sql2);
                        foreach ( $arr2 as $value2 )
                        {
                            if ($value2['value']==$_GET['search_sel'][$value['name']]) $sel=" selected ";
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

                        if ($value['type']=="datetime"){

$format=json_decode(stripslashes($value['custom']),true);

//                $date = UTIL_DateTime::parseDate($question['birthdate'], UTIL_DateTime::MYSQL_DATETIME_DATE_FORMAT);
//                $date = UTIL_DateTime::parseDate($value['custom'], UTIL_DateTime::MYSQL_DATETIME_DATE_FORMAT);
//                $age = UTIL_DateTime::getAge($date['year'], $date['month'], $date['day']);


$add_opt .="<div class=\"clearfix\" style=\"margin:auto;min-width:100px;display:inline-block;width:100%;\">
    <div class=\"ow_ipc_header clearfix ow_left\" style=\"width:100%;margin:auto;\">";


//$add_opt .=OW::getLanguage()->text('search', 'questions_question_age').":<br/>";
$add_opt .=OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').":<br/>";

$add_opt .="<div class=\"clearfix ow_center\" style=\"padding:0;\">";
//$add_opt .=OW::getLanguage()->text('search', 'questions_question_age_from').":";
$add_opt .="<select name=\"search_sel[".$value['name']."_from]\" style=\"min-width:20px;\">";
    if (!isset($format['year_range']['from']) OR !$format['year_range']['from']) $sel=" selected ";
        else $sel=" ";
    $add_opt .="<option ".$sel." value=\"\">".OW::getLanguage()->text('search', 'questions_question_age_from')."</option>";
for ($i=$format['year_range']['from'];$i<=$format['year_range']['to'];$i++){
//    if ($value2['value']==$_GET['search_sel'][$value['name']]) $sel=" selected ";
//        else $sel=" ";
//    if ($i==$format['year_range']['from']) $sel=" selected ";
    if (isset($_GET['search_sel'][$value['name']."_from"]) AND $i==$_GET['search_sel'][$value['name']."_from"]) $sel=" selected ";
        else $sel=" ";
    $add_opt .="<option ".$sel." value=\"".$i."\">".$i."</option>";    
}
$add_opt .="</select>";
//$add_opt .="&nbsp;-&nbsp;";
//$add_opt .=OW::getLanguage()->text('search', 'questions_question_age_to').":";
$add_opt .="<select name=\"search_sel[".$value['name']."_to]\" style=\"min-width:20px;\">";
    if (!isset($format['year_range']['to']) OR !$format['year_range']['to']) $sel=" selected ";
        else $sel=" ";
    $add_opt .="<option ".$sel." value=\"\">".OW::getLanguage()->text('search', 'questions_question_age_to')."</option>";
for ($i=$format['year_range']['from'];$i<=$format['year_range']['to'];$i++){
//    if ($value2['value']==$_GET['search_sel'][$value['name']]) $sel=" selected ";
//        else $sel=" ";
    if (isset($_GET['search_sel'][$value['name']."_to"]) AND $i==$_GET['search_sel'][$value['name']."_to"]) $sel=" selected ";
        else $sel=" ";
    $add_opt .="<option ".$sel." value=\"".$i."\">".$i."</option>";    
}
$add_opt .="</select>";
$add_opt .="</div>";
/*
//$add_opt .=print_r($format['year_range'],1);
$add_opt .=$format['year_range']['from'];
$add_opt .="----";
$add_opt .=$format['year_range']['to'];
*/
//        $add_opt .=OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').":<br/>";
//        if (!isset($_GET['search_text'][$value['name']]))  $_GET['search_text'][$value['name']]="";
//        $add_opt .="<input type=\"text\" name=\"search_text[".$value['name']."]\" value=\"".$_GET['search_text'][$value['name']]."\" style=\"min-width:100px;width:100%;\">";
$add_opt .="    </div>
</div>";
                        }
                        if ($value['type']=="text"){

                        if (isset($add_opt)) $add_opt .=" ";
//                        $add_opt .=OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').": ";
//                        $add_opt .="<input type=\"text\" name=\"search_text[".$value['name']."]\" value=\"".$_GET['search_text'][$value['name']]."\" style=\"width:150px;\">";


$add_opt .="<div class=\"clearfix\" style=\"margin:auto;min-width:100px;display:inline-block;width:100%;\">
    <div class=\"ow_ipc_header clearfix\" style=\"\">";
        $add_opt .=OW::getLanguage()->text('base', 'questions_question_'.$value['name'].'_label').":<br/>";
        if (!isset($_GET['search_text'][$value['name']]))  $_GET['search_text'][$value['name']]="";
        $add_opt .="<input type=\"text\" name=\"search_text[".$value['name']."]\" value=\"".$_GET['search_text'][$value['name']]."\" style=\"min-width:100px;width:100%;\">";
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

//                $content_f="";
//                if ($add_opt){
//                    $content_f .="<form metod=\"get\" action=\"".$curent_url."members/".$option.$add."\">";
//                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
//                    $content_f .="&nbsp;";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .=$add_opt;
//                    $content_f .="<hr/>";
//                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .=$add_opt;
//                    $content_f .="<hr/>";
//                    $content_f .="</form>";
//                }
//                $add_opt =$content_f;

//            }



//$content_t .="</div>";

//        $content_t .=$content;
//        $content_t .="</div>";

//            $content_t .="<form metod=\"get\" action=\"".$curent_url."members/".$option.$add."\">";
/*
<form id="searchform" metod="get" action="http://test.a6.pl/query" style="display:inline;">
<input style="position:relative;display:inline-block;float:left;left:3px;top:1px;width:310px;font-size:120%;" type="text" id="query" name="query" value="Search people, places, and more..." onblur="if(this.value == '') { this.value='Search people, places, and more...'};" onfocus="if (this.value == 'Search people, places, and more...') {this.value=''};" autocomplete="off" spellcheck="false">
</form>
*/
            if ($add_opt){
                $content_t .="<div class=\"ow_content\" style=\"min-height:0;min-width:100px;width:165px;\">";
                $content_t .=$add_opt;
                $content_t .="</div>";
            }
//            $content_t .="</form>";

        
        
        return $content_t;
    }





    public function pagination($curent_page=0,$prev_page=0,$next_page=0,$module="",$url_pages=""){
    $curent_url=OW_URL_HOME;
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