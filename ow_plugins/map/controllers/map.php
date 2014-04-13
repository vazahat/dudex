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



class MAP_CTRL_Map extends OW_ActionController
{


    public function indexcheckmobile($params)
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        if (OW::getPluginManager()->isPluginActive('mobille')){
//            $param=explode("?",$_SERVER["REQUEST_URI"]
            if (isset($_GET['mobi'])){
                OW::getApplication()->redirect($curent_url."mobile/v2/option/index?mobi=".$_GET['mobi']);
            }else{
                OW::getApplication()->redirect($curent_url."mobile/v2/option/index");
            }
        }else{
            echo "<div style=\"display:block;padding:0;width:100%;height:90%;min-height:100px;margin:auto;text-align:center;background:#f00;\">";
            echo "<div style=\"display:inline-block;margin:20px;margin-top:50px;font-size:120%;color:#fff;text-align:center;font-weight:bold;\">";
            echo OW::getLanguage()->text('map', 'webservice_dont_support_onlinefunctions').":<br/>".$curent_url;
            echo "</div>";
            echo "</div>";
        }
        exit;
    }

    public function indexshowall($params)
    {
        $content="";
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $menu = BASE_CTRL_UserList::getMenu('map');
//        $this->addComponent('menu', $menu);
        $content .=$menu->render();
        $language = OW::getLanguage();
//        $this->setPageHeading($language->text('map', 'map_page_heading'));
//        $this->setPageHeadingIconClass('ow_ic_bookmark');

        $content .="<div class=\"clearfix\" style=\"margin-bottom:20px;\">".MAP_BOL_Service::getInstance()->get_profile_map('all',$id_user)."</div>";
        $this->assign('content', $content);
    }

    public function indexshowfriends($params)
    {
        $content="";
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $menu = BASE_CTRL_UserList::getMenu('map');
//        $this->addComponent('menu', $menu);
        $content .=$menu->render();
        $language = OW::getLanguage();
//        $this->setPageHeading($language->text('map', 'map_page_heading'));
//        $this->setPageHeadingIconClass('ow_ic_bookmark');

        $content .="<div class=\"clearfix\" style=\"margin-bottom:20px;\">".MAP_BOL_Service::getInstance()->get_profile_map('profile',$id_user)."</div>";
        $this->assign('content', $content);
    }

    public function indexgetprofile($params) //save
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $content ="";
            $name="";
            $desc="";
        if (isset($params['idprof']) AND $params['idprof']>0 AND isset($params['ss']) AND $params['ss']==substr(session_id(),2,5)){
//            echo "fsdf OK todo";

                    $dname=BOL_UserService::getInstance()->getDisplayName($params['idprof']);
                    $uurl=BOL_UserService::getInstance()->getUserUrl($params['idprof']);
                    $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($params['idprof']);
                    if (!$uimg) {
                        $uimg=$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png";
                    }

            if ($uimg) $showimage="";
                else $showimage="display:none;";

            if ($value['slogan']=="") $disable_slogan="display:none;";
                else $disable_slogan="";
//            if ($value['price']=="" OR $value['price']=="0" OR $value['price']==0) 
            $disable_price="display:none;";
//                else $disable_price="";
//            if ($value['discount']=="") 
            $disable_discount="display:none;";
//                else $disable_discount="";

            $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_user WHERE id='".addslashes($params['idprof'])."' LIMIT 1";
            $arrp2 = OW::getDbo()->queryForList($sql);
            if (isset($arrp2[0]['id']) AND $arrp2[0]['id']>0) {
                $last_activity=date('Y-m-d H:i:s',$arrp2[0]['activityStamp']);
                $join_date=date('Y-m-d H:i:s',$arrp2[0]['joinStamp']);

                $desc .="<div class=\"clearfix\" style=\"text-align:left;font-size:11px;\"><div class=\"ow_left\">".OW::getLanguage()->text('map', 'join_date').": ".$join_date."</div></div>";
                $desc .="<div class=\"clearfix\" style=\"text-align:left;font-size:11px;\"><div class=\"ow_left\">".OW::getLanguage()->text('map', 'last_visit').": ".$last_activity."</div></div>";
            }
            $content .="

        <div class=\"popup_deal_box\">
            <div class=\"popup_deal_navigation\">
                <span class=\"popup_deal_navigation_prev\">◄</span>
                <span class=\"popup_deal_dealnumber\"></span>
                <span class=\"popup_deal_navigation_next\">►</span>
            </div>
            
            <div class=\"popup_deal_headline\">
                <span class=\"popup_deal_disount_percent\" style=\"color: rgb(170, 0, 0);".$disable_discount."\"></span>
                <span class=\"popup_deal_disount_text\" style=\"".$disable_discount."\">".OW::getLanguage()->text('map', 'discount_upto')."</span>
                <span class=\"popup_deal_category\" style=\"color: rgb(170, 0, 0);\">".$dname."</span>&nbsp;<span class=\"popup_deal_price\" style=\"".$disable_price."\">".number_format($value['price'],2)."</span>
            </div>
            
            <div class=\"popup_deal_content_right \">
                <img class=\"popup_deal_image\" style=\"".$showimage."\" width=\"60\" height=\"53\" alt=\"\" src=\"".$uimg."\">
                <a href=\"".$uurl."\" target=\"_blank\"><div class=\"popup_deal_buy_button\" style=\"background-color: rgb(170, 0, 0);\">".OW::getLanguage()->text('map', 'more')."</div></a>
            </div>
        
            <div class=\"popup_deal_content\">
                <p class=\"popup_deal_merchant\">".$name."</p>
                <p class=\"popup_deal_title\">".$desc."</p>
            </div>
            
            <div class=\"popup_deal_address\">
                <p class=\"popup_deal_street\"></p>
                <p class=\"popup_deal_city_zip\"></p>
            </div>
            
            <span class=\"popup_deal_enlarge\" style=\"color: rgb(170, 0, 0);\">".$button_ed."</span>
            <span class=\"popup_deal_shrink\" style=\"color: rgb(170, 0, 0);\">Small</span>
        </div>

        ";
            echo $content;

        }else{
            echo OW::getLanguage()->text('map', 'details_not_found');
        }
        exit;
    }

    public function indexsaveprofile($params) //save
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
//print_r($_POST);exit;
        if ($id_user AND isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),2,5) AND isset($_POST['per_lan']) AND isset($_POST['per_lon']) AND isset($_POST['us']) AND $_POST['us']==$id_user){
            $sql="INSERT INTO " . OW_DB_PREFIX. "map_home (
                idh_owner ,    home_lat ,       home_lon
            )VALUES(
                '".addslashes($id_user)."','".addslashes($_POST['per_lan'])."','".addslashes($_POST['per_lon'])."' 
            ) ON DUPLICATE KEY UPDATE home_lat='".addslashes($_POST['per_lan'])."', home_lon='".addslashes($_POST['per_lon'])."' ";
//echo $sql;
            OW::getDbo()->insert($sql);
            echo "OK";
        }else{
            echo "Not saved";
        }
        exit;
    }
	
    public function index_ajax_showpage($params) //whow ajax page
    {
//echo "sdfsdf";exit;
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

//print_r($params);
//echo "sss";exit;
//map/get/:id_mark/:ss
//            if (isset($params['id_mark']) AND $params['id_mark']>0 AND isset($params['ss']) AND $params['ss']=""){

if (isset($_GET['mm']) AND $_GET['mm']=="edit"){
    $cmm="edit";
}else{
    $cmm="map";
}


if (isset($_GET['mob']) AND $_GET['mob']=="1"){
    $mob=1;
}else{
    $mob=0;
}

/*
if (isset($_GET['check_cat'])){
    $check_cat="?check_cat=".$_GET['check_cat'];
    $check_cata="&check_cat=".$_GET['check_cat'];
}else{
    $check_cat="";
    $check_cata="";
}
*/
if (isset($_GET['check_cat'])){
//    $check_cat="?check_cat=".$_GET['check_cat'];
//    $check_cata="&check_cat=".$_GET['check_cat'];
    $check_cat_ajax="";
    $check_cat_ajaxx="";
    $fox=$_GET['check_cat'];
//check_cat%5B%5D=1806&
//check_cat%5B%5D=1807
//print_r($fox);exit;
    if (is_array($fox)){
        for ($i=0;$i<count($fox);$i++){
            if ($check_cat_ajax) $check_cat_ajax .="&";
            $check_cat_ajax .="check_cat%5B%5D=".$fox[$i];
        }
    }
    if ($check_cat_ajax) {
        $check_cat_ajax="&".$check_cat_ajax;
        $check_cat_ajaxx="?".$check_cat_ajax;
    }
}else{
    $check_cat_ajax="";
    $check_cat_ajaxx="";
}
//$check_cat_ajax=$_GET['check_cat'];




if (isset($params['pname']) AND $params['pname']!=""){
    $pname=$params['pname'];
}else{
    $pname="";
}
$content_db="";
$button_ed="";
$content_av="";

        if ($pname=="map"){
            $add="";
            if (isset($params['id_mark']) AND $params['id_mark']>0){
                if ($is_admin){
                    $add="";
                }else{
                    $add=" AND mm.active='1' ";
//                    $add=" AND (mm.can_watch_groups='1' OR mm.can_watch_groups='0') AND mm.active='1' ";
                }
//                $query = "SELECT * FROM " . OW_DB_PREFIX. "map  
//                WHERE id= '".addslashes($params['id_mark'])."' ".$add." LIMIT 1";
                $query = "SELECT * FROM " . OW_DB_PREFIX. "map mm
                    LEFT JOIN " . OW_DB_PREFIX. "map_images mmi ON (mmi.id_map=mm.id AND is_default='1')
                WHERE mm.id='".addslashes($params['id_mark'])."' ".$add." LIMIT 1";


                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $value=$arr[0];
                }else{
                    $value=array();
                    $value['id']=0;
                }
            }else{
                $value=array();
                $value['id']=0;
            }

$button_ed ="";

            if (!$value['id']){
                $content_db="Map not found or was moving...";
            }else{

                $pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.'map'.DS;
                $pluginStaticURL2=OW::getPluginManager()->getPlugin('map')->getStaticUrl();

                $name=stripslashes($value['name']);
                $name=MAP_BOL_Service::getInstance()->html2txt($name);
                $name=str_replace("\r\n"," ",$name);
                $name=str_replace("\r","",$name);
                $name=str_replace("\n","",$name);
                $name=str_replace("\t","",$name);
                $name=str_replace("'","\"",$name);
//                $name=mb_substr($name,0,180);
                $name=mb_substr($name,0,25);

            $lat=$value['lat'];
            $lon=$value['lon'];

                $desc=stripslashes($value['desc']);
                $desc=MAP_BOL_Service::getInstance()->html2txt($desc);
                $desc=str_replace("\r\n"," ",$desc);
                $desc=str_replace("\r","",$desc);
                $desc=str_replace("\n","",$desc);
                $desc=str_replace("\t","",$desc);
                $desc=str_replace("'","\"",$desc);

//                $desc=mb_substr($desc,0,250);
                $desc=mb_substr($desc,0,125);



                $dimg="";
                $full="";
                if (isset($value['image']) AND $value['image']){
                    $img=$value['id_map']."_".$value['image']."_mini.".$value['itype'];
                    $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$value['id_owner'].DS;
//                    $dimg="<div style=\"background:url(".$url.$img.") no-repeat center;background-size: auto;min-width:250px; max-width:300px;height:160px; border-bottom:1px solid #ddd; border-top:1px solid #ddd;\" ></div>";
//                    $dimg="<div style=\"background:url(".$url.$img.") no-repeat center;background-size: auto;min-width:200px; max-width:250px;height:120px; border-bottom:1px solid #ddd; border-top:1px solid #ddd;\" ></div>";
//                    $dimg="<div style=\"background:url(".$url.$img.") no-repeat center;background-size: auto;min-width:200px; height:120px; border-bottom:1px solid #ddd; border-top:1px solid #ddd;\" ></div>";
                    $dimg=$url.$img;
                }
//echo "fsdF";exit;
//$button_ed .="--".$params['ss'].print_r($_GET,1);

//--av start
                if (OW::getConfig()->getValue('map', 'show_owner')==1){
                            if ($value['id_owner']>0){
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['id_owner']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_owner']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_owner']);
                                if ($uimg){
                                    $uimg_result ="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" height=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }else{
                                    $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }
                            }else{
                                $dname="...";
                                $uurl="";
                                $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                            }
$content_av ="<div class=\"ow_my_avatar_widget clearfix\" style=\"line-height: 0;\">
    <div class=\"ow_left ow_my_avatar_img\" style=\"max-height:45px;\">
        <div class=\"ow_avatar\" style=\"display:inline-block;line-heightx:0;float:left;\">";
            if ($uurl){
                $content_av .="<a href=\"".$uurl."\">";
            }
            $content_av .=$uimg_result;
            if ($uurl){
                $content_av .="</a>";
            }
        $content_av .="</div>
    </div>
    <div class=\"ow_my_avatar_cont\" style=\"display:inline-block;line-heightx:0;float:left;margin:15px 10px 0;;\">";
        if (strlen($dname)>14){
            $dname=substr($dname,0,14);
        }
        if ($uurl){
            $content_av .="<a href=\"".$uurl."\" class=\"ow_my_avatar_username\"><span style=\"min-width:90px;width: auto;\">@".$dname."</span></a>";
        }else{
            $content_av .="<span style=\"min-width:90px;width: auto;\">@".$dname."</span>";
        }
    $content_av .="</div>
</div>";
                }//if show owner
//--av end

                if ($mob==1){//mobile
//                    $more_url=$curent_url."map/ginfo/".$value['id'];
                    $more_url="javascript:load_content_zoom(".$value['id'].");";
                }else{//not mobile
                    $more_url=$curent_url."map/zoom/".$value['id'];
                }


                if ($id_user AND $cmm=="edit" AND ($value['id_owner']==$id_user OR $is_admin) AND isset($params['ss']) AND $params['ss']>0 AND $params['ss']==$value['id']){//edit curent
                    $button_ed .="<div class=\"ow_center clearfix\" style=\"width:100%;margin:auto;border-bottom:1px solid #eee;text-align:right;\">";
                    $button_ed .="<b>{<i style=\"color:#f00;\">".OW::getLanguage()->text('map', 'now_youareeditingthis')."</i>}</b>";
                    $button_ed .="</div>";
//                    $button_ed .="<div class=\"ow_center clearfix\"><div class=\"ow_right\" style=\"float:right;position: relative;\">
//                        <a class=\"ow_lbutton\" href=\"".$more_url."\">".OW::getLanguage()->text('map', 'more')."</a></div>
//                    </div>";
                }else if ($id_user AND $cmm=="edit" AND ($value['id_owner']==$id_user OR $is_admin)){
//                }else if ($id_user>0 AND ($value['id_owner']==$id_user)){
                    $button_ed .="<div style=\"width:100%;margin:auto;border-bottom:1px solid #eee;text-align:right;\">";
                    $button_ed .="<div class=\"ow_center clearfix\">
                        <div class=\"ow_right\" style=\"float:right;position: relative;\">";
//                            $button_ed .="<a class=\"ow_lbutton\" href=\"".$more_url."\">".OW::getLanguage()->text('map', 'more')."</a>";

                    $button_ed .="<a href=\"".$curent_url."map/edit/".$value['id']."?mapmode=ed&la=".$lat."&ln=".$lon."&zo=17&cc=".$value['id_cat'].$check_cat_ajax."#ef\" title=\"".OW::getLanguage()->text('map', 'edit_marker')."\"><img src=\"".$pluginStaticURL2."edit.gif\" /></a>";
                    $button_ed .="<a href=\"".$curent_url."map/del/".$value['id']."?mapmode=ed&delmark=true&ss=".substr(session_id(),3,5)."&la=".$lat."&ln=".$lon."&zo=12&cc=".$value['id_cat'].$check_cat_ajax."#ef\" onclick=\" if (confirm(\'Confirm delete?\')) return true; else return false;\" title=\"".OW::getLanguage()->text('map', 'delete_marker')."\"><img src=\"".$pluginStaticURL2."delete.gif\" /></a>";


                        $button_ed .="</div>
                    </div>";
                    $button_ed .="</div>";
                }else{
                    $button_ed .="<div class=\"ow_center clearfix\"><div class=\"ow_right\" style=\"float:right;position: relative;\">
                        <a class=\"ow_lbutton\" href=\"".$more_url.$check_cat_ajaxx."\">".OW::getLanguage()->text('map', 'more')."</a>
                    </div></div>";
                }

//                $content_db ="<div style=\"overflow:hidden;\"><b>".$name."</b> ".$content_av." ".$dimg."<br/>".$desc.$button_ed."</div>";
//                $content_db ="<div style=\"overflow:hidden;\"><b>".$name."</b> ".$content_av." ".$dimg." ".$desc.$button_ed."</div>";
                if ($desc){
//                    $content_db ="<div style=\"overflow:hidden;\"><b>".$name."</b> ".$dimg."<br/>".$desc."<br/> ".$button_ed."<hr/> ".$content_av."</div>";
                    $content_db ="<div style=\"overflow:hidden;\"><b>".$name."</b> ".$dimg."<br/><div class=\"clearfix\" style=\"display:inline-block;border-top:1px solid #eee;width:100%;margin:auto;font-size:12px;\">".$desc."</div> ".$button_ed." <div class=\"clearfix\" style=\"display:inline-block;border-top:1px solid #eee;width:100%;margin:auto;\">".$content_av."</div></div>";
                }else{
                    $content_db ="<div style=\"overflow:hidden;\"><b>".$name."</b> ".$dimg." ".$button_ed." <div class=\"clearfix\" style=\"display:inline-block;border-top:1px solid #eee;width:100%;margin:auto;\">".$content_av."</div></div>";
                }

//$content_db .=$mob;
/*
            $content_db ="<div id=\"popup_OpenLayers_Feature_Vector_154\" class=\"olPopup\" style=\"display:iline-block;overflow: hidden;width: 304px; height: 124px; background-color: white; opacity: 1; border: 0px; \">
                <div id=\"popup_OpenLayers_Feature_Vector_154_GroupDiv\" style=\"position: relative; overflow: hidden;\">
                <div id=\"popup_OpenLayers_Feature_Vector_154_contentDiv\" class=\"olPopupContent\" style=\"width: 300px; height: 120px; position: relative;\">
        <div class=\"popup_deal_box\" data-url=\"\" data-deal-id=\"27419206\">
            <div class=\"popup_deal_navigation\">
                <span class=\"popup_deal_navigation_prev\">◄</span>
                <span class=\"popup_deal_dealnumber\"></span>
                <span class=\"popup_deal_navigation_next\">►</span>
            </div>
            
            <div class=\"popup_deal_headline\">
                <span class=\"popup_deal_disount_percent\" style=\"color: rgb(127, 71, 153);\">- 50%</span>
                <span class=\"popup_deal_disount_text\">Rabat do</span>
                <span class=\"popup_deal_category\" style=\"color: rgb(127, 71, 153);\">Kursy</span>&nbsp;<span class=\"popup_deal_price\">89.90 zł</span>
            </div>
            
            <div class=\"popup_deal_content_right\">
                <img class=\"popup_deal_image\" width=\"80\" height=\"53\" alt=\"\" src=\"http://static.pl.groupon-content.net/68/30/1379520683068.jpg\">
                <div class=\"popup_deal_buy_button\" style=\"background-color: rgb(127, 71, 153);\">Zobacz</div>
            </div>
        
            <div class=\"popup_deal_content\">
                <p class=\"popup_deal_merchant\">ESENSAI Pole Dance Studio</p>
                <p class=\"popup_deal_title\">Aerial hoop, stretching lub pole dance: 4 lub 8 h zajęć od 89,90 zł w Esensai Pole Dance Studio</p>
            </div>
            
            <div class=\"popup_deal_address\">
                <p class=\"popup_deal_street\">Grunwaldzka</p>
                <p class=\"popup_deal_city_zip\">80-309 Gdańsk</p>
            </div>
            
            <span class=\"popup_deal_enlarge\" style=\"color: rgb(127, 71, 153);\">Zobacz Więcej</span>
            <span class=\"popup_deal_shrink\" style=\"color: rgb(127, 71, 153);\">Mniej</span>
        </div>
    </div></div></div>";
*/


            if ($dimg) $showimage="";
                else $showimage="display:none;";

            if ($value['slogan']=="") $disable_slogan="display:none;";
                else $disable_slogan="";
            if ($value['price']=="" OR $value['price']=="0" OR $value['price']==0) $disable_price="display:none;";
                else $disable_price="";
            if ($value['discount']=="") $disable_discount="display:none;";
                else $disable_discount="";

            $content_db ="

        <div class=\"popup_deal_box\">
            <div class=\"popup_deal_navigation\">
                <span class=\"popup_deal_navigation_prev\">◄</span>
                <span class=\"popup_deal_dealnumber\"></span>
                <span class=\"popup_deal_navigation_next\">►</span>
            </div>
            
            <div class=\"popup_deal_headline\">
                <span class=\"popup_deal_disount_percent\" style=\"color: rgb(170, 0, 0);".$disable_discount."\">- ".$value['discount']."</span>
                <span class=\"popup_deal_disount_text\" style=\"".$disable_discount."\">".OW::getLanguage()->text('map', 'discount_upto')."</span>
                <span class=\"popup_deal_category\" style=\"color: rgb(170, 0, 0);".$disable_price."\">".OW::getLanguage()->text('map', 'price')."</span>&nbsp;<span class=\"popup_deal_price\" style=\"".$disable_price."\">".number_format($value['price'],2)."</span>
            </div>
            
            <div class=\"popup_deal_content_right \">
                <img class=\"popup_deal_image\" style=\"".$showimage."\" width=\"80\" height=\"53\" alt=\"\" src=\"".$dimg."\">
                <a href=\"".$more_url.$check_cat_ajaxx."\" target=\"_blank\"><div class=\"popup_deal_buy_button\" style=\"background-color: rgb(170, 0, 0);\">".OW::getLanguage()->text('map', 'more')."</div></a>
            </div>
        
            <div class=\"popup_deal_content\">
                <p class=\"popup_deal_merchant\">".$name."</p>
                <p class=\"popup_deal_title\">".$desc."</p>
            </div>
            
            <div class=\"popup_deal_address\">
                <p class=\"popup_deal_street\"></p>
                <p class=\"popup_deal_city_zip\"></p>
            </div>
            
            <span class=\"popup_deal_enlarge\" style=\"color: rgb(170, 0, 0);\">".$button_ed."</span>
            <span class=\"popup_deal_shrink\" style=\"color: rgb(170, 0, 0);\">Small</span>
        </div>

        ";



            }
        }else if ($pname=="news"){

            if (isset($params['id_mark']) AND $params['id_mark']>0){
                if ($is_admin){
                    $add="";
                }else{
                    $add=" AND nn.status='1' AND nn.is_published='1' ";
//                    $add=" AND (mm.can_watch_groups='1' OR mm.can_watch_groups='0') AND mm.active='1' ";
                }
                $timeStamp=strtotime(date('Y-m-d H:i:s'));
//                $add .=" AND (startTimeStamp>'".addslashes($timeStamp)."' AND endTimeStamp<'".addslashes($timeStamp)."') ";

//                $query = "SELECT * FROM " . OW_DB_PREFIX. "map  
//                WHERE id= '".addslashes($params['id_mark'])."' ".$add." LIMIT 1";
                $query = "SELECT * FROM " . OW_DB_PREFIX. "news nn 
                                LEFT JOIN " . OW_DB_PREFIX. "news_content nnc ON (nnc.id_news=nn.id) 
                WHERE nn.id='".addslashes($params['id_mark'])."' ".$add." LIMIT 1";
//echo $query;
                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $value=$arr[0];
                }else{
                    $value=array();
                    $value['id']=0;
                }
            }else{
                
            }

            if (!$value['id']){
                $content_db="Map not found or was moving...";
            }else{

            $name=stripslashes($value['topic_name']);
            $name=str_replace("\r\n"," ",$name);
            $name=str_replace("\r","",$name);
            $name=str_replace("\n","",$name);
            $name=str_replace("\t","",$name);
            $name=str_replace("'","\"",$name);
            $name=mb_substr($name,0,180);
//            $lat=$value['lat'];
//            $lon=$value['lon'];
            $desc=stripslashes($value['content']);
            $desc=str_replace("\r\n"," ",$desc);
            $desc=str_replace("\r","",$desc);
            $desc=str_replace("\n","",$desc);
            $desc=str_replace("\t","",$desc);
            $desc=str_replace("'","\"",$desc);
//            $desc=str_replace("/","\/",$desc);
            $desc=mb_substr($desc,0,250);

//            $button_ed .="<div class=\"ow_right\" style=\"float:right;position: relative;\"><a class=\"ow_lbutton\" href=\"".$curent_url."map/zoom/".$value['id']."\">".OW::getLanguage()->text('map', 'more')."</a></div>";
            $button_ed .="<div class=\"ow_right\" style=\"float:right;position: relative;\"><a class=\"ow_lbutton\" href=\"".$curent_url."news/".$value['id']."/index.html\" title=\"".$name."\" target=\"_blank\">".OW::getLanguage()->text('map', 'more')."</a></div>";

//--av start
                            if ($value['id_sender']>0){
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['id_sender']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_sender']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_sender']);
                                if ($uimg){
                                    $uimg_result ="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" height=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }else{
                                    $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }
                            }else{
                                $dname="...";
                                $uurl="";
                                $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                            }
$content_av ="<div class=\"ow_my_avatar_widget clearfix\" style=\"line-height: 0;\">
    <div class=\"ow_left ow_my_avatar_img\" style=\"max-height:45px;\">
        <div class=\"ow_avatar\" style=\"display:block;line-height:0;\">";
            if ($uurl){
                $content_av .="<a href=\"".$uurl."\">";
            }
            $content_av .=$uimg_result;
            if ($uurl){
                $content_av .="</a>";
            }
        $content_av .="</div>
    </div>
    <div class=\"ow_my_avatar_cont\">";
        if (strlen($dname)>14){
            $dname=substr($dname,0,14);
        }
        if ($uurl){
            $content_av .="<a href=\"".$uurl."\" class=\"ow_my_avatar_username\"><span style=\"min-width:90px;width: auto;\">@".$dname."</span></a>";
        }else{
            $content_av .="<span style=\"min-width:90px;width: auto;\">@".$dname."</span>";
        }
    $content_av .="</div>
</div>";
//--av end



