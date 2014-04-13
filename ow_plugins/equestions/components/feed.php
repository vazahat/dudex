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
 * @package equestions.components
 */
abstract class EQUESTIONS_CMP_Feed extends OW_Component
{
    const ORDER_LATEST = 'latest';
    const ORDER_POPULAR = 'popular';

    const FEED_ALL = 'all';
    const FEED_MY = 'my';
    const FEED_FRIENDS = 'friends';

    protected $uniqId, $feedType, $userId, $startStamp, $count,
            $renderedQuestionIds = array(), $order;

    /**
     *
     * @var EQUESTIONS_BOL_FeedService
     */
    protected $service;

    public function __construct( $startStamp, $userId, $count )
    {
        parent::__construct();

        $this->userId = $userId;
        $this->uniqId = uniqid('questionList');
        $this->startStamp = (int) $startStamp;
        $this->count = $count;

        $this->service = EQUESTIONS_BOL_FeedService::getInstance();

        $template = OW::getPluginManager()->getPlugin('equestions')->getCmpViewDir() . 'feed.html';
        $this->setTemplate($template);

        $this->order = $this->service->getDefaultOrder();
        $this->feedType = self::FEED_ALL;

        EQUESTIONS_Plugin::getInstance()->addStatic();
    }

    public function setOrder( $order )
    {
        $this->order = $order;
    }

    public function setFeedType( $type )
    {
        $this->feedType = $type;
    }

    public function setRenderedQuestionIds( $questionIds )
    {
        $this->renderedQuestionIds = $questionIds;
    }

    abstract public function findFeed( $startStamp, $count, $questionIds, $order );
    abstract public function findActivity( $startStamp, $questionIds );
    abstract public function findFeedCount( $startStamp );

    public function getBubbleActivityList( $activityList )
    {
        $out = array();
        foreach ( $activityList as $questionId => $activity )
        {
            $out[$questionId] = reset($activity);
        }

        return $out;
    }

    public function getRenderedCount()
    {
        return count($this->renderedQuestionIds);
    }

    public function getRenderedQuestionIds()
    {
        return $this->renderedQuestionIds;
    }

    public function renderList()
    {
        $questions = $this->findFeed($this->startStamp, $this->count, $this->renderedQuestionIds, $this->order);

        $questionIds = array();
        foreach ( $questions as $item )
        {
            $questionIds[] = $item->id;
        }

        $activityList = $this->findActivity($this->startStamp, $questionIds);
        $buubleActivityList = $this->getBubbleActivityList($activityList);

        $this->renderedQuestionIds = array_merge($this->renderedQuestionIds, $questionIds);

        $cmp = new EQUESTIONS_CMP_FeedList($questions, $buubleActivityList, $activityList);

        return $cmp->render();
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $this->assign('list', $this->renderList());

        $feedCount = $this->findFeedCount($this->startStamp);
        $renderedCount = $this->getRenderedCount();

        $viewMore = $renderedCount < $feedCount;
        $this->assign('viewMore', $viewMore);

        $this->assign('uniqId', $this->uniqId);

        $js = UTIL_JsGenerator::newInstance();

        $data = array(
            'viewMore' => $viewMore,
            'startStamp' => $this->startStamp,
            'userId' => $this->userId,
            'className' => get_class($this),
            'questionIds' => $this->renderedQuestionIds,
            'order' => $this->order,
            'feedType' => $this->feedType
        );

        $js->newObject('questionList', 'QUESTIONS_QuestionList', array($this->uniqId, $data));
        $js->callFunction(array('questionList', 'setResponder'), array(
            OW::getRouter()->urlFor('EQUESTIONS_CTRL_List', 'rsp')
        ));

        OW::getDocument()->addOnloadScript($js);
    }
}