<?php
class YNCONTACTIMPORTER_CLASS_LoginForm extends Form
{
	public function __construct($providerName)
	{
		parent::__construct('login-form');
		$language = OW::getLanguage();
		$this->setAction("");
		
		$label = $language -> text('yncontactimporter', 'login_email');
		if($providerName == 'hyves')
		{
			$label = $language -> text('yncontactimporter', 'login_username');
		}
		
		// email
		$email = new TextField('email');
		$email 	-> setLabel($label) 
				-> setRequired(true);
		$this -> addElement($email);

		//pass
		$password = new PasswordField('password');
		$password -> setLabel($language -> text('yncontactimporter', 'login_password')) 
				  -> setRequired(true);
		$this -> addElement($password);

		//providerName
		$hiddenProviderName = new HiddenField('providerName');
		$hiddenProviderName->setValue($providerName);
		$this->addElement($hiddenProviderName);
		
		// button submit
		$submit = new Submit('submit');
		$submit -> setValue($language -> text('yncontactimporter', 'submit_btn_label'));
		$this -> addElement($submit);
	}

}