//            $content_db =$full."<b>".$name."</b>".$dimg."<br/>".$desc.$button_ed;
//            $content_db ="<b>".$name."</b><br/>".$desc.$button_ed;
            $name_url ="<a href=\"".$curent_url."news/".$value['id']."/index.html\" title=\"".$name."\" target=\"_blank\">".$name."</a>";

            $content_db ="<div style=\"overflow:hidden;\"><b>".$name_url."</b> ".$content_av." ".$desc.$button_ed."</div>";
            }//if found

        }else if ($pname=="event"){

            if (isset($params['id_mark']) AND $params['id_mark']>0){
                if ($is_admin){
                    $add="";
                }else{
                    $add=" AND status='1' ";
//                    $add=" AND (mm.can_watch_groups='1' OR mm.can_watch_groups='0') AND mm.active='1' ";
                }
                $timeStamp=strtotime(date('Y-m-d H:i:s'));
                $add .=" AND (startTimeStamp>'".addslashes($timeStamp)."' AND endTimeStamp<'".addslashes($timeStamp)."') ";

//                $query = "SELECT * FROM " . OW_DB_PREFIX. "map  
//                WHERE id= '".addslashes($params['id_mark'])."' ".$add." LIMIT 1";
                $query = "SELECT * FROM " . OW_DB_PREFIX. "event_item 
                WHERE id='".addslashes($params['id_mark'])."' ".$add." LIMIT 1";
                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $value=$arr[0];
                }else{
                    $value=array();
                    $value['id']=0;
                }
            }else{
                
            }

            if (!$value['id']){
                $content_db="Map not found or was moving...";
            }else{

            $name=stripslashes($value['name']);
            $name=str_replace("\r\n"," ",$name);
            $name=str_replace("\r","",$name);
            $name=str_replace("\n","",$name);
            $name=str_replace("\t","",$name);
            $name=str_replace("'","\"",$name);
            $name=mb_substr($name,0,180);
//            $lat=$value['lat'];
//            $lon=$value['lon'];
            $desc=stripslashes($value['description']);
            $desc=str_replace("\r\n"," ",$desc);
            $desc=str_replace("\r","",$desc);
            $desc=str_replace("\n","",$desc);
            $desc=str_replace("\t","",$desc);
            $desc=str_replace("'","\"",$desc);
//            $desc=str_replace("/","\/",$desc);
            $desc=mb_substr($desc,0,250);

//            $button_ed .="<div class=\"ow_right\" style=\"float:right;position: relative;\"><a class=\"ow_lbutton\" href=\"".$curent_url."map/zoom/".$value['id']."\">".OW::getLanguage()->text('map', 'more')."</a></div>";
            $button_ed .="<div class=\"ow_right\" style=\"float:right;position: relative;\"><a class=\"ow_lbutton\" href=\"".$curent_url."product/".$value['id']."/zoom/index.html\" title=\"".$name."\" target=\"_blank\">".OW::getLanguage()->text('map', 'more')."</a></div>";

//--av start
                            if ($value['id_owner']>0){
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['id_owner']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_owner']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_owner']);
                                if ($uimg){
                                    $uimg_result ="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" height=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }else{
                                    $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }
                            }else{
                                $dname="...";
                                $uurl="";
                                $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                            }
$content_av ="<div class=\"ow_my_avatar_widget clearfix\" style=\"line-height: 0;\">
    <div class=\"ow_left ow_my_avatar_img\" style=\"max-height:45px;\">
        <div class=\"ow_avatar\" style=\"display:block;line-height:0;\">";
            if ($uurl){
                $content_av .="<a href=\"".$uurl."\">";
            }
            $content_av .=$uimg_result;
            if ($uurl){
                $content_av .="</a>";
            }
        $content_av .="</div>
    </div>
    <div class=\"ow_my_avatar_cont\">";
        if (strlen($dname)>14){
            $dname=substr($dname,0,14);
        }
        if ($uurl){
            $content_av .="<a href=\"".$uurl."\" class=\"ow_my_avatar_username\"><span style=\"min-width:90px;width: auto;\">@".$dname."</span></a>";
        }else{
            $content_av .="<span style=\"min-width:90px;width: auto;\">@".$dname."</span>";
        }
    $content_av .="</div>
</div>";
//--av end



//            $content_db =$full."<b>".$name."</b>".$dimg."<br/>".$desc.$button_ed;
//            $content_db ="<b>".$name."</b><br/>".$desc.$button_ed;
            $name_url ="<a href=\"".$curent_url."product/".$value['id']."/zoom/index.html\" title=\"".$name."\" target=\"_blank\">".$name."</a>";

            $content_db ="<div style=\"overflow:hidden;\"><b>".$name_url."</b> ".$content_av." ".$desc.$button_ed."</div>";
            }//if found


        }else if ($pname=="shop"){

            if (isset($params['id_mark']) AND $params['id_mark']>0){
                if ($is_admin){
                    $add="";
                }else{
                    $add=" AND active='1' ";
//                    $add=" AND (mm.can_watch_groups='1' OR mm.can_watch_groups='0') AND mm.active='1' ";
                }
//                $query = "SELECT * FROM " . OW_DB_PREFIX. "map  
//                WHERE id= '".addslashes($params['id_mark'])."' ".$add." LIMIT 1";
                $query = "SELECT * FROM " . OW_DB_PREFIX. "shoppro_products 
                WHERE id='".addslashes($params['id_mark'])."' ".$add." LIMIT 1";
                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $value=$arr[0];
                }else{
                    $value=array();
                    $value['id']=0;
                }
            }else{
                
            }

            if (!$value['id']){
                $content_db="Map not found or was moving...";
            }else{

            $name=stripslashes($value['name']);
            $name=str_replace("\r\n"," ",$name);
            $name=str_replace("\r","",$name);
            $name=str_replace("\n","",$name);
            $name=str_replace("\t","",$name);
            $name=str_replace("'","\"",$name);
            $name=mb_substr($name,0,180);
//            $lat=$value['lat'];
//            $lon=$value['lon'];
            $desc=stripslashes($value['description']);
            $desc=str_replace("\r\n"," ",$desc);
            $desc=str_replace("\r","",$desc);
            $desc=str_replace("\n","",$desc);
            $desc=str_replace("\t","",$desc);
            $desc=str_replace("'","\"",$desc);
//            $desc=str_replace("/","\/",$desc);
            $desc=mb_substr($desc,0,250);

//            $button_ed .="<div class=\"ow_right\" style=\"float:right;position: relative;\"><a class=\"ow_lbutton\" href=\"".$curent_url."map/zoom/".$value['id']."\">".OW::getLanguage()->text('map', 'more')."</a></div>";
            $button_ed .="<div class=\"ow_right\" style=\"float:right;position: relative;\"><a class=\"ow_lbutton\" href=\"".$curent_url."product/".$value['id']."/zoom/index.html\" title=\"".$name."\" target=\"_blank\">".OW::getLanguage()->text('map', 'more')."</a></div>";

//--av start
                            if ($value['id_owner']>0){
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['id_owner']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_owner']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_owner']);
                                if ($uimg){
                                    $uimg_result ="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" height=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }else{
                                    $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }
                            }else{
                                $dname="...";
                                $uurl="";
                                $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                            }
$content_av ="<div class=\"ow_my_avatar_widget clearfix\" style=\"line-height: 0;\">
    <div class=\"ow_left ow_my_avatar_img\" style=\"max-height:45px;\">
        <div class=\"ow_avatar\" style=\"display:block;line-height:0;\">";
            if ($uurl){
                $content_av .="<a href=\"".$uurl."\">";
            }
            $content_av .=$uimg_result;
            if ($uurl){
                $content_av .="</a>";
            }
        $content_av .="</div>
    </div>
    <div class=\"ow_my_avatar_cont\">";
        if (strlen($dname)>14){
            $dname=substr($dname,0,14);
        }
        if ($uurl){
            $content_av .="<a href=\"".$uurl."\" class=\"ow_my_avatar_username\"><span style=\"min-width:90px;width: auto;\">@".$dname."</span></a>";
        }else{
            $content_av .="<span style=\"min-width:90px;width: auto;\">@".$dname."</span>";
        }
    $content_av .="</div>
</div>";
//--av end



//            $content_db =$full."<b>".$name."</b>".$dimg."<br/>".$desc.$button_ed;
//            $content_db ="<b>".$name."</b><br/>".$desc.$button_ed;
            $name_url ="<a href=\"".$curent_url."product/".$value['id']."/zoom/index.html\" title=\"".$name."\" target=\"_blank\">".$name."</a>";

            $content_db ="<div style=\"overflow:hidden;\"><b>".$name_url."</b> ".$content_av." ".$desc.$button_ed."</div>";
            }//if found

        }else if ($pname=="fanpage"){
//$content_db ="fanpage";
            if (isset($params['id_mark']) AND $params['id_mark']>0){
                if ($is_admin){
                    $add="";
                }else{
                    $add=" AND active='1' AND is_published='1' ";
                }
                $query = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages  
                WHERE id='".addslashes($params['id_mark'])."' ".$add." LIMIT 1";


                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $value=$arr[0];
                }else{
                    $value=array();
                    $value['id']=0;
                }
            }else{
                $value=array();
                $value['id']=0;
            }

            if (!$value['id']){
                $content_db="Map not found or was moving...";
            }else{

            $addres_desc="";
            if (!isset($value['a_city'])) $value['a_city']="";
            if ($value['a_city']) {
                if ($addres_desc) $addres_desc .="<br/>";
                $addres_desc .=stripslashes($value['a_city']);
            }

                if (!isset($value['a_postcode'])) $value['a_postcode']="";
                if ($value['a_postcode']) {
                    if ($addres_desc) $addres_desc .=" ";
                    $addres_desc .=stripslashes($value['a_postcode']);
                }

            if (!isset($value['a_street'])) $value['a_street']="";
            if ($value['a_street']) {
                if ($addres_desc) $addres_desc .="<br/>";
                $addres_desc .=stripslashes($value['a_street']);
            }


            if (!isset($value['a_country'])) $value['a_country']="";
            if ($value['a_country']) {
                if ($addres_desc) $addres_desc .="<br/>";
                $addres_desc .=stripslashes($value['a_country']);
            }



            if (!isset($value['title_fan_page'])) $value['title_fan_page']="";
            $name=stripslashes($value['title_fan_page']);
            $name=str_replace("\r\n"," ",$name);
            $name=str_replace("\r","",$name);
            $name=str_replace("\n","",$name);
            $name=str_replace("\t","",$name);
            $name=str_replace("'","\"",$name);
            $name=mb_substr($name,0,180);
//            $name=str_replace("\\","\\\\",$name);
//            $lat=$value['lat'];
//            $lon=$value['lon'];


            $desc=$addres_desc;
            $desc=str_replace("\r\n"," ",$desc);
            $desc=str_replace("\r","",$desc);
            $desc=str_replace("\n","",$desc);
            $desc=str_replace("\t","",$desc);
            $desc=str_replace("'","`",$desc);
//
            $desc=str_replace("\\","\\\\",$desc);
//            $desc=mb_substr($desc,0,250);



//            $button_ed .="<div class=\"ow_right\" style=\"float:right;position: relative;\"><a class=\"ow_lbutton\" href=\"".$curent_url."map/zoom/".$value['id']."\">".OW::getLanguage()->text('map', 'more')."</a></div>";
//            $button_ed .="<div class=\"ow_right\" style=\"float:right;position: relative;\"><a class=\"ow_lbutton\" href=\"".$curent_url."fanpageid/".$value['id']."\" target=\"_blank\">".OW::getLanguage()->text('map', 'more')."</a></div>";
            $button_ed .="<div class=\"ow_right\" style=\"float:right;position: relative;\"><a class=\"ow_lbutton\" href=\"".$curent_url."fanpageid/".$value['id_owner']."\" title=\"".$name."\" target=\"_blank\">".OW::getLanguage()->text('map', 'more')."</a></div>";


//--av start
                            if ($value['id_owner']>0){
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['id_owner']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_owner']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_owner']);
                                if ($uimg){
                                    $uimg_result ="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" height=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }else{
                                    $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }
                            }else{
                                $dname="...";
                                $uurl="";
                                $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                            }
$content_av ="<div class=\"ow_my_avatar_widget clearfix\" style=\"line-height: 0;\">
    <div class=\"ow_left ow_my_avatar_img\" style=\"max-height:45px;\">
        <div class=\"ow_avatar\" style=\"display:block;line-height:0;\">";
            if ($uurl){
                $content_av .="<a href=\"".$uurl."\">";
            }
            $content_av .=$uimg_result;
            if ($uurl){
                $content_av .="</a>";
            }
        $content_av .="</div>
    </div>
    <div class=\"ow_my_avatar_cont\">";
        if (strlen($dname)>14){
            $dname=substr($dname,0,14);
        }
        if ($uurl){
            $content_av .="<a href=\"".$uurl."\" class=\"ow_my_avatar_username\"><span style=\"min-width:90px;width: auto;\">@".$dname."</span></a>";
        }else{
            $content_av .="<span style=\"min-width:90px;width: auto;\">@".$dname."</span>";
        }
    $content_av .="</div>
</div>";
//--av end

            $name_url ="<a href=\"".$curent_url."fanpageid/".$value['id_owner']."\" title=\"".$name."\" target=\"_blank\">".$name."</a>";
//            $content_db ="<b>".$name."</b>".$content_av."<br/>".$desc.$button_ed;
            $content_db ="<div style=\"overflow:hidden;\"><b>".$name_url."</b> ".$content_av." ".$desc.$button_ed."</div>";

            }//end found





        }else{//end if $pname
$content_db ="error".print_r($params,1);
        }
                $content_db =str_replace("'","\'",$content_db);
//                $content_db =str_replace("\r\n","<br/>",$content_db);
                $content_db =str_replace("\r\n","",$content_db);
//                $content_db =str_replace("\n","<br/>",$content_db);
                $content_db =str_replace("\n","",$content_db);

            echo $content_db;
            exit;
    } 


    public function indexgmap($params)
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url = 'http';
        if (isset($_SERVER["HTTPS"])) {$curent_url .= "s";}
        $curent_url .= "://";
        $curent_url .= $_SERVER["SERVER_NAME"]."/";
        $curent_url=OW_URL_HOME;
        $pluginStaticURL2=OW::getPluginManager()->getPlugin('map')->getStaticUrl();

//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n----------POST:\n".print_r($_POST,1), FILE_APPEND | LOCK_EX);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n----------GET:\n".print_r($_GET,1), FILE_APPEND | LOCK_EX);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n----------SESSION:\n".print_r($_SESSION,1), FILE_APPEND | LOCK_EX);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n----------FILES:\n".print_r($_FILES,1), FILE_APPEND | LOCK_EX);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n----------SERVER:\n".print_r($_SERVER,1), FILE_APPEND | LOCK_EX);




/*
//---round start
$orig_lat=54.351892;
$orig_lon= 18.646299;
$dist=10;  // Radius in miles;
$found="";

        $sql = "SELECT *, 
            3956 * 2 * ASIN(SQRT( POWER(SIN((".$orig_lat." -abs(dest.lat)) * pi()/180 / 2),2) + COS(".$orig_lat." * pi()/180 ) * COS( abs(dest.lat) *  pi()/180) * POWER(SIN((".$orig_lon." - dest.lon) *  pi()/180 / 2), 2) )) as distance 
        FROM " . OW_DB_PREFIX. "map dest having distance < ".$dist."
        ORDER BY distance limit 10";
//echo $sql;exit;
        $arrc = OW::getDbo()->queryForList($sql);
        foreach ( $arrc as $row ){
            $found .=$row['id']."|2";
        }
if ($found){
echo $found;
exit;
}
//---round end
*/

        $content="";
//echo "afsdF";exit;
//        $content .="sdfsdfs";
//$name = "Your Name";

/*
$f = fopen("position.cur", "r");
$p = fgets($f);
*/
$msec="";
$mi="";
$mh="";
$ml="";
        if (isset($_GET['c'])){
            $mx=explode("_",$_GET['c'],3);
            if (isset($mx[0])) $mi=$mx[0];
            if (isset($mx[1])) $mh=$mx[1];
            if (isset($mx[2])) $ml=$mx[2];
            if (!isset($mi) OR !isset($mh) OR !isset($ml) OR $mi=="null" OR $mi=="" OR $mh=="null" OR $mh==""){
//                $msec=$_GET['c'];
            }
        }

//echo $mi."--".$mh."---";exit;
///        if (isset($_GET['c'])){
        if ( ($mi AND $mh) OR $msec){

            if ($mi AND $mh AND $ml){
//                $sql="SELECT * FROM " . OW_DB_PREFIX. "map_scan WHERE `secret`='".addslashes($mi)."' AND hash_unique='".addslashes($mh)."' AND `active`='1' LIMIT 1";
                $sql="SELECT ms.*,users.username FROM " . OW_DB_PREFIX. "map_scan ms 
                    LEFT JOIN " . OW_DB_PREFIX. "base_user users ON (users.id=ms.id_owner) 
                WHERE ms.`secret`='".addslashes($mi)."' AND ms.`hash_unique`='".addslashes($mh)."' AND 
                ms.`active`='1' AND users.emailVerify='1' AND (users.username='".addslashes($ml)."' OR users.email='".addslashes($ml)."') ";
            }else if ($ml){
//                $sql="SELECT * FROM " . OW_DB_PREFIX. "map_scan WHERE `secret`='".addslashes($msec)."' AND `active`='1' LIMIT 1";
                $sql="SELECT ms.*,users.username FROM " . OW_DB_PREFIX. "map_scan ms 
                    LEFT JOIN " . OW_DB_PREFIX. "base_user users ON (users.id=ms.id_owner) 
                WHERE ms.`secret`='".addslashes($msec)."' AND 
                ms.`active`='1' AND users.emailVerify='1' AND (users.username='".addslashes($ml)."' OR users.email='".addslashes($ml)."') ";
            }else{
                $sql="";
            }

            if ($sql){
                $mmb = OW::getDbo()->queryForList($sql);    
            }else{
                $mmb =array();
            }

            if (isset($mmb[0]['id_owner']) AND $mmb[0]['id_owner']>0){

                $sql="SELECT * FROM " . OW_DB_PREFIX. "map_scan_data WHERE `id_owner`='".addslashes($mmb[0]['id_owner'])."' ORDER BY d_time DESC LIMIT 1";
//echo $sql;
                $row = OW::getDbo()->queryForList($sql);    
                if (isset($row[0]['id_scan']) AND $row[0]['id_scan']>0){
//echo $mi."--".$mh."---";exit;
//echo "fsFD";exit;
$time = $row[0]['d_time'];
$lat = $row[0]['d_latitude'];
$lon = $row[0]['d_longitude'];
$acc = $row[0]['d_accuracy'];

$acc = (int)$acc;
$pos = $lat.",".$lon;
//$time = strftime("%Y-%m-%d %H:%M:%S", $time);
$utime = urlencode($time);
//$uname = urlencode($name);

if (isset($_GET['ct'])){
    $curent_cat=$_GET['ct'];
}else{
    $curent_cat=0;
}

//echo "sfsdF";exit;
$mm=MAP_BOL_Service::getInstance()->make_markers($mmb[0]['id_owner'],$curent_cat);
//echo "sfsdF";
//exit;

//$mmc=MAP_BOL_Service::getInstance()->make_markers_poi();

$mmpp="";
$mmp=MAP_BOL_Service::getInstance()->make_paths($mmb[0]['id_owner']);
//print_r($mmp);exit;
if (isset($mmp[0]) AND isset($mmp[1])){
$mpo=$mmp[0];
$mpop=$mmp[1];

if (isset($_GET['c'])){
    $cpar=$_GET['c'];
}else if (isset($_POST['c'])){
    $cpar=$_POST['c'];
}else{
    $cpar="";
}


$mmpp ="
 var flightPlanCoordinates = [

        ".$mpo."
  ];
  var flightPath = new google.maps.Polyline({
    path: flightPlanCoordinates,
    strokeColor: '#FF0000',
    strokeOpacity: 1.0,
    strokeWeight: 2
  });

  flightPath.setMap(friendsmapTest.map);

    ".$mpop."
";
}


//    <link href="/maps/documentation/javascript/examples/default.css" rel="stylesheet">

/*
  var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      icon: '".$curent_url."ow_userfiles/plugins/base/avatars/avatar_1_1371045733.jpg',
      title: 'Hello World!'
  });
      icon: 'http://www.rippleplatform.com/img/load.gif',
*/


//$lat_c=$lat+0.0001;
//$lon_c=$lon+0.00001;
//<script src=\"http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/data.json\" type=\"text/javascript\"></script>
//    <script src=\"".$pluginStaticURL2."rmarkr.js\"></script>
//    <script src=\"https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true\"></script>

echo "<html>
<head>
<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">
<meta charset=\"utf-8\">
    <title>Simple markers</title>

    <meta name=\"viewport\" content=\"initial-scale=1.0, user-scalable=no\">
    <script src=\"".$pluginStaticURL2."markerclusterer.js\"></script>
    <script src=\"http://www.google.com/jsapi\"></script>
    <script type=\"text/javascript\" src=\"".$curent_url."ow_static/plugins/base/js/jquery-1.7.1.min.js\"></script>

<script>

    var sh_main=0;

function $(element) {
  return document.getElementById(element);
}





".$mm."
";


if (!isset($_GET['op']) OR !$_GET['op']=="upload_photo_a"){

echo "var friendsmapTest = {};

friendsmapTest.pics = null;
friendsmapTest.picscount = 0;
friendsmapTest.map = null;
friendsmapTest.markerClusterer = null;
friendsmapTest.markers = [];
friendsmapTest.infoWindow = null;

friendsmapTest.init = function() {
  var latlng = new google.maps.LatLng(".$lat.",".$lon.");
  var options = {
/*
    'zoom': 15,
    'center': latlng,
    'mapTypeId': google.maps.MapTypeId.ROADMAP
*/

    zoom: 15,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP,

    // MAP CONTROLS (START)
      mapTypeControl: true,

      panControl: true,
      panControlOptions: {
      position: google.maps.ControlPosition.TOP_RIGHT
      },
        zoomControl: true,
      zoomControlOptions: {
      style: google.maps.ZoomControlStyle.LARGE,
      position: google.maps.ControlPosition.LEFT_TOP
      },
        streetViewControl: true,
      streetViewControlOptions: {
      position: google.maps.ControlPosition.LEFT_TOP
        },


  };

  friendsmapTest.map = new google.maps.Map($('map'), options);
  friendsmapTest.pics = data.photos;
  friendsmapTest.picscount = data.count;
  
  var useGmm = 1;
  

  friendsmapTest.infoWindow = new google.maps.InfoWindow();

  friendsmapTest.showMarkers();


//----mpos start
    var redpin = new google.maps.MarkerImage(
        '".$curent_url."ow_static/plugins/map/main_marker.gif',
        null,//shadow size
        null,//shadow point
        new google.maps.Point(16,16),//anchor
        new google.maps.Size(32,32)//scale
    );

    var marker = new google.maps.Marker({
      position: latlng,
      map: friendsmapTest.map,
    animation: google.maps.Animation.DROP,
      icon: redpin,
      title: 'Hello! It`s Your last location...\\n(".$row[0]['d_time'].")'
    });
    var infowindow = new google.maps.InfoWindow({
        content: '<b>Your Position</b><br/>Last update:\\n".$row[0]['d_time']."'
    });
    google.maps.event.addListener(marker, 'click', function() {
        infowindow.open( friendsmapTest.map, marker);
    });
//----mpos end

".$mmpp."


};

friendsmapTest.showMarkers = function() {
  friendsmapTest.markers = [];

  var type = 0;

  if (friendsmapTest.markerClusterer) {
    friendsmapTest.markerClusterer.clearMarkers();
  }

  var panel = $('markerlist');
  panel.innerHTML = '';
  var numMarkers = friendsmapTest.picscount;

  for (var i = 0; i < numMarkers; i++) {
    if (friendsmapTest.pics[i].marker_type=='friend'){

        var titleText = friendsmapTest.pics[i].photo_title;
        if (titleText == '') {
          titleText = 'No title';
        }

        var item = document.createElement('DIV');
        var title = document.createElement('A');
        title.href = '#';
        title.className = 'title';
        title.innerHTML = titleText;

        item.appendChild(title);
        panel.appendChild(item);
    }        


    var latLng = new google.maps.LatLng(friendsmapTest.pics[i].latitude,
        friendsmapTest.pics[i].longitude);


    if (friendsmapTest.pics[i].markerurl!=null && friendsmapTest.pics[i].markerurl!='' && friendsmapTest.pics[i].marker_type=='friend'){
        var imageUrl =friendsmapTest.pics[i].markerurl;
        var markerImage = new google.maps.MarkerImage(
            imageUrl,
            null,//size
            null,//origin
            null,//anchor
            new google.maps.Size(50,50)//scale
        );
    }else if (friendsmapTest.pics[i].markerurl!=null && friendsmapTest.pics[i].markerurl!='' && friendsmapTest.pics[i].marker_type=='poi'){
        var imageUrl ='".$pluginStaticURL2."ico".DS."'+friendsmapTest.pics[i].markerurl;
        var markerImage = new google.maps.MarkerImage(
            imageUrl,
            null,//size
            null,//origin
            null,//anchor
            new google.maps.Size(32,32)//scale
        );
    }else{
        var imageUrl = 'http://chart.apis.google.com/chart?cht=mm&chs=24x32&chco=FFFFFF,008CFF,000000&ext=.png';
        var markerImage = new google.maps.MarkerImage(
            imageUrl,
            new google.maps.Size(24, 32)
        );
    }






    var marker = new google.maps.Marker({
      'position': latLng,
      'icon': markerImage
    });

    var fn = friendsmapTest.markerClickFunction(friendsmapTest.pics[i], latLng);
    google.maps.event.addListener(marker, 'click', fn)
    if (friendsmapTest.pics[i].marker_type=='friend'){;
        google.maps.event.addDomListener(title, 'click', fn);
    }
    friendsmapTest.markers.push(marker);
  }

  window.setTimeout(friendsmapTest.time, 0);
};

friendsmapTest.markerClickFunction = function(pic, latlng) {


  return function(e) {
    e.cancelBubble = true;
    e.returnValue = false;
    if (e.stopPropagation) {
      e.stopPropagation();
      e.preventDefault();
    }
    var title = pic.photo_title;
    var url = pic.owner_url;
    var fileurl = pic.photo_file_url;

    if (pic.marker_type=='poi'){
        var infoHtml = '<div class=\"info\"><div class=\"info-body\">' +
          '<a href=\"' + url + '\" target=\"_blank\"><img src=\"' +
          fileurl + '\" class=\"info-img\"/></a>".MAP_BOL_Service::getInstance()->corectforjava(OW::getLanguage()->text('map', 'loading'))."' +
        '</div>'+
          '</div>';
    }else{
        var upload_date = pic.upload_date;
        var infoHtml = '<div class=\"info\"><div class=\"info-body\" style=\"display:inline-block;min-height:180px;\">' +
         '<div style=\"line-height: 30px;font-weight:bold;\">'+title+'</div>'+
          '<a href=\"' + url + '\" target=\"_blank\"><img src=\"' +
          fileurl + '\" class=\"info-img\"/></a>' +
         '<div style=\"line-height: 14px;font-size:70%;\">'+upload_date+'</div>'+
        '</div>'+
          '</div>';
    }



    friendsmapTest.infoWindow.setContent(infoHtml);
    friendsmapTest.infoWindow.setPosition(latlng);
    friendsmapTest.infoWindow.open(friendsmapTest.map);

    friendsmapTest.map.setZoom(16);
    friendsmapTest.map.setCenter(latlng);

//    if (pic.marker_type=='friend'){
    if (pic.marker_type=='poi'){
        load_content(0,pic.photo_id,'map');
    }

//$('markerlist')
                jQuery('#map-container').css('margin-left','0');
                sh_main=0;
                jQuery('#panel').hide();



  };
};


    function load_content(info,id,pname){
       jQuery.ajax({
        url: '".$curent_url."map/get/'+id+'/'+info+'/'+pname+'?mob=1&mm=map".$check_cat_ajax."',
        success: function(data){
            var infoHtml = data;
//            var infoHtml = '<div class=\"info\" style=\"cursor: default; overflow: auto; width: 200px; max-width:300px;max-height: 350px;\">'+data+'</div>';
/*
            var infoHtml = '<div id=\"popup_OpenLayers_Feature_Vector_154\" class=\"olPopup\" style=\"position: absolute; overflow: hidden;width: 304px; height: 124px; background-color: white; opacity: 1; border: 0px; z-index: 751;\">'+
                '<div id=\"popup_OpenLayers_Feature_Vector_154_GroupDiv\" style=\"position: relative; overflow: hidden;\">'+
                '<div id=\"popup_OpenLayers_Feature_Vector_154_contentDiv\" class=\"olPopupContent\" style=\"width: 300px; height: 120px; position: relative;\">'+
        '<div class=\"popup_deal_box\" data-url=\"\" data-deal-id=\"27419206\">'+
            '<div class=\"popup_deal_navigation\">'+
                '<span class=\"popup_deal_navigation_prev\">◄</span>'+
                '<span class=\"popup_deal_dealnumber\"></span>'+
                '<span class=\"popup_deal_navigation_next\">►</span>'+
            '</div>'+
            
            '<div class=\"popup_deal_headline\">'+
                '<span class=\"popup_deal_disount_percent\" style=\"color: rgb(127, 71, 153);\">- 50%</span>'+
                '<span class=\"popup_deal_disount_text\">Rabat do</span>'+
                '<span class=\"popup_deal_category\" style=\"color: rgb(127, 71, 153);\">Kursy</span>&nbsp;<span class=\"popup_deal_price\">89.90 zł</span>'+
            '</div>'+
            
            '<div class=\"popup_deal_content_right\">'+
                '<img class=\"popup_deal_image\" width=\"80\" height=\"53\" alt=\"\" src=\"http://static.pl.groupon-content.net/68/30/1379520683068.jpg\">'+
                '<div class=\"popup_deal_buy_button\" style=\"background-color: rgb(127, 71, 153);\">Zobacz</div>'+
            '</div>'+
        
            '<div class=\"popup_deal_content\">'+
                '<p class=\"popup_deal_merchant\">ESENSAI Pole Dance Studio</p>'+
                '<p class=\"popup_deal_title\">Aerial hoop, stretching lub pole dance: 4 lub 8 h zajęć od 89,90 zł w Esensai Pole Dance Studio</p>'+
            '</div>'+
            
            '<div class=\"popup_deal_address\">'+
                '<p class=\"popup_deal_street\">Grunwaldzka</p>'+
                '<p class=\"popup_deal_city_zip\">80-309 Gdańsk</p>'+
            '</div>'+
            
            '<span class=\"popup_deal_enlarge\" style=\"color: rgb(127, 71, 153);\">Zobacz Więcej</span>'+
            '<span class=\"popup_deal_shrink\" style=\"color: rgb(127, 71, 153);\">Mniej</span>'+
        '</div>'+
    '</div></div></div>';
*/
            friendsmapTest.infoWindow.setContent(infoHtml);
        }
      });
    }

    function load_content_zoom(id){
        jQuery('#main_zoom_content').hide();
        jQuery('#zoom_content').html('".MAP_BOL_Service::getInstance()->corectforjava(OW::getLanguage()->text('map', 'loading'))."');

       jQuery.ajax({
        url: '".$curent_url."map/ginfo/'+id,
        success: function(data){
jQuery('#search-bar').hide();
jQuery('#main_map').hide();

            jQuery('#main_zoom_content').show();
            jQuery('#zoom_content').html(data);

        }
      });
    }

    function load_content_zoom_close(id){
        jQuery('#search-bar').show();
        jQuery('#main_map').show();
        jQuery('#main_zoom_content').hide();
        jQuery('#zoom_content').html('');
    }

friendsmapTest.clear = function() {
//  $('timetaken').innerHTML = 'cleaning...';
  for (var i = 0, marker; marker = friendsmapTest.markers[i]; i++) {
    marker.setMap(null);
  }
};

friendsmapTest.change = function() {
  friendsmapTest.clear();
  friendsmapTest.showMarkers();
};

friendsmapTest.time = function() {
//  $('timetaken').innerHTML = 'timing...';
  var start = new Date();


    friendsmapTest.markerClusterer = new MarkerClusterer(friendsmapTest.map, friendsmapTest.markers);
//    for (var i = 0, marker; marker = friendsmapTest.markers[i]; i++) {
//      marker.setMap(friendsmapTest.map);
//    }

  var end = new Date();
//  $('timetaken').innerHTML = end - start;
};




        google.load('maps', '3', {other_params: 'sensor=false'});
        google.setOnLoadCallback(friendsmapTest.init);
";
}//if (!isset($_GET['op']) OR !$_GET['op']=="upload_photo_a"){



echo "
var j = jQuery.noConflict();    
j(document).ready(function(){

    j('#map-container').css('margin-left','0');
//    j('#show_hide').css('left','40');
    j('#panel').hide();


    j('#show_hide').click(function(){
            if (sh_main==1){
                j('#map-container').css('margin-left','0');
                sh_main=0;
//                j('#panel').hide('slide', { direction: 'left' }, 1000);
                j('#panel').hide();
            }else{
                j('#map-container').css('margin-left','200');
                sh_main=1;
//                j('#panel').show('slide', { direction: 'left' }, 1000);
                j('#panel').show();
            }
    });

    j('#mcategory').change(function() {
        window.location = '".$curent_url."map/gmap?c=".$cpar."&ct='+j( this ).val();
    });


    j('#b_close_page').click(function(){
//        j('#map_page').hide();
        window.location = '".$curent_url."map/gmap?c=".$cpar."';
    });

});



</script>


<style>
    #show_hide{
    }

     body {
        margin: 0;
        padding: 0;
        font-family: Arial;
        font-size: 14px;
        
      }
    #main_map{
        position:relative;
        top:48px;
    }

      #panel {
        float: left;
        width: 200px;
        height: 100%;
        margin:0;
        padding:0;
      }

      #map-container {
        margin-left: 200px;
        overflow: hidden;
      }

      #map {
        width: 100%;
        height: 100%;
        margin-bottom: -48px;
      }

      #markerlist {
        height: 200px;
        max-height:350px;
        margin: 10px 5px 0 10px;
        overflow: auto;
      }

      .title {
        border-bottom: 1px solid #e0ecff;
        overflow: hidden;
        width: 100%;
        cursor: pointer;
        padding: 2px 0;
        display: block;
        color: #000;
        text-decoration: none;
      }

      .title:visited {
        color: #000;
      }

      .title:hover {
        background: #e0ecff;
      }


      .info {
        width: 100%;
      }
      .titlex {
        width: 100%;
        text-align:center;
      }

      .info img {
        border: 0;
      }

      .info-body {
        width: 100%;
        height: 100px;
        line-height: 100px;
        margin: 1px 0;
        text-align: center;
        overflow: hidden;
      }

      .info-img {
        max-height: 150px;
        max-width: 150px;
      }

















