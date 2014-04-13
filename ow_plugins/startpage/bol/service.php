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



final class STARTPAGE_BOL_Service
{
    /**
     * Constructor.
     */
    private function __construct() { }
    
    /**
     * Singleton instance.
     *
     * @var OCSTOPUSERS_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns an instance of class
     *
     * @return OCSTOPUSERS_BOL_Service
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    public static function sortArrayItemByDesc( $el1, $el2 )
    {
        if ( $el1['score'] === $el2['score'] )
        {
        	if ( $el1['rates'] === $el2['rates'] )
        	{
        		return 0;
        	}
        	
            return $el1['rates'] < $el2['rates'] ? 1 : -1;
        }

        return $el1['score'] < $el2['score'] ? 1 : -1;
    }

    public static function sortArrayItemByTimeDesc( $el1, $el2 )
    {
        if ( $el1['timeStamp'] === $el2['timeStamp'] )
        {
            return 0;
        }

        return $el1['timeStamp'] < $el2['timeStamp'] ? 1 : -1;
    }
    
    public function findList( $page, $limit )
    {
    	$first = ( $page - 1 ) * $limit;
    	
    	$topRatedList = $this->findMostRatedEntityList('user_rates', $first, $limit);

        if ( !$topRatedList )
        {
            return array();
        }
        
        $userArr = BOL_UserService::getInstance()->findUserListByIdList(array_keys($topRatedList));

        $users = array();

        foreach ( $userArr as $key => $user )
        {
            $users[$key]['dto'] = $user;
            $users[$key]['score'] = $topRatedList[$user->id]['avgScore'];
            $users[$key]['rates'] = $topRatedList[$user->id]['ratesCount'];
        }
        
        usort($users, array('OCSTOPUSERS_BOL_Service', 'sortArrayItemByDesc'));
        
        return $users;
    }
    
    public function countUsers()
    {
    	return BOL_RateService::getInstance()->findMostRatedEntityCount('user_rates');
    }
    
    public function findRateUserList( $userId, $page, $limit )
    {
    	$rateDao = BOL_RateDao::getInstance();
    	$userDao = BOL_UserDao::getInstance();
    	
    	$limit = (int) $limit;
        $first = ( $page - 1 ) * $limit;
        
        $sql = "SELECT `r`.`score`, `r`.`userId`, `r`.`timeStamp`
            FROM `" . $rateDao->getTableName() . "` AS `r`
            INNER JOIN `" . $userDao->getTableName() . "` AS `u` ON (`u`.`id` = `r`.`userId`) 
            WHERE `entityId` = :entityId AND `entityType` = 'user_rates'
            ORDER BY `timeStamp` DESC
            LIMIT :first, :limit";
        
        $list = OW::getDbo()->queryForList($sql, array('entityId' => $userId, 'first' => $first, 'limit' => $limit));
        
        if ( !$list )
        {
        	return null;
        }
        
        $idList = array();
        $keyList = array();
        foreach ( $list as $rate )
        {
        	$keyList[$rate['userId']] = $rate;
        	array_push($idList, $rate['userId']);
        }
        
        $userArr = BOL_UserService::getInstance()->findUserListByIdList($idList);

        $users = array();
        foreach ( $userArr as $key => $user )
        {
            $users[$key]['dto'] = $user;
            $users[$key]['score'] = $keyList[$user->id]['score'];
            $users[$key]['timeStamp'] = $keyList[$user->id]['timeStamp'];
        }
        
        usort($users, array('OCSTOPUSERS_BOL_Service', 'sortArrayItemByTimeDesc'));

        return $users;
    }
    
    public function countRateUsers( $userId )
    {
        $rateDao = BOL_RateDao::getInstance();
        $userDao = BOL_UserDao::getInstance();
        
        $sql = "SELECT COUNT(*)
            FROM `" . $rateDao->getTableName() . "` AS `r`
            INNER JOIN `" . $userDao->getTableName() . "` AS `u` ON (`u`.`id` = `r`.`userId`) 
            WHERE `entityId` = :entityId AND `entityType` = 'user_rates'";
        
        return (int) OW::getDbo()->queryForColumn($sql, array('entityId' => $userId));
    }
    
    public function findMostRatedEntityList( $entityType, $first, $count )
    {
    	$rateDao = BOL_RateDao::getInstance();
    	
    	$query = "SELECT `" . BOL_RateDao::ENTITY_ID . "` AS `id`, COUNT(*) as `ratesCount`, AVG(`score`) as `avgScore`
            FROM " . $rateDao->getTableName() . "
                        WHERE `" . BOL_RateDao::ENTITY_TYPE . "` = :entityType AND `" . BOL_RateDao::ACTIVE . "` = 1
            GROUP BY `" . BOL_RateDao::ENTITY_ID . "`
                        ORDER BY `avgScore` DESC, `ratesCount` DESC
                        LIMIT :first, :count";

        $arr = OW::getDbo()->queryForList($query, array('entityType' => $entityType, 'first' => (int) $first, 'count' => (int) $count));
        
        $resultArray = array();

        foreach ( $arr as $value )
        {
            $resultArray[$value['id']] = $value;
        }

        return $resultArray;
    }

    public function mb_word_wrap($string, $max_length, $end_substitute = null, $html_linebreaks = true) { 

    if($html_linebreaks) $string = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
    $string = strip_tags($string); //gets rid of the HTML

    if(empty($string) || mb_strlen($string) <= $max_length) {
        if($html_linebreaks) $string = nl2br($string);
        return $string;
    }

    if($end_substitute) $max_length -= mb_strlen($end_substitute, 'UTF-8');

    $stack_count = 0;
    while($max_length > 0){
        $char = mb_substr($string, --$max_length, 1, 'UTF-8');
        if(preg_match('#[^\p{L}\p{N}]#iu', $char)) $stack_count++; //only alnum characters
        elseif($stack_count > 0) {
            $max_length++;
            break;
        }
    }
    $string = mb_substr($string, 0, $max_length, 'UTF-8').$end_substitute;
    if($html_linebreaks) $string = nl2br($string);

    return $string;
    }

    public function make_box( $arr,$type="html" )
    {
        $start_content_li="";
        $curent_url=OW_URL_HOME;
//        $id_user=$params['id_user'];
//        $id_pager=$params['id_page'];

        if ($type=="array"){
            $box_array=array();
        }
        if (isset($arr) AND is_array($arr)){
                        foreach ( $arr as $value )
                        {
//print_r($value);exit;
                            $data_array=array();
                            if (isset($value['data'])){
                                $data_array=json_decode($value['data'],true);
                            }

                            $action_name=OW::getLanguage()->text('startpage', 'action_other');
//                            $action_name=OW::getLanguage()->text('startpage', 'action_other')."[".$value['activityType']."]";

//echo isset($data_array['ownerId'])."--".$data_array['ownerId'];exit;
                            if ($value['entityType']=="user_join"){
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['entityId']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['entityId']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['entityId']);
                                if ($uimg){
                                    $uimg_result ="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" height=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }else{
                                    $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }
                                $action_name=OW::getLanguage()->text('startpage', 'action_user_join');
                            }else if ($value['activityType']=="create" AND $value['entityType']=="blog-post"){
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['activityId']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['activityId']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['activityId']);
                                if ($uimg){
                                    $uimg_result ="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" height=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }else{
                                    $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }
                                $action_name=OW::getLanguage()->text('startpage', 'action_blog-post');
                            }else if ($value['muserId']>0){
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['muserId']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['muserId']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['muserId']);
                                if ($uimg){
//                                    $uimg_result ="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" height=\"45px\" style=\"max-width:45px;max-height:45px;\" align=\"left\" class=\"ui-li-thumb\">";
                                    $uimg_result ="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" height=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }else{
                                    $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }
//                            if ($value['activityType']=="create"){
                                if ($value['activityType']=="create" AND $value['entityType']=="user-status"){
                                    $action_name=OW::getLanguage()->text('startpage', 'action_user-status');
                                }else if ($value['activityType']=="comment" AND $value['entityType']=="user-status"){
//                                    $action_name=OW::getLanguage()->text('startpage', 'action_user-comment');
//                                }else if ($value['activityType']=="create" AND $value['entityType']=="blog-post"){
//                                    $action_name=OW::getLanguage()->text('startpage', 'action_user-comment');
                                }
                            }else{
                                $dname="...";
                                $uurl="";
                                $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                            }
if ($type=="array"){
    $start_content_li ="";
}

//----li start
            $content_tmp="";
            if (isset($data_array['content'])){
                if ($value['activityType']=="comment" AND $value['activityId']>0){
                        $sql = "SELECT * 
                        FROM " . OW_DB_PREFIX. "base_comment  
                        WHERE id='".addslashes($value['activityId'])."' 
                        LIMIT 1";
                        $arrc = OW::getDbo()->queryForList($sql);
                        $valuec=$arrc[0];
                        $content_tmp=$this->html2txt(stripslashes($valuec['message']));            
                }else{
                    $content_tmp=stripslashes($data_array['content']);
                }

                if ($value['entityType']=="multiple_photo_upload" ){
                }else{
                    $content_tmp=preg_replace('/<a(.*?)href="(.*?)"(.*?)>([^<]*)\<\/a\>/i', "<div class=\"clearfix ow_center\"><div class=\"clearfix ow_right\"><a class=\"ow_lbutton ow_right\" style=\"display: inline;width:auto;padding: 0px 5px;\" href=\"$2\">".OW::getLanguage()->text('startpage', 'more')."</a></div></div>", $content_tmp);
                }

                $content_tmp=str_replace("\r\n"," ",$content_tmp);
                $content_tmp=str_replace("\t"," ",$content_tmp);
                $content_tmp=str_replace("\n"," ",$content_tmp);
                $content_tmp=str_replace("\r"," ",$content_tmp);
                $content_tmp = preg_replace('/\s{2,}/',' ', $content_tmp);

                $content_tmp=str_replace("[ph:attachment]","",$content_tmp);
                $content_tmp=str_replace("[ph:activity]","",$content_tmp);

                $content_tmp =trim($content_tmp);

//                $start_content_li .=$content_tmp;
//                $start_content_li .="<textarea>".$content_tmp."</textarea>";
            }
//---
            if (isset($data_array['string'])){
                $title_tmp=$data_array['string'];
            }else{
                $title_tmp="";
            }
            if (!$title_tmp) $title_tmp=$action_name;
//---
            $data=date ("Y-m-d H:i:s", $value['timeStamp']);

            $start_content_li .=$this->box_content($title_tmp,$content_tmp,$data,$dname,$uurl,$uimg_result);
//            $start_content_li .=$this->box_content($title,$content_tmp,$data,$dname);
//------li end

if ($type=="array"){
    $box_array[]=$start_content_li;
}

                        }//foreach
        }
//$ss=array();
//$ss[]=print_r($arr,1);
//$ss[]="ssS333";
//return $ss;
        if ($type=="array"){
            return $box_array;
        }else{
            return $start_content_li;
        }
    }



    public function make_box_shop($type="html",$start=0,$perpage=20)
    {
        $start_content_li="";
        $curent_url=OW_URL_HOME;
//        $id_user=$params['id_user'];
//        $id_pager=$params['id_page'];

//        $pluginStaticURL2=OW::getPluginManager()->getPlugin('photo')->getStaticUrl();
        $pluginStaticURL =OW::getPluginManager()->getPlugin('shoppro')->getUserFilesUrl();
        $pluginStaticDir =OW::getPluginManager()->getPlugin('shoppro')->getUserFilesDir();

                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "shoppro_products 
                        WHERE active='1'  
                         ORDER BY date_modyfing DESC 
                            LIMIT ".$start.",".$perpage;
                            $arr = OW::getDbo()->queryForList($sql);
        if (isset($arr) AND is_array($arr)){
                        foreach ( $arr as $value )
                        {

                            $action_name=OW::getLanguage()->text('startpage', 'shop_product');

                            if ($value['id_owner']>0){
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['id_owner']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['id_owner']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['id_owner']);
                                if ($uimg){
                                    $uimg_result ="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" height=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }else{
                                    $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }
                            }else{
                                $dname="...";
                                $uurl="";
                                $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                            }
    $box_url="";
if ($type=="array"){
    $start_content_li ="";
}

//----li start


//--
            $content_tmp="";
            if (isset($value['description'])){



                    $content_tmp=stripslashes($value['description']);
                

//                    $content_tmp=preg_replace('/<a(.*?)href="(.*?)"(.*?)>([^<]*)\<\/a\>/i', "<div class=\"clearfix ow_center\"><div class=\"clearfix ow_right\"><a class=\"ow_lbutton ow_right\" style=\"display: inline;width:auto;padding: 0px 5px;\" href=\"$2\">".OW::getLanguage()->text('startpage', 'more')."</a></div></div>", $content_tmp);
                

                $content_tmp=str_replace("\r\n"," ",$content_tmp);
                $content_tmp=str_replace("\t"," ",$content_tmp);
                $content_tmp=str_replace("\n"," ",$content_tmp);
                $content_tmp=str_replace("\r"," ",$content_tmp);
                $content_tmp = preg_replace('/\s{2,}/',' ', $content_tmp);

//                $content_tmp=str_replace("[ph:attachment]","",$content_tmp);
//                $content_tmp=str_replace("[ph:activity]","",$content_tmp);

                $content_tmp =trim($content_tmp);
                $content_tmp =mb_substr($this->html2txt($content_tmp),0,255);
//                if (mb_strlen($content_tmp)<

//                $start_content_li .=$content_tmp;
//                $start_content_li .="<textarea>".$content_tmp."</textarea>";
            }
            
//---
            $title_tmp=$value['name'];
            if (!$title_tmp) $title_tmp=OW::getConfig()->getValue('startpage', 'shop_product');
//---
            $data=date ("Y-m-d H:i:s", $value['date_modyfing']);
//--
            $box_url=$curent_url."product/".$value['id']."/zoom/".$this->make_seo_url($title_tmp).".html";
//--
            $content_tmp_image="";
            if (is_file($pluginStaticDir."images/product_".$value['id'].".jpg")){
                $content_tmp_image="<img src=\"".$pluginStaticURL."images/product_".$value['id'].".jpg\" width=\"140\" height=\"140\" alt=\"".$title_tmp."\" title=\"".$title_tmp."\">";
            }



            $start_content_li .=$this->box_content($title_tmp,$content_tmp_image.$content_tmp,$data,$dname,$uurl,$uimg_result,$box_url);
//            $start_content_li .=$this->box_content($title,$content_tmp,$data,$dname);

//------li end

if ($type=="array"){
    $box_array[]=$start_content_li;
}

                        }//foreach
        }
//$ss=array();
//$ss[]=print_r($arr,1);
//$ss[]="ssS333";
//return $ss;
        if ($type=="array"){
            return $box_array;
        }else{
            return $start_content_li;
        }
    }



    public function make_box_photo($type="html",$start=0,$perpage=20)
    {
        $start_content_li="";
        $curent_url=OW_URL_HOME;
//        $id_user=$params['id_user'];
//        $id_pager=$params['id_page'];
//        $pluginStaticURL2=OW::getPluginManager()->getPlugin('photo')->getStaticUrl();
        $pluginStaticURL =OW::getPluginManager()->getPlugin('photo')->getUserFilesUrl();
        $pluginStaticDir =OW::getPluginManager()->getPlugin('photo')->getUserFilesDir();



                            $sql = "SELECT ph.*,al.name as alname, al.userId,al.createDatetime FROM " . OW_DB_PREFIX. "photo ph 
                            LEFT JOIN " . OW_DB_PREFIX. "photo_album al ON (al.id=ph.albumId) 
                        WHERE ph.privacy='everybody' AND ph.status='approved'  
                         ORDER BY ph.addDatetime DESC 
                            LIMIT ".$start.",".$perpage;
                            $arr = OW::getDbo()->queryForList($sql);
        if (isset($arr) AND is_array($arr)){
                        foreach ( $arr as $value )
                        {

                            $action_name=OW::getLanguage()->text('startpage', 'shop_product');

                            if ($value['userId']>0){
                                $dname=BOL_UserService::getInstance()->getDisplayName($value['userId']);
                                $uurl=BOL_UserService::getInstance()->getUserUrl($value['userId']);
                                $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value['userId']);
                                if ($uimg){
                                    $uimg_result ="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" height=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }else{
                                    $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                                }
                            }else{
                                $dname="...";
                                $uurl="";
                                $uimg_result ="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"max-width:45px;max-height:45px;border:0;margin:0px;align:left;display:inline-block;\" align=\"left\" class=\"ui-li-thumb\">";
                            }
    $box_url="";
if ($type=="array"){
    $start_content_li ="";
}

//----li start
            $content_tmp="";
//$content_tmp.=$pluginStaticURL;

//http://test3.a6.pl/ow_static/plugins/photo/
//http://test3.a6.pl/ow_userfiles/plugins/photo/photo_preview_10.jpg
////http://test3.a6.pl/ow_userfiles/plugins/shoppro/


//http://test3.a6.pl/ow_userfiles/plugins/photo/photo_preview_10.jpg
/*
            if (isset($value['description'])){

                    $content_tmp=stripslashes($value['description']);
                

//                    $content_tmp=preg_replace('/<a(.*?)href="(.*?)"(.*?)>([^<]*)\<\/a\>/i', "<div class=\"clearfix ow_center\"><div class=\"clearfix ow_right\"><a class=\"ow_lbutton ow_right\" style=\"display: inline;width:auto;padding: 0px 5px;\" href=\"$2\">".OW::getLanguage()->text('startpage', 'more')."</a></div></div>", $content_tmp);
                

                $content_tmp=str_replace("\r\n"," ",$content_tmp);
                $content_tmp=str_replace("\t"," ",$content_tmp);
                $content_tmp=str_replace("\n"," ",$content_tmp);
                $content_tmp=str_replace("\r"," ",$content_tmp);
                $content_tmp = preg_replace('/\s{2,}/',' ', $content_tmp);

//                $content_tmp=str_replace("[ph:attachment]","",$content_tmp);
//                $content_tmp=str_replace("[ph:activity]","",$content_tmp);

                $content_tmp =trim($content_tmp);
                $content_tmp =mb_substr($this->html2txt($content_tmp),0,255);
//                if (mb_strlen($content_tmp)<

//                $start_content_li .=$content_tmp;
//                $start_content_li .="<textarea>".$content_tmp."</textarea>";
            }
*/
//---
            $title_tmp=$value['description'];
//            if (!$title_tmp) $title_tmp=OW::getConfig()->getValue('startpage', 'shop_product');
            if (!$title_tmp) $title_tmp="";
//---
            $data=date ("Y-m-d H:i:s", $value['createDatetime']);
//--
            $box_url=$curent_url."photo/view/".$value['id'];
//---
            if (is_file($pluginStaticDir."photo_preview_".$value['id'].".jpg")){
//                    $content_tmp.=$pluginStaticURL."photo_preview_".$value['id'].".jpg";
//                $content_tmp.="<div class=\"ow_photo_list_item_thumb\"><img src=\"".$pluginStaticURL."photo_preview_".$value['id'].".jpg"."\" width=\"140\" height=\"140\"></div>";
                $content_tmp.="<img src=\"".$pluginStaticURL."photo_preview_".$value['id'].".jpg\" width=\"140\" height=\"140\" alt=\"".$title_tmp."\" title=\"".$title_tmp."\">";
            }


