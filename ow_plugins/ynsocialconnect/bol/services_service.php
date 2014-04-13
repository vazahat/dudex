<?php
/**
 * YNSOCIALCONNECT_BOL_ServicesService
 *
 * @author lytk
 * @package ow_plugins.ynsocialconnect.bol
 * @since 1.0
 */

class YNSOCIALCONNECT_BOL_ServicesService
{
	/*
	 * @var YNSOCIALCONNECT_BOL_ServicesService
	 */
	private static $classInstance;

	/*
	@var YNSOCIALCONNECT_BOL_ServicesDao
	*/
	private $servicesDao;

	private function __construct()
	{
		$this -> servicesDao = YNSOCIALCONNECT_BOL_ServicesDao::getInstance();
	}

	/**
	 * Returns class instance
	 *
	 * @return YNSOCIALCONNECT_BOL_ServicesService
	 */
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
		{
			self::$classInstance = new self();
		}
		return self::$classInstance;
	}

	public function getEnabledProviders($iLimit = 5, $iLimitSelected = 20, $bDisplay = true)
	{
		return $this -> servicesDao -> getEnabledProviders($iLimit, $iLimitSelected, $bDisplay);
	}

	public function getOpenProviders($iLimit = 5, $iLimitSelected = 20, $bDisplay = true)
	{
		return $this -> servicesDao -> getOpenProviders($iLimit, $iLimitSelected, $bDisplay);
	}

	public function getProvider($sService = "")
	{
		return $this -> servicesDao -> getProvider($sService);
	}

	public function updateStatistics($sService, $sType)
	{
		return $this -> servicesDao -> updateStatistics($sService, $sType);
	}

	public function getAllProviders()
	{
		return $this -> servicesDao -> getAllProviders();
	}

	public function updateOrderByServiceId($serviceId, $order)
	{
		return $this -> servicesDao -> updateOrderByServiceId($serviceId, $order);
	}

	public function updateActiveById($serviceId, $active)
	{
		return $this -> servicesDao -> updateActiveById($serviceId, $active);
	}

	public function updateActiveStatusAllServices($active = '1')
	{
		return $this -> servicesDao -> updateActiveStatusAllServices($active);
	}

	public function getProvidersByStatus($bDisplay = true)
	{
		return $this -> servicesDao -> getProvidersByStatus($bDisplay);
	}
	public function getOWQuestionDtoList($providerName)
    {
        $aliases = $this->findAliasList($providerName);
        $questions = BOL_QuestionService::getInstance()->findAllQuestions();
        $out = array();
        foreach ($questions as $question)
        {
            /* @var $question BOL_Question */
            $isText = in_array($question->presentation, array(
                BOL_QuestionService::QUESTION_PRESENTATION_TEXT,
                BOL_QuestionService::QUESTION_PRESENTATION_TEXTAREA,
                BOL_QuestionService::QUESTION_PRESENTATION_URL,
                BOL_Questionservice::QUESTION_PRESENTATION_RADIO,
                BOL_Questionservice::QUESTION_PRESENTATION_BIRTHDATE,
            ));
            $hasAlias = !empty($aliases[$question->name]);

            if ($isText || $hasAlias)
            {
                $out[] = $question;
            }
        }

        return $out;
    }
	/**
	 * get mapping field
	 */
	public function getServiceFields($service)
	{
		$options = YNSOCIALCONNECT_BOL_OptionsDao::getInstance() -> getOptionsByService($service);
		return $options;
	}
	public function assignQuestion($question, $field, $service)
    {
        $fieldDto = YNSOCIALCONNECT_BOL_FieldsDao::getInstance()->findByQuestion($question, $service);
        if ($fieldDto === null)
        {
            $fieldDto = new YNSOCIALCONNECT_BOL_Fields();
        }

        $fieldDto->question = $question;
        $fieldDto->field = $field;
        $fieldDto->service = $service;

        YNSOCIALCONNECT_BOL_FieldsDao::getInstance()->save($fieldDto);
    }

    public function unsetQuestion($question, $service)
    {
        $fieldDto = YNSOCIALCONNECT_BOL_FieldsDao::getInstance()->findByQuestion($question, $service);

        if ($fieldDto === null)
        {
            return;
        }

        YNSOCIALCONNECT_BOL_FieldsDao::getInstance()->delete($fieldDto);
    }
	public function findAliasDtoList($service)
    {
        return YNSOCIALCONNECT_BOL_FieldsDao::getInstance()->findByService($service);
    }
	
	public function findAliasList($service)
    {
        $out = array();
        $aliases = $this->findAliasDtoList($service);
        foreach($aliases as $alias)
        {
            $out[$alias->question] = $alias->field;
        }
        return $out;
	}
}