.search-bar {
-webkit-animation: slide-down .5s;
-moz-animation: slide-down .5s;
-ms-animation: slide-down .5s;
-o-animation: slide-down .5s;
animation: slide-down .5s;
position: fixed;
top: 0;
left: 0;
width: 100%;
z-index: 10010;
margin: 0;
box-shadow: 0 2px 10px rgba(0,0,0,0.2);
background: #fff;
}

.search-bar>.in {
position: relative;
width:100%;
height: 30px;
padding: 0;
margin: auto;
}

.search-bar .in {
    padding: 0;
}












.popup_deal_box {
width: 290px;
height: 110px;
color: #5b5b5b;
position: relative;
overflow: hidden;
margin: 5px;
}
div.olMapViewport {
text-align: left;
}
Xdiv.olMap {
cursor: default;
}
.shadow_main_item, .map_shadow_box {
zoom: 1;
}
.popup_deal_navigation {
display: none;
border-bottom: 1px solid #D5D5D5;
margin-bottom: 2px;
padding-bottom: 2px;
}
.popup_deal_headline {
font-size: 13px;
border-bottom: 1px solid #D5D5D5;
padding-bottom: 1px;
margin-bottom: 5px;
height: 16px;
overflow: hidden;
}
.popup_deal_content_right {
width: 82px;
float: right;
}
.popup_deal_content {
width: 202px;
height: 80px;
overflow: hidden;
}
.popup_deal_box p {
margin: 0;
padding: 0;
}
.popup_deal_box p {
margin: 0;
padding: 0;
}
.popup_deal_address {
font-size: 11px;
display: none;
}
.popup_deal_shrink, .popup_deal_enlarge {
font-size: 10px;
text-decoration: underline;
cursor: pointer;
position: absolute;
bottom: 0;
left: 0;
}
.popup_deal_shrink {
display: none;
}
.popup_deal_box p {
margin: 0;
padding: 0;
}

popup_deal_street p, popup_deal_city_zip p {
display: block;
-webkit-margin-before: 1em;
-webkit-margin-after: 1em;
-webkit-margin-start: 0px;
-webkit-margin-end: 0px;
}
.popup_deal_navigation span {
font-weight: bold;
}

.popup_deal_image {
border: 1px solid #EBEBEB;
}

.popup_deal_buy_button {
background: red;
width: 72px;
height: 19px;
line-height: 19px;
text-align: center;
color: #fff;
margin: 5px auto 0 auto;
background: rgb(170, 0, 0) url('".$pluginStaticURL2."button_popup.png') 0 0 no-repeat;
cursor: pointer;
}
.popup_deal_merchant {
font-weight: bold;
display: inline-block;
margin-bottom: 2px !important;
}
.popup_deal_title {
font-size: 11px;
}
.popup_deal_headline {
font-size: 13px;
border-bottom: 1px solid #D5D5D5;
padding-bottom: 1px;
margin-bottom: 5px;
height: 16px;
overflow: hidden;
}
.popup_deal_headline {
font-size: 13px;
}
.popup_deal_disount_text {
float: right;
margin-right: 5px;
}
.popup_deal_disount_percent {
float: right;
}
.popup_deal_category {
font-weight: bold;
}
.popup_deal_price {
font-weight: bold;
}
.popup_deal_disount_percent {
font-weight: bold;
}

</style>
</head>
<body style=\"border:0;padding:0;margin:0;\">";

//-------zoom s
echo "<div class=\"main_zoom_content\" id=\"main_zoom_content\" style=\"display:none;\">";

    echo "<div style=\"display:inline-block;width:100%;margin:auto;\">";
        echo "<a href=\"javascript:load_content_zoom_close();\">";
            echo "<div class=\"close_zoom_content\" id=\"close_zoom_content\" style=\"display:inline-block;float:right;margin:10px;\">";
//                        <img src=\"".$pluginStaticURL2."imgs8.png\" draggable=\"false\" style=\"position: absolute; left: -18px; top: -44px; width: 68px; height: 67px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px;\">
//                echo "<b style=\"color:#f00;\">[X]</b>";
                    echo "<div style=\"width: 24px; height: 24px; overflow: hidden; position: absolute; opacity: 0.7; right: 12px; top: 12px; z-index: 90000; cursor: pointer;\">
                        <img src=\"".$pluginStaticURL2."close.png\" draggable=\"false\" style=\"width: 24px; height: 24px; -webkit-user-select: none; border: 0px; padding: 0px; margin: 0px;\">
                        </div>";

            echo "</div>";
        echo "</a>";
    echo "</div>";

    echo "<div draggable=\"false\" style=\"position: abdolute; z-index:10009;left: 50px; right:50px; top: 50px; width: 100%px; min-height: 350px; overflow: hidden; -webkit-user-select: none; background-color: white;\">";
            echo "<div class=\"zoom_content\" id=\"zoom_content\" style=\"margin:20px;\">";
            echo "</div>";
    echo "</div>";

echo "</div>";
//-------zoom e

//--------------s
echo "<div class=\"slide-out search-bar\" id=\"search-bar\" style=\"min-width: 260px;min-height:50px;\">
        <div class=\"in clearfix\">";


        if (!isset($_GET['op']) OR !$_GET['op']=="upload_photo_a"){
            echo "<div style=\"min-width:48px;height:48px;position:inline-block;float:left;\">";
                echo "<div id=\"show_hide\" style=\"margin:0px 10px 0 0;text-align:center;\">";
                    echo "<a href=\"javascript:void(0);\" alt=\"".OW::getLanguage()->text('map', 'b_menu')."\" title=\"".OW::getLanguage()->text('map', 'b_menu')."\" ><img src=\"".$pluginStaticURL2."menu_48.png\" style=\"border:0;margin:0;padding:0;max-width:48px;max-height:48px;\"></a>";
                echo "</div>";
            echo "</div>";
        }


        if (isset($_GET['op']) AND $_GET['op']=="upload_photo_a"){
            echo "<div style=\"min-width:48px;height:48px;position:inline-block;float:left;\">";
                echo "<div style=\"margin:0px 10px 0 0;text-align:center;\">";
                    if (isset($_GET['c'])) $parx="?c=".$_GET['c'];
                        else $parx="";
                    echo "<a href=\"javascript:void(0);\" onClick=\"document.location.href ='".$curent_url."map/gmap".$parx."';\" alt=\"".OW::getLanguage()->text('map', 'b_home')."\" title=\"".OW::getLanguage()->text('map', 'b_home')."\" ><img src=\"".$pluginStaticURL2."home_48.png\" style=\"border:0;margin:0;padding:0;max-width:48px;max-height:48px;\"></a>";
                echo "</div>";
            echo "</div>";
        }


        echo "<div style=\"min-width:48px;height:48px;position:inline-block;float:left;\">";
            echo "<div style=\"margin:0px 10px 0 0;text-align:center;\">";

                echo "<a href=\"javascript:void(0);\" onClick=\"document.location.reload(true);\" alt=\"".OW::getLanguage()->text('map', 'b_reload_map')."\" title=\"".OW::getLanguage()->text('map', 'b_reload_map')."\" ><img src=\"".$pluginStaticURL2."reload_48.png\" style=\"border:0;margin:0;padding:0;max-width:48px;max-height:48px;\"></a>";
            echo "</div>";
        echo "</div>";



        if (OW::getPluginManager()->isPluginActive('mobille')){
            echo "<div style=\"min-width:48px;height:48px;position:inline-block;float:left;\">";
                echo "<div style=\"margin:0px 10px 0 0;text-align:center;\">";
                    if (isset($_GET['c'])) $parx="?mobi=".$_GET['c'];
                        else $parx="";
                    echo "<a href=\"javascript:void(0);\" onClick=\"document.location.href ='".$curent_url."mobile/v2/option/index".$parx."';\" alt=\"".OW::getLanguage()->text('map', 'b_open_mobileversion')."\" title=\"".OW::getLanguage()->text('map', 'b_open_mobileversion')."\" ><img src=\"".$pluginStaticURL2."www_48.png\" style=\"border:0;margin:0;padding:0;max-width:48px;max-height:48px;\"></a>";
                echo "</div>";
            echo "</div>";
        }


        echo "<div style=\"min-width:48px;height:48px;position:inline-block;float:left;\">";
            echo "<div style=\"margin:0px 10px 0 0;text-align:center;\">";
                    $parx="";
                    if (isset($_GET['c'])) $parx="?c=".$_GET['c'];
                    if ($parx) $parx.="&";
                    $parx .="op=upload_photo_a";
                echo "<a href=\"javascript:void(0);\" onClick=\"document.location.href ='".$curent_url."map/gmap".$parx."';\" alt=\"".OW::getLanguage()->text('map', 'b_upload_photo')."\" title=\"".OW::getLanguage()->text('map', 'b_upload_photo')."\" ><img src=\"".$pluginStaticURL2."upload_48.png\" style=\"border:0;margin:0;padding:0;max-width:48px;max-height:48px;\"></a>";
            echo "</div>";
        echo "</div>";

/*
        echo "<div style=\"min-width:48px;height:48px;position:inline-block;float:left;\">";
            echo "<div style=\"margin:0px 10px 0 0;text-align:center;\">";
                    $parx="";
                    if (isset($_GET['c'])) $parx="?c=".$_GET['c'];
                    if ($parx) $parx.="&";
                    $parx .="op=upload_photo_a";
                echo "<a href=\"javascript:void(0);\" onClick=\"document.location.href ='".$curent_url."map/gmap".$parx."';\" alt=\"".OW::getLanguage()->text('map', 'b_upload_photo')."\" title=\"".OW::getLanguage()->text('map', 'b_upload_photo')."\" ><img src=\"".$pluginStaticURL2."help_48.png\" style=\"border:0;margin:0;padding:0;max-width:48px;max-height:48px;\"></a>";
            echo "</div>";
        echo "</div>";

*/
        echo "</div>
</div>";
//--------------e




echo "<div id=\"main_map\">";


echo "<div id=\"panel\">";

        echo "<div style=\"width:100%;margin:auto;background:#039bde;color:#fff;margin-top:10px;\">";
            echo "<strong style=\"margin:10px;\">".MAP_BOL_Service::getInstance()->corectforjava(OW::getLanguage()->text('map', 'category_list')).":</strong>";
        echo "</div>";
        echo "<div id=\"categorylist\">";
        echo "<select id=\"mcategory\" name=\"mcategory\" style=\"width:100%;\">";
        echo MAP_BOL_Service::getInstance()->get_category_list($curent_cat);
        echo "</select>";
        echo "</div>";
        
        echo "<div style=\"width:100%;margin:auto;background:#039bde;color:#fff;margin-top:10px;\">";
            echo "<strong style=\"margin:10px;\">".MAP_BOL_Service::getInstance()->corectforjava(OW::getLanguage()->text('map', 'marker_list_friend')).":</strong>";
        echo "</div>";
        echo "<div id=\"markerlist\"></div>";




/*
        echo "<div style=\"width:100%;margin:auto;background:#039bde;color:#fff;margin-top:10px;\">";
            echo "<strong style=\"margin:10px;\">".MAP_BOL_Service::getInstance()->corectforjava(OW::getLanguage()->text('map', 'option')).":</strong>";
        echo "</div>";
        echo "<div id=\"option_content\">";
            echo "<div>";
            echo "</div>";
        echo "</div>";
*/
echo "</div>";


echo "<div id=\"map-container\">";

//----page start
        if (isset($_GET['op']) AND $_GET['op']=="upload_photo_a"){
            echo "<div id=\"map_page\" class=\"\" style=\"width:100%;height:100%;mrgin:auto;position:absolute;z-index:10;background:#fff;\">";
                echo "<div style=\"min-width:48px;height:48px;position:relative;float:right;z-index:11;\">";
                    echo "<div id=\"b_close_page\" style=\"margin:0px 10px 0 0;text-align:center;\">";
                        echo "<a href=\"javascript:void(0);\" alt=\"".OW::getLanguage()->text('map', 'b_close')."\" title=\"".OW::getLanguage()->text('map', 'b_close')."\" ><img src=\"".$pluginStaticURL2."close_48.png\" style=\"border:0;margin:0;padding:0;max-width:48px;max-height:48px;\"></a>";
                    echo "</div>";
                echo "</div>";

                echo "<div class=\"content_page\" style=\"width:100%;margin:auto;margin:20px 10px;\">";
                    echo MAP_BOL_Service::getInstance()->make_upload_image_form();
                echo "</div>";
            echo "</div>";
        }
//----page end


        echo "<div id=\"map\"></div>";
echo "</div>";

echo "</div>";


echo "</body>";
echo "</html>";
                }else{
                    $content=OW::getLanguage()->text('map', 'you_dont_have_position_yet');
                }
            }else{
                $content="";
                $content .="<div style=\"position:absolute;
     width:350px;
     height:300px;
     z-index:15;
     top:50%;
     left:50%;
     margin:-175px 0 0 -175px;
    background:#eee;
     border:2px solid #f00;\">";
                    $content .="<div style=\"margin:50px 20px;text-align:center;\">";
                        $content .="<a href=\"".$curent_url."sign-in?back-uri=index\">".OW::getLanguage()->text('map', 'please_config_mobilleapp')."</a>";
                    $content .="</div>";
                $content .="</div>";
//            $content="OK2";
            }
        }else{
//            $content="OK1";
                $content="";
                $content .="<div style=\"position:absolute;
     width:350px;
     height:300px;
     z-index:15;
     top:50%;
     left:50%;
     margin:-175px 0 0 -175px;
     border:2px solid #f00;\">";
                    $content .="<div style=\"margin:50px 20px;text-align:center;\">";
                        $content .="<a href=\"".$curent_url."sign-in\">".OW::getLanguage()->text('map', 'please_config_mobilleapp')."</a>";
                    $content .="</div>";
                $content .="</div>";
        }




        echo $content;
//        $this->assign('content', $content);

        exit;
    }


    public function indexscan($params) //monitoring members
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url = 'http';
        if (isset($_SERVER["HTTPS"])) {$curent_url .= "s";}
        $curent_url .= "://";
        $curent_url .= $_SERVER["SERVER_NAME"]."/";
        $curent_url=OW_URL_HOME;
        $timestamp=strtotime(date('Y-m-d H:i:s'));
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n----------POST:\n".print_r($_POST,1), FILE_APPEND | LOCK_EX);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n----------GET:\n".print_r($_GET,1), FILE_APPEND | LOCK_EX);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n----------SESSION:\n".print_r($_SESSION,1), FILE_APPEND | LOCK_EX);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n----------FILES:\n".print_r($_FILES,1), FILE_APPEND | LOCK_EX);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n----------SERVER:\n".print_r($_SERVER,1), FILE_APPEND | LOCK_EX);
        if (OW::getRequest()->isPost()){

//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", print_r($_FILES,1), FILE_APPEND | LOCK_EX);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", print_r($_POST,1), FILE_APPEND | LOCK_EX);

//uploadedfile

//            if (isset($_POST['action']) AND $_POST['action'] =="getfromfile" AND isset($_POST['file_content']) AND isset($_POST['secret'])){
            if (isset($_FILES['uploadedfile']['tmp_name']) AND isset($_POST['secret']) AND isset($_POST['slogin']) AND !$_FILES['uploadedfile']['error'] AND $_FILES['uploadedfile']['size']>0 AND is_file($_FILES['uploadedfile']['tmp_name'])){
//                $tmp= readfile($_FILES['uploadedfile']['tmp_name']);
                $tmp= file_get_contents($_FILES['uploadedfile']['tmp_name']);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", $tmp, FILE_APPEND | LOCK_EX);
                $authenticate=0;
                $auth_owner=0;
                
//                $sql="SELECT * FROM " . OW_DB_PREFIX. "map_scan WHERE `secret`='".addslashes($_POST['secret'])."' AND `active`='1' LIMIT 1";
                $sql="SELECT ms.*,users.username FROM " . OW_DB_PREFIX. "map_scan ms 
                    LEFT JOIN " . OW_DB_PREFIX. "base_user users ON (users.id=ms.id_owner) 
                WHERE ms.`secret`='".addslashes($_POST['secret'])."' AND 
                ms.`active`='1' AND users.emailVerify='1' AND (users.username='".addslashes($_POST['slogin'])."' OR users.email='".addslashes($_POST['slogin'])."') ";

                $mmb = OW::getDbo()->queryForList($sql);    
                if (isset($mmb[0]['id']) AND $mmb[0]['id']>0){
                    $authenticate=$mmb[0]['id'];
                    $auth_owner=$mmb[0]['id_owner'];
                }
                if ($authenticate>0 AND $auth_owner>0){                    

//                $tmp= base64_decode($_POST['file_content']);
                $tmp_tab=explode("#n_#_n#",$tmp);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n-SS--------------------------------\n", FILE_APPEND | LOCK_EX);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", print_r($tmp_tab,1), FILE_APPEND | LOCK_EX);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", "\n-EE--------------------------------\n", FILE_APPEND | LOCK_EX);
            
                    for ($i=0;$i<count($tmp_tab);$i++){
                        $line_tab=explode("&",$tmp_tab[$i]);

                        $latitude=0;
                        $longitude=0;
                        $datax=$timestamp=date('Y-m-d H:i:s');
                        $altitude="NULL";
                        $accuracy="NULL";
                        $provider="NULL";
                        $bearing="NULL";
                        $speed="NULL";
                        $battlevel="NULL";
                        $charging="NULL";
                        $deviceid="NULL";
                        $subscriberid="NULL";
                        $secret="";

                        for ($j=0;$j<count($line_tab);$j++){
                            list($nn,$vv)=explode("=",$line_tab[$j],2);
//        echo $nn."--".$vv;
                            if ($nn=="latitude" AND $vv){
                                $latitude=$vv;
                            }else
                            if ($nn=="longitude" AND $vv){
                                $longitude=$vv;
                            }else
                            if ($nn=="time" AND $vv){
                                $datax=$vv;
                            }else
                            if ($nn=="altitude" AND $vv){
                                $altitude=addslashes($vv);
                            }else
                            if ($nn=="accuracy" AND $vv){
                                $accuracy=addslashes($vv);
                            }else
                            if ($nn=="provider" AND $vv){
                                $provider=addslashes($vv);
                            }else
                            if ($nn=="bearing" AND $vv){
                                $bearing=addslashes($vv);
                            }else
                            if ($nn=="speed" AND $vv){
                                $speed=addslashes($vv);
                            }else
                            if ($nn=="battlevel" AND $vv){
                                $battlevel=addslashes($vv);
                            }else
                            if ($nn=="charging" AND $vv){
                                $charging=addslashes($vv);
                            }else
                            if ($nn=="deviceid" AND $vv){
                                $deviceid=addslashes($vv);
                            }else
                            if ($nn=="subscriberid" AND $vv){
                                $subscriberid=addslashes($vv);
                            }else 
                            if ($nn=="secret" AND $vv){
                                $secret=$vv;
                            }
                        }//for j
                        if ($latitude AND $longitude AND $_POST['secret']==$secret){
                            $sql="INSERT INTO " . OW_DB_PREFIX. "map_scan_data (
                                id_scan,id_owner,
                                d_latitude,d_longitude,d_altitude,
                                d_time,
                                d_accuracy,d_provider,d_bearing,d_speed,d_battlevel,d_charging,d_deviceid,d_subscriberid,
                                source_post_type,
add_timestamp
                            )VALUES(
                                '".addslashes($authenticate)."','".addslashes($auth_owner)."',
                                '".addslashes($latitude)."','".addslashes($longitude)."','".$altitude."',
                                '".addslashes($datax)."',
                                '".$accuracy."','".$provider."','".$bearing."','".$speed."','".$battlevel."','".$charging."','".$deviceid."','".$subscriberid."',
                                'file',
'".addslashes($datax)."'
                            )ON DUPLICATE KEY UPDATE duplicate_times=duplicate_times+1, d_time='".addslashes($datax)."', add_timestamp=NOW() ";
//                            )ON DUPLICATE KEY UPDATE duplicate_times=duplicate_times+1, d_time='".addslashes($datax)."', add_timestamp='".addslashes($datax)."'";
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.txt", $sql, FILE_APPEND | LOCK_EX);
                            OW::getDbo()->insert($sql);
                            
                        }
                    }//for $i
                }//if auth OK
            //end if action upload file
            }else if (isset($_POST['latitude']) AND isset($_POST['longitude']) AND isset($_POST['secret']) AND isset($_POST['slogin'])){
//                $sql="SELECT * FROM " . OW_DB_PREFIX. "map_scan WHERE `secret`='".addslashes($_POST['secret'])."' AND `active`='1' LIMIT 1";
                $sql="SELECT ms.*,users.username FROM " . OW_DB_PREFIX. "map_scan ms 
                    LEFT JOIN " . OW_DB_PREFIX. "base_user users ON (users.id=ms.id_owner) 
                WHERE ms.`secret`='".addslashes($_POST['secret'])."' AND 
                ms.`active`='1' AND users.emailVerify='1' AND (users.username='".addslashes($_POST['slogin'])."' OR users.email='".addslashes($_POST['slogin'])."') ";
                $mmb = OW::getDbo()->queryForList($sql);
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxXXXXXXXXXXXXXXXX.txt", $sql, FILE_APPEND | LOCK_EX);                
                if (isset($mmb[0]['id']) AND $mmb[0]['id']>0){

                    if (isset($_POST['time'])){
//                    $datax=$timestamp=strtotime(date('Y-m-d H:i:s',$_POST['time']));
                        $datax=$timestamp=$_POST['time'];
                    }else{
//                        $datax=$timestamp=strtotime(date('Y-m-d H:i:s'));
                        $datax=$timestamp=date('Y-m-d H:i:s');
                    }
                    if (isset($_POST['altitude'])){
                        $altitude=$_POST['altitude'];
                    }else{
                        $altitude="";
                    }
                    if (isset($_POST['accuracy'])){
                        $accuracy=$_POST['accuracy'];
                    }else{
                        $accuracy="";
                    }
                    if (isset($_POST['provider'])){
                        $provider=$_POST['provider'];
                    }else{
                        $provider="";
                    }
                    if (isset($_POST['bearing'])){
                        $bearing=$_POST['bearing'];
                    }else{
                        $bearing="";
                    }
                    if (isset($_POST['speed'])){
                        $speed=$_POST['speed'];
                    }else{
                        $speed="";
                    }
                    if (isset($_POST['battlevel'])){
                        $battlevel=$_POST['battlevel'];
                    }else{
                        $battlevel="";
                    }
                    if (isset($_POST['charging'])){
                        $charging=$_POST['charging'];
                    }else{
                        $charging="";
                    }
                    if (isset($_POST['deviceid'])){
                        $deviceid="'".addslashes($_POST['deviceid'])."'";
                    }else{
                        $deviceid="NULL";
                    }
                    if (isset($_POST['subscriberid'])){
                        $subscriberid="'".addslashes($_POST['subscriberid'])."'";
                    }else{
                        $subscriberid="NULL";
                    }

                    $sql="INSERT INTO " . OW_DB_PREFIX. "map_scan_data (
                        id_scan,id_owner,
                        d_latitude,d_longitude,d_altitude,
                        d_time,
                        d_accuracy,d_provider,d_bearing,d_speed,d_battlevel,d_charging,
                        d_deviceid,d_subscriberid,
add_timestamp
                    )VALUES(
                        '".addslashes($mmb[0]['id'])."','".addslashes($mmb[0]['id_owner'])."',
                        '".addslashes($_POST['latitude'])."','".addslashes($_POST['longitude'])."','".addslashes($altitude)."',
                        '".addslashes($datax)."',
                        '".addslashes($accuracy)."','".addslashes($provider)."','".addslashes($bearing)."','".addslashes($speed)."','".addslashes($battlevel)."','".addslashes($charging)."',
                        '".$deviceid."','".$subscriberid."',
NOW()
                    ) ON DUPLICATE KEY UPDATE duplicate_times=duplicate_times+1, d_time='".addslashes($datax)."',  add_timestamp=NOW() ";
//file_put_contents("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxXXXXXXXXXXXXXXXX.txt", $sql, FILE_APPEND | LOCK_EX);                
                    OW::getDbo()->insert($sql);
                    echo "Last Saved: ".date("Y-m-d H:i:s");
                }else{
                    echo "You are cool too... :) (check secret and secret2...)";
                }
            }else{
                echo "You are cool... :) (no found all require data...)";
            }
        }else{//if post
            echo "i like You too... :)";
        }
        exit;	
    }

    public function indexdownloadapp($params)
    {	
        $allow_for_guests=1;
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        if (($id_user OR $allow_for_guests) AND MAP_BOL_Service::getInstance()->is_file_application()){
            $pluginStaticDir =OW::getPluginManager()->getPlugin('map')->getRootDir();
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename("map_mobile.apk"));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($pluginStaticDir."map_mobile.apk"));
            ob_clean();
            flush();
            readfile($pluginStaticDir."map_mobile.apk");
            exit;
        }else{
            OW::getApplication()->redirect($curent_url."map");
        }
        exit;