            $start_content_li .=$this->box_content($title_tmp,$content_tmp,$data,$dname,$uurl,$uimg_result,$box_url);
//            $start_content_li .=$this->box_content($title,$content_tmp,$data,$dname);

//------li end

if ($type=="array"){
    $box_array[]=$start_content_li;
}

                        }//foreach
        }
//$ss=array();
//$ss[]=print_r($arr,1);
//$ss[]="ssS333";
//return $ss;
        if ($type=="array"){
            return $box_array;
        }else{
            return $start_content_li;
        }
    }










    public function box_content($title="",$content="",$data="",$membr_name="",$member_url="",$member_img="",$box_url="" ){
        $start_content_li="";
        $curent_url=OW_URL_HOME;
//        $id_user=$params['id_user'];
//        $id_pager=$params['id_page'];

$start_content_li .="<div class=\"item ow_box_empty ow_box item_border\">";
//$start_content_li .="<div class=\"item ow_box\">";
    $start_content_li .="<div class=\"ow_content Xow_box_empty clearfix ow_break_word\">";
        $start_content_li .="<div class=\"clearfix\">";
            if (isset( $data) AND $data>0 AND is_int($data)){
                $start_content_li .="<div class=\"clearfix ow_right ow_nowrap ow_remark\" style=\"font-size:11px;\">".date ("Y-m-d H:i:s", $data)."</div>";
            }else if (isset( $data)){
                $start_content_li .="<div class=\"clearfix ow_right ow_nowrap ow_remark\" style=\"font-size:11px;\">".$data."</div>";
            }
    
$start_content_li .="<div class=\"ow_my_avatar_widget clearfix\" style=\"line-height: 0;\">
    <div class=\"ow_left ow_my_avatar_img\" style=\"max-height:45px;\">
        <div class=\"ow_avatar\" style=\"display:block;line-height:0;\">";
            if ($member_url){
                $start_content_li .="<a href=\"".$member_url."\">";
            }
            $start_content_li .=$member_img;
            if ($member_url){
                $start_content_li .="</a>";
            }
        $start_content_li .="</div>
    </div>
    <div class=\"ow_my_avatar_cont\">";
        if ($member_url){
            $start_content_li .="<a href=\"".$member_url."\" class=\"ow_my_avatar_username\"><span style=\"min-width:90px;width: auto;\">@".$membr_name."</span></a>";
        }else{
            $start_content_li .="<span style=\"min-width:90px;width: auto;\">@".$membr_name."</span>";
        }
    $start_content_li .="</div>
</div>";


        $start_content_li .="</div>";
        if ($box_url) $start_content_li .="<a href=\"".$box_url."\">";
            $start_content_li .="<h2 class=\"ow_ipc_header clearfix ow_center\" style=\"font-size:14px;overflow: hidden;margin-bottom:10px;\">".mb_strtoupper($title)."</h2>";
        if ($box_url) $start_content_li .="</a>";

//$start_content_li .="<div class=\"ow_box ow_stdmargin clearfix index-BASE_CMP_MyAvatarWidget ow_break_word\" style=\"\">";

//$start_content_li .="</div>";




    
    $start_content_li .="<div class=\"clearfix\">";

//        $start_content_li .="<p class=\"ow_user_list_item \">";
//        $start_content_li .="</p>";

        if ($box_url) $start_content_li .="<a href=\"".$box_url."\">";
        $start_content_li .="<div class=\"clearfix ow_center wall_content \">";
        $start_content_li .=$content;
        $start_content_li .="</div>";
        if ($box_url) $start_content_li .="</a>";

    $start_content_li .="</div>";



//$start_content_li .="</li>";
$start_content_li .="</div>";
$start_content_li .="</div>";//item

        return $start_content_li;
    }



    public function make_tabs($selected="",$content="")
    {
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
$content_t ="";
        $content_t .="<div class=\"ow_content\">";
/*
        $page_on_top_shop=OW::getConfig()->getValue('startpage', 'ta_newsfeed_');
        if ($page_on_top_shop!=""){
                $content_t .="<div class=\"ow_content_menu_wrap ow_content_html\">";
                if (OW::getConfig()->getValue('shoppro', 'admin_replace_btobr')==1 AND !OW::getPluginManager()->isPluginActive('wysiwygeditor')){
                    $page_on_top_shop=SHOPPRO_BOL_Service::getInstance()->ntobr($page_on_top_shop);
                }
                $page_top_template="";
                $page_top_template .="<div class=\"ow_dnd_widget index-PAGES_CMP_MenuWidget\">";



                if (OW::getConfig()->getValue('shoppro', 'config_page_on_top_shop_title')!=""){
                $page_top_template .="<div class=\"ow_box_cap_empty ow_dnd_configurable_component clearfix\">
                    <div class=\"ow_box_cap_right\">
                        <div class=\"ow_box_cap_body\">
                            <h3 class=\"ow_ic_info\">".OW::getConfig()->getValue('shoppro', 'config_page_on_top_shop_title')."</h3>
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


//        if ($selected=="1" OR $selected=="newsfeed" OR !$selected) $sel=" active ";
        if ($selected=="newsfeed" OR !$selected) $sel=" active ";
            else $sel="";
//echo $selected."===".$sel;exit;
        $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."startpage/newsfeed\"><span class=\"ow_ic_plugin\">".OW::getLanguage()->text('startpage', 'ta_newsfeed')."</span></a></li>";


        if ( OW::getPluginManager()->isPluginActive('shoppro') ){
//            if ($id_user>0 OR $is_admin){
                if ($selected=="shop") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."startpage/shop\"><span class=\"ow_ic_cart\">".OW::getLanguage()->text('startpage', 'ta_shop')."</span></a></li>";
//            }
        }

        if ( OW::getPluginManager()->isPluginActive('photo') ){
//            if ($id_user>0 OR $is_admin){
                if ($selected=="photo") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."startpage/photo\"><span class=\"ow_ic_photo\">".OW::getLanguage()->text('startpage', 'ta_photo')."</span></a></li>";
//            }
        }

/*
//    if  (SHOPPRO_BOL_Service::getInstance()->check_acces()){


//        if ($id_user>0 OR $is_admin){
        if (($id_user>0 AND OW::getConfig()->getValue('shoppro', 'mode_membercanshell')==1) OR $is_admin){
            if ($selected=="myitems") $sel=" active ";
                else $sel="";
            $content_t .="<li class=\"_store_my_items ".$sel."\"><a href=\"".$curent_url."shopmy/show\"><span class=\"ow_ic_plugin\">".OW::getLanguage()->text('shoppro', 'product_table_showmyitems')."</span></a></li>";//moje zamówienia
        }


//        $content_t .="<li class=\"_plugin \"><a href=\"http://www.oxwall.org/store\"><span class=\"ow_ic_plugin\">Plugins</span></a></li>";
//        $content_t .="<li class=\"_theme \"><a href=\"http://www.oxwall.org/store/themes\"><span class=\"ow_ic_plugin\">Themes</span></a></li>";
//        $content_t .="<li class=\"_store_purchase_list \"><a href=\"http://www.oxwall.org/store/granted-list\"><span class=\"ow_ic_cart\">My purchases</span></a></li>";
//        $content_t .="<li class=\"_store_my_items  active\"><a href=\"http://www.oxwall.org/store/list/my-items\"><span class=\"ow_ic_plugin\">My items</span></a></li>";
//        $content_t .="<li class=\"_store_dev_tools \"><a href=\"http://www.oxwall.org/store/dev-tools\"><span class=\"ow_ic_gear_wheel\">Developer tools</span></a></li>";


//        if (($id_user>0 AND OW::getConfig()->getValue('shoppro', 'mode_membercanshell')==1) OR $is_admin){
//        if ($id_user>0 AND $is_admin){
//        if ($id_user>0 OR $is_admin){
        if (($id_user>0 AND OW::getConfig()->getValue('shoppro', 'mode_membercanshell')==1) OR $is_admin){
            if ($selected==10 OR $selected=="admin") $sel=" active ";
                else $sel="";
            $content_t .="<li class=\"_store_dev_tools ".$sel."\"><a href=\"".$curent_url."ordershop/showorder\"><span class=\"ow_ic_gear_wheel\">".OW::getLanguage()->text('shoppro', 'product_table_showorder')."</span></a></li>";
        }


        if (OW::getConfig()->getValue('shoppro', 'mode_ads_approval') AND $is_admin AND $is_admin){
//            if ($id_user>0 AND $is_admin){
                if ($selected==11 OR $selected=="approval") $sel=" active ";
                    else $sel="";
                $content_t .="<li class=\"_store_dev_tools ".$sel."\"><a href=\"".$curent_url."ordershop/approval\"><span class=\"ow_ic_gear_wheel\">".OW::getLanguage()->text('shoppro', 'product_table_approvedlist')."</span></a></li>";
//            }
        }

                                if ($is_admin OR ($id_user>0 AND OW::getConfig()->getValue('shoppro', 'mode_membercanshell')==1) ){
//                                    $content .="&nbsp;|&nbsp;";
                
            if ($selected=="addnewproduct") $sel=" active ";
                else $sel="";
            $content_t .="<li class=\"_store_dev_tools ".$sel."\"><a href=\"".$curent_url."product/0/add\"><span class=\"ow_ic_add\">".OW::getLanguage()->text('shoppro', 'product_table_selyourproduct')."</span></a></li>";


                                }
//    }//if  (SHOPPRO_BOL_Service::getInstance()->check_acces()){
*/
$content_t .="</ul>";
$content_t .="</div>";
        $content_t .=$content;
        $content_t .="</div>";
        return $content_t;
    }



    public function get_curent_url()
    {
 $pageURL = 'http';
 if (isset($_SERVER["HTTPS"]) AND $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if (isset($_SERVER["SERVER_PORT"]) AND $_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
    }


    public function get_status($idu=0)
    {
//        $ret=OW::getLanguage()->text('startpage', 'default_status_offline');
        $ret="offline";
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        if (!$idu) $idu=$id_user;
        if ($idu>0){
            $sql = "SELECT buo.*,bus.status FROM " . OW_DB_PREFIX. "base_user_startpage buo 
                LEFT JOIN " . OW_DB_PREFIX. "base_user_status bus ON (bus.userId=buo.userId) 
                WHERE buo.userId='".addslashes($idu)."' 
                LIMIT 1";
//echo $sql;
            $arr = OW::getDbo()->queryForList($sql);
            if (isset($arr[0])){
                $value=$arr[0];
//                if (!$value['status']) $status=OW::getLanguage()->text('startpage', 'default_status_startpage');
                if (!$value['status']) $ret="startpage";
                    else $ret=stripslashes($value['status']);
            }else{
//                $ret=OW::getLanguage()->text('startpage', 'default_status_startpage');
//                $ret="offline";
//---------
                $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_user 
                WHERE id='".addslashes($idu)."' 
                LIMIT 1";
                $arr = OW::getDbo()->queryForList($sql);
                if (isset($arr[0])){
                    $value=$arr[0];
                    if ($value['activityStamp']) $ret=$this->what_was_ago($value['activityStamp']);
                        else $ret="offline";
                }else{
                    $ret="offline";
                }
//---------
            }
        }
        return $ret;
    }

    public function get_decoded_status($status="")
    {
//        $ret=OW::getLanguage()->text('startpage', 'default_status_offline');
        if ($status){
            $ret=$status;
        }else{
            $ret=OW::getLanguage()->text('startpage', 'default_status_offline');
        }
        if ($status=="startpage"){
            $ret =OW::getLanguage()->text('startpage', 'default_status_startpage');
        }else if ($status=="absent"){
            $ret =OW::getLanguage()->text('startpage', 'status_startpage_absent');
        }else if ($status=="rightback"){
            $ret =OW::getLanguage()->text('startpage', 'status_startpage_rightback');
        }else if ($status=="notime"){
            $ret=OW::getLanguage()->text('startpage', 'status_startpage_notime');
        }else if ($status=="notdisturb"){
            $ret=OW::getLanguage()->text('startpage', 'status_startpage_notdisturb');
        }else if ($status=="chat"){
            $rret=OW::getLanguage()->text('startpage', 'status_startpage_chat');
        }
        return $ret;
    }
/*
    public function ago($tm,$rcs = 0) {
       $cur_tm = time(); $dif = $cur_tm-$tm;
       $pds = array('second','minute','hour','day','week','month','year','decade');
       $lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
       for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);

       $no = floor($no); if($no <> 1) $pds[$v] .='s'; $x=sprintf("%d %s ",$no,$pds[$v]);
       if(($rcs == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= time_ago($_tm);
       return $x;
    }
*/
    public function what_was_ago($time)
    {
       $periods = array(OW::getLanguage()->text('startpage', 'status_ago_second'), OW::getLanguage()->text('startpage', 'status_ago_minute'), OW::getLanguage()->text('startpage', 'status_ago_hour'), OW::getLanguage()->text('startpage', 'status_ago_day'), OW::getLanguage()->text('startpage', 'status_ago_week'), OW::getLanguage()->text('startpage', 'status_ago_month'), OW::getLanguage()->text('startpage', 'status_ago_year'), OW::getLanguage()->text('startpage', 'status_ago_decade'));
       $periods_s = array(OW::getLanguage()->text('startpage', 'status_ago_seconds'), OW::getLanguage()->text('startpage', 'status_ago_minuts'), OW::getLanguage()->text('startpage', 'status_ago_hours'), OW::getLanguage()->text('startpage', 'status_ago_days'), OW::getLanguage()->text('startpage', 'status_ago_weeks'), OW::getLanguage()->text('startpage', 'status_ago_months'), OW::getLanguage()->text('startpage', 'status_ago_years'), OW::getLanguage()->text('startpage', 'status_ago_decades'));
       $lengths = array("60","60","24","7","4.35","12","10");
       $now = time();
       $difference     = $now - $time;
       $tense         = OW::getLanguage()->text('startpage', 'status_ago');
       for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
           $difference /= $lengths[$j];
       }

       $difference = round($difference);

       if($difference != 1) {
//           $periods[$j].= OW::getLanguage()->text('startpage', 'status_ago_many_s');;
            $periods_show=$periods_s[$j];
       }else{
            $periods_show=$periods[$j];
       }
       return $difference." ".$periods_show." ".$tense;
    }

    public function get_startpage($type="full",$curent_member_profile=0,$page=1)
    {
//return;
//echo "dfdf";exit;
        $per_page=OW::getConfig()->getValue('startpage', 'show_startpage_maxitems');
        if (!$per_page) $per_page=0;


        $page=$page-1;
        if ($page<0) $page=0;
        $start_page=($page*$per_page);

        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();
        $curent_url=OW_URL_HOME;
        $content="";

        if (!$curent_member_profile OR $curent_member_profile=="")  $curent_member_profile=$id_user;
        


//echo "--".$curent_member_profile."======".BOL_UserService::getInstance()->getDisplayName(1);exit;$curent_member_profile
//print_r($_SESSION);
//    if ($id_user>0){
    if ($curent_member_profile>0 AND OW::getConfig()->getValue('startpage', 'show_profile_inwidget_startpage')==1){

            $dname=BOL_UserService::getInstance()->getDisplayName($curent_member_profile);
            $uurl=BOL_UserService::getInstance()->getUserUrl($curent_member_profile);
            $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($curent_member_profile);

                    $contentt="";
                    if ($uimg){
                        $contentt .="<a href=\"".$uurl."\">";
                        $contentt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"max-width: 100%;\" align=\"left\" >";
                        $contentt .="</a>";
                    }else{
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
                        $contentt .="<a href=\"".$uurl."\"  >";
                        $contentt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" width=\"45px\" style=\"max-width: 100%;\" align=\"left\" >";
                        $contentt .="</a>";
                    }
$mystatus=$this->get_status($curent_member_profile);


$content .="<div class=\"ow_box ow_stdmargin clearfix index-BASE_CMP_MyAvatarWidget ow_break_word\" style=\"margin-top:-10px;\">
                <div class=\"ow_my_avatar_widget clearfix\" id=\"aron_my_avatar_widget\">
                    <div class=\"ow_left ow_my_avatar_img\">
                        <div class=\"ow_avatar\">
                        ".$contentt."
                        </div>
                    </div>
                    <div class=\"ow_my_avatar_cont\" style=\"height:22px;text-align:left;\">
                        <a href=\"".$uurl."\" class=\"ow_my_avatar_username\"><span style=\"\">".$dname."</span></a>

                    </div>
    <div class=\"ow_small ow_remark ow_ipc_dateX ow_left\" style=\"margin-left:5px;max-height: 14px;overflow: hidden;white-space: nowrap;\">".$this->get_decoded_status($mystatus)."</div>
                </div>";

//----stat start
//$mystatus=$this->get_status();//move up

if ($curent_member_profile>0 AND $id_user>0 AND $curent_member_profile==$id_user){
    $content .="<div class=\"ow_center ow_remark clearfix\">";
    $content .="<div class=\"ow_box_toolbar_cont clearfix ow_left\">";

//$content .="<div class=\"ow_left\">".$this->get_decoded_status($mystatus)."</div>";

    $content .="<div class=\"ow_box_toolbar ow_remark\">
        <span style=\"\" class=\"ow_nowrap\">
        <a href=\"javascript:void(0);\" id=\"startpage_pop\" >".OW::getLanguage()->text('startpage', 'change_self_status')."</a>
        </span>
        </div>
        </div>
    </div>";




//$burl=STARTPAGE_BOL_Service::getInstance()->get_curent_url();
$burl=$this->get_curent_url();
$burl=base64_encode($burl);
$content .="<form id=\"startpage_overlay_form\" style=\"display:none\" method=\"POST\" action=\"".$curent_url."startpage/savestatus\">
<input type=\"hidden\" name=\"ss\" value=\"".substr(session_id(),2,3)."\">
<input type=\"hidden\" name=\"burl\" value=\"".$burl."\">
<h2>".OW::getLanguage()->text('startpage', 'change_self_status')."</h2>
<br/> 
<select name=\"fstatus\">";

//echo "---".$mystatus;
if (!$mystatus OR $mystatus=="startpage") $sel=" SELECTED ";
    else $sel="";
$content .="<option ".$sel." value=\"startpage\">".OW::getLanguage()->text('startpage', 'default_status_startpage')."</option>";
if ($mystatus=="absent") $sel=" SELECTED ";
    else $sel="";
$content .="<option ".$sel." value=\"absent\">".OW::getLanguage()->text('startpage', 'status_startpage_absent')."</option>";
if ($mystatus=="rightback") $sel=" SELECTED ";
    else $sel="";
$content .="<option ".$sel." value=\"rightback\">".OW::getLanguage()->text('startpage', 'status_startpage_rightback')."</option>";
if ($mystatus=="notime") $sel=" SELECTED ";
    else $sel="";
$content .="<option ".$sel." value=\"notime\">".OW::getLanguage()->text('startpage', 'status_startpage_notime')."</option>";
if ($mystatus=="notdisturb") $sel=" SELECTED ";
    else $sel="";
$content .="<option ".$sel." value=\"notdisturb\">".OW::getLanguage()->text('startpage', 'status_startpage_notdisturb')."</option>";
if ($mystatus=="chat") $sel=" SELECTED ";
    else $sel="";
$content .="<option ".$sel." value=\"chat\">".OW::getLanguage()->text('startpage', 'status_startpage_chat')."</option>";
$content .="</select>";
$content .="<br/>&nbsp;<br/>
                    <span class=\"ow_button\">
                        <span class=\" ow_positive\">
                            <input type=\"submit\" id=\"save_status\" name=\"Save_status\" value=\"".OW::getLanguage()->text('startpage', 'save_status')."\" title=\"".OW::getLanguage()->text('startpage', 'save_status')."\" class=\"ow_ic_save ow_positive\" style=\"\">
                        </span>
                    </span>

                    <span class=\"ow_button\">
                        <span class=\" ow_positive\">
                            <input type=\"button\" id=\"startpage_close\" name=\"Close_status\" value=\"&nbsp;\" title=\"".OW::getLanguage()->text('startpage', 'close')."\" class=\"ow_ic_restrict ow_positive\" style=\"padding-left: 0;padding-right: 27px; background-position: 50% 50%;\">
                        </span>
                    </span>




</form>";

}//if ($curent_member_profile>0 AND $id_user>0 AND $curent_member_profile==$id_user){
//----stat end


    $content .="<div class=\"ow_box_bottom_left\"></div>
    <div class=\"ow_box_bottom_right\"></div>
    <div class=\"ow_box_bottom_body\"></div>
    <div class=\"ow_box_bottom_shadow\"></div>
</div>";



        }//if user:$curent_member_profile


if ($type=="full"){

//$content .="<div class=\"clearfix ow_box ow_stdmargin clearfix ow_break_word\" style=\"margin-bottom:10px;   background: transparent;\">";
if (OW::getConfig()->getValue('startpage', 'show_startpage_only_friends')==1 AND OW::getPluginManager()->isPluginActive('friends')){//-----------------------------------for friends only
    $content .="<div class=\"clearfix\">";
    $content .="<div class=\"ow_small ow_remark ow_ipc_dateX ow_left\">";
    $content .=OW::getLanguage()->text('startpage', 'friends_startpage');
    $content .="</div>";
    $content .="</div>";
}

$content .="<div class=\"ow_box ow_stdmargin clearfix index-BASE_CMP_AddNewContent ow_break_word\" style=\"margin-bottom:10px;   background: transparent; padding: 0;margin: 2px;\">";




    $add="";
    if ($id_user){
        $add=" WHERE buo.userId<>'".addslashes($id_user)."' ";
    }
    $show_type="all";


    if (OW::getConfig()->getValue('startpage', 'show_startpage_only_friends')==1 AND OW::getPluginManager()->isPluginActive('friends')){//-----------------------------------for friends only
            $show_type="friends";
/*
        $sql = "SELECT buo.*,bus.status FROM " . OW_DB_PREFIX. "base_user_startpage buo 
    LEFT JOIN " . OW_DB_PREFIX. "base_user_status bus ON (bus.userId=buo.userId) 
        ".$add." 
            ORDER BY buo.activityStamp DESC 
            LIMIT ".$start_page.",".$per_page;
*/
//        $sql="SELECT * FROM " . OW_DB_PREFIX. "friends_friendship ff 
//    LEFT JOIN " . OW_DB_PREFIX. "base_user_startpage buo ON (buo. =ff.userId AND ff.status='active')
//        LIMIT ".$start_page.",".$per_page;

    $status="status";
    $user_id="userId";
    $friend_id="friendId";
    $startus1=" fr." . $status . " = 'active' AND ";
    $startus2=" fr." . $status . " = 'active' AND ";



             $sql = "(SELECT fr.id, fr.userId, fr.friendId, fr.status as fr_status, buo.userId as startpage_userId, bus.status,buo.activityStamp 
                    FROM " . OW_DB_PREFIX . "friends_friendship fr
                LEFT JOIN " . BOL_UserSuspendDao::getInstance()->getTableName() . " ussx ON ( fr." .$user_id . " = ussx.userId ) 
LEFT JOIN " . OW_DB_PREFIX. "base_user_startpage buo ON (buo.userId =fr." .$user_id . ") 
LEFT JOIN " . OW_DB_PREFIX. "base_user_status bus ON (bus.userId=fr." .$user_id . ") 
                WHERE ".$startus1." ussx.userId IS NULL AND fr." . $friend_id . " = '".$id_user."' AND buo.userId>'0' 
                 )
                UNION
                ( SELECT fr.id, fr.userId, fr.friendId, fr.status as fr_status, buo.userId as startpage_userId, bus.status,buo.activityStamp 
                    FROM " . OW_DB_PREFIX . "friends_friendship fr
                LEFT JOIN " . BOL_UserSuspendDao::getInstance()->getTableName() . " ussx ON ( fr." . $friend_id . " = ussx.userId )
LEFT JOIN " . OW_DB_PREFIX. "base_user_startpage buo ON (buo.userId =fr." . $friend_id . ") 
LEFT JOIN " . OW_DB_PREFIX. "base_user_status bus ON (bus.userId=fr." . $friend_id . ") 
                WHERE ".$startus2." ussx.userId IS NULL AND fr." . $user_id . " = '".$id_user ."' AND buo.userId>'0'
            ) LIMIT ".$start_page.",".$per_page;
//echo $sql;exit;
    }else if (OW::getPluginManager()->isPluginActive('friends')){//-----------------------------------for friends offline 
            $show_type="friends";

    $status="status";
    $user_id="userId";
    $friend_id="friendId";
    $startus1=" fr." . $status . " = 'active' AND ";
    $startus2=" fr." . $status . " = 'active' AND ";

             $sql = "(SELECT fr.id, fr.userId, fr.friendId, fr.status as fr_status, buo.userId as startpage_userId, bus.status,buo.activityStamp 
                    FROM " . OW_DB_PREFIX . "friends_friendship fr 
                LEFT JOIN " . BOL_UserSuspendDao::getInstance()->getTableName() . " ussx ON ( fr." .$user_id . " = ussx.userId ) 
LEFT JOIN " . OW_DB_PREFIX. "base_user_startpage buo ON (buo.userId =fr." .$user_id . ") 
LEFT JOIN " . OW_DB_PREFIX. "base_user_status bus ON (bus.userId=fr." .$user_id . ") 
                WHERE ".$startus1." ussx.userId IS NULL AND fr." . $friend_id . " = '".$id_user."' AND !buo.userId 
                 )
                UNION
                ( SELECT fr.id, fr.userId, fr.friendId, fr.status as fr_status, buo.userId as startpage_userId, bus.status,buo.activityStamp 
                    FROM " . OW_DB_PREFIX . "friends_friendship fr 
                LEFT JOIN " . BOL_UserSuspendDao::getInstance()->getTableName() . " ussx ON ( fr." . $friend_id . " = ussx.userId )
LEFT JOIN " . OW_DB_PREFIX. "base_user_startpage buo ON (buo.userId =fr." . $friend_id . ") 
LEFT JOIN " . OW_DB_PREFIX. "base_user_status bus ON (bus.userId=fr." . $friend_id . ") 
                WHERE ".$startus2." ussx.userId IS NULL AND fr." . $user_id . " = '".$id_user ."' AND !buo.userId
            ) LIMIT ".$start_page.",".$per_page;
//echo $sql;exit;

    }else{//---------------------------------------------------------------------------------------------for all members start
        $sql = "SELECT buo.*,bus.status FROM " . OW_DB_PREFIX. "base_user_startpage buo 
    LEFT JOIN " . OW_DB_PREFIX. "base_user_status bus ON (bus.userId=buo.userId) 
        ".$add." 
            ORDER BY buo.activityStamp DESC 
            LIMIT ".$start_page.",".$per_page;
    }
//echo $sql;
//exit;
if (OW::getConfig()->getValue('startpage', 'show_small_startpage_list')=="1"){
    $is_small_list=true;
    $img_w=20;
    $img_h=20;

}else{
    $is_small_list=false;
    $img_w=40;
    $img_h=40;

}

        $arr = OW::getDbo()->queryForList($sql);
        foreach ( $arr as $value )
        {
            if ($show_type=="all"){
                $value_userId=$value['userId'];
            }else{//-----friends
                $value_userId=$value['startpage_userId'];
            }

            $dname=BOL_UserService::getInstance()->getDisplayName($value_userId);
            $uurl=BOL_UserService::getInstance()->getUserUrl($value_userId);
            $uimg=BOL_AvatarService::getInstance()->getAvatarUrl($value_userId);
            $fromdate=date("Y-m-d H:i:s",$value['activityStamp']);
            if (!$value['status']) $status="startpage";
                else $status=stripslashes($value['status']);
//echo "---".$status."<hr>";
            $contentt="";
                    if ($uimg){
//                        $contentt .="<a href=\"".$uurl."\">";
//                        $contentt .="<img src=\"".$uimg."\" alt=\"".$dname."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;\" >";
//                        $contentt .="</a>";
                    }else{
                        $uimg=$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png";
//                        $tabt .="<i>".OW::getLanguage()->text('search', 'index_hasnotimage')."</i>";
//                        $contentt .="<a href=\"".$uurl."\"  >";
//                        $contentt .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".$dname."\" style=\"border:0;margin:10px;\">";
//                        $contentt .="</a>";
                    }
//$content .="<div class=\"ow_box clearfix ow_left\" style=\"margin: 5px;".$border_im.$background_im."\">";
//$content .="<div class=\"ow_box clearfix ow_left\" style=\"margin: 3px;\">";

//$content .="<div class=\"ow_box clearfix ow_left\" style=\"margin: 1px;\">";
if ($is_small_list==false){
    $content .="<div class=\"ow_add_content ow_box clearfix ow_left\" style=\"margin: 1px;    background-image:none;padding: 5px 0 5px; min-width: 160px;width:100%\">";
}else{
    $content .="<div class=\"ow_add_content ow_box clearfix ow_left\" style=\"margin: 1px;    background-image:none;padding: 3px 0 1px; min-width: 160px;width:100%\">";
}
//$content .="<div class=\"ow_box clearfix\" style=\"margin: 5px;\">";

//                            $content .="<li >".stripslashes($row2['title'])."</li>";
//$content .="<li >";

//$content .="<div class=\"ow_my_avatar_widget clearfix\" style=\"margin:2px;margin-bottom:10px;text-align:left;\">";
//$content .="<div class=\"ow_my_avatar_widget clearfix\" style=\"margin:1px;margin-bottom:10px;text-align:left;\">";
$content .="<div class=\" ow_my_avatar_widget clearfix\" style=\"margin:1px;text-align:left;\">";


                $content .="<div class=\" ow_left ow_my_avatar_img\" style=\"max-height:".$img_h."px;max-width:".$img_w."px;margin-left: 4px;margin-top: -1px;margin-bottom:3px;\">
                    <div class=\"ow_avatar\" style=\"max-width:".$img_w."px;max-height:".$img_h."px;\">
                        <a href=\"".$uurl."\"><img alt=\"\" title=\"".$dname."\" src=\"".$uimg."\" style=\"max-height:".$img_h."px;max-width:".$img_w."px;\"></a>
                    </div>
                </div>

    <div class=\"ow_my_avatar_cont\" style=\"margin-left:10px;max-height:".$img_h."px;height:auto;\">";

if ($is_small_list==false){
        $content .="<a href=\"".$uurl."\" class=\"ow_my_avatar_username\" style=\"margin-left:1px;display: inline;\">
        <span class=\"ow_small ow_remark ow_ipc_dateX ow_left\" style=\"font-size:11px;
overflow: hidden;
height: 18px;
max-height: 18px;
max-width: 100px;
white-space: nowrap;
width: 100px;
top: 5px;
position: relative;
z-index: 5;
margin-left:10px;
\">@".$dname."</span></a>";

/*
$content .="<br/>";
        $content .="<span class=\"ow_comments_date ow_nowrap ow_tiny ow_remark\" style=\"overflow: hidden;
height: 18px;
max-height: 18px;
max-width: 140px;
white-space: nowrap;\">".$fromdate."</span>";
*/
//$content .="<br/>";
        $content .="<span class=\"ow_remark ow_right\" style=\"overflow: hidden;
height: 18px;
max-height: 18px;
max-width: 120px;
width: auto;
text-align:right;
margin-right:5px;
white-space: nowrap;
top: 5px;
position: relative;
z-index: 5;
font-weight:bold;
margin-left:10px;
\">".$this->get_decoded_status($status)."</span>";
}else{
        $content .="<span class=\"ow_remark ow_right\" style=\"overflow: hidden;
height: 20px;
max-height: 20px;
max-width: 160px;
width: auto;
text-align:right;
margin-right:5px;
white-space: nowrap;
position: relative;
z-index: 5;
font-weight:bold;
margin-left:3px;
\">".$dname.": ".$this->get_decoded_status($status)."</span>";
}
//$content .="<br/>";


    $content .="</div>";



$content .="</div>";



$content .="</div>";


        }//for 




$content .="</div>";



$content .="<div class=\"ow_box_toolbar_cont clearfix\">
<div class=\"ow_box_toolbar ow_remark\">
        <span style=\"\" class=\"ow_nowrap\">
        <a href=\"".$curent_url."users/startpage\">".OW::getLanguage()->text('base', 'more')."</a>
        </span>
</div>
    </div>";



    }//if $type=="full"




        return $content;
    }


    public function check_user_activity_log(){//on normal mode
//return;
//file_put_contents("ow_plugins/startpage/xxxxxxx.LOGS", "\n\n------check_user_activity_log------:\n".print_r($_POST,1)."\n---------------\n".print_r($_GET,1),FILE_APPEND);
//echo "sfsdF";exit;
        $curent_url=OW_URL_HOME;
        $id_user = OW::getUser()->getId();//citent login user (uwner)
        if ($id_user) return;
        if (isset($_SESSION['was_cover_first_tile']) AND $_SESSION['was_cover_first_tile']==true) return;
        if (OW::getConfig()->getValue('startpage', 'disable_startpage')!="2") return;
//---s st

        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) AND $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $tmp=str_replace(OW_URL_HOME,"",$pageURL);
        if (substr($tmp,0,1)=="/") $tmp=substr($tmp,1);
