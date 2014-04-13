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

if (!OW::getConfig()->configExists('ivideo', 'videoWidth'))
    OW::getConfig()->addConfig('ivideo', 'videoWidth', '500', '');

if (!OW::getConfig()->configExists('ivideo', 'videoHeight'))
    OW::getConfig()->addConfig('ivideo', 'videoHeight', '400', '');

if (!OW::getConfig()->configExists('ivideo', 'videoPreviewWidth'))
    OW::getConfig()->addConfig('ivideo', 'videoPreviewWidth', '180', '');

if (!OW::getConfig()->configExists('ivideo', 'videoPreviewHeight'))
    OW::getConfig()->addConfig('ivideo', 'videoPreviewHeight', '150', '');

if (!OW::getConfig()->configExists('ivideo', 'videosPerRow'))
    OW::getConfig()->addConfig('ivideo', 'videosPerRow', '4', '');

if (!OW::getConfig()->configExists('ivideo', 'makeUploaderMain'))
    OW::getConfig()->addConfig('ivideo', 'makeUploaderMain', '0', '');

OW::getConfig()->saveConfig('ivideo', 'allowedExtensions', 'mp4,flv');

$ivideo = OW::getPluginManager()->getPlugin('ivideo');
$staticDir = OW_DIR_STATIC_PLUGIN . $ivideo->getModuleName() . DS;

UTIL_File::removeDir($staticDir);
UTIL_File::copyDir($ivideo->getStaticDir(), $staticDir);

OW::getDbo()->query("alter table " . OW_DB_PREFIX . "ivideo_videos  modify filename VARCHAR(1000)");
