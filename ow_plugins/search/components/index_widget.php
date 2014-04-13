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


class SEARCH_CMP_IndexWidget extends BASE_CLASS_Widget
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
            self::SETTING_TITLE => OW::getLanguage()->text('search', 'index_page_title') // Set the widget title 
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
        $content .= "<form id=\"searchform\" metod=\"get\" action=\"".$curent_url."query\" style=\"display:inline;\">";
//        $content .= "<input style=\"display:block;min-width:200px;font-size:110%;\" type=\"text\" name=\"query\" value=\"".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."\" onblur=\"if(this.value == '') { this.value='".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."'};\" onfocus=\"if (this.value == '".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."') {this.value=''};\" autocomplete=\"off\" spellcheck=\"false\" >";
        $content .= "<input style=\"display:block;min-width:120px;width:100%;font-size:110%;\" type=\"text\" name=\"query\" value=\"".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."\" onblur=\"if(this.value == '') { this.value='".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."'};\" onfocus=\"if (this.value == '".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."') {this.value=''};\" autocomplete=\"off\" spellcheck=\"false\" >";
        $content .= "</form>";
//        $content = $this->nl2br ? nl2br( $this->content ) : $this->content;
        $this->assign('content', $content);
    }
}