//http://facejuggle.com/str/sign-in
//sign-in
//http://test.a6.pl/sign-in
//echo $_SERVER["REQUEST_URI"];
//echo $tmp;exit;
        $v1=strtolower(substr($tmp,0,8));
        $v1=str_replace("/","",$v1);
        $v1=str_replace("\\","",$v1);
        $v1=str_replace("?","",$v1);
        $v2=strtolower(substr($tmp,0,7));
        $v2=str_replace("/","",$v2);
        $v2=str_replace("\\","",$v2);
        $v2=str_replace("?","",$v2);

        $testx=$tmp;
        $testx_tmp=explode("?",$testx);
        if (isset($testx_tmp[0])) $testx=$testx_tmp[0];

//echo $v1."--".$v2."--".$testx;exit;
//print_r($_POST);exit;
////sign-in--sign-inArray ( [form_name] => std-sign-in [identity] => aron [password] => Aronx1403 [submit] => Sign In )
        if (isset($_POST['form_name']) AND $_POST['form_name']=="sign-in"){
            return;
        }else if ($v1=="sign-in" OR $v2=="sign-in" AND !isset($_POST['form_name'])) {
            return;
        }else if ($testx=="mobille/" OR $testx=="/mobille" OR $testx=="mobile/" OR $testx=="/mobile" OR strpos($testx,"mobille")!==false OR strpos($testx,"mobile")!==false){
            return;
        }else if ($testx=="terms-of-use" OR $testx=="terms-of-use/"  OR $testx=="/terms-of-use" OR strpos($testx,"terms-of-use")!==false ){
            return;
        }else if ($testx=="privacy-policy" OR $testx=="privacy-policy/"  OR $testx=="/privacy-policy" OR strpos($testx,"privacy-policy")!==false ){
            return;
        }else if ($testx=="rss" OR $testx=="rss/" OR $testx=="/rss" OR strpos($testx,"rsss.php")!==false OR strpos($testx,"rss/")!==false){
            return;
        }else if ($testx=="facebook-connect" OR $testx=="/facebook-connect" OR $testx=="fbconnect_channel.html" OR $testx=="/fbconnect_channel.html" OR strpos($testx,"facebook-connect")!==false  OR strpos($testx,"fbconnect_channel.html")!==false ){
            return;
        }else if ($testx=="base/base-document/maintenance" OR $testx=="/base/base-document/maintenance" OR $testx=="base/base-document/maintenance" OR strpos($testx,"base/base-document/maintenance")!==false ){
            return;
        }else if ($testx=="base/captcha" OR $testx=="/base/captcha" OR $testx=="base/captcha/" OR strpos($testx,"base/captcha")!==false ){
            return;
        }else if (OW::getConfig()->getValue('base', 'maintenance')==1){
            return;
        }else if ($testx=="contact/" OR $testx=="/contact" OR $testx=="contact/user/" OR $testx=="/contact/user" OR strpos($testx,"contact/user/")!==false ){
            return;
        }else if ($testx=="reset-password" OR $testx=="reset-password/"  OR $testx=="/reset-password" OR strpos($testx,"reset-password")!==false ){        
            return;
        }else if ($testx=="forgot-password" OR $testx=="forgot-password/"  OR $testx=="/forgot-password" OR strpos($testx,"forgot-password")!==false ){        
            return;
        }

