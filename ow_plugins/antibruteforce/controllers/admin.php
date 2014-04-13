<?php

/**
 * Copyright (c) 2013, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 *
 * @author Kairat Bakytow
 * @package ow_plugins.antibruteforce.controllers
 * @since 1.0
 */
class ANTIBRUTEFORCE_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    public function index( array $params = array() )
    {
        $config = OW::getConfig();
        $configs = $config->getValues( 'antibruteforce' );
        
        $form = new Form( 'settings' );
        $form->setAjax();
        $form->setAjaxResetOnSuccess( false );
        $form->setAction( OW::getRouter()->urlForRoute('antibruteforce.admin') );
        $form->bindJsFunction(Form::BIND_SUCCESS, 'function(data){if(data.result){OW.info("Settings successfuly saved");}else{OW.error("Parser error");}}');
        
        $auth = new CheckboxField( 'auth' );
        $auth->setValue( $configs['authentication'] );
        $form->addElement( $auth );
        
        $reg = new CheckboxField( 'reg' );
        $reg->setValue( $configs['registration'] );
        $form->addElement( $reg );
        
        $tryCount = new TextField( 'tryCount' );
        $tryCount->setRequired();
        $tryCount->addValidator( new IntValidator(1) );
        $tryCount->setValue( $configs['try_count'] );
        $form->addElement( $tryCount );
        
        $expTime = new TextField( 'expTime' );
        $expTime->setRequired();
        $expTime->setValue( $configs['expire_time'] );
        $expTime->addValidator( new IntValidator(1) );
        $form->addElement( $expTime );
        
        $title = new TextField( 'title' );
        $title->setRequired();
        $title->setValue( $configs['lock_title'] );
        $form->addElement( $title );
        
        $desc = new Textarea( 'desc' );
        $desc->setValue( $configs['lock_desc'] );
        $form->addElement( $desc );
        
        $submit = new Submit( 'save' );
        $form->addElement( $submit );
        
        $this->addForm( $form );
        
        if ( OW::getRequest()->isAjax() )
        {
            if ( $form->isValid($_POST) )
            {
                $config->saveConfig( 'antibruteforce', 'authentication', $form->getElement('auth')->getValue() );
                $config->saveConfig( 'antibruteforce', 'registration', $form->getElement('reg')->getValue() );
                $config->saveConfig( 'antibruteforce', 'try_count', $form->getElement('tryCount')->getValue() );
                $config->saveConfig( 'antibruteforce', 'expire_time', $form->getElement('expTime')->getValue() );
                $config->saveConfig( 'antibruteforce', 'lock_title', strip_tags($form->getElement('title')->getValue()) );
                $config->saveConfig( 'antibruteforce', 'lock_desc', strip_tags($form->getElement('desc')->getValue()) );
                
                exit( json_encode(array('result' => true)) );
            }
        }
    }
}
