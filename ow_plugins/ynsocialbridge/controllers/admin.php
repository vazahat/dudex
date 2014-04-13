<?php
class YNSOCIALBRIDGE_CTRL_Admin extends ADMIN_CTRL_Abstract
{
	protected $_plugin = NULL;
	protected $_contactImporterPlugin = NULL;

	public function __construct()
	{
		parent::__construct();
		$this -> setPageHeading(OW::getLanguage() -> text('ynsocialbridge', 'admin_ynsocialbridge_heading'));
		$this -> setPageHeadingIconClass('ow_ic_gear_wheel');

		$this -> _plugin = OW::getPluginManager() -> getPlugin('ynsocialbridge');
		//load css
		$cssUrl = $this -> _plugin -> getStaticCssUrl() . 'ynsocialbridge.css';
		OW::getDocument() -> addStyleSheet($cssUrl);

		//add menu
		$menu = $this -> _initMenu();

		//check Contact importer
		if ($this -> _contactImporterPlugin = BOL_PluginService::getInstance() -> findPluginByKey('yncontactimporter'))
		{
			$this -> assign('hasContactimporter', 1);
		}
		else
		{
			$this -> assign('hasContactimporter', 0);
		}

		//put languages to database when chage
		//OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('ynsocialbridge')->getRootDir() . 'langs.zip',
		// 'ynsocialbridge');

	}

	/**
	 *
	 * init admin menu
	 *
	 * @return  void
	 */
	protected function _initMenu()
	{
		//Facebook menu
		$item[0] = new BASE_MenuItem( array());
		$item[0] -> setLabel(OW::getLanguage() -> text('ynsocialbridge', 'facebook_settings'));
		$item[0] -> setIconClass('ow_yn_facebook_icon');
		$item[0] -> setKey('1');
		$item[0] -> setUrl(OW::getRouter() -> urlForRoute('ynsocialbridge-admin'));
		$item[0] -> setOrder(1);

		$item[1] = new BASE_MenuItem( array());

		//Twitter menu
		$item[1] -> setLabel(OW::getLanguage() -> text('ynsocialbridge', 'twitter_settings'));
		$item[1] -> setIconClass('ow_yn_twitter_icon');
		$item[1] -> setKey('2');
		$item[1] -> setUrl(OW::getRouter() -> urlForRoute('ynsocialbridge-admin-twitter'));
		$item[1] -> setOrder(2);

		//LinkedIn menu
		$item[2] = new BASE_MenuItem( array());
		$item[2] -> setLabel(OW::getLanguage() -> text('ynsocialbridge', 'linkedin_settings'));
		$item[2] -> setIconClass('ow_yn_linkedin_icon');
		$item[2] -> setKey('3');
		$item[2] -> setUrl(OW::getRouter() -> urlForRoute('ynsocialbridge-admin-linkedin'));
		$item[2] -> setOrder(3);
		
		$menu = new BASE_CMP_ContentMenu($item);
		$this -> addComponent('menu', $menu);
	}

	/**
	 * Default action facebook
	 */
	public function facebook()
	{
		$servive = 'facebook';
		//setting facebook form
		$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($servive);
		$form = new YNSOCIALBRIDGE_CLASS_SettingsForm($clientConfig, $servive);

		if (OW::getRequest() -> isPost() && $form -> isValid($_POST))
		{
			$data = $form -> getValues();
			if (!$clientConfig)
			{
				$clientConfig = new YNSOCIALBRIDGE_BOL_Apisetting();
			}
			$params = array(
				'key' => $data['key'],
				'secret' => $data['secret']
			);
			if ($this -> _contactImporterPlugin)
			{
				$params['max_invite_day'] = $data['max_invite_day'];
			}
			$clientConfig -> apiParams = serialize($params);
			$clientConfig -> apiName = $servive;
			YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> save($clientConfig);
			OW::getFeedback() -> info(OW::getLanguage() -> text('ynsocialbridge', $servive . '_settings_updated'));
		}
		$this -> addForm($form);
	}

	/**
	 * Twitter action
	 */
	public function twitter()
	{
		$servive = 'twitter';
		//setting twitter form
		$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($servive);

		$form = new YNSOCIALBRIDGE_CLASS_SettingsForm($clientConfig, $servive);
		if (OW::getRequest() -> isPost() && $form -> isValid($_POST))
		{
			$data = $form -> getValues();
			if (!$clientConfig)
			{
				$clientConfig = new YNSOCIALBRIDGE_BOL_Apisetting();
			}
			$params = array(
				'key' => $data['key'],
				'secret' => $data['secret']
			);
			if ($this -> _contactImporterPlugin)
			{
				$params['max_invite_day'] = $data['max_invite_day'];
			}
			$clientConfig -> apiParams = serialize($params);
			$clientConfig -> apiName = $servive;
			YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> save($clientConfig);
			OW::getFeedback() -> info(OW::getLanguage() -> text('ynsocialbridge', $servive . '_settings_updated'));
		}
		$this -> addForm($form);
	}

	/**
	 * LinkedIn action
	 */
	public function linkedin()
	{
		$servive = 'linkedin';
		//setting linkedin form
		$clientConfig = YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($servive);

		$form = new YNSOCIALBRIDGE_CLASS_SettingsForm($clientConfig, $servive);
		if (OW::getRequest() -> isPost() && $form -> isValid($_POST))
		{
			$data = $form -> getValues();
			if (!$clientConfig)
			{
				$clientConfig = new YNSOCIALBRIDGE_BOL_Apisetting();
			}
			$params = array(
				'key' => $data['key'],
				'secret' => $data['secret']
			);
			if ($this -> _contactImporterPlugin)
			{
				$params['max_invite_day'] = $data['max_invite_day'];
			}
			$clientConfig -> apiParams = serialize($params);
			$clientConfig -> apiName = $servive;
			YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> save($clientConfig);
			OW::getFeedback() -> info(OW::getLanguage() -> text('ynsocialbridge', $servive . '_settings_updated'));
		}
		$this -> addForm($form);
	}
}
