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
class EQUESTIONS_CTRL_List extends OW_ActionController
{
    const ITEMS_COUNT = 10;

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

    private function getMenu()
    {
        $menu = new EQUESTIONS_CMP_FeedMenu();

        return $menu;
    }

    public function all()
    {
        $language = OW::getLanguage();

        OW::getDocument()->setTitle($language->text('equestions', 'list_all_page_title'));
        OW::getDocument()->setDescription($language->text('equestions', 'list_all_page_description'));
        OW::getDocument()->setHeading($language->text('equestions', 'list_heading'));
        OW::getDocument()->setHeadingIconClass('ow_ic_lens');

        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'equestions', 'main_menu_list');

        $userId = OW::getUser()->getId();

        $cmp = new EQUESTIONS_CMP_MainFeed(time(), $userId, self::ITEMS_COUNT);
        $cmp->setFeedType(EQUESTIONS_CMP_Feed::FEED_ALL);
        $order = EQUESTIONS_BOL_FeedService::getInstance()->getOrder(EQUESTIONS_CMP_Feed::FEED_ALL, OW::getUser()->getId());
        $cmp->setOrder($order);

        $menu = $this->getMenu();
        $menu->setOrder($order);
        $this->addComponent('list', $cmp);
        $this->addComponent('menu', $menu);

        if ( EQUESTIONS_BOL_Service::getInstance()->isCurrentUserCanAsk() )
        {
            $add = new EQUESTIONS_CMP_QuestionAdd();
            $this->addComponent('add', $add);
        }
    }

    public function my()
    {
        $language = OW::getLanguage();

        OW::getDocument()->setTitle($language->text('equestions', 'list_my_page_title'));
        OW::getDocument()->setHeading($language->text('equestions', 'list_heading'));
        OW::getDocument()->setHeadingIconClass('ow_ic_lens');

        if ( !OW::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }

        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'equestions', 'main_menu_list');

        $userId = OW::getUser()->getId();

        $cmp = new EQUESTIONS_CMP_MyFeed(time(), $userId, self::ITEMS_COUNT);
        $cmp->setFeedType(EQUESTIONS_CMP_Feed::FEED_MY);
        $order = EQUESTIONS_BOL_FeedService::getInstance()->getOrder(EQUESTIONS_CMP_Feed::FEED_MY, OW::getUser()->getId());
        $cmp->setOrder($order);

        $menu = $this->getMenu();
        $menu->setOrder($order);

        $this->addComponent('list', $cmp);
        $this->addComponent('menu', $menu);

        $add = new EQUESTIONS_CMP_QuestionAdd();
        $this->addComponent('add', $add);
    }

    public function friends()
    {
        if ( !OW::getPluginManager()->isPluginActive('friends') )
        {
            throw new Redirect404Exception();
        }

        if ( !OW::getUser()->isAuthenticated() )
        {
            throw new AuthenticateException();
        }

        $language = OW::getLanguage();

        OW::getDocument()->setTitle($language->text('equestions', 'list_friends_page_title'));
        OW::getDocument()->setHeading($language->text('equestions', 'list_heading'));
        OW::getDocument()->setHeadingIconClass('ow_ic_lens');

        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'equestions', 'main_menu_list');

        $userId = OW::getUser()->getId();

        $cmp = new EQUESTIONS_CMP_FriendsFeed(time(), $userId, self::ITEMS_COUNT);
        $cmp->setFeedType(EQUESTIONS_CMP_Feed::FEED_FRIENDS);
        $order = EQUESTIONS_BOL_FeedService::getInstance()->getOrder(EQUESTIONS_CMP_Feed::FEED_FRIENDS, OW::getUser()->getId());
        $cmp->setOrder($order);

        $menu = $this->getMenu();
        $menu->setOrder($order);

        $this->addComponent('list', $cmp);
        $this->addComponent('menu', $menu);
    }

    public function rsp()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        $query = json_decode($_POST['query'], true);
        $data = json_decode($_POST['data'], true);

        $responce = array();
        $method = trim($query['command']);

        $responce = call_user_func(array($this, $method), $query, $data);

        echo json_encode($responce);
        exit;
    }

    private function more( $query, $data )
    {
        $count = empty($query['count']) ? self::ITEMS_COUNT : $query['count'];
        $className = $data['className'];

        $cmp = new $className($data['startStamp'], $data['userId'], $count);
        $cmp->setOrder($data['order']);
        $cmp->setRenderedQuestionIds($data['questionIds']);
        $questionsCount = $cmp->findFeedCount($data['startStamp']);

        $html = $cmp->renderList();
        $script = OW::getDocument()->getOnloadScript();

        $data['offset'] = $cmp->getRenderedCount();
        $data['questionIds'] = $cmp->getRenderedQuestionIds();
        $data['viewMore'] = $data['offset'] < $questionsCount;

        return array(
            'data' => $data,
            'markup' => array(
                'html' => $html,
                'script' => $script,
                'position' => 'append'
            )
        );
    }

    private function order( $query, $data )
    {
        if ( !empty($query['order']) )
        {
            $data['order'] = $query['order'];
        }

        EQUESTIONS_BOL_FeedService::getInstance()->setOrder($data['feedType'], $query['order'], OW::getUser()->getId());

        $count = empty($query['count']) ? self::ITEMS_COUNT : $query['count'];
        $className = $data['className'];

        $cmp = new $className($data['startStamp'], $data['userId'], $count);
        $cmp->setOrder($data['order']);
        $questionsCount = $cmp->findFeedCount($data['startStamp']);

        $html = $cmp->renderList();
        $script = OW::getDocument()->getOnloadScript();

        $data['offset'] = $cmp->getRenderedCount();
        $data['questionIds'] = $cmp->getRenderedQuestionIds();
        $data['viewMore'] = $data['offset'] < $questionsCount;

        return array(
            'data' => $data,
            'markup' => array(
                'html' => $html,
                'script' => $script,
                'position' => 'replace'
            )
        );
    }

    public function addQuestion()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        if ( !OW::getUser()->isAuthenticated() )
        {
            echo json_encode(false);
            exit;
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
                'reset' => false,
                'warning' => EQUESTIONS_CLASS_CreditsBridge::getInstance()->credits->getErrorMessage(EQUESTIONS_CLASS_Credits::ACTION_ASK)
            ));
            exit;
        }

        $question = empty($_POST['question']) ? '' : htmlspecialchars($_POST['question']);
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
            'allowAddOprions' => $allowAddOprions
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
            'visibility' => 15 // Visibility all ( 15 )
        ));

        OW::getEventManager()->trigger($event);

        $activityList = EQUESTIONS_BOL_FeedService::getInstance()->findMainActivity(time(), array($questionDto->id), array(0, 6));

        $cmp = new EQUESTIONS_CMP_FeedItem($questionDto, reset($activityList[$questionDto->id]), $activityList[$questionDto->id]);
        $html = $cmp->render();
        $script = OW::getDocument()->getOnloadScript();

        echo json_encode(array(
            'markup' => array(
                'html' => $html,
                'script' => $script,
                'position' => 'prepend'
            ),
            'permissions' => $permissions
        ));
        exit;
    }
}