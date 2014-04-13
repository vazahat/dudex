<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * FAQ action controller
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_faq.controllers
 * @since 1.0
 */
class OCSFAQ_CTRL_Faq extends OW_ActionController
{    
    /**
     * Default action
     */
    public function index()
    {
        $lang = OW::getLanguage();
                
        OW::getDocument()->setHeading($lang->text('ocsfaq', 'faq_page_heading'));
        OW::getDocument()->setHeadingIconClass('ow_ic_help');
        
        $faqService = OCSFAQ_BOL_FaqService::getInstance();
        
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
        
        $this->assign('expand', OW::getConfig()->getValue('ocsfaq', 'expand_answers'));
    }
}