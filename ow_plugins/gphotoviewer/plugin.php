<?php
class GPHOTOVIEWER_Plugin
{
    const PLUGIN_KEY = 'gphotoviewer';
    const PLUGIN_VERSION = 1;

    const PRIVACY_ACTION_VIEW_MY_GPHOTOVIEWER = 'view_my_gphotoviewer';

    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return GPHOTOVIEWER_Plugin
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct()
    {
		
    }

    private $staticAdded = false;


    public function isReady()
    {
        $installed = OW::getConfig()->getValue('gphotoviewer', 'plugin_installed');

        return $installed || !OW::getPluginManager()->isPluginActive('gphotoviewer');
    }

    public function init()
    {
        /*if ( $this->isReady() )
        {
            $this->fullInit();
        }
        else
        {
            $this->shortInit();
        }*/
		$this->fullInit();
    }

    private function shortInit()
    {
		
    }

    private function fullInit()
    {
        //OW::getRouter()->addRoute(new OW_Route('gphotoviewer-index', 'gphotoviewer', 'GPHOTOVIEWER_CTRL_List', 'all'));
		OW::getEventManager()->bind('core.after_master_page_render', 'photoviewer_script_render');
    }

    public function activate()
    {
        /*if ( $this->isReady() )
        {
            $this->fullActivate();
        }
        else
        {
            $this->shortActivate();
        }*/
		$this->fullActivate();
    }

    private function fullActivate()
    {
		OW::getEventManager()->bind('core.after_master_page_render', 'photoviewer_script_render');
    }

    private function shortActivate()
    {

    }

    public function deactivate()
    {
        OW::getNavigation()->deleteMenuItem('gphotoviewer', 'main_menu_item');
    }


    public function install()
    {
        OW::getConfig()->addConfig('gphotoviewer', 'plugin_installed', '0');

        if ( $this->isReady() )
        {
            $this->startInstall();
            $this->completeInstall();
        }
        else
        {
            $this->startInstall();
        }
    }

    public function startInstall()
    {
        $plugin = OW::getPluginManager()->getPlugin(self::PLUGIN_KEY);
        BOL_LanguageService::getInstance()->importPrefixFromZip($plugin->getRootDir() . 'langs.zip', 'gphotoviewer');
    }

    public function completeInstall()
    {
        //OW::getPluginManager()->addPluginSettingsRouteName('gphotoviewer', 'gphotoviewer-admin-main');

        OW::getConfig()->saveConfig('gphotoviewer', 'plugin_installed', '1');
    }

    //Callbacks

    public function onSetupAdminNotification( BASE_CLASS_EventCollector $e )
    {

    }

    public function onAuthLabelsCollect( BASE_CLASS_EventCollector $event )
    {

    }

    public function collectPrivacyActions( BASE_CLASS_EventCollector $event )
    {
    }

    public function onPrivacyChange( OW_Event $e )
    {
		
    }

}