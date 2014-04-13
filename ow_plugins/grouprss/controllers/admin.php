<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class GROUPRSS_CTRL_Admin extends ADMIN_CTRL_Abstract
{

    public function __construct()
    {
        parent::__construct();

        if ( OW::getRequest()->isAjax() )
        {
            return;
        }

        $this->setPageHeading(OW::getLanguage()->text('grouprss', 'admin_settings_title'));
        $this->setPageTitle(OW::getLanguage()->text('grouprss', 'admin_settings_title'));
        $this->setPageHeadingIconClass('ow_ic_gear_wheel');
    }

    public function index()
    {
        $language = OW::getLanguage();
        $config = OW::getConfig();

        $adminForm = new Form('adminForm');  
            
        $element = new Selectbox('actionMember');
        $element->setLabel($language->text('grouprss', 'action_member_label'));
        $element->setDescription($language->text('grouprss', 'action_member_desc'));
        $element->setValue($config->getValue('grouprss', 'actionMember'));
        $element->setRequired();
        $element->addOption('admin' , $language->text('grouprss', 'site_admin'));
        $element->addOption('owner' , $language->text('grouprss', 'group_owner'));
        $element->addOption('both' , $language->text('grouprss', 'both_admin_owner'));                
        $adminForm->addElement($element);

        $element = new Selectbox('postLocation');
        $element->setLabel($language->text('grouprss', 'post_location_label'));
        $element->setDescription($language->text('grouprss', 'post_location_desc'));
        $element->setValue($config->getValue('grouprss', 'postLocation'));
        $element->setRequired();
        $element->addOption('wall' , $language->text('grouprss', 'wall_location'));
        $element->addOption('newsfeed' , $language->text('grouprss', 'newsfeed_location'));              
        $adminForm->addElement($element);
        
        $element = new CheckboxField('disablePosting');
        $element->setLabel($language->text('grouprss', 'disable_posting_label'));
        $element->setDescription($language->text('grouprss', 'disable_posting_desc'));
        $element->setValue($config->getValue('grouprss', 'disablePosting'));
        $adminForm->addElement($element);
               
        $element = new Submit('saveSettings');
        $element->setValue(OW::getLanguage()->text('grouprss', 'admin_save_settings'));
        $adminForm->addElement($element);
        
        if ( OW::getRequest()->isPost() )
        {
           if ( $adminForm->isValid($_POST) )
           {
               $values = $adminForm->getValues(); 
               $config->saveConfig('grouprss', 'actionMember', $values['actionMember']);
               $config->saveConfig('grouprss', 'postLocation', $values['postLocation']);               
               $config->saveConfig('grouprss', 'disablePosting', $values['disablePosting']);     

               GROUPRSS_BOL_FeedService::getInstance()->addAllGroupFeed();
               //OW::getFeedback()->info($language->text('grouprss', 'user_save_success'));    
           }
        }

       $this->addForm($adminForm);
   } 
}