if (isset($_SERVER['REQUEST_URI'])){
    $test1=$_SERVER['REQUEST_URI'];
}else{
    $test1="";
}

//if (strpos($test1,"language_id=")===true) return;//language

$test1_tmp=explode("?",$test1);
if (isset($test1_tmp[0])) $test1=$test1_tmp[0];
//echo "--".strpos($_SERVER['REQUEST_URI'],"");exit;
if ($test1=="mobille/" OR $test1=="/mobille" OR $test1=="mobile/" OR $test1=="/mobile" OR strpos($_SERVER['REQUEST_URI'],"mobille")!==false){
    return;
}else if ($test1=="rss" OR $test1=="/rss" OR strpos($_SERVER['REQUEST_URI'],"rsss.php")!==false ){
    return;
}else if (strpos($_SERVER['REQUEST_URI'],"startpage/checkf")!==false ){
    return;
}
//---s en

$script ="";
//$script .= '<script type="text/javascript">';
    //$('#console_item_51a239a755e59').click(function(){new OW_FloatBox({ $contents: $('#base_cmp_floatbox_ajax_signin')});});

$bbjoin="<div class=\"clearfix\" style=\"margin-left: 20px;\"><div class=\"ow_center\">
<a href=\"".$curent_url."join\"><span class=\"ow_button\"><span class=\" ow_positive\"><input type=\"button\" value=\"".OW::getLanguage()->text('base', 'base_join_menu_item')."\" id=\"input_56314412xx\" class=\"ow_positive\" name=\"submit\"></span></a></span>
</div></div>";
$bbjoin=str_replace("'","",$bbjoin);
$bbjoin=str_replace("\r\n","",$bbjoin);
$bbjoin=str_replace("\r","",$bbjoin);
$bbjoin=str_replace("\n","",$bbjoin);

$gbtext="Connect with Facebook";
$gbtext=str_replace("'","",$gbtext);
$gbtext=str_replace("\r\n","",$gbtext);
$gbtext=str_replace("\r","",$gbtext);
$gbtext=str_replace("\n","",$gbtext);

if (OW::getConfig()->getValue('startpage', 'widgetjavacode')){
$fbwid=OW::getConfig()->getValue('startpage', 'widgetjavacode');
//$fbwid="
//<iframe src=\"//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2FMyCollegeSocialcom&width=292&height=290&show_faces=true&colorscheme=light&stream=false&show_border=true&header=true&appId=245817625491300\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:292px; height:290px;\" allowtransparency=\"true\"></iframe>
//";
$fbwid=str_replace("'","",$fbwid);
$fbwid=str_replace("\r\n","",$fbwid);
$fbwid=str_replace("\r","",$fbwid);
$fbwid=str_replace("\n","",$fbwid);
}else{
    $fbwid="";
}
/*
$closetext="<div style=\"display: inline-block;position: relative;float: right;right: 30px;\">".OW::getLanguage()->text('startpage', 'skip')."</div>";
$closetext=str_replace("'","",$closetext);
$closetext=str_replace("\r\n","",$closetext);
$closetext=str_replace("\r","",$closetext);
$closetext=str_replace("\n","",$closetext);
*/

$ortxt="<div style=\"display: block;text-align: left;width: 100%;min-width: 250px;\" class=\"ow_center\">".OW::getLanguage()->text('admin', 'or')."</div>";
$ortxt=str_replace("'","",$ortxt);
$ortxt=str_replace("\r\n","",$ortxt);
$ortxt=str_replace("\r","",$ortxt);
$ortxt=str_replace("\n","",$ortxt);

$ident="Login";
$ident=str_replace("'","",$ident);
$ident=str_replace("\r\n","",$ident);
$ident=str_replace("\r","",$ident);
$ident=str_replace("\n","",$ident);

//$titlex="Welcome to MyCollegeSocial.com";
$titlex=OW::getConfig()->getValue('startpage', 'toptitle');
$titlex=str_replace("'","",$titlex);
$titlex=str_replace("\r\n","",$titlex);
$titlex=str_replace("\r","",$titlex);
$titlex=str_replace("\n","",$titlex);

$script .= "
new OW_FloatBox({ \$contents: \$('#base_cmp_floatbox_ajax_signin')});
//(function(_scope) {
//        new OW_FloatBox({\$contents:\$('#base_cmp_floatbox_ajax_signin')});
//})(window);

$('div .connect_button_cont span.fb_button_text').html('".$gbtext."');

$('div.ow_connect_buttons').before('".$bbjoin."');

$('#input_56314412xx').click(function() {
    window.location.href='".$curent_url."join';
});

";
if ($fbwid){
$script .= "
$('div.ow_sign_up').html('".$fbwid."');
";
}
/*
$script .= "
$('a.close').html('".$closetext."');
$('input.identity').val('".$ident."');
";
*/
$script .= "
$('div.ow_sign_in span.ow_connect_text').html('".$ortxt."');
$('div.ow_sign_in div.ow_connect_buttons div.connect_button_list').css('float','none');
$('div.ow_sign_in div.ow_connect_buttons div.connect_button_list').css('text-align','center');
";


if ($titlex){
$script .= "
$('#base_cmp_floatbox_ajax_signin div.ow_sign_in_wrap>h2').css('display','block');
$('#base_cmp_floatbox_ajax_signin div.ow_sign_in_wrap>h2').html('".$titlex."');
";
}else{
$script .= "
$('#base_cmp_floatbox_ajax_signin div.ow_sign_in_wrap>h2').css('display','none');
";
}

//$script .= "</script>";
//if (!OW::getUser()->getId()){
 OW::getDocument()->addOnloadScript($script);      

        $_SESSION['was_cover_first_tile']=true;

    }
//----'-------

    public function check_user_activity(){//for guest restrict mode - hard mode

//file_put_contents("ow_plugins/startpage/xxxxxxx.LOGS", "\n\n------check_user_activity------:\n".print_r($_POST,1)."\n---------------\n".print_r($_GET,1),FILE_APPEND);

//echo "afadf";exit;

if (OW::getConfig()->getValue('startpage', 'disable_force_imagechache')=="1"){
    $firce_disable_image_cache=true;
}else{
    $firce_disable_image_cache=false;
}

        $id_user = OW::getUser()->getId();//citent login user (uwner)
        $is_user = OW::getUser()->getId();//citent login user (uwner)
        $is_admin = OW::getUser()->isAdmin();//iss admin
        $curent_url=OW_URL_HOME;
//echo OW::getConfig()->getValue('startpage', 'disable_startpage');
        if (OW::getConfig()->getValue('startpage', 'disable_startpage')!="0") return;
//echo "sss";exit;
//        if (OW::getConfig()->getValue('startpage', 'disable_startpage')=="2") return;

//return;


/*
//=======================
echo "START<br>";
                        $i=rand(88888,99999999);
                        $_POST['acctype']="asdasd".$i;
                        $_POST['uname']="asdasd".$i;
                        $_POST['email']="asdasdasdasd".$i."@asdas.com";
                        $_POST['pass']="afsdfwdfsdfsd";
                        $_POST['pass2']="afsdfwdfsdfsd";
                        $_POST['ss']=substr(session_id(),3,5);
echo $this->register();
echo "<br>END";
exit;
//======================
*/



//============v1

//---s st

if (strpos($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"],"ow_cron")===true){
    return;
}

if (OW::getRequest()->isPost()){
    return;
}



if ( OW::getPluginManager()->isPluginActive('mobille')){
    if (OW::getConfig()->getValue('mobille', 'disable_detect_mobile')!="1"){
        if (MOBILLE_BOL_Service::getInstance()->get_mobile_status()){
            return;
        }
    }
}


//print_r($_SESSION);
//print_r($_POST);
//print_r($_GET);
//exit;
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) AND $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $tmp=str_replace(OW_URL_HOME,"",$pageURL);
        if (substr($tmp,0,1)=="/") $tmp=substr($tmp,1);
/*
if ($tmp=="sign-in?"){
    return;
}
if ($id_user){
    return;
}
*/
//http://facejuggle.com/str/sign-in
//sign-in
//http://test.a6.pl/sign-in
//echo $_SERVER["REQUEST_URI"];
//echo $tmp;exit;
        $v1=strtolower(substr($tmp,0,8));
        $v1=str_replace("/","",$v1);
        $v1=str_replace("\\","",$v1);
        $testx_tmp=explode("?",$v1);
        if (isset($testx_tmp[0])) $v1=$testx_tmp[0];
        $v1=str_replace("?","",$v1);

        $v2=strtolower(substr($tmp,0,7));
        $v2=str_replace("/","",$v2);
        $v2=str_replace("\\","",$v2);

        $testx_tmp=explode("?",$v2);
        if (isset($testx_tmp[0])) $v2=$testx_tmp[0];
        $v2=str_replace("?","",$v2);

        $testx=$tmp;
        $testx_tmp=explode("?",$testx);
        if (isset($testx_tmp[0])) $testx=$testx_tmp[0];

//echo $v1."--".$v2;
//print_r($_POST);exit;
//print_r($_GET);exit;
////sign-in--sign-inArray ( [form_name] => std-sign-in [identity] => aron [password] => Aronx1403 [submit] => Sign In )
        if (isset($_POST['form_name']) AND $_POST['form_name']=="sign-in"){
            return;
        }else if ($v1=="sign-in" OR $v2=="sign-in" AND !isset($_POST['form_name'])) {
            return;
        }else if ($testx=="mobille/" OR $testx=="/mobille" OR $testx=="mobile/" OR $testx=="/mobile" OR strpos($testx,"mobille")!==false OR strpos($testx,"mobile")!==false){
            return;
        }else if ($testx=="adsense/" OR $testx=="/adsense" OR strpos($testx,"adsense/show")!==false){
            return;
        }else if ($testx=="terms-of-use" OR $testx=="terms-of-use/"  OR $testx=="/terms-of-use" OR strpos($testx,"terms-of-use")!==false ){
            return;
        }else if ($testx=="privacy-policy" OR $testx=="privacy-policy/"  OR $testx=="/privacy-policy" OR strpos($testx,"privacy-policy")!==false ){
            return;
        }else if ($testx=="rss" OR $testx=="rss/"  OR $testx=="/rss" OR strpos($testx,"rsss.php")!==false OR strpos($testx,"rss/")!==false){
            return;
        }else if ($testx=="facebook-connect" OR $testx=="/facebook-connect" OR $testx=="fbconnect_channel.html" OR $testx=="/fbconnect_channel.html" OR strpos($testx,"facebook-connect")!==false OR strpos($testx,"fbconnect_channel.html")!==false ){
            return;
        }else if ($testx=="base/base-document/maintenance" OR $testx=="/base/base-document/maintenance" OR $testx=="base/base-document/maintenance" OR strpos($testx,"base/base-document/maintenance")!==false ){
            return;
        }else if ($testx=="base/captcha" OR $testx=="/base/captcha" OR $testx=="base/captcha/" OR strpos($testx,"base/captcha")!==false ){
            return;
        }else if (OW::getConfig()->getValue('base', 'maintenance')==1){
            return;
        }else if ($testx=="contact/" OR $testx=="/contact" OR $testx=="contact/user/" OR $testx=="/contact/user" OR strpos($testx,"contact/user/")!==false ){
            return;
        }else if ($testx=="reset-password" OR $testx=="reset-password/"  OR $testx=="/reset-password" OR strpos($testx,"reset-password")!==false ){        
            return;
        }else if ($testx=="forgot-password" OR $testx=="forgot-password/"  OR $testx=="/forgot-password" OR strpos($testx,"forgot-password")!==false ){        
            return;
        }

//---s en
// AND ($id_user>0 OR !$testx OR $testx=="index" OR strpos($testx,"index/")!==false)) {
//echo $testx;exit;
//return;
//        if (!OW::getConfig()->getValue('startpage', 'force_for_guest') AND ($testx!="" OR $testx=="index" OR $testx=="index/")) {
        if (!OW::getConfig()->getValue('startpage', 'force_for_guest') AND ($testx!="" AND $testx!="startx") AND !$id_user ) {
//        if (!OW::getConfig()->getValue('startpage', 'force_for_guest') AND ($testx!="") AND !$id_user) {
            return;
        }

//============v2


//print_r($_SESSION);exit;
//print_r(OW::getSession());exit;


if (isset($_SERVER['REQUEST_URI'])){
    $test1=$_SERVER['REQUEST_URI'];
}else{
    $test1="";
}

//if (strpos($test1,"language_id=")===true) return;//language
//echo "fsdfSDF";exit;
//echo $test1;exit;

$test1_tmp=explode("?",$test1);
if (isset($test1_tmp[0])) $test1=$test1_tmp[0];
//echo "--".strpos($_SERVER['REQUEST_URI'],"");exit;
if ($test1=="mobille/" OR $test1=="/mobille" OR $test1=="mobile/" OR $test1=="/mobile" OR strpos($_SERVER['REQUEST_URI'],"mobille")!==false){
    return;
}else if ($test1=="rss" OR $test1=="rss/"  OR $test1=="/rss" OR strpos($test1,"rsss.php")!==false OR strpos($test1,"rss/")!==false){
    return;
}else if ($test1=="paypal_ipn" OR $test1=="/paypal_ipn" OR strpos($_SERVER['REQUEST_URI'],"paypal_ipn")!==false){
    return;
}else if ($test1=="shopipn" OR $test1=="/shopipn" OR $test1=="shopipn/" OR $test1=="shopipn/back" OR $test1=="/shopipn/back" OR $test1=="/shopipn/back/" OR $test1=="shopipn/back/" OR strpos($_SERVER['REQUEST_URI'],"shopipn")!==false){
    return;
}else if ($test1=="cartipn" OR $test1=="/cartipn" OR strpos($_SERVER['REQUEST_URI'],"cartipn")!==false){
    return;
}else if ($test1=="paypal_cancel" OR $test1=="/paypal_cancel" OR strpos($_SERVER['REQUEST_URI'],"paypal_cancel")!==false){
    return;
}else if ($test1=="paypal_complete" OR $test1=="/paypal_complete" OR strpos($_SERVER['REQUEST_URI'],"paypal_complete")!==false){
    return;
}else if ($test1=="facebook-connect" OR $test1=="/facebook-connect" OR $test1=="/facebook-connect/login" OR $test1=="facebook-connect/login" OR strpos($_SERVER['REQUEST_URI'],"facebook-connect/login")!==false){
    return;
}else if ($test1=="base/base-document/maintenance" OR $test1=="/base/base-document/maintenance" OR $test1=="base/base-document/maintenance" OR strpos($_SERVER['REQUEST_URI'],"base/base-document/maintenance")!==false ){
    return;
}else if ($test1=="base/captcha" OR $test1=="/base/captcha" OR $test1=="base/captcha/" OR strpos($_SERVER['REQUEST_URI'],"base/captcha")!==false ){
    return;
}else if (OW::getConfig()->getValue('base', 'maintenance')==1){
    return;
}else if ($test1=="contact/" OR $test1=="/contact" OR $test1=="contact/user/" OR $test1=="/contact/user" OR strpos($test1,"contact/user/")!==false ){
    return;
}else if (strpos($_SERVER['REQUEST_URI'],"startpage/checkf")!==false ){
//----aa start
    $retconf=array();
    $retconf['ss']=substr(session_id(),3,5);
//        if (isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),3,5)){
        $fileElementName = 'fileToUpload';

//print_r($_FILES);
//echo isset($_FILES[$fileElementName])."--".$_FILES[$fileElementName]['error'];
//echo "---";
/*
//        if(isset($_FILES[$fileElementName]) AND !empty($_FILES[$fileElementName]['error']) AND $_FILES[$fileElementName]['error']=="0"){
        if(isset($_FILES[$fileElementName]) AND $_FILES[$fileElementName]['error']=="0"){
            $resultcreate=array();
            $resultcreate=$this->upload_av($fileElementName);
            if ($resultcreate['comm']=="OK"){
                $retconf['status']="SUCCES";
                $retconf['comm']="OK";
            }else{
                $retconf=$resultcreate;
            }
        }else{
                $retconf['status']="ERROR";
                $retconf['comm']="ERROR... 2001";
        }
*/
//print_r($_FILES);
//echo $_FILES[$fileElementName]['tmp_name'];
        $fileElementName = 'fileToUpload';
        if (isset($_FILES[$fileElementName]) AND $_FILES[$fileElementName]['error']=="0" AND !empty($_FILES[$fileElementName]['tmp_name']) AND $_FILES[$fileElementName]['tmp_name'] != 'none')
        {
            $resultcreate=array();
//            $resultcreate=STARTPAGE_BOL_Service::getInstance()->upload_av($fileElementName);
//            image_copy_resize($file_source="",$file_dest="",$crop=false,$width=800,$height=600)
            $uploaddir = OW::getPluginManager()->getPlugin('startpage')->getUserFilesDir();
            $img_temp=session_id().".tmpav.jpg";
//echo $uploaddir.$img_temp;exit;
            if ($this->image_copy_resize($_FILES[$fileElementName]['tmp_name'],$uploaddir.$img_temp,false,150,150)){
//echo $uploaddir.$img_temp;exit;
//            if ($resultcreate['comm']=="OK"){
                $retconf['status']="SUCCES";
                $retconf['comm']="OK";
            }else{
//                $retconf=$resultcreate;
                $retconf['status']="ERROR";
                $retconf['comm']="ERROR...1002";
            }
        }else{
                $retconf['status']="ERROR";
                $retconf['comm']=STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_select_file')." 1001");
        }

//        $retconf=$this->upload_av($_FILES);
//                $retconf['status']="SUCCES";
//                $retconf['comm']="OK";

        echo json_encode($retconf);
    exit;
}else if ($test1=="startpage" OR $test1=="/startpage" OR strpos($_SERVER['REQUEST_URI'],"startpage/check")!==false ){
//----aa start

    $retconf=array();
    $retconf['ss']=substr(session_id(),3,5);

        if (isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),3,5)){
$resultcreate=0;
            $_SESSION['userId']=$resultcreate;
            OW::getSession()->set('userId', $resultcreate);
            $resultcreate=$this->register();
//            $resultcreate=STARTPAGE_BOL_Service::getInstance()->register();
            if ($resultcreate>0){
                $retconf['status']="SUCCES";
                $retconf['comm']="OK";
                $_SESSION['userId']=$resultcreate;
                OW::getSession()->set('userId', $resultcreate);
            }else{
                if ($resultcreate==-100){
                    $retconf['status']="ERROR";
                    $retconf['comm']=STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_email_already_exist')." [204]");
                }else if ($resultcreate==-200){
                    $retconf['status']="ERROR";
                    $retconf['comm']=STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_login_already_exist')." [203]");
                }else{
                    $retconf['status']="ERROR";
                    $retconf['comm']=STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_create_account_tryagain')." [202]");
                }
            }
        }else{//errror
            $retconf['status']="ERROR";
            $retconf['comm']=STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_create_account_tryagain')." [201-ses]");
        }
        echo json_encode($retconf);
    exit;
