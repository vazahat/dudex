<?php

require_once YNMEDIAIMPORTER_PROVIDER_PATH . '/libs/phpFlickr.php';

class Ynmediaimporter_Provider_Flickr extends Ynmediaimporeter_Provider_Abstract
{
    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @var string
     */
    protected $_serviceName = 'flickr';

    protected $_maxPhotoLimit = 200;

    protected $_maxAlbumLimit = 20;

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
        $this -> _urls['host'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/flickr';
        $this -> _urls['login'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/flickr';
        $this -> _urls['logout'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/flickr/logout';
        $this -> _urls['getPhotos'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/flickr/photos';
        $this -> _urls['getFavourites'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/flickr/favourites';
        $this -> _urls['getAlbums'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/flickr/albums';
        $this -> _urls['getGallery'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/flickr/gallery';

    }

    public function getUserUId()
    {
        if (null == $this -> _uid)
        {
            $user = $this -> getUser();
            $this -> _uid = $user['id'];
        }
        return $this -> _uid;
    }

    /**
     * overwrite method
     * get displayname of connected user
     * @return string
     */
    public function getUserDisplayname()
    {
        $user = $this -> getUser();

        if (!empty($user['realname']))
        {
            return $user['realname'];
        }
        else
        if (!empty($user['fullname']))
        {
            return $user['fullname'];
        }
        else
        if (!empty($user['username']))
        {
            return $user['username'];
        }
        return $user['nsid'];
    }

    /**
     * get profile url
     * @return string
     */
    public function getUserProfileUrl()
    {
        $user = $this -> getUser();
        return $user['photosurl'];
    }

    /**
     * get current user avatar.
     * @link http://www.flickr.com/services/api/misc.buddyicons.html
     * @return string
     */
    public function getUserAvatarUrl()
    {
        $user = $this -> getUser();
        if (isset($user['iconfarm']) && $user['iconfarm'] > 0)
        {
            /*
             * http://farm{icon-farm}.staticflickr.com/{icon-server}/buddyicons/{nsid}.jpg
             */
            return sprintf('http://farm%s.staticflickr.com/%s/buddyicons/%s.jpg', $user['iconfarm'], $user['iconserver'], $user['nsid']);
        }
        return 'http://www.flickr.com/images/buddyicon.gif';

    }

    /**
     * get current user avatar.
     * @link http://www.flickr.com/services/api/misc.buddyicons.html
     * @return string
     */
    public function getUserSquareAvatarUrl()
    {
        $user = $this -> getUser();
        if (isset($user['iconfarm']) && $user['iconfarm'] > 0)
        {
            /*
             * http://farm{icon-farm}.staticflickr.com/{icon-server}/buddyicons/{nsid}.jpg
             */
            return sprintf('http://farm%s.staticflickr.com/%s/buddyicons/%s.jpg', $user['iconfarm'], $user['iconserver'], $user['nsid']);
        }
        return 'http://www.flickr.com/images/buddyicon.gif';

    }

    /**
     * return list of albums
     * @link http://www.flickr.com/services/api/misc.urls.html
     */
    public function _getAlbums($params, $cache = 1)
    {

        $params = array_merge(array(
            'extra' => 'my',
            'limit' => YNMEDIAIMPORTER_PER_PAGE,
            'offset' => 0,
            'media' => 'photoset',
            'uid' => $this -> getUserUId(),
            'aid' => '',
            'provider' => 'flickr',
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

        $result = array();

        foreach ($response['photoset'] as $item)
        {
            // http://farm{farm-id}.staticflickr.com/{server-id}/{id}_{secret}.jpg
            $nid = $this -> createNodeId('flickr', 'photoset', $item['id']);
            $result[$nid] = array(
                'nid' => $nid,
                'id' => $item['id'],
                'aid' => $item['id'],
                'uid' => $params['uid'],
                'media_parent' => '',
                'media' => 'photoset',
                'photo_count' => $item['photos'],
                'title' => $item['title'],
                'description' => $item['description'],
                'provider' => 'flickr',
                'src_thumb' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_m.jpg', $item['farm'], $item['server'], $item['primary'], $item['secret']),
                'src_small' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_m.jpg', $item['farm'], $item['server'], $item['primary'], $item['secret']),
                'src_medium' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_c.jpg', $item['farm'], $item['server'], $item['primary'], $item['secret']),
                'src_big' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_b.jpg', $item['farm'], $item['server'], $item['primary'], $item['secret']),
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
     * return list of albums
     * @link http://www.flickr.com/services/api/misc.urls.html
     */
    public function _getGalleries($params = array(), $cache = 0)
    {

        $params = array_merge(array(
            'extra' => 'my',
            'limit' => YNMEDIAIMPORTER_PER_PAGE,
            'offset' => 0,
            'media' => 'gallery',
            'uid' => $this -> getUserUId(),
            'aid' => '',
            'provider' => 'flickr',
        ), $params);

        if ($cache && ($data = $this -> loadFromCache($this -> createCacheKey($params))) !== false)
        {
            return array(
                $this -> correctNodesStatus($data['result']),
                $data['params'],
                $data['media'],
            );
        }

        $response = $this -> RPC($this -> _urls['getGallery'], $params);

        $result = array();

        foreach ($response['galleries']['gallery'] as $item)
        {
            // http://farm{farm-id}.staticflickr.com/{server-id}/{id}_{secret}.jpg
            $nid = $this -> createNodeId('flickr', 'gallery', $item['id']);
            $result[$nid] = array(
                'nid' => $nid,
                'id' => $item['id'],
                'aid' => $item['id'],
                'media_parent' => 'gallery',
                'uid' => $params['uid'],
                'media' => 'gallery',
                'photo_count' => $item['count_photos'],
                'title' => $item['title'],
                'description' => $item['description'],
                'provider' => 'flickr',
                'src_thumb' => @sprintf('http://farm%d.staticflickr.com/%s/%s_%s_m.jpg', $item['primary_photo_farm'], $item['primary_photo_server'], $item['primary_photo_id'], $item['primary_photo_secret']),
                'src_small' => @sprintf('http://farm%d.staticflickr.com/%s/%s_%s_m.jpg', $item['primary_photo_farm'], $item['primary_photo_server'], $item['primary_photo_id'], $item['primary_photo_secret']),
                'src_medium' => @sprintf('http://farm%d.staticflickr.com/%s/%s_%s_c.jpg', $item['primary_photo_farm'], $item['primary_photo_server'], $item['primary_photo_id'], $item['primary_photo_secret']),
                'src_big' => @sprintf('http://farm%d.staticflickr.com/%s/%s_%s_b.jpg', $item['primary_photo_farm'], $item['primary_photo_server'], $item['primary_photo_id'], $item['primary_photo_secret']),
                'status' => 0,
            );
        }

        $data = array(
            'result' => $result,
            'params' => $params,
            'media' => 'album',
        );

        $this -> saveToCache($data, $this -> createCacheKey($params));

        return @array(
            $this -> correctNodesStatus($data['result']),
            $data['params'],
            $data['gallery'],
        );
    }

    /**
     * return list of photos.
     */
    public function _getPhotos($params, $cache = 1)
    {
        $cache = 0;
        $media_parent =  isset($params['media_parent'])?$params['media_parent']:null;
        
        $params = array_merge(array(
            'extra' => 'my',
            'limit' => YNMEDIAIMPORTER_PER_PAGE,
            'offset' => 0,
            'media_parent' => $media_parent,
            'uid' => $this -> getUserUId(),
            'aid' => '',
            'provider' => 'flickr',
        ), $params);

        if ($cache && ($data = $this -> loadFromCache($this -> createCacheKey($params))) !== false)
        {
            return array(
                $this -> correctNodesStatus($data['result']),
                $data['params'],
                $data['media'],
            );
        }

        $lookup = $this -> RPC($this -> _urls['getPhotos'], $params);
        
        
        $result = array();

        if (is_array($lookup))
        {
            foreach ($lookup as $item)
            {
                // http://farm{farm-id}.staticflickr.com/{server-id}/{id}_{secret}.jpg
                $nid = $this -> createNodeId('flickr', 'photo', $item['id']);
                $result[$nid] = array(
                    'nid' => $nid,
                    'id' => $item['id'],
                    'aid' => $params['aid'],
                    'media_parent' => '',
                    'media' => 'photo',
                    'media_parent' => '',
                    'uid' => $params['uid'],
                    'photo_count' => 1,
                    'title' => $item['title'],
                    'description' => '',
                    'provider' => 'flickr',
                    'src_thumb' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_m.jpg', $item['farm'], $item['server'], $item['id'], $item['secret']),
                    'src_small' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_m.jpg', $item['farm'], $item['server'], $item['id'], $item['secret']),
                    'src_medium' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_c.jpg', $item['farm'], $item['server'], $item['id'], $item['secret']),
                    'src_big' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_b.jpg', $item['farm'], $item['server'], $item['id'], $item['secret']),
                    'status' => 0,
                );
            }
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
     * return list of favourite photos.
     */
    public function _getFavourites($params, $cache = 1)
    {
        $cache = 0;
        $media_parent =  isset($params['media_parent']) ? $params['media_parent'] : null;
        
        $params = array_merge(array(
            'extra' => 'my',
            'limit' => YNMEDIAIMPORTER_PER_PAGE,
            'offset' => 0,
            'media_parent' => $media_parent,
            'uid' => $this -> getUserUId(),
            'aid' => '',
            'provider' => 'flickr',
        ), $params);

        if ($cache && ($data = $this -> loadFromCache($this -> createCacheKey($params))) !== false)
        {
            return array(
                $this -> correctNodesStatus($data['result']),
                $data['params'],
                $data['media'],
            );
        }

        $lookup = $this -> RPC($this -> _urls['getFavourites'], $params);
        
        
        $result = array();

        if (is_array($lookup))
        {
            foreach ($lookup as $item)
            {
                // http://farm{farm-id}.staticflickr.com/{server-id}/{id}_{secret}.jpg
                $nid = $this -> createNodeId('flickr', 'photo', $item['id']);
                $result[$nid] = array(
                    'nid' => $nid,
                    'id' => $item['id'],
                    'aid' => $params['aid'],
                    'media_parent' => '',
                    'media' => 'photo',
                    'media_parent' => '',
                    'uid' => $params['uid'],
                    'photo_count' => 1,
                    'title' => $item['title'],
                    'description' => '',
                    'provider' => 'flickr',
                    'src_thumb' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_m.jpg', $item['farm'], $item['server'], $item['id'], $item['secret']),
                    'src_small' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_m.jpg', $item['farm'], $item['server'], $item['id'], $item['secret']),
                    'src_medium' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_c.jpg', $item['farm'], $item['server'], $item['id'], $item['secret']),
                    'src_big' => sprintf('http://farm%d.staticflickr.com/%s/%s_%s_b.jpg', $item['farm'], $item['server'], $item['id'], $item['secret']),
                    'status' => 0,
                );
            }
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
        $media_parent = $params['media_parent'];

        $result = array();

        do
        {
            $photos = $this -> _getPhotos(array(
                'offset' => $page * $limit,
                'limit' => $limit,
                'extra' => 'aid',
                'aid' => $aid,
                'media' => $media,
                'media_parent'=>$media_parent,
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

    public function getLogoutIframeUrl()
    {
        return '';
        return 'https://instagram.com/accounts/logout/';
    }

}
