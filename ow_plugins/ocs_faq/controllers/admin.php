<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * FAQ administration action controller
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_faq.controllers
 * @since 1.0
 */
class OCSFAQ_CTRL_Admin extends ADMIN_CTRL_Abstract
{    
    /**
     * Default action
     */
    public function index()
    {
        $lang = OW::getLanguage();
                
        OW::getDocument()->setHeading($lang->text('ocsfaq', 'admin_page_heading'));
        OW::getDocument()->setHeadingIconClass('ow_ic_gear_wheel');
        
        $faqService = OCSFAQ_BOL_FaqService::getInstance();
        
        if ( !empty($_GET['qId']) )
        {
        	$faqService->deleteQuestion($_GET['qId']);
        	$this->redirect(OW::getRouter()->urlForRoute('ocsfaq.admin_config'));        	
        }
        else if ( !empty($_GET['catId']) )
        {
        	$faqService->deleteCategory((int) $_GET['catId']);
        }
        
        $form = new AddQuestionForm();
        $this->addForm($form);
        
        $form2 = new UpdateSettingsForm();
        $this->addForm($form2);
        
        $form3 = new AddCategoryForm();
        $this->addForm($form3);
                
        if ( OW::getRequest()->isPost() )
        {
	        if ( $_POST['form_name'] == 'add-question-form' && $form->isValid($_POST) )
	        {
	            $values = $form->getValues();
	
	            $question = new OCSFAQ_BOL_Question();
	            $question->question = trim(htmlspecialchars($values['question']));
	            $question->answer = trim($values['answer']);
	            $question->order = $faqService->getNextOrder();
	            $question->isFeatured = (int) $values['isFeatured'];
	            if ( isset($values['category']) )
	            {
	            	$question->categoryId = (int) $values['category'];
	            }
	                        
	            $faqService->addQuestion($question);
	            OW::getFeedback()->info($lang->text('ocsfaq', 'question_added'));
	            $this->redirect();
	        }
	        else if ( $_POST['form_name'] == 'update-settings-form' && $form2->isValid($_POST) )
	        {
	        	$values = $form2->getValues();
	        	
	        	OW::getConfig()->saveConfig('ocsfaq', 'expand_answers', intval($values['expand']));
	        	OW::getFeedback()->info($lang->text('ocsfaq', 'settings_updated'));
                $this->redirect();
	        }
	        else if ( $_POST['form_name'] == 'add-category-form' && $form3->isValid($_POST) )
	        {
		        $values = $form3->getValues();
		        
		        $faqService->addCategory($values['category']);
		        OW::getFeedback()->info($lang->text('ocsfaq', 'category_added'));
	            $this->redirect();
	        }
        }
        
        OW::getDocument()->addScript(
            OW::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'jquery-ui-1.8.9.custom.min.js'
        );
        
        $lang->addKeyForJs('ocsfaq', 'update_question');
        
        $catAssigned = $faqService->categoriesAssigned();
        $this->assign('catAssigned', $catAssigned);
        
        if ( $catAssigned )
        {
        	$questions = $faqService->getQuestionByCategoriesList();
        }
        else 
        {
        	$questions = $faqService->getQuestionList();
        }
        $this->assign('questions', $questions);
        $this->assign('getQuestionUrl', json_encode(OW::getRouter()->urlForRoute('ocsfaq.admin_get_question')));
        
        $logo = OW::getPluginManager()->getPlugin('ocsfaq')->getStaticUrl() . 'img/oxwallcandystore-logo.jpg';
        $this->assign('logo', $logo);
        
        $categories = $faqService->getCategories();
        $this->assign('categories', $categories);
        
        $js = 
		'$("a.ocs_faq_edit").click(function(){
		    var faqId = $(this).attr("ref");
		    var fb = OW.ajaxFloatBox(
		        "OCSFAQ_CMP_FaqEdit", 
		        [faqId], 
		        {width: 550, title: '.json_encode($lang->text('ocsfaq', 'update_question')).'}
		    );
		});
		
		$("a.ocs_faq_delete").click(function(){
		    var faqId = $(this).attr("ref");
		    if ( confirm('.json_encode($lang->text('base', 'are_you_sure')).') )
		    {
		        document.location.href = "'.OW::getRouter()->urlForRoute('ocsfaq.admin_config').'?qId=" + faqId;
		    }
		    else
		    {
		        return false;
		    }
		});
		
		$(".questions").hover(
			function(){
				$("a.ocs_faq_delete", $(this)).show();
				$("a.ocs_faq_edit", $(this)).show();
			},
			function(){
			    $("a.ocs_faq_delete", $(this)).hide();
			    $("a.ocs_faq_edit", $(this)).hide();
			}
		);
		
		$(".cat_rows").sortable({
		    items: ".category_tr",
		    cursor: "move",
		    placeholder: "ph",
		    forcePlaceholderSize: true,
		    connectWith: ".cat_rows",
		    start: function(event, ui){
		        $(ui.placeholder).append("<td colspan=\"2\"></td>");
		        $(".category_rows").sortable("refreshPositions");
		    },
		    update: function(){
		        var cats = $(".cat_rows").sortable("serialize");
		        var url = '.json_encode(OW::getRouter()->urlForRoute('ocsfaq.admin_cat_reorder')).';
		        $.post(url, cats)' . ($catAssigned ? '.success( function() { document.location.reload(); } )' : '') . ';
		    }
		});
		';

		if ( $categories )
		{
			$js .=
			'$("a.cat_edit").click(function(){
			    var catId = $(this).attr("ref");
			    var fb = OW.ajaxFloatBox(
			        "OCSFAQ_CMP_CategoryEdit", 
			        [catId], 
			        {width: 550, title: '.json_encode($lang->text('base', 'edit')).'}
			    );
			});
			
			$("a.cat_delete").click(function(){
			    var catId = $(this).attr("ref");
			    if ( confirm('.json_encode($lang->text('base', 'are_you_sure')).') )
			    {
			        document.location.href = "'.OW::getRouter()->urlForRoute('ocsfaq.admin_config').'?catId=" + catId;
			    }
			    else
			    {
			        return false;
			    }
			});
			
			$(".category_tr").hover(
				function(){
					$("a.cat_delete", $(this)).show();
					$("a.cat_edit", $(this)).show();
				},
				function(){
				    $("a.cat_delete", $(this)).hide();
				    $("a.cat_edit", $(this)).hide();
				}
			);';
		}

		if ( !$catAssigned )
		{
			$js .=
			'$(".question_rows").sortable({
			    items: ".question_tr",
			    cursor: "move",
			    placeholder: "ph",
			    forcePlaceholderSize: true,
			    connectWith: ".question_rows",
			    start: function(event, ui){
			        $(ui.placeholder).append("<td colspan=\"2\"></td>");
			        $(".question_rows").sortable("refreshPositions");
			    },
			    update: function(){
			        var questions = $(".question_rows").sortable("serialize");
			        
			        var url = '.json_encode(OW::getRouter()->urlForRoute('ocsfaq.admin_reorder')).';
			        $.post(url, questions);
			    }
			});';
		}
		else
		{
			foreach ( $questions as $id => $cat )
			{
				$js .= 
				'$(".question_rows'.$id.'").sortable({
			    items: ".question_tr'.$id.'",
			    cursor: "move",
			    placeholder: "ph",
			    forcePlaceholderSize: true,
			    connectWith: ".question_rows'.$id.'",
			    start: function(event, ui){
			        $(ui.placeholder).append("<td colspan=\"2\"></td>");
			        $(".question_rows'.$id.'").sortable("refreshPositions");
			    },
			    update: function(){
			        var questions = $(".question_rows'.$id.'").sortable("serialize") + "&catId='.$id.'";
			        
			        var url = '.json_encode(OW::getRouter()->urlForRoute('ocsfaq.admin_reorder')).';
			        $.post(url, questions);
			    }
			});
			';
			}
		}

        OW::getDocument()->addOnloadScript($js);
    }
    
    public function ajaxReorder( )
    {
    	$questions = array_flip($_POST['question']);

    	$questionService = OCSFAQ_BOL_FaqService::getInstance();
    	
    	if ( isset($_POST['catId']) )
    	{
	    	$qList = $questionService->findQuestionsByCategoryId($_POST['catId']);
    	}
    	else
    	{
    		$qList = $questionService->getQuestionList();
    	}
    	
    	foreach ( $qList as $q )
    	{
    		$q->order = $questions[$q->id] + 1;printVar($q->order);
    		$questionService->updateQuestion($q);
    	}
    	
    	exit;
    }
    
    public function ajaxCatReorder( )
    {
    	$cats = array_flip($_POST['cat']);

    	$service = OCSFAQ_BOL_FaqService::getInstance();
    	$cList = $service->getCategories();
    	
    	foreach ( $cList as $cat )
    	{
    		$cat->order = $cats[$cat->id] + 1;
    		$service->updateCategory($cat);
    	}
    	
    	exit;
    }
    
    public function editQuestion( )
    {
        if ( OW::getRequest()->isPost() && $_POST['form_name'] == 'update-question-form' )
        {
            $faqService = OCSFAQ_BOL_FaqService::getInstance();

            $values = $_POST;
            $qId = $values['questionId'];
            if ( !$qId || ! $question = $faqService->findQuestionById($qId) )
            {
                $this->redirect(OW::getRouter()->urlForRoute('ocsfaq.admin_config'));
            }

            $question->question = trim(htmlspecialchars($values['question']));
            $question->answer = trim($values['answer']);
            $question->isFeatured = isset($values['isFeatured']) ? $values['isFeatured'] == 'on' : 0;
            $question->categoryId = $values['category'];
                    
            $faqService->updateQuestion($question);

            OW::getFeedback()->info(OW::getLanguage()->text('ocsfaq', 'question_updated'));
        }
        
        $this->redirect(OW::getRouter()->urlForRoute('ocsfaq.admin_config'));
    }
    
    public function editCategory( )
    {
        if ( OW::getRequest()->isPost() && $_POST['form_name'] == 'update-category-form' )
        {
            $faqService = OCSFAQ_BOL_FaqService::getInstance();

            $values = $_POST;
            $cId = $values['cId'];
            if ( !$cId || ! $category = $faqService->findCategoryById($cId) )
            {
                $this->redirect(OW::getRouter()->urlForRoute('ocsfaq.admin_config'));
            }

            $category->name = trim(htmlspecialchars($values['name']));
                    
            $faqService->updateCategory($category);

            OW::getFeedback()->info(OW::getLanguage()->text('ocsfaq', 'category_updated'));
        }
        
        $this->redirect(OW::getRouter()->urlForRoute('ocsfaq.admin_config'));
    }
}

