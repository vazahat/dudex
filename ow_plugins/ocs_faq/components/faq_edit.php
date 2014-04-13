<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * FAQ question edit component
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_faq.components
 * @since 1.0
 */
class OCSFAQ_CMP_FaqEdit extends OW_Component
{
    public function __construct( $qId )
    {
        parent::__construct();        
       
        $form = new UpdateQuestionForm();
        $this->addForm($form);
        
        $service = OCSFAQ_BOL_FaqService::getInstance();
        $question = $service->findQuestionById($qId);

        $categories = $service->getCategories();
        $this->assign('categories', $categories);
        
        if ( $question )
        {
	        $form->getElement('questionId')->setValue($qId);
	        $form->getElement('question')->setValue($question->question);
	        $form->getElement('answer')->setValue($question->answer);
	        $form->getElement('isFeatured')->setValue($question->isFeatured);
	        if ( $categories )
	        {
	        	$form->getElement('category')->setValue($question->categoryId);
	        }
        }
    }
}

class UpdateQuestionForm extends Form
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct('update-question-form');

        $this->setAction(OW::getRouter()->urlFor('OCSFAQ_CTRL_Admin', 'editQuestion'));
        
        $lang = OW::getLanguage();

        $questionId = new HiddenField('questionId');
        $questionId->setRequired(true);
        $this->addElement($questionId);
        
        $question = new TextField('question');
        $question->setRequired(true);
        $question->setLabel($lang->text('ocsfaq', 'question'));
        $this->addElement($question);
        
        $btnSet = array(BOL_TextFormatService::WS_BTN_IMAGE, BOL_TextFormatService::WS_BTN_VIDEO, BOL_TextFormatService::WS_BTN_HTML);
        $answer = new WysiwygTextarea('answer', $btnSet);
        $answer->setRequired(true);
        $answer->setLabel($lang->text('ocsfaq', 'answer'));
        $this->addElement($answer);

        $isFeatured = new CheckboxField('isFeatured');
        $isFeatured->setLabel($lang->text('ocsfaq', 'is_featured'));
        $this->addElement($isFeatured);
        
        $categories = OCSFAQ_BOL_FaqService::getInstance()->getCategories();
        
        if ( $categories )
        {
        	$category = new Selectbox('category');
        	foreach ( $categories as $cat )
        	{
        		$category->addOption($cat->id, $cat->name);
        	}
        	$category->setLabel($lang->text('ocsfaq', 'category'));
        	$this->addElement($category);
        }
        
        // submit
        $submit = new Submit('update');
        $submit->setValue($lang->text('ocsfaq', 'btn_save'));
        $this->addElement($submit);
    }
}