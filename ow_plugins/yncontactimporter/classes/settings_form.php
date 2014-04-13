<?php
class YNCONTACTIMPORTER_CLASS_SettingsForm extends Form
{
	public function __construct($ctrl)
	{
		parent::__construct('settings-form');
		$configs = OW::getConfig()->getValues('yncontactimporter');

        $ctrl->assign('configs', $configs);

        $l = OW::getLanguage();
		
		$miValidator = new IntValidator(1, 999);
					$miValidator -> setErrorMessage($l -> text('yncontactimporter', 'max_validation_error', array(
						'min' => 1,
						'max' => 999
					)));
		
		//Contacts per page
        $textField['contact_per_page'] = new TextField('contact_per_page');
        $textField['contact_per_page']->setLabel($l->text('yncontactimporter', 'settings_contact_per_page'))
            ->setValue($configs['contact_per_page'])
            ->addValidator($miValidator)
            ->setRequired(true);
        $this->addElement($textField['contact_per_page']);
		
		//Maximum invite per times
        $textField['max_invite_per_times'] = new TextField('max_invite_per_times');
        $textField['max_invite_per_times']->setLabel($l->text('yncontactimporter', 'settings_max_invite_per_times'))
            ->setValue($configs['max_invite_per_times'])
            ->addValidator($miValidator)
            ->setRequired(true);
        $this->addElement($textField['max_invite_per_times']);

		//Default invite message
        $textField['default_invite_message'] = new Textarea('default_invite_message');
        $textField['default_invite_message']->setLabel($l->text('yncontactimporter', 'settings_default_invite_message'))
            ->setValue($configs['default_invite_message']);
        $this->addElement($textField['default_invite_message']);
		
		// Logo width
        $textField['logo_width'] = new TextField('logo_width');
        $textField['logo_width']->setLabel($l->text('yncontactimporter', 'settings_logo_width'))
            ->setValue($configs['logo_width'])
            ->addValidator($miValidator)
            ->setRequired(true);
        $this->addElement($textField['logo_width']);
		
		// Logo Height
        $textField['logo_height'] = new TextField('logo_height');
        $textField['logo_height']->setLabel($l->text('yncontactimporter', 'settings_logo_height'))
            ->setValue($configs['logo_height'])
            ->addValidator($miValidator)
            ->setRequired(true);
        $this->addElement($textField['logo_height']);
		

        $submit = new Submit('submit');
        $submit->setValue($l->text('yncontactimporter', 'save_btn_label'));
		$submit->addAttribute('class', 'ow_ic_save ow_positive');
        $this->addElement($submit);
	}

}
