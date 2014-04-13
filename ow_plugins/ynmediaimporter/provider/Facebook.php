<?php

/**
 * @package Social media importer
 * @subpackage provider
 * @author nam nguyen
 * @license YouNet Company.
 */
class Ynmediaimporter_Provider_Facebook extends Ynmediaimporeter_Provider_Abstract
{

    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @var string
     */
    protected $_serviceName = 'facebook';
	
	/**
     * 
     * @see Socialbridge_Api_Facebook
     * 
     */
     
     protected $_facebookAPI = NULL;

    /**
     * overwrite from abstract class
     *
     * @var int
     */
    protected $_maxPhotoLimit = 100;

    public function _getPhotos($params, $cache = 0)
    {
        $params = array_merge(array(
            'extra' => 'my',
            'limit' => YNMEDIAIMPORTER_PER_PAGE,
            'offset' => 0,
            'uid' => 'me()',
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
		
		$photos = $this->_facebookAPI->getPhotos($params);

        $result = $this -> correctPhotosFormat($photos);

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

    public function _getAlbums($params, $cache = false)
    {
        $params = array_merge(array(
            'extra' => 'my',
            'limit' => YNMEDIAIMPORTER_PER_PAGE,
            'offset' => 0,
            'uid' => 'me()',
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

        if ($cache && ($result = $this -> loadFromCache($this -> createCacheKey($params))) !== false)
        {
            return array(
                $this -> correctNodesStatus($result),
                $params,
                'album',
            );
        }
		$albums = $this->_facebookAPI->getAlbums($params);

        $result = $this -> correctAlbumsFormat($albums);

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

    public function correctPhotosFormat($photos = array())
    {
        $ret = array();
        $photos = (array)$photos;

        foreach ($photos as $photo)
        {
            $nid = $this -> createNodeId($this -> _serviceName, 'photo', $photo['pid']);
            $ret[$nid] = array(
                'nid' => $nid,
                'id' => $photo['pid'],
                'aid' => $photo['aid'],
                'uid' => $photo['owner'],
                'media' => 'photo',
                'media_parent' => 'album',
                'provider' => 'facebook',
                'title' => $photo['caption'],
                'description' => '',
                'photo_count' => 1,
                'src_thumb' => str_replace('_t.jpg', '_a.jpg', $photo['src']),
                'src_small' => $photo['src_small'],
                'src_medium' => $photo['src'],
                'src_big' => $photo['src_big'],
                'status' => '',
            );
        }
        return $ret;

    }

    /**
     * @var array $cover_pids
     */
    public function correctAlbumsFormat($albums)
    {
        $pid = array();
        foreach ((array)$albums as $album)
        {

            $pid[] = "'" . (isset($album['cover_pid']) ? $album['cover_pid'] : "") . "'";
        }

        $pid = implode(',', $pid);

        /**
         * @var string
         */
        $query = "SELECT pid, src_small, caption, src, src_big from photo where pid IN ($pid)";
		
		$covers = $this->_facebookAPI->facebookQuery(array("query"=> $query));

        /**
         * define map to used later.
         */
        $maps = array();

        foreach ($covers as $cover)
        {
            $pid = $cover['pid'];
            $maps[$pid] = array(
                $cover['src_small'],
                $cover['src'],
                $cover['src_big']
            );
        }
        unset($covers);

        foreach ($albums as $index => $album)
        {
            $coverId = $album['cover_pid'];
            @list($scr_small, $src_medium, $src_big) = $maps[$coverId];
            $albums[$index]['src_small'] = $scr_small;
            $albums[$index]['src_medium'] = $src_medium;
            $albums[$index]['src_big'] = $src_big;
        }

        /**
         * reover albums
         */
        $result = array();

        foreach ($albums as $album)
        {
            $album = $this -> _correctAlbumFormat($album);
            $result[$album['nid']] = $album;
        }
        unset($albums);

        return $result;
    }

    /**
     * match to common interface of all albums.
     */
    public function _correctAlbumFormat($album)
    {
        return array(
            'nid' => $this -> createNodeId($this -> _serviceName, 'album', $album['aid']),
            'id' => $album['aid'],
            'aid' => $album['aid'],
            'uid' => $album['owner'],
            'media' => 'album',
            'media_parent' => '',
            'provider' => 'facebook',
            'title' => $album['name'],
            'description' => '',
            'photo_count' => $album['photo_count'],
            'src_thumb' => str_replace('_t.jpg', '_a.jpg', $album['src_small']),
            'src_small' => $album['src_small'],
            'src_medium' => $album['src_medium'],
            'src_big' => $album['src_big'],
            'status' => '',
        );
        return $item;
    }

    /**
     * constructor.
     */
    public function __construct()
    {
    	if(!OW::getPluginManager()->isPluginActive('ynsocialbridge'))
		{
			return;
		}
        $socialBridgeObj = new YNSOCIALBRIDGE_CLASS_Core();
        $this->_facebookAPI = $socialBridgeObj -> getInstance('facebook');
    }

    /**
     * override method from abstract class.
     * @see ./Abstract.php
     * @param string $callback_url
     * @return string
     */
    public function getAuthUrl($callback_url, $params = array())
    {
		/*
        $params['scope'] = "user_photos,friends_photos,offline_access,read_stream";
		$params['redirect_uri'] = $callback_url;
        $url = $this -> _facebookAPI -> getLoginUrl($params);
        return $url;
        */
    	$url = $this -> _facebookAPI->getConnectUrl() .
						'?scope=user_photos,friends_photos,offline_access,read_stream'.
						'&' . http_build_query(array(
						'callbackUrl' => $callback_url));
		
        return $url;
    }

    public function getDisconnectUrl()
    {
    	return OW::getRouter()->urlForRoute('ynmediaimporter.disconnect', array('service' => 'facebook'));
    	/*
        $front =  Zend_Controller_Front::getInstance();
        $request =  $front->getRequest();
        $router = $front->getRouter();
        return $router->assemble(array('action'=>'disconnect-facebook'),'ynmediaimporter_general',true);
        */
    }

    /**
     * do connect to this service.
     * call this method to this method in callback functions. when auth process
     * is done.
     * @param array $data , this data post form from remote server
     * @return Ynmediaimporeter_Provider_Abstract
     * @throws Exception
     */
    public function doConnect($post)
    {
    		$data = array();
		    if (isset($post['ssid']) && !empty($post['ssid']))
	        {
	            $_SESSION[YNMEDIAIMPORTER_SSID] = $post['ssid'];
	        }
    		$this->_facebookAPI->saveToken();
         	$token  = $this->_facebookAPI->_accessToken;

		   	if($token)
		    {
		    	$data['connect_data'] = $token;
		    	$data['connect_data_time'] =  time();
		   	}
		   	$data['is_connected'] = 1;
		   	$me = $this -> _facebookAPI -> getOwnerInfo();
		   	
        	$data['user'] = (array)$me;
			$data['connect_data'] = 1;
    		
			/**
	         * set to session.
	         */
	        $this -> setSession($data);
	        return $this;
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

	public function getUserAvatarUrl()
    {
    	$data = parent::getUserAvatarUrl();
    	while (is_array($data)) {
    		$data = current($data);
    	}
    	return $data;
    }
	
    public function getUserSquareAvatarUrl()
    {
        return sprintf("https://graph.facebook.com/%s/picture/?type=large", $this -> getUserUId());
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
     * check logout.
     * do disconnect to service
     * 1. clear peristant data related to this object
     * 2. call remote process to clear persitent dat on remote server.
     * 3. WARING: DO NOT UNSET SESSION ID OF SSID VALUE. IT IS SHARED TO ALL
     * SERVICE.
     * @return Ynmediaimporeter_Provider_Abstract
     * @throws Exceptions
     */
    public function doDisconnect()
    {
        $this -> unsetSession();
        return $this;
    }
   
}

