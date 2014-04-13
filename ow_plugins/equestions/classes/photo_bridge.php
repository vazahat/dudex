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
class EQUESTIONS_CLASS_PhotoBridge
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_CLASS_PhotoBridge
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_CLASS_PhotoBridge
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
        return OW::getPluginManager()->isPluginActive('photo');
    }

    public function findUserPhotos( $userId, $start, $offset )
    {
        $photoService = PHOTO_BOL_PhotoService::getInstance();
        $photoDao = PHOTO_BOL_PhotoDao::getInstance();
        $albumDao = PHOTO_BOL_PhotoAlbumDao::getInstance();

        $query = 'SELECT p.* FROM ' . $photoDao->getTableName() . ' AS p
            INNER JOIN ' . $albumDao->getTableName() . ' AS a ON p.albumId=a.id
                WHERE a.userId=:u AND p.status = "approved" ORDER BY p.addDatetime DESC
                    LIMIT :start, :offset';

        $list = OW::getDbo()->queryForList($query, array(
            'u' => $userId,
            'start' => $start,
            'offset' => $offset
        ));

        $out = array();
        foreach ( $list as $photo )
        {
            $id = $photo['id'];
            $out[$id] = array(
                'id' => $id,
                'thumb' => $photoService->getPhotoPreviewUrl($id),
                'url' => $photoService->getPhotoUrl($id),
                'path' => $photoService->getPhotoPath($id),
                'description' => $photo['description'],
                'permalink' => OW::getRouter()->urlForRoute('view_photo', array(
                    'id' => $id
                ))
            );

            $out[$id]['oembed'] = json_encode(array(
                'type' => 'photo',
                'url' => $out[$id]['url'],
                'href' => $out[$id]['permalink'],
                'description' => $out[$id]['description']
            ));
        }

        return $out;
    }
}