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
 * @package equestions.controllers
 */
class EQUESTIONS_CTRL_Questions extends OW_ActionController
{
    /**
     *
     * @var EQUESTIONS_BOL_Service
     */
    private $service;

    public function __construct()
    {
        parent::__construct();

        $this->service = EQUESTIONS_BOL_Service::getInstance();
    }

    public function question($params)
    {
        $questionId = (int) $params['qid'];
        $question = $this->service->findQuestion($questionId);

        if ( empty($question) )
        {
            throw new Redirect404Exception;
        }

        $language = OW::getLanguage();

        OW::getDocument()->setTitle($language->text('equestions', 'question_page_title'));
        OW::getDocument()->setDescription($language->text('equestions', 'question_page_description', array(
            'question' => UTIL_String::truncate(strip_tags($question->text), 200)
        )));

        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'equestions', 'main_menu_list');

        $cmp = new EQUESTIONS_CMP_Question($questionId, null, null, array(
            'focusToPost' => isset($_GET['f'])
        ));

        $this->addComponent('question', $cmp);
    }

    public function newsfeedAdd()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        if ( empty($_POST['question']) )
        {
            echo json_encode(false);
            exit;
        }

        $permissions = EQUESTIONS_CLASS_CreditsBridge::getInstance()->getAllPermissions(EQUESTIONS_CLASS_Credits::ACTION_ASK);

        if ( !$permissions[EQUESTIONS_CLASS_Credits::ACTION_ASK] )
        {
            echo json_encode(array(
                'warning' => EQUESTIONS_CLASS_CreditsBridge::getInstance()->credits->getErrorMessage(EQUESTIONS_CLASS_Credits::ACTION_ASK)
            ));
            exit;
        }

        $question = empty($_POST['question']) ? '' : htmlspecialchars($_POST['question']);
        //$question = UTIL_HtmlTag::autoLink($question);
        $answers = empty($_POST['answers']) ? array() : array_filter($_POST['answers'], 'trim');
        $allowAddOprions = !empty($_POST['allowAddOprions']);

        $attachment = empty($_POST['attachment']) ? array() : json_decode($_POST['attachment'], true);

        if ( !empty($attachment) )
        {
            if ( $attachment['type'] == 'file' )
            {
                $attachment['url'] = OW::getEventManager()->call('base.attachment_save_image', array(
                    'genId' => $attachment['fileId']
                ));

                $attachment['type'] = 'photo';
                $attachment['href'] = $attachment['url'];
            }
        }

        $userId = OW::getUser()->getId();
        $questionDto = $this->service->addQuestion($userId, $question, $attachment, array(
            'allowAddOprions' => $allowAddOprions,
            'context' => array(
                'type' => $_POST['feedType'],
                'id' => $_POST['feedId']
            )
        ));

        foreach ($answers as $ans)
        {
            $this->service->addOption($questionDto->id, $userId, $ans);
        }

        $event = new OW_Event('feed.action', array(
            'entityType' => EQUESTIONS_BOL_Service::ENTITY_TYPE,
            'entityId' => $questionDto->id,
            'pluginKey' => 'equestions',
            'userId' => $userId,
            'feedType' => $_POST['feedType'],
            'feedId' => $_POST['feedId'],
            'visibility' => (int) $_POST['visibility']
        ), array(
            'contextFeedType' => $_POST['feedType'],
            'contextFeedId' => $_POST['feedId']
        ));

        OW::getEventManager()->trigger($event);

        echo json_encode(array(
            'questionId' => $questionDto->id,
            'permissions' => $permissions
        ));

        exit;
    }

    public function rsp()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        $query = json_decode($_POST['query'], true);
        $data = json_decode($_POST['data'], true);

        $relation = null;
        if ( !empty($_POST['relation']) )
        {
            $relation = json_decode($_POST['relation'], true);
        }

        $responce = array();
        $method = trim($query['command']);

        $responce = call_user_func(array($this, $method), $query, $data, $relation);

        echo json_encode($responce);
        exit;
    }

    private function answer($query, $data, $relation)
    {
        $userId = OW::getUser()->getId();

        $unvote = false;

        foreach ( $query['answers']['yes'] as $optionId )
        {
            $this->service->addAnswer($userId, $optionId);

            $unvote = $data['poll'];
        }

        if ( !empty($query['answers']['no']) )
        {
            $this->service->removeAnswerList($userId, $query['answers']['no']);
        }

        $answerCount = EQUESTIONS_BOL_Service::getInstance()->findTotalAnswersCount($data['questionId']);

        if ( !empty($relation) )
        {
            $relation = $this->reload(array(
                'answerCount' => $answerCount
            ), $relation['data']);
        }

        $permissions = EQUESTIONS_CLASS_CreditsBridge::getInstance()->getAllPermissions(EQUESTIONS_CLASS_Credits::ACTION_ANSWER);

        return array(
            'forceNotifications' => true,
            'relation' => $relation,
            'status' => array(
                'posts' => false,
                'votes' => $answerCount,
                'follows' => false
            ),
            'unvote' => $unvote,
            'permissions' => $permissions
        );
    }

    private function addAnswer($query, $data, $relation)
    {
        $userId = OW::getUser()->getId();
        $questionId = $data['questionId'];
        $uniqId = $data['uniqId'];

        $text = strip_tags(trim($query['text']));
        $option = $this->service->findOptionByText($questionId, $text);
        $new = false;

        if ( $option === null )
        {
            $new = true;
            $option = $this->service->addOption($questionId, $userId, $text);
        }

        $cmp = new EQUESTIONS_CMP_Answer($option, $uniqId);
        $cmp->setIsMultiple(!$data['poll']);

        $voteCount = 0;
        $checked = false;
        $users = array();

        if ( $new )
        {
            $cmp->setEditMode();
            $data['optionTotal']++;
        }
        else
        {
            $canEdit = $option->userId == $userId;

            $answerCount = $data['poll']
                ? $this->service->findTotalAnswersCount($questionId)
                : $this->service->findMaxAnswersCount($questionId);

            if ( $answerCount )
            {
                $voteCount = $this->service->findAnswerCountByOptionId($option->id);

                if ( $voteCount )
                {
                    $checked = $this->service->findAnswer($userId, $option->id) !== null;
                    $users = $this->service->findAnsweredUserIdList($option->id, $data['userContext'], $checked ? 4 : 3);

                    $cmp->setVoteCount($voteCount);
                    $cmp->setVoted($checked);
                    $cmp->setUsers($users);
                    $cmp->setPercents($voteCount * 100 / $answerCount);

                    $canEdit = $canEdit && ( $voteCount == 0 || $voteCount == 1 && $checked );
                }
            }

            $cmp->setEditMode($data['editMode'] || $canEdit);
        }

	$data['displayedCount']++;

        $options = array();
        $options[] = array(
            'markup' => $cmp->render(),
            'data' => array(
                'newOption' => $new,
                'checked' => $checked,
                'users' => $users,
                'voteCount' => $voteCount,
                'id' => $option->id
            )
        );

        if ( !empty($relation) )
        {
            $relation = $this->reload(array(), $relation['data']);
        }

        $permissions = EQUESTIONS_CLASS_CreditsBridge::getInstance()->getAllPermissions(EQUESTIONS_CLASS_Credits::ACTION_ADD_ANSWER);

        return array(
            'options' => $options,
            'data' => $data,
            'relation' => $relation,
            'permissions' => $permissions
        );
    }

    private function more($query, $data)
    {
        $userId = OW::getUser()->getId();
        $questionId = $data['questionId'];
        $uniqId = $data['uniqId'];
        $count = empty($query['inc']) ? EQUESTIONS_BOL_Service::INC_DISPLAY_COUNT : round($query['inc']);

        $tmp = $data['displayedCount'] > 10 ? 10 : $data['displayedCount'];

        $ost = $data['optionTotal'] - ( $data['offset'] + $count );
        $inc = $ost <= $tmp ? $count + $ost : $count;

        $tmp = $this->service->findOptionListAndAnswerCountList($questionId, $data['startStamp'], $data['userContext'], array($query['offset'], $inc));
        $optionList = $tmp['optionList'];
        $countList = $tmp['countList'];

        $data['displayedCount'] += $inc;
        $data['offset'] += $inc;

        $answerCount = $data['poll']
                ? $this->service->findTotalAnswersCount($questionId)
                : $this->service->findMaxAnswersCount($questionId);

        $list = new EQUESTIONS_CMP_OptionList($optionList, $uniqId, $userId);
        $list->setIsPoll($data['poll']);
        $list->setEditable($data['editable']);
        $list->setAnswerCount($answerCount);
        $list->setEditMode($data['editMode']);
        $list->setUsersContext($data['userContext']);
        $opts = $list->initOption($countList, $data['displayedCount']);

        $options = array();
        foreach ($opts as $option)
        {
            $options[] = array(
                'markup' => $list->getOption($option['id'])->render(),
                'data' => $option
            );
        }

        return array(
            'options' => $options,
            'data' => $data
        );
    }

    private function removeOption( $query, $data, $relation )
    {
        $userId = OW::getUser()->getId();
        $questionId = $data['questionId'];
        $optionId = $query['opt'];

        $this->service->removeOptionById($optionId);

        $answerCount = EQUESTIONS_BOL_Service::getInstance()->findTotalAnswersCount($questionId);

        $out = array(
            'status' => array(
                'posts' => false,
                'votes' => $answerCount,
                'follows' => false
            )
        );

        if ( !empty($relation) )
        {
            $out['relation'] = $this->reload(array(), $relation['data']);
        }

        return $out;
    }

    private function reload( $query, $data )
    {
        $question = EQUESTIONS_BOL_Service::getInstance()->findQuestion($data['questionId']);
        $optionTotal = EQUESTIONS_BOL_Service::getInstance()->findOptionCount($data['questionId']);
        $answerCount = !empty($query['answerCount']) ? $query['answerCount'] : EQUESTIONS_BOL_Service::getInstance()->findTotalAnswersCount($data['questionId']);

        $count = EQUESTIONS_BOL_Service::DISPLAY_COUNT;
        if ( $optionTotal - $count < 2 )
        {
            $count = $optionTotal;
        }

        $cmp = new EQUESTIONS_CMP_Answers($question, $optionTotal, array(0, $count), $data['uniqId']);
        $cmp->setUsersContext($data['userContext']);
        $cmp->setTotalAnswerCount($answerCount);
        $markup = $cmp->render();
        $js = OW::getDocument()->getOnloadScript();

        return array(
            'reload' => array(
                'markup' => $markup,
                'script' => $js
            ),
            'status' => array(
                'posts' => false,
                'votes' => $answerCount,
                'follows' => false
            )
        );
    }

    private function follow( $query, $data )
    {
        if ( !OW::getUser()->isAuthenticated() )
        {
            return array(
                'warning' => OW::getLanguage()->text('equestions', 'not_authed_follow_warning')
            );
        }

        $this->service->addFollow(OW::getUser()->getId(), $data['questionId']);

       $status = array(
            'posts' => false,
            'votes' => false,
            'follows' => $this->service->findFollowsCount($data['questionId'], $data['userContext'])
        );

        return array(
            'relation' => array(
                'call' => 'showUnfollow',
                'status' => $status
            ),
            'status' => $status
        );
    }

    private function unfollow( $query, $data )
    {
        if ( !OW::getUser()->isAuthenticated() )
        {
            return array(
                'warning' => OW::getLanguage()->text('equestions', 'not_authed_unfollow_warning')
            );
        }

        $this->service->removeFollow(OW::getUser()->getId(), $data['questionId']);

        $status = array(
            'posts' => false,
            'votes' => false,
            'follows' => $this->service->findFollowsCount($data['questionId'], $data['userContext'])
        );

        return array(
            'relation' => array(
                'call' => 'showFollow',
                'status' => $status
            ),
            'status' => $status
        );
    }

    private function deleteQuestion( $query, $data )
    {
        if ( !OW::getUser()->isAuthenticated() )
        {
            return array(
                'warning' => OW::getLanguage()->text('equestions', 'not_authed_delete_warning')
            );
        }

        $questionId = $data['questionId'];
        $question = $this->service->findQuestion($questionId);

        if ( !$this->service->isCurrentUserCanEdit($question) )
        {
            return array();
        }

        $this->service->deleteQuestion($questionId);

        return array(
            'message' => OW::getLanguage()->text('equestions', 'question_delete_complete_msg'),
            'listing' => array(
                'loadMore' => 1
            )
        );
    }

    private function askUsers( $query, $data )
    {
        if ( !OW::getUser()->isAuthenticated() )
        {
            return array(
                'warning' => OW::getLanguage()->text('equestions', 'not_authed_ask_warning')
            );
        }

        $userIds = $query['ids'];
        $questionId = $data['questionId'];
        $question = $this->service->findQuestion($questionId);

        $params = array(
            'userId' => OW::getUser()->getId(),
            'questionId' => $questionId,
        );

        if ( $query['all'] )
        {
            $params['special'] = true;
            $users = BOL_UserDao::getInstance()->findAll();
            foreach ( $users as $user )
            {
                if ( $user->id != $params['userId'] )
                {
                    $userIds[] = $user->id;
                }
            }
        }

        foreach ( $userIds as $uid )
        {
            $params['recipientId'] = $uid;
            $event = new OW_Event( EQUESTIONS_BOL_Service::EVENT_QUESTION_BEFORE_ASK, $params, $data);
            OW::getEventManager()->trigger($event);
        }

        $permissions = EQUESTIONS_CLASS_CreditsBridge::getInstance()->getAllPermissions(EQUESTIONS_CLASS_Credits::ACTION_ASK);

        return array(
            'message' => OW::getLanguage()->text('equestions', 'question_ask_complete_msg', array(
                'count' => count($userIds)
            )),
            'permissions' => $permissions
        );
    }
}