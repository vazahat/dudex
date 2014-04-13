<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

class ATTACHMENTS_Plugin
{
    const PLUGIN_KEY = 'attachments';
    const PLUGIN_VERSION = 288;

    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return ATTACHMENTS_Plugin
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     *
     * @var OW_Plugin
     */
    private $plugin;

    private function __construct()
    {
        $this->plugin = OW::getPluginManager()->getPlugin(self::PLUGIN_KEY);
    }

    public function addJs( $script )
    {
        $this->addStatic($script);
    }

    public function addStatic( $onloadJs = '' )
    {
        static $fistCall = true;

        $staticUrl = OW::getPluginManager()->getPlugin('attachments')->getStaticUrl();
        OW::getDocument()->addStyleSheet($staticUrl . 'styles.css' . '?' . self::PLUGIN_VERSION);

        if ( $fistCall )
        {
            if ( OW::getRequest()->isAjax() )
            {
                OW::getDocument()->addOnloadScript('window.ATTPAjaxLoadCallbackQueue = [];');
                OW::getDocument()->addOnloadScript(UTIL_JsGenerator::composeJsString('
                    if ( !window.ATTP ) OW.addScriptFiles([{$url}]);
                ', array(
                    'url' => $staticUrl . 'scripts.js' . '?' . self::PLUGIN_VERSION
                )));
            }
            else
            {
                OW::getDocument()->addScript($staticUrl . 'scripts.js' . '?' . self::PLUGIN_VERSION);

                if ( !empty($onloadJs) )
                {
                    OW::getDocument()->addOnloadScript($onloadJs);
                }

                return;
            }
        }

        $fistCall = false;

        OW::getDocument()->addOnloadScript('(function() {
            var loaded = function() {
                ' . $onloadJs . '
            };

            if ( window.ATTP )
                loaded.call();
            else
                window.ATTPAjaxLoadCallbackQueue.push(loaded);
        })();');
    }
}