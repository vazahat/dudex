<?php
class YNCONTACTIMPORTER_CMP_InviteFriends extends OW_Component
{
	public function __construct($params)
	{
		if(isset($params['contacts_add']) && count($params['contacts_add']) > 0 && empty($_POST['skip_add']))
		{
			$this->assign('add_friend', true);
			$params['add_friend'] = true;
			$params['contacts'] = $params['contacts_add'];
			$params['actionUrl'] = OW::getRouter() -> urlForRoute('yncontactimporter-import');
		}
		else 
		{
			$maxInvitePerTimes = OW::getConfig() -> getValue('yncontactimporter', 'max_invite_per_times');
			if (!$maxInvitePerTimes)
			{
				$maxInvitePerTimes = 10;
			}
			$this -> assign('maxInvite', $params['maxInvite']);
			$this -> assign('maxInvitePerTimes', $maxInvitePerTimes);
			$this -> assign('totalInvited', $params['totalInvited']);
			$this -> assign('useSocialBridge', $params['useSocialBridge']);
			$this -> assign('urlLoading',OW::getPluginManager() -> getPlugin('yncontactimporter') -> getStaticUrl() . "img/loading.gif");
			$params['add_friend'] = false;
			$params['actionUrl'] = OW::getRouter() -> urlForRoute('yncontactimporter-invite');
			$this->assign('add_friend', false);
		}
		
		$this->addComponent('contacts', new YNCONTACTIMPORTER_CMP_Contacts($params));
		$this -> assign('service', $params['service']);
	}
}

?>