<?php

/**
 * Copyright (c) 2014, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/** 
 * 
 *
 * @author Kairat Bakytow <kainisoft@gmail.com>
 * @package ow_plugins.profileprogressbar.components
 * @since 1.0
 */
class PROFILEPROGRESSBAR_CMP_Synchronize extends OW_Component
{
    public function onBeforeRender()
    {
        parent::onBeforeRender();
        
        $document = OW::getDocument();
        $plugin = OW::getPluginManager()->getPlugin('profileprogressbar');
        $theme = OW::getConfig()->getValue('profileprogressbar', 'theme');

        $document->addStyleSheet($plugin->getStaticCssUrl() . $theme . '.css');
        $document->addScript($plugin->getStaticJsUrl() . 'jquery-ui.custom.min.js');
        $document->addScript($plugin->getStaticJsUrl() . 'profile_progressbar.js');
        
        $data = PROFILEPROGRESSBAR_BOL_Service::getInstance()->getProgressbarData(OW::getUser()->getId(), (bool)OW::getConfig()->getValue('profileprogressbar', 'show_hint'));

        OW::getDocument()->addScriptDeclarationBeforeIncludes(
            UTIL_JsGenerator::composeJsString(
                ';window.PROFILEPROGRESSBARPARAMS = {
                    totalQuestionCount: {$totalQuestionCount},
                    completeQuestionCount: {$completeQuestionCount}
                };', $data[PROFILEPROGRESSBAR_BOL_Service::KEY_PROGRESSBAR]
            )
        );
        
        if ( !empty($data[PROFILEPROGRESSBAR_BOL_Service::KEY_HINT]) )
        {
            $document->addStyleSheet($plugin->getStaticCssUrl() . 'tipTip.css');
            $document->addScript($plugin->getStaticJsUrl() . 'jquery.tipTip.minified.js');
            $document->addOnloadScript('$("#profile-progressbar").tipTip({
                maxWidth: "auto",
                content: "' . $data[PROFILEPROGRESSBAR_BOL_Service::KEY_HINT] . '"
                });'
            );
        }
    }
}
