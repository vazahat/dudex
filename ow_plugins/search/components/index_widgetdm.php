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


class SEARCH_CMP_IndexWidgetdm extends BASE_CLASS_Widget
{
    private $content = false;
    private $nl2br = false;
 
    public function __construct( BASE_CLASS_WidgetParameter $paramObject )
    {
        parent::__construct();
 /*
        $params = $paramObject->customParamList;
 
        if ( !empty($params['content']) )
        {
            $this->content = $paramObject->customizeMode && !empty($_GET['disable-js']) ? UTIL_HtmlTag::stripJs($params['content']) : $params['content'];
        }
 
        if ( isset($params['nl_to_br']) )
        {
            $this->nl2br = (bool) $params['nl_to_br'];
        }
*/
    }
 
    public static function getSettingList() // If you redefine this method, you'll be able to add fields to the widget configuration form 
    {
        $settingList = array();
/*
        $settingList['content'] = array(
            'presentation' => self::PRESENTATION_TEXTAREA, // Field type
            'label' => OW::getLanguage()->text('base', 'custom_html_widget_content_label'), // Field name
            'value' => '' // Default value
        );
 
        $settingList['nl_to_br'] = array(
            'presentation' => self::PRESENTATION_CHECKBOX,
            'label' => OW::getLanguage()->text('base', 'custom_html_widget_nl2br_label'),
            'value' => '0'
        );
 */
        return $settingList;
    }
 /*
    public static function processSettingList( $settings, $place ) // This method is called before saving the widget settings. Here you can process the settings entered by a user before saving them. 
    {
        if ( $place != BOL_ComponentService::PLACE_DASHBOARD && !OW::getUser()->isAdmin() )
        {
            $settings['content'] = UTIL_HtmlTag::stripJs($settings['content']);
            $settings['content'] = UTIL_HtmlTag::stripTags($settings['content'], array('frame'), array(), true, true);
        }
        else
        {
            $settings['content'] = UTIL_HtmlTag::sanitize($settings['content']);
        }
 
        return $settings;
    }

 
    public static function getStandardSettingValueList() // If you redefine this method, you will be able to set default values for the standard widget settings. 
    {


        return array(
            self::SETTING_TITLE => OW::getLanguage()->text('search', 'index_page_title'), // Set the widget title 
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_ICON => 'ow_ic_lens'  
        );
    }
 */
    public static function getStandardSettingValueList() // If you redefine this method, you will be able to set default values for the standard widget settings. 
    {
        return array(
            self::SETTING_TITLE => OW::getLanguage()->text('search', 'widget_title_search_members') // Set the widget title 
        );
    }
    public static function getAccess() // If you redefine this method, you'll be able to manage the widget visibility 
    {
        return self::ACCESS_ALL;
    }
 
