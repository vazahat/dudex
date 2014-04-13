<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package mcompose.classes
 * @since 1.0
 */
class MCOMPOSE_CLASS_UserSelectField extends FormElement
{
    protected $data = array();

    protected $classAttr = array();

    protected $invitation;

    protected $groups;
    
    protected $context;
    
    protected $staticInitComplete = false;

    protected $groupDefaults = array(
        'priority' => 0,
        'alwaysVisible' => true,
        'noMatchMessage' => false,
        /*'noMatchMessage' => array(
            'prefix' => 'mcompose',
            'key' => 'selector_no_matches'
        )*/
    );

    /**
     *
     * @var MCOMPOSE_BOL_Service
     */
    private $service;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct( $name, $invitation = null, $context = MCOMPOSE_BOL_Service::CONTEXT_USER )
    {
        parent::__construct($name);

        if ( !empty($invitation) )
        {
            $this->setInvitation($invitation);
        }

        $this->context = $context;
        
        $this->addClass('mc-user-select');
        $this->addClass('jhtmlarea');

        $this->service = MCOMPOSE_BOL_Service::getInstance();
    }

    public function setData( $data )
    {
        $this->data = $data;
    }
    
    public function setInvitation( $invitation )
    {
        $this->invitation = $invitation;
    }

    public function addClass( $class )
    {
        $this->classAttr[] = $class;
    }

    public function setupGroup( $group, $settings = array() )
    {
        $this->groups[$group] = isset($this->groups[$group])
                ? $this->groups[$group]
                : $this->groupDefaults;

        $this->groups[$group] = array_merge($this->groups[$group], $settings);
    }

    /**
     * @see FormElement::renderInput()
     *
     * @param array $params
     * @return string
     */
    public function renderInput( $params = null )
    {
        parent::renderInput($params);

        $staticUrl = OW::getPluginManager()->getPlugin('mcompose')->getStaticUrl();

        OW::getDocument()->addStyleSheet($staticUrl . 'select2.css');
        OW::getDocument()->addScript($staticUrl . 'select2.js');
        OW::getDocument()->addStyleSheet($staticUrl . 'style.css');
        OW::getDocument()->addScript($staticUrl . 'script.js');

        $this->addAttribute('type', 'hidden');
        $this->addAttribute('style', 'width: 100%');


        $imagesUrl = OW::getPluginManager()->getPlugin('base')->getStaticCssUrl();

        $css = array(
            '.mc-tag-bg { background-image: url(' . $imagesUrl . 'images/tag_bg.png); };'
        );

        OW::getDocument()->addStyleDeclaration(implode("\n", $css));

        return UTIL_HtmlTag::generateTag('input', $this->attributes) 
                . '<div class="us-field-fake"><input type="text" class="ow_text invitation" value="' . $this->invitation . '" /></div>';
    }

    public function getElementJs()
    {
        $options = array(
            "multiple" => true,
            "width" => "copy",
            "containerCssClass" => implode(' ', $this->classAttr),
            "dropdownCssClass" => 'ow_bg_color ow_border mc-dropdown ow_small',
            "placeholder" => $this->invitation,
            'multiple' => true,
            "minimumInputLength" => 1,
            "maximumSelectionSize" => OW::getConfig()->getValue('mcompose', 'max_users')
        );

        $settings = array();
        $settings['rspUrl'] = OW::getRouter()->urlFor('MCOMPOSE_CTRL_Compose', 'rsp');
        $settings['groups'] = $this->groups;
        $settings['groupDefaults'] = $this->groupDefaults;
        $settings['context'] = $this->context;

        OW::getLanguage()->addKeyForJs('mcompose', 'selector_searching');
        OW::getLanguage()->addKeyForJs('mcompose', 'selector_no_matches');
        OW::getLanguage()->addKeyForJs('mcompose', 'selector_too_many');

        $js = UTIL_JsGenerator::newInstance();
        $js->addScript('var formElement = new MCOMPOSE.UserSelectorFormElement({$id}, {$name});', array(
            'name' => $this->getName(),
            'id' => $this->getId()
        ));
        
        $js->addScript('formElement.init("#" + {$id}, {$settings}, {$options}, {$data});', array(
            'id' => $this->getId(),
            'settings' => $settings,
            'options' => $options,
            'data' => $this->data
        ));
        
        if ( !empty($this->value) ) 
        {
            $js->callFunction(array('formElement', 'setValue'), array($this->value));
        }
        
        /** @var $value Validator  */
        foreach ( $this->validators as $value )
        {
            $js->addScript("formElement.addValidator(" . $value->getJsValidator() . ");");
        }

        $this->staticInitComplete = true;
        
        return $js->generateJs();
    }
}