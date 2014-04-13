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
 * @package equestions.bol
 */
class EQUESTIONS_BOL_Service
{

    const EVENT_ON_INTERACT_PERMISSION_CHECK = 'questions.interact_permission_check';

    const EVENT_BEFORE_QUESTION_ADDED = 'questions.before_question_added';

    const EVENT_QUESTION_ADDED = 'questions.question_added';
    const EVENT_QUESTION_REMOVED = 'questions.question_removed';

    const EVENT_OPTION_ADDED = 'questions.option_added';
    const EVENT_OPTION_REMOVE = 'questions.option_remove';

    const EVENT_ANSWER_ADDED = 'questions.answer_added';
    const EVENT_ANSWER_REMOVED = 'questions.answer_removed';

    const EVENT_POST_ADDED = 'questions.post_added';
    const EVENT_POST_REMOVED = 'questions.post_removed';

    const EVENT_FOLLOW_ADDED = 'questions.follow_added';
    const EVENT_FOLLOW_REMOVED = 'questions.follow_removed';

    const EVENT_QUESTION_ASKED = 'questions.question_asked';
    const EVENT_QUESTION_BEFORE_ASK = 'questions.question_before_ask';

    const ENTITY_TYPE = 'question';

    const DISPLAY_COUNT = 3;
    const MORE_DISPLAY_COUNT = 10;
    const INC_DISPLAY_COUNT = 15;

    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return EQUESTIONS_BOL_Service
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
     *
     * @var EQUESTIONS_BOL_QuestionDao
     */
    private $questionDao;

    /**
     *
     * @var EQUESTIONS_BOL_AnswerDao
     */
    private $answerDao;

    /**
     *
     * @var EQUESTIONS_BOL_OptionDao
     */
    private $optionDao;

    /**
     *
     * @var EQUESTIONS_BOL_FollowDao
     */
    private $followDao;

    public function __construct()
    {
        $this->questionDao = EQUESTIONS_BOL_QuestionDao::getInstance();
        $this->answerDao = EQUESTIONS_BOL_AnswerDao::getInstance();
        $this->optionDao = EQUESTIONS_BOL_OptionDao::getInstance();
        $this->followDao = EQUESTIONS_BOL_FollowDao::getInstance();
    }

    /**
     *
     * @param $userId
     * @param $question
     * @param $settings
     * @return EQUESTIONS_BOL_Question
     */
    public function addQuestion( $userId, $text, $attachment = array(), $settings = array(), $privacy = EQUESTIONS_BOL_FeedService::PRIVACY_EVERYBODY )
    {
        $event = new OW_Event(self::EVENT_BEFORE_QUESTION_ADDED, array(
            'text' => $text,
            'userId' => $userId,
            'settings' => $settings,
            'attachment' => $attachment
        ),array(
            'text' => $text,
            'settings' => $settings,
            'privacy' => $privacy,
            'attachment' => $attachment
        ));

        OW::getEventManager()->trigger($event);

        $data = $event->getData();
        $text = $data['text'];
        $settings = $data['settings'];
        $privacy = $data['privacy'];
        $attachment = $data['attachment'];

        $question = new EQUESTIONS_BOL_Question();
        $question->setSettings($settings);

        if ( !empty($attachment) )
        {
            $question->setAttachment($attachment);
        }

        $question->text = $text;
        $question->userId = (int) $userId;
        $question->timeStamp = time();

        $this->questionDao->save($question);

        $this->followDao->addFollow($userId, $question->id);

        OW::getEventManager()->trigger( new OW_Event(self::EVENT_QUESTION_ADDED, array(
            'text' => $text,
            'userId' => $userId,
            'settings' => $settings,
            'id' => $question->id,
            'privacy' => $privacy
        )));

        return $question;
    }

