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
 * @package ucarousel.components
 * @since 1.0
 */
class UCAROUSEL_CMP_Users extends OW_Component
{
    private $uniqId;

    public function __construct( $users, $size, $layout )
    {
        parent::__construct();

        $questionService = BOL_QuestionService::getInstance();
        $userService = BOL_UserService::getInstance();

        $this->uniqId = uniqid('ucl_');

        $idList = $this->fetchIdList($users);

        $qList = $questionService->getQuestionData($idList, array(
            'sex', 'birthdate'
        ));

        $displayNames = $userService->getDisplayNamesForList($idList);
        $urls = $userService->getUserUrlsForList($idList);

        $tplData = array();

        foreach ( $idList as $userId )
        {
            $tplData[$userId] = array();
            $tplData[$userId]['displayName'] = empty($displayNames[$userId]) ? null : $displayNames[$userId];
            $tplData[$userId]['url'] = empty($urls[$userId]) ? null : $urls[$userId];
            $tplData[$userId]['sex'] = empty($qList[$userId]['sex']) || in_array($layout, array(3, 4))
                ? null
                : strtolower(BOL_QuestionService::getInstance()->getQuestionValueLang('sex', $qList[$userId]['sex']));

            $tplData[$userId]['birthdate'] = null;

            if ( !empty($qList[$userId]['birthdate']) && in_array($layout, array(1, 3)) )
            {
                $date = UTIL_DateTime::parseDate($qList[$userId]['birthdate'], UTIL_DateTime::MYSQL_DATETIME_DATE_FORMAT);
                $age = UTIL_DateTime::getAge($date['year'], $date['month'], $date['day']);
                $tplData[$userId]['birthdate'] = $age;
            }


            $avatar = BOL_AvatarService::getInstance()->getAvatarUrl($userId, 2);
            $tplData[$userId]['thumb'] =  $avatar ? $avatar : BOL_AvatarService::getInstance()->getDefaultAvatarUrl(2);
        }

        $sizes = array(
            'small' => 100,
            'medium' => 150,
            'big' => OW::getConfig()->getValue('base', 'avatar_big_size')
        );

        $this->assign('list', $tplData);
        $avatarSize = $sizes[$size];

        $this->assign('size', $size);
        $this->assign('uniqId', $this->uniqId);

        OW::getDocument()->addStyleDeclaration('.uc-avatar-size { width: ' . $avatarSize . 'px; height: ' . ($avatarSize + $avatarSize / 10) . 'px; }');
        OW::getDocument()->addStyleDeclaration('.uc-carousel-size { height: ' . ($avatarSize + 50) . 'px; }');

        OW::getDocument()->addStyleDeclaration('.uc-shape-waterWheel .uc-carousel { width: ' . ($avatarSize + 20) . 'px; }');
    }

    public function initCarousel( $options )
    {
        $static = OW::getPluginManager()->getPlugin('ucarousel')->getStaticUrl();
        OW::getDocument()->addScript($static . 'jquery.event.drag.js');
        OW::getDocument()->addScript($static . 'jquery.event.drop.js');
        OW::getDocument()->addScript($static . 'jquery.roundabout.min.js');
        OW::getDocument()->addScript($static . 'jquery.roundabout-shapes.min.js');
        OW::getDocument()->addStyleSheet($static . 'styles.css?380');

        $js = UTIL_JsGenerator::newInstance();

        $extraOptions = array(
            'lazySusan' => array(),
            'waterWheel' => array( 'dragAxis' => 'y' ),
            'figure8' => array(),
            'square' => array( 'minOpacity' => 0.6 ),
            'conveyorBeltLeft' => array( 'minOpacity' => 1.0 ),
            'conveyorBeltRight' => array( 'minOpacity' => 1.0 ),
            'diagonalRingLeft' => array(),
            'diagonalRingRight' => array(),
            'rollerCoaster' => array( 'minOpacity' => 0.6 ),
            'tearDrop' => array()
        );

        $params = array(
            'minZ' => 2,
            'maxZ' => 100,
            'shape' => $options['shape']
        );

        $params = array_merge($params, $extraOptions[$options['shape']]);

        $this->assign('shape', $options['shape']);

        if ( !empty($options['autoplay']) )
        {
            $params['autoplay'] = true;
            $params['autoplayDuration'] = $options['speed'];
            $params['autoplayPauseOnHover'] = true;
        }

        if ( !empty($options['dragging']) )
        {
            $params['enableDrag'] = true;
        }

        $js->addScript('$("#' . $this->uniqId . '").css("visibility", "visible").roundabout({$params});', array(
            'params' => $params
        ));

        OW::getDocument()->addOnloadScript($js);
    }

    private function fetchIdList( $dtoList )
    {
        $out = array();
        foreach ( $dtoList as $user )
        {
            $out[] = $user->id;
        }

        return $out;
    }
}