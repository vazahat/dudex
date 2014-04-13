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
 * @package mcompose.classes
 * @since 1.0
 */
require_once OW::getPluginManager()->getPlugin('mailbox')->getClassesDir() . 'create_conversation_form.php';

class MCOMPOSE_CLASS_Form extends Form
{
    protected $userId, $context;

    public function __construct( $name, $senderId, $context = MCOMPOSE_BOL_Service::CONTEXT_USER, $initJs = true, $inPopup = true )
    {
        parent::__construct($name);
        
        $language = OW::getLanguage();

        $this->userId = $senderId;
        $this->context = $context;

        $this->setAction(OW::getRouter()->urlFor('MCOMPOSE_CTRL_Compose', 'send', array('userId' => $senderId, 'formName' => $this->getName())));
        $this->setMethod(self::METHOD_POST);
        $this->setId($this->getName());
        $this->setEnctype('multipart/form-data');

        $this->setAjax();
        $this->setAjaxResetOnSuccess(false);

        $to = new MCOMPOSE_CLASS_UserSelectField('recipients', OW::getLanguage()->text('mcompose', 'selector_invitation_label'));
        $to->setRequired();
        $this->addElement($to);

        //thickbox
        $validatorSubject = new StringValidator(0, 2048);
        $validatorSubject->setErrorMessage($language->text('mailbox', 'message_too_long_error', array('maxLength' => 2048)));

        $subject = new TextField('subject');
        $subject->setInvitation('Subject');
        $subject->setHasInvitation(true);
        $subject->setLabel($language->text('mailbox', 'subject'))->addAttribute('class', 'ow_text');
        $subject->addValidator($validatorSubject);
        $subject->setRequired(true);
        $this->addElement($subject);

        $validatorTextarea = new StringValidator(0, 24000);
        $validatorTextarea->setErrorMessage($language->text('mailbox', 'message_too_long_error', array('maxLength' => 24000)));

        $message = new WysiwygTextarea('message', array( BOL_TextFormatService::WS_BTN_IMAGE, BOL_TextFormatService::WS_BTN_VIDEO ), true);

        $message->setLabel($language->text('mailbox', 'text'))->addAttribute('class', 'ow_text');
        $message->setSize(300);
        $message->addValidator($validatorTextarea);
        $message->setRequired(true);
        $this->addElement($message);


        if ( OW::getConfig()->getValue('mailbox', 'enable_attachments') )
        {
            $multiUpload = new MAILBOX_CLASS_AjaxFileUpload('attachments');
            //$multiUpload->setId('attachments');
            $this->addElement($multiUpload);
        }


        // Captcha
        $captcha = new MailboxCaptchaField('captcha');
        $captcha->addValidator(new MailboxCaptchaValidator($captcha->getId()));
        $captcha->addAttribute('disabled', 'disabled');

        $this->addElement($captcha);

        $submit = new Submit('send');
        $submit->setValue($language->text('mailbox', 'send_button'));
        $submit->addAttribute('class', 'ow_button ow_ic_mail');
        $this->addElement($submit);

        if ( $initJs )
        {
             $js = "owForms['" . $this->getName() . "'].bind( 'success',
            function( json )
            {
                var _complete = function(){ 
                    if ( _scope.floatBox ) _scope.floatBox.close();
                };

                var form = $('#" . $this->getName() . "');
                var captcha = form.find('input[name=captcha]');

                if ( json.result == 'permission_denied' )
                {
                    if ( json.message )
                    {
                        OW.error(json.message);
                    }
                    else
                    {
                        OW.error(". json_encode(OW::getLanguage()->text('mailbox', 'write_permission_denied')).");
                    }
                    
                    _complete();
                }
                else if ( json.result == 'display_captcha' )
            	{
                   window.". $captcha->jsObjectName .".refresh();

                   if ( captcha.attr('disabled') != 'disabled' )
                   {
                        owForms['" . $this->getName() . "'].getElement('captcha').showError(". json_encode(OW::getLanguage()->text('base', 'form_validator_captcha_error_message')) . ");
                   }
                   else
                   {
                        captcha.removeAttr('disabled');
                   }

                   form.find('tr.captcha').show();
                   form.find('tr.mailbox_conversation').hide();
                }
                else if ( json.result == true )
            	{
                    captcha.attr('disabled','disabled');
                    form.find('tr.captcha').hide();
                    window.". $captcha->jsObjectName .".refresh();

                    form.find('tr.captcha').hide();
                    form.find('tr.mailbox_conversation').show();

                    owForms['" . $this->getName() . "'].resetForm();
                    form.find('textarea[name=message]').get(0).htmlareaRefresh();

                    if ( json.error )
                        OW.error(json.error);

                    if ( json.warning )
                        OW.warning(json.warning);

                     if ( json.message )
                        OW.info(json.message);
                        
                    _complete();
                }
                else if ( json.error )
                {
                    OW.error(json.error);
                    
                    _complete();
                }

            }); ";

            OW::getDocument()->addOnloadScript( $js );
        }
    }

    protected function checkCaptcha()
    {
        $lastSendStamp = BOL_PreferenceService::getInstance()->getPreferenceValue('mailbox_create_conversation_stamp', $this->userId);
        $displayCaptcha = BOL_PreferenceService::getInstance()->getPreferenceValue('mailbox_create_conversation_display_capcha', $this->userId);

        if ( !$displayCaptcha && ($lastSendStamp + CreateConversationForm::DISPLAY_CAPTCHA_TIMEOUT) > time() )
        {
            BOL_PreferenceService::getInstance()->savePreferenceValue('mailbox_create_conversation_display_capcha', true, $this->userId);
            $displayCaptcha = true;
        }

        $captcha = $this->getElement('captcha');
        $captcha->setRequired();

        return !$displayCaptcha || ( $captcha->isValid() && UTIL_Validator::isCaptchaValid($captcha->getValue()) );

    }


    public function process()
    {
        $language = OW::getLanguage();
        $uploadFiles = MAILBOX_BOL_FileUploadService::getInstance();

        $isAuthorized = OW::getUser()->isAuthorized( 'mailbox', 'send_message' );
        if( !$isAuthorized )
        {
            return array('result'=> 'permission_denied' );
        }

        if ( !$this->checkCaptcha() )
        {
            return array('result'=> 'display_captcha' );
        }

        $values = $this->getValues();
        $recipients = $values['recipients'];

        $fileDtoList = array();
        if ( !empty($values['attachments']) )
        {
            $fileDtoList = $uploadFiles->findUploadFileList($values['attachments']);
        }

        $event = new BASE_CLASS_EventCollector(MCOMPOSE_BOL_Service::EVENT_ON_SEND, array(
            "recipients" => $recipients,
            "userId" => $this->userId,
            "context" => $this->context
        ));
        
        OW::getEventManager()->trigger($event);
        $userIds = array_unique($event->getData());
        
        $error = null;
        $sentCount = 0;
        foreach ( $userIds as $uid )
        {
            try
            {
                $this->sendMessage($uid, $values['subject'], $values['message'], $fileDtoList);
                $sentCount++;
            }
            catch ( LogicException $e )
            {
                $error = $e->getMessage();
                break;
            }
        }

        foreach ( $fileDtoList as $fileDto )
        {
            $uploadFiles->deleteUploadFile($fileDto->hash, $fileDto->userId);
        }

        BOL_PreferenceService::getInstance()->savePreferenceValue('mailbox_create_conversation_display_capcha', false, $this->userId);
        BOL_PreferenceService::getInstance()->savePreferenceValue('mailbox_create_conversation_stamp', time(), $this->userId);

        $result = array('result' => true);
        if ( $sentCount > 0 && $error !== null )
        {
            $result['warning'] = $language->text('mcompose', 'some_messages_not_sent', array(
                'sentCount' => $sentCount
            ));
        }
        else if ( $sentCount > 0 )
        {
            $result['message'] = $language->text('mailbox', 'create_conversation_message');
        }

        if ( $error !== null )
        {
            $result['error'] = $error;
        }

        return $result;
    }

    public function sendMessage( $userId, $subject, $message, $attachments = array() )
    {
        $conversationService = MAILBOX_BOL_ConversationService::getInstance();

        // credits check
        $creditsEventParams = array('pluginKey' => 'mailbox', 'action' => 'send_message');
        if ( OW::getEventManager()->call('usercredits.check_balance', $creditsEventParams) === false )
        {
            $error = OW::getEventManager()->call('usercredits.error_message', $creditsEventParams);
            throw new LogicException($error);
        }

        $conversation = $conversationService->createConversation($this->userId, $userId, htmlspecialchars($subject), $message);
        $message = $conversationService->getLastMessages($conversation->id);

        foreach( $attachments as $fileDto )
        {
            $attachmentDto = new MAILBOX_BOL_Attachment();
            $attachmentDto->messageId = $message->initiatorMessageId;
            $attachmentDto->fileName = htmlspecialchars($fileDto->fileName);
            $attachmentDto->fileSize = $fileDto->fileSize;
            $attachmentDto->hash = $fileDto->hash;

            $tmpFilePath = OW::getPluginManager()->getPlugin('mcompose')->getPluginFilesDir() . uniqid('mcomose_') . '.' . UTIL_File::getExtension($fileDto->fileName);

            if ( $conversationService->fileExtensionIsAllowed( UTIL_File::getExtension($fileDto->fileName) ) && copy($fileDto->filePath, $tmpFilePath) )
            {
                $conversationService->addAttachment($attachmentDto, $tmpFilePath);
            }
        }

        // credits track
        OW::getEventManager()->call('usercredits.track_action', $creditsEventParams);

        return true;
    }
}
