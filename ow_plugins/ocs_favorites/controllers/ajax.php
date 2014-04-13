<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Favorites ajax action controller
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_favorites.controllers
 * @since 1.5.3
 */
class OCSFAVORITES_CTRL_Ajax extends OW_ActionController
{
    public function add()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array()));
        }

        $lang = OW::getLanguage();

        if ( !OW::getUser()->isAuthenticated() )
        {
            exit(json_encode(array('result' => false, 'error' => $lang->text('ocsfavorites', 'signin_required'))));
        }

        if ( !OW::getUser()->isAuthorized('ocsfavorites', 'add_to_favorites') )
        {
            exit(json_encode(array()));
        }

        if ( empty($_POST['favoriteId']) )
        {
            exit(json_encode(array()));
        }

        $service = OCSFAVORITES_BOL_Service::getInstance();

        $userId = OW::getUser()->getId();
        $favoriteId = (int) $_POST['favoriteId'];

        $user = BOL_UserService::getInstance()->findUserById($favoriteId);

        if ( !$user )
        {
            exit(json_encode(array()));
        }

        $favorite = $service->isFavorite($userId, $favoriteId);
        if ( $favorite )
        {
            exit(json_encode(array()));
        }

        $service->addFavorite($userId, $favoriteId);

        // track credits
        $eventParams = array('pluginKey' => 'ocsfavorites', 'action' => 'add_to_favorites');
        if ( OW::getEventManager()->call('usercredits.check_balance', $eventParams) === true )
        {
            OW::getEventManager()->call('usercredits.track_action', $eventParams);
        }

        exit(json_encode(array('result' => true, 'msg' => $lang->text('ocsfavorites', 'favorite_added'))));
    }

    public function remove()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            exit(json_encode(array()));
        }

        $lang = OW::getLanguage();

        if ( !OW::getUser()->isAuthenticated() )
        {
            exit(json_encode(array('result' => false, 'error' => $lang->text('ocsfavorites', 'signin_required'))));
        }

        if ( !OW::getUser()->isAuthorized('ocsfavorites', 'add_to_favorites') )
        {
            exit(json_encode(array()));
        }

        if ( empty($_POST['favoriteId']) )
        {
            exit(json_encode(array()));
        }

        $service = OCSFAVORITES_BOL_Service::getInstance();

        $userId = OW::getUser()->getId();
        $favoriteId = (int) $_POST['favoriteId'];

        $user = BOL_UserService::getInstance()->findUserById($favoriteId);

        if ( !$user )
        {
            exit(json_encode(array()));
        }

        $favorite = $service->isFavorite($userId, $favoriteId);
        if ( !$favorite )
        {
            exit(json_encode(array()));
        }

        $service->deleteFavorite($userId, $favoriteId);

        exit(json_encode(array('result' => true, 'msg' => $lang->text('ocsfavorites', 'favorite_removed'))));
    }
}