//        $this->assign('content', $content);
    }

    public function indexmapconf($params)
    {	
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $content="";
//        if ($id_user>0 AND isset($params['checkowner']) AND $params['checkowner']>0 AND $params['checkowner']==$id_user AND MAP_BOL_Service::getInstance()->is_file_application()){
        if ($id_user>0 AND isset($params['checkowner']) AND $params['checkowner']>0 AND $params['checkowner']==$id_user){

            if (isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),3,5)){
                if (isset($_POST['c_map_mobile_active']) AND $_POST['c_map_mobile_active']==1) $active=1;
                    else $active=0;
                if (isset($_POST['c_map_mobile_secret1'])) $secret1=$_POST['c_map_mobile_secret1'];
                    else $secret1="";
                if (isset($_POST['c_map_mobile_secret2'])) $secret2=$_POST['c_map_mobile_secret2'];
                    else $secret2="";
                $sql="INSERT INTO " . OW_DB_PREFIX. "map_scan (
                    id,id_owner, active,  secret,  hash_unique
                ) VALUES(
                    '','".addslashes($id_user)."','".addslashes($active)."','".addslashes($secret1)."','".addslashes($secret2)."'
                ) ON DUPLICATE KEY UPDATE active='".addslashes($active)."',  secret='".addslashes($secret1)."',  hash_unique='".addslashes($secret2)."' ";
                OW::getDbo()->insert($sql);
            }

            $sql="SELECT * FROM " . OW_DB_PREFIX. "map_scan WHERE id_owner='".addslashes($id_user)."' LIMIT 1";
            $mmb = OW::getDbo()->queryForList($sql);
            $active=0;
            $secret1="";
            $secret2="";
            if (isset($mmb[0]['id']) AND $mmb[0]['id']>0){
                $active=$mmb[0]['active'];
                $secret1=$mmb[0]['secret'];
                $secret2=$mmb[0]['hash_unique'];
            }
            

            $content .="<form method=\"POST\" action=\"".$curent_url."map/confowner/".$params['checkowner']."\">";
            $content .="<input type=\"hidden\" name=\"ss\" value=\"".substr(session_id(),3,5)."\">";

            $content .="<h1 class=\"ow_stdmargin ow_ic_file\">".OW::getLanguage()->text('map', 'mobile_map_setting')."</h1>";
            $content .="<div class=\"ow_content\">
                <form action=\"\" method=\"post\" style=\"width:100%;\">
                <input type=\"hidden\" name=\"save\" value=\"besave\">

                <table style=\"width:100%;\" class=\"ow_table_1 ow_form ow_stdmargin\"><tbody>
                <tr><th class=\"ow_name ow_txtleft\" colspan=\"3\">
                <span class=\"ow_section_icon ow_ic_gear_wheel\">".OW::getLanguage()->text('map', 'setting')."</span>
                </th></tr>

                <tr><td><b>".OW::getLanguage()->text('map', 'active_your_mobile_map').":</b></td>
                <td>
                    <select name=\"c_map_mobile_active\">";
                    if ($active=="0" OR !$active) $sel=" selected ";
                        else $sel="";
                    $content .="<option ".$sel." value=\"0\">".OW::getLanguage()->text('map', 'no')."</option>";
                    if ($active==1) $sel=" selected ";
                        else $sel="";
                    $content .="<option ".$sel." value=\"1\">".OW::getLanguage()->text('map', 'yes')."</option>";
                    $content .="</select>
                </td></tr>

                <tr><td><b>".OW::getLanguage()->text('map', 'secret_password_one').":</b></td>
                <td>
                    <input type=\"text\" name=\"c_map_mobile_secret1\" value=\"".$secret1."\" style=\"display:inline-block;width:150px;\">
                </td></tr>

                <tr><td><b>".OW::getLanguage()->text('map', 'secret_password_two').":</b></td>
                <td>
                    <input type=\"text\" name=\"c_map_mobile_secret2\" value=\"".$secret2."\" style=\"display:inline-block;width:150px;\">
                </td></tr>


                <tr>
                <td colspan=\"2\">
                ".OW::getLanguage()->text('map', 'mobile_info')."
                </td></tr>

                <tr>
                <td colspan=\"2\">";

                if (MAP_BOL_Service::getInstance()->is_file_application()){
                    $content .="<a href=\"".$curent_url."map/downloadapplication\">".OW::getLanguage()->text('map', 'download_application_mobile')."</a>";
                }

                $content .="</td></tr>

                <tr><td colspan=\"2\"><div class=\"clearfix ow_submit ow_smallmargin\">
                <div class=\"ow_center\">
                    <span class=\"ow_button\">
                        <span class=\"ow_positive\">
                            <input type=\"submit\" name=\"saveb\" value=\"".OW::getLanguage()->text('map', 'save')."\" class=\"ow_ic_save ow_positive\">
                        </span>
                    </span>
                </div>
                </div></td>
                </tr>

            </tbody></table></form>
            </div>";


            $content .="</form>";

        }else{
            OW::getApplication()->redirect($curent_url."map");
        }
        $this->assign('content', $content);
    }

    public function indexadsense($params)
    {	

        echo "<html>";
        echo "<head>";
        echo "</head>";
        echo "<body style=\"width:100%;margin:0;padding:0;background:#f00;\">";

        $GLOBALS['google']['client']='ca-mb-pub-6284883887062470';
        $GLOBALS['google']['https']=$this->read_global('HTTPS');
        $GLOBALS['google']['ip']=$this->read_global('REMOTE_ADDR');
        $GLOBALS['google']['markup']='xhtml';
        $GLOBALS['google']['output']='xhtml';
        $GLOBALS['google']['ref']=$this->read_global('HTTP_REFERER');
        $GLOBALS['google']['slotname']='1136492375';
        $GLOBALS['google']['url']=$this->read_global('HTTP_HOST') . $this->read_global('REQUEST_URI');

        $GLOBALS['google']['useragent']=$this->read_global('HTTP_USER_AGENT');
        $google_dt = time();
        $this->google_set_screen_res();
        $this->google_set_muid();
        $this->google_set_via_and_accept();



        $google_ad_handle = @fopen($this->google_get_ad_url(), 'r');
        if ($google_ad_handle) {
          while (!feof($google_ad_handle)) {
            echo fread($google_ad_handle, 8192);
          }
          fclose($google_ad_handle);
        }
        echo "</body>";
        echo "</html>";

        exit;
    }

//============
public function read_global($var) {
  return isset($_SERVER[$var]) ? $_SERVER[$var]: '';
}

public function google_append_url(&$url, $param, $value) {
  $url .= '&' . $param . '=' . urlencode($value);
}

public function google_append_globals(&$url, $param) {
 $this-> google_append_url($url, $param, $GLOBALS['google'][$param]);
}

public function google_append_color(&$url, $param) {
  global $google_dt;
  $color_array = explode(',', $GLOBALS['google'][$param]);
  $this->google_append_url($url, $param,
                    $color_array[$google_dt % count($color_array)]);
}

public function google_set_screen_res() {
   $screen_res = $this->read_global('HTTP_UA_PIXELS');
  if ($screen_res == '') {
    $screen_res = $this->read_global('HTTP_X_UP_DEVCAP_SCREENPIXELS');
  }
  if ($screen_res == '') {
    $screen_res = $this->read_global('HTTP_X_JPHONE_DISPLAY');
  }
  $res_array = preg_split('/[x,*]/', $screen_res);
  if (count($res_array) == 2) {
    $GLOBALS['google']['u_w']=$res_array[0];
    $GLOBALS['google']['u_h']=$res_array[1];
  }
}

public function google_set_muid() {
  $muid = $this->read_global('HTTP_X_DCMGUID');
  if ($muid != '') {
    $GLOBALS['google']['muid']=$muid;
     return;
  }
  $muid = $this->read_global('HTTP_X_UP_SUBNO');
  if ($muid != '') {
    $GLOBALS['google']['muid']=$muid;
     return;
  }
  $muid = $this->read_global('HTTP_X_JPHONE_UID');
  if ($muid != '') {
    $GLOBALS['google']['muid']=$muid;
     return;
  }
  $muid = $this->read_global('HTTP_X_EM_UID');
  if ($muid != '') {
    $GLOBALS['google']['muid']=$muid;
     return;
  }
}

public function google_set_via_and_accept() {
  $ua = $this->read_global('HTTP_USER_AGENT');
  if ($ua == '') {
    $GLOBALS['google']['via']=$this->read_global('HTTP_VIA');
    $GLOBALS['google']['accept']=$this->read_global('HTTP_ACCEPT');
  }
}

public function google_get_ad_url() {
  $google_ad_url = 'http://pagead2.googlesyndication.com/pagead/ads?';
  $this->google_append_url($google_ad_url, 'dt',
                    round(1000 * array_sum(explode(' ', microtime()))));
  foreach ($GLOBALS['google'] as $param => $value) {
    if (strpos($param, 'color_') === 0) {
     $this-> google_append_color($google_ad_url, $param);
    } else if (strpos($param, 'url') === 0) {
      $google_scheme = ($GLOBALS['google']['https'] == 'on')
          ? 'https://' : 'http://';
      $this->google_append_url($google_ad_url, $param,
                        $google_scheme . $GLOBALS['google'][$param]);
    } else {
      $this->google_append_globals($google_ad_url, $param);
    }
  }
  return $google_ad_url;
}



//==========================	
    public function del($params)
    {	
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $iddelmark=0;
        if (isset($params['id_mark']) AND $params['id_mark']>0){
            $iddelmark=$params['id_mark'];
        }
//print_r($_GET);
                    if ($is_admin){
                        $add=" ";
                    }else{
                        $add=" AND id_owner='".addslashes($id_user)."' ";
                    }

        if ($iddelmark>0 AND $id_user>0 AND isset($_GET['ss']) AND $_GET['ss']==substr(session_id(),3,5) ){
            $query = "DELETE FROM " . OW_DB_PREFIX. "map WHERE id='".addslashes($iddelmark)."' ".$add." LIMIT 1";
//echo $query;
            OW::getDbo()->query($query);


//                    $path_f=MAP_BOL_Service::getInstance()->get_plugin_dir('map');
//                    $path_f .=$id_user.DS;
//                    $new_image=uniqid('', true);
//                    $filex=$last_insertid."_".$new_image.".jpg";
//                    $filex_mini=$last_insertid."_".$new_image."_mini.jpg";

//--del img start
            $path_f=MAP_BOL_Service::getInstance()->get_plugin_dir('map');
            $path_f .=$id_user.DS;

                    if ($is_admin){
                        $add=" ";
                    }else{
                        $add=" AND id_ownerm='".addslashes($id_user)."' ";
                    }

            $query = "SELECT * FROM " . OW_DB_PREFIX. "map_images 
            WHERE id_map='".addslashes($iddelmark)."' ".$add;
            $arr = OW::getDbo()->queryForList($query);
            foreach ( $arr as $value ){
                $img=$value['id_map']."_".$value['image'].".".$value['itype'];
                MAP_BOL_Service::getInstance()->file_delete($path_f.$img);
                $img=$value['id_map']."_".$value['image']."_mini.".$value['itype'];
                MAP_BOL_Service::getInstance()->file_delete($path_f.$img);
            }
            $sql="DELETE FROM " . OW_DB_PREFIX. "map_images WHERE id_map='".addslashes($iddelmark)."' ".$add;
            OW::getDbo()->query($sql);
//--del img end


//echo "end";exit;

            OW::getFeedback()->info(OW::getLanguage()->text('map', 'deleted_succedfull'));

            if (isset($_POST['latMap']) AND $_POST['latMap']!=""){
                $la=$_POST['latMap'];
            }else if (isset($_GET['la']) AND $_GET['la']!=""){
                $la=$_GET['la'];
            }else{
                $la=0;
            }

            if (isset($_POST['lngMap']) AND $_POST['lngMap']!=""){
                $ln=$_POST['lngMap'];
            }else if (isset($_GET['ln']) AND $_GET['ln']!=""){
                $ln=$_GET['ln'];
            }else{
                $ln=0;
            }

            if (isset($_POST['zoomMap']) AND $_POST['zoomMap']!=""){
                $zo=$_POST['zoomMap'];
            }else if (isset($_GET['zo']) AND $_GET['zo']!=""){
                $zo=$_GET['zo'];
            }else{
                $zo=0;
            }
//echo "map?mapmode=ed&la=".$la."&ln=".$ln."&zo=".$zo;
//exit;
//            if ($is_admin AND ){
            OW::getApplication()->redirect($curent_url."map?mapmode=ed&la=".$la."&ln=".$ln."&zo=".$zo);
        }else{
//exit;
            OW::getApplication()->redirect($curent_url."map");
        }
        exit;
    }
	
	

    public function indexcat($params)
    {
    $content="";
$curent_url=OW_URL_HOME;
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();


        if (!$is_admin) {
            OW::getApplication()->redirect($curent_url."map");
        }
//print_r($params);exit;
//print_r($_POST);exit;
//echo $_POST['ss']."--".substr(session_id(),3,5);
//exit;


        if (isset($params['ctab']) AND $params['ctab']=="save" AND isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),3,5)){
//print_r($_POST);
//exit;

                if (isset($_POST['f_name'])){

                    $fname=$_POST['f_name'];
                    $factive=$_POST['f_active'];
                    $fdelete=$_POST['f_delete'];
                    $fid2=$_POST['f_id2'];

                    $query = "SELECT * FROM " . OW_DB_PREFIX. "map_category 
                    WHERE 1 ORDER BY name";
                    $arr = OW::getDbo()->queryForList($query);
                    foreach ( $arr as $value ){
                        if (isset($fname[$value['id']]) AND $fname[$value['id']]){
                            $name=$fname[$value['id']];
                        }else{
                            $name="Category";
                        }
                        if (isset($factive[$value['id']]) AND $factive[$value['id']]){
                            $active=1;
                        }else{
                            $active=0;
                        }

                        if (isset($fid2[$value['id']]) AND $fid2[$value['id']]>0){
                            $id2=$fid2[$value['id']];
                        }else{
                            $id2=0;
                        }
                        if (isset($fdelete[$value['id']]) AND $fdelete[$value['id']]==1){
                            $sql="DELETE FROM " . OW_DB_PREFIX. "map_category 
                            WHERE id='".$value['id']."' LIMIT 1";
                            OW::getDbo()->query($sql);
                        }else{
                            $sql="UPDATE " . OW_DB_PREFIX. "map_category SET 
                                name='".addslashes($name)."',
                                active='".addslashes($active)."',
                                id2='".addslashes($id2)."' 
                            WHERE id='".$value['id']."' LIMIT 1";
                            OW::getDbo()->query($sql);
                        }
                    }//for
                }
            
                    if (isset($_POST['f_name_new']) AND $_POST['f_name_new']){
                        if (isset($_POST['f_active_new']) AND $_POST['f_active_new']) $active=1;
                            else $active=0;
                        if (isset($_POST['f_id2_new']) AND $_POST['f_id2_new']>0){
                            $id2=$_POST['f_id2_new'];
                        }else{
                            $id2=0;
                        }
                        $sql="INSERT INTO " . OW_DB_PREFIX. "map_category (
                            id,  id2 ,    active , name ,   name_translate
                        )VALUES(
                            '','".addslashes($id2)."','".addslashes($active)."','".addslashes($_POST['f_name_new'])."',''
                        )";
                        OW::getDbo()->query($sql);
                    }


            OW::getApplication()->redirect($curent_url."map/tabc/category");
            exit;
        }

        $query = "SELECT * FROM " . OW_DB_PREFIX. "map_category 
        WHERE id2='0' ORDER BY name";
        $contentt="";
        $arr = OW::getDbo()->queryForList($query);
        $alt=1;
        foreach ( $arr as $value ){
            $contentt .="<tr>";

            $contentt .="<td class=\"ow_alt".$alt." \">";
            $contentt .=$value['id'];
            $contentt .="</td>";

            $contentt .="<td class=\"ow_alt".$alt." \">";
            $contentt .="<input type=\"hidden\" name=\"f_id2[".$value['id']."]\" value=\"0\">";
            $contentt .="<b>".OW::getLanguage()->text('map', 'maincategory')."</b>";
            $contentt .="</td>";

            $contentt .="<td class=\"ow_alt".$alt." \">";
            $contentt .="<div style=\"\"><input type=\"text\" name=\"f_name[".$value['id']."]\" value=\"".stripslashes($value['name'])."\"></div>";
            $contentt .="</td>";


            $contentt .="<td class=\"ow_alt".$alt." \">";
            if ($value['active']==1) $sel=" CHECKED ";
                else $sel="";
            $contentt .="<input ".$sel." type=\"checkbox\" name=\"f_active[".$value['id']."]\" value=\"1\">";
            $contentt .="</td>";
            $contentt .="<td class=\"ow_alt".$alt." \">";
            $sel="";
            $contentt .="<input style=\"border:2px solid #f00;\" ".$sel." type=\"checkbox\" name=\"f_delete[".$value['id']."]\" value=\"1\">";
            $contentt .="</td>";
            $contentt .="</tr>";
//---------2222 st
        $query2 = "SELECT * FROM " . OW_DB_PREFIX. "map_category 
        WHERE id2='".addslashes($value['id'])."' ORDER BY name";
//        $contentt="";
        $arr2 = OW::getDbo()->queryForList($query2);
        $alt2=1;
        foreach ( $arr2 as $value2 ){

            $alt++;
            if ($alt>2){
                $alt=1;
            }


            $contentt .="<tr>";

            $contentt .="<td class=\"ow_alt".$alt." \">";
            $contentt .=$value2['id'];
            $contentt .="</td>";

            $contentt .="<td class=\"ow_alt".$alt." \">";
//            $contentt .="<input type=\"text\" name=\"f_id2[".$value2['id']."]\" value=\"".stripslashes($value2['id2'])."\">";
            $contentt .="<select name=\"f_id2[".$value2['id']."]\" >";
            $contentt .=MAP_BOL_Service::getInstance()->get_category(0,$value2['id2'],0);
            $contentt .="</select>";
            $contentt .="</td>";

            $contentt .="<td class=\"ow_alt".$alt." \">";
            $contentt .="<div style=\"margin-left:20px;\"><input type=\"text\" name=\"f_name[".$value2['id']."]\" value=\"".stripslashes($value2['name'])."\"></div>";
            $contentt .="</td>";


            $contentt .="<td class=\"ow_alt".$alt." \">";
            if ($value2['active']==1) $sel=" CHECKED ";
                else $sel="";
            $contentt .="<input ".$sel." type=\"checkbox\" name=\"f_active[".$value2['id']."]\" value=\"1\">";
            $contentt .="</td>";
            $contentt .="<td class=\"ow_alt".$alt." \">";
            $sel="";
            $contentt .="<input style=\"border:2px solid #f00;\" ".$sel." type=\"checkbox\" name=\"f_delete[".$value2['id']."]\" value=\"1\">";
            $contentt .="</td>";
            $contentt .="</tr>";

//            $alt2++;
//            if ($alt2>2){
//                $alt2=1;
//            }
        }
//---------2222 en

            $alt++;
            if ($alt>2){
                $alt=1;
            }
        }
        
        if ($contentt){
            $content .="<form method=\"POST\" action=\"".$curent_url."map/tabc/save\">";
            $content .="<input type=\"hidden\" name=\"ss\" value=\"".substr(session_id(),3,5)."\">";
            $content .="<table class=\"ow_table_1 ow_form\">";
            $content .="<tr class=\"ow_tr_first\">";

            $content .="<th>";
            $content .="<b>ID</b>";
            $content .="</th>";

            $content .="<th>";
            $content .="<b>".OW::getLanguage()->text('map', 'subcategory')."</b>";
            $content .="</th>";

            $content .="<th>";
            $content .="<b>".OW::getLanguage()->text('map', 'cname')."</b>";
            $content .="</th>";
            $content .="<th>";
            $content .="<b>".OW::getLanguage()->text('map', 'cactive')."</b>";
            $content .="</th>";
            $content .="<th>";
            $content .="<b>".OW::getLanguage()->text('map', 'cdelete')."</b>";
            $content .="</th>";
            $content .="</tr>";
            $content .= $contentt;

            $content .="<tr>";
            $content .="<td colspan=\"5\">";
            $content .="<b>".OW::getLanguage()->text('map', 'cadd_new').":</b>";
            $content .="</td>";
            $content .="</tr>";

            $content .="<tr>";

            $content .="<td>";
            $content .="</td>";

            $content .="<td>";
//            $content .="<input type=\"text\" name=\"f_id2\" value=\"0\">";
            $content .="<select name=\"f_id2_new\">";
//            $content .=get_category($id_master=0,$selected=0, $disable_cat=0
            $content .="<option value=\"0\">-- ".OW::getLanguage()->text('map', 'maincategory')." --</option>";
            $content .=MAP_BOL_Service::getInstance()->get_category(0,0,0);
            $content .="</select>";
            $content .="</td>";

            $content .="<td>";
            $content .="<input type=\"text\" name=\"f_name_new\" value=\"\">";
            $content .="</td>";
            $content .="<td >";
            $content .="<input checked type=\"checkbox\" name=\"f_active_new\" value=\"1\">";
            $content .="</td>";
            $content .="<td >";

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
        }

        $content=MAP_BOL_Service::getInstance()->make_tabs('category',$content);
        $this->assign('content', $content);
    }
	
	
    public function index($params)
    {
//echo "SdfsDF";exit;
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
                    $curent_url = 'http';
                    if (isset($_SERVER["HTTPS"])) {$curent_url .= "s";}
                    $curent_url .= "://";
                    $curent_url .= $_SERVER["SERVER_NAME"]."/";
$curent_url=OW_URL_HOME;

$pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.'map'.DS;
$pluginStaticURL2=OW::getPluginManager()->getPlugin('map')->getStaticUrl();

$columns=3;

$max_check_category=5;

//$perpage=300;
$perpage=5000;

//$perpage=OW::getConfig()->getValue('map', 'perpage');//not usd
//if (!$perpage) $perpage=300;
if (!$perpage) $perpage=5000;
$total_was=0;


$ideditmark=0;
if (isset($_GET['mapmode']) AND $_GET['mapmode']=="ed"){
    $mapmode="edit";
    if (isset($params['id_mark']) AND $params['id_mark']>0){
//map/edit/:id_mark
        $ideditmark=$params['id_mark'];
    }

}else{
    $mapmode="";
}

if (isset($params['ctab']) AND $params['ctab']!=""){
    $ctab=$params['ctab'];
}else{
//    $ctab="all";
    if (isset($_GET['ctab']) AND $_GET['ctab']){
        $ctab=$_GET['ctab'];
    }else{
        $ctab="map";
    }
}

if (isset($_GET['searchmap']) AND $_GET['searchmap']!=""){
    $querymap=$_GET['searchmap'];
}else{
    $querymap="";
}
//print_r($_GET);exit;

//print_r($_POST);exit;

if (isset($_POST['dosavemarker']) AND $_POST['dosavemarker']!="" AND $id_user>0 AND isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),3,5) ){
//    echo "afsdF";
//print_r($_POST);exit;
//    
    if (!isset($_POST['mtitle']) OR !$_POST['mtitle']) $_POST['mtitle']=OW::getLanguage()->text('map', 'no_title');
    if (!isset($_POST['f_category']) OR !$_POST['f_category']) $_POST['f_category']=0;

$was_set_asdefault=false;

//map/edit/10?mapmode=ed
//ttid\" value=\"".$valuex['id']."_".$id_user."\">
    $pi=0;
    $ui=0;
    if (isset($_POST['ttid']) AND $_POST['ttid']!=""){
        list($pi,$ui)=explode("_",$_POST['ttid']);
    }

    if (!$pi){
        $query = "SELECT * FROM " . OW_DB_PREFIX. "map  
        WHERE id_owner='".addslashes($id_user)."' AND lat='".addslashes($_POST['latMap'])."' AND lon='".addslashes($_POST['lngMap'])."' LIMIT 1";
//echo $query;exit;
        $arrs = OW::getDbo()->queryForList($query);
        if (isset($arrs[0]) AND $arrs[0]['id']>0){
            $pi=$arrs[0]['id'];
            $_POST['f_category']=$arrs[0]['id_cat'];
            if ($arrs[0]['name']){
                $_POST['mtitle']=$arrs[0]['name'];
            }
            if ($arrs[0]['ico']){
                $_POST['f_iconmarker']=$arrs[0]['ico'];
            }
            $_POST['mpdescription']=$arrs[0]['desc'];
            $_POST['zoomMap']=$arrs[0]['zoom'];

            $_POST['f_promotiontype']=$arrs[0]['type_promo'];
            $_POST['f_mtags']=$arrs[0]['tags'];
            
        }
    }

    if (!isset($_POST['f_iconmarker']) OR $_POST['f_iconmarker']==""){
        $_POST['f_iconmarker']="world.png";
    }

//        $content .="<input type=\"file\" name=\"imgs[]\" id=\"file\">";
//imgs
//print_r($_FILES);
//print_r($_POST);exit;
//--del start
                    if ($is_admin){
                        $add=" ";
                    }else{
                        $add=" AND id_ownerm='".addslashes($id_user)."' ";
                    }
    if (isset($_POST['delimg']) AND is_array($_POST['delimg'])){
        $ddel=$_POST['delimg'];
        for ($i=0;$i<count($ddel);$i++){
//            echo $ddel[$i]."<br>";
//del_24_515694e8c13ad5.82177638
            list($info,$marker,$imgdel)=explode("_",$ddel[$i]);
//echo $imgdel."--".$info."--".$marker."---".$last_insertid;exit;
//52559847e40dc8.68897554--del--3
            if ($imgdel AND $info=="del" AND $marker>0){

//---------------------------------------------------------------------------del img start
//print_r($_POST);exit;
        if ($is_admin){
                $query = "SELECT * FROM " . OW_DB_PREFIX. "map  
                WHERE id='".addslashes($marker)."' LIMIT 1";
                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $iduu=$arr[0]['id_owner'];
                }else{
                    $iduu=0;
                }
        }else{
            $iduu=$id_user;
        }
//echo $iduu;exit;
            if ($iduu>0){
                $path_f=MAP_BOL_Service::getInstance()->get_plugin_dir('map');
                $path_f .=$iduu.DS;

                $query = "SELECT * FROM " . OW_DB_PREFIX. "map_images 
                WHERE id_map='".addslashes($marker)."' ".$add." AND image='".addslashes($imgdel)."' LIMIT 1";
//echo $query;exit;
                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $value=$arr[0];
                    $img=$value['id_map']."_".$value['image'].".".$value['itype'];
                    MAP_BOL_Service::getInstance()->file_delete($path_f.$img);
//echo $path_f.$img;
                    $img=$value['id_map']."_".$value['image']."_mini.".$value['itype'];
                    MAP_BOL_Service::getInstance()->file_delete($path_f.$img);
//echo $path_f.$img;

                    $sql="DELETE FROM " . OW_DB_PREFIX. "map_images WHERE id_map='".addslashes($marker)."' ".$add." AND image='".addslashes($imgdel)."' LIMIT 1";
                    OW::getDbo()->query($sql);            
//echo $sql;
                }
            }//if $iduu>0
//--del img end
            }
        }//for
    }
