<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * FAQ category edit component
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_faq.components
 * @since 1.0
 */
class OCSFAQ_CMP_CategoryEdit extends OW_Component
{
    public function __construct( $cId )
    {
        parent::__construct();        
       
        $form = new UpdateCategoryForm();
        $this->addForm($form);
        
        $service = OCSFAQ_BOL_FaqService::getInstance();
        $category = $service->findCategoryById($cId);

        if ( $category )
        {
        	$form->getElement('cId')->setValue($category->id);
	        $form->getElement('name')->setValue($category->name);
        }
    }
}

class UpdateCategoryForm extends Form
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct('update-category-form');

        $this->setAction(OW::getRouter()->urlFor('OCSFAQ_CTRL_Admin', 'editCategory'));
        
        $lang = OW::getLanguage();

        $catId = new HiddenField('cId');
        $catId->setRequired(true);
        $this->addElement($catId);
        
        $name = new TextField('name');
        $name->setRequired(true);
        $name->setLabel($lang->text('ocsfaq', 'category'));
        $this->addElement($name);
        
        // submit
        $submit = new Submit('update');
        $submit->setValue($lang->text('ocsfaq', 'btn_save'));
        $this->addElement($submit);
    }
}