    /**
     *
     * @param int $id
     * @return EQUESTIONS_BOL_Question
     */
    public function deleteQuestion( $id )
    {

        $options = $this->optionDao->findByQuestionId($id);

        if ( !empty($options) )
        {
            $optionIds = array();
            foreach ( $options as $opt )
            {
                $this->removeOptionById($opt->id);
            }
        }

        $follows = $this->followDao->findByQuestionId($id);

        foreach ( $follows as $follow )
        {
            $this->removeFollow($follow->userId, $follow->questionId);
        }

        $this->questionDao->deleteById($id);

        OW::getEventManager()->trigger( new OW_Event(self::EVENT_QUESTION_REMOVED, array(
            'id' => $id
        )));
    }


    /**
     *
     * @param $questionId
     * @param $userId
     * @param $text
     * @return EQUESTIONS_BOL_Option
     */
    public function addOption( $questionId, $userId, $text, $time = null )
    {
        $option = new EQUESTIONS_BOL_Option();
        $option->questionId = (int) $questionId;
        $option->text = $text;
        $option->userId = (int) $userId;
        $option->timeStamp = empty($time) ? time() : $time;

        $this->optionDao->save($option);

        OW::getEventManager()->trigger( new OW_Event(self::EVENT_OPTION_ADDED, array(
            'questionId' => $questionId,
            'text' => $text,
            'userId' => $userId,
            'id' => $option->id
        )));

        return $option;
    }

    public function removeOptionById( $id )
    {
        $answers = $this->answerDao->findByOptionId($id);

        foreach ( $answers as $answer )
        {
            $this->removeAnswerByDto($answer);
        }

        $this->optionDao->deleteById($id);

        OW::getEventManager()->trigger( new OW_Event(self::EVENT_OPTION_REMOVE, array(
            'id' => $id
        )));
    }

    /**
     *
     * @param $questionId
     * @param $userId
     * @param $text
     * @return EQUESTIONS_BOL_Answer
     */
    public function addAnswer( $userId, $optionId )
    {
        $answer = $this->answerDao->findAnswer($userId, $optionId);

        if ($answer !== null)
        {
            return $answer;
        }

        $answer = new EQUESTIONS_BOL_Answer();
        $answer->optionId = (int) $optionId;
        $answer->userId = (int) $userId;
        $answer->timeStamp = time();

        $this->answerDao->save($answer);

        OW::getEventManager()->trigger( new OW_Event(self::EVENT_ANSWER_ADDED, array(
            'optionId' => $optionId,
            'userId' => $userId,
            'id' => $answer->id
        )));

        return $answer;
    }

    private function removeAnswerByDto( EQUESTIONS_BOL_Answer $answer )
    {
        $this->answerDao->delete($answer);

        OW::getEventManager()->trigger( new OW_Event(self::EVENT_ANSWER_REMOVED, array(
            'optionId' => $answer->optionId,
            'userId' => $answer->userId,
            'id' => $answer->id
        )));
    }

    public function addFollow( $userId, $questionId )
    {
        $follow = $this->followDao->addFollow($userId, $questionId);

        $event = new OW_Event(self::EVENT_FOLLOW_ADDED, array(
            'userId' => $userId,
            'questionId' => $questionId,
            'id' => $follow->id
        ));

        OW::getEventManager()->trigger($event);

        return $follow;
    }

    public function removeFollow( $userId, $questionId )
    {
        $follow = $this->followDao->findFollow($userId, $questionId);
        $this->followDao->delete($follow);

        $event = new OW_Event(self::EVENT_FOLLOW_REMOVED, array(
            'userId' => $userId,
            'questionId' => $questionId,
            'id' => $follow->id
        ));

        OW::getEventManager()->trigger($event);
    }



    public function removeAnswer( $userId, $optionId )
    {
        $answer = $this->answerDao->findAnswer($userId, $optionId);

        if ( $answer === null )
        {
            return;
        }

        $this->removeAnswerByDto($answer);
    }

    public function removeAnswerById( $answerId )
    {
        $answer = $this->answerDao->findById($answerId);

        if ( $answer === null )
        {
            return;
        }

        $this->removeAnswerByDto($answer);
    }