//-------------------------------------------------------------del end





//print_r($_POST);exit;

    if ($ideditmark>0 AND $pi>0 AND $pi==$ideditmark AND $ui>0 AND $ui==$id_user){

        if (isset($_POST['defimg'])){
            list($dinfo,$dmarker,$dimg)=explode("_",$_POST['defimg']);      
            if ($dinfo=="def" AND $dmarker>0 AND $dmarker==$ideditmark AND $dimg!=""){
                if ($is_admin){
                    $add=" ";
                }else{
                    $add=" AND id_ownerm='".addslashes($id_user)."' ";
                }

                $sql="UPDATE " . OW_DB_PREFIX. "map_images SET is_default='0' WHERE id_map='".addslashes($ideditmark)."' ".$add;
                OW::getDbo()->query($sql);

                $sql="UPDATE " . OW_DB_PREFIX. "map_images SET is_default='1' WHERE id_map='".addslashes($ideditmark)."' ".$add." AND image='".addslashes($dimg)."' LIMIT 1";
                OW::getDbo()->query($sql);
                $was_set_asdefault=true;
            }
        }

        if ($is_admin){
            $add=" ";
        }else{
            $add=" AND id_owner='".addslashes($id_user)."' ";
        }

$addxx="";
if ($is_admin){
    $addxx=", type_promo='".addslashes($_POST['f_promotiontype'])."' ";
}
        $query = "UPDATE " . OW_DB_PREFIX. "map SET 
            `name`='".addslashes($_POST['mtitle'])."',
`id_cat`='".addslashes($_POST['f_category'])."',
            `desc`='".addslashes($_POST['mpdescription'])."',
            `lat`='".addslashes($_POST['latMap'])."',
            `lon`='".addslashes($_POST['lngMap'])."',
            `zoom`='".addslashes($_POST['zoomMap'])."',
            `ico`= '".addslashes($_POST['f_iconmarker'])."',
`tags`='".addslashes($_POST['f_mtags'])."' 
".$addxx."
 
        WHERE id='".addslashes($ideditmark)."' ".$add." LIMIT 1";

        OW::getDbo()->query($query);
//echo $query;exit;
        $last_insertid=$ideditmark;
    }else{
        $query = "SELECT * FROM " . OW_DB_PREFIX. "map  
        WHERE id_owner='".addslashes($id_user)."' AND lat='".addslashes($_POST['latMap'])."' AND lon='".addslashes($_POST['lngMap'])."' LIMIT 1";
        $arrs = OW::getDbo()->queryForList($query);
        if (isset($arrs[0]) AND $arrs[0]['id']>0){
            $last_insertid=-100;
        }else{

$addxx_n="";
$addxx_v="";
if ($is_admin){
    $addxx_n=", `type_promo` ";
    $addxx_v=", '".addslashes($_POST['f_promotiontype'])."' ";
}

            $query = "INSERT INTO " . OW_DB_PREFIX. "map (
                `id`,`id_owner` ,`id_cat`,`active`,  `name`,    `desc`,    `lat`  ,   `lon` ,   
                `zoom`, `ico`,
`tags` 
".$addxx_n."
            )VALUES(
                '','".addslashes($id_user)."','".addslashes($_POST['f_category'])."','1','".addslashes($_POST['mtitle'])."','".addslashes($_POST['mpdescription'])."','".addslashes($_POST['latMap'])."','".addslashes($_POST['lngMap'])."',
                '".addslashes($_POST['zoomMap'])."','".addslashes($_POST['f_iconmarker'])."',
'".addslashes($_POST['f_mtags'])."' 
".$addxx_v."
            )";
            $last_insertid=OW::getDbo()->insert($query);
        }
    }
//echo $query;exit;


//---img start
    if ($last_insertid>0){

        if ($is_admin){
                $query = "SELECT * FROM " . OW_DB_PREFIX. "map  
                WHERE id='".addslashes($last_insertid)."' LIMIT 1";
                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $iduu=$arr[0]['id_owner'];
                }else{
                    $iduu=0;
                }
        }else{
            $iduu=$id_user;
        }
        $files=array();
        if ($iduu>0 AND isset($_FILES['imgs']) AND is_array($_FILES['imgs'])){
            $files=$_FILES['imgs'];
            $was_def=false;
            for ($i=0;$i<count($files);$i++){
                if (isset($files['error'][$i]) AND !$files['error'][$i]){
                    $path_f=MAP_BOL_Service::getInstance()->get_plugin_dir('map');
                    MAP_BOL_Service::getInstance()->dir_mkdir($path_f,$iduu);
                    $path_f .=$iduu.DS;
                    $new_image=uniqid('', true);
                    $filex=$last_insertid."_".$new_image.".jpg";
                    $filex_mini=$last_insertid."_".$new_image."_mini.jpg";
                    MAP_BOL_Service::getInstance()->file_copy($files['tmp_name'][$i],$path_f.$filex);

//                    MAP_BOL_Service::getInstance()->image_copy_resize($file_source="",$file_dest="",$crop=false,$width=800,$height=600);
                    MAP_BOL_Service::getInstance()->image_copy_resize($path_f.$filex,$path_f.$filex_mini,false,250,160);//resize mini
                    MAP_BOL_Service::getInstance()->image_copy_resize($path_f.$filex,$path_f.$filex,false,800,600);//resize big



                if ($iduu>0){
                    if ($was_def==true OR (isset($ideditmark) AND $ideditmark>0)){
                        $def="0";
                    }else{
                        $def="1";
                        $was_def=true;
                    }
                    $query = "INSERT INTO " . OW_DB_PREFIX. "map_images (
                        `idm`,`id_ownerm`,`id_map` ,`image`,`itype`,`is_default`
                    )VALUES(
                        '','".addslashes($iduu)."','".addslashes($last_insertid)."','".addslashes($new_image)."','jpg','".addslashes($def)."'
                    )";
                    $last_insertid_img=OW::getDbo()->insert($query);
                }
//echo $query;
                    
//                    ow_map_images
                }
            }
        }
    }
//---img end



//--default strat
//is_default
        if ($is_admin){
            $add="";
        }else{
            $add=" AND id_ownerm='".addslashes($id_user)."' ";
        }
        if ($iduu>0 AND $last_insertid>0){
            $query = "SELECT * FROM " . OW_DB_PREFIX. "map_images 
            WHERE  id_map='".addslashes($last_insertid)."' ".$add." AND is_default='1' LIMIT 1";
//echo $query."<hr>";
            $arr = OW::getDbo()->queryForList($query);
            if (!isset($arr[0])){
//                $query = "UPDATE " . OW_DB_PREFIX. "map_images SET is_default='1' WHERE id_ownerm='".addslashes($id_user)."' AND id_map='".addslashes($last_insertid)."' ORDER BY data_add DESC LIMIT 1";
                $query = "UPDATE " . OW_DB_PREFIX. "map_images SET is_default='1' WHERE id_map='".addslashes($last_insertid)."' ".$add." ORDER BY data_add DESC LIMIT 1";
//echo $query;exit;
                OW::getDbo()->query($query);
            }
        }
//            foreach ( $arr as $value ){
//            }
//--default end
//echo "aaa";exit;


    OW::getFeedback()->info(OW::getLanguage()->text('map', 'saved_succedfull'));
//    OW::getApplication()->redirect($curent_url."map?mapmode=ed&la=".$_POST['latMap']."&ln=".$_POST['lngMap']."&zo=".$_POST['zoomMap']."&check_cat%5B%5D=".$_POST['f_category']."&pico=".$_POST['f_iconmarker']);
    OW::getApplication()->redirect($curent_url."map?mapmode=ed&la=".$_POST['latMap']."&ln=".$_POST['lngMap']."&zo=".$_POST['zoomMap']."&check_cat%5B%5D=".$_POST['f_category']."&pico=".$_POST['f_iconmarker']."&cc=".$_POST['f_category']);
    exit;
}


//OW::getDocument()->addScript('http://maps.google.com/maps/api/js?v=3&sensor=false');
OW::getDocument()->addScript('http://maps.google.com/maps/api/js?v=3&sensor=true');




OW::getDocument()->addScript('http://jquery-ui-map.googlecode.com/svn/trunk/ui/jquery.ui.map.js');
OW::getDocument()->addScript('http://jquery-ui-map.googlecode.com/svn/trunk/ui/jquery.ui.map.services.js');
//OW::getDocument()->addScript('http://jquery-ui-map.googlecode.com/svn/trunk/ui/jquery.ui.map.extensions.js');



//OW::getDocument()->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js');
OW::getDocument()->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js');



OW::getDocument()->addStyleSheet('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/base/jquery-ui.css');
//        OW::getDocument()->addStyleSheet('http://jquery-ui-map.googlecode.com/svn-history/r64/trunk/demos/css/main.css');



//OW::getDocument()->addScript($pluginStaticURL2.'clustermarker.js');
//OW::getDocument()->addScript($pluginStaticURL2.'markerclusterer.js');
//OW::getDocument()->addScript($pluginStaticURL2.'Clusterer2.js');
//http://www.daftlogic.com/projects-google-maps-distance-calculator.htm
//OW::getDocument()->addScript('http://google-maps-utility-library-v3.googlecode.com/svn/tags/markermanager/1.0/src/markermanager.js');
//OW::getDocument()->addScript('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer_compiled.js');


//clus
OW::getDocument()->addScript('http://google-maps-utility-library-v3.googlecode.com/svn/tags/markermanager/1.0/src/markermanager.js');
OW::getDocument()->addScript('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer_compiled.js');
//OW::getDocument()->addScript($pluginStaticURL2.'markermanager.js');
//OW::getDocument()->addScript($pluginStaticURL2.'markerclusterer_compiled.js');

//OW::getDocument()->addScript('http://gmaps-samples-v3.googlecode.com/svn/trunk/toomanymarkers/markers.js');
//OW::getDocument()->addScript('http://gmaps-samples-v3.googlecode.com/svn/trunk/toomanymarkers/functions.js');

$content="";
$records="";
$script="";
//OW::getDocument()->addStyleSheet($pluginStaticURL2.'wall.css');
//OW::getDocument()->addScript($pluginStaticURL2.'js/demo.js');
$found=false;


if ($mapmode=="edit"){


        if ($is_admin){
            $add=" mm.id>'0' ";
        }else{
            $add=" mm.id_owner='".addslashes($id_user)."' ";
        }


        $query_add="";
        if ($querymap){
//            $query_add =" AND (mm.name LIKE '%".addslashes($querymap)."%' OR LOWER(mm.name) LIKE '%".addslashes($querymap)."%') ";
            $query_add =" AND (mm.name LIKE '%".addslashes($querymap)."%' OR LOWER(mm.name) LIKE '%".addslashes($querymap)."%' OR 
                            mm.tags LIKE '%".addslashes($querymap)."%' OR LOWER(mm.tags) LIKE '%".addslashes($querymap)."%' 
                        ) ";
        }



        $add_cat="";
//        if (isset($_GET['check_cat'])){
        if (!$querymap AND isset($_GET['check_cat'])){
//echo "aa";exit;
            $category_list=$_GET['check_cat'];
            for ($i=0;$i<count($category_list);$i++){
                if ($i>$max_check_category) break;
                if (!$category_list[$i]) $category_list[$i]=0;
                if ($add_cat!="") $add_cat .=",";
                $add_cat .=$category_list[$i];
            }
//echo $add_cat;exit;
            if ($add_cat=="" AND $add_cat!="0" ){
//echo "dupa";exit;
                $query = "SELECT * FROM " . OW_DB_PREFIX. "map_category WHERE active='1' ORDER BY RAND() LIMIT 1";
                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $value=$arr[0];
                    $add_cat=" AND mm.id_cat='".$value['id']."' ";
                    $_GET['check_cat'][]=$value['id'];
                }else{
                    $add_cat=" AND mm.id_cat='0' ";
                }
            }else{
                if ($add_cat!="0"){
                    $add_cat=" AND mm.id_cat IN (".$add_cat.") ";
                }else{
                    $add_cat=" AND mm.id_cat='0' ";
                }
            }
//        }else{
        }else if (!$querymap){
                $query = "SELECT * FROM " . OW_DB_PREFIX. "map_category WHERE active='1' ORDER BY RAND() LIMIT 1";
                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $value=$arr[0];
                    $add_cat=" AND mm.id_cat='".$value['id']."' ";
                    $_GET['check_cat'][]=$value['id'];
                }else{
                    $add_cat=" AND mm.id_cat='0' ";
                }
        }

//$perpageedit=100;
/*
        $query = "SELECT mm.* FROM " . OW_DB_PREFIX. "map mm 
            LEFT JOIN " . OW_DB_PREFIX. "map_images mmi ON (mmi.id_map=mm.id AND is_default='1') 
        WHERE ".$add." ".$query_add.$add_cat." GROUP BY mm.id LIMIT ".$perpage;

*/        


//        $query = "SELECT mm.* FROM " . OW_DB_PREFIX. "map mm 
//        WHERE ".$add." ".$query_add.$add_cat." LIMIT ".$perpage;

//$query = "SELECT mm.* FROM " . OW_DB_PREFIX. "map mm WHERE ".$add." ".$query_add.$add_cat;
        $query = "SELECT mm.id_cat,mm.ico,mm.lat,mm.lon,mm.id FROM ".OW_DB_PREFIX."map mm WHERE ".$add." ".$query_add." ".$add_cat." LIMIT ".$perpage;
//        $query = "SELECT * FROM ".OW_DB_PREFIX."map mm WHERE ".$add." ".$query_add." ".$add_cat." LIMIT ".$perpage;
//$query ="SELECT mm.id_cat,mm.ico,mm.lat,mm.lon,mm.id FROM ow_map mm WHERE mm.id>'0' AND mm.id_cat='0' LIMIT 5000";


//echo $query;exit;
        $arr = OW::getDbo()->queryForList($query);

        foreach ($arr as $value){

//echo $value['id_cat']."--".$ideditmark."--".$value['id']."<hr>";
//exit;
/*
            $name=stripslashes($value['name']);
            $name=str_replace("\r\n"," ",$name);
            $name=str_replace("\r","",$name);
            $name=str_replace("\n","",$name);
            $name=str_replace("\t","",$name);
            $name=str_replace("'","\"",$name);

            $lat=$value['lat'];
            $lon=$value['lon'];
            $desc=stripslashes($value['desc']);
            $desc=str_replace("\r\n"," ",$desc);
            $desc=str_replace("\r","",$desc);
            $desc=str_replace("\n","",$desc);
            $desc=str_replace("\t","",$desc);
            $desc=str_replace("'","\"",$desc);
            $button_ed ="";

$dimg="";
$full="";
//http://1.s3.envato.com/files/28915052/Garden.JPG
if (isset($value['image']) AND $value['image']){
    $img=$value['id_map']."_".$value['image']."_mini.".$value['itype'];
//    $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$id_user.DS;
    $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$value['id_owner'].DS;
//    $dimg="<div style=\"background:url(".$url.$img.") no-repeat;background-size: 100%;width:250px; height:160px;\" ></div>";
    $dimg="<div style=\"background:url(".$url.$img.") no-repeat center;background-size: auto;min-width:250px; max-width:300px;height:160px; border-bottom:1px solid #ddd; border-top:1px solid #ddd;\" ></div>";
//    $full="<div class=\"\" style=\"position: relative;background-color: #eee;\">[x]</div>";
}
*/
            $lat=$value['lat'];
            $lon=$value['lon'];
                if ($value['ico']) $ico=$value['ico'];
                    else $ico="normal";
            $records .="addMarker('".$value['id_cat']."','".$lat."', '".$lon."','".$ideditmark."','".$ico."','','".$value['id']."','map');\n";

/*
            if ($ideditmark>0 AND $value['id']==$ideditmark){

                $button_ed .="<div style=\"width:100%;margin:auto;border-bottom:1px solid #eee;text-align:right;\">";
//$button_ed .="<div class=\"ow_leftx\" style=\"float:left;position: relative;background-color: #eee;\"><a href=\"".$curent_url."map/zoom/".$value['id']."\">[".OW::getLanguage()->text('map', 'more')."]</a></div>";
                $button_ed .="<b>{<i style=\"color:#f00;\">".OW::getLanguage()->text('map', 'now_youareeditingthis')."</i>}</b>";
                $button_ed .="</div>";
$button_ed .="<div class=\"ow_right\" style=\"float:right;position: relative;background-color: #eee;\"><a href=\"".$curent_url."map/zoom/".$value['id']."\">[".OW::getLanguage()->text('map', 'more')."]</a></div>";
///                $records .="addMarker('".$lat."', '".$lon."','".$full."<b>".$name."</b>".$dimg."<br/>".$desc.$button_ed."','','normal','".$value['id']."');\n";
            }else{
                $button_ed .="<div style=\"width:100%;margin:auto;border-bottom:1px solid #eee;text-align:right;\">";
$button_ed .="<div class=\"ow_right\" style=\"float:right;position: relative;background-color: #eee;\"><a href=\"".$curent_url."map/zoom/".$value['id']."\">[".OW::getLanguage()->text('map', 'more')."]</a></div>";
                $button_ed .="<a href=\"".$curent_url."map/edit/".$value['id']."?mapmode=ed&la=".$lat."&ln=".$lon."&zo=17\" title=\"".OW::getLanguage()->text('map', 'edit_marker')."\"><img src=\"".$pluginStaticURL2."edit.gif\" /></a>";
//                $button_ed .="<a href=\"".$curent_url."map/del/".$value['id']."?mapmode=ed&delmark=true&ss=".substr(session_id(),3,5)."&la=".$lat."&ln=".$lon."&zo=12\" title=\"".OW::getLanguage()->text('map', 'delete_marker')."\"><img src=\"".$pluginStaticURL2."delete.gif\" /></a>";
//                $button_ed .="<a href=\"".$curent_url."map/del/".$value['id']."?mapmode=ed&delmark=true&ss=".substr(session_id(),3,5)."&la=".$lat."&ln=".$lon."&zo=12\" OnClick=\"return if (confirm(\"Confirm?\")==false) {return false; }else{return true;}\" title=\"".OW::getLanguage()->text('map', 'delete_marker')."\"><img src=\"".$pluginStaticURL2."delete.gif\" /></a>";

//                $button_ed .="<a href=\"javascript:void(0);\" onclick=\"if (!confirm(\"Confirm?\")) return false;\" title=\"".OW::getLanguage()->text('map', 'delete_marker')."\"><img src=\"".$pluginStaticURL2."delete.gif\" /></a>";
                $button_ed .="<a href=\"".$curent_url."map/del/".$value['id']."?mapmode=ed&delmark=true&ss=".substr(session_id(),3,5)."&la=".$lat."&ln=".$lon."&zo=12\" onclick=\" if (confirm(\'Confirm delete?\')) return true; else return false;\" title=\"".OW::getLanguage()->text('map', 'delete_marker')."\"><img src=\"".$pluginStaticURL2."delete.gif\" /></a>";
                $button_ed .="</div>";
//            $records .="addMarker('".$lat."', '".$lon."','".$button_ed."<b>".$name."</b><br/>".$desc."','umbrella-2','normal');\n";
                if ($value['ico']) $ico=$value['ico'];
                    else $ico="world";
///                $records .="addMarker('".$lat."', '".$lon."','".$full."<b>".$name."</b>".$dimg."<br/>".$desc.$button_ed."','".$ico."','normal','".$value['id']."');\n";
            }
*/
            if (!$found) $found=true;
        }

}else{

//<script src="http://maps.google.com/maps/api/js?v=3&sensor=false" type="text/javascript"></script>




if ($ctab=="all" OR !$ctab OR $ctab=="map"){


//        if ($ctab!="all"){
//        $perpage=5000;
//        }

        $query_add="";
        if ($querymap){
            $query_add=" AND (mm.name LIKE '%".addslashes($querymap)."%' OR LOWER(mm.name) LIKE '%".addslashes($querymap)."%') ";
        }

        if ($is_admin){
            $add=" 1 ";
        }else{
            $add=" active='1' ";
        }

//echo print_r($_GET['check_cat']);
        $add_cat="";
        if ( !$querymap AND isset($_GET['check_cat'])){
            $category_list=$_GET['check_cat'];
            for ($i=0;$i<count($category_list);$i++){
                if ($i>$max_check_category) break;
                if (!$category_list[$i]) $category_list[$i]=0;
                if ($add_cat) $add_cat .=",";
                $add_cat .=$category_list[$i];
            }
            if ($add_cat==""){
                $query = "SELECT * FROM " . OW_DB_PREFIX. "map_category WHERE active='1' AND id2>'0' ORDER BY RAND() LIMIT 1";
                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $value=$arr[0];
                    $add_cat=" AND mm.id_cat='".$value['id']."' ";
                    $_GET['check_cat'][]=$value['id'];
                }else{
                    $add_cat=" AND mm.id_cat='0' ";
                }
            }else{
                $add_cat=" AND mm.id_cat IN (".$add_cat.") ";
            }
//echo $add_cat;
        }else if (!$querymap){
                $query = "SELECT * FROM " . OW_DB_PREFIX. "map_category WHERE active='1' AND id2>'0' ORDER BY RAND() LIMIT 1";
                $arr = OW::getDbo()->queryForList($query);
                if (isset($arr[0])){
                    $value=$arr[0];
                    $add_cat=" AND mm.id_cat='".$value['id']."' ";
                    $_GET['check_cat'][]=$value['id'];
                }else{
                    $add_cat=" AND mm.id_cat='0' ";
                }
        }
//echo $querymap."--".print_r($_GET['check_cat'],1);
//echo $add_cat;

//-----start
        $query = "SELECT COUNT(*) as alli FROM " . OW_DB_PREFIX. "map mm WHERE ".$add." ".$query_add.$add_cat;//location
//echo $query;exit;
        $arr = OW::getDbo()->queryForList($query);
        if (isset($arr[0])){
            $value=$arr[0];
            $all=$value['alli'];
        }else{
            $all=0;
        }
//        $total_was=$total_was+$all;
//---end
//echo $query;exit;
//echo $perpage."---".$total_was."---".$all."<br>";



//        $query = "SELECT * FROM " . OW_DB_PREFIX. "map WHERE active='1' ".$query_add." LIMIT ".$perpage;

        $query = "SELECT * FROM " . OW_DB_PREFIX. "map mm 
            LEFT JOIN " . OW_DB_PREFIX. "map_images mmi ON (mmi.id_map=mm.id AND is_default='1') 
        WHERE ".$add." ".$query_add.$add_cat." GROUP BY mm.id LIMIT ".$perpage;
//echo $query;exit;








/*
        $query = "SELECT * FROM " . OW_DB_PREFIX. "map mm
            LEFT JOIN " . OW_DB_PREFIX. "map_images mmi ON
            (mmi.id_map=mm.id AND is_default='1')
        WHERE ".$add." ".$query_add." GROUP BY mm.id LIMIT 5000";
*/

//        WHERE mm.id_owner='".addslashes($id_user)."' ".$query_add." LIMIT ".$perpage;
//echo $query;exit;
        $arr = OW::getDbo()->queryForList($query);
        foreach ( $arr as $value ){
/*
            $name=stripslashes($value['name']);
            $name=str_replace("\r\n"," ",$name);
            $name=str_replace("\r","",$name);
            $name=str_replace("\n","",$name);
            $name=str_replace("\t","",$name);
            $name=str_replace("'","\"",$name);

            $lat=$value['lat'];
            $lon=$value['lon'];
            $desc=stripslashes($value['desc']);
            $desc=str_replace("\r\n"," ",$desc);
            $desc=str_replace("\r","",$desc);
            $desc=str_replace("\n","",$desc);
            $desc=str_replace("\t","",$desc);
            $desc=str_replace("'","\"",$desc);
*/
            $lat=$value['lat'];
            $lon=$value['lon'];
            if ($value['ico']) $ico=$value['ico'];
                else $ico="world";
/*
$dimg="";
$button_ed="";
//http://1.s3.envato.com/files/28915052/Garden.JPG
if (isset($value['image']) AND $value['image']){
    $img=$value['id_map']."_".$value['image']."_mini.".$value['itype'];
//    $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$id_user.DS;
    $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$value['id_owner'].DS;
//    $dimg="<div style=\"background:url(".$url.$img.") no-repeat;background-size: 100%;width:250px; height:160px;\" ></div>";
    $dimg="<div style=\"background:url(".$url.$img.") no-repeat center;background-size: auto;min-width:250px; max-width:300px; height:160px; border-bottom:1px solid #ddd; border-top:1px solid #ddd;\" ></div>";
//$button_ed .="<div class=\"ow_leftx\" style=\"float:left;position: relative;background-color: #eee;\"><a href=\"".$curent_url."map/zoom/".$value['id']."\">[".OW::getLanguage()->text('map', 'more')."]</a></div>";
$button_ed .="<div class=\"ow_right\" style=\"text-align:right;display: block;background-color: #eee;\"><a href=\"".$curent_url."map/zoom/".$value['id']."\">[".OW::getLanguage()->text('map', 'more')."]</a></div>";
}
*/
///            $records .="addMarker('".$lat."', '".$lon."','<b>".$name."</b>".$dimg."<br/>".$desc.$button_ed."','".$ico."','normal','".$value['id']."');\n";

            $records .="addMarker('".$value['id_cat']."','".$lat."', '".$lon."','0','".$ico."','','".$value['id']."','map');\n";
            if (!$found) $found=true;
    
            $total_was=$total_was+1;
            if ($perpage-$total_was<0){
                break;
                $perpagex=0;
            }


        }

//        $query = "SELECT * FROM " . OW_DB_PREFIX. "base_user_online ";//userId
}//if ($ctab=="all" OR !$ctab OR $ctab=="map"){




