<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
BOL_LanguageService::getInstance()->addPrefix('sponsors', 'Sponsors Wall');

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('sponsors')->getRootDir() . 'langs.zip', 'sponsors');

OW::getPluginManager()->addPluginSettingsRouteName('sponsors', 'sponsors_admin');

if (!OW::getConfig()->configExists('sponsors', 'minimumPayment'))
    OW::getConfig()->addConfig('sponsors', 'minimumPayment', '10', '');

if (!OW::getConfig()->configExists('sponsors', 'alwaysSingleFlip'))
    OW::getConfig()->addConfig('sponsors', 'alwaysSingleFlip', '0', '');

if (!OW::getConfig()->configExists('sponsors', 'topSponsorsCount'))
    OW::getConfig()->addConfig('sponsors', 'topSponsorsCount', '10', '');

if (!OW::getConfig()->configExists('sponsors', 'sponsorValidity'))
    OW::getConfig()->addConfig('sponsors', 'sponsorValidity', '90', '');

if (!OW::getConfig()->configExists('sponsors', 'autoApprove'))
    OW::getConfig()->addConfig('sponsors', 'autoApprove', '0', '');

if (!OW::getConfig()->configExists('sponsors', 'newSponsorLinkAtLast'))
    OW::getConfig()->addConfig('sponsors', 'newSponsorLinkAtLast', '1', '');

if (!OW::getConfig()->configExists('sponsors', 'onlyAdminCanAdd'))
    OW::getConfig()->addConfig('sponsors', 'onlyAdminCanAdd', '0', '');

if (!OW::getConfig()->configExists('sponsors', 'cutoffDay'))
    OW::getConfig()->addConfig('sponsors', 'cutoffDay', '5', '');

OW::getDbo()->query("
   CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "sponsors_sponsor_details` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `website` varchar(128) NOT NULL default '' ,  
  `image` varchar(128) NOT NULL default '' ,
  `price`decimal(4,2) NOT NULL,
  `userId` int(11) default 0,
  `status` int(1) default 0,
  `validity` int(4) default 0,  
  `timestamp` int(11) NOT NULL, 
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$product = new BOL_BillingProduct();
$product->active = 1;
$product->productKey = 'sponsors_sponsor';
$product->adapterClassName = 'SPONSORS_CLASS_SponsorProductAdapter';

BOL_BillingService::getInstance()->saveProduct($product);

$sourcePath = dirname(__FILE__) . DS . 'static' . DS;
copy($sourcePath . "background.jpg", OW::getPluginManager()->getPlugin('sponsors')->getUserFilesDir() . "background.jpg");
copy($sourcePath . "defaultSponsor.jpg", OW::getPluginManager()->getPlugin('sponsors')->getUserFilesDir() . "defaultSponsor.jpg");
copy($sourcePath . "sponsor.png", OW::getPluginManager()->getPlugin('sponsors')->getUserFilesDir() . "sponsor.png");