    public function onBeforeRender() // The standard method of the component that is called before rendering
    {
        $content="";
        $curent_url=OW_URL_HOME;
/*
//        $content .= "<form id=\"searchform\" metod=\"get\" action=\"".$curent_url."query\" style=\"display:inline;\">";
//        $content .= "<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\" style=\"display:inline;\">";
        $content .= "<form metod=\"get\" action=\"".$curent_url."query\" style=\"display:inline;\">";
$content .= "<div class=\"clearfix ow_center\">";
        $content .= "<input class=\"ow_left\" style=\"margin:auto;display:inline-block;width:99%;min-width:125px;font-size:105%;\" type=\"text\" name=\"query\" value=\"".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion_search'))."\" onblur=\"if(this.value == '') { this.value='".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion_search'))."'};\" onfocus=\"if (this.value == '".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion_search'))."') {this.value=''};\" autocomplete=\"off\" spellcheck=\"false\" >";
//        $content .="<input class=\"ow_ic_lens ow_right\" style=\"display:inline;background-repeat: no-repeat;background-position: center center;\" type=\"submit\" value=\"&nbsp;\" title=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\">";
//        $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//        $content .="<span class=\"ow_button ow_right\" style=\"display:inline-block;\">
//                        <input style=\"max-height:25px;\" type=\"submit\" value=\"\" title=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\" style=\"background-repeat: no-repeat;background-position: center center;\">
//                </span>";
$content .= "</div>";
$content .= "</form>";
*/
//$content .= "<h3 class=\"clearfix ow_left\" style=\"display:inline-block;\">".OW::getLanguage()->text('search', 'search_members').":</h3>";


//        $content .= "<form metod=\"get\" action=\"".$curent_url."query/user\" style=\"display:inline;\">";

$content_s="";
$content_s .= "<div class=\"clearfix ow_center\" style=\"margin-bottom:10px;\">";
//        $content_s .= "<input class=\"ow_left\" style=\"margin:auto;display:inline-block;width:75%;min-width:125px;font-size:105%;\" type=\"text\" name=\"query\" value=\"".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."\" onblur=\"if(this.value == '') { this.value='".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."'};\" onfocus=\"if (this.value == '".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."') {this.value=''};\" autocomplete=\"off\" spellcheck=\"false\" >";
        $content_s .= "<input class=\"ow_left\" style=\"margin:auto;display:inline-block;width:70%;min-width:105px;font-size:105%;\" type=\"text\" name=\"query\" value=\"\" spellcheck=\"false\" >";

//        $content .="<input class=\"ow_ic_lens ow_right\" style=\"display:inline;background-repeat: no-repeat;background-position: center center;\" type=\"submit\" value=\"&nbsp;\" title=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\">";


//        $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
        $content_s .="<span class=\"ow_button ow_right\" style=\"display:inline-block;min-width: 36px;max-width:50px;padding: 0;\">
                        <input style=\"max-height:25px;min-width: 36px;margin: 0;background-position: center center;padding: 0px;background-position: center;\" type=\"submit\" value=\"\" title=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\" style=\"background-repeat: no-repeat;background-position: center center;\">
                </span>";
$content_s .= "</div>";
//$content .= "</form>";

//----atrat start
                $sql = "SELECT * FROM " . OW_DB_PREFIX. "base_question WHERE onSearch='1' ORDER BY sortOrder ";
                $arr1 = OW::getDbo()->queryForList($sql);
                $enter=1;
                $inline=3;
$add_opt="";
                foreach ( $arr1 as $value )
                {
//                    $add_opt .=$value['name']."--".$value['type']."-".$value['presentation']."<hr>";
                    if ($value['type']=="select"){
                        if ($add_opt) $add_opt .="&nbsp; ";
$add_opt .="<div class=\"clearfix  ow_boxx\" style=\"padding-right:5px;margin:auto;min-width:150px;display:inline-block;width:100%;\">";
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


/*
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



                    }//text

*/


                }//for

                $content_f="";
//                if ($add_opt){
//                    $content_f .="<form metod=\"get\" action=\"".$curent_url."query/".$option.$add."\">";
//                    $content_f .="<input type=\"text\" name=\"query\" value=\"".$query."\" style=\"width:80%;\">";
//                    $content_f .="<input type=\"submit\" name=\"\" value=\"".OW::getLanguage()->text('search', 'search')."\">";
//                    $content_f .="&nbsp;";
//                    $content_f .="<span class=\"ow_button ow_ic_lens\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<span class=\"ow_button\"><span><input type=\"submit\" value=\"".OW::getLanguage()->text('search', 'search')."\" id=\"btn-add-new-photo\" class=\"ow_ic_lens\"></span></span>";
//                    $content_f .="<br/>";
                    $content_f .= "<form metod=\"get\" action=\"".$curent_url."query/user\" style=\"display:inline;\">";
                    $content_f .="<div clas=\"clearfix\" style=\"margin-top:10px;min-height:0;\">";
                    $content_f .=$content_s;
                    if ($add_opt){
                        $content_f .=$add_opt;
                    }
                    $content_f .="</div>";
                    $content_f .= "</form>";
//                    $content_f .="<hr/>";
//                    $content_f .="</form>";
//                }
                $content .=$content_f;
//----atrat end



        $content .= "</form>";
//        $content = $this->nl2br ? nl2br( $this->content ) : $this->content;
        $this->assign('content', $content);
    }
}