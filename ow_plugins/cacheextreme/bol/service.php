<?php

final class CACHEEXTREME_BOL_Service {
	/**
	 *
	 */
	protected static $instance = null;

	public static function getInstance() {
		if ( self::$instance==null ) {
			self::$instance = new CACHEEXTREME_BOL_Service();
		}

		return self::$instance;
	}

	public function processCleanUp() {
		$configs = OW::getConfig()->getValues( 'cacheextreme' );

		//clean template cache
		if ($configs['template_cache']) {
			OW_ViewRenderer::getInstance()->clearCompiledTpl();
		}

		//clean db backend cache
		if ($configs['backend_cache']) {
			OW::getCacheManager()->clean(array(),OW_CacheManager::CLEAN_ALL);
		}

		//clean themes static contents cache
		if ($configs['theme_static']) {
			OW::getThemeManager()->getThemeService()->processAllThemes();
		}

		//clean plugins static contents cache
		if ($configs['plugin_static']) {
			$pluginService = BOL_PluginService::getInstance();
            $activePlugins = $pluginService->findActivePlugins();

            /* @var $pluginDto BOL_Plugin */
            foreach ( $activePlugins as $pluginDto )
            {
                $pluginStaticDir = OW_DIR_PLUGIN . $pluginDto->getModule() . DS . 'static' . DS;

                if ( file_exists($pluginStaticDir) )
                {
                    $staticDir = OW_DIR_STATIC_PLUGIN . $pluginDto->getModule() . DS;

                    if ( file_exists($staticDir) )
                    {
                    	UTIL_File::removeDir($staticDir);
                    }
                    mkdir($staticDir);
                    chmod($staticDir, 0777);

                    UTIL_File::copyDir($pluginStaticDir, $staticDir);
                }
            }
		}		
	}
}