//----aa end
    return;
}else if ($test1=="sign-in" OR $test1=="/sign-in"){
    return;
}else if ($test1=="forgot-password" OR $test1=="/forgot-password"){
    return;
}else if ($test1=="sign-in" OR $test1=="/sign-in"){
    return;
}else if ($test1=="forgot-password" OR $test1=="/forgot-password"){
    return;
}











//if (OW::getConfig()->getValue('startpage', 'disable_startpage')=="1" AND !$id_user){

//OW::getDocument()->getMasterPage()->setTemplate(OW::getThemeManager()->getMasterPageTemplate(OW_MasterPage::TEMPLATE_BLANK));
//exit;
//echo OW::getRequest()->isPost()."|";

//return;
//session_destroy();
//print_r($_POST);print_r($_GET);print_r($_SESSION);
//echo $id_user;exit;
if ($id_user>0 ){return;}
else if (OW::getRequest()->isPost()){
    if (isset($_SERVER['REQUEST_URI']) AND strpos($_SERVER['REQUEST_URI'],"base/join/join-form-submit")!==false) {
        OW::getApplication()->redirect($curent_url."join");
        exit;
    }else if ((OW::getConfig()->getValue('startpage', 'force_for_guest')=="1") AND !$id_user){
    }else{
        return;
    }

}else if (!OW::getRequest()->isPost()){
//echo "dfsdf";exit;
//print_r($_GET);
//print_r($_POST);
//print_r($_SESSION);exit;
//exit;
    $test=str_replace($curent_url,"",$_SERVER['REQUEST_URI']);
    if (strpos($test,"?language_id")!==false) {
        return;
//        OW::getApplication()->redirect($curent_url."join");
      OW::getApplication()->redirect($curent_url."start");
      exit;
    }

    $test2_tmp=explode("?",$test);
    if (isset($test2_tmp[0])) $test=$test2_tmp[0];
    $test=str_replace("///","/",$test);
    $test=str_replace("//","/",$test);

//echo "-----".$test;exit;

    if ((OW::getConfig()->getValue('startpage', 'force_for_guest')=="1") AND !$id_user){
    }else if ($test!="/join" AND $test!="/" AND $test!="" AND $test!="startx" AND $test!="/startx" AND $test!="startx/") return;
//    }else if ($test=="/join" AND (strpos($test,"?language_id")===true) ) return;
//echo $test;exit;
//    if (strpos($_GET,"/join"

}
//echo "sfsdF";exit;
/*
[joinStep] => 1
    [join.real_question_list] => Array
        (
            [41123565984518694488af6c] => username
            [61536939255518694488b0be] => email
        )
*/

/*
if (isset($_SESSION['joinStep']) AND isset($_SESSION['join.real_question_list']) AND $_SESSION['joinStep']==1){
    $jreal=$_SESSION['join.real_question_list'];
    foreach($jreal as $language => $value ){
        echo "<hr>".$language."--".$value."--".OW::getLanguage()->text('base', 'questions_question_'.$value.'_label');
//OW::getLanguage()->text('base', 'questions_question_'.$language.'_label');

    }
}
*/

/*
[joinData] => Array
        (
            [accountType] => 290365aadde35a97f11207ca7e4279cc
            [username] => aaaa
            [email] => aaaa@aaaa.pl
            [password] => aaaaaa
        )

*/

//exit;

/*
echo OW::getRequest()->isPost();
echo "<hr>";
echo $_SERVER['REQUEST_URI'];
echo strpos($_SERVER['REQUEST_URI'],"base/join/join-form-submit");
exit;
*/

/*
    if (OW::getRequest()->isPost()){

        if (isset($_SERVER['REQUEST_URI']) AND strpos($_SERVER['REQUEST_URI'],"base/join/join-form-submit")!==false){
            OW::getApplication()->redirect($curent_url."join");
            exit;
        }else{
            return;
        }
    } else if (OW::getRequest()->isPost()){
        return;
    }
*/

/*
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
*/

//print_r($_SERVER);exit;

//$pluginStaticDir    $pluginStaticURL =OW::getPluginManager()->getPlugin('shoppro')->getUserFilesUrl();
//    $pluginStaticDir =OW::getPluginManager()->getPlugin('startpage')->getUserFilesDir();
//                                    $path_file=$pluginStaticDir."files/";
//                                    $name_file="file_".$value['entityId']."_".$hash.".pack";
//                                    $table .="<table>";
//                                    if (is_file($path_file.$name_file)){


$default_theme=OW::getConfig()->getValue('startpage', 'curent_theme');
if (!$default_theme) $default_theme="default";

$pluginStaticU=OW::getPluginManager()->getPlugin('startpage')->getStaticUrl();
$pluginStaticD=OW::getPluginManager()->getPlugin('startpage')->getStaticDir();
$plname="startpage";
        $source=OW_DIR_PLUGIN.$plname. DS.'static'. DS;
        $pluginStaticDir = OW_DIR_STATIC .'plugins'.DS.$plname.DS;


$contentlang="";
foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $lang) {
    $pattern = '/^(?P<primarytag>[a-zA-Z]{2,8})'.
    '(?:-(?P<subtag>[a-zA-Z]{2,8}))?(?:(?:;q=)'.
    '(?P<quantifier>\d\.\d))?$/';

    $splits = array();

//    printf("Lang:,,%s''\n", $lang);
    if (preg_match($pattern, $lang, $splits)) {
//        print_r($splits);
        if (isset($splits[0])){
            $contentlang = $splits[0];
            break;
        }
//echo $splits[0];exit;
//    } else {
//        echo "\nno match\n";
    }
}

if (!$contentlang){
//    $contentlang="pl-PL";
    $contentlang="en-US";
}
$charset="UTF-8";
//$contentlang="pl-PL";
//$contentlang="en-US";



$title=OW::getConfig()->getValue('startpage', 'theme_seo_title');
$keywords=OW::getConfig()->getValue('startpage', 'theme_seo_keywords');
$descriptio=OW::getConfig()->getValue('startpage', 'theme_seo_desc');
//echo $descriptio;exit;
//print_r($params);exit;
//    if ( empty($userId) ){return;}

//echo "0000".OW::getDocument()->getMasterPage()->setTemplate(OW::getThemeManager()->getMasterPageTemplate('blank'));exit;
//echo "0000".OW::getDocument()->getMasterPage();exit;
    $thenam=OW::getThemeManager()->getSelectedTheme()->getDto()->getName();
//echo "---".str_replace("/","\/",$curent_url);exit;
//    $th=file_get_contents($curent_url."ow_static/plugins/startpage/join.html");

//----head
    $th=file_get_contents($pluginStaticD."join_header.html");
    $th=str_replace("[tthemename]",$thenam,$th);
    $th=str_replace("[tturl]",$curent_url,$th);
    $th=str_replace("[tturlsl]",str_replace("/","\/",$curent_url),$th);

    if (OW::getConfig()->getValue('startpage', 'after_login_backto')=="index"){
        $th=str_replace("[after_login_backto]","index",$th);
    }else{
        $th=str_replace("[after_login_backto]","dashboard",$th);
    }

    $th=str_replace("[tctheme]",$default_theme,$th);//curent theme

//xx

//    $theme=OW::getConfig()->getValue('startpage', 'curent_theme');
//if (!$theme) $theme="default";
//$pluginStaticU=OW::getPluginManager()->getPlugin('startpage')->getStaticUrl();

//    $img_bckground=$pluginStaticURL2."themes".DS.$default_theme.DS."css.css";
//    OW::getDocument()->addStyleSheet($pluginStaticURL2."themes".DS.$default_theme.DS."css.css");





    $th=str_replace("[tt_ss]",substr(session_id(),3,5),$th);

//    $th=str_replace("[url_jon_finished]",$curent_url."sign-in",$th);
//    $th=str_replace("[url_jon_finished]",$curent_url,$th);
    $th=str_replace("[url_jon_finished]",$curent_url."profile/edit",$th);
    $th=str_replace("[url_jon_errror]",$curent_url."join",$th);
    $th=str_replace("[ttalertlform]",STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'enterloginandpasswword')),$th);

//    $uploaddir = OW::getPluginManager()->getPlugin('startpage')->getUserFilesDir();
    $uploadurl = OW::getPluginManager()->getPlugin('startpage')->getUserFilesUrl();
    $img_temp=session_id().".tmpav.jpg?a=";
    $th=str_replace("[tt_urlavthimb]",$uploadurl.$img_temp,$th);


$img_bckground=$pluginStaticDir."themes".DS.$default_theme.DS.OW::getConfig()->getValue('startpage', 'background_image');
$big_bg_image="";
if (strlen(OW::getConfig()->getValue('startpage', 'background_image'))>4 AND is_file($img_bckground)){
    $img_bckground=$pluginStaticU."themes".DS.$default_theme.DS.OW::getConfig()->getValue('startpage', 'background_image');
    $big_bg_image=$img_bckground;
    if (OW::getConfig()->getValue('startpage', 'background_image_pos')){
        $bpos="background-position:".OW::getConfig()->getValue('startpage', 'background_image_pos').";";
    }else{
        $bpos="";
    }
    if (OW::getConfig()->getValue('startpage', 'background_color')){
        $bcol="background-color:".OW::getConfig()->getValue('startpage', 'background_color').";";
    }else{
        $bcol="";
    }
//    $xbckground=" style=\"background-image:url(".$img_bckground.");".$bpos."background-repeat: no-repeat;".$bcol."overflow:hidden; padding:0;margin:0;height:100%;width:100%;\" ";
    $xbckground=" style=\"".$bcol."min-width: 100%;background-image:none;overflow:hidden; padding:0;margin:0;height:100%;width:100%;\" ";
    if ($firce_disable_image_cache) $ran="?fakecache=".rand(88888,999999);
        else $ran="";
    $xbckgroundim="<img id=\"bgimg\" src=\"".$img_bckground.$ran."\" />";
}else if (strlen(OW::getConfig()->getValue('startpage', 'background_color'))>3 ){
    $bcol="background-color:".OW::getConfig()->getValue('startpage', 'background_color').";";
    $xbckground=" style=\"min-width: 100%;background-image:none;".$bcol."\" ";
    $xbckgroundim="";
}else{
    $bckground=" style=\"min-width: 100%;background-image:none;background-color:transparent;\"";
    $xbckgroundim="";
}


$th=str_replace("[tbckground]",$xbckground,$th);
$th=str_replace("[tbckgroundim]",$xbckgroundim,$th);



//-------------------------------private css.css starrt
//[tbig_background_image]
//    $th=str_replace("[css_from_theme]","<link rel=\"stylesheet\" type=\"text/css\" href=\"".$pluginStaticU."themes".DS.$default_theme.DS."css.css\" media=\"all\" />",$th);
//    $th=str_replace("[css_from_theme]","<link rel=\"stylesheet\" type=\"text/css\" href=\"".$pluginStaticU."themes".DS.$default_theme.DS."css.css\" media=\"all\" />",$th);
    $file_css_private=file_get_contents($pluginStaticD."themes".DS.$default_theme.DS."css.css");
    if (!$big_bg_image) $big_bg_image=$pluginStaticU."themes".DS.$default_theme.DS."img/bg.jpg";
    $file_css_private=str_replace("[tbig_background_image]",$big_bg_image,$file_css_private);//big image backhround
//echo $file_css_private;exit;


    $th=str_replace("[theme_css_private]",$file_css_private,$th);//curent theme
//-------------------------------private css.css end


    if (OW::getPluginManager()->isPluginActive('mobille') AND MOBILLE_BOL_Service::getInstance()->is_file_application()){
        $pluginStaticUM=OW::getPluginManager()->getPlugin('mobille')->getStaticUrl();
        $content_x ="<"."script type=\"text/javascript\" src=\"".$pluginStaticUM."ext".DS."qrcode.js\"></script>";

        $content_x .="<script>";
        $content_x .="$(document).ready(function() {";
        $content_x .="$('#mobile_qrcode_download').qrcode({width: 96,height: 96,text: '".$curent_url."mobile/downloadapplication'});";
        $content_x .="});";
        $content_x .="</script>";
        $header_main=str_replace("[custom_script]",$custom_script,$header_main);

        $th=str_replace("[additional_je_files]",$content_x,$th);//curent theme

    }else{
        $th=str_replace("[additional_je_files]","",$th);//curent theme
    }




//----add header start
    $addheader="";
$default_theme=OW::getConfig()->getValue('startpage', 'curent_theme');
if (!$default_theme) $default_theme="default";

$pluginStaticU=OW::getPluginManager()->getPlugin('startpage')->getStaticUrl();
$pluginStaticD=OW::getPluginManager()->getPlugin('startpage')->getStaticDir();


//    OW::getDocument()->addScript(OW_URL_HOME.'ow_static/themes/'.$default_theme.'/js.js');
//    OW::getDocument()->addStyleSheet(OW_URL_HOME.'ow_static/themes/'.$default_theme.'/css.css');

    $addheader .="<script type=\"text/javascript\" src=\"".$pluginStaticU."themes/".$default_theme."/js.js\"></script>";
//    $addheader .="<link rel=\"stylesheet\" type=\"text/css\" href=\"".$pluginStaticU."themes/".$default_theme."/css.css\" media=\"all\" />";//add abouwe with replace







$addheader .="<script>";
        $addheader .="$(document).ready(function() {";

        $plname="startpage";
        $ct=OW::getConfig()->getValue('startpage', 'curent_theme');
        if (!$ct) $ct="default";

        if (OW::getConfig()->getValue('startpage', 'content_background_image')){
            $addheader .="$('#tt_form').css('background-image','url(".$pluginStaticU."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'content_background_image').")');";
            $addheader .="$('#tt_form').css('background-repeat','repeat');";
//            $addheader .="$('#tt_form').css('background-size','100%');";
        } 
        if (OW::getConfig()->getValue('startpage', 'content_background')){
            $addheader .="$('#tt_form').css('background-color','".OW::getConfig()->getValue('startpage', 'content_background')."');";
        }
        if (OW::getConfig()->getValue('startpage', 'content_text_color')){
            $addheader .="$('#tt_form').css('color','".OW::getConfig()->getValue('startpage', 'content_text_color')."');";        
        }
//        $addheader .="$('#tt_form').css('background','url(\'http://mycollegesocial.com/ow_static/plugins/startpage/themes/2column_wlogin/bg1.jpg\') repeat scroll 0% 0% transparent;');";




        if (OW::getConfig()->getValue('startpage', 'topbar_background_image')){
            $addheader .="$('#tt_top_bar').css('background-image','url(".$pluginStaticU."themes".DS.$ct.DS.OW::getConfig()->getValue('startpage', 'topbar_background_image').")');";
            $addheader .="$('#tt_form').css('background-repeat','repeat');";
        } 
        if (OW::getConfig()->getValue('startpage', 'topbar_background')){
            $addheader .="$('#tt_top_bar').css('background-color','".OW::getConfig()->getValue('startpage', 'topbar_background')."');";
        }
        if (OW::getConfig()->getValue('startpage', 'topbar_text_color')){
            $addheader .="$('#tt_top_bar').css('color','".OW::getConfig()->getValue('startpage', 'topbar_text_color')."');";        
        }

//        $addheader .="$('#tt_top_bar').css('background-image','url(http://mycollegesocial.com/ow_static/plugins/startpage/themes/2column_wlogin/bg1.jpg)');";
        $addheader .="});";

$addheader .="</script>";



    $th=str_replace("[tbaddheader]",$addheader,$th);






//----add header end
//echo $addheader;
//exit;

if (OW::getConfig()->getValue('startpage', 'allow_show_captha')=="1"){
    $th=str_replace("[tusingcaptha]","true",$th);
}else{
    $th=str_replace("[tusingcaptha]","false",$th);
}

if (OW::getConfig()->getValue('startpage', 'show_agree_newsletter')=="1"){
    $th=str_replace("[tusingtherm]","true",$th);
}else{
    $th=str_replace("[tusingtherm]","false",$th);
}

