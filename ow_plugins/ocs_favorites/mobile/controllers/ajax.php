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
 * @since 1.6.0
 */
class OCSFAVORITES_MCTRL_Ajax extends OW_ActionController
{
    public function action()
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

        if ( empty($_POST['favoriteId']) )
        {
            exit(json_encode(array()));
        }

        $command = !empty($_POST['command']) && in_array($_POST['command'], array("remove-favorite", "add-favorite"))
            ? $_POST['command']
            : "add-favorite";

        $service = OCSFAVORITES_BOL_Service::getInstance();

        $userId = OW::getUser()->getId();
        $favoriteId = (int) $_POST['favoriteId'];

        $favorite = $service->isFavorite($userId, $favoriteId);

        if ( !$favorite && !OW::getUser()->isAuthorized('ocsfavorites', 'add_to_favorites') )
        {
            exit(json_encode(array('result' => false)));
        }

        $user = BOL_UserService::getInstance()->findUserById($favoriteId);

        if ( !$user )
        {
            exit(json_encode(array('result' => false)));
        }

        if ( $favorite && $command == "add-favorite" || !$favorite && $command == "remove-favorite" )
        {
            exit(json_encode(array('result' => false)));
        }

        switch ( $command )
        {
            case "add-favorite":
                $service->addFavorite($userId, $favoriteId);

                // track credits
                $eventParams = array('pluginKey' => 'ocsfavorites', 'action' => 'add_to_favorites');
                if ( OW::getEventManager()->call('usercredits.check_balance', $eventParams) === true )
                {
                    OW::getEventManager()->call('usercredits.track_action', $eventParams);
                }

                exit(json_encode(array('result' => true, 'msg' => $lang->text('ocsfavorites', 'favorite_added'))));


            case "remove-favorite":
                $service->deleteFavorite($userId, $favoriteId);

                exit(json_encode(array('result' => true, 'msg' => $lang->text('ocsfavorites', 'favorite_removed'))));
        }
    }
}