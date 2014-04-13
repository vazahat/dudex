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


//$is_points=SEARCH_BOL_Service::getInstance()->get_all_questions('');

class SEARCH_BOL_Service
{
    private static $classInstance;

    var $tabmenu=array();    

    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct()
    {

    }

    public function getDepartmentLabel( $id )
    {
        return OW::getLanguage()->text('shoppro', $this->getDepartmentKey($id));
    }

    public function addDepartment( $email, $label )
    {
        $contact = new SHOPPRO_BOL_Department();
        $contact->email = $email;
        SHOPPRO_BOL_DepartmentDao::getInstance()->save($contact);

        BOL_LanguageService::getInstance()->addValue(
            OW::getLanguage()->getCurrentId(),
            'shoppro',
            $this->getDepartmentKey($contact->id),
            trim($label));
    }

    public function deleteDepartment( $id )
    {
        $id = (int) $id;
        if ( $id > 0 )
        {
            $key = BOL_LanguageService::getInstance()->findKey('shoppro', $this->getDepartmentKey($id));
            BOL_LanguageService::getInstance()->deleteKey($key->id, true);
            SHOPPRO_BOL_DepartmentDao::getInstance()->deleteById($id);
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




    public function make_ads($type="full")
    {
        $ret="";
        $ret .=SEARCH_BOL_Service::getInstance()->make_adsfromadsense($type);
        $ret .=SEARCH_BOL_Service::getInstance()->make_adsfromadspro($type);
        if ($type=="full"){//ads
            $ret .=SEARCH_BOL_Service::getInstance()->make_adsfromadvertisement($type);
        }
        return $ret;
    }




    public function make_adsfromadvertisement($type="full")
    {
$curent_url=OW_URL_HOME;
        $ret="";
        if ( OW::getPluginManager()->isPluginActive('ads') AND OW::getConfig()->getValue('search', 'allow_ads_ads')){
//                        LEFT JOIN " . OW_DB_PREFIX. "base_question_data uadd ON (uadd.userId=uss.id ".$add_query." )
//                            LEFT JOIN " . OW_DB_PREFIX. "adspro_adverts_packs adxp ON (adxp.userId=uss.id ".$add_query." )
                     $timestamp=strtotime(date('Y-m-d H:i:s'));
                        $sql = "SELECT * FROM " . OW_DB_PREFIX. "ads_banner 
                        ORDER BY RAND() LIMIT 1";
                        $arr = OW::getDbo()->queryForList($sql);
                        if (isset($arr[0])){
                            $value=$arr[0];
                            if (isset($value['id']) AND $value['id']>0){
//                                $sql="UPDATE " . OW_DB_PREFIX. "adspro_adverts SET total_shown=total_shown+1 WHERE id='".addslashes($value['id'])."' LIMIT 1";
//                                OW::getDbo()->query($sql);

                    if ($type=="full"){
//                        $ret .="<div class=\"ow_console_dropdown_hover ow_comments_item_content clearfix\" style=\"overflow:hidden;margin:3px;margin-bottom:15px;min-width:300px;width:100%;display:block;min-height:50px;\">";
                        $ret .="<div class=\"ow_bg_color aron_dropdown_hover ow_comments_item_content clearfix\" style=\"text-align: center;overflow:hidden;margin:3px;margin-bottom:15px;min-width:300px;width:100%;display:block;min-height:50px;\">";
                    }else{
                        $ret .="<div class=\"ow_bg_color aron_dropdown_hover ow_comments_item_content clearfix\" style=\"overflow:hidden;margin:3px;width:300px;display:block;min-height:50px;\">";
                    }

$ret .="<div class=\"ow_center clearfix \" style=\"text-align:left;border-bottom:1px solid #eee;\">";
$ret .="<i style=\"font-weight:bold;font-size: 9px;line-height:9px;\">".OW::getLanguage()->text('search', 'advertisement')."</i>";
$ret .="</div>";
//$linkopen=$curent_url."ads/golink/".$value['id'];
//                                        $ret .="<a href=\"".$linkopen."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" target=\"_blank\">";
                                        if ($type=="full"){
                                            $ret .="<div class=\"clearfix\" style=\"overflow:hidden;text-align:left;font-weight:bold;font-size:10px;display:inline-block;min-height:40px;max-width:550px;min-width:230px;margin-left:0px;margin-top:10px;margin-bottom:10px;width:100%;\">";
                                        }else{
                                            $ret .="<div class=\"clearfix\" style=\"overflow:hidden;text-align:left;font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:0px;margin-top:10px;margin-bottom:10px;width:100%;\">";
                                        }
                                        $content=stripslashes($value['code']);
//                                        $content=$this->html2txt($content,160);                                        
                                        $ret .=$content;

                                        $ret .="</div>";
//                                        $ret .="</a>";
                                    $ret .="</div>";
                                }//if $value['id']>0
                        }
        }
        return $ret;
    }



    public function make_adsfromadspro($type="full")
    {
$curent_url=OW_URL_HOME;
        $ret="";
        if ( OW::getPluginManager()->isPluginActive('adspro') AND OW::getConfig()->getValue('search', 'allow_ads_adspro')){
//                        LEFT JOIN " . OW_DB_PREFIX. "base_question_data uadd ON (uadd.userId=uss.id ".$add_query." )
//                            LEFT JOIN " . OW_DB_PREFIX. "adspro_adverts_packs adxp ON (adxp.userId=uss.id ".$add_query." )
                     $timestamp=strtotime(date('Y-m-d H:i:s'));
                        $sql = "SELECT * FROM " . OW_DB_PREFIX. "adspro_adverts adxs 

                        WHERE 
                        (
                            adxs.status='live' AND
                            adxs.approved='TRUE' AND adxs.expiredate<'".addslashes($timestamp)."' AND 
                            (adxs.max_clicks='0' OR (adxs.max_clicks>0 AND adxs.total_clicks<adxs.max_clicks)) AND 
                            (adxs.max_shown='0' OR (adxs.max_shown>0 AND adxs.total_shown<adxs.max_shown))
                        )
                        ORDER BY RAND() LIMIT 1";
//echo $sql;exit;
                        $arr = OW::getDbo()->queryForList($sql);
                        if (isset($arr[0])){
//echo $sql;exit;
                            $value=$arr[0];
                            if (isset($value['id']) AND $value['id']>0){
                                $sql="UPDATE " . OW_DB_PREFIX. "adspro_adverts SET total_shown=total_shown+1 WHERE id='".addslashes($value['id'])."' LIMIT 1";
                                OW::getDbo()->query($sql);
/*

                                $ret .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                    $ret .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";

                                    if ($uimg){
                                        $ret .="<a href=\"".$uurl."\" style=\"display:inline;color:#008;font-size:14px;font-weight:bold;\">";
                                        $ret .="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                                        $ret .="</a>";
                                    }else{
                                        $ret .="<a href=\"".$uurl."\" style=\"display:inline;;font-size:14px;font-weight:bold;\">";
                                        $ret .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                                        $ret .="</a>";
                                    }

                                    $ret .="</div>";
                                    $ret .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;;float:left;min-height:40px;min-width:230px;max-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $ret .="<a href=\"".stripslashes($value['link'])."\" style=\"display:inline;;font-size:14px;font-weight:bold;\" title=\"".stripslashes($value['name'])."\" >";
                                    $ret .=stripslashes($value['advert_text']);
                                    $ret .="</a>";
                                    $ret .="</div>";
                                $ret .="</div>";
*/


                    if ($type=="full"){
                        $ret .="<div class=\"ow_bg_color aron_dropdown_hover ow_comments_item_content clearfix\" style=\"overflow:hidden;margin:3px;margin-bottom:15px;min-width:300px;width:100%;display:block;min-height:50px;\">";
                    }else{
                        $ret .="<div class=\"ow_bg_color aron_dropdown_hover ow_comments_item_content clearfix\" style=\"overflow:hidden;margin:3px;width:300px;display:block;min-height:50px;\">";
                    }
$ret .="<div class=\"ow_center clearfix \" style=\"text-align:left;border-bottom:1px solid #eee;\">";
//$ret .="<i>Advertisement</i>";
$ret .="<i style=\"font-weight:bold;font-size: 9px;line-height:9px;\">".OW::getLanguage()->text('search', 'advertisement')."</i>";
$ret .="</div>";
//$linkopen=http://test3.a6.pl/ads/golink/1
$linkopen=$curent_url."ads/golink/".$value['id'];
//stripslashes($value['link'])
//                                        $ret .="<a href=\"".$linkopen."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" target=\"_blank\">";
                                        $ret .="<a href=\"".$linkopen."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;font-size:12px;font-weight:bold;\" target=\"_blank\">";
                                        $ret .="<div class=\"clearfix\" style=\"text-align:left;font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:0px;margin-top:10px;margin-bottom:10px;width:100%;\">";
//max_clicks      total_showntotal_shown  max_shown advert_text
//$ret .="<div class=\"ow_tooltip_body clearfix ow_comments_item_content\" style=\"\">";
//$ret .="<div class=\"ow_tooltip_body clearfix \" style=\"\">";


//                                        $ret .="<a href=\"".stripslashes($value['link'])."/index.html\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                        $content=stripslashes($value['advert_text']);
                                        $content=$this->html2txt($content,160);                                        
//                                        $ret .=
//                                        if (mb_strlen($content)>160) $content .="...";
                                        $ret .=$content;
//                                        $ret .="</a>";

//$ret .="</div>";

                                        $ret .="</div>";
                                        $ret .="</a>";
                                    $ret .="</div>";
                                }//if $value['id']>0

                        }

        }
        return $ret;
    }

    public function make_adsfromadsense($type="full")
    {
$curent_url=OW_URL_HOME;
        $ret="";

        if ( OW::getPluginManager()->isPluginActive('adsense') AND OW::getConfig()->getValue('search', 'allow_ads_adsense')){
//                        LEFT JOIN " . OW_DB_PREFIX. "base_question_data uadd ON (uadd.userId=uss.id ".$add_query." )
//                            LEFT JOIN " . OW_DB_PREFIX. "adspro_adverts_packs adxp ON (adxp.userId=uss.id ".$add_query." )
//
                     $timestamp=strtotime(date('Y-m-d H:i:s'));
                        $sql = "SELECT * FROM " . OW_DB_PREFIX. "adsense adxs 
                            LEFT JOIN " . OW_DB_PREFIX. "adsense_ads adxp ON (adxp.id_ads=adxs.id) 
                        WHERE 
                        (
                            adxs.active='1' AND
                            adxs.is_published='1' AND
                            adxs.data_end<'".addslashes($timestamp)."' AND 
                            adxs.description!='' 
                        )
                        ORDER BY RAND() LIMIT 1";
//echo $sql;exit;
                        $arr = OW::getDbo()->queryForList($sql);
                        if (isset($arr[0])){
//echo $sql;exit;
                            $value=$arr[0];
                            if (isset($value['id']) AND $value['id']>0){
                                $sql="UPDATE " . OW_DB_PREFIX. "adsense SET count_show=count_show+1 WHERE id='".addslashes($value['id'])."' LIMIT 1";
                                OW::getDbo()->query($sql);
/*

                                $ret .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"overflow:hidden;margin:3px;width:300px;border-bottom:1px solid #eee;display:block;\">";
                                    $ret .="<div class=\"ow_console_dropdown_hover clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;float:left;width:45px;\">";

                                    if ($uimg){
                                        $ret .="<a href=\"".$uurl."\" style=\"display:inline;;font-size:14px;font-weight:bold;\">";
                                        $ret .="<img src=\"".$uimg."\" title=\"".$dname."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                                        $ret .="</a>";
                                    }else{
                                        $ret .="<a href=\"".$uurl."\" style=\"display:inline;;font-size:14px;font-weight:bold;\">";
                                        $ret .="<img src=\"".$curent_url."ow_static/themes/".OW::getConfig()->getValue('base', 'selectedTheme')."/images/no-avatar.png\" title=\"".OW::getLanguage()->text('search', 'index_hasnotimage')."\" width=\"45px\" style=\"border:0;margin:10px;align:left;display:inline;\" align=\"left\" >";
                                        $ret .="</a>";
                                    }

                                    $ret .="</div>";
                                    $ret .="<div class=\"clearfix\" style=\"font-weight:bold;font-size:14px;display:inline-block;;float:left;min-height:40px;min-width:230px;max-width:230px;margin-left:20px;margin-top:20px;\">";
                                    $ret .="<a href=\"".stripslashes($value['link'])."\" style=\"display:inline;;font-size:14px;font-weight:bold;\" title=\"".stripslashes($value['name'])."\" >";
                                    $ret .=stripslashes($value['advert_text']);
                                    $ret .="</a>";
                                    $ret .="</div>";
                                $ret .="</div>";
*/


                    if ($type=="full"){
                        $ret .="<div class=\"ow_bg_color aron_dropdown_hover ow_comments_item_content clearfix\" style=\"overflow:hidden;margin:0px;margin-bottom:15px;min-width:300px;width:100%;display:block;min-height:50px;padding: 0;\">";
                    }else{
                        $ret .="<div class=\"ow_bg_color aron_dropdown_hover ow_comments_item_content clearfix\" style=\"overflow:hidden;margin:0px;width:300px;display:block;min-height:50px;padding: 0;\">";
                    }
$ret .="<div class=\"ow_center clearfix \" style=\"text-align:left;border-bottom:1px solid #eee;\">";
//$ret .="<i>Advertisement</i>";
$ret .="<i style=\"font-weight:bold;font-size: 9px;line-height:9px;\">".OW::getLanguage()->text('search', 'advertisement')."</i>";
$ret .="</div>";
//$linkopen=http://test3.a6.pl/ads/golink/1
//$linkopen=$curent_url."ads/golink/".$value['id'];
$linkopen=$curent_url."adsense/click/".$value['id']."_".$value['id_owner']."/".base64_encode(urlencode(stripslashes($value['ads_url'])));
//stripslashes($value['link'])
//                                        $ret .="<a href=\"".$linkopen."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" target=\"_blank\">";
                                        $ret .="<a href=\"".$linkopen."\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;font-size:12px;font-weight:bold;\" target=\"_blank\">";
                                        $ret .="<div class=\"clearfix\" style=\"text-align:left;font-weight:bold;font-size:10px;display:inline-block;min-height:40px;min-width:230px;margin-left:0px;margin-top:10px;margin-bottom:10px;width:100%;\">";
//max_clicks      total_showntotal_shown  max_shown advert_text
//$ret .="<div class=\"ow_tooltip_body clearfix ow_comments_item_content\" style=\"\">";
//$ret .="<div class=\"ow_tooltip_body clearfix \" style=\"\">";


//                                        $ret .="<a href=\"".stripslashes($value['link'])."/index.html\" title=\"".stripslashes($value['name'])."\"  style=\"display:inline;;font-size:12px;font-weight:bold;\" >";
                                        $content=stripslashes($value['description']);
                                        $content=$this->html2txt($content,160);                                        
//                                        $ret .=
//                                        if (mb_strlen($content)>160) $content .="...";
                                        $ret .=$content;
//                                        $ret .="</a>";

//$ret .="</div>";

                                        $ret .="</div>";
                                        $ret .="</a>";
                                    $ret .="</div>";
                                }//if $value['id']>0

                        }

        }
        return $ret;
    }


    public function makePagination($page = 1, $totalitems, $limit = 15, $adjacents = 1, $targetpage = "/", $pagestring = "?page=",$position="right")
    {               
    //defaults
    if(!$adjacents) $adjacents = 1;
    if(!$limit) $limit = 15;
    if ($limit==0) $limit=1;
//    if(!$page) $page = 0;
//    $page=$page-1;
//    if ($page<0) $page=0;
    if(!$targetpage) $targetpage = "/";
    if(!isset($margin) OR !$margin) $margin = "";
    if(!isset($padding) OR !$padding) $padding="";
    if(!isset($pagestring1) OR !$pagestring1) $pagestring1="";

    //other vars
    $prev = $page - 1;                                                                  //previous page is page - 1
    $next = $page + 1;                                                                  //next page is page + 1
    $lastpage = ceil($totalitems / $limit);                             //lastpage is = total items / items per page, rounded up.
    $lpm1 = $lastpage - 1;                                                              //last page minus 1
    $space=" ";
//$lastpage++;    
//return "--".$lastpage;    
    if ($position=="center") $position="ow_center";
    else if ($position=="left") $position="ow_left";
    else $position="ow_right";
    $pagination = "";
    if($lastpage > 1)
    {   
        $pagination .= "<div class=\"".$position." ow_paging clearfix ow_smallmargin\"";
        if($margin || $padding)
        {
            $pagination .= " style=\"";
            if($margin)
                $pagination .= "margin: $margin;";
            if($padding)
                $pagination .= "padding: $padding;";
            $pagination .= "\"";
        }
        $pagination .= ">";

        //previous button
        if ($page > 1) 
            $pagination .= "<a href=\"".$targetpage.$pagestring.$prev."\">«</a>".$space;
        else
            $pagination .= "<a class=\"disabled\" href=\"".$targetpage.$pagestring1."\">«</a>".$space;
//            $pagination .= "<span class=\"disabled\">«</span>";    
        $pagination .= "&nbsp;&nbsp;&nbsp;";
        
        //pages 
        if ($lastpage < 7 + ($adjacents * 2))   //not enough pages to bother breaking it up
        {       
            for ($counter = 1; $counter <= $lastpage; $counter++)
            {
//                    $pagination .= "<span class=\"active\">$counter</span>";
                if ($counter == $page)
                    $pagination .= "<a class=\"active\" href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>".$space;                                     
                else
                    $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>".$space;                                     
            }
        }
        elseif($lastpage >= 7 + ($adjacents * 2))       //enough pages to hide some
        {
            //close to beginning; only hide later pages
            if($page < 1 + ($adjacents * 3))            
            {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                {
//                        $pagination .= "<span class=\"active\">$counter</span>";
                    if ($counter == $page)
                        $pagination .= "<a class=\"active\" href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>".$space;                                 
                    else
                        $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>".$space;                                 
                }
                $pagination .= "<span class=\"elipses\">...</span>".$space;
                $pagination .= "<a href=\"" . $targetpage . $pagestring . $lpm1 . "\">$lpm1</a>".$space;
                $pagination .= "<a href=\"" . $targetpage . $pagestring . $lastpage . "\">$lastpage</a>".$space;               
            }
            //in middle; hide some front and some back
            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "1\">1</a>".$space;
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "2\">2</a>".$space;
                $pagination .= "<span class=\"elipses\">...</span>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
//                        $pagination .= "<span class=\"active\">$counter</span>";
                    if ($counter == $page)
                        $pagination .= "<a class=\"active\" href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>".$space;                                  
                    else
                        $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>".$space;                                 
                }
                $pagination .= "...";
                $pagination .= "<a href=\"" . $targetpage . $pagestring . $lpm1 . "\">$lpm1</a>".$space;
                $pagination .= "<a href=\"" . $targetpage . $pagestring . $lastpage . "\">$lastpage</a>".$space;               
            }
            //close to end; only hide early pages
            else
            {
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "1\">1</a>".$space;
                $pagination .= "<a href=\"" . $targetpage . $pagestring . "2\">2</a>".$space;
                $pagination .= "<span class=\"elipses\">...</span>".$space;
                for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
                {
//                      $pagination .= "<span class=\"active\">$counter</span>";
                    if ($counter == $page)
                        $pagination .= "<a class=\"active\" href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>".$space;                                 
                    else
                        $pagination .= "<a href=\"" . $targetpage . $pagestring . $counter . "\">$counter</a>".$space;                                 
                }
            }
        }
        
        //next button
        $pagination .= "&nbsp;&nbsp;&nbsp;";
        if ($page < $counter - 1) 
            $pagination .= "<a href=\"" . $targetpage . $pagestring . $next . "\">»</a>".$space;
        else
            $pagination .= "<a class=\"disabled\" href=\"" . $targetpage . $pagestring . $lastpage . "\">»</a>".$space;
//            $pagination .= "<a class=\"disabled\" href=\"" . $targetpage . $pagestring . $next . "\">»</a>".$space;
//            $pagination .= "<span class=\"disabled\">»</span>";
        $pagination .= "</div>\n";
    }

    if ($pagination){
        return "<div class=\"clearfix ow_smallmargin\">".$pagination."</div>";
    }else{
        return $pagination;
    }

    }

