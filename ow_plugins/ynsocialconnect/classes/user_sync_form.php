<?php

class YNSOCIALCONNECT_CLASS_UserSyncForm extends Form
{
	const FORM_NAME = 'user_sync_form';

	public function __construct()
	{
		parent::__construct(self::FORM_NAME);

		//	init
		$fields = array();
		$language = OW::getLanguage();
		$config = OW::getConfig();
		$plugin = OW::getPluginManager() -> getPlugin('ynsocialconnect');
		$key = strtolower($plugin -> getKey());

		//	process button

		//	synchronize 
		$fields['synchronize'] = new Submit('synchronize');
		$fields['synchronize'] -> setValue(OW::getLanguage() -> text('ynsocialconnect', 'btn_synchronize'));
		$this -> addElement($fields['synchronize']);
		
		//	no button 
		$fields['no'] = new Submit('no');
		$fields['no'] -> setValue(OW::getLanguage() -> text('ynsocialconnect', 'btn_no'));
		$this -> addElement($fields['no']);
		
		//	end
	}

}
