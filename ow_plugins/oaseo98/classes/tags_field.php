<?php

/**
 * Copyright (c) 2011 Sardar Madumarov
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package oaseo.classes
 */
class OA_CCLASS_TagsField extends FormElement
{

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct( $name )
    {
        parent::__construct($name);
        $this->value = array();
    }

    /**
     * @param array $value
     * @return OA_CCLASS_TagsField
     */
    public function setValue( $value )
    {
        if ( !is_array($value) )
        {
            throw new InvalidArgumentException('Array expected');
        }

        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getElementJs()
    {
        $values = $this->getValue();
        $initItems = array();

        if ( !empty($values) )
        {
            foreach ( $values as $value )
            {
                $initItems[] = $value;
            }
        }

        $params = array(
            'initItems' => $initItems,
            'labels' => array('too_short' => OW::getLanguage()->text('oaseo', 'too_short_tag_warning_message'), 'duplicate' => OW::getLanguage()->text('oaseo', 'duplicate_tag_warning_message'))
        );

        OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('oaseo')->getStaticJsUrl() . 'oa_tags_field.js');
        $jsString = "var formElement = new OA_TagsField('" . $this->getId() . "', '" . $this->getName() . "', " . json_encode($params) . ");";

        /** @var $value Validator  */
        foreach ( $this->validators as $value )
        {
            $jsString .= "formElement.addValidator(" . $value->getJsValidator() . ");";
        }

        return $jsString;
    }

    public function renderInput( $params = null )
    {
        parent::renderInput($params);

        OW::getDocument()->addStyleDeclaration("
            ul.oa_tags_field{
                background:#fff;
                padding:5px;
                font-family:Arial;
                cursor:text;
                border: 1px solid #ced1da;
                border-top: 1px solid #abadb3;
                border-left:1px solid #abadb3;
                width:95%;
            }

            ul.oa_tags_field input{
                background:none;
                border:none;
            }

            ul.oa_tags_field li.tag{
                display:inline-block;
                background:#e3e3e3;
                padding: 3px 4px 4px 5px;
                margin-right:5px;
                margin-bottom:4px;
                border:1px solid #e3e3e3;
            }

            ul.oa_tags_field li.tag:hover{
                border:1px solid #666;
            }

            ul.oa_tags_field li.new_tag{
                display:inline-block;
            }

            ul.oa_tags_field li.tag a{
                display: inline-block;
                background:red;
                color:#fff;
                padding:0 3px;
                margin-left:4px;
                line-height:14px;
                font: bold 11px Arial;
            }

            ul.oa_tags_field li.tag a:hover{
                text-decoration:none;
            }
        ");

        $values = $this->getValue();
        $valuesString = '<span class="values">';
        $liString = '';

        if ( !empty($values) )
        {
            foreach ( $values as $value )
            {
                $valuesString .= '<input type="hidden" name="' . $this->getName() . '[]" value="' . $value . '" class="tag-' . str_replace(' ', '_', $value) . '" />';
                $liString .= '<li class="tag tag-' . str_replace(' ', '_', $value) . '"><span>' . $value . '</span><a href="javascript://">x</a></li>';
            }
        }

        $valuesString .= '</span>';

        return '<div id="' . $this->getId() . '">' . $valuesString . '<ul class="oa_tags_field">' . $liString . '<li class="new_tag"><input type="text" style="width:18px;" /></ul></div>';
    }
}