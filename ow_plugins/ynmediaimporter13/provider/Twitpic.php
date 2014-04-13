<?php

class Ynmediaimporter_Provider_Twitpic extends Ynmediaimporeter_Provider_Abstract
{
    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @var string
     */
    protected $_serviceName = 'twitpic';
    
    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @var string
     */
    protected $_hasGetAlbums = false;
    
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
        $this -> _urls['host'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/twitpic';
        $this -> _urls['logout'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/twitpic/logout';
        $this -> _urls['login'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/twitpic/login';
        $this -> _urls['getPhotos'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/twitpic/photos';
        $this -> _urls['getAlbums'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/twitpic/albums';
    }
    
    public function getUserUid()
    {
        if (null == $this -> _uid)
        {
            $user = $this -> getUser();
            $this -> _uid = $user['id'];
        }
        return $this -> _uid;
    }

    public function getUserAvatarUrl()
    {
        $user = $this -> getUser();
        return $user['avatar'];
    }

    public function getUserSquareAvatarUrl()
    {
        $user = $this -> getUser();
        return $user['profile_image_url'];
    }
    
    public function getUserProfileUrl(){
        $user = $this->getUser();
        return sprintf('https://twitter.com/%s',$user['screen_name']);
    }
    
    public function getUserDisplayname(){
        $user = $this->getUser();
        return $user['name'];
    }
    
    public function _getAlbums($params, $cache = true)
    {
        return $this -> _getPhotos($params, $cache);
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

        $response = $this -> RPC($this -> _urls['getPhotos'], $params);
        
        /* recorrec rows format*/
        $result = array();
        
        foreach ($response['images'] as $row)
        {
            $id =  $row['short_id'];
                        
            $nodeId = $this -> createNodeId('twitpic', 'photo', $id);
            
            $result[$nodeId] = array(
                'nid' => $nodeId,
                'id' => $id,
                'uid' => $row['user_id'],
                'aid' => $album,
                'photo_count' => 1,
                'media' => 'photo',
                'media_parent' => '',
                'provider' => 'twitpic',
                'title' => $row['message'],
                'src_thumb' => $row['photo_location'],
                // photo_thumb
                'src_small' => $row['photo_location'],
                'src_medium' => $row['photo_location'],
                'src_big' => $row['photo_location'],
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
}
