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



$config = OW::getConfig();

if ( !$config->configExists('search', 'horizontal_position') ){
    $config->addConfig('search', 'horizontal_position', "325", 'position horizontal');
}
if ( !$config->configExists('search', 'vertical_position') ){
    $config->addConfig('search', 'vertical_position', "0", 'vertical_position');
}
if ( !$config->configExists('search', 'zindex_position') ){
    $config->addConfig('search', 'zindex_position', "99", 'zindex_position');
}
if ( !$config->configExists('search', 'width_topsearchbar') ){
    $config->addConfig('search', 'width_topsearchbar', "250", 'width_topsearchbar');
}
if ( !$config->configExists('search', 'turn_off_topsearchbar') ){
    $config->addConfig('search', 'turn_off_topsearchbar', "0", '');
}
if ( !$config->configExists('search', 'hmanyitems_show_topsearchbarlist') ){
    $config->addConfig('search', 'hmanyitems_show_topsearchbarlist', "3", '');
}
if ( !$config->configExists('search', 'maxallitems_topsearchbarlist') ){
    $config->addConfig('search', 'maxallitems_topsearchbarlist', "8", '');
}

if ( !$config->configExists('search', 'search_force_users') ){
    $config->addConfig('search', 'search_force_users', "2", '');
}

if ( !$config->configExists('search', 'search_position') ){
//    $config->addConfig('search', 'search_position', "absolute", '');
    $config->addConfig('search', 'search_position', "oxwall15", '');
}
if ( !$config->configExists('search', 'height_topsearchbar') ){
    $config->addConfig('search', 'height_topsearchbar', "25", '');
}
if ( !$config->configExists('search', 'turn_offplugin_cms') ){
    $config->addConfig('search', 'turn_offplugin_cms', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_forum') ){
    $config->addConfig('search', 'turn_offplugin_forum', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_links') ){
    $config->addConfig('search', 'turn_offplugin_links', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_video') ){
    $config->addConfig('search', 'turn_offplugin_video', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_photo') ){
    $config->addConfig('search', 'turn_offplugin_photo', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_shoppro') ){
    $config->addConfig('search', 'turn_offplugin_shoppro', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_classifiedspro') ){
    $config->addConfig('search', 'turn_offplugin_classifiedspro', "1", '');
}
if ( !$config->configExists('search', 'turn_offplugin_pages') ){
    $config->addConfig('search', 'turn_offplugin_pages', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_groups') ){
    $config->addConfig('search', 'turn_offplugin_groups', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_blogs') ){
    $config->addConfig('search', 'turn_offplugin_blogs', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_event') ){
    $config->addConfig('search', 'turn_offplugin_event', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_fanpage') ){
    $config->addConfig('search', 'turn_offplugin_fanpage', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_html') ){
    $config->addConfig('search', 'turn_offplugin_html', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_games') ){
    $config->addConfig('search', 'turn_offplugin_games', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_adsense') ){
    $config->addConfig('search', 'turn_offplugin_adsense', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_mochigames') ){
    $config->addConfig('search', 'turn_offplugin_mochigames', "1", '');
}
if ( !$config->configExists('search', 'turn_offplugin_basepages') ){
    $config->addConfig('search', 'turn_offplugin_basepages', "1", '');
}
if ( !$config->configExists('search', 'turn_offplugin_adspro') ){
    $config->addConfig('search', 'turn_offplugin_adspro', "1", '');
}
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
if ( !$config->configExists('search', 'turn_offplugin_news') ){
    $config->addConfig('search', 'turn_offplugin_news', "0", '');
}
if ( !$config->configExists('search', 'bg_results_topsearchbar') ){
    $config->addConfig('search', 'bg_results_topsearchbar', "", '');
}



/*
$config = OW::getConfig();

if ( !$config->configExists('ocsslider', 'configuration') )
{
	$confArr = array(
	   'width' => 570,
	   'height' => 270,
	   'effect' => 'fade',
	   'pagination' => true,
	   'preload' => true,
	   'navigation' => true,
	   'speed' => 500,
	   'play' => 10000,
	   'crossfade' => true,
	   'randomize' => false,
	   'hoverPause' => true,
	   'titles' => true
	);
	
    $config->addConfig('ocsslider', 'configuration', json_encode($confArr), 'Slideshow configuration');
}

$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."ocsslider_image` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `hash` int(10) NOT NULL,
  `ext` VARCHAR( 5 ) NOT NULL,
  `href` varchar(255) DEFAULT NULL,
  `order` int(10) NOT NULL DEFAULT '0',
  `title` VARCHAR( 255 ) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

*/
/*
OW::getPluginManager()->addPluginSettingsRouteName('widgetplus', 'widgetplus.admin_widget');
*/

$path = OW::getPluginManager()->getPlugin('search')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'search');
OW::getPluginManager()->addPluginSettingsRouteName('search', 'search.admin');
