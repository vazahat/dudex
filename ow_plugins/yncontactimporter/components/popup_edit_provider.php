<?php
class YNCONTACTIMPORTER_CMP_PopupEditProvider extends OW_Component
{
    public function __construct( $providerId )
    {
        parent::__construct();
        
        $providerEditForm = new YNCONTACTIMPORTER_CLASS_ProviderEditForm($providerId);
        $this->addForm($providerEditForm);

        $provider = YNCONTACTIMPORTER_BOL_ProviderService::getInstance()->findProviderById($providerId);
        
        $providerEditForm->getElement('id')->setValue($providerId);
        $providerEditForm->getElement('title')->setValue($provider->title);
		$providerEditForm->getElement('order')->setValue($provider->order);
		$providerEditForm->getElement('enable')->setValue($provider->enable);
    }
}
?>