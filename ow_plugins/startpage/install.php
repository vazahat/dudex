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
//---notused
if ( !$config->configExists('startpage', 'backgrounc_cards_color') ){
    $config->addConfig('startpage', 'backgrounc_cards_color', '', '');
}
if ( !$config->configExists('startpage', 'border_cards_color') ){
    $config->addConfig('startpage', 'border_cards_color', '', '');
}
if ( !$config->configExists('startpage', 'show_startpage_only_friends') ){
    $config->addConfig('startpage', 'show_startpage_only_friends', '0', '');
}
if ( !$config->configExists('startpage', 'show_startpage_maxitems') ){
    $config->addConfig('startpage', 'show_startpage_maxitems', '10', '');
}
if ( !$config->configExists('startpage', 'show_profile_inwidget_startpage') ){
    $config->addConfig('startpage', 'show_profile_inwidget_startpage', '1', '');
}
if ( !$config->configExists('startpage', 'show_small_startpage_list') ){
    $config->addConfig('startpage', 'show_small_startpage_list', '0', '');
}

//---
if ( !$config->configExists('startpage', 'disable_startpage') ){
    $config->addConfig('startpage', 'disable_startpage', '0', '');
}
if ( !$config->configExists('startpage', 'theme_image_top') ){
    $config->addConfig('startpage', 'theme_image_top', '', '');
}
if ( !$config->configExists('startpage', 'theme_image_cover') ){
    $config->addConfig('startpage', 'theme_image_cover', '', '');
}
if ( !$config->configExists('startpage', 'theme_header_width') ){
    $config->addConfig('startpage', 'theme_header_width', '100%', '');
}
if ( !$config->configExists('startpage', 'theme_header_height') ){
    $config->addConfig('startpage', 'theme_header_height', '45px', '');
}
if ( !$config->configExists('startpage', 'theme_center_column') ){
    $config->addConfig('startpage', 'theme_center_column', '', '');
}
if ( !$config->configExists('startpage', 'theme_header_backgroundcolor') ){
    $config->addConfig('startpage', 'theme_header_backgroundcolor', '#009de0', '');
}
if ( !$config->configExists('startpage', 'theme_slogan') ){
    $config->addConfig('startpage', 'theme_slogan', '', '');
}
if ( !$config->configExists('startpage', 'theme_slogan_desc') ){
    $config->addConfig('startpage', 'theme_slogan_desc', '', '');
}
if ( !$config->configExists('startpage', 'curent_theme') ){
    $config->addConfig('startpage', 'curent_theme', 'twocolumn', '');
}

if ( !$config->configExists('startpage', 'theme_seo_title') ){
    $config->addConfig('startpage', 'theme_seo_title', 'Join Us', '');
}
if ( !$config->configExists('startpage', 'theme_seo_keywords') ){
    $config->addConfig('startpage', 'theme_seo_keywords', '', '');
}
if ( !$config->configExists('startpage', 'theme_seo_desc') ){
    $config->addConfig('startpage', 'theme_seo_desc', 'Join Us', '');
}
if ( !$config->configExists('startpage', 'force_for_guest') ){
    $config->addConfig('startpage', 'force_for_guest', '1', '');
}
if ( !$config->configExists('startpage', 'hide_accouttype') ){
    $config->addConfig('startpage', 'hide_accouttype', '0', '');
}
if ( !$config->configExists('startpage', 'logo_margin_left') ){
    $config->addConfig('startpage', 'logo_margin_left', '25', '');
}
if ( !$config->configExists('startpage', 'allow_upload_avatar') ){
    $config->addConfig('startpage', 'allow_upload_avatar', '1', '');
}

if ( !$config->configExists('startpage', 'widgetjavacode') ){
    $config->addConfig('startpage', 'widgetjavacode', '', '');
}
if ( !$config->configExists('startpage', 'toptitle') ){
    $config->addConfig('startpage', 'toptitle', 'Welcome to our website...', '');
}

