<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package ow_plugins.YNSOCIALSTREAM.controllers
 * @since 1.0
 */
class YNSOCIALSTREAM_CTRL_Admin extends ADMIN_CTRL_Abstract
{
	

    /**
     * Default action
     */
    public function globalSettings()
    {    	
    	//insert language
    	//OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('ynsocialstream')->getRootDir().'langs.zip', 'ynsocialstream');
    	
       	
    	
        $language = OW::getLanguage();

        $this->setPageHeading($language->text('ynsocialstream', 'admin_page_heading'));
        $this->setPageTitle($language->text('ynsocialstream', 'admin_page_title'));
        $this->setPageHeadingIconClass('ow_ic_comment');        
      
      	$configs = OW::getConfig()->getValues('ynsocialstream');

        $this->assign('configs', $configs);

        $form = new YNSOCIALSTREAM_ConfigSaveForm($configs);
       
        $this->addForm($form);
       
        if ( OW::getRequest()->isPost() && $form->isValid($_POST) )
        {
            if ( $form->process($_POST) )
            {            	
                OW::getFeedback()->info($language->text('ynsocialstream', 'settings_updated'));
                //$this->redirect(OW::getRouter()->urlForRoute('ynsocialstream-global-settings'));
            }
        }       
        //$this->addComponent('menu', $this->getMenu());
    }

    

    // private function getMenu()
    // {
        // $language = OW::getLanguage();
// 
        // $menuItems = array();
// 
        // $item = new BASE_MenuItem();
        // $item->setLabel($language->text('ynsocialstream', 'admin_menu_item_global_settings'));
        // $item->setUrl(OW::getRouter()->urlForRoute('ynsocialstream-global-settings'));
        // $item->setKey('ynsocialstream-global-settings');
        // $item->setIconClass('ow_ic_gear_wheel');
        // $item->setOrder(0);
// 
        // $menuItems[] = $item;
// // 
        // // $item = new BASE_MenuItem();
        // // $item->setLabel($language->text('ynsocialstream', 'admin_menu_item_level_settings'));
        // // $item->setUrl(OW::getRouter()->urlForRoute('ynsocialstream.level_settings'));
        // // $item->setKey('ynsocialstream_level_settings');
        // // $item->setIconClass('ow_ic_files');
        // // $item->setOrder(1);
// // 
        // // $menuItems[] = $item;
// 
        // return new BASE_CMP_ContentMenu($menuItems);
    // }
}

/**
 * Save photo configuration form class
 */
class YNSOCIALSTREAM_ConfigSaveForm extends Form
{

    /**
     * Class constructor
     *
     */
    public function __construct( $configs )
    {
        parent::__construct('YNSOCIALSTREAM_ConfigSaveForm');

        $language = OW::getLanguage();

        $field = new RadioField('get_feed_cron');
        $field->setLabel($language->text('ynsocialstream', 'admin_get_feed_cron_label'));
        $field->setValue($configs['get_feed_cron']);
        $field->addOptions(array('1' => $language->text('admin', 'permissions_index_yes'), '0' => $language->text('admin', 'permissions_index_no')));
        $this->addElement($field);
		
		
		$fbValidator = new IntValidator(1, 999);
					$fbValidator -> setErrorMessage($language -> text('ynsocialstream', 'max_validation_error_fb', array(
						'min' => 1,
						'max' => 999
					)));						
        $field = new TextField('max_facebook_get_feed');
        $field->setValue($configs['max_facebook_get_feed']);
        $field->setRequired(true);       
        $field->addValidator($fbValidator);
        $field->setLabel($language->text('ynsocialstream', 'admin_max_facebook_get_feed_label'));
        $this->addElement($field);
		
		$twValidator = new IntValidator(1, 20);
					$twValidator -> setErrorMessage($language -> text('ynsocialstream', 'max_validation_error_tw', array(
						'min' => 1,
						'max' => 20
					)));
        
        $field = new TextField('max_twitter_get_feed');
        $field->setValue($configs['max_twitter_get_feed']);
        $field->setRequired(true);        
        $field->addValidator($twValidator);
        $field->setLabel($language->text('ynsocialstream', 'admin_max_twitter_get_feed_label'));
        $this->addElement($field);
		
		
        
        $field = new TextField('max_linkedin_get_feed');
        $field->setValue($configs['max_linkedin_get_feed']);
        $field->setRequired(true);       
		$liValidator = new IntValidator(1, 20);
					$liValidator -> setErrorMessage($language -> text('ynsocialstream', 'max_validation_error_li', array(
						'min' => 1,
						'max' => 20
					))); 
        $field->addValidator($liValidator);
        $field->setLabel($language->text('ynsocialstream', 'admin_max_linkedin_get_feed_label'));
        $this->addElement($field); 

        // submit
        $submit = new Submit('save');
        $submit->setValue($language->text('ynsocialstream', 'admin_save_btn'));
        $this->addElement($submit);
    }

    /**
     * Updates photo plugin configuration
     *
     * @return boolean
     */
    public function process( $data )
    {
        $config = OW::getConfig();

        $config->saveConfig('ynsocialstream', 'get_feed_cron', $data['get_feed_cron']);
        $config->saveConfig('ynsocialstream', 'max_facebook_get_feed', $data['max_facebook_get_feed']);
        $config->saveConfig('ynsocialstream', 'max_twitter_get_feed', $data['max_twitter_get_feed']);
        $config->saveConfig('ynsocialstream', 'max_linkedin_get_feed', $data['max_linkedin_get_feed']);       
     
        return true;
    }
}

class YNSOCIALSTREAM_LevelSettingsForm extends Form
{

    public function __construct( $configs )
    {
        parent::__construct('YNSOCIALSTREAM_LevelSettingsForm');

        $language = OW::getLanguage();
        
        $field = new CheckboxGroup('level_id');
        $field->setLabel($language->text('ynsocialstream', 'admin_level_id_label'));
        $field->setValue($configs['get_feed']);
        $field->addOptions(array('1' => $language->text('admin', 'permissions_index_yes'), '0' => $language->text('admin', 'permissions_index_no')));
        $this->addElement($field);
        
        $field = new RadioField('get_feed');
        $field->setLabel($language->text('ynsocialstream', 'admin_get_feed_label'));
        $field->setValue($configs['get_feed']);
        $field->addOptions(array('1' => $language->text('admin', 'permissions_index_yes'), '0' => $language->text('admin', 'permissions_index_no')));
        $this->addElement($field);
        
        

        $btn = new Submit('save');
        $btn->setValue($language->text('ynsocialstream', 'save_customization_btn_label'));
        $this->addElement($btn);
    }

    public function process( $data, $types )
    {
        $changed = false;
        $configValue = json_decode(OW::getConfig()->getValue('YNSOCIALSTREAM', 'disabled_action_types'), true);
        $typesToSave = array();

        foreach ( $types as $type )
        {
            $typesToSave[$type] = isset($data[$type]);
            if ( !isset($configValue[$type]) || $configValue[$type] !== $typesToSave[$type] )
            {
                $changed = true;
            }
        }

        $jsonValue = json_encode($typesToSave);
        OW::getConfig()->saveConfig('YNSOCIALSTREAM', 'disabled_action_types', $jsonValue);

        return $changed;
    }
}