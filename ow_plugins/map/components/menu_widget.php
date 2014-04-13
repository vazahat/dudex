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


class MAP_CMP_MenuWidget extends BASE_CLASS_Widget
{

    public function __construct( BASE_CLASS_WidgetParameter $params )
    {
        parent::__construct();

//        $service = PostService::getInstance();
//
//        $count = $params->customParamList['count'];
//        $previewLength = $params->customParamList['previewLength'];

//        $list = $service->findList(0, $count);
/*
        if ( empty($list) && !OW::getUser()->isAuthorized('shoppro', 'add') && !$params->customizeMode )
        {
            $this->setVisible(false);

            return;
        }
*/
		
$this->userId = OW::getUser()->getId();//citent login user (uwner)
//$this->feed_working_Id=$params->additionalParamList['entityId'];//set wotching user id		
		

        $posts = array();

        $userService = BOL_UserService::getInstance();

        $postIdList = array();
/*
        foreach ( $list as $dto )
        {
            

            if ( mb_strlen($dto->getTitle()) > 50 )
            {
                $dto->setTitle(UTIL_String::splitWord(UTIL_String::truncate($dto->getTitle(), 50, '...')));
            }
            $text = $service->processPostText($dto->getPost());

            $posts[] = array(
                'dto' => $dto,
                'text' => UTIL_String::splitWord(UTIL_String::truncate($text, $previewLength)),
                'truncated' => ( mb_strlen($text) > $previewLength ),
            );

            $idList[] = $dto->getAuthorId();
            $postIdList[] = $dto->id;
        }
*/
        $commentInfo = array();



        $this->assign('commentInfo', $commentInfo);
        $this->assign('list', $posts);




    }

    public static function getSettingList()
    {

        $options = array();
$settingList = array();
/*
        for ( $i = 3; $i <= 10; $i++ )
        {
            $options[$i] = $i;
        }

        $settingList['count'] = array(
            'presentation' => self::PRESENTATION_SELECT,
            'label' => OW::getLanguage()->text('pages', 'cmp_widget_post_count'),
            'optionList' => $options,
            'value' => 3,
        );
        $settingList['previewLength'] = array(
            'presentation' => self::PRESENTATION_TEXT,
            'label' => OW::getLanguage()->text('pages', 'blog_widget_preview_length_lbl'),
            'value' => 50,
        );
*/
        return $settingList;
    }

    public static function getStandardSettingValueList()
    {
        $list = array(
            self::SETTING_TITLE => OW::getLanguage()->text('pages', 'main_menu_item'),
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_ICON => 'ow_ic_write'
        );

        return $list;
    }

    public static function getAccess()
    {
        return self::ACCESS_ALL;
    }
	
    public function onBeforeRender() // The standard method of the component that is called before rendering
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


        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"])) {$pageURL .= "s";}
        $pageURL .= "://";
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];


         $content="";
/*
        if (!$this->userId){
            echo "UKNOW!!";
        }else if ($this->userId==$this->feed_working_Id){
            echo "OWNER!!!!";
        }else{
            echo "GUEST!!!";
        }
*/
        $del_link="";
//        if ($this->userId==$this->feed_working_Id){//is owner
        if ($is_admin){//is owner
            //$content .="<a href=\"html/editpage/0\"><b style=\"color:#080;\">[+] ADD NEW PAGE</b></a><a name=\"add_new_page\"></a>";
	    $content .="<a href=\"".$curent_url."pages/editpage/0?returl=".urlencode($pageURL)."\">";
//            $content .="<b style=\"color:#080;\">[+] ".OW::getLanguage()->text('pages', 'main_menu_addpage')."</b>";
            $content .="<div class=\"clearfix ow_submit ow_smallmargin\">
                <div class=\"ow_center\">
                    <span class=\"ow_button\">
                        <span class=\" ow_positive\">
                            <input type=\"submit\" name=\"ok\" value=\"".OW::getLanguage()->text('pages', 'main_menu_addpage')."\" class=\"ow_ic_add ow_positive\">
                        </span>
                    </span>
                </div>
            </div>";
            $content .="</a>";
            $content .="<a name=\"add_new_page\">";
            $content .="</a>";
            $content .="<hr>";
        }




