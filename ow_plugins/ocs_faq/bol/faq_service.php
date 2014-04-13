<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * FAQ Service Class.  
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_faq.bol
 * @since 1.0
 */
final class OCSFAQ_BOL_FaqService
{
    /**
     * @var OCSFAQ_BOL_QuestionDao
     */
    private $questionDao;
    /**
     * @var OCSFAQ_BOL_CategoryDao
     */
    private $categoryDao;
    /**
     * Class instance
     *
     * @var OCSFAQ_BOL_FaqService
     */
    private static $classInstance;

    /**
     * Class constructor
     *
     */
    private function __construct()
    {
        $this->questionDao = OCSFAQ_BOL_QuestionDao::getInstance();
        $this->categoryDao = OCSFAQ_BOL_CategoryDao::getInstance();
    }

    /**
     * Returns class instance
     *
     * @return OCSFAQ_BOL_FaqService
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * Adds question
     *
     * @param OCSFAQ_BOL_Question $question
     * @return int
     */
    public function addQuestion( OCSFAQ_BOL_Question $question )
    {
        $this->questionDao->save($question);

        return $question->id;
    }
    
    public function updateQuestion( OCSFAQ_BOL_Question $question )
    {
        $this->questionDao->save($question);

        return $question->id;
    }
    
    public function deleteQuestion( $questionId )
    {
    	$this->questionDao->deleteById($questionId);
    	
    	return true;
    }
    	
	public function findQuestionById( $questionId )
	{
		return $this->questionDao->findById($questionId);
	}
	
	public function getQuestionList()
	{
		return $this->questionDao->findAllQuestions();		
	}
	
	public function getQuestionByCategoriesList()
	{
		$result = array();
		
		$unassigned = $this->questionDao->findUnassignedQuestions();
		
		if ( $unassigned )
		{
			$result[0]['name'] = OW::getLanguage()->text('ocsfaq', 'category_general');
			$result[0]['questions'] = $unassigned;
		}
		
		$categories = $this->getCategories();
		
		if ( $categories )
		{
			foreach ( $categories as $cat )
			{
				$questions = $this->questionDao->findListByCategoryId($cat->id);
				if ( !$questions )
				{
					continue;
				}
				
				$result[$cat->id]['name'] = $cat->name;
				$result[$cat->id]['questions'] = $questions;
			}
		}
		
		return $result;
	}
	
    public function getFeaturedQuestionList()
    {
        return $this->questionDao->findFeaturedQuestions();      
    }
	
	public function getNextOrder()
	{
		return $this->questionDao->getMaxOrder() + 1;
	}
	
	public function addCategory( $name )
	{
		if ( !mb_strlen($name) )
		{
			return false;
		}
		
		$category = new OCSFAQ_BOL_Category();
		$category->name = trim($name);
		$category->order = $this->categoryDao->getMaxOrder() + 1;
		
		$this->categoryDao->save($category);
		
		return true;
	}
	
	public function updateCategory( OCSFAQ_BOL_Category $cat )
	{
		$this->categoryDao->save($cat);
	}
	
	public function getCategories()
	{
		return $this->categoryDao->getList();
	}
	
	public function findCategoryById( $id )
	{
		if ( !$id )
		{
			return false;
		}
		
		return $this->categoryDao->findById($id);
	}
	
	public function deleteCategory( $id )
	{
		if ( !$id )
		{
			return false;
		}
		
		$questions = $this->findQuestionsByCategoryId($id);
		
		if ( $questions )
		{
			foreach ( $questions as $q )
			{
				$q->categoryId = 0;
				$this->questionDao->save($q);
			}
		}
		
		$this->categoryDao->deleteById($id);
		
		return true;
	}
	
	public function findQuestionsByCategoryId( $id )
	{	
		return $this->questionDao->findListByCategoryId($id);
	}
	
	public function categoriesAssigned()
	{
		return (bool) $this->questionDao->countQuestionsAssignedToCategories();
	}
}