if (($ctab=="all" OR $ctab=="shop") AND OW::getPluginManager()->isPluginActive('shoppro') AND OW::getConfig()->getValue('map', 'tabdisable_shop')!="1") {




$sqlfrom="SELECT * FROM " . OW_DB_PREFIX. "shoppro_products WHERE active='1' AND (map_lan='' OR map_lat='') AND map_waschecking='0' LIMIT 80 ";
$sqlto="UPDATE " . OW_DB_PREFIX. "shoppro_products SET map_lan='[lan]', map_lat='[lat]', map_waschecking='1' WHERE [where] LIMIT 1";
$sqlwas="UPDATE " . OW_DB_PREFIX. "shoppro_products SET map_waschecking='1' WHERE [where] LIMIT 1";
MAP_BOL_Service::getInstance()->update_latitude_array($sqlfrom,$sqlto,$sqlwas,"shoppro",20);


//to_date
//        $query = "SELECT * FROM " . OW_DB_PREFIX. "shoppro_products WHERE active='1' AND location!='' AND (map_lan!='' OR map_lat!='') AND map_waschecking='1' ";//location
        $query_add="";
        if ($querymap){
            $query_add=" AND (name LIKE '%".addslashes($querymap)."%' OR LOWER(name) LIKE '%".addslashes($querymap)."%') ";
        }

//-----start
        $query = "SELECT COUNT(*) as alli FROM " . OW_DB_PREFIX. "shoppro_products WHERE active='1' AND map_lan!='' AND map_lat!='' ".$query_add;//location
        $arr = OW::getDbo()->queryForList($query);
        if (isset($arr[0])){
            $value=$arr[0];
            $all=$value['alli'];
        }else{
            $all=0;
        }
//        $total_was=$total_was+$all;
//---end



        $perpagex=$perpage-$total_was;
        if ($perpagex<1) $perpagex=0;

        $query = "SELECT * FROM " . OW_DB_PREFIX. "shoppro_products WHERE active='1' AND map_lan!='' AND map_lat!='' ".$query_add." LIMIT ".$perpagex;//location
        $arr = OW::getDbo()->queryForList($query);
        foreach ( $arr as $value ){


            if ($value['map_lan'] AND $value['map_lat']){
                $lat=$value['map_lat'];
                $lon=$value['map_lan'];
//                $records .="addMarker('".$lat."', '".$lon."','<b>".$name."</b><br/>".$desc."','pirates');\n";
//                $records .="addMarker('".$lat."', '".$lon."','0','scoutgroup','random','".$value['id']."','shop');\n";
                $records .="addMarker('".$value['cat_id']."','".$lat."', '".$lon."','0','supermarket','random','".$value['id']."','shop');\n";
//                $records .="addMarker('".$lat."', '".$lon."','".$ideditmark."','".$ico."','','".$value['id']."','map');\n";

            }
            if (!$found) $found=true;
            $total_was=$total_was+1;
            if ($perpage-$total_was<0){
                break;
                $perpagex=0;
            }
        }

}//if shop


if (($ctab=="all" OR $ctab=="fanpage") AND OW::getPluginManager()->isPluginActive('fanpage') AND OW::getConfig()->getValue('map', 'tabdisable_fanpage')!="1"){

//echo "ssssss--".$ctab;exit;

//update_latitude_array
//map_waschecking

$sqlfrom="SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND map_waschecking='0' AND a_city!='' AND (map_lan='' OR map_lat='') LIMIT 80 ";
$sqlto="UPDATE " . OW_DB_PREFIX. "fanpage_pages SET map_lan='[lan]', map_lat='[lat]', map_waschecking='1' WHERE [where] LIMIT 1";
$sqlwas="UPDATE " . OW_DB_PREFIX. "fanpage_pages SET map_waschecking='1' WHERE [where] LIMIT 1";
MAP_BOL_Service::getInstance()->update_latitude_array($sqlfrom,$sqlto,$sqlwas,"fanpage",20);


//echo "sss";exit;
//exit;

//        $query = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (a_city<>'' OR (map_lan!='' AND map_lat!='')) AND a_city IS NOT NULL ";//a_city
//        $query = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (map_lan!='' AND map_lat!='') AND a_city IS NOT NULL ";//a_city
//        $query = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (map_lan!='' AND map_lat!='') AND map_waschecking='1' ";//a_city
        $query_add="";
        if ($querymap){
            $query_add=" AND ((title_fan_page LIKE '%".addslashes($querymap)."%' OR LOWER(title_fan_page) LIKE '%".addslashes($querymap)."%') OR a_city LIKE '".addslashes($querymap)."' OR a_street LIKE '".addslashes($querymap)."' )";
        }

//-----start
        $query = "SELECT COUNT(*) as alli FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND map_lan!='' AND map_lat!='' ".$query_add;
        $arr = OW::getDbo()->queryForList($query);
        if (isset($arr[0])){
            $value=$arr[0];
            $all=$value['alli'];
        }else{
            $all=0;
        }
//        $total_was=$total_was+$all;
//---end

//echo $perpage."---".$total_was."---".$all."<br>";

        $perpagex=$perpage-$total_was;
        if ($perpagex<1) $perpagex=0;
//$perpagex=100;
        $query = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND map_lan!='' AND map_lat!='' ".$query_add." LIMIT ".$perpagex;//a_city
//echo $perpage."---".$total_was."<br>";
//echo $query;exit;
//$records .="addMarker('".$lat."', '".$lon."','".$query."','ne_barn-2','normal');\n";
        $arr = OW::getDbo()->queryForList($query);
//echo $query;
        foreach ( $arr as $value ){

            if ($value['map_lan'] AND $value['map_lat']){
                $lat=$value['map_lat'];
                $lon=$value['map_lan'];
//            $records .="addMarker('".$lat."', '".$lon."','".$ideditmark."','".$ico."','','".$value['id']."','map');\n";
//                if (!$value['a_street']) $records .="addMarker('".$lat."', '".$lon."','0','pirates','random','".$value['id']."','fanpage');\n";
//                    else $records .="addMarker('".$lat."', '".$lon."','0','ne_barn-2','normal','".$value['id']."','fanpage');\n";
//--sear cat s
                $icat=$value['id_category'];
                if (!$icat) $icat=0;
                if ($icat>0){
                    $queryv = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_categories WHERE active='1' AND id='".addslashes($icat)."' LIMIT 1 ";
                    $arrv = OW::getDbo()->queryForList($queryv);
                    foreach ( $arrv as $valuev ){
                        if ($valuev['id2']!=0){
                            $queryv2 = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_categories WHERE active='1' AND id='".addslashes($valuev['id2'])."' AND id2='0' LIMIT 1 ";
                            $arrv2 = OW::getDbo()->queryForList($queryv2);
                            foreach ( $arrv2 as $valuev2 ){
                                if ($valuev2['id']>0) {
                                    $icat=$valuev2['id'];
                                    break;
                                }
                            }
                        }else{
                            $icat=$valuev['id'];
                            break;
                        }
                    }
                }
//--sear cat e
                    $records .="addMarker('".$icat."','".$lat."', '".$lon."','0','ne_barn-2','normal','".$value['id']."','fanpage');\n";
            }//else lan and lat
            if (!$found) $found=true;

            $total_was=$total_was+1;
            if ($perpage-$total_was<0){
                break;
                $perpagex=0;
            }

        }
}//if fanpage


if (($ctab=="all" OR $ctab=="event") AND OW::getPluginManager()->isPluginActive('event') AND OW::getConfig()->getValue('map', 'tabdisable_event')!="1"){

//echo "ssssss--".$ctab;exit;

//update_latitude_array
//map_waschecking
/*
$sqlfrom="SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND map_waschecking='0' AND a_city!='' AND (map_lan='' OR map_lat='') LIMIT 80 ";
$sqlto="UPDATE " . OW_DB_PREFIX. "fanpage_pages SET map_lan='[lan]', map_lat='[lat]', map_waschecking='1' WHERE [where] LIMIT 1";
$sqlwas="UPDATE " . OW_DB_PREFIX. "fanpage_pages SET map_waschecking='1' WHERE [where] LIMIT 1";
MAP_BOL_Service::getInstance()->update_latitude_array($sqlfrom,$sqlto,$sqlwas,"fanpage",20);
*/


//echo "sss";exit;
//exit;

//        $query = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (a_city<>'' OR (map_lan!='' AND map_lat!='')) AND a_city IS NOT NULL ";//a_city
//        $query = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (map_lan!='' AND map_lat!='') AND a_city IS NOT NULL ";//a_city
//        $query = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (map_lan!='' AND map_lat!='') AND map_waschecking='1' ";//a_city
        $query_add="";
        if ($querymap){
            $query_add=" AND ((title LIKE '%".addslashes($querymap)."%' OR LOWER(title) LIKE '%".addslashes($querymap)."%') OR location LIKE '%".addslashes($querymap)."%' OR LOWER(location) LIKE '%".addslashes($querymap)."%' )";
        }

        $timeStamp=strtotime(date('Y-m-d H:i:s'));
        $query_add .=" AND (startTimeStamp>'".addslashes($timeStamp)."' AND endTimeStamp<'".addslashes($timeStamp)."') ";

//-----start
        $query = "SELECT COUNT(*) as alli FROM " . OW_DB_PREFIX. "event_item WHERE status='1' ".$query_add;
        $arr = OW::getDbo()->queryForList($query);
        if (isset($arr[0])){
            $value=$arr[0];
            $all=$value['alli'];
        }else{
            $all=0;
        }
//        $total_was=$total_was+$all;
//---end

//echo $perpage."---".$total_was."---".$all."<br>";

        $perpagex=$perpage-$total_was;
        if ($perpagex<1) $perpagex=0;
//$perpagex=100;
        $query = "SELECT * FROM " . OW_DB_PREFIX. "event_item WHERE status='1' ".$query_add." LIMIT ".$perpagex;//a_city
//echo $perpage."---".$total_was."<br>";
//echo $query;exit;
//$records .="addMarker('".$lat."', '".$lon."','".$query."','ne_barn-2','normal');\n";
        $arr = OW::getDbo()->queryForList($query);
//echo $query;
        foreach ( $arr as $value ){

            if ($value['map_lan'] AND $value['map_lat']){
                $lat=$value['map_lat'];
                $lon=$value['map_lan'];
//            $records .="addMarker('".$lat."', '".$lon."','".$ideditmark."','".$ico."','','".$value['id']."','map');\n";
//                if (!$value['a_street']) $records .="addMarker('".$lat."', '".$lon."','0','pirates','random','".$value['id']."','fanpage');\n";
//                    else $records .="addMarker('".$lat."', '".$lon."','0','ne_barn-2','normal','".$value['id']."','fanpage');\n";
                    $records .="addMarker('event','".$lat."', '".$lon."','0','ne_barn-2','normal','".$value['id']."','event');\n";
            }//else lan and lat
            if (!$found) $found=true;

            $total_was=$total_was+1;
            if ($perpage-$total_was<0){
                break;
                $perpagex=0;
            }

        }
}//if eevent

//echo $ctab;exit;

if (($ctab=="all" OR $ctab=="news") AND OW::getPluginManager()->isPluginActive('news') AND OW::getConfig()->getValue('map', 'tabdisable_news')!="1"){

//echo "ssssss--".$ctab;exit;

//update_latitude_array
//map_waschecking
/*
$sqlfrom="SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND map_waschecking='0' AND a_city!='' AND (map_lan='' OR map_lat='') LIMIT 80 ";
$sqlto="UPDATE " . OW_DB_PREFIX. "fanpage_pages SET map_lan='[lan]', map_lat='[lat]', map_waschecking='1' WHERE [where] LIMIT 1";
$sqlwas="UPDATE " . OW_DB_PREFIX. "fanpage_pages SET map_waschecking='1' WHERE [where] LIMIT 1";
MAP_BOL_Service::getInstance()->update_latitude_array($sqlfrom,$sqlto,$sqlwas,"fanpage",20);
*/


//echo "sss";exit;
//exit;

//        $query = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (a_city<>'' OR (map_lan!='' AND map_lat!='')) AND a_city IS NOT NULL ";//a_city
//        $query = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (map_lan!='' AND map_lat!='') AND a_city IS NOT NULL ";//a_city
//        $query = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_pages WHERE active='1' AND is_published='1' AND (map_lan!='' AND map_lat!='') AND map_waschecking='1' ";//a_city
        $query_add="";
        if ($querymap){
            $query_add=" AND ((topic_name LIKE '%".addslashes($querymap)."%' OR LOWER(topic_name) LIKE '%".addslashes($querymap)."%') ) ";
        }

        $timeStamp=strtotime(date('Y-m-d H:i:s'));
//        $query_add .=" AND (startTimeStamp>'".addslashes($timeStamp)."' AND endTimeStamp<'".addslashes($timeStamp)."') ";
            $query_add .=" AND mlon!='' AND mlat!='' ";

//-----start
        
        $query = "SELECT COUNT(*) as alli FROM " . OW_DB_PREFIX. "news WHERE active='1' AND is_published='1' ".$query_add;
        $arr = OW::getDbo()->queryForList($query);
        if (isset($arr[0])){
            $value=$arr[0];
            $all=$value['alli'];
        }else{
            $all=0;
        }
//        $total_was=$total_was+$all;
//---end

//echo $perpage."---".$total_was."---".$all."<br>";

        $perpagex=$perpage-$total_was;
        if ($perpagex<1) $perpagex=0;
//$perpagex=100;
        $query = "SELECT * FROM " . OW_DB_PREFIX. "news WHERE active='1' AND is_published='1' ".$query_add." ORDER BY data_added DESC LIMIT ".$perpagex;//a_city
//echo $perpage."---".$total_was."<br>";
//echo $query;exit;
//$records .="addMarker('".$lat."', '".$lon."','".$query."','ne_barn-2','normal');\n";
        $arr = OW::getDbo()->queryForList($query);
//echo $query;
        foreach ( $arr as $value ){

            if ($value['mlat'] AND $value['mlon']){
                $lat=$value['mlat'];
                $lon=$value['mlon'];
//            $records .="addMarker('".$lat."', '".$lon."','".$ideditmark."','".$ico."','','".$value['id']."','map');\n";
//                if (!$value['a_street']) $records .="addMarker('".$lat."', '".$lon."','0','pirates','random','".$value['id']."','fanpage');\n";
//                    else $records .="addMarker('".$lat."', '".$lon."','0','ne_barn-2','normal','".$value['id']."','fanpage');\n";
                    $records .="addMarker('".$value['id_topic']."','".$lat."', '".$lon."','0','newsagent','normal','".$value['id']."','news');\n";
            }//else lan and lat
            if (!$found) $found=true;

            $total_was=$total_was+1;
            if ($perpage-$total_was<0){
                break;
                $perpagex=0;
            }

        }
}//if news




//exit;
}//if edit end


$script .="
 var last_found;
 var icon = new google.maps.MarkerImage('http://maps.google.com/mapfiles/ms/micons/blue.png',
 new google.maps.Size(32, 32), new google.maps.Point(0, 0),
 new google.maps.Point(16, 32));
 var center = null;
 var map = null;
 var currentPopup;
 var bounds = new google.maps.LatLngBounds();

 var marker;
";
//-----------edit start
/*
if (isset($_GET['check_cat'])){
    $check_cat="?check_cat=".$_GET['check_cat'];
    $check_cata="&check_cat=".$_GET['check_cat'];
}else{
    $check_cat="";
    $check_cata="";
}
*/
if (isset($_GET['check_cat'])){
//    $check_cat="?check_cat=".$_GET['check_cat'];
//    $check_cata="&check_cat=".$_GET['check_cat'];
    $check_cat_ajax="";
    $fox=$_GET['check_cat'];
//check_cat%5B%5D=1806&
//check_cat%5B%5D=1807
//print_r($fox);exit;
    if (is_array($fox)){
        for ($i=0;$i<count($fox);$i++){
            if ($check_cat_ajax) $check_cat_ajax .="&";
            $check_cat_ajax .="check_cat%5B%5D=".$fox[$i];
        }
    }
    if ($check_cat_ajax) $check_cat_ajax="&".$check_cat_ajax;
}else{
    $check_cat_ajax="";
}
//$check_cat_ajax=$_GET['check_cat'];

    if ($mapmode=="edit"){
$script .="var mapmode='edit';";
        if (isset($_GET['la']) AND $_GET['la']!=""){
            $la=$_GET['la'];
        }else{
            $la="31.653381399664";
        }
        if (isset($_GET['ln']) AND $_GET['ln']!=""){
            $ln=$_GET['ln'];
        }else{
            $ln="-42.5390625";
        }
        if (isset($_GET['zo']) AND $_GET['zo']>0){
            $zo=$_GET['zo'];
        }else{
            $zo="2";
        }
//$records="";
    }else{
        $script .="var mapmode='map';";
        $la="31.653381399664";
        $ln="-42.5390625";
        $zo="2";
    }

if ($found==true) {
    $script .="var foundresults=true;";
}else{
    if ($querymap){
        OW::getFeedback()->info(OW::getLanguage()->text('map', 'search_fraze_nofound'));
    }


    $script .="var foundresults=false;";
}

//OW::getDocument()->addScript($pluginStaticURL2.'markerclusterer.js');
/*
//    var scriptx = '<script type=\"text/'+'javascript\" src=\"".$pluginStaticURL2."xmarkerclusterer'+'.js\"><' + '/script>';
    var scriptx = '<script type=\"text/'+'javascript\" src=\"".$pluginStaticURL2."markerclusterer'+'.js\"><' + '/script>';
//var scriptx ='ss';
//    document.write(scriptx);
*/
$script .="

    var gmarkers = [];
var mc = null;
var map = null;
var showMarketClusterer = false;

//var xmarkers = [];


    

    function addMarker(mcategory,lat, lng, info, iconx,seekp,idmarker,pname) {
        if (iconx!=undefined && iconx!='') {
            var iconok=new google.maps.MarkerImage('".$pluginStaticURL2."ico/'+iconx+'.png',
                new google.maps.Size(32, 32), new google.maps.Point(0, 0),
                new google.maps.Point(16, 32)
            );
        }else{
            var iconok=iconx;
        }

        if (seekp!=undefined && seekp=='random'){
            var min = .999999;
            var max = 1.000001;
            lat=lat*(Math.random() * (max - min) + min);
            lng=lng*(Math.random() * (max - min) + min);
        }


         var pt = new google.maps.LatLng(lat, lng);
         bounds.extend(pt);

         var marker = new google.maps.Marker({
             position: pt,
             icon: iconok,
             map: map,
//title:mcategory,
zIndex: Math.round(pt.lat()*-100000)<<5

        });

marker.mycategory = 'c_'+mcategory;
marker.myname = name;
gmarkers.push(marker);





















         var popup = new google.maps.InfoWindow({
             content: info,
             maxWidth: 300
         });
/*
         google.maps.event.addListener(marker, 'click', function() {
            if (currentPopup != null) {
                 currentPopup.close();
                 currentPopup = null;
            }
            popup.open(map, marker);
            currentPopup = popup;
         });
*/

        google.maps.event.addListener(marker, 'click', function() {

                var popup = new google.maps.InfoWindow({
                 content: '<div id=\"marker-info\"><img src=\"".$pluginStaticURL2."loading.gif\"> ".OW::getLanguage()->text('map', 'loading_pleasewait')."</div>',
                 maxWidth: 300
                });
                if (currentPopup != null) {
                 currentPopup.close();
                 currentPopup = null;
                }
                popup.open(map, marker);
                currentPopup = popup;

              load_content(marker, info,idmarker,pname);
        });

         google.maps.event.addListener(popup, 'closeclick', function() {
//                 map.panTo(center);
             currentPopup = null;
         });
    }





    function addres2lat(seachaddres,title,description,icon){
        geocoder_map = new google.maps.Geocoder();
        if (icon==undefined) var icon='blue';
        return geocoder_map.geocode( { 'address':seachaddres}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                addMarker('',results[0].geometry.location.lat(),results[0].geometry.location.lng(),'<b>'+title+'</b><br/>'+description,icon);
            }else {
                return;
//                alert('Geocode for '+seachaddres+' was not successful for the following reason: ' + status);
            }
        });
    }



    function placeMarker(location) {
      if ( marker ) {
        marker.setPosition(location);
      } else {
        marker = new google.maps.Marker({
          position: location,
          map: map
        });
      }
    }


    function load_content(marker, info,id,pname){
      $.ajax({
        url: '".$curent_url."map/get/'+id+'/'+info+'/'+pname+'?mm='+mapmode+'".$check_cat_ajax."',
        success: function(data){

            var popup = new google.maps.InfoWindow({
             content: data,
             maxWidth: 300
            });


            if (currentPopup != null) {
                 currentPopup.close();
                 currentPopup = null;
            }
            popup.open(map, marker);
            currentPopup = popup;


        }
      });
    }















    function initMap() {

//bounds = new google.maps.LatLngBounds ();
var trafficLayer = new google.maps.TrafficLayer();

        document.getElementById('map_canvas').innerHTML = '<div id=\"map\" width=\"1024\" height=\"550\"></div>'; 


        if (mapmode=='edit'){
             map = new google.maps.Map(document.getElementById('map'), {
                 center: new google.maps.LatLng(".$la.", ".$ln."),
                 zoom: ".$zo.",
panControl: true,
mapTypeControl: true,
                 mapTypeId: google.maps.MapTypeId.ROADMAP,
                 mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                },
                 navigationControl: true,
                 navigationControlOptions: {
//                    style: google.maps.NavigationControlStyle.SMALL
style: google.maps.NavigationControlStyle.ZOOM_PAN
                }
            });
//        }else if (foundresults==true){
        }else{
             map = new google.maps.Map(document.getElementById('map'), {
                 center: new google.maps.LatLng(0, 0),
                 zoom: 3,
panControl: true,
mapTypeControl: true,
                 mapTypeId: google.maps.MapTypeId.ROADMAP,
                 mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                },
                 navigationControl: true,
                 navigationControlOptions: {
//                    style: google.maps.NavigationControlStyle.DEFAULT
//                    style: google.maps.NavigationControlStyle.ANDROID
//                    style: google.maps.NavigationControlStyle.SMALL
style: google.maps.NavigationControlStyle.ZOOM_PAN
                }
            });
        }
/*
var noPoi = [
{
    featureType: 'poi',
    stylers: [
      { visibility: 'off' }
    ]   
  }
];

map.setOptions({styles: noPoi});
*/

        
//for(i=gmarkers.length-1; i>=0; i--){
//    map.addOverlay(gmarkers[i]);
//}



//    infoWindow = new google.maps.InfoWindow();
        var geocoder = new google.maps.Geocoder();  
        $('#searchbox').autocomplete({
           source: function(request, response) {

              if (geocoder == null){
                   geocoder = new google.maps.Geocoder();
              } 
             geocoder.geocode( {'address': request.term }, function(results, status) {
               if (status == google.maps.GeocoderStatus.OK) {

                  var searchLoc = results[0].geometry.location;
               var lat = results[0].geometry.location.lat();
                  var lng = results[0].geometry.location.lng();
                  var latlng = new google.maps.LatLng(lat, lng);
                  var bounds = results[0].geometry.bounds;

                  geocoder.geocode({'latLng': latlng}, function(results1, status1) {
                      if (status1 == google.maps.GeocoderStatus.OK) {
                        if (results1[1]) {
                         response($.map(results1, function(loc) {
                        return {
                            label  : loc.formatted_address,
                            value  : loc.formatted_address,
                            bounds   : loc.geometry.bounds
                          }
                        }));
                        }
                      }
                    });
            }
              });
           },
           select: function(event,ui){
              var pos = ui.item.position;
              var lct = ui.item.locType;
              var bounds = ui.item.bounds;
              if (bounds){
                    map.fitBounds(bounds);
              }
           }
        });

        if (mapmode=='edit'){
            google.maps.event.addListener(map, 'click', function(event) {
//alert('POS:'+event.latLng.lat()+' x '+event.latLng.lng()+' - '+map.getZoom());
//                document.getElementById('latMap').innerHTML = event.latLng.lat();
//                document.getElementById('lngMap').innerHTML = event.latLng.lng();
//                document.getElementById('zoomMap').innerHTML = map.getZoom();

                $('#latMap').val(event.latLng.lat());
                $('#lngMap').val(event.latLng.lng());
                $('#zoomMap').val(map.getZoom());
//http://.../map?mapmode=ed
//            if (mapmode=='edit'){
                placeMarker(event.latLng);
//            }
            }); 
        }

/*
google.maps.event.addListener(map,'zoom_changed',function () {
         if (map.getZoom() >= 4) {
            hideMarkers();          
         }
}
*/

    
        ".$records."        

        if (mapmode!='edit' && foundresults==true){
             center = bounds.getCenter();
             map.fitBounds(bounds);
        }









//var trafficLayer = new google.maps.TrafficLayer();
//trafficLayer.setMap(map);
var controlDiv = document.createElement('DIV');
$(controlDiv).addClass('gmap-control-container')
             .addClass('gmnoprint');
          
var controlUI = document.createElement('DIV');
$(controlUI).addClass('gmap-control');
$(controlUI).text('".OW::getLanguage()->text('map', 'traffic_button')."');
$(controlDiv).append(controlUI);
          
var legend = '<ul>'
           + '<li><span style=\"background-color: #30ac3e\">&nbsp;&nbsp;</span><span style=\"color: #30ac3e\"> &gt; 80 km ".OW::getLanguage()->text('map', 'traffic_perhour')."</span></li>'
           + '<li><span style=\"background-color: #ffcf00\">&nbsp;&nbsp;</span><span style=\"color: #ffcf00\"> 40 - 80 km ".OW::getLanguage()->text('map', 'traffic_perhour')."</span></li>'
           + '<li><span style=\"background-color: #ff0000\">&nbsp;&nbsp;</span><span style=\"color: #ff0000\"> &lt; 40 km ".OW::getLanguage()->text('map', 'traffic_perhour')."</span></li>'
           + '<li><span style=\"background-color: #c0c0c0\">&nbsp;&nbsp;</span><span style=\"color: #c0c0c0\"> ".OW::getLanguage()->text('map', 'traffic_notavaiable')."</span></li>'
           + '</ul>';
          
