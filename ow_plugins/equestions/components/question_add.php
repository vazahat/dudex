<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

require_once OW_DIR_CORE . 'form_element.php';

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package equestions.components
 */
class EQUESTIONS_CMP_QuestionAdd extends OW_Component
{
    public function __construct()
    {
        parent::__construct();

        if ( !EQUESTIONS_BOL_Service::getInstance()->isCurrentUserCanAsk() )
        {
            $this->setVisible(false);

            return;
        }

        $template = OW::getPluginManager()->getPlugin('equestions')->getCmpViewDir() . 'question_add.html';
        $this->setTemplate($template);
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $uniqId = uniqid('questionAdd');
        $this->assign('uniqId', $uniqId);

        $config = OW::getConfig()->getValues(EQUESTIONS_Plugin::PLUGIN_KEY);

        $this->assign('configs', $config);

        $form = $this->initForm();
        $this->addForm($form);

        EQUESTIONS_Plugin::getInstance()->addStatic();

        $attachmentsId = null;

        if ( $config['attachments'] )
        {
            $types = array();
            if ( $config['attachments_image'] )
            {
                $types[] = 'image';
            }

            if ( $config['attachments_video'] )
            {
                $types[] = 'video';
            }

            if ( $config['attachments_link'] )
            {
                $types[] = 'link';
            }

            $attachments = new EQUESTIONS_CMP_Attachments($types);

            $attachments->initJs();
            $this->addComponent('attachments', $attachments);

            $attachmentsId = $attachments->getUniqId();
        }

        $js = UTIL_JsGenerator::newInstance()->newObject('questionsAdd', 'QUESTIONS_QuestionAdd', array($uniqId, $form->getName(), array(
            'maxQuestionLength' => 500,
            'minQuestionLength' => 3,
            'maxAnswerLength' => 150
        ), $attachmentsId));

        OW::getDocument()->addOnloadScript($js);

    }

    public function initForm()
    {
        return new EQUESTIONS_AddForm();
    }
}

class EQUESTIONS_AddForm extends Form
{
    public function __construct()
    {
        parent::__construct('questions_add');

        $language = OW::getLanguage();

        $this->setAjax();
        $this->setAjaxResetOnSuccess(false);

        $field = new Textarea('question');
        $field->addAttribute('maxlength', 500);
        $field->setRequired();
        $field->setHasInvitation(true);
        $field->setInvitation( $language->text('equestions', 'question_add_text_inv') );
        $this->addElement($field);

        $field = new HiddenField('attachment');
        $this->addElement($field);

        $field = new CheckboxField('allowAddOprions');
        $field->addAttribute('checked');
        $field->setLabel( $language->text('equestions', 'question_add_allow_add_opt') );
        $this->addElement($field);

        $field = new EQUESTIONS_OptionsField('answers');
        $field->setHasInvitation(true);
        $field->setInvitation( $language->text('equestions', 'question_add_option_inv') );
        $this->addElement($field);


        $submit = new Submit('save');
        $submit->setValue($language->text('equestions', 'question_add_save'));
        $this->addElement($submit);

        if ( !OW::getRequest()->isAjax() )
        {
            OW::getLanguage()->addKeyForJs('equestions', 'feedback_question_empty');
            OW::getLanguage()->addKeyForJs('equestions', 'feedback_question_min_length');
            OW::getLanguage()->addKeyForJs('equestions', 'feedback_question_max_length');
            OW::getLanguage()->addKeyForJs('equestions', 'feedback_question_two_apt_required');
            OW::getLanguage()->addKeyForJs('equestions', 'feedback_question_dublicate_option');
            OW::getLanguage()->addKeyForJs('equestions', 'feedback_option_max_length');

            $this->initJsResponder();
        }

        $this->setAction( OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlFor('EQUESTIONS_CTRL_List', 'addQuestion')) );
    }

    public function initJsResponder()
    {
        $js = UTIL_JsGenerator::composeJsString(' owForms["questions_add"].bind( "success", function( r )
        {
            var form = owForms["questions_add"];
            if ( r.reset !== false )
            {
                form.getElement("answers").resetValue();
                form.getElement("question").resetValue();
                OW.trigger("questions.after_question_add", [r]);
            }

            if ( r )
            {
                window.QUESTIONS_ListObject.ajaxSuccess(r);
            }

        });');

        OW::getDocument()->addOnloadScript( $js );
    }
}

/**
 * Form element: TextField.
 *
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package ow_core
 * @since 1.0
 */
class EQUESTIONS_OptionsField extends InvitationFormElement
{
    private $itemIds = array();

    /**
     * @see FormElement::renderInput()
     *
     * @param array $params
     * @return string
     */
    public function renderInput( $params = array() )
    {
        $value = $this->getValue();
        $countValue = empty($value) ? 3 : count($value) + 1;
        $count = $countValue > 3 ? $countValue : 3;
        $content = $this->renderItem(-1, true);

        for ( $i=0; $i < $count; $i++ )
        {
            $content .= $this->renderItem($i);
        }

        return UTIL_HtmlTag::generateTag('div', array_merge($this->attributes, $params), true, $content);
    }

    private function renderItem( $index, $proto = false )
    {
        $value = $this->getValue();

        $inputAttrs = array(
            'type' => 'text',
            'maxlength' => 150,
            'name' => $this->getName() . '[]',
            'class' => 'mt-item-input',
            'value' => empty($value[$index]) ? '' : $value[$index]
        );

        $contAttrs = array(
            'class' => 'mt-item ow_smallmargin'
        );

        if ( $proto )
        {
            $inputAttrs['value'] = '';
            $contAttrs['style'] = 'display: none;';
        }

        if ( $this->getHasInvitation() && empty($inputAttrs['value']) )
        {
            $inputAttrs['value'] = $this->invitation;
            $inputAttrs['class'] .= ' invitation';
        }

        $input = UTIL_HtmlTag::generateTag('input', $inputAttrs);

        return UTIL_HtmlTag::generateTag('div', $contAttrs, true, $input);
    }

    public function getElementJs() {

        $js = UTIL_JsGenerator::newInstance()->newObject('formElement', 'QUESTIONS_AnswersField', array(
            $this->getId(), $this->getName(), ($this->getHasInvitation() ? $this->getInvitation() : false)
        ));

        /** @var $value Validator  */
        foreach ( $this->validators as $value )
        {
             $js .= "formElement.addValidator(" . $value->getJsValidator() . ");";
        }

        return  $js;
    }
}