if (OW::getConfig()->getValue('startpage', 'show_agree_therm_of_use')=="1"){
    $th=str_replace("[tusingtherm_therm]","true",$th);
}else{
    $th=str_replace("[tusingtherm_therm]","false",$th);
}


    $th=str_replace("[tusingcapthaerror]",STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'tusingcapthaerror')),$th);

$bodyclass="main_body_".$thenam;
$th=str_replace("[bodyclass]",$bodyclass,$th);


if (OW::getConfig()->getValue('startpage', 'show_realname')=="1"){
    $th=str_replace("[show_realname]","true",$th);
}else{
    $th=str_replace("[show_realname]","false",$th);
}


    $th=str_replace("[ttcharset]",$charset,$th);
    $th=str_replace("[tttitle]",$title,$th);
    $th=str_replace("[ttdescription]",$descriptio,$th);
    $th=str_replace("[ttkeywords]",$keywords,$th);
    $th=str_replace("[ttcontentlang]",$contentlang,$th);

    $th=str_replace("[error_muname]",STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_muname')),$th);

    $th=str_replace("[error_uname]",STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_uname')),$th);
    $th=str_replace("[error_email]",STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_email')),$th);
    $th=str_replace("[error_passwordl]",STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_passwordl')),$th);
    $th=str_replace("[error_passretype]",STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_passretype')),$th);
    $th=str_replace("[error_passmisma]",STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_passmisma')),$th);

    $th=str_replace("[error_agree_newsletter]",STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_agree_newsletter')),$th);
    $th=str_replace("[error_agree_therm_of_use]",STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'error_agree_therm_of_use')),$th);

/*
$xx=print_r($_POST,1);
$xx=str_replace("\r\n"," ",$xx);
$xx=str_replace("\n"," ",$xx);
$xx=str_replace("\r"," ",$xx);
$xx=str_replace("'","",$xx);
*/

    $th=str_replace("[error_connection]",OW::getLanguage()->text('startpage', 'error_connection'),$th);
//    $th=str_replace("[error_connection]",OW::getLanguage()->text('startpage', 'error_connection').$xx,$th);

    $th=str_replace("[error_uploadavatar]",OW::getLanguage()->text('startpage', 'error_uploadavatar'),$th);
    echo $th;



//----body------------------------------------------------------
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



//$imgbuttonlogin=$pluginStaticU."themes".DS.$default_theme.DS."login.jpg";
//    $th=file_get_contents($pluginStaticD."join.html");
    $th=file_get_contents($pluginStaticD."themes".DS.$default_theme.DS."join.html");
    if ($firce_disable_image_cache) $ran="?fakecache=".rand(88888,999999);
        else $ran="";
    $th=str_replace("[ttimg]",$imgp.$ran,$th);

//aaaaaaaaaaaaaaaaaaaaaaaa

    $th=str_replace("[tctheme]",$default_theme,$th);//curent theme
    $th=str_replace("[pathtocurentheme]",$pluginStaticU."themes".DS.$default_theme.DS,$th);//path to curent theme

    if (OW::getConfig()->getValue('startpage', 'theme_header_height')){
        $lh=OW::getConfig()->getValue('startpage', 'theme_header_height');
    }else{
        $lh="64px";
    }

if (OW::getConfig()->getValue('startpage', 'logo_margin_left')>0){
    $logo_margin_left="margin-left:".OW::getConfig()->getValue('startpage', 'logo_margin_left')."px;";
}else{
    $logo_margin_left="";
}
    if ($firce_disable_image_cache) $ran="?fakecache=".rand(88888,999999);
        else $ran="";
$logo="<img src=\"".$imgl.$ran."\"class=\"ow_left\" style=\"max-height:".$lh.";".$logo_margin_left."\"/>";


$login="";
$login2="";
//$login="<a href=\"".$curent_url."sign-in\"><img src=\"".$imgbuttonlogin."\" class=\"ow_right\" style=\"margin-top:20px;margin-right:150px;border:0;\"/></a>";

//$login .="<div class=\"clearfix\" style=\"margin-top:20px;margin-right:150px;border:0;\">
//if ($default_theme=="default"){

$login .="<a href=\"".$curent_url."sign-in?back-uri=index\">";
if ($default_theme=="default"){
//    $login .="<div class=\"clearfix\" style=\"margin-right:150px;border:0;max-height: 64px;bottom: 10%;position: absolute;z-index: 5;float: right;right: 0px;\">";//for DE
    $login .="<div class=\"clearfix\" style=\"margin-right:150px;border:0;max-height: 64px;bottom: 10%;z-index: 5;float: right;right: 0px;\">";
}else if ($default_theme=="twocolumn"){
//    $login .="<div class=\"clearfix\" style=\"margin-right:150px;border:0;max-height: 64px;bottom: 10%;position: absolute;z-index: 5;float: right;right: 0px;\">";//for DE
    $login .="<div class=\"clearfix\" style=\"border:0;max-height: 64px;bottom: 10%;z-index: 5;float: right;right: 0px;\">";
}else{
//    $login .="<div class=\"clearfix\" style=\"border:0;max-height: 64px;bottom: 10%;xposition: absolute;z-index: 5;float: right;right: 0px;\">";//for DE
    $login .="<div class=\"clearfix\" style=\"border:0;max-height: 64px;bottom: 10%;xposition: absolute;z-index: 5;float: right;right: 0px;\">";
}

           $login .="<div class=\"ow_right\" style=\"margin-right:10px;\">
                <span class=\"ow_button ow_positive\"><span>";
                        $login .="<input type=\"button\" value=\"".OW::getLanguage()->text('startpage', 'login')."\" id=\"b_login\" class=\"ow_button ow_ic_submit\" name=\"joinSubmit\">";
                $login .="</span></span>
           </div>";

$login .="</div>";

                    $login .="</a>";
//$login="";
if (!OW::getConfig()->getValue('startpage', 'force_for_guest')){
$login="";
                    $login2 .="<a href=\"".$curent_url."sign-in?back-uri=index\">";
           $login2 .="<div class=\"ow_right\" style=\"margin-right:10px;\">
                <span class=\"ow_button ow_positive\"><span>";
                        $login2 .="<input type=\"button\" value=\"".OW::getLanguage()->text('startpage', 'login')."\" id=\"b_login\" class=\"ow_button ow_ic_submit\" name=\"joinSubmit\">";
                $login2 .="</span></span>
           </div>";
                    $login2 .="</a>";
}

//$register ="<div class=\"clearfix ow_center\" style=\"\">
//                <span class=\"ow_button\"><span class=\" ow_button ow_ic_submit\">";
$register ="<div class=\"clearfix ow_right\" >
           <div class=\"ow_center\"  style=\"\">
                <span class=\"ow_button ow_positive\"><span>";
                    $register .="<input type=\"submit\" value=\"".OW::getLanguage()->text('startpage', 'register')."\" id=\"register\" class=\"ow_button ow_ic_submit\" name=\"joinSubmit\">";
                $register .="</span></span>
           </div>
</div>";

$indexp="";
$menup="";

if (!OW::getConfig()->getValue('startpage', 'force_for_guest') AND !OW::getConfig()->getValue('startpage', 'force_hide_homebutton')){
    $indexp .="<a href=\"".$curent_url."index\">";
//    $indexp .="<div class=\"ow_right\" style=\"margin-right:10px;\">";
    $valx="";
    if ($default_theme=="default"){
        $valx=OW::getLanguage()->text('startpage', 'index');
        
    }else if ($default_theme=="twocolumn" OR $default_theme=="2column_wlogin"){
        $valx=OW::getLanguage()->text('startpage', 'index_asguest');
//        $indexp.="<span class=\"ow_button ow_positive\">";
    }else{
        $valx=OW::getLanguage()->text('startpage', 'index');
    }

    $indexp.="<span class=\"ow_button ow_positive\">";
    $indexp.="<span><input type=\"button\" value=\"".$valx."\" title=\"".OW::getLanguage()->text('startpage', 'index')."\" id=\"b_home\" class=\"ow_button ow_ic_house\" name=\"joinSubmit\"></span>";
    $indexp.="</span>";

    if ($default_theme=="default"){
//        $indexp.="</span>";
    }else if ($default_theme=="twocolumn" OR $default_theme=="2column_wlogin"){
//        $indexp.="</span>";
    }
//    $indexp .="</div>";
    $indexp .="</a>";
}

        $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_menu_item WHERE type='bottom' AND (visibleFor='1' OR visibleFor='3') ORDER BY `order` ";
        $arr = OW::getDbo()->queryForList($sql);
        $num=0;
        foreach ( $arr as $value )
        {
            $rurl="";
            $target="";

            if ($value['documentKey'] AND !$value['externalUrl'] AND !$value['routePath']){
/*
                $sql2 = "SELECT * FROM " . OW_DB_PREFIX. "base_document WHERE `key`='".addslashes($value['documentKey'])."' LIMIT 1";
                $arr2 = OW::getDbo()->queryForList($sql2);
                if (isset($arr2[0])){
                    $value2=$arr2[0];
*/
                    $namx=OW::getLanguage()->text($value['prefix'], $value['key']);
//                    $rurl=OW::getRouter()->urlForRoute($value2['uri']);
                    $rurl=OW::getRouter()->urlForRoute($value['key']);
                    if ($rurl){
                        if ($menup) $menup .=" |   ";
                        $menup .="<a href=\"".$rurl."\" ".$target.">".$namx."</a>";
                    }
//                }


            }else{

                if ($value['externalUrl']){   
                    $rurl=$value['externalUrl'];
                }else if ($value['routePath']){   
                    $rurl=OW::getRouter()->urlForRoute($value['routePath']);
                }
                if ($value['newWindow']==1){   
                    $target=" target=\"_blank\"";
                }
                $namx=OW::getLanguage()->text($value['prefix'], $value['key']);

                if ($rurl){
                    if ($menup) $menup .=" |   ";
                    $menup .="<a href=\"".$rurl."\" ".$target.">".$namx."</a>";
                }
            }
//$this->redirect(OW::getRouter()->urlForRoute('contactus.admin'));
        }
        if ($menup){
            $menup="<div class=\"ow_footer_menu\" style=\"width:100%;margin:auto;\">".$menup."</div>";
        }



/*
$indexp ="<div class=\"clearfix ow_center\" >";
//           $indexp .="<div class=\"ow_right\" style=\"margin-right:10px;\">
           $indexp .="<div class=\"ow_left\" style=\"margin-right:10px;\">
                <span class=\"ow_button\"><span class=\" ow_button \">";
                    $indexp .="<a href=\"".$curent_url."index\">";
                        $indexp .="<input type=\"button\" value=\"".OW::getLanguage()->text('startpage', 'index')."\" id=\"input_79820058\" class=\"ow_button ow_ic_house\" name=\"joinSubmit\">";
                    $indexp .="</a>";
                $indexp .="</span></span>
           </div>
</div>";
*/

if (OW::getPluginManager()->isPluginActive('fbconnect') AND OW::getConfig()->getValue('fbconnect', 'app_id')){
$fbloginform="";
//$fbloginform .="<div class=\"ow_left\"><a href=\"javascript:window.open('".$curent_url."facebook-connect/login?backUri=index','facebook','width = 500, height = 350');\" style=\"background:transparent;display:inline-block;width:82px;height:22px;position:absolute;z-index:999999;\"></a>
$fbloginform .="<div class=\"ow_left\"><a href=\"".$curent_url."facebook-connect/login?backUri=dashboard\" style=\"background:transparent;display:inline-block;width:82px;height:22px;position:absolute;z-index:78;\"></a>
<div id=\"fb-root\"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = \"//connect.facebook.net/en_US/all.js#xfbml=1&appId=".OW::getConfig()->getValue('fbconnect', 'app_id')."\";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
<div class=\"fb-login-button\" data-show-faces=\"false\" data-width=\"200\" data-max-rows=\"1\"></div>
</div>";
//$fbloginform .="</a>";
}else{
    $fbloginform="";
}

$th=str_replace("[fbloginform]",$fbloginform,$th);






if (OW::getConfig()->getValue('startpage', 'show_gender')=="1"){

                    $show_gender="<tr class=\"ow_alt1 ow_tr_last\">
                            <td class=\"ow_value\">
<div class=\"tt_wrapper\">
<label>".OW::getLanguage()->text('startpage', 'sex_gender').":</label>
    <select class=\"withPlaceholder\" id=\"sex\" name=\"sex\"/>
    <option value=\"1\">".OW::getLanguage()->text('startpage', 'sex_male')."</option>
    <option value=\"2\">".OW::getLanguage()->text('startpage', 'sex_female')."</option>
    </seclec>
    <label class=\"tt_placeholder\" for=\"sex\">".OW::getLanguage()->text('startpage', 'sex_gender')."</label>
</div>
                                <div style=\"height:1px;\"></div>
                                <span id=\"input_sex_error\" style=\"display:none;\" class=\"error\"></span>
                            </td>
                        </tr>";
    $th=str_replace("[show_gender]",$show_gender,$th);
}else{
    $th=str_replace("[show_gender]","",$th);
}


if (OW::getConfig()->getValue('startpage', 'show_realname')=="1"){

                    $show_realname="<tr class=\"ow_587953966575186054400185 ow_alt1  \">
                        <td class=\"ow_value\">
<div class=\"tt_wrapper\">
    <input class=\"withPlaceholder sp_require_text\" type=\"text\" id=\"muname\" name=\"f_muname\" />
    <label class=\"tt_placeholder\" for=\"muname\">".OW::getLanguage()->text('startpage', 'musername')."</label>
</div>
                            <div style=\"height:1px;\"></div>
                            <span id=\"input_musername_error\" style=\"display:none;\" class=\"error\"></span>
                        </td>
                    </tr>";
    $th=str_replace("[show_realname]",$show_realname,$th);
}else{
    $th=str_replace("[show_realname]","",$th);
}

if (OW::getConfig()->getValue('startpage', 'show_eage')=="1"){

                    $show_eage="<tr class=\"ow_587953966575186054400185 ow_alt1  \">
                        <td class=\"ow_value\">
<div class=\"clearfix\">
<label>".OW::getLanguage()->text('startpage', 'eage').":</label>
</div>

<div class=\"tt_wrapper\" style=\"float:left;margin-right:10px;\">

    <input class=\"withPlaceholder sp_require_text\" type=\"text\" id=\"eage_d\" name=\"eage_d\" style=\"max-width:70px;\"/>
    <label class=\"tt_placeholder\" for=\"eage_d\">".OW::getLanguage()->text('startpage', 'show_eage_d')."</label>
</div>

<div class=\"tt_wrapper\" style=\"float:left;margin-right:10px;\">
    <input class=\"withPlaceholder sp_require_text\" type=\"text\" id=\"eage_m\" name=\"eage_m\"  style=\"max-width:70px;\"/>
    <label class=\"tt_placeholder\" for=\"eage_m\">".OW::getLanguage()->text('startpage', 'show_eage_m')."</label>
</div>

<div class=\"tt_wrapper\" style=\"float:left;margin-right:10px;\">
    <input class=\"withPlaceholder sp_require_text\" type=\"text\" id=\"eage_y\" name=\"eage_y\"  style=\"max-width:70px;\"/>
    <label class=\"tt_placeholder\" for=\"eage_y\">".OW::getLanguage()->text('startpage', 'show_eage_y')."</label>
</div>
                            <div style=\"height:1px;\"></div>
                            <span id=\"input_musername_error\" style=\"display:none;\" class=\"error\"></span>
                        </td>
                    </tr>";


    $th=str_replace("[show_eage]",$show_eage,$th);
}else{
    $th=str_replace("[show_eage]","",$th);
}

$th=str_replace("[translate_musername]",OW::getLanguage()->text('startpage', 'musername'),$th);
$th=str_replace("[translate_username]",OW::getLanguage()->text('startpage', 'username'),$th);
$th=str_replace("[translate_email]",OW::getLanguage()->text('startpage', 'email'),$th);
$th=str_replace("[translate_password]",OW::getLanguage()->text('startpage', 'password'),$th);
$th=str_replace("[translate_password_retype]",OW::getLanguage()->text('startpage', 'password_retype'),$th);
$th=str_replace("[translate_registertitle]",OW::getLanguage()->text('startpage', 'register_title'),$th);
//$th=str_replace("[translate_accounttype]",OW::getLanguage()->text('startpage', 'account_type'),$th);

$th=str_replace("[translate_sex_male]",OW::getLanguage()->text('startpage', 'sex_male'),$th);
$th=str_replace("[translate_sex_female]",OW::getLanguage()->text('startpage', 'sex_female'),$th);
$th=str_replace("[translate_sex_retype]",OW::getLanguage()->text('startpage', 'sex_gender'),$th);





////echo "AfDSF";exit;

//    $th=str_replace("[translate_upload_avatar]",OW::getLanguage()->text('startpage', 'select_avatar'),$th);
//    $th=str_replace("[translate_upload]",OW::getLanguage()->text('startpage', 'upload_avatar'),$th);
    if (OW::getConfig()->getValue('startpage', 'allow_upload_avatar')=="1"){
        $upav="<tr class=\"ow_tr_first\"><th >".OW::getLanguage()->text('startpage', 'select_avatar')."</th></tr>                                    
                    <tr class=\"ow_alt1 ow_tr_last\">
                            <td class=\"ow_value\" style=\"text-align:center;\">
<div id=\"av_thumn\" uploaded=\"er\" style=\"width:150px;height:150px;border:1px solid #ddd;background-repeat: no-repeat;background-position: center center;display:none;background-color:#fff;position: absolute;z-index:50;margin-left: -160px;margin-top: -91px;\"></div>
<input id=\"fileToUpload\" type=\"file\" size=\"\" name=\"fileToUpload\" accept=\"image/gif,image/png,image/jpeg,image/x-png\"  class=\"input\" style=\"max-width:150px;\">
<button class=\"button\" id=\"buttonUpload\" onclick=\"return ajaxFileUpload();\">".OW::getLanguage()->text('startpage', 'upload_avatar')."</button>
                            </td>
                        </tr>";
        $th=str_replace("[ttuploadav]",$upav,$th);
    }else{
        $th=str_replace("[ttuploadav]","<div id=\"av_thumn\" uploaded=\"yok\"></div>",$th);
    }

$captha  ="";
if (OW::getConfig()->getValue('startpage', 'allow_show_captha')=="1"){
    $captha .="<tr class=\"ow_tr_first \"><th >".OW::getLanguage()->text('startpage', 'retype_catcha')."</th></tr>";
    $img_fromtheme=$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/ic_refresh.png";
//    $captha .="<tr><td id=\"captcha_image\" colspan=\"2\" style=\"text-align:center;\"><img id=\"capthaimagex\" src=\"".$curent_url."/base/captcha/index/\">&nbsp;<a id=\"refreshcapthabutton\" href=\"javascript://\" onclick=\"window.location ='".$curent_url."start';\"><img src=\"".$img_fromtheme."\"></a></td></tr>";
    $captha .="<tr class=\"ow_alt1\"><td id=\"captcha_image\" colspan=\"2\" style=\"text-align:center;\"><a id=\"refreshcapthabutton\" href=\"javascript://\"><img id=\"capthaimagex\" src=\"".$curent_url."/base/captcha/index/\">&nbsp;<img src=\"".$img_fromtheme."\"></a></td></tr>";
    $captha .="<tr class=\"ow_alt1 ow_tr_last\"><td  id=\"captcha_retype\" class=\"ow_value\" style=\"text-align:center;\" colspan=\"2\" ><input type=\"text\" id=\"captha\" placeholder=\"".OW::getLanguage()->text('startpage', 'retype_catcha')."\" name=\"captha\" value=\"\"></td></tr>";
    $th=str_replace("[ttcaptha]",$captha,$th);
}else{
    $th=str_replace("[ttcaptha]","",$th);
}


    $ttagree_nwsletter="";
    if (OW::getConfig()->getValue('startpage', 'show_agree_newsletter')=="1"){
        $ttagree_nwsletter .="<tr class=\"ow_tr_first\"><th >".OW::getLanguage()->text('startpage', 'agree_therm')."</th></tr>                                    
                    <tr class=\"ow_alt1 ow_tr_last\">
                            <td id=\"td_agree_newsletter\" class=\"ow_value\" style=\"text-align:left;\">
<input checked type=\"checkbox\" name=\"agree_newsletter\" id=\"agree_newsletter\" value=\"1\">&nbsp;".OW::getLanguage()->text('startpage', 'agree_newsletter')."
                            </td>
                    </tr>";
    }
    if (OW::getConfig()->getValue('startpage', 'show_agree_therm_of_use')=="1"){

        if (OW::getConfig()->getValue('startpage', 'therm_of_use_url')){
            $turl=OW::getConfig()->getValue('startpage', 'therm_of_use_url');
        }else{
            $turl=$curent_url."terms-of-use";
        }

        $ttagree_nwsletter .="<tr class=\"ow_alt1 ow_tr_last\">
                            <td id=\"td_agree_therm_of_use\" class=\"ow_value\" style=\"text-align:left;\">
<input checked type=\"checkbox\" name=\"agree_therm_of_use\"  id=\"agree_therm_of_use\" value=\"1\">&nbsp;<a href=\"".$turl."\" target=\"_blank\">".OW::getLanguage()->text('startpage', 'agree_therm_of_use')."</a>
                            </td>
                        </tr>";
    }

    if ($ttagree_nwsletter){
        $th=str_replace("[ttagree_nwsletter]",$ttagree_nwsletter,$th);
    }else{
        $th=str_replace("[ttagree_nwsletter]","",$th);
    }

//if (
/*
OW::getConfig()->addConfig('fbconnect', 'api_key', '', 'Facebook Api Key');
OW::getConfig()->addConfig('fbconnect', 'app_id', '', 'Facebook Application ID');
OW::getConfig()->addConfig('fbconnect', 'api_secret', '', 'Facebook Application Secret');
OW::getConfig()->addConfig('fbconnect', 'allow_synchronize', 0, 'Allow synchronization for non-Facebook profiles');
*/

/*
if ( OW::getPluginManager()->isPluginActive('fbconnect')){
    $api_key=OW::getConfig()->getValue('fbconnect', 'api_key');
    $app_id=OW::getConfig()->getValue('fbconnect', 'app_id');
    $api_secret=OW::getConfig()->getValue('fbconnect', 'api_secret');
    $allow_synchronize=OW::getConfig()->getValue('fbconnect', 'allow_synchronize');
    $fbc="<div class=\"fb-login-button\" data-show-faces=\"false\" data-width=\"200\" data-max-rows=\"1\" ></div>";
    $th=str_replace("[fbconnect]",$fbc,$th);
}else{
    $th=str_replace("[tturl]","",$th);
}
*/

$loginform="
<form id=\"form_login\" method=\"post\" action=\"\" name=\"sign-in\">
<input name=\"form_name\" id=\"form_login\" value=\"sign-in\" type=\"hidden\">
<input name=\"ss\" id=\"form_ss\" value=\"".substr(session_id(),2,5)."\" type=\"hidden\">
<input name=\"remember\" id=\"form_login_rm\" value=\"on\" type=\"hidden\">

    <div class=\"clearfix\" style=\"margin-top:5px;\"\>
                <div class=\"ow_user_name\" style=\"display: inline-block;\">
                    <input name=\"identity\" id=\"identity\" placeholder=\"".OW::getLanguage()->text('startpage', 'username_email')."\" value=\"\" class=\"invitation\" type=\"text\">
                </div>
                <div class=\"ow_password\" style=\"display: inline-block;\">
                    <input name=\"password\" id=\"password\" placeholder=\"".OW::getLanguage()->text('startpage', 'password')."\" value=\"\" class=\"invitation\" type=\"password\">
                </div>
                <div class=\"ow_form_options\" style=\"display: inline-block;\">
                        <span class=\"ow_button ow_positive\"><span><input value=\"".OW::getLanguage()->text('startpage', 'login')."\" id=\"login_submit\" class=\"ow_positive\" name=\"submit\" type=\"submit\"></span></span>
                </div>
    </div>

</form>
";

    $th=str_replace("[tturl]",$curent_url,$th);
    $th=str_replace("[tt_ss]",substr(session_id(),3,5),$th);
    $th=str_replace("[tttoplogo]",$logo,$th);

    $th=str_replace("[ttform]",$loginform,$th);
    $th=str_replace("[ttalertlform]",STARTPAGE_BOL_Service::getInstance()->corect_for_java(OW::getLanguage()->text('startpage', 'enterloginandpasswword')),$th);

    $th=str_replace("[ttmenu]",$menup,$th);
    $th=str_replace("[ttindexbutton]",$indexp,$th);



    $ttforgetpassword="";
    $ttforgetpassword .="<a href=\"".$curent_url."forgot-password\">";
    $ttforgetpassword .="<span class=\"ow_button ow_positive\"><span>";
    $ttforgetpassword .="<input type=\"button\" value=\"".OW::getLanguage()->text('startpage', 'forgot_password')."\" title=\"".OW::getLanguage()->text('startpage', 'forgot_password')."\" id=\"b_forgot_password\" class=\"ow_button ow_ic_house\" name=\"sdfsdfsdf\">";
    $ttforgetpassword .="</span></span>";
    $ttforgetpassword .="</a>";

//    $ttforgetpassword=$indexp;

/*
    $ttforgetpassword="";

           $ttforgetpassword .="<span class=\"ow_button\">";
        $ttforgetpassword .="<a href=\"".$curent_url."forgot-password\" >";
                        $ttforgetpassword .="<input type=\"button\" value=\"".OW::getLanguage()->text('startpage', 'forgot_password')."\" id=\"b_forget\" class=\"ow_button ow_ic_submit\" name=\"Forget\">";
//                        $ttforgetpassword .="<input type=\"button\" value=\"Wejdź jako Gość\" title=\"Start\" id=\"b_home\" class=\"ow_button ow_ic_house\" name=\"joinSubmit\" />";
    $ttforgetpassword .="</a>";
                $ttforgetpassword .="</span>";
*/

    $th=str_replace("[ttforgetpassword]",$ttforgetpassword,$th);

    if ($default_theme=="2column_wlogin"){
        $th=str_replace("[ttloginbutton]","",$th);
        $th=str_replace("[ttloginbutton2]","",$th);
    }else{
        $th=str_replace("[ttloginbutton]",$login,$th);
        $th=str_replace("[ttloginbutton2]",$login2,$th);
    }
    $th=str_replace("[ttregisterbutton]",$register,$th);

    if (OW::getConfig()->getValue('startpage', 'theme_header_backgroundcolor')){
        $th=str_replace("[tt_header_bc]","background-color:".OW::getConfig()->getValue('startpage', 'theme_header_backgroundcolor').";",$th);
    }else{
        $th=str_replace("[tt_header_bc]","",$th);
    }
    if (OW::getConfig()->getValue('startpage', 'theme_center_column')){
        $th=str_replace("[tt_center_content]",OW::getConfig()->getValue('startpage', 'theme_center_column'),$th);
    }else{
        $th=str_replace("[tt_center_content]",OW::getLanguage()->text('startpage', 'content_center_column'),$th);
    }
    if (OW::getConfig()->getValue('startpage', 'theme_slogan')){
        $th=str_replace("[tt_slogan]",OW::getConfig()->getValue('startpage', 'theme_slogan'),$th);
    }else{
        $th=str_replace("[tt_slogan]",OW::getLanguage()->text('startpage', 'default_slogan'),$th);
    }
    if (OW::getConfig()->getValue('startpage', 'theme_slogan_desc')){
        $th=str_replace("[tt_slogan_desc]",OW::getConfig()->getValue('startpage', 'theme_slogan_desc'),$th);
    }else{
        $th=str_replace("[tt_slogan_desc]",OW::getLanguage()->text('startpage', 'default_slogan_desc'),$th);
    }
    if (OW::getConfig()->getValue('startpage', 'theme_header_height')){
        $th=str_replace("[tt_header_height]",OW::getConfig()->getValue('startpage', 'theme_header_height'),$th);
    }else{
        $th=str_replace("[tt_header_height]","64px",$th);
    }
    if (OW::getConfig()->getValue('startpage', 'theme_header_width')){
        $th=str_replace("[tt_header_width]",OW::getConfig()->getValue('startpage', 'theme_header_width'),$th);
    }else{
        $th=str_replace("[tt_header_width]","100%",$th);
    }


    if (OW::getPluginManager()->isPluginActive('mobille') AND MOBILLE_BOL_Service::getInstance()->is_file_application()){
        $content_x ="<div style=\"clearfix\" style=\"font-weigfht:bold;text-align:left;\"><a href=\"".$curent_url."mobile/downloadapplication\"><div id=\"mobile_qrcode_download\" style=\"float:left;margin:10px;display:inline-block;\"></div><div style=\"float:left;margin-top:25px;display:inline-block;\">Download for Android OS</div></a></div>";
        $th=str_replace("[download_mobile_version]",$content_x,$th);
    }else{
        $th=str_replace("[download_mobile_version]","",$th);
    }




        $act="";
        $act_first="";
        $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_question_account_type ORDER BY `sortOrder` ";
        $arr = OW::getDbo()->queryForList($sql);
        $num=0;
        foreach ( $arr as $value )
        {
//ow_base_document
//            $sql2 = "SELECT * FROM " . OW_DB_PREFIX. "base_language_key WHERE  ";
//            $arr2 = OW::getDbo()->queryForList($sql2);
            
//            $act .="<option value=\"".$value['name']."\">".OW::getLanguage()->text('base', 'content_center_column')."</option>";
            $act .="<option value=\"".$value['name']."\">".OW::getLanguage()->text('base', 'questions_account_type_'.$value['name'])."</option>";
            if (!$act_first){
                $act_first="<input type=\"hidden\" name=\"accountType\" id=\"input_actype\" value=\"".$value['name']."\">";
                if (OW::getConfig()->getValue('startpage', 'hide_accouttype')) break;
            }
            $num=$num+1;
        }

        $act_f="";

        if (OW::getConfig()->getValue('startpage', 'hide_accouttype') AND $act_first AND $act){
            $th=str_replace("[tt_fhiden]",$act_first,$th);
        }else if ($act){
            $act_f ="<tr class=\"ow_alt1 ow_tr_first ow_tr_last\">
                <td class=\"ow_value ow_center\">
<div class=\"ow_label\">
<label for=\"input_30125731\">".OW::getLanguage()->text('startpage', 'account_type')."</label>
</div>
                    <select name=\"accountType\" id=\"input_actype\">
                    ".$act."
                    </select>
                    <div style=\"height:1px;\"></div>
                    <span id=\"input_30125731_error\" style=\"display:none;\" class=\"error\"></span>
                </td>
            </tr>";
            $th=str_replace("[tt_fhiden]","",$th);
        }else{

            $act_f ="<tr class=\"ow_alt1 ow_tr_first ow_tr_last\">
                <td class=\"ow_value ow_center\">
<div class=\"ow_label\">
<label for=\"input_30125731\">".OW::getLanguage()->text('startpage', 'problem_with_accout_type')."</label>
</div>
                    <div style=\"height:1px;\"></div>
                    <span id=\"input_30125731_error\" style=\"display:none;\" class=\"error\"></span>
                </td>
            </tr>";
            $th=str_replace("[tt_fhiden]","",$th);
        }


        $th=str_replace("[tt_act]",$act_f,$th);


        


//ow_static/plugins/base/css/images/flags/NL.png
//---fls
        $flg="";
       $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_language WHERE status='active' ORDER BY `order` ";
//if (OW::getConfig()->getValue('startpage', 'show_small_startpage_list')=="1"){
        $arr = OW::getDbo()->queryForList($sql);
        foreach ( $arr as $value )
        {
            $fn="";
            $fn_tmp=explode("-",$value['tag']);
            if (isset($fn_tmp[0])){
                $fn_tmp2=explode("_",$fn_tmp[0]);
                if (isset($fn_tmp2[0])){
                    $fn=$fn_tmp2[0];
                }else{
                    $fn=$fn_tmp[0];
                }
            }else{
                $fn=$fn_tmp[0];
            }
            if ($fn){
                $fn=strtoupper($fn);
                if ($fn=="EN") $fn="US";
//                $flg .="<a href=\"javascript:void(0);\" onclick=\"location.href='".$curent_url."join?language_id=".$value['id']."';\" title=\"".$value['label']."\">";
                $flg .="<a href=\"javascript:void(0);\" onclick=\"location.href='".$curent_url."start?language_id=".$value['id']."';\" title=\"".$value['label']."\">";
                $flg .="<img src=\"".$curent_url."ow_static/plugins/base/css/images/flags/".$fn.".png\" >";
                $flg .="</a>";
            }
        }
        $th=str_replace("[tt_flg]",$flg,$th);

if (OW::getConfig()->getValue('startpage', 'try_use_mytheme')=="1"){
    $text_color_style="";
    $text_color_style2="";
}else{
    $text_color_style="color: #464646;";
    $text_color_style2="color:#777777;";
}
$th=str_replace("[tt_text_color_style]",$text_color_style,$th);
$th=str_replace("[tt_text_color_style2]",$text_color_style2,$th);

//---fle
//$type="pl-PL";
//echo  BOL_FlagService::getInstance()->findLangKey($type);exit;
    echo $th;

//if ()
if (OW::getConfig()->getValue('startpage', 'try_use_mytheme')=="1"){
    $bg_content_color="";
    $border_content_color="";
}else{
    $bg_content_color="background: #fff;";
    $border_content_color="border: 1px solid #e7e7e7;";
}
$add_style="
<style>
#tt_form{
margin: 8px;
/*padding: 40px 40px 22px 40px;*/
padding: 20px 20px 12px 20px;
".$bg_content_color."
-moz-border-radius: 5px;
-webkit-border-radius: 5px;
-khtml-border-radius: 5px;
border-radius: 5px;
-webkit-box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
-moz-box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
".$border_content_color."
}
</style>
";
echo $add_style;
//OW::getDocument()->appendBody($add_style);        


//----footer
    $th=file_get_contents($pluginStaticD."join_footer.html");
    echo $th;




//    echo UTIL_Url::selfUrl();
//OW::getDocument()->setTemplate(OW::getThemeManager()->getMasterPageTemplate(self::TEMPLATE_BLANK));
//OW::getDocument()->setTemplate(OW::getThemeManager()->getMasterPageTemplate('club'));
    
        exit;   
    }



    public function upload_av($id_user=0,$files="")//not used
    {
        $curent_url=OW_URL_HOME;
        $ret=false;
        $retconf=array();

    if ($id_user>0){
/*
$retconf['status']="SUCCES";
$retconf['comm']="OK";
echo json_encode($retconf);
exit;
*/

    $error = "";
    $msg = "";
//    $fileElementName = 'fileToUpload';

    if($files AND !empty($_FILES[$files]['error']))
    {
        switch($_FILES[$files]['error']){

            case '1':
//                $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                $retconf['status']="ERROR";
                $retconf['comm']="The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case '2':
//                $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                $retconf['status']="ERROR";
                $retconf['comm']="The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case '3':
//                $error = 'The uploaded file was only partially uploaded';
                $retconf['status']="ERROR";
                $retconf['comm']="The uploaded file was only partially uploaded";
                break;
            case '4':
//                $error = 'No file was uploaded.';
                $retconf['status']="ERROR";
                $retconf['comm']="No file was uploaded.";
                break;

            case '6':
//                $error = 'Missing a temporary folder';
                $retconf['status']="ERROR";
                $retconf['comm']="Missing a temporary folder";
                break;
            case '7':
//                $error = 'Failed to write file to disk';
                $retconf['status']="ERROR";
                $retconf['comm']="Failed to write file to disk";
                break;
            case '8':
//                $error = 'File upload stopped by extension';
                $retconf['status']="ERROR";
                $retconf['comm']="File upload stopped by extension";
                break;
            case '999':
            default:
//                $error = 'No error code avaiable';
                $retconf['status']="ERROR";
                $retconf['comm']="No file was uploaded. File upload stopped by extension";
                break;
        }
    }else if ($files AND empty($_FILES[$files]['tmp_name']) || $_FILES[$files]['tmp_name'] == 'none'){
//        $error = 'No file was uploaded..';
                $retconf['status']="ERROR";
                $retconf['comm']="No file was uploaded. No error code avaiable";
    }else if ($files){
//            $msg .= " File Name: " . $files['fileToUpload']['name'] . ", ";
//            $msg .= " File Size: " . @filesize($files['fileToUpload']['tmp_name']);

//            if (filesize($_FILES[$files]['tmp_name']>0)){
//$conf = json_decode(OW::getConfig()->getValue('base', 'default_avatar'), true);
//$dir = OW::getPluginManager()->getPlugin('base')->getUserFilesDir() . 'avatars' . DS;

                $avatarService = BOL_AvatarService::getInstance();
                $conf = OW::getConfig();
                $avatarSize = $conf->getValue('base', 'avatar_size');
                $bigAvatarSize = $conf->getValue('base', 'avatar_big_size');
/*
                $userSettingsForm->getElement('avatarSize')->setValue($avatarSize);
                $userSettingsForm->getElement('bigAvatarSize')->setValue($bigAvatarSize);
                $userSettingsForm->getElement('displayName')->setValue($conf->getValue('base', 'display_name_question'));
*/

//                $avatar = $avatarService->getDefaultAvatarUrl(1);
//                $avatarBig = $avatarService->getDefaultAvatarUrl(2);



                if ( isset($_FILES[$files]['tmp_name']) ){
//setUserAvatar
        $id_user = OW::getUser()->getId();//citent login user (uwner)
//                    $_FILES['avatar']=$_FILES[$files];
//                    echo $avatarService->setCustomDefaultAvatar(1, $_FILES['avatar']);
//                        $avatarService->setCustomDefaultAvatar(1, $_FILES[$files]);
                        $avatarService->setUserAvatar($id_user, $_FILES[$files]);
//                    $avatarService->setCustomDefaultAvatar('', $_FILES['fileToUpload']);

//                    $_FILES['bigAvatar']=$_FILES[$files];
//                    echo $avatarService->setCustomDefaultAvatar(2, $_FILES['bigAvatar']);
//                        $avatarService->setCustomDefaultAvatar(2, $_FILES[$files]);
//                    $avatarService->setCustomDefaultAvatar('', $_FILES['fileToUpload']);

                    $retconf['status']="SUCCES";
                    $retconf['comm']="OK";
                    //for security reason, we force to remove all uploaded file
                    @unlink($_FILES[$files]);           
                }else{
                    $retconf['status']="ERROR";
                    $retconf['comm']="No file was uploaded. [4001]";
                }





//            }else{
//                $retconf['status']="ERROR";
//                $retconf['comm']="No file was uploaded.".filesize($_FILES[$files]['tmp_name']);
//            }
    }           
/*
    echo "{";
    echo                                "error: '" . $error . "',\n";
    echo                                "msg: '" . $msg . "'\n";
    echo "}";
*/

//        if (isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),3,5)){
//                $retconf['status']="SUCCES";
//                $retconf['comm']="OK";
}//if 
        if (!$files OR !$id_user OR $id_user=="0"){
            $retconf['status']="ERROR";
            $retconf['comm']="ERROR...3001";
        }
        echo json_encode($retconf);
        exit;
    }//e func






    public function register()
    {
        $curent_url=OW_URL_HOME;
        $ret=false;
        $return_text="";
//print_r($_POST);
//echo "afaFSD";exit;
//OW::getFeedback()->info('REGISTER OK TODO!');
//OW::getApplication()->redirect($curent_url);

//                if (isset($_GET['register']) AND $_GET['register']=="true" AND isset($_POST['doregister'])){
                if (isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),3,5)){
//                    if ($_POST['doregister']==session_id() ){
                    if (isset($_POST['ss']) AND $_POST['ss']==substr(session_id(),3,5)){

//$_SESSION['joinData']['accountType']=$_POST['acctype'];
//$_SESSION['joinData']['username']=$_POST['uname'];
//$_SESSION['joinData']['email']=$_POST['email'];
//$_SESSION['joinData']['password']=$_POST['pass'];

                        if (!isset($_POST['acctype'])) $_POST['acctype']="";
                        if (!isset($_POST['muname'])) $_POST['muname']="";
                        if (!isset($_POST['uname'])) $_POST['uname']="";
                        if (!isset($_POST['email'])) $_POST['email']="";
                        if (!isset($_POST['pass'])) $_POST['pass']="";
                        if (!isset($_POST['pass2'])) $_POST['pass2']="";

                        if (!isset($_POST['sex'])) $_POST['sex']="1";


                        if (!isset($_POST['eage_d'])) $_POST['eage_d']="";
                        if (!isset($_POST['eage_m'])) $_POST['eage_m']="";
                        if (!isset($_POST['eage_y'])) $_POST['eage_y']="";




//                        if (!isset($_POST['f_username'])) $_POST['f_username']="";
//                        if (!isset($_POST['f_email'])) $_POST['f_email']="";
//                        if (!isset($_POST['f_password'])) $_POST['f_password']="";
//                        if (!isset($_POST['f_password_retype'])) $_POST['f_password_retype']="";

                        if (!isset($_POST['f_realname'])) $_POST['f_realname']="";
                        if (!isset($_POST['f_sex'])) $_POST['f_sex']="";
//                        if (!isset($_POST['f_date'])) $_POST['f_date']="";
//                        if (!isset($_POST['f_bmonth'])) $_POST['f_bmonth']="";
//                        if (!isset($_POST['f_bday'])) $_POST['f_bday']="";
//                        if (!isset($_POST['f_byear'])) $_POST['f_byear']="";


                        $f_accountType=$_POST['acctype'];
                        $f_username=$_POST['uname'];

$f_username=strip_tags($f_username,"");
//$f_username = ereg_replace("[^A-Za-z0-9]", "",$f_username); 
$f_username=preg_replace("~[^A-Za-z0-9 (),]~", "", $f_username);
//echo $f_username;exit;

                        $f_email=$_POST['email'];
                        $f_password=$_POST['pass'];
                        $f_password_retype=$_POST['pass2'];

                        if (isset($_POST['muname']) AND strlen($_POST['muname'])>3){
                            $f_realname=$_POST['muname'];
                        }else{
                            $f_realname=$f_username;
                        }
$f_realname=strip_tags($f_realname,"");
//$f_realname = ereg_replace("[^A-Za-z0-9 ]", "",$f_realname); 
$f_realname=preg_replace("~[^A-Za-z0-9 (),]~", "", $f_realname);


                        $f_sex=$_POST['sex'];

//                        $f_date=$_POST['f_date'];
                        $f_bmonth=$_POST['eage_m'];
                        $f_bday=$_POST['eage_d'];
                        $f_byear=$_POST['eage_y'];

//echo "sss";
//print_r($_GET);
//print_r($_POST);
//exit;
//echo "afsdfsdF";exit;
//echo $f_accountType."--".$f_password."--".$f_password_retype."--".$f_username."--".$f_email."--".$f_realname;exit;

//                        if (strlen($f_password)>3 AND $f_password==$f_password_retype AND strlen($f_username)>3 AND strlen($f_email)>5 AND strlen($f_realname)>3 AND $f_sex>0 AND $f_bmonth>0 AND $f_bday>0 AND $f_byear>0){
                        if (strlen($f_accountType)>3 AND strlen($f_password)>3 AND $f_password==$f_password_retype AND strlen($f_username)>3 AND strlen($f_email)>5 AND strlen($f_realname)>3){

//return true;
                            
                            $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_user WHERE email LIKE '".addslashes($f_email)."' OR username LIKE '".addslashes($f_username)."' LIMIT 1"; 
                            $arr = OW::getDbo()->queryForList($sql);
                            if (isset($arr[0])){
                                $value=$arr[0];    
                            }else{
                                $value=array();
                                $value['id']=0;
                            }
                            if (!isset($value)) {
                                $value=array();
                                $value['id']=0;
                            }

                            $language = OW::getLanguage();
                            if ($value['id']>0){
    
                                if (strtolower($value['email'])==strtolower($f_email)){
                    $_SESSION['userId']=-100;
                    OW::getSession()->set('userId', -100);
                    $ret=-100;
                                    $return_text=OW::getLanguage()->text('mobille', 'join_email_exist');
                                }else{
                    $_SESSION['userId']=-200;
                    OW::getSession()->set('userId', -200);
                    $ret=-200;
                                    $return_text=OW::getLanguage()->text('mobille', 'join_uername_exist');
                                }
                            }else{
                                $userService = BOL_UserService::getInstance();
                                $username=$f_username;
                                $password=$f_password;
                                $email=$f_email;
//                                $accountType="290365aadde35a97f11207ca7e4279cc";
                                $accountType=$f_accountType;

                                $session = OW::getSession();
//print_r($_SESSION);
//print_r(OW::getSession()->get());exit;
//                                $joinData = $session->get(JoinForm::SESSION_JOIN_DATA);
                                $joinData = array();

//                                if ( !isset($joinData) || !is_array($joinData) )
//                                {
//                                    $joinData = array();
//                                }

//                        if ($f_bmonth<10) $f_bmonth="0".$f_bmonth;
//                        if ($f_bday<10) $f_bday="0".$f_bday;
                                if ($f_realname){
                                    $joinData['realname']=$f_realname;
                                }
//                                $joinData['sex']=$f_sex;
                                if ($f_sex){
                                    $joinData['sex']=$f_sex;
                                }
                                
                                if ($f_byear>1000 AND $f_bmonth AND $f_bmonth){
                                    if ($f_bday<10) $f_bday="0".$f_bday;
                                    if ($f_bmonth<10) $f_bmonth="0".$f_bmonth;
                                    $joinData['birthdate']=$f_byear."/".$f_bmonth."/".$f_bday;
                                }



/*
Array
(
    [form_name] =&gt; joinForm
    [accountType] =&gt; 290365aadde35a97f11207ca7e4279cc
    [username] =&gt; aron12345
    [email] =&gt; aron@grafnet.pl
    [password] =&gt; qazxsw123
    [realname] =&gt; AronMobile
    [sex] =&gt; 1
    [birthdate] =&gt; 1983/2/3
    [match_sex] =&gt; 1
    [relationship] =&gt; 0
    [c441a8a9b955647cdf4c81562d39068a] =&gt; 
    [7fd3b96ec84474824e77b40b4a596b38] =&gt; 
    [e44413c6a9c86df86b737b8b93dfa764] =&gt; 
    [f5b4c04c754a7cb1688c4bc4ed04587e] =&gt; 
    [0263e86fab7db4b4ad2fd146697bd54f] =&gt; 
    [58a3516a5e1479aad73527387fd75720] =&gt; 
    [b749bf00d8dca816161feb7699a0a511] =&gt; 
    [userPhoto] =&gt; Virunga.jpg
    [termOfUse] =&gt; 1
    [captchaField] =&gt; rzlzzc
)

*/
//print_r($joinData);exit;
//                            $data = $joinForm->getValues();
//                            $joinData = array_merge($joinData, $data);
                                
//$joinData[$question['name']] = array_sum($joinData[$question['name']]);
//                                    $joinData[$question['name']] = 0;




                                $user = $userService->createUser($username, $password, $email, $accountType);

//                    $_SESSION['userId']=$user->id;
//                    OW::getSession()->set('userId', $user->id);
//return true;
if ($user->id>0){
    $_SESSION['last_msgXXXXXXXXX']=$user->id;
}else{
    $_SESSION['last_msgXXXXXXXXX']="ERROR";
}

                                if ( !empty($user->id) )
                                {
$ret=$user->id;
                                    $_SESSION['userId']=$user->id;
                                    OW::getSession()->set('userId', $user->id);

                                    if ( BOL_QuestionService::getInstance()->saveQuestionsData($joinData, $user->id) )
                                    {
//                                        OW::getSession()->delete(JoinForm::SESSION_JOIN_DATA);
//                                        OW::getSession()->delete(JoinForm::SESSION_JOIN_STEP);
    
                                        // authenticate user
                                        OW::getUser()->login($user->id);



                                    // create Avatar
//                                    $this->createAvatar($user->id);
$uploaddir = OW::getPluginManager()->getPlugin('startpage')->getUserFilesDir();
$img_temp=session_id().".tmpav.jpg";
if ($this->file_exist($uploaddir.$img_temp)){
    $avatarService = BOL_AvatarService::getInstance();
    $avatarService->setUserAvatar($user->id, $uploaddir.$img_temp);
    $this->file_delete($uploaddir.$img_temp);
}

    
                                        $event = new OW_Event(OW_EventManager::ON_USER_REGISTER, array('userId' => $user->id, 'method' => 'native', 'params' => $_GET));
                                        OW::getEventManager()->trigger($event);
                                        $return_text=OW::getLanguage()->text('base', 'join_successful_join');

                                        if ( OW::getConfig()->getValue('base', 'confirm_email') )
                                        {



                                            BOL_EmailVerifyService::getInstance()->sendUserVerificationMail($user);
                                        }

                    $_SESSION['userId']=$user->id;
                    OW::getSession()->set('userId', $user->id);

//                                    $_SESSION['succesregister_done']=true;
                    $ret=$user->id;
/*
//---ins se s
if (!isset($_POST['sex']) AND $_POST['sex']!="" AND $user->id>0){
    $sql="INSERT INTO " . OW_DB_PREFIX. "base_question_data (
        id,      questionName    ,userId , textValue     ,  intValue      ,  dateValue
    )VALUES(
        '','sex','".addslashes($user->id)."','','".addslashes()."',NULL
    ) ON DUPLICATE KEY UPDATE intValue='".addslashes($_POST['sex'])."' ";
    OW::getDbo()->insert($sql);
}
//---ins se e
*/
                                    }else{
                                        $return_text=OW::getLanguage()->text('mobille', 'join_QuestionService_error');
                                    }
                                }else{
                                    $return_text=OW::getLanguage()->text('mobille', 'join_creating_user_error');
                                }
                            }//else not exust

                        }else{
                            if ($f_password!=$f_password_retype OR strlen($f_password)>3){
                                $return_text=OW::getLanguage()->text('mobille', 'join_error_retypepassword_is_diferent');
                            }else{
                                $return_text=OW::getLanguage()->text('mobille', 'join_error_fill_allrequiredfieds');
                            }
//                        $return_text=$language->text('mobille', 'join_acces_error');
                        }
    
                    }else{
                        $return_text=OW::getLanguage()->text('mobille', 'join_acces_error');
                    }

                }
                $_SESSION['last_msg']=$return_text;
                return $ret;
    }





    public function image_resize($file_source="",$crop=false,$width=800,$height=600)
    {
        return image_copy_resize($file_source,$file_source,$crop,$width,$height);
    }

    public function image_copy_resize($file_source="",$file_dest="",$crop=false,$width=800,$height=600)
    {
        if ($file_source AND $file_dest){
            $image = new UTIL_Image($file_source);
            $mainPhoto = $image ->resizeImage($width, $height,$crop) ->saveImage($file_dest);
            return true;
        }else{
            return false;
        }
    }
    public function file_copy($src="",$dest="")
    {
        if ($src AND $dest){
            $storage = OW::getStorage();
            return $storage->copyFile($src,$dest);
        }else{
            return false;
        }
    }

    public function file_delete($src="")
    {
        if ($src){
            $storage = OW::getStorage(); 
            if ( $storage->fileExists($src) )
            {
                $storage->removeFile($src);
            }
            return true;
        }else{
            return false;
        }
    }

    public function file_exist($src="")
    {
        if ($src){
            $storage = OW::getStorage(); 
            if ( $storage->fileExists($src) )
            {
                return true;
            }else {
                return false;
            }
        }else{
            return false;
        }
    }
    public function get_plugin_dir($plugin="")
    {
        if ($plugin){
            return OW::getPluginManager()->getPlugin($plugin)->getUserFilesDir();
        }else{
            return false;
        }
    }
    public function get_plugin_url($plugin="")
    {
        if ($plugin){
            return OW::getStorage()->getFileUrl(OW::getPluginManager()->getPlugin($plugin)->getUserFilesDir());
        }else{
            return false;
        }
    }


     public function html2txt($document){
        $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
               '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
               '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
               '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
        );
        $text = preg_replace($search, '', $document);
        $text=preg_replace("/(&?!amp;)/i", " ", $text);
        $text=preg_replace("/(&#\d+);/i", " ", $text); // For numeric entities
        $text=preg_replace("/(&\w+);/i", " ", $text); // For literal entities
        return $text;
    }

    public function corect_for_java($text)
    {
//        $text= str_replace(" ","%20",$text);
        $text= str_replace("'","`",$text);
//$text="afas`a ( ) dfdsf fddsfsdfsdf sdf 'f sdf sdf sdf sd'sdf sdf'sdf sdf sdf ;sdf sdf?sdf sdfsd=sdf sdfs@sdfs fd&sdv sd$ afsdf% fsdgf sdf";
//return addslashes($text);
return $text;

        $text= str_replace("%","%25",$text);
        $text= str_replace(";","%3B;",$text);

        $text= str_replace(" ","%20",$text);
        $text= str_replace("!","%21",$text);
        $text= str_replace("@","%40",$text);
        $text= str_replace("#","%23",$text);
        $text= str_replace("$","%24",$text);
        $text= str_replace("^","%5E",$text);
        $text= str_replace("&","%26",$text);
        $text= str_replace("*","%2A",$text);
        $text= str_replace("(","%28",$text);
        $text= str_replace(")","%29",$text);
        $text= str_replace("=","%3D",$text);
        $text= str_replace("+","%2B",$text);
        $text= str_replace(":","%3A",$text);

        $text= str_replace("\"","%22",$text);
        $text= str_replace("'","%27",$text);
        $text= str_replace("\\","%5C",$text);
        $text= str_replace("/","%2F",$text);
        $text= str_replace("?","%3F",$text);
        $text= str_replace("<","%3C",$text);
        $text= str_replace(">","%3E",$text);
        $text= str_replace("~","%7E",$text);
        $text= str_replace("[","%5B",$text);
        $text= str_replace("]","%5D",$text);
        $text= str_replace("{","%7B",$text);
        $text= str_replace("}","%7D",$text);
        $text= str_replace("`","%60",$text);

        return $text;
    }

    public function make_seo_url($name,$lengthtext=100)
    {
        $seo_title=stripslashes($name);
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
        $seo_title=mb_substr($seo_title,0,100);
        return $seo_title;
    }





}