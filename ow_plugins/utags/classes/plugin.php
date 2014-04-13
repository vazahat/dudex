<?php

/**
 * Copyright (c) 2013, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package utags.classes
 */
class UTAGS_CLASS_Plugin
{
    /**
     * Singleton instance.
     *
     * @var UTAGS_CLASS_Plugin
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return UTAGS_CLASS_Plugin
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    private function __construct() 
    {
        
    }
    
    
    public function onFinalize()
    {
        $plugin = OW::getPluginManager()->getPlugin("utags");
        $staticUrl = $plugin->getStaticUrl();
        
        //$styleUrl = $staticUrl . "style.css?" . $plugin->getDto()->build;
        //$scriptUrl = $staticUrl . "script.js?" . $plugin->getDto()->build;
        $styleUrl = $staticUrl . "style.min.css?" . $plugin->getDto()->build;
        $scriptUrl = $staticUrl . "script.min.js?" . $plugin->getDto()->build;
        
        $js = UTIL_JsGenerator::newInstance();
        
        // Developer Version
        /*$js->addScript('(function( scriptFiles, styleFile ){
            var callbacks = {}, ready = {}, loading = false, fireCallbacks;
            callbacks["dom"] = []; ready["dom"] = false;
            callbacks["script"] = []; ready["script"] = false;
            callbacks["full"] = []; ready["full"] = false

            fireCallbacks = function( r ) {
                if (r && r != "full") {
                    ready[r] = true;
                    $.each(callbacks[r], function(i, c) { c(window.UTAGS); });
                    callbacks[r] = [];
                }
                
                ready["full"] = ready["script"] && ready["dom"];
                loading = !ready["full"];

                if ( ready["full"] ) {
                    $.each(callbacks["full"], function(i, c) { c(window.UTAGS); });
                    callbacks["full"] = [];
                }
                
            };
            window.UTAGS_Require = function( r, callback ) {
                if ( !callback && $.isFunction(r) ) {
                    callback = r;
                    r = null;
                }
                r = r || "full";
                
                if ( callback ) callbacks[r].push(callback);
                
                if ( ready[r] ) {
                    fireCallbacks(r);
                    return window.UTAGS;
                }

                if ( loading ) return;
                loading = true;
                
                OW.addCssFile(styleFile);
                OW.addScriptFiles(scriptFiles, function() {
                    fireCallbacks("script");
                });

                OW.loadComponent("UTAGS_CMP_Tags", [], {
                    onReady: function( html ) {
                        $(html).appendTo(document.body);
                    },

                    onLoad: function() {
                        fireCallbacks("dom");
                    }
                });
            };
        })({$scriptFiles}, {$styleFile});', array(
            "scriptFiles" => array($scriptUrl),
            "styleFile" => $styleUrl
        ));*/
        
        // Compressed version
        $js->addScript('(function(f,c){var d={},b={},e=false,a;d.dom=[];b.dom=false;d.script=[];b.script=false;d.full=[];b.full=false;a=function(g){if(g&&g!="full"){b[g]=true;$.each(d[g],function(h,j){j(window.UTAGS)});d[g]=[]}b.full=b.script&&b.dom;e=!b.full;if(b.full){$.each(d.full,function(h,j){j(window.UTAGS)});d.full=[]}};window.UTAGS_Require=function(g,h){if(!h&&$.isFunction(g)){h=g;g=null}g=g||"full";if(h){d[g].push(h)}if(b[g]){a(g);return window.UTAGS}if(e){return}e=true;OW.addCssFile(c);OW.addScriptFiles(f,function(){a("script")});OW.loadComponent("UTAGS_CMP_Tags",[],{onReady:function(i){$(i).appendTo(document.body)},onLoad:function(){a("dom")}})}})({$scriptFiles}, {$styleFile});', array(
            "scriptFiles" => array($scriptUrl),
            "styleFile" => $styleUrl
        ));
        
        OW::getDocument()->addScriptDeclaration($js, "text/javascript", 0);
    }
    
    public function collectAdminNotifications( BASE_CLASS_EventCollector $e )
    {
        $language = OW::getLanguage();
        $e->add($language->text('utags', 'admin_plugin_required_notification', array(
            'pluginUrl' => 'http://www.oxwall.org/store/item/16',
            'settingUrl' => OW::getRouter()->urlForRoute('utags-settings-page')
        )));
    }
    
    public function addAuthLabels( BASE_CLASS_EventCollector $event )
    {
        $language = OW::getLanguage();
        $event->add(
            array(
                'utags' => array(
                    'label' => $language->text('utags', 'auth_group_label'),
                    'actions' => array(
                        'view_tags' => $language->text('utags', 'auth_action_view_tags'),
                        'add_tags' => $language->text('utags', 'auth_action_add_tags')
                    )
                )
            )
        );
    }
    
    public function mobileInit()
    {
        UTAGS_CLASS_BaseBridge::getInstance()->genericInit();
        UTAGS_CLASS_FriendsBridge::getInstance()->genericInit();
        UTAGS_CLASS_PhotoBridge::getInstance()->genericInit();
        UTAGS_CLASS_NewsfeedBridge::getInstance()->genericInit();
        UTAGS_CLASS_PrivacyBridge::getInstance()->genericInit();
        UTAGS_CLASS_CreditsBridge::getInstance()->genericInit();
        UTAGS_CLASS_TagsBridge::getInstance()->genericInit();
        
        UTAGS_MCLASS_NotificationsBridge::getInstance()->init();
    }
    
    public function init()
    {
        OW::getRouter()->addRoute(new OW_Route('utags-settings-page', 'admin/plugins/photo-tags', 'UTAGS_CTRL_Admin', 'index'));
        
        OW::getEventManager()->bind('admin.add_auth_labels', array($this, 'addAuthLabels'));
        
        if ( !UTAGS_CLASS_PhotoBridge::getInstance()->isActive() )
        {
            OW::getEventManager()->bind('admin.add_admin_notification', array($this, 'collectAdminNotifications'));

            return;
        }
        
        UTAGS_CLASS_BaseBridge::getInstance()->init();
        UTAGS_CLASS_FriendsBridge::getInstance()->init();
        UTAGS_CLASS_PhotoBridge::getInstance()->init();
        UTAGS_CLASS_NotificationsBridge::getInstance()->init();
        UTAGS_CLASS_NewsfeedBridge::getInstance()->init();
        UTAGS_CLASS_PrivacyBridge::getInstance()->init();
        UTAGS_CLASS_CreditsBridge::getInstance()->init();
        UTAGS_CLASS_TagsBridge::getInstance()->init();
        
        OW::getEventManager()->bind(OW_EventManager::ON_FINALIZE, array($this, "onFinalize"));
    }
}