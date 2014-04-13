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
class SPONSORS_CMP_ProfileWidget extends BASE_CLASS_Widget {

    public function __construct(BASE_CLASS_WidgetParameter $params) {
        parent::__construct();

        $userId = (int) $params->additionalParamList['entityId'];

        if (!SPONSORS_BOL_Service::getInstance()->isMemberSponsor($userId)) {
            $this->setVisible(false);
            return;
        }

        $this->setSettingValue(
                self::SETTING_TOOLBAR, array(
            array(
                'label' => OW::getLanguage()->text('sponsors', 'become_sponsor'),
                'href' => OW::getRouter()->urlForRoute('sponsors_sponsor')
            )
                )
        );

        $this->assign('sponsorImageUrl', OW::getPluginManager()->getPlugin('sponsors')->getUserFilesUrl() . "defaultSponsor.jpg");
    }

    public static function getStandardSettingValueList() {
        return array(
            self::SETTING_TITLE => OW::getLanguage()->text('sponsors', 'user_widget_title'),
            self::SETTING_ICON => self::ICON_STAR,
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_WRAP_IN_BOX => true
        );
    }

    public static function getAccess() {
        return self::ACCESS_ALL;
    }

}