class AddQuestionForm extends Form
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct('add-question-form');

        $lang = OW::getLanguage();

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
        $submit = new Submit('add');
        $submit->setValue($lang->text('ocsfaq', 'btn_add_question'));
        $this->addElement($submit);
    }
}

class AddCategoryForm extends Form
{
	public function __construct()
	{
		parent::__construct('add-category-form');
		
		$lang = OW::getLanguage();
		
		$category = new TextField('category');
		$category->setRequired(true);
		$category->setHasInvitation(true);
		$category->setInvitation($lang->text('ocsfaq', 'category'));
		$category->setId("category_input");

		$this->addElement($category);
		
		$submit = new Submit('add');
		$submit->setValue($lang->text('ocsfaq', 'btn_add_category'));
		$this->addElement($submit);
	}
}

class UpdateSettingsForm extends Form
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct('update-settings-form');

        $lang = OW::getLanguage();
        
        $expand = new CheckboxField('expand');
        $expand->setLabel($lang->text('ocsfaq', 'expand_answers'));
        if ( OW::getConfig()->getValue('ocsfaq', 'expand_answers') )
        {
        	$expand->setValue(1);
        }
        $this->addElement($expand);
        
        // submit
        $submit = new Submit('update');
        $submit->setValue($lang->text('ocsfaq', 'btn_save'));
        $this->addElement($submit);
    }
}