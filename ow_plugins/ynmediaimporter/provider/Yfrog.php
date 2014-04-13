<?php

class Ynmediaimporter_Provider_Yfrog extends Ynmediaimporeter_Provider_Abstract
{

    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @var string
     */
    protected $_serviceName = 'yfrog';

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
        $this -> _urls['host'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/yfrog';
        $this -> _urls['logout'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/yfrog/logout';
        $this -> _urls['login'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/yfrog/login';
        $this -> _urls['getPhotos'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/yfrog/photos';
        $this -> _urls['getAlbums'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/yfrog/albums';
    }

    public function getUserUId()
    {
    	
        if (null == $this -> _uid)
        {
            $user = $this -> getUser();
            $this -> _uid = 0;
            if (isset($user['id']))
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

    public function getUserProfileUrl()
    {
        $user = $this -> getUser();
        return sprintf('https://twitter.com/%s', $user['screen_name']);
    }

    public function getUserDisplayname()
    {
        $user = $this -> getUser();
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
        $max_limit = YNMEDIAIMPORTER_PER_PAGE;
		
        $params = array_merge(array(
            'limit' => $max_limit,
            'offset' => 0,
            'uid' => $this -> getUserUId(),
            'aid' => '',
        ), $params);
		
		if($params['limit'] > 25){
			$params['limit'] = 25;
		}
        
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
        
    	if (!is_array($response['result']['photos'])){
        	$data = array(
	            'result' => $result,
	            'params' => $params,
	            'media' => 'photo',
	        );
	        return array(
	            $this -> correctNodesStatus($data['result']),
	            $data['params'],
	            $data['media'],
	        );
        }
        
        
        foreach ($response['result']['photos'] as $row)
        {
            $id =  str_replace("http://yfrog.com/api/photoinfo.json?url=http://yfrog.com/", "", $row['photo_link']);
            
            $nodeId = $this -> createNodeId('yfrog', 'photo', $id);
            
            $result[$nodeId] = array(
                'nid' => $nodeId,
                'id' => $id,
                'uid' => $row['photo_author'],
                'aid' => $params['aid'],
                'photo_count' => 1,
                'media' => 'photo',
                'media_parent' => '',
                'provider' => 'yfrog',
                'title' => '',
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
