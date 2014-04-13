<?php
class YNCONTACTIMPORTER_CTRL_Admin extends ADMIN_CTRL_Abstract
{
	protected $_plugin = NULL;
	protected $_menu = NULL;
	public function __construct()
	{
		parent::__construct();
		$this -> setPageHeading(OW::getLanguage() -> text('yncontactimporter', 'admin_yncontactimporter_heading'));
		$this -> setPageHeadingIconClass('ow_ic_gear_wheel');

		$this -> _plugin = OW::getPluginManager() -> getPlugin('yncontactimporter');
		//load css
		$cssUrl = $this -> _plugin -> getStaticCssUrl() . 'yncontactimporter.css';
		OW::getDocument() -> addStyleSheet($cssUrl);

		//add menu
		$this -> _menu = $this -> _initMenu();

		//put languages to database when chage
		//OW::getLanguage() -> importPluginLangs($this -> _plugin -> getRootDir() . 'langs.zip', 'yncontactimporter');
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
		$item[0] -> setLabel(OW::getLanguage() -> text('yncontactimporter', 'global_settings'));
		$item[0] -> setIconClass('ow_ic_gear_wheel');
		$item[0] -> setKey('global_setting');
		$item[0] -> setUrl(OW::getRouter() -> urlForRoute('yncontactimporter-admin'));
		$item[0] -> setOrder(1);

		$item[1] = new BASE_MenuItem( array());

		//Twitter menu
		$item[1] -> setLabel(OW::getLanguage() -> text('yncontactimporter', 'management_providers'));
		$item[1] -> setIconClass('ow_ic_files');
		$item[1] -> setKey('management_providers');
		$item[1] -> setUrl(OW::getRouter() -> urlForRoute('yncontactimporter-admin-providers'));
		$item[1] -> setOrder(2);

		$menu = new BASE_CMP_ContentMenu($item);
		$this -> addComponent('menu', $menu);
		return $menu;
	}

	/**
	 * Default action index
	 */
	public function index()
	{
		$el = $this -> _menu -> getElement('global_setting');
		if ($el)
		{
			$el -> setActive(true);
		}
		//global setting form
		$form = new YNCONTACTIMPORTER_CLASS_SettingsForm($this);

		if (!empty($_POST) && $form -> isValid($_POST))
		{
			$data = $form -> getValues();
			OW::getConfig() -> saveConfig('yncontactimporter', 'contact_per_page', $data['contact_per_page']);
			OW::getConfig() -> saveConfig('yncontactimporter', 'max_invite_per_times', $data['max_invite_per_times']);
			OW::getConfig() -> saveConfig('yncontactimporter', 'default_invite_message', $data['default_invite_message']);
			OW::getConfig() -> saveConfig('yncontactimporter', 'logo_width', $data['logo_width']);
			OW::getConfig() -> saveConfig('yncontactimporter', 'logo_height', $data['logo_height']);
			OW::getFeedback() -> info(OW::getLanguage() -> text('yncontactimporter', 'settings_updated'));
		}

		$this -> addForm($form);
	}

	/**
	 * provider action
	 */
	public function provider()
	{
		$el = $this -> _menu -> getElement('management_providers');
		if ($el)
		{
			$el -> setActive(true);
		}
		$providers = YNCONTACTIMPORTER_BOL_ProviderService::getInstance() -> getAllProviders();
		foreach ($providers as $provider)
		{
			if (in_array($provider->name, array(
				'facebook',
				'twitter',
				'linkedin'
			)))
			{
				$core = new YNCONTACTIMPORTER_CLASS_Core();
				if(!$core->checkSocialBridgePlugin($provider->name))
				{
					continue;
				}
			}
			$arr_providers[] = array(
				'id' => $provider -> id,
				'title' => $provider -> title,
				'logo' => OW::getPluginManager() -> getPlugin('yncontactimporter') -> getStaticUrl() . "img/" . $provider -> name . ".png",
				'enable' => ($provider -> enable) ? OW::getLanguage() -> text('yncontactimporter', 'enabled') : OW::getLanguage() -> text('yncontactimporter', 'disabled'),
				'order' => $provider -> order
			);
		}
		$this -> assign('providers', $arr_providers);
	}

	/**
	 * ajax edit provider action
	 */
	public function ajaxEditProvider()
	{
		if (!OW::getRequest() -> isAjax())
		{
			throw new Redirect404Exception();
		}

		if (OW::getRequest() -> isPost())
		{
			try
			{
				$values = $_POST;
				$id = (int)$values['id'];
				$providerEditForm = new YNCONTACTIMPORTER_CLASS_ProviderEditForm($id);
				if ($providerEditForm -> isValid($_POST))
				{
					$provider = YNCONTACTIMPORTER_BOL_ProviderService::getInstance() -> findProviderById($id);
					if ($provider && ($provider -> title != trim($values['title']) || $provider -> enable != $values['enable'] || $provider -> order != $values['order']))
					{
						$provider -> title = $values['title'];
						$provider -> enable = $values['enable'];
						$provider -> order = $values['order'];
						YNCONTACTIMPORTER_BOL_ProviderService::getInstance() -> save($provider);
					}
					exit(json_encode(array(
						'result' => true,
						'message' => OW::getLanguage() -> text('yncontactimporter', 'provider_updated')
					)));
				}
			}
			catch ( LogicException $e )
			{
				exit(json_encode(array(
					'result' => false,
					'message' => 'Error!'
				)));
			}
		}
		exit(json_encode(array(
			'result' => true,
			'message' => OW::getLanguage() -> text('yncontactimporter', 'provider_updated')
		)));
	}

}
