<?php
class YNSOCIALCONNECT_CLASS_ConfigFieldsForm extends Form
{
/**
     * Class constructor
     */
    public function __construct( $providerName )
    {
        parent::__construct('provider-config-form');
        
        $this->setAjax(true);
		$this->bindJsFunction(Form::BIND_SUCCESS, 'function(data){if( data.result ){OW.info(data.message);setTimeout(function(){location.reload();}, 1000);}else{OW.error(data.message);}}');
        $this->setAction(OW::getRouter()->urlForRoute('ynsocialconnect-admin-ajaxUpdateProfileQuestion'));
        $language = OW::getLanguage();
		
		$service = YNSOCIALCONNECT_BOL_ServicesService::getInstance();
        $questionDtoList = $service -> getOWQuestionDtoList($providerName);
		$aliases = $service-> findAliasList($providerName);
		$options = $service -> getServiceFields($providerName);
        foreach ( $questionDtoList as $question )
        {
        	$new_element = new Selectbox('alias['.$question -> name.']');
			foreach ($options as $option) 
			{
				$new_element -> addOption($option -> name, $option -> label);
			}
			$new_element -> setValue(empty($aliases[$question->name]) ? '' : $aliases[$question->name]);
			$this->addElement($new_element);
        }
		
		$hidden = new TextField('providerName');
		$hidden -> addAttribute('type', 'hidden');
		$hidden -> setValue($providerName);
		$this->addElement($hidden);
		
        $submit = new Submit('edit');
        $submit->setValue($language->text('ynsocialconnect', 'save_btn_label'));
        $this->addElement($submit);
    }
}
?>