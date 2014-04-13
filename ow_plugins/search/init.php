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

$config = OW::getConfig();

if ( !$config->configExists('search', 'turn_offplugin_map') ){
    $config->addConfig('search', 'turn_offplugin_map', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_wiki') ){
    $config->addConfig('search', 'turn_offplugin_wiki', "1", '');
}
if ( !$config->configExists('search', 'allow_ads_adsense') ){
    $config->addConfig('search', 'allow_ads_adsense', "1", '');
}
if ( !$config->configExists('search', 'allow_ads_adspro') ){
    $config->addConfig('search', 'allow_ads_adspro', "0", '');
}
if ( !$config->configExists('search', 'allow_ads_ads') ){
    $config->addConfig('search', 'allow_ads_ads', "1", '');
}

/*
$config = OW::getConfig();

if ( !$config->configExists('search', 'search_position') ){
    $config->addConfig('search', 'search_position', "absolute", '');
}

if ( !$config->configExists('search', 'height_topsearchbar') ){
    $config->addConfig('search', 'height_topsearchbar', "25", '');
}

if ( !$config->configExists('search', 'horizontal_position') ){
    $config->addConfig('search', 'horizontal_position', "0", 'position horizontal');
}
if ( !$config->configExists('search', 'vertical_position') ){
    $config->addConfig('search', 'vertical_position', "0", 'vertical_position');
}
if ( !$config->configExists('search', 'zindex_position') ){
    $config->addConfig('search', 'zindex_position', "0", 'zindex_position');
}
if ( !$config->configExists('search', 'width_topsearchbar') ){
    $config->addConfig('search', 'width_topsearchbar', "250", 'width_topsearchbar');
}
if ( !$config->configExists('search', 'turn_off_topsearchbar') ){
    $config->addConfig('search', 'turn_off_topsearchbar', "0", '');
}
*/
/*
$config = OW::getConfig();
if ( !$config->configExists('search', 'hmanyitems_show_topsearchbarlist') ){
    $config->addConfig('search', 'hmanyitems_show_topsearchbarlist', "6", '');
}
if ( !$config->configExists('search', 'maxallitems_topsearchbarlist') ){
    $config->addConfig('search', 'maxallitems_topsearchbarlist', "12", '');
}
*/


/*
OW::getRouter()->addRoute(
    new OW_Route('widgetplus.admin_sliders', 'admin/plugin/widgetplus', 'WIDGETPLUS_CTRL_Admin', 'index')
);

OW::getRouter()->addRoute(
    new OW_Route('widgetplus.admin_settings', 'admin/plugin/widgetplus/settings', 'WIDGETPLUS_CTRL_Admin', 'settings')
);
*/


OW::getRouter()->addRoute(new OW_Route('search', 'search', "SEARCH_CTRL_Query", 'index'));
OW::getRouter()->addRoute(new OW_Route('searchmmbers', 'members', "SEARCH_CTRL_Querymembers", 'index'));
OW::getRouter()->addRoute(new OW_Route('searchmmbersopt', 'members/:option', "SEARCH_CTRL_Querymembers", 'index'));
OW::getRouter()->addRoute(new OW_Route('searchopt', 'query/:option', "SEARCH_CTRL_Query", 'index'));
OW::getRouter()->addRoute(new OW_Route('search.index', 'query', "SEARCH_CTRL_Query", 'index'));
OW::getRouter()->addRoute(new OW_Route('search.start', 'search', "SEARCH_CTRL_Query", 'index'));
OW::getRouter()->addRoute(new OW_Route('search.indexquick', 'qquery', "SEARCH_CTRL_Query", 'ajax_search'));

OW::getRouter()->addRoute(new OW_Route('search.admin', 'admin/plugins/search', "SEARCH_CTRL_Admin", 'dept'));



$registry = OW::getRegistry();
//$registry->addToArray(BASE_CMP_ConnectButtonList::HOOK_REMOTE_AUTH_BUTTON_LIST, array(new SEARCHPLUS_CMP_ConnectButton(), 'render'));


/*
function toconsolex( BASE_CLASS_EventCollector $e ){
///echo "sssssssss";
//$e->member();

$e->add(
        array(
            BASE_CMP_Console::DATA_KEY_URL => 'javascript://',
            BASE_CMP_Console::DATA_KEY_ICON_CLASS => 'new_mail ow_ic_chat',
            BASE_CMP_Console::DATA_KEY_BLOCK_CLASS => 'main_im_tab_container',
            BASE_CMP_Console::DATA_KEY_BLOCK => true,
            BASE_CMP_Console::DATA_KEY_ID => 'main_im_tab',
            BASE_CMP_Console::DATA_KEY_ITEMS_LABEL => OW::getLanguage()->text('ajaxim', 'chat')
        )
    );
}
*/

function toconsolex(BASE_EventCollector $e ){
/*
$e->add(
            array(
                BASE_CMP_Console::DATA_KEY_URL => OW::getRouter()->urlForRoute('mailbox_default'),
                BASE_CMP_Console::DATA_KEY_ICON_CLASS => 'ow_ic_mail',
                BASE_CMP_Console::DATA_KEY_TITLE => OW::getLanguage()->text('mailbox', 'mailbox'),
            )
        );
*/
$curent_url=OW_URL_HOME;
$config = OW::getConfig();
$id_user = OW::getUser()->getId();
$is_admin = OW::getUser()->isAdmin();

//function current_url() {
    //return sprintf("http://%s%s",$_SERVER["HTTP_HOST"],$_SERVER["REQUEST_URI"]);
  //}
if ($id_user>0){
//$curent_url .="photo/";
if (OW::getConfig()->getValue('search', 'turn_off_topsearchbar')!=1){
$url_spr=  sprintf("http://%s%s",$_SERVER["HTTP_HOST"],$_SERVER["REQUEST_URI"]);
$url_spr=substr($url_spr,strlen($curent_url),strlen($url_spr));
list($url_spr)=explode("/",$url_spr);
//echo $url_spr;exit;
if ($url_spr!="admin"){



$width_topsearchbar=OW::getConfig()->getValue('search', 'width_topsearchbar');
if (!$width_topsearchbar) $width_topsearchbar=350;
if ($width_topsearchbar<60) $width_topsearchbar=60;

$height_topsearchbar=OW::getConfig()->getValue('search', 'height_topsearchbar');
if (!$height_topsearchbar) $height_topsearchbar=22;

$left_margin=OW::getConfig()->getValue('search', 'horizontal_position');
if ($left_margin=="" OR $left_margin==0) {
    $left_margin_tab="margin-left:-300px;";
}else{
    $left_margin_tab=" margin-left:".$left_margin."px;";
}

$top_margin=OW::getConfig()->getValue('search', 'vertical_position');
if ($top_margin!="0" AND $top_margin!="") $top_margin=" margin-top:".$top_margin."px;";
    else $top_margin="";

$search_position=OW::getConfig()->getValue('search', 'search_position');
if (!$search_position) {
    $search_position_bar="position:absolute;";
}else {
    $search_position_bar="position:".$search_position.";";
}

if ($search_position=="absolute"){
    $left_margin_tab="margin-left:-300px;";
    $search_position_bar .="left:".$left_margin."px;";
    
}
//$search_position_bar="";

//if ($search_position=="absolute"){
//    $with_console_search=$width_topsearchbar;
//}else{
    $with_console_search="1";
//}


$z_index=OW::getConfig()->getValue('search', 'zindex_position');
if ($z_index!="" AND $z_index!="0") $z_index="z-index:".$z_index.";";
    else $z_index=" z-index:99;";


if ($config->getValue('search', 'search_position')=="oxwall15"){
    if ($left_margin!=""){
        $ml=$left_margin;
    }else{
        $ml=325;
    }
/*
    if (!$is_admin){
        if (!$ml) $ml=0;
        $ml=$ml-70;
    }
*/
    $style_main="width:".$width_topsearchbar."px;display:inline-block;margin-top:0px;margin-right:".$ml."px;".$z_index."   background:none;border:0;";
    $class_main="ow_console_item ow_console_dropdownC";

    $style_console="width:".$width_topsearchbar."px;height:".($height_topsearchbar+2)."px;        padding: 0;margin: 0;border:0;";
    $class_console="ow_console_body";
//    $style_input="position:relative;display:inline-block;float:left;left:3px;top:-1px;width:".($width_topsearchbar-40)."px;font-size:120%;";


//-----ousde
//    $style_input="position:relative;display:inline-block;float:left;left:3px;top:0px;width:".($width_topsearchbar-40)."px;max-height:".($height_topsearchbar+1)."px;font-size:120%;";
//----inline
    $style_input="text-indent: 20px;position:relative;display:inline-block;float:left;left:3px;top:0px;width:".($width_topsearchbar)."px;max-height:".($height_topsearchbar+1)."px;font-size:120%;";
}else{
    $style_main=$search_position_bar.$z_index."width:".$with_console_search."px;margin:auto;";
    $class_main="ow_console clearfix";

    $style_console="width:".$width_topsearchbar."px;".$left_margin_tab.$top_margin;
    $class_console="";
//-----outside
//    $style_input="position:relative;display:inline-block;float:left;left:3px;top:1px;width:".($width_topsearchbar-40)."px;font-size:120%;";
//----inline
    $style_input="position:relative;display:inline-block;float:left;left:3px;top:1px;width:".($width_topsearchbar)."px;font-size:120%;";
}


$content_seaerch="";

//$content_seaerch .= "<div class=\"ow_console clearfix\" style=\"".$style_main."\">";
//$content_seaerch .= "<div class=\"ow_console_item ow_console_dropdownC\" style=\"".$style_main."\">";
$content_seaerch .= "<div class=\"".$class_main."\" style=\"".$style_main."\">";

    $content_seaerch .= "<div class=\"".$class_console." console_item common_shortcuts\" style=\"".$style_console."\">";


//        $content_seaerch .= "<div class=\"ow_ic_lens\" style=\"width:16px;height:16px;margin:3px;position:relative;display:inline-block;float:left;top:4px;background-position: center;background-repeat: no-repeat;\">&nbsp;</div>";

//----outside
//        $content_seaerch .= "<div class=\"ow_ic_lens\" style=\"width:16px;height:16px;margin:3px;position:relative;display:inline-block;float:left;top:1px;background-position: center;background-repeat: no-repeat;\">&nbsp;</div>";
//----inline
        $content_seaerch .= "<div class=\"ow_ic_lens\" style=\"width:16px;height:16px;margin:3px;position:absolute;left: 10px;z-index: 9;display:inline-block;float:left;background-position: center;background-repeat: no-repeat;\">&nbsp;</div>";


$content_seaerch .= "<div class=\"fake_node\" id=\"main_im_tab_container0\">";
//        echo "<div class=\"ow_ic_lens\" style=\"width:16px;height:16px;margin:3px;position:relative;display:inline-block;float:left;top:4px;\">&nbsp;</div>";
        $content_seaerch .= "<form id=\"searchform\" metod=\"get\" action=\"".$curent_url."query\" style=\"display:inline;\">";
//        echo "<input style=\"margin:1px;auto;padding:auto;position:relative;display:inline-block;float:left;left:3px;top:1px;width:".($width_topsearchbar-40)."px;font-size:120%;\" type=\"text\" id=\"query\" name=\"query\" value=\"".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."\" onblur=\"if(this.value == '') { this.value='".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."'};\" onfocus=\"if (this.value == '".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."') {this.value=''};\" autocomplete=\"off\" spellcheck=\"false\" >";
        if (OW::getConfig()->getValue('search', 'hmanyitems_show_topsearchbarlist')>0){
            $content_seaerch .= "<input style=\"".$style_input."\" type=\"text\" id=\"query\" name=\"query\" value=\"".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."\" onblur=\"if(this.value == '') { this.value='".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."'};\" onfocus=\"if (this.value == '".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."') {this.value=''};\" autocomplete=\"off\" spellcheck=\"false\" >";
        }else{
            $content_seaerch .= "<input style=\"".$style_input."\" type=\"text\" id=\"query_d\" name=\"query\" value=\"".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."\" onblur=\"if(this.value == '') { this.value='".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."'};\" onfocus=\"if (this.value == '".stripslashes(OW::getLanguage()->text('search', 'tips_default_sugestion'))."') {this.value=''};\" autocomplete=\"off\" spellcheck=\"false\" >";
        }
        $content_seaerch .= "</form>";
//        echo "<div id=\"aron_results_qqsearch\" style=\"width:100%;meight:300px;border:1px solid #eee;position:absolute;z-index:9999995;margin-top:30px;background-color:#fff;display:none;\">&nbsp;</div>";

$content_seaerch .= "</div>";
        
//        $content_seaerch .= "<div id=\"aron_results_qqsearch\" style=\"min-width:".($width_topsearchbar-30)."px;margin:auto;border:1px solid #eee;position:absolute;z-index:9999995;margin-top:30px;background-color:#fff;display:none;\">&nbsp;</div>";
//        $content_seaerch .= "<div class=\"ow_content\" id=\"aron_results_qqsearch\" style=\"min-width:".($width_topsearchbar-30)."px;width: auto;margin:auto;border:1px solid #eee;position:absolute;z-index:9999995;margin-top:30px;background-color:#fff;display:none;\">&nbsp;</div>";


//        $content_seaerch .= "<div class=\"ow_content\" id=\"aron_results_qqsearch\" style=\"min-width:".($width_topsearchbar-30)."px;width: auto;margin:auto;border:1px solid #eee;position:absolute;z-index:9999995;margin-top:30px;background-color:#fff;display:none;\">&nbsp;</div>";

//display: block;
//display:none;
//margin-top:30px;
//border:1px solid #eee;
//position:absolute;
//z-index:9999995;


if (OW::getConfig()->getValue('search', 'bg_results_topsearchbar')){
    $bg_results_topsearchbar="background-color: ".OW::getConfig()->getValue('search', 'bg_results_topsearchbar').";";
}else{
    $bg_results_topsearchbar="";
}

$content_seaerch .= "<div id=\"aron_results_qqsearch_main\" class=\"OW_ConsoleItemContent ow_content\" 
style=\"display: none;
min-width:".($width_topsearchbar-30)."px;
width: auto;
margin:auto;
border:0;
position:absolute;
z-index:0;
background-color:transparent;
\">
    <div class=\"ow_tooltip  console_tooltip ow_tooltip_top_right\" style=\"opacity: 1; top: 22px;\">

        <div class=\"ow_tooltip_tail\">
            <span></span>
        </div>
        <div class=\"ow_tooltip_body\" id=\"aron_results_qqsearch\" style=\"".$bg_results_topsearchbar."\">";
/*        
                <ul class=\"ow_console_dropdown\">
                        <li class=\" ow_dropdown_menu_item ow_cursor_pointer\">
                <div class=\"ow_console_dropdown_cont\">
                    <a href=\"http://mycollegesocial.com/user/admin\" class=\"hint-target\">My Profile</a>
                </div>
            </li>
                    <li class=\" ow_dropdown_menu_item ow_cursor_pointer\">
                <div class=\"ow_console_dropdown_cont\">
                    <a href=\"http://mycollegesocial.com/profile/edit\">Profile Edit</a>
                </div>
            </li>
                    <li class=\" ow_dropdown_menu_item ow_cursor_pointer\">
                <div class=\"ow_console_dropdown_cont\">
                    <a href=\"http://mycollegesocial.com/profile/preference\">My Preferences</a>
                </div>
            </li>
                    <li class=\" ow_dropdown_menu_item ow_cursor_pointer\">
                <div class=\"ow_console_dropdown_cont\">
                    <a href=\"http://mycollegesocial.com/uploaded-video/latest\">Videos Upload</a>
                </div>
            </li>
                    <li class=\" ow_dropdown_menu_item ow_cursor_pointer\">
                <div class=\"ow_console_dropdown_cont\">
                    <a href=\"http://mycollegesocial.com/profile/privacy\">Privacy</a>
                </div>
            </li>
        
                    <li><div class=\"ow_console_divider\"></div></li>
                                <li class=\" ow_dropdown_menu_item ow_cursor_pointer\">
                <div class=\"ow_console_dropdown_cont\">
                    <a href=\"http://mycollegesocial.com/sign-out\">Sign Out</a>
                </div>
            </li>
        
            </ul>
*/
            
    $content_seaerch .= "</div>

    </div>
</div>";






    $content_seaerch .= "</div>";

//echo "</div>";
$content_seaerch .= "</div>";



if ($config->getValue('search', 'search_position')!="oxwall15"){
    echo $content_seaerch;
}else{
    $content_seaerch=str_replace("\r\n"," ",$content_seaerch);
    $content_seaerch=str_replace("\r"," ",$content_seaerch);
    $content_seaerch=str_replace("\n"," ",$content_seaerch);
    $content_seaerch=str_replace("'","\\'",$content_seaerch);
}

$script="";

$script .= "<style media=\"all\">\n
.aron_dropdown_hover:hover{background-color:#eee;color:#222;}\n
</style>\n";


$script .= "<script type=\"text/javascript\">\n";
//echo "$('h2').insertBefore($('.container'));";
$script .= "
$(document).ready(function() {
";

if ($config->getValue('search', 'search_position')=="oxwall15"){
    $script .= "$('.ow_console .ow_console_body div div').first().before('".$content_seaerch."'); ";
//    $('.ow_console .ow_console_body div').first().append('".$content_seaerch."');
//    $('.ow_console .ow_console_body DIV').html('ssss<hr/>');
}


$script .= "  mouse_is_inside=true; 
    $('#search_submit_more').click(function() {
        alert('sss');
    });

//    $('#query').keypress(function(event) {
    $('#query').keyup(function(event) {
        var valuekey = event.charCode;
        var c = String.fromCharCode(event.which);
        if ($(this).val().length>1){
//alert(valuekey+'--'+c);


    $.ajax({
        type     : 'POST',
        url      : '".$curent_url."qquery',
        data     : {
            query : $(this).val(),
            action : 'search'
        },
        success : function(msg) {
            $('#aron_results_qqsearch').html(msg);
            $('#aron_results_qqsearch').show();
$('#aron_results_qqsearch_main').show();
        },
        complete : function(r) {
            $('#loading').hide();
        },
        error:    function(error) {
        }
    });

        }else{
            $('#aron_results_qqsearch').hide();
$('#aron_results_qqsearch_main').hide();
        }
    }).on('keydown', function(e) {
        if (e.keyCode==8){
            $('#query').trigger('keypress');
        }else if (e.keyCode==13){
            $('#aron_results_qqsearch').hide();
$('#aron_results_qqsearch_main').hide();
            $('#searchform').attr('action', '".$curent_url."query/search');
            $('#searchform').submit();
        
        }
     });


    $('#query').focusout(function() {
        if(!mouse_is_inside) {
            $('#aron_results_qqsearch').hide();
$('#aron_results_qqsearch_main').hide();
        }
    });


    $('#aron_results_qqsearch').mouseenter(function(){
        mouse_is_inside=true; 
    }).mouseleave(function(){
        mouse_is_inside=false; 
    });

    $('#aron_results_qqsearch').mouseover(function(){
        mouse_is_inside=true; 
    }).mouseout(function(){
        mouse_is_inside=false; 
    });




})
";
//echo "function querychck(){"
//echo "if (query.length>2){";
//echo "alert(query);";
//echo "}";
//echo "}";
$script .= "</script>\n";

//echo $script;
ow::getDocument()->appendBody($script);



/*
$con="<input tyle=\"text\" style=\"width:250px;\" name=\"query\" value=\"Wyszukaj osoby, miejsca i inne...\"  onblur=\"if(this.value == '') { this.value='Wyszukaj osoby, miejsca i inne...'}\" onfocus=\"if (this.value == 'Wyszukaj osoby, miejsca i inne...') {this.value=''}\" autocomplete=\"off\" spellcheck=\"false\" >";
$e->add(
            array(
//                BASE_CMP_Console::DATA_KEY_URL => OW::getRouter()->urlForRoute('mailbox_default'),
//                BASE_CMP_Console::DATA_KEY_URL => 'javascript://',
                BASE_CMP_Console::DATA_KEY_URL => '',

//                BASE_CMP_Console::DATA_KEY_ICON_CLASS => 'ow_ic_mail',
//                BASE_CMP_Console::DATA_KEY_ICON_CLASS => 'ow_ic_file',
                BASE_CMP_Console::DATA_KEY_ICON_CLASS => 'ow_ic_lens',

                BASE_CMP_Console::DATA_KEY_TITLE => OW::getLanguage()->text('searchplus', 'Quick_Search'),
                BASE_CMP_Console::DATA_KEY_ITEMS_LABEL => $con,
                BASE_CMP_Console::DATA_KEY_ID => 'main_aron_tab',

                BASE_CMP_Console::DATA_KEY_BLOCK => true,
                BASE_CMP_Console::DATA_KEY_BLOCK_ID =>'main_aron_search',

//                BASE_CMP_Console::DATA_KEY_HIDDEN_CONTENT => 'bbbbbbb',

//                BASE_CMP_Console::DATA_KEY_BLOCK_CLASS => 'ow_mild_green'
                BASE_CMP_Console::DATA_KEY_BLOCK_CLASS => 'main_aron_tab_container',
//                BASE_CMP_Console::DATA_KEY_ITEMS_LABEL => OW::getLanguage()->text('searchplus', 'Quick_Search')
            )
        );
*/
    }//if not admin
    }//if not off
    }//if $id_user >0
}
//$registry->addToArray(BASE_CMP_Console, array(new SEARCHPLUS_CMP_ConnectButton(), 'render'));
//OW::getEventManager()->bind(BASE_CMP_Console::EVENT_NAME, 'toconsolex');
//$registry->addToArray(BASE_CMP_Console::EVENT_NAME , array(new SEARCHPLUS_CMP_ConnectButton(), 'render'));
//$console=new BASE_CMP_Console();
//->addComponent('render', new SEARCHPLUS_CMP_ConnectButton());

OW::getEventManager()->bind(BASE_CMP_Console::EVENT_NAME, 'toconsolex');




//$registry->addToArray(
//$aa=new BASE_CMP_Console();
//$this->addComponent('switchLanguage', new BASE_CMP_SwitchLanguage());
