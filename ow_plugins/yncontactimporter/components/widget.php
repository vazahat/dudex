<?php
class YNCONTACTIMPORTER_CMP_Widget extends BASE_CLASS_Widget
{
	public function __construct(BASE_CLASS_WidgetParameter $params)
	{
		parent::__construct();
		if ( !OW::getUser()->isAuthorized('yncontactimporter', 'invite') )
        {
            $this->setVisible(false);
            return;
        }
		//load css
		$cssUrl = OW::getPluginManager() -> getPlugin('yncontactimporter') -> getStaticCssUrl() . 'yncontactimporter.css';
		OW::getDocument() -> addStyleSheet($cssUrl);

		$limit = (int) $params->customParamList['count'];
		if (!$limit)
			$limit = 5;
		$providers = YNCONTACTIMPORTER_BOL_ProviderService::getInstance() -> getAllProviders(array('limit' => $limit, 'enable' => 1));
		$arr_providers = array();
		foreach ($providers as $provider)
		{
			if(in_array($provider->name, array('facebook', 'twitter','linkedin')))
			{
				$core = new YNCONTACTIMPORTER_CLASS_Core();
				if(!$core->checkSocialBridgePlugin($provider->name))
				{
					continue;
				}
			}
			$item = array();
			$item['title'] = $provider -> title;
			$item['name'] = $provider -> name;
			$item['id'] = $provider->id;
			$item['logo'] = OW::getPluginManager() -> getPlugin('yncontactimporter') -> getStaticUrl() . "img/" . $provider -> name . ".png";
			$arr_providers[] = $item;
		}
		$this -> assign('providers', $arr_providers);
		$this -> assign('viewMore', OW::getRouter() -> urlForRoute('yncontactimporter-import'));
		
		$this->assign('authorization',OW::getLanguage() -> text('yncontactimporter', 'authorization'));
		$this->assign('import_your_contacts',OW::getLanguage() -> text('yncontactimporter', 'import_your_contacts'));
		$this -> assign("uploadCSVTitle", OW::getLanguage() -> text('yncontactimporter', 'upload_csv_file'));
		
		//check show more
		$this->assign("showMore", 0);
		if($limit < 9)
		{
			$this->assign("showMore", 1);
		}
		
		//get config
		$width = '30px';
		$height = '30px';
		$configs = OW::getConfig()->getValues('yncontactimporter');
		if(isset($configs['logo_width']))
			$width = $configs['logo_width']."px";
		if(isset($configs['logo_height']))
			$height = $configs['logo_height']."px";
		$this->assign("width", $width);
		$this->assign("height", $height);
		
		OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('yncontactimporter')->getRootDir() .
		 'langs.zip',
		 'yncontactimporter');
	}

	public static function getStandardSettingValueList()
	{
		return array(
			self::SETTING_SHOW_TITLE => true,
			self::SETTING_WRAP_IN_BOX => true,
			self::SETTING_TITLE => OW::getLanguage() -> text('yncontactimporter', 'widget_title'),
			self::SETTING_ICON => self::ICON_FRIENDS
		);
	}

	public static function getAccess()
	{
		return self::ACCESS_MEMBER;
	}
	public static function getSettingList()
    {
    	for($i = 1; $i <= 10; $i ++)
			$option[$i] = $i;
        $settingList['count'] = array(
            'presentation' => self::PRESENTATION_SELECT,
            'label' => OW::getLanguage()->text('yncontactimporter', 'widget_providers_count'),
            'optionList' => $option,
            'value' => 10
        );
        return $settingList;
    }
}
