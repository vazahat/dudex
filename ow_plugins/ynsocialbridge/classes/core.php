<?php
class YNSOCIALBRIDGE_CLASS_Core
{
	/**
	 *
	 * @param string service
	 * @return Object or Null
	 */
	public function getInstance($service = 'facebook')
	{
		$className = "YNSOCIALBRIDGE_CLASS_".ucwords($service);
    	return new $className();
	}
	//get init main menu
	public function initMenu()
	{
		$menuItems = array();

		$listNames = array('connects' => array('iconClass' => 'ow_yn_socialbridge_connects'),
			//'stream-settings' => array('iconClass' => 'ow_yn_socialstream'),
			//'publisher-settings' => array('iconClass' => 'ow_yn_socialpublisher'),
		);
		
		$listNames = array('connects' => array('iconClass' => 'ow_yn_socialbridge_connects'));
		
		if(OW::getPluginManager()->isPluginActive('ynsocialstream'))
		{
			$listNames['stream-settings'] = array('iconClass' => 'ow_yn_socialstream');
		}
		
		if(OW::getPluginManager()->isPluginActive('ynsocialpublisher'))
		{
			$listNames['publisher-settings'] = array('iconClass' => 'ow_yn_socialpublisher');
		}
		
		$count = 0;

		foreach ($listNames as $actionKey => $actionArr)
		{
			$menuItem = new BASE_MenuItem();
			$menuItem -> setKey($actionKey);
			$menuItem -> setUrl(OW::getRouter() -> urlForRoute('ynsocialbridge-' . $actionKey));
			$menuItem->setOrder($count);
			$menuItem -> setLabel(OW::getLanguage() -> text('ynsocialbridge', 'menu_item_' . $actionKey));
			$menuItem -> setIconClass($actionArr['iconClass']);
			$menuItems[] = $menuItem;
			$count++;
		}
		$contentMenu = new BASE_CMP_ContentMenu($menuItems);
		return $contentMenu;
	}
}
