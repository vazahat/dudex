<?php

class CACHEEXTREME_CTRL_Admin extends ADMIN_CTRL_Abstract {

	function index() {
		$language = OW::getLanguage();

        $mainItem = new BASE_MenuItem();
        $mainItem->setLabel( $language->text( 'cacheextreme', 'adm_menu_config' ) );
        $mainItem->setUrl( OW::getRouter()->urlForRoute( 'cacheextreme.admin' ) );
        $mainItem->setKey( 'general' );
        $mainItem->setIconClass( 'ow_ic_gear_wheel' );
        $mainItem->setOrder( 0 );

        $aboutItem = new BASE_MenuItem();
        $aboutItem->setLabel( $language->text( 'cacheextreme', 'adm_menu_about' ) );
        $aboutItem->setUrl( OW::getRouter()->urlForRoute( 'cacheextreme.about' ) );
        $aboutItem->setKey( 'about' );
        $aboutItem->setIconClass( 'ow_ic_help' );
        $aboutItem->setOrder( 1 );

        $menu = new BASE_CMP_ContentMenu( array( $mainItem, $aboutItem ) );
        $this->addComponent( 'menu', $menu );

        $configs = OW::getConfig()->getValues( 'cacheextreme' );

        $cacheControlForm = new CacheControlForm();

        $this->addForm( $cacheControlForm );

        if ( OW::getRequest()->isPost() && $cacheControlForm->isValid( $_POST ) ) {
            $res = $cacheControlForm->process();

            CACHEEXTREME_BOL_Service::getInstance()->processCleanUp();

            OW::getFeedback()->info( $language->text( 'cacheextreme', 'settings_updated' ) );
            $this->redirect( OW::getRouter()->urlForRoute( 'cacheextreme.admin' ) );
        }

        if ( !OW::getRequest()->isAjax() ) {
            $this->setPageHeading( OW::getLanguage()->text( 'cacheextreme', 'admin_heading' ) );
            $this->setPageHeadingIconClass( 'ow_ic_gear_wheel' );

            $menu->deactivateElements();
            $elem = $menu->getElement( 'general' );
            if ( $elem ) {
                $elem->setActive( true );
            }
        }

        $cacheControlForm->getElement( 'templateCache' )->setValue( $configs['template_cache'] );
        $cacheControlForm->getElement( 'backendCache' )->setValue( $configs['backend_cache'] );
        $cacheControlForm->getElement( 'themeStatic' )->setValue( $configs['theme_static'] );
        $cacheControlForm->getElement( 'pluginStatic' )->setValue( $configs['plugin_static'] );
	}

	function about() {
		require_once (CACHEEXTREME_DIR_ROOT . DS . 'libs' . DS . 'php-markdown' . DS . 'markdown.php');
		$language = OW::getLanguage();
		$mainItem = new BASE_MenuItem();
        $mainItem->setLabel( $language->text( 'cacheextreme', 'adm_menu_config' ) );
        $mainItem->setUrl( OW::getRouter()->urlForRoute( 'cacheextreme.admin' ) );
        $mainItem->setKey( 'general' );
        $mainItem->setIconClass( 'ow_ic_gear_wheel' );
        $mainItem->setOrder( 0 );

        $aboutItem = new BASE_MenuItem();
        $aboutItem->setLabel( $language->text( 'cacheextreme', 'adm_menu_about' ) );
        $aboutItem->setUrl( OW::getRouter()->urlForRoute( 'cacheextreme.about' ) );
        $aboutItem->setKey( 'about' );
        $aboutItem->setIconClass( 'ow_ic_help' );
        $aboutItem->setOrder( 1 );

        $this->setPageHeading( OW::getLanguage()->text( 'cacheextreme', 'admin_heading' ) );
        $this->setPageHeadingIconClass( 'ow_ic_help' );

        $menu = new BASE_CMP_ContentMenu( array( $mainItem, $aboutItem ) );
        $this->addComponent( 'menu', $menu );

        $elem = $menu->getElement( 'about' );
        if ( $elem ) {
            $elem->setActive( true );
        }

        $this->assign(
        	'aboutContent', 
        	Markdown(file_get_contents(CACHEEXTREME_DIR_ROOT . DS . 'README.md'))
        );
	}
}

class CacheControlForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct() {
        parent::__construct( 'cacheControlForm' );

        $language = OW::getLanguage();

        // template cache control
        $templateCacheField = new CheckboxField( 'templateCache' );
        $this->addElement( $templateCacheField->setLabel( $language->text( 'cacheextreme', 'lblTemplateCache' ) ) );

        // backend cache control
        $backendCacheField = new CheckboxField( 'backendCache' );
        $this->addElement( $backendCacheField->setLabel( $language->text( 'cacheextreme', 'lblBackendCache' ) ) );

        // themes static cache control
        $themeStaticField = new CheckboxField( 'themeStatic' );
        $this->addElement( $themeStaticField->setLabel( $language->text( 'cacheextreme', 'lblThemeStatic' ) ) );

        // plugin static cache control
        $pluginStaticField = new CheckboxField( 'pluginStatic' );
        $this->addElement( $pluginStaticField->setLabel( $language->text( 'cacheextreme', 'lblPluginStatic' ) ) );

        // submit
        $submit = new Submit( 'clean' );
        $submit->setValue( $language->text( 'cacheextreme', 'btn_clean' ) );
        $this->addElement( $submit );
    }

    public function process() {
        $values = $this->getValues();

        $config = OW::getConfig();

        $config->saveConfig( 'cacheextreme', 'template_cache', $values['templateCache'] );
        $config->saveConfig( 'cacheextreme', 'backend_cache', $values['backendCache'] );
        $config->saveConfig( 'cacheextreme', 'theme_static', $values['themeStatic'] );
        $config->saveConfig( 'cacheextreme', 'plugin_static', $values['pluginStatic'] );

        return array( 'result' => true );
    }
}