    public function removeAnswerList($userId, $optionIds)
    {
        foreach ( $optionIds as $optionId )
        {
            $this->removeAnswer($userId, $optionId);
        }
    }

    public function removeAnswerListByIdList( $answerIds )
    {
        foreach ( $answerIds as $answerId )
        {
            $this->removeAnswerById($answerId);
        }
    }



    /**
     *
     * @param int $id
     * @return EQUESTIONS_BOL_Question
     */
    public function findQuestion( $id )
    {
        return $this->questionDao->findById($id);
    }

    /**
     *
     * @param int $id
     * @return EQUESTIONS_BOL_Option
     */
    public function findOption( $id )
    {
        return $this->optionDao->findById($id);
    }

    /**
     *
     * @param int $questionId
     * @param string $text
     * @return EQUESTIONS_BOL_Question
     */
    public function findOptionByText( $questionId, $text )
    {
        return $this->optionDao->findByText($questionId, $text);
    }

    public function findOptionList( $questionId, $priorUsers = array(), $limit = null )
    {
        return $this->optionDao->findByQuestionId($questionId, $priorUsers, $limit);
    }

    public function findOptionListAndAnswerCountList($questionId, $startStamp, $priorUsers = array(), $limit = null)
    {
        return $this->optionDao->findListWithAnswerCountList($questionId, $startStamp, $priorUsers, $limit);
    }

    public function findOptionCount( $questionId )
    {
        return $this->optionDao->findCountByQuestionId($questionId);
    }

    public function findAnswersCount( $optionIds )
    {
        return $this->answerDao->findCountList($optionIds);
    }

    public function findAnswerCountByOptionId( $optionId )
    {
        return $this->answerDao->findCount($optionId);
    }

    public function findUserAnswerList( $userId, $optionIds )
    {
        return $this->answerDao->findUserAnswerList($userId, $optionIds);
    }

    public function findAnswer( $userId, $optionId )
    {
        return $this->answerDao->findAnswer($userId, $optionId);
    }

    public function findAnsweredUserIdList( $optionId, $usersContext = null, $count = null )
    {
        if ($usersContext === null)
        {
            $answers = $this->answerDao->findList($optionId, $count);
        }
        else
        {
            $answers = $this->answerDao->findListWithUserIdList($optionId, $usersContext, $count);
        }

        $out = array();
        foreach ( $answers as $item )
        {
            $out[] = $item->userId;
        }

        return $out;
    }


    public function findTotalAnswersCount( $questionId )
    {
        return (int) $this->answerDao->findTotalCountByQuestionId($questionId);
    }

    public function findMaxAnswersCount( $questionId )
    {
        return (int) $this->answerDao->findMaxCountByQuestionId($questionId);
    }

    public function findUserAnswerListByQuestionId( $userId, $questionId )
    {
        return $this->answerDao->findByQuestionIdAndUserId($questionId, $userId);
    }

    public function isCurrentUserCanEdit( EQUESTIONS_BOL_Question $question )
    {
        return OW::getUser()->getId() == $question->userId || OW::getUser()->isAuthorized('equestions');
    }

    public function isCurrentUserCanAsk()
    {
        return OW::getUser()->isAuthorized('equestions', 'ask');
    }

    public function isCurrentUserCanAnswer( EQUESTIONS_BOL_Question $question )
    {
        return OW::getUser()->isAuthorized('equestions', 'answer');
    }

    public function isCurrentUserCanPost( EQUESTIONS_BOL_Question $question )
    {
        return OW::getUser()->isAuthorized('equestions', 'add_comment');
    }

    public function isCurrentUserCanAddOptions( EQUESTIONS_BOL_Question $question )
    {
        if ( $question->userId == OW::getUser()->getId() )
        {
            return true;
        }

        return OW::getUser()->isAuthorized('equestions', 'add_answer');
    }

