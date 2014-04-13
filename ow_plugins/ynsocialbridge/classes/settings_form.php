<?php
class YNSOCIALBRIDGE_CLASS_SettingsForm extends Form
{
	public function __construct($clientConfig, $service)
	{
		parent::__construct('settingsForm');
		$language = OW::getLanguage();
		$params = array(
			'key' => '',
			'secret' => '',
			'max_invite_day' => 10
		);
		if ($clientConfig)
			$params = unserialize($clientConfig -> apiParams);

		// API Key
		$textField['key'] = new TextField('key');
		$textField['key'] -> setLabel($language -> text('ynsocialbridge', 'facebook_setting_key')) -> setValue($params['key']) -> setRequired(false);
		$this -> addElement($textField['key']);

		//API Secret
		$textField['secret'] = new TextField('secret');
		$textField['secret'] -> setLabel($language -> text('ynsocialbridge', 'facebook_setting_secret')) -> setValue($params['secret']) -> setRequired(false);
		$this -> addElement($textField['secret']);

		if (BOL_PluginService::getInstance() -> findPluginByKey('yncontactimporter'))
		{

			switch ($service)
			{
				case 'facebook' :
					$miValidator = new IntValidator(1, 20);
					$miValidator -> setErrorMessage($language -> text('ynsocialbridge', 'max_invite_validation_error', array(
						'min' => 1,
						'max' => 20
					)));
					break;
				case 'twitter' :
					$miValidator = new IntValidator(1, 250);
					$miValidator -> setErrorMessage($language -> text('ynsocialbridge', 'max_invite_validation_error', array(
						'min' => 1,
						'max' => 250
					)));
					break;
				case 'linkedin' :
					$miValidator = new IntValidator(1, 10);
					$miValidator -> setErrorMessage($language -> text('ynsocialbridge', 'max_invite_validation_error', array(
						'min' => 1,
						'max' => 10
					)));
					break;

				default :
					$miValidator = new IntValidator(1, 10);
					$miValidator -> setErrorMessage($language -> text('ynsocialbridge', 'max_invite_validation_error', array(
						'min' => 1,
						'max' => 10
					)));
					break;
			}

			//Max invite
			$textField['max_invite_day'] = new TextField('max_invite_day');
			$textField['max_invite_day'] -> setLabel($language -> text('ynsocialbridge', 'max_invite_day')) -> setValue($params['max_invite_day']) -> setRequired(false) -> addValidator($miValidator);
			$this -> addElement($textField['max_invite_day']);
		}
		// button submit
		$submit = new Submit('submit');
		$submit -> setValue($language -> text('ynsocialbridge', 'save_btn_label'));
		$this -> addElement($submit);
	}

}
