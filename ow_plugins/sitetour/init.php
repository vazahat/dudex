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
OW::getRouter()->addRoute(new OW_Route('sitetour_admin', 'admin/sitetour/steps', "SITETOUR_CTRL_Admin", 'index'));
OW::getRouter()->addRoute(new OW_Route('sitetour_settings', 'admin/sitetour/settings', "SITETOUR_CTRL_Admin", 'settings'));
OW::getRouter()->addRoute(new OW_Route('sitetour_console', 'admin/sitetour/console', "SITETOUR_CTRL_Admin", 'console'));
OW::getRouter()->addRoute(new OW_Route('sitetour_save_updates', 'admin/sitetour/save-updates', "SITETOUR_CTRL_Admin", 'save1'));
OW::getRouter()->addRoute(new OW_Route('sitetour_save_positions', 'admin/sitetour/save-positions', "SITETOUR_CTRL_Admin", 'save2'));

function sitetour_after_route(OW_Event $event) {

    if (!OW::getUser()->isAuthenticated() && OW::getConfig()->getValue('sitetour', 'enableForGuests') != '1') {
        return;
    }

    $configs = OW::getConfig()->getValues('sitetour');

    $handlerAttributes = OW::getRequestHandler()->getHandlerAttributes();
    $attrKey = $handlerAttributes[OW_RequestHandler::ATTRS_KEY_CTRL];
    $attrAction = $handlerAttributes[OW_RequestHandler::ATTRS_KEY_ACTION];

    $allSteps = "";

    if ($attrKey == 'BASE_CTRL_ComponentPanel' && $attrAction == 'profile') {
        $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('profile');
    }

    if ($attrKey == 'BASE_CTRL_ComponentPanel' && $attrAction == 'dashboard') {
        $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('dashboard');
    }

    if ($attrKey == 'BASE_CTRL_ComponentPanel' && $attrAction == 'index') {
        $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('index');
    }

    if ($attrKey == 'BASE_CTRL_UserList') {
        $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('members');
    }


    if ($attrKey == 'BLOGS_CTRL_Blog' && $attrAction == 'index') {
        $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('blogs');
    }

    if ($attrKey == 'BLOGS_CTRL_View' && $attrAction == 'index') {
        $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('blog-view');
    }

    if ($attrKey == 'GROUPS_CTRL_Groups') {
        if ($attrAction == 'index') {
            $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('groups');
        } else {
            $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('group-view');
        }
    }

    if ($attrKey == 'LINKS_CTRL_List') {
        $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('links');
    }

    if ($attrKey == 'LINKS_CTRL_View' && $attrAction == 'index') {
        $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('link-view');
    }

    if ($attrKey == 'PHOTO_CTRL_Photo') {
        if ($attrAction == 'viewList') {
            $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('photos');
        }
    }

    if ($attrKey == 'VIDEO_CTRL_Video') {
        if ($attrAction == 'viewList') {
            $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('videos');
        }
    }

    if ($attrKey == 'FORUM_CTRL_Index') {
        if ($attrAction == 'index') {
            $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('forum');
        }
    }

    if ($attrKey == 'EVENT_CTRL_Base') {
        if ($attrAction == 'eventsList') {
            $allSteps = SITETOUR_BOL_StepDao::getInstance()->getAllJsonSteps('events');
        }
    }

    if (empty($allSteps)) {
        return;
    }

    $language = OW::getLanguage();

    $headSrc = 'function startIntro(){
                    var intro = introJs();
                    intro.setOptions({
                            steps:  ' . $allSteps . '.filter(function (obj) {return $(obj.element).length;}),
                            nextLabel: "' . $language->text('sitetour', 'next') . '", prevLabel: "' . $language->text('sitetour', 'prev') . '", skipLabel: "' . $language->text('sitetour', 'skip') . '", doneLabel: "' . $language->text('sitetour', 'done') . '",
                            exitOnEsc: ' . $configs['exitOnEsc'] . ',
                            exitOnOverlayClick: ' . $configs['exitOnOverlayClick'] . ',
                            showStepNumbers: ' . $configs['showStepNumbers'] . ',
                            keyboardNavigation: ' . $configs['keyboardNavigation'] . ',
                            showButtons: ' . $configs['showButtons'] . ',
                            showBullets: ' . $configs['showBullets'] . '
                    });
                    
                    intro.start();
                }
                    
                $(document).ready(function(){ 
                    if( typeof $.cookie("sitetour") == "undefined" ){
                        $.cookie("sitetour","done", { expires: 15 });
                        startIntro();
                    }

                    $("#tour-start").click(function() {
                        startIntro();
                    });

                });';

    $tourButton = '<div class="tour-wrap" id="tour-start">
                      <p class="tour-left"> 
                          <span>' . $language->text('sitetour', 'page_guide') . '</span> 
                      </p> 
                      <p class="tour-right"> 
                          <span>' . $language->text('sitetour', 'page_guide_text') . '</span> 
                      </p> 
                   </div>';


    OW::getDocument()->appendBody($tourButton);
    OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('sitetour')->getStaticJsUrl() . 'jquery.cookie.js');
    OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('sitetour')->getStaticJsUrl() . 'intro.min.js');
    OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('sitetour')->getStaticCssUrl() . 'introjs.min.css');
    OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('sitetour')->getStaticCssUrl() . 'style.css');

    $style = '.introjs-tooltip{min-width:' . $configs['introWidth'] . 'px}
    .tour-left {background: none repeat scroll 0 0 ' . $configs['guideColor'] . '; border-right: 1px solid red;}
    .tour-wrap {background: none repeat scroll 0 0 ' . $configs['guideColor'] . '; top: ' . $configs['guidePos'] . '%;}';

    if ($configs['enableRTL'] == '1') {
        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('sitetour')->getStaticCssUrl() . 'introjs-rtl.min.css');
    }

    OW::getDocument()->addCustomHeadInfo('<script type="text/javascript">' . $headSrc . '</script>');
    OW::getDocument()->addCustomHeadInfo('<style>' . $style . '</style>');
}

OW::getEventManager()->bind(OW_EventManager::ON_AFTER_ROUTE, 'sitetour_after_route');