if ( !$config->configExists('startpage', 'force_hide_homebutton') ){
    $config->addConfig('startpage', 'force_hide_homebutton', '0', '');
}
if ( !$config->configExists('startpage', 'background_color') ){
    $config->addConfig('startpage', 'background_color', '#fff', '');
}
if ( !$config->configExists('startpage', 'background_image') ){
    $config->addConfig('startpage', 'background_image', '', '');
}
if ( !$config->configExists('startpage', 'background_image_pos') ){
    $config->addConfig('startpage', 'background_image_pos', 'center center', '');
}
if ( !$config->configExists('startpage', 'disable_force_imagechache') ){
    $config->addConfig('startpage', 'disable_force_imagechache', '0', '');
}
if ( !$config->configExists('startpage', 'try_use_mytheme') ){
    $config->addConfig('startpage', 'try_use_mytheme', '0', '');
}
if ( !$config->configExists('startpage', 'allow_show_captha') ){
    $config->addConfig('startpage', 'allow_show_captha', '0', '');
}
if ( !$config->configExists('startpage', 'show_gender') ){
    $config->addConfig('startpage', 'show_gender', '0', '');
}
if ( !$config->configExists('startpage', 'show_agree_newsletter') ){
    $config->addConfig('startpage', 'show_agree_newsletter', '0', '');
}
if ( !$config->configExists('startpage', 'show_agree_therm_of_use') ){
    $config->addConfig('startpage', 'show_agree_therm_of_use', '0', '');
}
if ( !$config->configExists('startpage', 'therm_of_use_url') ){
    $config->addConfig('startpage', 'therm_of_use_url', 'terms-of-use', '');
}

if ( !$config->configExists('startpage', 'show_realname') ){
    $config->addConfig('startpage', 'show_realname', '1', '');
}
if ( !$config->configExists('startpage', 'show_eage') ){
    $config->addConfig('startpage', 'show_eage', '0', '');
}
if ( !$config->configExists('startpage', 'after_login_backto') ){
    $config->addConfig('startpage', 'after_login_backto', 'index', '');
}


if ( !$config->configExists('startpage', 'content_background') ){
    $config->addConfig('startpage', 'content_background', '#ffffff', '');
}
if ( !$config->configExists('startpage', 'content_text_color') ){
    $config->addConfig('startpage', 'content_text_color', '#555555', '');
}
if ( !$config->configExists('startpage', 'content_background_image') ){
    $config->addConfig('startpage', 'content_background_image', '', '');
}
if ( !$config->configExists('startpage', 'topbar_background') ){
    $config->addConfig('startpage', 'topbar_background', '#ffffff', '');
}
if ( !$config->configExists('startpage', 'topbar_text_color') ){
    $config->addConfig('startpage', 'topbar_text_color', '#555555', '');
}
if ( !$config->configExists('startpage', 'topbar_background_image') ){
    $config->addConfig('startpage', 'topbar_background_image', '', '');
}

/*
$dbPref = OW_DB_PREFIX;

$sql = "CREATE TABLE IF NOT EXISTS `".$dbPref."startpage` (
  `id` int(22) unsigned NOT NULL auto_increment,
  `id_owner` int(11) NOT NULL,
  `order_main` int(11) NOT NULL,
  `title` varchar(255) collate utf8_bin NOT NULL,
  `url_external` varchar(255) collate utf8_bin default NULL,
  `content` text collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";


OW::getDbo()->query($sql);
*/
/*
// Default section
$sql = "INSERT INTO `".$dbPref."forum_section`
    (`name`, `order`, `entity`, `isHidden`)
    VALUES ('General', 1, NULL, 0);";

$sectionId = OW::getDbo()->insert($sql);

if ( $sectionId )
{
    // Default group
    $sql = "INSERT INTO `".$dbPref."forum_group`
        (`sectionId`, `name`, `description`, `order`, `entityId`)
        VALUES (".$sectionId.", 'General Chat', 'Just about anything', 1, NULL);";

    $groupId = OW::getDbo()->insert($sql);
}


INSERT INTO `ow_gamesplus` (`id`, `title`, `code`) VALUES
(1, 'Sniper Olimpic', '<embed src="http://games.mochiads.com/c/g/sniper-olimpyc/SniperOlimpyc.swf" menu="false" quality="high" width="512" height="512" t
(2, 'Zombie Disposal', '<embed src="http://games.mochiads.com/c/g/zombie-disposal/ZombieDisposal_Mochi.swf" menu="false" quality="high" width="650" heigh

*/


//----main:
//BOL_LanguageService::getInstance()->addPrefix('startpage', 'Start Page');
OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('startpage')->getRootDir().'langs.zip', 'startpage');
OW::getPluginManager()->addPluginSettingsRouteName('startpage', 'startpage.admin');