var controlLegend = document.createElement('DIV');
$(controlLegend).addClass('gmap-control-legend');
$(controlLegend).html(legend);
$(controlLegend).hide();
$(controlDiv).append(controlLegend);
$(controlUI).mouseenter(function() {
    $(controlLegend).show();
})
.mouseleave(function() {
    $(controlLegend).hide();
});
          
google.maps.event.addDomListener(controlUI, 'click', function() {
    if (typeof trafficLayer.getMap() == 'undefined' || trafficLayer.getMap() === null) {
        $(controlUI).addClass('gmap-control-active');
        trafficLayer.setMap(map);
    } else {
        trafficLayer.setMap(null);
        $(controlUI).removeClass('gmap-control-active');
    }

});
          
map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlDiv);








    gtoggleMarkerClusterer();//tutn on clusters













    }//end init mp




function gtoggleMarkerClusterer() {
  showMarketClusterer = !showMarketClusterer;
  if (showMarketClusterer) {
    if (mc) {
//      mc.addMarkers(gmarkers.locations);
      mc.addMarkers(gmarkers);
    } else {
      mc = new MarkerClusterer(map, gmarkers, {maxZoom: 13});
    }
  } else {
    mc.clearMarkers();
  }
}


      function show_category(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].setVisible(true);
          }
        }
//        document.getElementById('swc_'+category).checked = true;
      }

      function hide_category(category) {
        for (var i=0; i<gmarkers.length; i++) {
          if (gmarkers[i].mycategory == category) {
            gmarkers[i].setVisible(false);
          }
        }
//        document.getElementById('swc_'+category).checked = false;
      }
/*
    function boxclick(box,category) {
      if (box.checked) {
        show_category('c_'+category);
      } else {
        hide_category('c_'+category);
      }
    }
*/


$(function(){



    $('#map_thumb_ico').css('background-image', 'url(".$curent_url."ow_static/plugins/map/ico/'+$('#f_iconmarker').val()+'.png)');  
    $('#map_thumb_ico').css('background-repeat', 'no-repeat');  



    $('#f_iconmarker').change(function() {
        $('#map_thumb_ico').css('background-image', 'url(".$curent_url."ow_static/plugins/map/ico/'+$(this).val()+'.png)');  
        $('#map_thumb_ico').css('background-repeat', 'no-repeat');  
    });
/*
    $('#hide_allcategory').click(function() {
        hide_category('aa');
    });

    $('#show_allcategory').click(function() {
        show_category('aa');
    });
*/

    $('.showhide_category').click(function() {
            if ($(this).attr('checked')=='checked'){
                show_category('c_'+$(this).attr('swc'));
            }else{
                hide_category('c_'+$(this).attr('swc'));
            }
    });

        initMap();

    $('#check_category :checkbox').click(function() {

if ($('#check_category #t_'+$(this).attr('swc')).css('font-weight')=='bold'){
    $('#check_category #t_'+$(this).attr('swc')).css('font-weight', 'normal');
}else{
    $('#check_category #t_'+$(this).attr('swc')).css('font-weight', 'bold');
}

        if($('#check_category :checkbox:checked').length >= ".$max_check_category.") {
//$('#check_category #t_'+$(this).attr('swc')).css('font-weight', 'normal');
            $('#check_category :checkbox:not(:checked)').attr('disabled', 'disabled');
        } else {

//$(this).css('color', '#c2c0c0');
//$('#check_category #t_'+$(this).attr('swc')).css('color', '#c2c0c0');
//$('#check_category #t_'+$(this).attr('swc')).css('font-weight', 'bold');
//            $('#check_category :checkbox').attr('disabled', '');
            $('#check_category :checkbox').removeAttr('disabled');
        }
    });
/*
    $('#form_cat_sel').submit(function() { 
                $.ajax({
                        type: 'POST',
                        url: '".$curent_url."map', 
                        data: $('#formId').serialize(), 
                        success: function(data, textStatus, jqXHR) {
alert('ok');
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
alert('error');
                        }
                });
                return false;
    });
*/






var gmaps_full = 0;
var ffs='<a href=\"#\" title=\"".str_replace("'","",str_replace("\"","",OW::getLanguage()->text('map', 'togglefullscreenview')))."\" id=\"gmaps_fullscreen\">'
    + '<img src=\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH1gMQEw014nffmgAAADV0RVh0Q29tbWVudAAoYykgMjAwNCBKYWt1YiBTdGVpbmVyCgpDcmVhdGVkIHdpdGggVGhlIEdJTVCQ2YtvAAAB1klEQVQ4y52TO2iTURTHf/+bGzUVadMlSPEFcQgIgiCUKnYqFF9QxcnH4NDBKtXFQXBwSPma4KhgBsFBUALt4LNWUXRQcQ0tiiUtFkVqTVMVTUz7dfiS2LwccuDAhXPP7/7PuefIiUVvAP00ZwmcWNRt1pxY1LUl1O7+2xXoJ8O9AEgGcOm5+Kgi/i5xHABTrckY/UsGJJDE0/h+jFFNDaY6eUPAz4Xrb7yXZVDRTzkvaFlrsT7VB0gw5vSysT1AuKOVe69nkDwZd55/ZMfWdkLBAOPxg/hWKSn3YDx2AIBrg3sZfTVNKBjg/adFrDWEO9pYY31cOrELF/Hs6qFagORVLaBv3zYmpjNEtrRhZPgwu0BnJFy+566qvAywtnT0IH6/xfr8yIj1LevwWQsSwgVX9XpgkISRIfFggrmFP0x9zjI795PsrzwjL6eKv2JKcisB3edHkQzHrjxmciZDKv2d7ZuCbA61kkrP83byK0cuPwRgz9lkLcB1oXtwhMyPHOkvi5w7uhNJSOJkT4RUep5v2d90DSQbz8GyC7m/S9x3DnudKAIkMRbvY2nZ/f8glaxrIIkxwshzZOg8c7fuNtnq2W5kjeK2UCjcHI4PnW5ml/O5/K0VjtO5n1HbI70AAAAASUVORK5CYII=\" style=\"z-index: 2; border: none; position: absolute; top: 5px; left: 5px;\">'
    + '<span style=\"z-index: 3; border: none; position: absolute; top: 2px;left: 20px;font-weight: bold;\">&nbsp;".str_replace("'","",str_replace("\"","",OW::getLanguage()->text('map', 'fillscreen')))."</span>'
    + '</a>';
$('#map').append(ffs);
$('#map #gmaps_fullscreen').click(function() {
  if (!jQuery('#map').data('fullscreen')) {
    jQuery('#map').data('fullscreen', jQuery('#map').attr('style'));
  }
  if (gmaps_full == 1) {
    jQuery('#map').attr('style', jQuery('#map').data('fullscreen'));
    jQuery('object').show();
    gmaps_full = 0;
  } else {
    jQuery('object').hide();
    gmaps_full = 1;
    jQuery('#map').data('fullscreen', jQuery('#map').attr('style'));
    jQuery('#map').css('position', 'fixed').css('z-index', parseInt(10100, 10));
    jQuery('#map').css('width', '100%').css('height', '100%');
    jQuery('#map').css('top', '0').css('left', '0');
  }
//  google.maps.event.trigger(eval('map_' + map_id), 'resize');
  google.maps.event.trigger(eval('map'), 'resize');
  return false;
    });







$('#filter').keyup(function(){

        // Retrieve the input field text and reset the count to zero
        var filter = $(this).val(), count = 0;
 
        // Loop through the comment list
        $('#check_category .fil_check').each(function(){
            // If the list item does not contain the text phrase fade it out
            if ($(this).text().search(new RegExp(filter, 'i')) < 0) {
                $(this).fadeOut();
 
            // Show the list item if the phrase matches and increase the count by 1
            } else {
                $(this).show();
                count++;
            }
        });
 
        // Update the count
        var numberItems = count;
        if (filter!=undefined && filter!=null && filter!=''){
            $('#filter-count').text('".OW::getLanguage()->text('map', 'numberoffound')." = '+count);
        }else{
            $('#filter-count').text('');
        }
});













});


//    $(window).load(function(){
//        initMap();
//        hide_category('aa');
//    });

//google.maps.event.addDomListener(window, 'load', initMap);    





















";



/*
 <?
 $query = mysql_query("SELECT * FROM poi_example");
 while ($row = mysql_fetch_array($query)){
 $name=$row['name'];
 $lat=$row['lat'];
 $lon=$row['lon'];
 $desc=$row['desc'];
 echo ("addMarker('cat',$lat, $lon,'<b>$name</b><br/>$desc');\n");
 }
 ?>
*/

OW::getDocument()->addOnloadScript($script);
//$content .="<script type=\"text/javascript\">$(document).ready(function () {".$script."})</script>";


$content_css ="
#map {
width: 100%;
height: 550px;
border: 0px;
padding: 0px;
}


.gmap-control-container {
    margin: 5px;
}
.gmap-control {
    cursor: pointer;
    background-color: -moz-linear-gradient(center top , #FEFEFE, #F3F3F3);
    background-color: #FEFEFE;
    border: 1px solid #A9BBDF;
    border-radius: 2px;
    padding: 0 6px;
    line-height: 160%;
    font-size: 12px;
    font-family: Arial,sans-serif;
    box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.35);
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -o-user-select: none;
    user-select: none;
}
.gmap-control:hover {
    border: 1px solid #678AC7;
}
.gmap-control-active {
    background-color: -moz-linear-gradient(center top , #6D8ACC, #7B98D9);
    background-color: #6D8ACC;
    color: #fff;
    font-weight: bold;
    border: 1px solid #678AC7;
}
.gmap-control-legend {
    position: absolute;
    text-align: left;
    z-index: -1;
    top: 20px;
    right: 0;
    width: 150px;
    height: 66px;
    font-size: 10px;
    background: #FEFEFE;
    border: 1px solid #A9BBDF;
    padding: 10px;
    box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.35);
}
.gmap-control-legend ul {
    margin: 0;
    padding: 0;
    list-style-type: none;
}
.gmap-control-legend li {
    line-height: 160%;
}















.popup_deal_box {
width: 290px;
height: 110px;
color: #5b5b5b;
position: relative;
overflow: hidden;
margin: 5px;
}
div.olMapViewport {
text-align: left;
}
Xdiv.olMap {
cursor: default;
}
.shadow_main_item, .map_shadow_box {
zoom: 1;
}
.popup_deal_navigation {
display: none;
border-bottom: 1px solid #D5D5D5;
margin-bottom: 2px;
padding-bottom: 2px;
}
.popup_deal_headline {
font-size: 13px;
border-bottom: 1px solid #D5D5D5;
padding-bottom: 1px;
margin-bottom: 5px;
height: 16px;
overflow: hidden;
}
.popup_deal_content_right {
width: 82px;
float: right;
}
.popup_deal_content {
width: 202px;
height: 80px;
overflow: hidden;
}
.popup_deal_box p {
margin: 0;
padding: 0;
}
.popup_deal_box p {
margin: 0;
padding: 0;
}
.popup_deal_address {
font-size: 11px;
display: none;
}
.popup_deal_shrink, .popup_deal_enlarge {
font-size: 10px;
text-decoration: underline;
cursor: pointer;
position: absolute;
bottom: 0;
left: 0;
}
.popup_deal_shrink {
display: none;
}
.popup_deal_box p {
margin: 0;
padding: 0;
}

popup_deal_street p, popup_deal_city_zip p {
display: block;
-webkit-margin-before: 1em;
-webkit-margin-after: 1em;
-webkit-margin-start: 0px;
-webkit-margin-end: 0px;
}
.popup_deal_navigation span {
font-weight: bold;
}

.popup_deal_image {
border: 1px solid #EBEBEB;
}

.popup_deal_buy_button {
background: red;
width: 72px;
height: 19px;
line-height: 19px;
text-align: center;
color: #fff;
margin: 5px auto 0 auto;
background: rgb(170, 0, 0) url('".$pluginStaticURL2."button_popup.png') 0 0 no-repeat;
cursor: pointer;
}
.popup_deal_merchant {
font-weight: bold;
display: inline-block;
margin-bottom: 2px !important;
}
.popup_deal_title {
font-size: 11px;
}
.popup_deal_headline {
font-size: 13px;
border-bottom: 1px solid #D5D5D5;
padding-bottom: 1px;
margin-bottom: 5px;
height: 16px;
overflow: hidden;
}
.popup_deal_headline {
font-size: 13px;
}
.popup_deal_disount_text {
float: right;
margin-right: 5px;
}
.popup_deal_disount_percent {
float: right;
}
.popup_deal_category {
font-weight: bold;
}
.popup_deal_price {
font-weight: bold;
}
.popup_deal_disount_percent {
font-weight: bold;
}




</style>";
//OW::getDocument()->addStyleSheet($content_css);//from url
//OW::getDocument()->addScript($content_css); 

$content .="<style>".$content_css."</style>";


/*
$content .="<div id=\"hide_allcategory\">Hide</div>";
$content .="<div id=\"show_allcategory\">Show</div>";
*/

if (isset($_GET['check_cat'])){
    $check_cat_ajax="";
    $check_cat_ajaxx="";
    $check_cat_ajaxa="";
    $fox=$_GET['check_cat'];
    if (is_array($fox)){
        for ($i=0;$i<count($fox);$i++){
            if ($check_cat_ajax) $check_cat_ajax .="&";
            $check_cat_ajax .="check_cat%5B%5D=".$fox[$i];
            $check_cat_ajaxa .="<input type=\"hidden\" name=\"check_cat[]\" value=\"".$fox[$i]."\">";
        }
    }
    if ($check_cat_ajax) {
        $check_cat_ajaxx="?".$check_cat_ajax;
        $check_cat_ajax="&".$check_cat_ajax;
    }
}else{
    $check_cat_ajax="";
    $check_cat_ajaxx="";
    $check_cat_ajaxa="";
}


$content .="<form method=\"get\" action=\"".$curent_url."map/search".$check_cat_ajaxx."\">";
if ($mapmode=="edit"){
    $content .="<input type=\"hidden\" name=\"mapmode\" value=\"ed\">";
}
$content .= $check_cat_ajaxa ;
//    $content .="<input type=\"hidden\" name=\"check_cat\" value=\"".$_GET['check_cat']."\">";
if ($ctab){
    $content .="<input type=\"hidden\" name=\"ctab\" value=\"".$ctab."\">";
}
$content .="<div class=\"clearfix\" style=\"padding:10px;\">";
//    $content .="<div class=\"ow_left\">".OW::getLanguage()->text('map', 'search_search').":</div> ";
    $content .="<input class=\"ow_left\" type=\"text\" placeholder=\"".OW::getLanguage()->text('map', 'search_onthemap')."\" name=\"searchmap\" value=\"".$querymap."\" id=\"searchmap\" style=\"width:300px;min-width:320px;height:30px; font-size:15px;\">";
    
    $content .="&nbsp;";

//            $content .="<div class=\"clearfix ow_submit ow_smallmargin\">
//                <div class=\"ow_center\">
                    $content .="<span class=\"ow_button\">
                        <span class=\" ow_positive\">
                            <input type=\"submit\" name=\"dosearch\" value=\"".OW::getLanguage()->text('map', 'search')."\" class=\"ow_ic_lens ow_positive\">
                        </span>
                    </span>";
//                </div>
//            </div>";

    $content .="<input class=\"ow_right\" type=\"text\" placeholder=\"".OW::getLanguage()->text('map', 'search_addres_bygoogle')."\" value=\"\" id=\"searchbox\" style=\"width:250px;min-width:300px;height:30px; font-size:15px;\">";
$content .="</div>";
$content .="</form>";



//$content .="<input type=\"button\" id=\"enter-full-screen\"  value=\"Searchfff\"/>";
//$content .="<input type=\"button\" id=\"exit-full-screen\"  value=\"Searcheee\"/>";



//$content .="<input type=\"text\" name=\"addressInput\" id=\"addressInput\" value=\"Gdańsk, Królewskei Wzgórze\">";
$content .="<div id=\"map_canvas\" style=\"width: 100%;min-height: 550px;\">";
$content .="<div id=\"map\" style=\"width:100%;min-height: 550px;\"></div>";
$content .="</div>";



if ($ctab=="all" OR !$ctab OR $ctab=="map"){
//print_r($_GET);exit;
//echo $ctab;exit;
/*
Array
(
    [check_cat] => Array
        (
            [0] => 0
            [1] => 1423
            [2] => 20
        )

    [saveb] => map+set_category
    [mobi] => 
)
*/


        if ($ctab!="all"){

            if (isset($_GET['searchmap']) AND $_GET['searchmap']){
                $checked_arr=array();
            }else if (isset($_GET['check_cat'])){
                $checked_arr=$_GET['check_cat'];
            }else{
                $checked_arr=array();
            }
/*

            $add_par_url="";
            if (isset($_GET['mapmode'])) $add_par_url="?mapmode=".$_GET['mapmode'];
            $content .="<form id=\"form_cat_sel\" action=\"".$curent_url."map".$add_par_url."\" method=\"GET\">";
            if (isset($_GET['mapmode'])) {
                $content .="<input type=\"hidden\" name=\"mapmode\" value=\"".$_GET['mapmode']."\">";
            }

*/
        }

//print_r($valuex);exit;

//-------------------if not edit mode ss
//echo $ideditmark;
//dosavemarker
//if (!$valuex['id']){
if (!$ideditmark){

//print_r($checked_arr);
//echo "----".in_array(20,$checked_arr);exit;

            $add_par_url="";
            if (isset($_GET['mapmode'])) $add_par_url="?mapmode=".$_GET['mapmode'];
            $content .="<form id=\"form_cat_sel\" action=\"".$curent_url."map".$add_par_url."\" method=\"GET\">";
            if (isset($_GET['mapmode'])) {
                $content .="<input type=\"hidden\" name=\"mapmode\" value=\"".$_GET['mapmode']."\">";
            }


$content .="<div class=\"clearfix\" id=\"check_category\" style=\"border-bottom:1px solid #bbb;\">";



        $content .="<h1 class=\"ow_title clearfix\" style=\"font-size:14px;\">".OW::getLanguage()->text('map', 'categories').":</h1>";

$content .="<div class=\"clearfix\" style=\"width:100%;margin-bottom:10px;\">";
$content .="<b>".OW::getLanguage()->text('map', 'filtercategory').":</b> ";
$content .="<input type=\"text\" class=\"text-input\" id=\"filter\" value=\"\" style=\"min-width:300px;width:300px;\"/>";
$content .="&nbsp;";
$content .="<span id=\"filter-count\"></span>";
$content .="</div>";


        $content .=MAP_BOL_Service::getInstance()->get_full_category($checked_arr,$max_check_category);


        if ($ctab!="all"){            
            if (!isset($_GET['searchmap']) OR !$_GET['searchmap']){
                $content .="<div class=\"clearfix ow_submit ow_smallmargin\" >
                    <div class=\"ow_center\">
                        <span class=\"ow_button ow_right\" style=\"margin-top:10px;\">
                            <span class=\"ow_positive\">
                                <input type=\"submit\" name=\"saveb\" value=\"".OW::getLanguage()->text('map', 'seach_by_category')."\" class=\"ow_ic_lens ow_positive\">
                            </span>
                        </span>
                    </div>
                </div>";
            }
            $content .="</form>";


        }
$content .="</div>";

        if (isset($_GET['searchmap']) AND $_GET['searchmap']){
                $content .="<div class=\"clearfix ow_submit ow_smallmargin\" >
                    <div class=\"ow_center\">
                        <span class=\"ow_right\" style=\"margin-top:10px;mrgin-bottom:20px;\">";
                $content .="<h3>".OW::getLanguage()->text('map', 'now_you_are_seach_by_keyword').": <b>".$_GET['searchmap']."</b></h3>";
if (isset($_GET['mapmode'])){
    $content .="<a class=\"ow_right\" href=\"".$curent_url."map?mapmode=ed".$check_cat_ajax."#ef\">";
}else{
    $content .="<a class=\"ow_right\" href=\"".$curent_url."map".$check_cat_ajaxx."\">";
}
$content .="[".OW::getLanguage()->text('map', 'cancel')."]";
$content .="</a>";
                        $content .="</span>
                    </div>
                </div>";
        }

}


if (($ctab=="all" OR $ctab=="shop") AND OW::getPluginManager()->isPluginActive('shoppro') AND OW::getConfig()->getValue('map', 'tabdisable_shop')!="1") {
$content .="<div class=\"clearfix\" style=\"border-bottom:1px solid #bbb;\">";
        $content .="<h1 class=\"ow_title clearfix\" style=\"font-size:14px;\">".OW::getLanguage()->text('map', 'categories').":</h1>";
$sel=" CHECKED ";
        $content .="<input type=\"checkbox\" ".$sel." class=\"showhide_category\" swc=\"0\" value=\"0\"><b>".OW::getLanguage()->text('map', 'woutcategories')."</b>; ";

        $queryc = "SELECT * FROM " . OW_DB_PREFIX. "shoppro_categories WHERE active='1' ORDER BY name ";
        $arrc = OW::getDbo()->queryForList($queryc);
        foreach ( $arrc as $valuec ){
            
//            if ($valuex['id_cat']==$valuec['id']) $sel=" checked ";
//                else $sel="";
//            $content .="<input type=\"checkbox\" ".$sel." onclick=\"boxclick(this,'".$valuec['id']."');\" id=\"swc_".$valuec['id']."\" value=\"".$valuec['id']."\">".stripslashes($valuec['name'])."; ";
            $content .="<input type=\"checkbox\" ".$sel." class=\"showhide_category\" swc=\"".$valuec['id']."\" value=\"".$valuec['id']."\"><b>".stripslashes($valuec['name'])."</b>; ";
        }
$content .="</div>";
}


if (($ctab=="all" OR $ctab=="fanpage") AND OW::getPluginManager()->isPluginActive('fanpage') AND OW::getConfig()->getValue('map', 'tabdisable_fanpage')!="1"){
$content .="<div class=\"clearfix\" style=\"border-bottom:1px solid #bbb;\">";
        $content .="<h1 class=\"ow_title clearfix\" style=\"font-size:14px;\">".OW::getLanguage()->text('map', 'categories').":</h1>";
$sel=" CHECKED ";
        $content .="<input type=\"checkbox\" ".$sel." class=\"showhide_category\" swc=\"0\" value=\"0\"><b>".OW::getLanguage()->text('map', 'woutcategories')."</b>; ";

        $queryc = "SELECT * FROM " . OW_DB_PREFIX. "fanpage_categories WHERE active='1' AND id2='0' ORDER BY name ";
        $arrc = OW::getDbo()->queryForList($queryc);
        foreach ( $arrc as $valuec ){
            
//            if ($valuex['id_cat']==$valuec['id']) $sel=" checked ";
//                else $sel="";
//            $content .="<input type=\"checkbox\" ".$sel." onclick=\"boxclick(this,'".$valuec['id']."');\" id=\"swc_".$valuec['id']."\" value=\"".$valuec['id']."\">".stripslashes($valuec['name'])."; ";
            $content .="<input type=\"checkbox\" ".$sel." class=\"showhide_category\" swc=\"".$valuec['id']."\" value=\"".$valuec['id']."\"><b>".stripslashes($valuec['name'])."</b>; ";
        }
$content .="</div>";
}

if (($ctab=="all" OR $ctab=="news") AND OW::getPluginManager()->isPluginActive('news') AND OW::getConfig()->getValue('map', 'tabdisable_news')!="1"){
$content .="<div class=\"clearfix\" style=\"border-bottom:1px solid #bbb;\">";
        $content .="<h1 class=\"ow_title clearfix\" style=\"font-size:14px;\">".OW::getLanguage()->text('map', 'categories').":</h1>";
$sel=" CHECKED ";
        $content .="<input type=\"checkbox\" ".$sel." class=\"showhide_category\" swc=\"0\" value=\"0\"><b>".OW::getLanguage()->text('map', 'woutcategories')."</b>; ";

        $queryc = "SELECT * FROM " . OW_DB_PREFIX. "news_topics WHERE active='1' ORDER BY t_name ";
        $arrc = OW::getDbo()->queryForList($queryc);
        foreach ( $arrc as $valuec ){
            
//            if ($valuex['id_cat']==$valuec['id']) $sel=" checked ";
//                else $sel="";
//            $content .="<input type=\"checkbox\" ".$sel." onclick=\"boxclick(this,'".$valuec['id']."');\" id=\"swc_".$valuec['id']."\" value=\"".$valuec['id']."\">".stripslashes($valuec['name'])."; ";
            $content .="<input type=\"checkbox\" ".$sel." class=\"showhide_category ow_bg_color\" swc=\"".$valuec['idt']."\" value=\"".$valuec['idt']."\"><b>".stripslashes($valuec['t_name'])."</b>; ";
        }
$content .="</div>";
}

if (($ctab=="all" OR $ctab=="event") AND OW::getPluginManager()->isPluginActive('event') AND OW::getConfig()->getValue('map', 'tabdisable_event')!="1"){
}//end categories



    $content .="</form>";
}//if dosavemarker
//-------------------if not edit mode ss


$content .="<div class=\"clearfix\" style=\"margin:10px;\"><a name=\"ef\"></a></div>";






/*
$content .="<div>
    <input type=\"text\" id=\"addressInput\" size=\"50\"  value=\"Gdańsk, Królewskei Wzgórze\" />
    <input type=\"hidden\" id=\"radiusSelect\" value=\"5\"/>
    <input type=\"button\" id=\"addressInput\"  value=\"Search\"/>
</div>";
$content .="<select id=\"locationSelect\" style=\"width:100%;visibility:hidden\"></select>";
*/

//$pluginStaticDir =OW::getPluginManager()->getPlugin('fanpage')->getStaticUrl();
//is_file(OW_DIR_STATIC_PLUGIN.'fanpage'.DS.'headers'.DS . $valuex['gr_page_fromtemplate']))

