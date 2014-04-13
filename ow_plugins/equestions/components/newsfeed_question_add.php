<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

require_once OW_DIR_CORE . 'form_element.php';

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package equestions.components
 */
class EQUESTIONS_CMP_NewsfeedQuestionAdd extends EQUESTIONS_CMP_QuestionAdd
{
    private $feedAutoId, $feedType, $feedId, $actionVisibility;

    public function __construct($feedAutoId, $feedType, $feedId, $actionVisibility = null)
    {
        parent::__construct();

        $this->feedAutoId = $feedAutoId;
        $this->feedType = $feedType;
        $this->feedId = $feedId;
        $this->actionVisibility = $actionVisibility;
    }

    public function initForm()
    {
       return new EQUESTIONS_NewsfeedAddForm($this->feedAutoId, $this->feedType, $this->feedId, $this->actionVisibility);
    }
}

class EQUESTIONS_NewsfeedAddForm extends EQUESTIONS_AddForm
{
    private $feedAutoId;

    public function __construct($feedAutoId, $feedType, $feedId, $actionVisibility)
    {
        $this->feedAutoId = $feedAutoId;

        parent::__construct();

        $field = new HiddenField('feedType');
        $field->setValue($feedType);
        $this->addElement($field);

        $field = new HiddenField('feedId');
        $field->setValue($feedId);
        $this->addElement($field);

        $field = new HiddenField('visibility');
        $field->setValue($actionVisibility);
        $this->addElement($field);

        $this->setAction( OW::getRequest()->buildUrlQueryString(OW::getRouter()->urlFor('EQUESTIONS_CTRL_Questions', 'newsfeedAdd')) );
    }

    public function initJsResponder()
    {

        //ow_newsfeed_status_input
        $js = UTIL_JsGenerator::composeJsString('
        OW.bind("questions.tabs_changed", function(tab){
            var status = this.$(".ow_newsfeed_status_input");
            var sVal = status.hasClass("invitation") ? "" : status.val();
            var qVal = owForms["questions_add"].getElement("question").getValue();

            tab.newTab.find("textarea").focus();
            if ( !status.hasClass("invitation") && sVal && !qVal )
            {
                owForms["questions_add"].getElement("question").setValue(sVal);
                $(owForms["questions_add"].getElement("question").input).triggerHandler("keyup");
            }
        });
        owForms["questions_add"].bind( "success", function( r )
        {
            var form = owForms["questions_add"];

            if ( r )
            {
                if ( r.questionId )
                {
                    form.getElement("answers").resetValue();
                    form.getElement("question").resetValue();
                    OW.trigger("questions.after_question_add", [r]);

                    window.ow_newsfeed_feed_list[{$autoId}].loadNewItem({"entityType": {$entityType}, "entityId": r.questionId}, false);
                }

                if ( r.warning )
                {
                    OW.warning(r.warning);
                }
            }
            else
            {
                OW.error({$errorMessage});
            }
        });', array(
            'autoId' => $this->feedAutoId,
            'errorMessage' => OW::getLanguage()->text('base', 'form_validate_common_error_message'),
            'entityType' => EQUESTIONS_BOL_Service::ENTITY_TYPE
        ));

        OW::getDocument()->addOnloadScript( $js );
    }
}
