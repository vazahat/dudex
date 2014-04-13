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
 * @package questions.classes
 */
class EQUESTIONS_CLASS_VideoBridge
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_CLASS_VideoBridge
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_CLASS_VideoBridge
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function isActive()
    {
        return OW::getPluginManager()->isPluginActive('video');
    }

    public function findUserVideos( $userId, $start, $offset )
    {
        $clipDao = VIDEO_BOL_ClipDao::getInstance();

        $example = new OW_Example();

        $example->andFieldEqual('status', 'approved');
        $example->andFieldEqual('userId', $userId);

        $example->setOrder('`addDatetime` DESC');
        $example->setLimitClause($start, $offset);

        $list = $clipDao->findListByExample($example);

        $out = array();
        foreach ( $list as $video )
        {
            $id = $video->id;
            $videoThumb = VIDEO_BOL_ClipService::getInstance()->getClipThumbUrl($id);

            $out[$id] = array(
                'id' => $id,
                'embed' => $video->code,
                'title' => UTIL_String::truncate($video->title, 65, ' ...'),
                'description' => UTIL_String::truncate($video->description, 130, ' ...'),
                'thumb' => $videoThumb == 'undefined' ? null : $videoThumb,
                'date' => UTIL_DateTime::formatDate($video->addDatetime),
                'permalink' => OW::getRouter()->urlForRoute('view_clip', array(
                    'id' => $id
                ))
            );

            $out[$id]['oembed'] = json_encode(array(
                'type' => 'video',
                'thumbnail_url' => $out[$id]['thumb'],
                'html' => $out[$id]['embed'],
                'title' => $out[$id]['title'],
                'description' => $out[$id]['description']
            ));
        }

        return $out;
    }
}