    public function isCurrentUserCanInteract( EQUESTIONS_BOL_Question $question )
    {
        $canInteract = OW::getUser()->isAuthenticated();

        $event = new OW_Event(self::EVENT_ON_INTERACT_PERMISSION_CHECK, array(
            'questionId' => $question->id,
            'settings' => $question->getSettings()
        ), $canInteract);

        OW::getEventManager()->trigger($event);

        return $event->getData();
    }

    public function isFollow( $userId, $questionId )
    {
        return $this->followDao->findFollow($userId, $questionId) !== null;
    }

    public function findAllQuestionList()
    {
        return $this->questionDao->findAll();
    }

    public function findAllQuestionsCount()
    {
        return $this->questionDao->countAll();
    }

    public function findFollowsCount( $questionId, $userContext = array(), $ignoreUsers = array() )
    {
        return $this->followDao->findFollowCount($questionId, $userContext, $ignoreUsers);
    }

    public function findFollows( $questionId, $userContext = array(), $ignoreUsers = array() )
    {
        return $this->followDao->findFollowList($questionId, $userContext, $ignoreUsers);
    }



    //TODO Methods that should be rewrited after Public interfaces will be avaliable
    public function findUsers( $kw, $limit = null )
    {
        $questionName = OW::getConfig()->getValue('base', 'display_name_question');
        $questionDataTable = BOL_QuestionDataDao::getInstance()->getTableName();

        $limitStr = $limit === null ? '' : 'LIMIT 0, ' . intval($limit);

        $query = 'SELECT DISTINCT qd.userId FROM ' . $questionDataTable . ' qd
            LEFT JOIN `' . BOL_UserSuspendDao::getInstance()->getTableName() . '` AS `us` ON ( `qd`.`userId` = `us`.`userId` )
            WHERE `us`.`userId` IS NULL AND questionName=:name AND textValue LIKE :kw ' . $limitStr;

        return OW::getDbo()->queryForColumnList($query, array(
            'kw' => $kw . '%',
            'name' => $questionName
        ));
    }

    public function findFriends( $kw, $userId, $limit = null )
    {
        $friendsTable = FRIENDS_BOL_FriendshipDao::getInstance()->getTableName();

        $questionName = OW::getConfig()->getValue('base', 'display_name_question');
        $questionDataTable = BOL_QuestionDataDao::getInstance()->getTableName();

        $limitStr = $limit === null ? '' : 'LIMIT 0, ' . intval($limit);

        $query = "SELECT `fr`.`userId` FROM `" . $friendsTable . "` AS `fr`
            INNER JOIN " . $questionDataTable . " qd ON fr.userId = qd.userId
            LEFT JOIN `" . BOL_UserSuspendDao::getInstance()->getTableName() . "` AS `us` ON ( `fr`.`friendId` = `us`.`userId` )
            WHERE `fr`.`status` = :status AND `us`.`userId` IS NULL AND `fr`.`friendId` = :userId
                AND qd.questionName=:name AND qd.textValue LIKE :kw
            UNION
            SELECT `fr`.`friendId` AS `userId` FROM `" . $friendsTable . "` AS `fr`
            INNER JOIN " . $questionDataTable . " qd ON fr.friendId = qd.userId
            LEFT JOIN `" . BOL_UserSuspendDao::getInstance()->getTableName() . "` AS `us` ON ( `fr`.`friendId` = `us`.`userId` )
            WHERE `fr`.`status` = :status AND `us`.`userId` IS NULL AND `fr`.`userId` = :userId
                AND qd.questionName=:name AND qd.textValue LIKE :kw
            $limitStr
            ";

        return OW::getDbo()->queryForColumnList($query,
            array(
                'userId' => $userId,
                'status' => FRIENDS_BOL_FriendshipDao::VAL_STATUS_ACTIVE,
                'kw' => $kw . '%',
                'name' => $questionName
            )
        );
    }
}