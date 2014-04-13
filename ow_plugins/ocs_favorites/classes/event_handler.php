<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow_plugins.ocs_favorites.classes
 * @since 1.5.3
 */
class OCSFAVORITES_CLASS_EventHandler
{
    /**
     * Class instance
     *
     * @var OCSFAVORITES_CLASS_EventHandler
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return OCSFAVORITES_CLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * @param BASE_CLASS_EventCollector $event
     */
    public function addProfileToolbarAction( BASE_CLASS_EventCollector $event )
    {
        $params = $event->getParams();

        if ( empty($params['userId']) )
        {
            return;
        }

        $userId = (int) $params['userId'];

        if ( OW::getUser()->getId() == $userId )
        {
            return;
        }

        if ( !OW::getUser()->isAuthorized('ocsfavorites', 'add_to_favorites') )
        {
            return;
        }

        $eventParams = array('pluginKey' => 'ocsfavorites', 'action' => 'add_to_favorites');
        $credits = OW::getEventManager()->call('usercredits.check_balance', $eventParams);

        $service = OCSFAVORITES_BOL_Service::getInstance();
        $lang = OW::getLanguage();

        $isFavorite = $service->isFavorite(OW::getUser()->getId(), $userId);

        $btnAddId = 'ocsfavadd_' . rand(10, 10000);
        $btnRemoveId = 'ocsfavremove_' . rand(10, 10000);

        $actionData = array(
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LABEL => $lang->text('ocsfavorites', 'add_favorite_button'),
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_HREF => 'javascript://',
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_ID => $btnAddId,
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_CLASS => 'ow_mild_green',
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_ATTRIBUTES => $isFavorite ? array('style' => 'display: none') : array()
        );

        $event->add($actionData);

        if ( !$isFavorite && $credits === false )
        {
            $error = OW::getEventManager()->call('usercredits.error_message', $eventParams);
            $script =
            '$("#' . $btnAddId . '").click(function(){
                OW.error(' . json_encode($error) . ');
            });
            ';
        }
        else
        {
            $script =
            '$("#' . $btnAddId . '").click(function(){
                var $btn = $(this);
                $.ajax({
                    url: ' . json_encode(OW::getRouter()->urlForRoute('ocsfavorites.add')) . ',
                    type: "POST",
                    data: { favoriteId: ' . json_encode($userId) . ' },
                    dataType: "json",
                    success: function(data) {
                        if ( data.result == true ) {
                            OW.info(data.msg);
                            $btn.hide();
                            $("#' . $btnRemoveId. '").show();
                        }
                        else if ( data.error != undefined ) {
                            OW.warning(data.error);
                        }
                    }
                });
            });
            ';
        }

        $actionData = array(
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LABEL => $lang->text('ocsfavorites', 'remove_favorite_button'),
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_HREF => 'javascript://',
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_ID => $btnRemoveId,
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_CLASS => 'ow_mild_red',
            BASE_CMP_ProfileActionToolbar::DATA_KEY_LINK_ATTRIBUTES => !$isFavorite ? array('style' => 'display: none') : array()
        );

        $event->add($actionData);

        $script .=
        '$("#' . $btnRemoveId . '").click(function(){
            if ( confirm(' . json_encode($lang->text('ocsfavorites', 'remove_from_favorites_confirm')) . ') )
            {
                var $btn = $(this);
                $.ajax({
                    url: ' . json_encode(OW::getRouter()->urlForRoute('ocsfavorites.remove')) . ',
                    type: "POST",
                    data: { favoriteId: ' . json_encode($userId) . ' },
                    dataType: "json",
                    success: function(data) {
                        if ( data.result == true ) {
                            OW.info(data.msg);
                            $btn.hide();
                            $("#' . $btnAddId. '").show();
                        }
                        else if ( data.error != undefined ) {
                            OW.warning(data.error);
                        }
                    }
                });
            }
        });
        ';

        OW::getDocument()->addOnloadScript($script);
    }

    /**
     * @param OW_Event $event
     */
    public function onUserUnregister( OW_Event $event )
    {
        $params = $event->getParams();

        $userId = $params['userId'];

        OCSFAVORITES_BOL_Service::getInstance()->deleteUserFavorites($userId);
    }

    /**
     * @param BASE_CLASS_EventCollector $event
     */
    public function addAuthLabels( BASE_CLASS_EventCollector $event )
    {
        $language = OW::getLanguage();
        $item = array(
            'ocsfavorites' => array(
                'label' => $language->text('ocsfavorites', 'auth_group_label'),
                'actions' => array(
                    'add_to_favorites' => $language->text('ocsfavorites', 'auth_action_label_add_favorite'),
                )
            )
        );

        if ( OW::getConfig()->getValue('ocsfavorites', 'can_view') )
        {
            $item['ocsfavorites']['actions']['view_users'] = $language->text('ocsfavorites', 'auth_action_label_view_users');
        }

        $event->add($item);
    }

    /**
     * @param BASE_EventCollector $event
     */
    public function adsEnabled( BASE_EventCollector $event )
    {
        $event->add('ocsfavorites');
    }

    /**
     * @param BASE_CLASS_EventCollector $event
     */
    public function addQuickLink( BASE_CLASS_EventCollector $event )
    {
        $service = OCSFAVORITES_BOL_Service::getInstance();
        $userId = OW::getUser()->getId();

        $count = $service->countFavoritesForUser($userId);
        if ( $count > 0 )
        {
            $url = OW::getRouter()->urlForRoute('ocsfavorites.list');
            $event->add(array(
                BASE_CMP_QuickLinksWidget::DATA_KEY_LABEL => OW::getLanguage()->text('ocsfavorites', 'my_favorites'),
                BASE_CMP_QuickLinksWidget::DATA_KEY_URL => $url,
                BASE_CMP_QuickLinksWidget::DATA_KEY_COUNT => $count,
                BASE_CMP_QuickLinksWidget::DATA_KEY_COUNT_URL => $url,
            ));
        }

        $count = $service->countUsersWhoAddedUserAsFavorite($userId);
        if ( $count && OW::getConfig()->getValue('ocsfavorites', 'can_view')
            && OW::getUser()->isAuthorized('ocsfavorites', 'view_users') )
        {
            $url = OW::getRouter()->urlForRoute('ocsfavorites.added_list');
            $event->add(array(
                BASE_CMP_QuickLinksWidget::DATA_KEY_LABEL => OW::getLanguage()->text('ocsfavorites', 'added_me'),
                BASE_CMP_QuickLinksWidget::DATA_KEY_URL => $url,
                BASE_CMP_QuickLinksWidget::DATA_KEY_COUNT => $count,
                BASE_CMP_QuickLinksWidget::DATA_KEY_COUNT_URL => $url,
            ));

            $mutual = $service->countMutualFavorites($userId);
            if ( $mutual )
            {
                $url = OW::getRouter()->urlForRoute('ocsfavorites.mutual_list');
                $event->add(array(
                    BASE_CMP_QuickLinksWidget::DATA_KEY_LABEL => OW::getLanguage()->text('ocsfavorites', 'mutual_attraction'),
                    BASE_CMP_QuickLinksWidget::DATA_KEY_URL => $url,
                    BASE_CMP_QuickLinksWidget::DATA_KEY_COUNT => $mutual,
                    BASE_CMP_QuickLinksWidget::DATA_KEY_COUNT_URL => $url,
                ));
            }
        }
    }

    /**
     * @param BASE_CLASS_EventCollector $e
     */
    public function addNotificationAction( BASE_CLASS_EventCollector $e )
    {
        $e->add(array(
            'section' => 'ocsfavorites',
            'action' => 'ocsfavorites-add_favorite',
            'sectionIcon' => 'ow_ic_heart',
            'sectionLabel' => OW::getLanguage()->text('ocsfavorites', 'email_notifications_section_label'),
            'description' => OW::getLanguage()->text('ocsfavorites', 'email_notifications_setting_post'),
            'selected' => true
        ));
    }

    /**
     * @param OW_Event $e
     */
    public function onAddFavorite( OW_Event $e )
    {
        $params = $e->getParams();

        $userId = (int) $params['userId'];
        $favoriteId = (int) $params['favoriteId'];
        $id = (int) $params['id'];

        if ( OW::getConfig()->getValue('ocsfavorites', 'can_view')
            && OW::getAuthorization()->isUserAuthorized($favoriteId, 'ocsfavorites', 'view_users') )
        {
            $params = array(
                'pluginKey' => 'ocsfavorites',
                'entityType' => 'ocsfavorites_add_favorite',
                'entityId' => $id,
                'action' => 'ocsfavorites-add_favorite',
                'userId' => $favoriteId,
                'time' => time()
            );

            $mutual = OCSFAVORITES_BOL_Service::getInstance()->isFavorite($favoriteId, $userId);

            $avatar = BOL_AvatarService::getInstance()->getDataForUserAvatars(array($userId));
            $data = array(
                'avatar' => $avatar[$userId],
                'string' => array(
                    'key' => 'ocsfavorites+email_notification_post' . ( $mutual ? '_mutual' : ''),
                    'vars' => array(
                        'userName' => $avatar[$userId]['title'],
                        'userUrl' => $avatar[$userId]['url']
                    )
                ),
                'url' => $avatar[$userId]['url']
            );

            $event = new OW_Event('notifications.add', $params, $data);
            OW::getEventManager()->trigger($event);
        }
    }

    /**
     * @param OW_Event $e
     */
    public function onRemoveFavorite( OW_Event $e )
    {
        $params = $e->getParams();

        $userId = (int) $params['userId'];
        $favoriteId = (int) $params['favoriteId'];
        $id = (int) $params['id'];

        if ( OW::getConfig()->getValue('ocsfavorites', 'can_view')
            && OW::getAuthorization()->isUserAuthorized($favoriteId, 'ocsfavorites', 'view_users') )
        {
            $params = array(
                'entityType' => 'ocsfavorites_add_favorite',
                'entityId' => $id
            );
            $event = new OW_Event('notifications.remove', $params);
            OW::getEventManager()->trigger($event);
        }
    }

    /**
     * @param OW_Event $e
     */
    public function initHint( OW_Event $e )
    {
        OCSFAVORITES_CLASS_HintBridge::getInstance()->init();
    }

    public function genericInit()
    {
        $em = OW::getEventManager();

        $em->bind(OW_EventManager::ON_USER_UNREGISTER, array($this, 'onUserUnregister'));
        $em->bind('ads.enabled_plugins', array($this, 'adsEnabled'));
        $em->bind('notifications.collect_actions', array($this, 'addNotificationAction'));
        $em->bind('ocsfavorites.add_favorite', array($this, 'onAddFavorite'));
        $em->bind('ocsfavorites.remove_favorite', array($this, 'onRemoveFavorite'));

        $credits = new OCSFAVORITES_CLASS_Credits();
        $em->bind('usercredits.on_action_collect', array($credits, 'bindCreditActionsCollect'));
    }

    public function init()
    {
        $this->genericInit();
        $em = OW::getEventManager();

        $em->bind(BASE_CMP_ProfileActionToolbar::EVENT_NAME, array($this, 'addProfileToolbarAction'));
        $em->bind('admin.add_auth_labels', array($this, 'addAuthLabels'));
        $em->bind(BASE_CMP_QuickLinksWidget::EVENT_NAME, array($this, 'addQuickLink'));
        $em->bind(OW_EventManager::ON_PLUGINS_INIT, array($this, 'initHint'));
    }
}