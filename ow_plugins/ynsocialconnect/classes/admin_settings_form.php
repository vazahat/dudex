<?php

class YNSOCIALCONNECT_CLASS_AdminSettingsForm extends Form
{
	const FORM_NAME = 'admin_settings_form';

	public function __construct()
	{
		parent::__construct(self::FORM_NAME);

		//	init
		$fields = array();
		$language = OW::getLanguage();
		$config = OW::getConfig();
		$plugin = OW::getPluginManager() -> getPlugin('ynsocialconnect');
		$key = strtolower($plugin -> getKey());

		//	process

		//	limit provider field
		$fields['limit_provider'] = new TextField('limit_provider');
		// @formatter:off
		$fields['limit_provider'] -> setValue($config -> getValue($key, 'limit_providers_view_on_login_header')) 
					-> setRequired(true)
					-> addValidator(new IntValidator(1, 999999));
		// @formatter:on
		$this -> addElement($fields['limit_provider']);
		
		//	size icon field
		$fields['size_icon'] = new TextField('size_icon');
		// @formatter:off
		$fields['size_icon'] -> setValue($config -> getValue($key, 'size_of_provider_icon_px')) 
					-> setRequired(true)
					-> addValidator(new IntValidator(1, 999999));
		// @formatter:on
		$this -> addElement($fields['size_icon']);
				
		//	postion providers on header field
		$fields['position_providers_on_header'] = new TextField('position_providers_on_header');
		// @formatter:off
		$fields['position_providers_on_header'] -> setValue($config -> getValue($key, 'position_providers_on_header')) 
					-> setRequired(true)
					-> addValidator(new IntValidator(1, 999999));
		// @formatter:on
		$this -> addElement($fields['position_providers_on_header']);
		
		//	SignUp Mode
		$fields['signup_mode'] = new RadioField('signup_mode');
		// @formatter:off
		$fields['signup_mode'] ->addOptions(
	            array(
	                '1' => $language->text('ynsocialconnect', 'user_standard_signup_process'),
	                '0' => $language->text('ynsocialconnect', 'user_quick_signup_process')
	            )
	        );
		$fields['signup_mode'] -> setValue($config -> getValue($key, 'signup_mode')) -> setRequired(true);
		// @formatter:on
		$this -> addElement($fields['signup_mode']);
		
		// submit
		$fields['submit'] = new Submit('save');
		$fields['submit'] -> setValue($language ->text('ynsocialconnect', 'btn_save'));
		$this -> addElement($fields['submit']);
		
		//	end
	}

}
