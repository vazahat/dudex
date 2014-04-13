<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package mcompose.components
 * @since 1.0
 */
class MCOMPOSE_CMP_SendMessage extends OW_Component
{
    public function __construct( $recipients = null, $context = MCOMPOSE_BOL_Service::CONTEXT_USER, $inPopup = true ) 
    {
        parent::__construct();
     
        $recipients = empty($recipients) ? array() : $recipients;
        
        $userId = OW::getUser()->getId();

        $isAuthorized = OW::getUser()->isAuthorized( 'mailbox', 'send_message' );

        $this->assign('isAuthorized', $isAuthorized);
        $this->assign('permissionMessage', OW::getLanguage()->text('mailbox', 'write_permission_denied'));

        if ( !$isAuthorized )
        {
            return;
        }
        
        $mailboxConfigs = OW::getConfig()->getValues('mailbox');

        $form = new MCOMPOSE_CLASS_Form(uniqid("mcmpose_send_message_form"), $userId, $context, true, $inPopup);
        
        $event = new OW_Event(MCOMPOSE_BOL_Service::EVENT_ON_INPUT_INIT, array(
            "input" => $form->getElement("recipients"),
            "userId" => $userId,
            "context" => $context
        ));
        OW::getEventManager()->trigger($event);

        $preloadedData = MCOMPOSE_BOL_Service::getInstance()->getSuggestEntries($userId, null, $recipients, $context);
        $values = array();

        foreach ( $recipients as $r )
        {
            if ( !empty($preloadedData[$r]) )
            {
                $values[] = $preloadedData[$r];
            }
        }

        $form->getElement("recipients")->setData($preloadedData);
        $form->getElement("recipients")->setValue($values);
        
        $this->addForm($form);
        
        $attachmentsInput = $form->getElement("attachments");
        if ( !empty($attachmentsInput) ) 
        {
            $this->assign("attachmentsId", $attachmentsInput->getId());
        }
        
        $this->assign("formName", $form->getName());
        
        $displayCaptcha = true;

        $this->assign('enableAttachments', !empty($mailboxConfigs['enable_attachments']));
        $this->assign('displayCaptcha', $displayCaptcha);
        
        $imagesUrl = OW::getPluginManager()->getPlugin('base')->getStaticCssUrl();

        $css = array(
            '.mc-attachments .ow_mailbox_attachment { background-image: url(' . $imagesUrl . 'images/tag_bg.png); }'
        );

        OW::getDocument()->addStyleDeclaration(implode("\n", $css));
        
        $jsParams = array(
            "senderId" => $userId,
            "recipients" => $recipients,
            "context" => $context,
            "formId" => $form->getId(),
            "formName" => $form->getName()
        );
        
        $js = UTIL_JsGenerator::composeJsString('var sendMessage = new MCOMPOSE.sendMessage({$params}, _scope);', array(
            "params" => $jsParams
        ));
        OW::getDocument()->addOnloadScript($js);
        
        OW::getLanguage()->addKeyForJs("mcompose", "close_fb_confirmation");
    }
}
