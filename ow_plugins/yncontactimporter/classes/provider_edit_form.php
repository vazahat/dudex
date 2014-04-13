<?php
class YNCONTACTIMPORTER_CLASS_ProviderEditForm extends Form
{
/**
     * Class constructor
     */
    public function __construct( $providerId )
    {
        parent::__construct('provider-edit-form');
        
        $this->setAjax(true);
		$this->bindJsFunction(Form::BIND_SUCCESS, 'function(data){if( data.result ){OW.info(data.message);setTimeout(function(){location.reload();}, 1000);}else{OW.error(data.message);}}');
        
        $this->setAction(OW::getRouter()->urlForRoute('yncontactimporter-admin-ajaxEditProvider'));
        
        $language = OW::getLanguage();
        $provider = YNCONTACTIMPORTER_BOL_ProviderService::getInstance()->findProviderById($providerId);

        // provider id field
        $providerIdField = new HiddenField('id');
        $providerIdField->setRequired(true);
        $this->addElement($providerIdField);
		
		// provider title
		$providerTitle = new TextField('title');
		$providerTitle->setLabel($language->text('yncontactimporter', 'provider_title'));
		$providerTitle->setRequired(true);
		$this->addElement($providerTitle);
		for($i = 1; $i <= 10; $i ++)
			$option[$i] = $i;
		
		// provider order
		$providerOrder = new Selectbox('order');
		$providerOrder->setLabel($language->text('yncontactimporter', 'order'));
		$providerOrder->addOptions($option);
		$providerOrder->setHasInvitation(false);
		$this->addElement($providerOrder);
		
		// provider enable
		$providerEnable = new Selectbox('enable');
		$providerEnable->setLabel($language->text('yncontactimporter', 'enabled_disabled'));
		$providerEnable->addOptions(array(1 => 'Enabled', 0 => 'Disabled'));
		$providerEnable->setHasInvitation(false);
		$this->addElement($providerEnable);

        $submit = new Submit('edit');
        $submit->setValue($language->text('yncontactimporter', 'save_btn_label'));
        $this->addElement($submit);
    }
}
?>