<?php
class YNSOCIALCONNECT_CTRL_Admin  extends ADMIN_CTRL_Abstract
{
	protected $_plugin = NULL;
	protected $_menu = NULL;

	public function __construct()
	{
		parent::__construct();

		if (!OW::getUser() -> isAuthenticated())
		{
			throw new AuthenticateException();
		}

		$this -> _menu = $this -> __getMenu();
		$this -> addComponent('menu', $this -> _menu);
		OW::getDocument() -> setHeading(OW::getLanguage() -> text('ynsocialconnect', 'h_social_connect_plugin'));
		OW::getDocument() -> setHeadingIconClass('ow_ic_key');
		$this -> _plugin = OW::getPluginManager() -> getPlugin('ynsocialconnect');
		
		//put languages to database when development
		//OW::getLanguage() -> importPluginLangs($this -> _plugin -> getRootDir() . 'langs.zip', 'ynsocialconnect');
	}

	public function settings()
	{
		$el = $this -> _menu -> getElement('ynsocialconnect_admin_settings');
		if ($el)
		{
			$el -> setActive(true);
		}
		//	init
		$form = new YNSOCIALCONNECT_CLASS_AdminSettingsForm();
		$config = OW::getConfig();
		$plugin = OW::getPluginManager() -> getPlugin('ynsocialconnect');
		$key = strtolower($plugin -> getKey());

		//	process
		if (OW::getRequest() -> isPost() && $form -> isValid($_POST))
		{
			$data = $form -> getValues();
			if ($data['form_name'] == YNSOCIALCONNECT_CLASS_AdminSettingsForm::FORM_NAME)
			{
				$config -> saveConfig($key, 'limit_providers_view_on_login_header', $data['limit_provider']);
				$config -> saveConfig($key, 'position_providers_on_header', $data['position_providers_on_header']);
				$config -> saveConfig($key, 'size_of_provider_icon_px', $data['size_icon']);
				$config -> saveConfig($key, 'signup_mode', $data['signup_mode']);
				OW::getFeedback() -> info(OW::getLanguage() -> text('ynsocialconnect', 'txt_global_setting_updated'));
			}
		}
		// 	end
		$this -> addForm($form);
	}

	public function manageProviders()
	{
		$el = $this -> _menu -> getElement('ynsocialconnect_admin_manage_providers');
		if ($el)
		{
			$el -> setActive(true);
		}
		//	init
		OW::getDocument() -> addScript(OW::getPluginManager() -> getPlugin('base') -> getStaticJsUrl() . 'jquery-ui-1.8.9.custom.min.js');

		//	process
		$providers = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getAllProviders();

		// 	end
		$this -> assign('providers', $providers);
		$this -> assign('sImgSrc', OW::getPluginManager() -> getPlugin('ynsocialconnect') -> getStaticUrl() . 'img/');
	}

	public function statistics()
	{
		$el = $this -> _menu -> getElement('ynsocialconnect_admin_statistics');
		if ($el)
		{
			$el -> setActive(true);
		}
		//	init
		//	process
		$providers = YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> getAllProviders();

		//	count total sync as total login
		foreach ($providers as $k => $aStat)
		{
			$providers[$k] -> totalLogin = $providers[$k] -> totalLogin + $providers[$k] -> totalSync;
		}

		// 	end
		$this -> assign('providers', $providers);
	}

	public function updateActive()
	{
		//	process
		////	inactive all services
		YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> updateActiveStatusAllServices('0');
		foreach ($_POST['provider'] as $id)
		{
			YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> updateActiveById($id, '1');
		}

		// 	end
		OW::getFeedback() -> info(OW::getLanguage() -> text('ynsocialconnect', 'txt_update_successfully'));
		$this -> redirect(OW::getRouter() -> urlFor('YNSOCIALCONNECT_CTRL_Admin', 'manageProviders'));
	}

	public function ajaxReorder()
	{
		//	init
		if (!OW::getRequest() -> isAjax())
		{
			throw new Redirect404Exception();
		}

		if (empty($_POST))
		{
			exit('{}');
		}

		//	process
		foreach ($_POST['order'] as $id => $order)
		{
			YNSOCIALCONNECT_BOL_ServicesService::getInstance() -> updateOrderByServiceId($id, $order);
		}

		//	end
		exit();
	}

	private function __getMenu()
	{
		//	init
		$language = OW::getLanguage();
		$menuItems = array();

		//	process
		////	settings
		$item = new BASE_MenuItem();
		$item -> setLabel(OW::getLanguage() -> text('ynsocialconnect', 'menu_settings'));
		$item -> setUrl(OW::getRouter() -> urlForRoute('ynsocialconnect_admin_settings'));
		$item -> setKey('ynsocialconnect_admin_settings');
		$item -> setIconClass('ow_ic_gear_wheel');
		$item -> setOrder(0);
		$menuItems[] = $item;

		////	manage providers
		$item = new BASE_MenuItem();
		$item -> setLabel(OW::getLanguage() -> text('ynsocialconnect', 'menu_manage_social_providers'));
		$item -> setUrl(OW::getRouter() -> urlForRoute('ynsocialconnect_admin_manage_providers'));
		$item -> setKey('ynsocialconnect_admin_manage_providers');
		$item -> setIconClass('ow_ic_gear_wheel');
		$item -> setOrder(1);
		$menuItems[] = $item;

		////	statistics
		$item = new BASE_MenuItem();
		$item -> setLabel(OW::getLanguage() -> text('ynsocialconnect', 'menu_statistics'));
		$item -> setUrl(OW::getRouter() -> urlForRoute('ynsocialconnect_admin_statistics'));
		$item -> setKey('ynsocialconnect_admin_statistics');
		$item -> setIconClass('ow_ic_files');
		$item -> setOrder(2);
		$menuItems[] = $item;

		// 	end
		return new BASE_CMP_ContentMenu($menuItems);
	}
	/**
	 * ajax edit provider action
	 */
	public function ajaxUpdateProfileQuestion()
	{
		if (!OW::getRequest() -> isAjax())
		{
			throw new Redirect404Exception();
		}

		if (OW::getRequest() -> isPost())
		{
			try
			{
				if ( empty($_POST['alias']) )
		        {
		            exit(json_encode(array(
						'result' => false,
						'message' => 'Error!'
					)));
		        }
				$list = $_POST['alias'];
				$service = $_POST['providerName'];
		        foreach ( $list as $question => $field )
		        {
		            if ( !empty($field) )
		            {
		                YNSOCIALCONNECT_BOL_ServicesService::getInstance()->assignQuestion($question, $field, $service);
		            }
		            else
		            {
		                YNSOCIALCONNECT_BOL_ServicesService::getInstance()->unsetQuestion($question, $service);
		            }
		        }
				
				exit(json_encode(array(
					'result' => true,
					'message' => OW::getLanguage() -> text('yncontactimporter', 'provider_updated')
				)));
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