if ($mapmode=="edit"){

$content .="<h1 class=\"ow_title clearfix\" style=\"font-size:14px;margin-bottom:20px;\">".OW::getLanguage()->text('map', 'add_edit_poi').":</h1>";

    $content .="<form method=\"post\" action=\"".$curent_url."map/edit/".$ideditmark."?mapmode=ed\" enctype=\"multipart/form-data\">";

    $valuex=array();
            $valuex['id']=0;
            $valuex['id_owner']=0;
            $valuex['id_cat']=0;
            $valuex['lat']="31.653381399664";
            $valuex['lon']="-42.5390625";
            $valuex['zoom']="2";
            $valuex['name']="";
            $valuex['desc']="";
            $valuex['ico']="world";

    if ($ideditmark>0){

        if ($is_admin){
            $add=" ";
        }else{
            $add=" AND id_owner='".addslashes($id_user)."' ";
        }

        $query = "SELECT * FROM " . OW_DB_PREFIX. "map WHERE id='".addslashes($ideditmark)."' ".$add." LIMIT 1";
        $arr = OW::getDbo()->queryForList($query);
        if (isset($arr[0])){
            $valuex=$arr[0];
        }
    }


    $content .="<div id=\"map_info\" class=\"ow_form clearfix\">";

        $content .="<div class=\"ow_value clearfix\" style=\"margin-top:10px;\">";
            $content .="<b>".OW::getLanguage()->text('map', 'markertitle').":</b> <input type=\"text\" name=\"mtitle\" id=\"mtitle\" value=\"".stripslashes($valuex['name'])."\" style=\"display:inline-block;max-width:300px;\" placeholder=\"".OW::getLanguage()->text('map', 'enter_marker_title')."\" /> ";
        $content .="</div>";


        $content .="<div class=\"ow_value clearfix\" style=\"margin-top:10px;\">";
            $content .="<b>".OW::getLanguage()->text('map', 'lat').":</b> <input type=\"text\" name=\"latMap\" id=\"latMap\" value=\"".$valuex['lat']."\" style=\"display:inline-block;max-width:180px;\"  placeholder=\"".OW::getLanguage()->text('map', 'enter_latitude')."\" /> ";
            $content .="<b>".OW::getLanguage()->text('map', 'lan').":</b> <input type=\"text\" name=\"lngMap\" id=\"lngMap\" value=\"".$valuex['lon']."\" style=\"display:inline-block;max-width:180px;\"  placeholder=\"".OW::getLanguage()->text('map', 'enter_longitude')."\"/> ";
            $content .="<b>".OW::getLanguage()->text('map', 'zoom').":</b> <input type=\"text\" name=\"zoomMap\" id=\"zoomMap\" value=\"".$valuex['zoom']."\" style=\"display:inline-block;max-width:50px;\"  placeholder=\"".OW::getLanguage()->text('map', 'map_zoom')."\"/> ";
        $content .="</div>";

        $content .="<div class=\"ow_value clearfix\" style=\"margin-top:10px;\">";
            $content .="<div id=\"map_info_content\" class=\"ow_value clearfix\">";
            $content .="<b>".OW::getLanguage()->text('map', 'markercategory').":</b> ";

//            $content .="<select name=\"f_category\" id=\"f_category\" style=\"max-width:150px;\">";        
            $content .="<select name=\"f_category\" id=\"f_category\" style=\"max-width:250px;\">";        
//echo $valuec['id_cat'];exit;
//print_r($valuex);exit;
//echo $valuex['id_cat'];exit;
            $content .=MAP_BOL_Service::getInstance()->get_category_list_edit($valuex['id_cat']);
            $content .="</select>";
            $content .=",&nbsp;<b>".OW::getLanguage()->text('map', 'markericon').":</b> ";
            $content .="<select name=\"f_iconmarker\" id=\"f_iconmarker\" style=\"max-width:180px;\">";
//--
//$header_dir="";
//$dir = OW::getPluginManager()->getPlugin('fanpage')->getPluginFilesDir();
$dir= OW_DIR_STATIC_PLUGIN . "map".DS."ico". DS;
if (is_dir($dir)){
    $dp = opendir($dir);
    while($file = readdir($dp)){
        if($file != '.' AND $file != '..'){
            $uts=filemtime($dir.$file).md5($file);  
            $file=substr($file,0,-4);
            $fole_array[$uts] = $file;
        }
    }
    closedir($dp);
//    krsort($fole_array);
    asort($fole_array);

    foreach ($fole_array as $key => $dir_name) {
//     echo "Key: $key; Value: $dir_name<br />\n";
        $sel="";
                if (isset($_GET['pico']) AND $_GET['pico']!="" AND $_GET['pico']==$dir_name) $sel=" SELECTED ";
                else if (!isset($_GET['pico']) AND $valuex['ico']==$dir_name) $sel=" SELECTED ";
                else $sel="";
                $content .="<option ".$sel." value=\"".$dir_name."\">".$dir_name."</option>";
    }
}
//--
$content .="</select>";
$content .="<div id=\"map_thumb_ico\" style=\"display: inline-block;width:32px;min-height:35px;top: 14px;position: relative;\"></div>";

            $content .="</div>";

            $content .="<div class=\"ow_value clearfix\" style=\"margin-top:10px;\">";
                $content .="<b>".OW::getLanguage()->text('map', 'markertags_search').":</b> <input type=\"text\" name=\"f_mtags\" id=\"f_mtags\" value=\"".stripslashes($valuex['tags'])."\" style=\"display:inline-block;\" placeholder=\"".OW::getLanguage()->text('map', 'enter_tahs_marker')."\" /> ";
            $content .="</div>";


        if ($is_admin){
            $content .="<div class=\"ow_value clearfix\" style=\"margin-top:10px;border:1px solid #f00;\">";
            $content .="<div style=\"margin:10px;\">";
                $content .="<b>".OW::getLanguage()->text('map', 'markerpromotionoption').":</b> ";
                $content .="<select name=\"f_promotiontype\" id=\"f_promotiontype\" style=\"max-width:180px;\">";
                    if ($valuex['type_promo']=="normal" OR $valuex['type_promo']=="" OR !$valuex['type_promo']) $sel=" selected ";
                        else $sel="";
                    $content .="<option ".$sel." value=\"normal\">".OW::getLanguage()->text('map', 'ptype_normal')."</option>";
                    if ($valuex['type_promo']=="promotion_unlimited") $sel=" selected ";
                        else $sel="";
                    $content .="<option ".$sel." value=\"promotion_unlimited\">".OW::getLanguage()->text('map', 'ptype_promotion_unlimited')."</option>";
                $content .="</select>";
            $content .="</div>";
            $content .="</div>";
        }



            $content .="<div id=\"map_info_content\" class=\"ow_value clearfix\" style=\"margin-top:15px;\">";
            $content .="<b>".OW::getLanguage()->text('map', 'mapdescription').":</b> ";
            if ($is_admin){
                $content .="<textarea class=\"html\" name=\"mpdescription\" style=\"background-image:none;\" placeholder=\"".OW::getLanguage()->text('map', 'enter_marker_info')."\">".$valuex['desc']."</textarea>";
            }else{
                $content .="<textarea name=\"mpdescription\" style=\"background-image:none;\" placeholder=\"".OW::getLanguage()->text('map', 'enter_marker_info')."\">".$valuex['desc']."</textarea>";
            }
            $content .="</div>";
        $content .="</div>";



        $content .="<div class=\"ow_value clearfix\" style=\"margin:10px;\">";
//                $content .="&nbsp;";

        if ($valuex['id']>0){
//            $content .="<div class=\"clearfix ow_submit ow_smallmargin\">
                $content .="<div class=\"ow_right\">";
                    $content .="<span class=\"ow_button\">
                        <span class=\" ow_positive\">
                            <input type=\"submit\" name=\"dosavemarker\" value=\"".OW::getLanguage()->text('map', 'update')."\" class=\"ow_ic_save ow_positive\">
                        </span>
                    </span>";
                $content .="</div>";
//            $content .="</div>";
        }else{
//            $content .="<div class=\"clearfix ow_submit ow_smallmargin\">
                $content .="<div class=\"ow_right\">";
                    $content .="<span class=\"ow_button\">
                        <span class=\" ow_positive\">
                            <input type=\"submit\" name=\"dosavemarker\" value=\"".OW::getLanguage()->text('map', 'save')."\" class=\"ow_ic_save ow_positive\">
                        </span>
                    </span>";
                $content .="</div>";
//            $content .="</div>";
        }
                $content .="<div class=\"ow_right\" style=\"padding-right:10px;\">";
                    $content .="<a href=\"".$curent_url."map?mapmode=ed\">";
                    $content .="<span class=\"ow_button\">
                        <span class=\" ow_positive\">
                            <input type=\"button\" name=\"dosavemarker\" value=\"".OW::getLanguage()->text('map', 'cancel')."\" class=\"ow_ic_cancel ow_positive\">
                        </span>
                    </span>";
                    $content .="</a>";
                $content .="</div>";
        $content .="</div>";


    $content .="</div>";
    $content .="<br/> <br/>";

    $content .="<input type=\"hidden\" name=\"mapmode\" value=\"ed\">";
    $content .="<input type=\"hidden\" name=\"ss\" value=\"".substr(session_id(),3,5)."\">";
    $content .="<input type=\"hidden\" name=\"ttid\" value=\"".$valuex['id']."_".$id_user."\">";


    $content .="<div class=\"ow_value clearfix\" style=\"border:1px solid #bbb;\">";
        $content .="<div class=\"ow_left ow_value clearfix item ow_box_empty ow_box item_border \" style=\"margin:10px;vertical-align:top;\">";
            $content .="<input type=\"file\" name=\"imgs[]\" id=\"file\">";
        $content .="</div>";
        $content .="<div class=\"ow_left ow_value clearfix item ow_box_empty ow_box item_border \" style=\"margin:10px;vertical-align:top;\">";
            $content .="<input type=\"file\" name=\"imgs[]\" id=\"file\">";
        $content .="</div>";
        $content .="<div class=\"ow_left ow_value clearfix item ow_box_empty ow_box item_border \" style=\"margin:10px;vertical-align:top;\">";
            $content .="<input type=\"file\" name=\"imgs[]\" id=\"file\">";
        $content .="</div>";
        $content .="<div class=\"ow_left ow_value clearfix item ow_box_empty ow_box item_border \" style=\"margin:10px;vertical-align:top;\">";
            $content .="<input type=\"file\" name=\"imgs[]\" id=\"file\">";
        $content .="</div>";
        $content .="<div class=\"ow_left ow_value clearfix item ow_box_empty ow_box item_border \" style=\"margin:10px;vertical-align:top;\">";
            $content .="<input type=\"file\" name=\"imgs[]\" id=\"file\">";
        $content .="</div>";
        $content .="<div class=\"ow_left ow_value clearfix item ow_box_empty ow_box item_border \" style=\"margin:10px;vertical-align:top;\">";
            $content .="<input type=\"file\" name=\"imgs[]\" id=\"file\">";
        $content .="</div>";

        $content .="<div class=\"ow_center clearfix\" style=\"margin:10px;vertical-align:top;\">";
                $content .="<div class=\"ow_center\">";
                    $content .="<span class=\"ow_button\">
                        <span class=\" ow_positive\">
                            <input type=\"submit\" name=\"dosavemarker\" value=\"".OW::getLanguage()->text('map', 'save')."\" class=\"ow_ic_save ow_positive\">
                        </span>
                    </span>";
                $content .="</div>";
        $content .="</div>";


    $content .="</div>";




//--------edit img end
    if ($ideditmark>0){
        $content .="<div class=\"ow_value ow_center clearfix\" style=\"margin:10px;\">";
//        $content .="<div class=\"ow_value clearfix item ow_box_empty ow_box item_border \" style=\"margin:10px;\">";
/*
//$dir= OW_DIR_STATIC_PLUGIN . "map".DS."ico". DS;
$dir=MAP_BOL_Service::getInstance()->get_plugin_dir('map').$id_user.DS;
$fole_array=array();
if (is_dir($dir)){
    $dp = opendir($dir);
    while($file = readdir($dp)){
        if($file != '.' AND $file != '..'){
            $uts=filemtime($dir.$file).md5($file);  
            $file=substr($file,0,-4);
            $fole_array[$uts] = $file;
        }
    }
    closedir($dp);
    asort($fole_array);

    foreach ($fole_array as $key => $dir_name) {
//        $sel="";
//                if ($valuex['ico']==$dir_name) $sel=" SELECTED ";
//                    else $sel="";
//                $content .="<option ".$sel." value=\"".$dir_name."\">".$dir_name."</option>";
            $img=$value['id_map']."_".$value['image']."_mini.".$value['itype'];
            $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$id_user.DS;

            $content .="<img src=\"".$url.$img."\" style=\"width:180px;\">";
    }
}
*/

        $cell=1;

        if ($is_admin){
            $add=" ";
        }else{
            $add=" AND id_ownerm='".addslashes($id_user)."' ";
        }

        $query = "SELECT * FROM " . OW_DB_PREFIX. "map_images 
        WHERE id_map='".addslashes($ideditmark)."' ".$add;
//echo $query;exit;
//echo $query;
        $arr = OW::getDbo()->queryForList($query);
        $ctab="";
        foreach ( $arr as $value ){
            $img=$value['id_map']."_".$value['image']."_mini.".$value['itype'];
//            $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$id_user.DS;
            $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$value['id_ownerm'].DS;
            $ctab .="<td style=\"padding:0;margin:0;text-align:center;\">";
            $ctab .="<div class=\"ow_center ow_value clearfix item ow_box_empty ow_box item_border \" style=\"margin:10px;vertical-align:top;\">";
                $ctab .="<img src=\"".$url.$img."\" style=\"max-width:180px;\">";

                $ctab .="<br/>";
                $ctab .="<div class=\"ow_left clearfix\" style=\"display:inline-block;margin-top:5px;\">";
                    if ($value['is_default']==1) $sel=" checked ";
                        else $sel="";
                    $ctab .="<input ".$sel." type=\"radio\" name=\"defimg\" value=\"def_".$value['id_map']."_".$value['image']."\">";
                    $ctab .="&nbsp;";
                    $ctab .="<b style=\"\">".OW::getLanguage()->text('map', 'select_default_image')."</b> ";
                $ctab .="</div>";
                $ctab .="<br/>";
                $ctab .="<div class=\"ow_right clearfix\" style=\"display:inline-block;margin-top:5px;\">";
                    $ctab .="<b style=\"color:#f00;\">".OW::getLanguage()->text('map', 'check_fordelete_image').":</b> ";
                    $ctab .="<input type=\"checkbox\" name=\"delimg[]\" value=\"del_".$value['id_map']."_".$value['image']."\">";
                $ctab .="</div>";

            $ctab .="</div>";
            $ctab .="</td>";
            $cell=$cell+1;
            if ($cell>$columns){
                $cell=1;
                $ctab .="</tr><tr>";
            }
        }
        if ($ctab){
            if ($ctab<$columns){
                for ($i=$cell;$i<$columns;$i++){
                    $ctab .="<td style=\"padding:0;margin:0;\"></td>";
                }
            }
            $content .="<table class=\"ow_center\" style=\"display:inline-block;\"><tr>".$ctab."</tr></table>";
        }
        $content .="</div>";
    }//if $ideditmark
//--------edit img end

    $content .="</form>";
//}else{
//    $content .="<form method=\"get\" action=\"".$curent_url."map/search\">";
}




/*
//$content .="<input type=\"text\" name=\"addressInput\" id=\"addressInput\" value=\"Gdańsk, Królewskei Wzgórze\">";
$content .="<div id=\"map_canvas\" style=\"width: 100%;min-height: 400px;\">";
$content .="<div id=\"map\" style=\"width:100%;\"></div>";
$content .="</div>";
*/



//if ($curent_tab=="shop" AND OW::getConfig()->getValue('map', 'tabdisable_shop')!=1){
            if ($ctab=="shop"){
                $content=MAP_BOL_Service::getInstance()->make_tabs('shop',$content);
            }else if ($ctab=="fanpage"){
                $content=MAP_BOL_Service::getInstance()->make_tabs('fanpage',$content);
            }else if ($ctab=="news"){
                $content=MAP_BOL_Service::getInstance()->make_tabs('news',$content);
            }else if ($ctab=="event"){
                $content=MAP_BOL_Service::getInstance()->make_tabs('event',$content);
            }else{
                $content=MAP_BOL_Service::getInstance()->make_tabs($mapmode,$content);
            }
//}

$this->assign('content', $content);

/*

	//params = GET, POST
//print_r($params);
//$id_user=$params['id_user'];
$id_pager=$params['id_page'];
        $this->setMapTitle(OW::getLanguage()->text('map', 'index_page_title')); //title menu
        $this->setMapHeading(OW::getLanguage()->text('map', 'index_page_heading')); //title page

        
        if ($id_pager>0){
//        $this->assign('pageurl', "http://www.otozakupy.pl/");


            $menu_list="";
            $rateDao = BOL_RateDao::getInstance();
            
            if ($is_admin){
                $add="";
            }else if ($id_user>0){
                $add=" AND (can_watch_groups='1' OR can_watch_groups='0') AND active='1' ";
            }else{
                $add=" AND (can_watch_groups='2' OR can_watch_groups='0') AND active='1' ";
            }
    	
            $query = "SELECT * FROM " . OW_DB_PREFIX. "map  
            WHERE id= '".addslashes($id_pager)."' ".$add." LIMIT 1";
//echo $query;exit;
//            $arr = OW::getDbo()->queryForList($query, array('id' => (int) $id, 'order_main' => (int) $order_main, 'title' => $title, 'url_external' => $url_external, 'content' => $content ));
            $arr = OW::getDbo()->queryForList($query);
            $value=$arr[0];

            $set_url_frame="";
            if ($value['id']>0){
$seo_title =strtolower($seo_title);

                if ($value['url_external']){
                    $set_url_frame=stripslashes($value['url_external']);
//echo $set_url_frame;exit;
                    $this->assign('pageurl', $set_url_frame);
                }else{
                    $set_url_frame="/map/page/".$id_pager."/".$seo_title.".html";
//echo $set_url_frame;exit;
                    $this->assign('pageurl', $set_url_frame);
                }
            }else{
                $set_url_frame="/map/page/0/index.html";
//echo  $set_url_frame;exit;
                $this->assign('pageurl', $set_url_frame);
            }        

            if ($set_url_frame){
                $this->setMapTitle(stripslashes($value['title'])); //title menu
                $this->setMapHeading(stripslashes($value['title'])); //title page

//$this->assign('testzmienna', "<hr>POST:".print_r($_POST,1)."----GET:".print_r($_GET,1)."---".print_r($params,1)."---------------".$set_url_frame."<hr>");
            }
        }else{
//print_r($_GET);
            echo "Map not found...";
             OW::getApplication()->redirect($curent_url."index");
        }
*/
    }




















    public function indexzoom($params)
    {
//echo "SdfsDF";exit;
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
                    $curent_url = 'http';
                    if (isset($_SERVER["HTTPS"])) {$curent_url .= "s";}
                    $curent_url .= "://";
                    $curent_url .= $_SERVER["SERVER_NAME"]."/";
$curent_url=OW_URL_HOME;

$pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.'map'.DS;
$pluginStaticURL2=OW::getPluginManager()->getPlugin('map')->getStaticUrl();
$content="";
$columns=3;

    if (isset($params['id_markz']) AND $params['id_markz']>0){
        $ideditmark=$params['id_markz'];
            if (!$is_admin) $addand=" AND active='1' ";
                else $addand="";
            $query = "SELECT * FROM " . OW_DB_PREFIX. "map WHERE id='".addslashes($ideditmark)."' ".$addand." LIMIT 1";
            $arr = OW::getDbo()->queryForList($query);
            if (isset($arr[0])){
                $row=$arr[0];
            }else{
                $row=array();
                $row['id']=0;
            }



        if ($ideditmark>0 AND $row['id']>0 AND $row['id']==$ideditmark){

//--------title start
                    $content .="<div class=\"ow_leftD clearfix ow_pageX\" >";
                        $content .="<h1 class=\"ow_stdmargin ow_ic_file\">";
                            $content .=stripslashes($row['name']);
                        $content .="</h1>";
                    $content .="</div>";
//                $content .="<br/>";
//--------title end
//                    $content .="<div class=\"clearfix\">";


//--------desc start
                    $content .="<div class=\"ow_leftX clearfix ow_pageXx\" >";
                        $content .=stripslashes($row['desc']);
                    $content .="</div>";
//                $content .="<br/>";
//--------desc end

            $content .="<div class=\"ow_value ow_center clearfix\" style=\"margin:10px;\">";





//--------image start

            $cell=1;
            $query = "SELECT * FROM " . OW_DB_PREFIX. "map_images 
            WHERE id_map='".addslashes($ideditmark)."' ";
            $arr = OW::getDbo()->queryForList($query);
            $ctab="";
            foreach ( $arr as $value ){
                $img=$value['id_map']."_".$value['image']."_mini.".$value['itype'];
//                $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$id_user.DS;
                $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$value['id_ownerm'].DS;
                $ctab .="<td style=\"padding:0;margin:0;text-align:center;\">";
                $ctab .="<div class=\"ow_center ow_value clearfix item ow_box_empty ow_box item_border \" style=\"margin:10px;vertical-align:top;\">";
                    $ctab .="<img src=\"".$url.$img."\" style=\"max-width:180px;\">";
                    $ctab .="<br/>";
                    $ctab .="<div class=\"ow_right clearfix ow_right ow_nowrap ow_remark\" style=\"display:inline-block;font-size:90%;\">";
                    if ($value['data_add']!="0000-00-00 00:00:00"){
                        list($xdat,$xtime)=explode(" ",$value['data_add']);
                        $ctab .=$xdat;
                    }
//                        $ctab .="<b style=\"color:#f00;\">".OW::getLanguage()->text('map', 'check_fordelete_image').":</b> ";
    //                    $ctab .="<input type=\"checkbox\" name=\"delimg[]\" value=\"del_".$value['id_map']."_".$value['image']."\">";
                    $ctab .="</div>";
/*
                    $ctab .="<br/>";
                    $ctab .="<div class=\"ow_right clearfix\" style=\"display:inline-block;\">";
                        $ctab .="<b style=\"color:#f00;\">".OW::getLanguage()->text('map', 'check_fordelete_image').":</b> ";
                        $ctab .="<input type=\"checkbox\" name=\"delimg[]\" value=\"del_".$value['id_map']."_".$value['image']."\">";
                    $ctab .="</div>";
                    $ctab .="<div class=\"ow_left clearfix\" style=\"display:inline-block;\">";
                        if ($value['is_default']==1) $sel=" checked ";
                            else $sel="";
                        $ctab .="<input ".$sel." type=\"radio\" name=\"defimg\" value=\"def_".$value['id_map']."_".$value['image']."\">";
                        $ctab .="&nbsp;";
                        $ctab .="<b style=\"\">".OW::getLanguage()->text('map', 'select_default_image')."</b> ";
                    $ctab .="</div>";
*/
                $ctab .="</div>";
                $ctab .="</td>";
                $cell=$cell+1;
                if ($cell>$columns){
                    $cell=1;
                    $ctab .="</tr><tr>";
                }
            }//for

            if ($ctab){
                if ($ctab<$columns){
                    for ($i=$cell;$i<$columns;$i++){
                        $ctab .="<td style=\"padding:0;margin:0;\"></td>";
                    }
                }
                $content .="<table class=\"ow_center\" style=\"display: inline-block;\"><tr>".$ctab."</tr></table>";
            }
//--------edit img end
            $content .="</div>";



        }else{//if $ideditmark >0
            $content .="<div class=\"ow_center\">".OW::getLanguage()->text('map', 'markernotfound')."</div>";
        }

    }else{//end if (isset($params['id_markz']) AND $params['id_markz']>0){ $content="";
        $content .="<div class=\"ow_center\">".OW::getLanguage()->text('map', 'markernotfound')."</div>";
    }



        $content=MAP_BOL_Service::getInstance()->make_tabs("zoom",$content);
        $this->assign('content', $content);

    }


    public function indexginfo($params)
    {
//echo "SdfsDF";exit;
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
                    $curent_url = 'http';
                    if (isset($_SERVER["HTTPS"])) {$curent_url .= "s";}
                    $curent_url .= "://";
                    $curent_url .= $_SERVER["SERVER_NAME"]."/";
$curent_url=OW_URL_HOME;

$pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.'map'.DS;
$pluginStaticURL2=OW::getPluginManager()->getPlugin('map')->getStaticUrl();
$content="";
$columns=3;

    if (isset($params['id_markz']) AND $params['id_markz']>0){
        $ideditmark=$params['id_markz'];
            if (!$is_admin) $addand=" AND active='1' ";
                else $addand="";
            $query = "SELECT * FROM " . OW_DB_PREFIX. "map WHERE id='".addslashes($ideditmark)."' ".$addand." LIMIT 1";
            $arr = OW::getDbo()->queryForList($query);
            if (isset($arr[0])){
                $row=$arr[0];
            }else{
                $row=array();
                $row['id']=0;
            }



        if ($ideditmark>0 AND $row['id']>0 AND $row['id']==$ideditmark){

//--------title start
                    $content .="<div class=\"ow_leftD clearfix ow_pageX\" >";
                        $content .="<h1 class=\"ow_stdmargin ow_ic_file\">";
                            $content .=stripslashes($row['name']);
                        $content .="</h1>";
                    $content .="</div>";
//                $content .="<br/>";
//--------title end
//                    $content .="<div class=\"clearfix\">";


//--------desc start
                    $content .="<div class=\"ow_leftX clearfix ow_pageXx\" >";
                        $content .=stripslashes($row['desc']);
                    $content .="</div>";
//                $content .="<br/>";
//--------desc end

            $content .="<div class=\"ow_value ow_center clearfix\" style=\"margin:10px;display:inline-block;width: 100%;
margin: auto;
text-align: center;border-top:1px solid #ddd;\">";





//--------image start

            $cell=1;
            $query = "SELECT * FROM " . OW_DB_PREFIX. "map_images 
            WHERE id_map='".addslashes($ideditmark)."' ";
            $arr = OW::getDbo()->queryForList($query);
            $ctab="";
            foreach ( $arr as $value ){
                $img=$value['id_map']."_".$value['image']."_mini.".$value['itype'];
//                $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$id_user.DS;
                $url=MAP_BOL_Service::getInstance()->get_plugin_url('map').$value['id_ownerm'].DS;
                $ctab .="<td style=\"padding:0;margin:0;text-align:center;\">";
                $ctab .="<div class=\"ow_center ow_value clearfix item ow_box_empty ow_box item_border \" style=\"margin:10px;vertical-align:top;\">";
                    $ctab .="<img src=\"".$url.$img."\" style=\"max-width:180px;\">";
                    $ctab .="<br/>";
                    $ctab .="<div class=\"ow_right clearfix ow_right ow_nowrap ow_remark\" style=\"display:inline-block;font-size:90%;\">";
                    if ($value['data_add']!="0000-00-00 00:00:00"){
                        list($xdat,$xtime)=explode(" ",$value['data_add']);
                        $ctab .=$xdat;
                    }
//                        $ctab .="<b style=\"color:#f00;\">".OW::getLanguage()->text('map', 'check_fordelete_image').":</b> ";
    //                    $ctab .="<input type=\"checkbox\" name=\"delimg[]\" value=\"del_".$value['id_map']."_".$value['image']."\">";
                    $ctab .="</div>";
/*
                    $ctab .="<br/>";
                    $ctab .="<div class=\"ow_right clearfix\" style=\"display:inline-block;\">";
                        $ctab .="<b style=\"color:#f00;\">".OW::getLanguage()->text('map', 'check_fordelete_image').":</b> ";
                        $ctab .="<input type=\"checkbox\" name=\"delimg[]\" value=\"del_".$value['id_map']."_".$value['image']."\">";
                    $ctab .="</div>";
                    $ctab .="<div class=\"ow_left clearfix\" style=\"display:inline-block;\">";
                        if ($value['is_default']==1) $sel=" checked ";
                            else $sel="";
                        $ctab .="<input ".$sel." type=\"radio\" name=\"defimg\" value=\"def_".$value['id_map']."_".$value['image']."\">";
                        $ctab .="&nbsp;";
                        $ctab .="<b style=\"\">".OW::getLanguage()->text('map', 'select_default_image')."</b> ";
                    $ctab .="</div>";
*/
                $ctab .="</div>";
                $ctab .="</td>";
                $cell=$cell+1;
                if ($cell>$columns){
                    $cell=1;
                    $ctab .="</tr><tr>";
                }
            }//for

            if ($ctab){
                if ($ctab<$columns){
                    for ($i=$cell;$i<$columns;$i++){
                        $ctab .="<td style=\"padding:0;margin:0;\"></td>";
                    }
                }
                $content .="<table class=\"ow_center\" style=\"display: inline-block;\"><tr>".$ctab."</tr></table>";
            }
//--------edit img end
            $content .="</div>";







        }else{//if $ideditmark >0
            $content .="<div class=\"ow_center\">".OW::getLanguage()->text('map', 'markernotfound')."</div>";
        }

    }else{//end if (isset($params['id_markz']) AND $params['id_markz']>0){ $content="";
        $content .="<div class=\"ow_center\">".OW::getLanguage()->text('map', 'markernotfound')."</div>";
    }



        echo $content;
        exit;
//        $content=MAP_BOL_Service::getInstance()->make_tabs("zoom",$content);
        $this->assign('content', $content);

    }




}


