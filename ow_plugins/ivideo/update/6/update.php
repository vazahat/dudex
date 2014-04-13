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
Updater::getLanguageService()->importPrefixFromZip(OW::getPluginManager()->getPlugin('ivideo')->getRootDir() . 'langs.zip', 'ivideo');

$ivideo = OW::getPluginManager()->getPlugin('ivideo');
$staticDir = OW_DIR_STATIC_PLUGIN . $ivideo->getModuleName() . DS;

UTIL_File::removeDir($staticDir);
UTIL_File::copyDir($ivideo->getStaticDir(), $staticDir);

if (!OW::getConfig()->configExists('ivideo', 'ffmpegPath'))
    OW::getConfig()->addConfig('ivideo', 'ffmpegPath', '', '');

//Updater::getConfigService()->saveConfig('ivideo', 'allowedExtensions', 'mp4,flv,avi,wmv,swf,mov,mpg,3g2,ram');

OW::getDbo()->query("alter table " . OW_DB_PREFIX . "ivideo_videos add column privacy varchar(50) NOT NULL default 'everybody'");
