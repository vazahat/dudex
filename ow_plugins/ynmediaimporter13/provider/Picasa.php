<?php

class Ynmediaimporter_Provider_Picasa extends Ynmediaimporeter_Provider_Abstract
{
    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @var string
     */
    protected $_serviceName = 'picasa';

    /**
     *
     * @var int
     *
     */

    protected $_maxPhotoLimit = 100;
    
    
    protected $_maxAlbumLimit = 100;
    
    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @return void
     */
    protected function _init()
    {
        /**
         * set urls for connection.
         */
        $this -> _urls['host'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/picasa';
        $this -> _urls['logout'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/picasa/logout';
        $this -> _urls['login'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/picasa/login';
        $this -> _urls['getPhotos'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/picasa/photos';
        $this -> _urls['getAlbums'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/picasa/albums';

    }

    /**
     * @return string
     */
    public function getUserDisplayname()
    {

        $user = $this -> getUser();
        return $user['full_name'];
    }

    public function getUserProfileUrl()
    {
        return 'https://picasaweb.google.com/home';
    }

    public function getUserAvatarUrl()
    {
        $user = $this -> getUser();
        return $user['avatar'];
    }

    public function getUserSquareAvatarUrl()
    {
        $user = $this -> getUser();
        return str_replace("/s64-c/", "/s160-c/", $user['avatar']);
    }

    public function getUserUid()
    {
        if (null == $this -> _uid)
        {
            $user = $this -> getUser();
            $this -> _uid = $user['user_id'];
        }
        return $this -> _uid;

    }

    /**
     * @param array $params
     * @return objects
     */
    public function _getAlbums($params, $cache = 1)
    {

        $params = array_merge(array(
            'limit' => YNMEDIAIMPORTER_PER_PAGE,
            'offset' => 0,
            'uid' => $this -> getUserUId(),
            'aid' => '',
        ), $params);

        if ($cache && ($data = $this -> loadFromCache($this -> createCacheKey($params))) !== false)
        {
            return array(
                $this -> correctNodesStatus($data['result']),
                $data['params'],
                $data['media'],
            );
        }

        $response = $this -> RPC($this -> _urls['getAlbums'], $params);

        /* recorrec rows format*/
        $result = array();

        foreach ($response as $row)
        {
            $nodeId = $this -> createNodeId('picasa', 'album', $row['album_id']);

            $result[$nodeId] = array(
                'nid' => $nodeId,
                'id' => $row['album_id'],
                'uid' => isset($row['user_id']) ? $row['user_id'] : $params['uid'],
                'aid' => isset($row['album_id']) ? $row['album_id'] : $params['aid'],
                'photo_count' => $row['photo_count'],
                'media' => 'album',
                'provider' => 'picasa',
                'media_parent' => '',
                'title' => $row['title'],
                'src_thumb' => $row['thumb'],
                'src_small' => $row['thumb'],
                'src_medium' => $row['thumb'],
                'src_big' => $row['large'],
                'description' => '',
                'status' => 0,
            );
        }

        $data = array(
            'result' => $result,
            'params' => $params,
            'media' => 'album',
        );

        $this -> saveToCache($data, $this -> createCacheKey($params));

        return array(
            $this -> correctNodesStatus($data['result']),
            $data['params'],
            $data['media'],
        );
    }

    /**
     * @param array $params
     * @return objects
     */
    public function _getPhotos($params, $cache = 1)
    {

        $params = array_merge(array(
            'limit' => YNMEDIAIMPORTER_PER_PAGE,
            'offset' => 0,
            'uid' => $this -> getUserUId(),
            'aid' => '',
        ), $params);

        if ($cache && ($data = $this -> loadFromCache($this -> createCacheKey($params))) !== false)
        {
            return array(
                $this -> correctNodesStatus($data['result']),
                $data['params'],
                $data['media'],
            );
        }

        $params['album'] = $album = $params['aid'];
		
        $response = $this -> RPC($this -> _urls['getPhotos'], $params);

        /* recorrec rows format*/
        $result = array();

        foreach ($response as $row)
        {
            $nodeId = $this -> createNodeId('picasa', 'photo', $row['photo_id']);

            $result[$nodeId] = array(
                'nid' => $nodeId,
                'id' => $row['photo_id'],
                'uid' => isset($row['user_id']) ? $row['user_id'] : $params['uid'],
                'aid' => $params['aid'],
                'photo_count' => 1,
                'media' => 'photo',
                'media_parent' => 'album',
                'provider' => 'picasa',
                'title' => $row['title'],
                'src_thumb' => $row['thumb'],
                'src_small' => $row['thumb'],
                'src_medium' => $row['thumb'],
                'src_big' => $row['large'],
                'description' => '',
                'status' => 0,
            );
        }

        $data = array(
            'result' => $result,
            'params' => $params,
            'media' => 'photo',
        );

        $this -> saveToCache($data, $this -> createCacheKey($params));

        return array(
            $this -> correctNodesStatus($data['result']),
            $data['params'],
            $data['media'],
        );

    }

    /**
     * return photos array
     */
    public function getAllPhoto($params)
    {

        $count = $params['photo_count'];

        $limit = $count < 21 ? 20 : $this -> getMaxPhotoLimit();

        $total = ceil($count / $limit);

        $page = 0;

        $media = $params['media'];
        $aid = $params['aid'];

        $result = array();

        do
        {
            $photos = $this -> _getPhotos(array(
                'offset' => $page * $limit,
                'limit' => $limit,
                'extra' => 'aid',
                'aid' => $aid,
                'media' => $media,
            ), 1);

            foreach ($photos[0] as $photo)
            {
                $result[$photo['nid']] = $photo;
            }
            ++$page;
        }
        while($page < $total);

        return $result;
    }
    

}
