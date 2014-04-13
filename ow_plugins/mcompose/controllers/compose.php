<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package mcompose.controllers
 */
class MCOMPOSE_CTRL_Compose extends MAILBOX_CTRL_Mailbox
{
    /**
     *
     * @var MCOMPOSE_BOL_Service
     */
    private $service;

    public function __construct()
    {
        parent::__construct();

        $this->service = MCOMPOSE_BOL_Service::getInstance();
    }

    public function init()
    {
        parent::init();
    }

    public function index()
    {
        $recipients = null;
        $context = MCOMPOSE_BOL_Service::CONTEXT_USER;
        
        if ( !empty($_GET["context"]) )
        {
            $context = $_GET["context"];
        }
        
        if ( !empty($_GET["recipients"]) )
        {
            $recipients = $_GET["recipients"];
        }
        
        $sendMessage = new MCOMPOSE_CMP_SendMessage($recipients, MCOMPOSE_BOL_Service::CONTEXT_USER);
        $this->addComponent("sendMessage", $sendMessage);
    }

    public function rsp()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect403Exception;
        }

        if ( !OW::getUser()->isAuthenticated() )
        {
            echo json_encode(array());
            exit;
        }

        $kw = $_GET['term'];
        $context = empty($_GET["context"]) ? MCOMPOSE_BOL_Service::CONTEXT_USER : $_GET["context"];
        $userId = OW::getUser()->getId();

        $entries = $this->service->getSuggestEntries($userId, $kw, null, $context);
        
        echo json_encode($entries);
        exit;
    }

    public function send( $params )
    {
        if ( !OW::getUser()->isAuthenticated() )
        {
            echo json_encode(array());
            exit;
        }

        $userId = OW::getUser()->getId();
        $formName = $params["formName"];

        $context = empty($_GET["context"]) ? MCOMPOSE_BOL_Service::CONTEXT_USER : $_GET["context"];
        $form = new MCOMPOSE_CLASS_Form($formName, $userId, $context, false);

        $out = array();

        if ( $form->isValid($_POST) )
        {
            $out = $form->process();
        }

        echo json_encode($out);
        exit;
    }
}