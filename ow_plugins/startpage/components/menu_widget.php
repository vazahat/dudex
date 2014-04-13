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


class ONLINE_CMP_MenuWidget extends BASE_CLASS_Widget
{

    public function __construct( BASE_CLASS_WidgetParameter $params )
    {
        parent::__construct();

//        $service = PostService::getInstance();

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
$this->feed_working_Id=$params->additionalParamList['entityId'];//set wotching user id		
		

        $posts = array();

        $userService = BOL_UserService::getInstance();
        $postIdList = array();
        $commentInfo = array();



        $this->assign('commentInfo', $commentInfo);
        $this->assign('list', $posts);





    }

    public static function getSettingList()
    {

        $options = array();

        for ( $i = 3; $i <= 10; $i++ )
        {
            $options[$i] = $i;
        }

        $settingList['count'] = array(
            'presentation' => self::PRESENTATION_SELECT,
            'label' => OW::getLanguage()->text('online', 'cmp_widget_post_count'),
            'optionList' => $options,
            'value' => 3,
        );
        $settingList['previewLength'] = array(
            'presentation' => self::PRESENTATION_TEXT,
            'label' => OW::getLanguage()->text('online', 'blog_widget_preview_length_lbl'),
            'value' => 50,
        );

        return $settingList;
    }

    public static function getStandardSettingValueList()
    {
        $list = array(
            self::SETTING_TITLE => OW::getLanguage()->text('online', 'main_menu_item'),
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
$curent_url=OW_URL_HOME;
$is_admin = OW::getUser()->isAdmin();

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
        if ($this->userId==$this->feed_working_Id){//is owner
		 
            //$content .="<a href=\"online/editpage/0\"><b style=\"color:#080;\">[+] ADD NEW PAGE</b></a><a name=\"add_new_page\"></a>";
			$content .="<a href=\"".$curent_url."online/editpage/0\">";
//                        $content .="<b style=\"color:#080;\">[+] ".OW::getLanguage()->text('online', 'main_menu_addpage')."</b>";

            $content .="<div class=\"clearfix ow_submit ow_smallmargin\">
                <div class=\"ow_center\">
                    <span class=\"ow_button\">
                        <span class=\" ow_positive\">
                            <input type=\"submit\" name=\"ok\" value=\"".OW::getLanguage()->text('online', 'main_menu_addpage')."\" class=\"ow_ic_add ow_positive\">
                        </span>
                    </span>
                </div>
            </div>";

                        $content .="</a>";
                        $content .="<a name=\"add_new_page\"></a>";
            $content .="<hr>";
        }



        if ($this->feed_working_Id>0){
            $menu_list="";
            $rateDao = BOL_RateDao::getInstance();
    	
            $query = "SELECT * FROM " . OW_DB_PREFIX. "online  
                        WHERE id_owner  = '".addslashes($this->feed_working_Id)."' ";
            //$arr = OW::getDbo()->queryForList($query, array('id' => (int) $id, 'order_main' => (int) $order_main, 'title' => $title, 'url_external' => $url_external, 'content' => $content));
			$arr = OW::getDbo()->queryForList($query);
        
            $resultArray = array();
//$pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.$plname.DS;
$pluginStaticDir =OW::getPluginManager()->getPlugin('online')->getStaticUrl();
            foreach ( $arr as $value )
            {
$seo_title=stripslashes($value['title']);
$seo_title=str_replace(" ","_",$seo_title);
$seo_title = preg_replace('/[^(\x20-\x7F)\x0A]*/','', $seo_title);
$seo_title =strtolower($seo_title);


            if ($this->userId==$this->feed_working_Id){//is owner
                $del_link="&nbsp;<a href=\"".$curent_url."online/editpage/".$value['id']."/?delit=true\" onclick=\"return confirm('Are you sure you want to delete?');\" title=\"Delete this page\">";
//                $del_link="<b style=\"color:#f00;\">[-]</b>";
                $del_link .="<img src=\"".$pluginStaticDir."delete.gif\">";
                $del_link .="</a>&nbsp;";

                $edit_link ="<a href=\"".$curent_url."online/editpage/".$value['id']."\" title=\"Edit this page\">";
//                $edit_link="<b style=\"color:#080;\">[*]</b>";
                $edit_link .="<img src=\"".$pluginStaticDir."edit3.gif\">";
                $edit_link .="</a>";
            }else if ($is_admin){
                $del_link="&nbsp;<a href=\"".$curent_url."online/editpage/".$value['id']."/?delit=true\" onclick=\"return confirm('Are you sure you want to delete?');\" title=\"Delete this page\">";
//                $del_link .="<b style=\"color:#f00;\">[-]</b>";
                $del_link .="<img src=\"".$pluginStaticDir."delete.gif\">";
                $del_link .="</a>";
            }


//                $menu_list .="<li><a href=\"".$curent_url."/online/".$this->feed_working_Id."/".$value['id']."/".$seo_title.".wall\" alt=\"".stripslashes($value['title'])."\" title=\"".stripslashes($value['title'])."\">".stripslashes($value['title'])."</a>".$del_link.$edit_link."</li>";
                $menu_list .="<li>".$edit_link.$del_link."<a href=\"".$curent_url."/online/".$this->feed_working_Id."/".$value['id']."/".$seo_title.".online\" alt=\"".stripslashes($value['title'])."\" title=\"".stripslashes($value['title'])."\">".stripslashes($value['title'])."</a></li>";
//            echo $value['title'];
//            echo "<br>";
//            echo $value['content'];
            }

            if ($menu_list){
                $content .="<ul>".$menu_list."</ul>";
            }else{
if ($this->userId!=$this->feed_working_Id){//is NOT owner
$content  ="<script>\n";
$content .="\$(document).ready(function(){\n";
$content .="\$(\".profile-NOTEPAD_CMP_MenuWidget\").empty().remove();\n";
$content .="    });\n";
$content .="</script>";
}
            }
        }else{
if ($this->userId!=$this->feed_working_Id){//is NOT owner
$content  ='<script>\n';
$content .='$(document).ready(function(){\n';
$content .='$(".profile-NOTEPAD_CMP_MenuWidget").empty().remove();\n';
$content .='    });\n';
$content .='</script>';
}
        }
        $this->assign('content', $content);
	}
	
}