    public function get_all_questions($idm=0)
    {
$id_user = OW::getUser()->getId();
        $ret="";
        if ($idm>0){
//            $questionService = BOL_QuestionService::getInstance();
            $user = BOL_UserService::getInstance()->findUserById($idm);
//        print_r($user);
            $accountType = $user->accountType;
            $questions =  BOL_QuestionService::getInstance()->findViewQuestionsForAccountType($accountType);
//            $questions =  BOL_QuestionService::getInstance()->findAllQuestions();

//print_r($questions);exit;

            $userIdList = array();
            array_push($userIdList, $idm);
//---aaa start
//getQuestionData



//$questionNameList = array();
//$section = null;
//$section = null;
//        $questionArray = array();
/*
        foreach ( $questions as $sort => $question )
        {
            if ( $section !== $question['sectionName'] )
            {
                $section = $question['sectionName'];
            }
 
            $questions[$sort]['hidden'] = false;
 
            if( !$questions[$sort]['onView'] )
            {
                $questions[$sort]['hidden'] = true;
            }
 
            $questionArray[$section][$sort] = $questions[$sort];
            $questionNameList[] = $questions[$sort]['name'];
        }
*/

/*
//$questionNameList[] = "sex";
$questionNameList[0] = "birthdate";
$questionNameList[1] = "15";
$questionNameList[2] = "relationship";
echo "xxx:";
print_r(BOL_QuestionService::getInstance()->findQuestionsValuesByQuestionNameList($questionNameList));
exit;
*/

//questions_question_1159a7f08df51508e06a1c1d6661dc9e_value_4
//echo  OW::getLanguage()->text('base', 'questions_question_1159a7f08df51508e06a1c1d6661dc9e_value_4');exit;
//echo  OW::getLanguage()->text('base', 'questions_question_1159a7f08df51508e06a1c1d6661dc9e_value_2');exit;
//echo  OW::getLanguage()->text('base', 'questions_question_relationship_value_15');exit;error
//echo  OW::getLanguage()->text('base', 'questions_question_sex_value_1');exit;
//echo  OW::getLanguage()->text('base', 'questions_question_relationship_value_1');exit;
/*
$userIdList[]=1;
$userIdList[]=2;
$qs[] = "birthdate";
$qs[] = "sex";
$qs[] = "relationship";
print_r(             $questionList = BOL_QuestionService::getInstance()->getQuestionData($userIdList, $qs));
exit;
*/
//---aaa end
//echo $questions[2]['onView'];exit;
//array_push($userIdList, 2);
//array_push($userIdList, 3);
            $qs=array();
            $qsd=array();
            for ($i=0;$i<count($questions);$i++){
//                if ($questions[$i]['onView'] AND ($questions[$i]['type']!="text" OR $questions[$i]['type']!="radio" OR $questions[$i]['type']!="date") ){
                if ($questions[$i]['onView'] AND ($questions[$i]['presentation']=="text" OR $questions[$i]['presentation']=="radio" OR $questions[$i]['presentation']=="date" OR $questions[$i]['presentation']=="select") ){
                    $qs[]=$questions[$i]['name'];

                    $qsd[$questions[$i]['name']]['name']=$questions[$i]['name'];
                    $qsd[$questions[$i]['name']]['type']=$questions[$i]['type'];
                    $qsd[$questions[$i]['name']]['presentation']=$questions[$i]['presentation'];
//echo $questions[$i]['name']."------------".$questions[$i]['type']."\n";
                }

            }
//print_r();
            $questionList = BOL_QuestionService::getInstance()->getQuestionData($userIdList, $qs);
//print_r($questionList);exit;
//print_r($qsd);
//exit;
//            for ($i=0;$i<count($questionList[1]);$i++){
//foreach ( $questionArray as $sectionKey => $section )
//            foreach ( $userIdList as $id )
            foreach ( $questionList as $uid => $q )
            {
//print_r( $q);exit;
                foreach ( $q as $uid2 => $q2 )
                {    
//echo $uid2."--".print_r($q2,1);exit;
//echo $uid2."--".$q2;exit;
//                    $ret .= "<div class=\"ow_left\">";
                    $retc="";
//                    $retc .= "<i>".OW::getLanguage()->text('base', 'questions_question_'.$uid2.'_label').":</i>";
                    if ($qsd[$uid2]['type']=="text" AND $qsd[$uid2]['presentation']=="text"){
                        if ($q2) $retc .= " <b>".$q2."</b>";
                    }else if ($qsd[$uid2]['type']=="select" AND $qsd[$uid2]['presentation']=="radio"){
                        $retc .= " <b>".OW::getLanguage()->text('base', 'questions_question_'.$uid2.'_value_'.$q2)."</b>";
                    }else if ($qsd[$uid2]['type']=="select" AND $qsd[$uid2]['presentation']=="select"){
                        $retc .= " <b>".OW::getLanguage()->text('base', 'questions_question_'.$uid2.'_value_'.$q2)."</b>";
//                    }else if ($qsd[$uid2]['type']=="text" AND $qsd[$uid2]['presentation']=="textarea"){
//                        $retc .= " <b>".OW::getLanguage()->text('base', 'questions_question_".$uid2."_value_'.$q2)."</b>";
//                    }else if ($qsd[$uid2]['type']=="select" AND $qsd[$uid2]['presentation']=="multicheckbox"){
//                        $retc .= " <b>".OW::getLanguage()->text('base', 'questions_question_".$uid2."_value_'.$q2)."</b>";
                    }else if ($qsd[$uid2]['type']=="select" AND $qsd[$uid2]['presentation']=="date"){
                        if ($q2) $retc .= " <b>".date("Y-m-d",$q2)."</b>";
                    }
//                    $retc .= "</div>";

                    if ($retc){
                        $ret .= "<div class=\"ow_left\">";
                        $ret .= "<i>".OW::getLanguage()->text('base', 'questions_question_'.$uid2.'_label').":</i>";
                        $ret .= $retc;
                        $ret .=";&nbsp;";
                        $ret .= "</div>";
                    }

/*
                    if ($q2>0){
                        $ret .= " <b>".BOL_QuestionService::getInstance()->getQuestionValueLang($uid2, $q2)."</b>";
//                        $ret .= " <b>".BOL_QuestionService::getInstance()->findQuestionValue($q2,$uid2)."</b>";
                    }else{
                        $ret .= " <b>".$q2."</b>";
                    }
*/
//                    

//echo OW::getLanguage()->text(self::QUESTION_LANG_PREFIX, $this->getQuestionLangKeyName(self::LANG_KEY_TYPE_QUESTION_VALUE, $uid2, $q2));
//echo "<hr>";

                }
//                echo "<i>".OW::getLanguage()->text('base', 'questions_question_'.$questionList[1]['name'].'_label').":</i>";
//                echo "<br>";
            }

        }
//echo "sss";exit;
//echo BOL_QuestionService::getInstance()->getQuestionValueLang('sex', 1);exit;
/*
$rr[]="f90cde5913235d172603cc4e7b9726e3";
$uu[]=1;
//echo " <b>".BOL_QuestionService::getInstance()->getQuestionValueLang($rr, $uu)."</b>";
print_r(BOL_QuestionService::getInstance()->getQuestionData($uu,$rr));

exit;
*/
        return $ret;
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