//        if ($this->feed_working_Id>0){
            $menu_list="";
            $rateDao = BOL_RateDao::getInstance();
    	    
            if ($is_admin){
                $add="";
            }else if ($id_user>0 AND !$is_admin){
                $add=" WHERE active='1' AND is_visibleinmenu='1' AND (can_watch_groups='1' OR can_watch_groups='0') ";//for users
            }else {
                $add=" WHERE active='1' AND is_visibleinmenu='1' AND (can_watch_groups='2' OR can_watch_groups='0') ";//for guests
            }
$pluginStaticDir =OW::getPluginManager()->getPlugin('pages')->getStaticUrl();
            $query = "SELECT * FROM " . OW_DB_PREFIX. "pages ".$add ." ORDER BY order_main ASC, title";
	    $arr = OW::getDbo()->queryForList($query);
        
            $resultArray = array();

            foreach ( $arr as $value )
            {
$seo_title=stripslashes($value['title']);
$seo_title=str_replace(" ","_",$seo_title);
$seo_title = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $seo_title);
$seo_title =strtolower($seo_title);
$del_link="";
$edit_link="";
//            if ($this->userId==$this->feed_working_Id){//is owner        
            if ($is_admin){//is owner        
                $del_link="&nbsp;<a href=\"".$curent_url."pages/editpage/".$value['id']."/?delit=true&returl=".urlencode($pageURL)."\" onclick=\"return confirm('Are you sure you want to delete?');\" title=\"Delete this page\">";
//                $del_link .="<b style=\"color:#f00;font-size:10px;\">[-]</b>";
                $del_link .="<img src=\"".$pluginStaticDir."delete.gif\">";
                $del_link .="</a>&nbsp;";


                $edit_link="<a href=\"".$curent_url."pages/editpage/".$value['id']."?returl=".urlencode($pageURL)."\" title=\"Edit this page\">";
//                $edit_link .="<b style=\"color:#080;font-size:10px;\">[*]</b>";
                $edit_link .="<img src=\"".$pluginStaticDir."edit3.gif\">";
                $edit_link .="</a>";
            }

if (!isset($value['is_localurl'])) $value['is_localurl']=0;
if (!isset($value['url_external'])) $value['url_external']="";

                if ($value['is_localurl'] AND $value['url_external']!="" AND $value['url_external']!="http://" AND $value['url_external']!="https://"){
/*
                    $from_config=$curent_url;
                    $from_config=str_replace("https://","",$from_config);
                    $from_config=str_replace("http://","",$from_config);
                    $trash=explode($from_config,$curent_url);
                    $url_detect=$trash[1];
*/                  
                    $linkpage_url=$value['url_external'];
                }else{
                    $linkpage_url=$curent_url."page/".$value['id']."/".$seo_title.".html";
                }

                $link_text=mb_substr(stripslashes($value['title']),0,40);
                if (strlen(stripslashes($value['title']))>40) $link_text .="..";
                $menu_list .="<li>";
                $menu_list .=$edit_link.$del_link;
                $menu_list .="<a href=\"".$linkpage_url."\" alt=\"".stripslashes($value['title'])."\" title=\"".stripslashes($value['title'])."\">".$link_text."</a>";
                $menu_list .="</li>";
//            echo $value['title'];
//            echo "<br>";
//            echo $value['content'];
            }

            if ($menu_list){
                $content .="<ul>".$menu_list."</ul>";
            }else{
/*
if ($this->userId!=$this->feed_working_Id){//is NOT owner
$content  ="<script>\n";
$content .="\$(document).ready(function(){\n";
$content .="\$(\".profile-MAP_CMP_MenuWidget\").empty().remove();\n";
$content .="    });\n";
$content .="</script>";
}
*/
            }
//        }else{
/*
if ($this->userId!=$this->feed_working_Id){//is NOT owner
$content  ='<script>\n';
$content .='$(document).ready(function(){\n';
$content .='$(".profile-MAP_CMP_MenuWidget").empty().remove();\n';
$content .='    });\n';
$content .='</script>';
}
*/
//        }
        $this->assign('content', $content);
	}
	
}

