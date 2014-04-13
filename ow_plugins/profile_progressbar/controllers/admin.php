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
 * @package ow_plugins.profileprogressbar.controllers
 * @since 1.0
 */
class PROFILEPROGRESSBAR_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    private $form;
    private $theme;
    private $plugin;
    private $document;
    
    public function __construct()
    {
        parent::__construct();
        
        $menuItems = array();
        
        $item = new BASE_MenuItem();
        $item->setLabel(OW::getLanguage()->text('profileprogressbar', 'themes_menu_item'));
        $item->setUrl(OW::getRouter()->urlForRoute('profileprogressbar.admin'));
        $item->setIconClass('ow_ic_picture');
        $item->setOrder(0);
        array_push( $menuItems, $item );
        
        $item = new BASE_MenuItem();
        $item->setLabel(OW::getLanguage()->text('profileprogressbar', 'features_menu_item'));
        $item->setUrl(OW::getRouter()->urlForRoute('profileprogressbar.admin_features'));
        $item->setIconClass('ow_ic_flag');
        $item->setOrder(1);
        array_push( $menuItems, $item );
        
        $item = new BASE_MenuItem();
        $item->setLabel(OW::getLanguage()->text('profileprogressbar', 'hint_menu_item'));
        $item->setUrl(OW::getRouter()->urlForRoute('profileprogressbar.admin_hint'));
        $item->setIconClass('ow_ic_comment');
        $item->setOrder(2);
        array_push( $menuItems, $item );

        $this->addComponent('menu', new BASE_CMP_ContentMenu($menuItems));
        
        $this->document = OW::getDocument();
        $this->plugin = OW::getPluginManager()->getPlugin('profileprogressbar');
        $this->theme = OW::getConfig()->getValue( 'profileprogressbar', 'theme' );
    }
    
    public function index( array $params = array() )
    {
        $this->document->addStyleSheet($this->plugin->getStaticCssUrl() . $this->theme . '.css');
        $this->document->addStyleSheet($this->plugin->getStaticCssUrl() . 'ui' . DS . 'jquery-ui-1.10.3.custom.min.css');
        
        $this->document->addScript($this->plugin->getStaticJsUrl() . 'jquery-ui.custom.min.js');
        $this->document->addScript($this->plugin->getStaticJsUrl() . 'profileprogressbar-admin.js', 'text/javascript', 1001);
        
        $this->form = new PROFILEPROGRESSBAR_CLASS_SettingsForm();
        
        if ( OW::getRequest()->isPost() && $this->form->isValid($_POST) )
        {
            $themeName = $this->form->getElement( 'themeList' )->getValue();
            
            OW::getConfig()->saveConfig( 'profileprogressbar', 'theme', $themeName );
        }
        
        $this->addForm($this->form);
    }
    
    public function features( array $params = array() )
    {
        $this->document->addStyleSheet($this->plugin->getStaticCssUrl() . 'ui' . DS . 'jquery-ui-1.10.3.custom.min.css');
        $this->document->addScript($this->plugin->getStaticJsUrl() . 'jquery-ui.custom.min.js');
        
        $config = OW::getConfig();
        
        if ( OW::getRequest()->isPost() && !empty($_POST['features']) )
        {
            $config->saveConfig('profileprogressbar', 'features', json_encode(array_map('intval', $_POST['features'])));
            $config->saveConfig('profileprogressbar', 'per_day', (int)$_POST['interval']);
        }
        
        $this->assign('perDay', $config->getValue('profileprogressbar', 'per_day'));
        $this->assign('features', get_object_vars(json_decode($config->getValue('profileprogressbar', 'features'))));
        $this->assign('availableFeatures', PROFILEPROGRESSBAR_BOL_Service::getInstance()->getAvailableFeatures());
    }
    
    public function hint( array $params = array() )
    {
        $this->document->addStyleSheet($this->plugin->getStaticCssUrl() . $this->theme . '.css');
        $this->document->addStyleSheet($this->plugin->getStaticCssUrl() . 'tipTip.css');
        $this->document->addScript($this->plugin->getStaticJsUrl() . 'jquery.tipTip.minified.js');
        
        $features = array_filter(@get_object_vars(json_decode(OW::getConfig()->getValue('profileprogressbar', 'features'))));
        
        $_features = array();
        
        foreach ( $features as $feature => $count )
        {
            $_features[$feature] = OW::getLanguage()->text('profileprogressbar', $feature.'_desc');
            $_features[$feature . 'Count'] = $count;
        }
        
        $form = new PROFILEPROGRESSBAR_CLASS_HintForm();
        
        function unsetUnusedHint( $val )
        {
            return strpos($val, '{$') === FALSE;
        }
        
        function getHint($_features)
        {
            $vars = array();
                
            foreach ( $_features as $key => $value )
            {
                $vars['{$' . $key .'}'] = $value;
            }

            $hintText = explode('#', OW::getLanguage()->text('profileprogressbar', 'hint_text'));
                
            foreach ( $hintText as $key => $hint )
            {
                $hintText[$key] = str_replace(array_keys($vars), array_values($vars), $hint);
            }

            $hintText = array_filter($hintText, 'unsetUnusedHint');

            return trim(implode('', $hintText));
        }
        
        if ( OW::getRequest()->isAjax() && $form->isValid($_POST) )
        {
            OW::getConfig()->saveConfig('profileprogressbar', 'show_hint', (int)$form->getElement('show-hint')->getValue());

            $languageService = BOL_LanguageService::getInstance();
            $langKey = $languageService->findKey('profileprogressbar', 'hint_text');
            $langValue = BOL_LanguageValueDao::getInstance()->findValue($languageService->getCurrent()->getId(), $langKey->getId());
            $langValue->setValue($_POST['hint-text']);
            BOL_LanguageService::getInstance()->saveValue($langValue);

            exit(json_encode(array('content' => getHint($_features))));
        }
        
        $hintText = getHint($_features);
        
        OW::getDocument()->addOnloadScript(
            UTIL_JsGenerator::composeJsString(
                ';$("#profile-progressbar").tipTip({
                    maxWidth: "auto",
                    content: {$hint}
                });', array('hint' => $hintText)
            )
        );
        
        $this->addForm($form);
    }
}
