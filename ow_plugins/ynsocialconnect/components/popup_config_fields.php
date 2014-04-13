<?php
class YNSOCIALCONNECT_CMP_PopupConfigFields extends OW_Component
{
    public function __construct( $providerName )
    {
        parent::__construct();
        $providerConfigForm = new YNSOCIALCONNECT_CLASS_ConfigFieldsForm($providerName);
        $this->addForm($providerConfigForm);
		$service = YNSOCIALCONNECT_BOL_ServicesService::getInstance();
        $provider = $service->getProvider($providerName);
		$this -> assign('provider', $provider->getTitle());
        $questionDtoList = $service -> getOWQuestionDtoList($providerName);
		$questionList = array();
        foreach ( $questionDtoList as $dto )
        {
            $questionList[$dto->sectionName][(int) $dto->sortOrder] = array(
                'name' => $dto->name,
                'el_name' => 'alias['.$dto->name.']'
            );
        }
		
		$questionSectionDtoList = BOL_QuestionService::getInstance()->findAllSections();
        $tplQuestionList = array();
        foreach ( $questionSectionDtoList as $sectionDto )
        {
            if ( empty($questionList[$sectionDto->name]) )
            {
                continue;
            }
            /* @var $sectionDto BOL_QuestionSection */
            $tplQuestionList[(int) $sectionDto->sortOrder] = array(
                'name' => $sectionDto->name,
                'items' => $questionList[$sectionDto->name]
            );
        }
        ksort($tplQuestionList);
        $this->assign('questionList', $tplQuestionList);
    }
}
?>