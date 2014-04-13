<?php

/**
 * define class for service all
 * @package Socialmediaimporter
 * @license YouNet Company
 * @author nam nguyen
 * @version 4.01
 */
class Ynmediaimporeter_Provider_Abstract
{

    /**
     * service identity name, etc: facebook, twiter, instagram, flickr, picasa,
     * yfrog.
     * name of adapter must be lowercase
     *
     * @var string
     */
    protected $_serviceName = null;

    /**
     * @var string
     */
    protected $_sessionNameSpace = null;

    /**
     * has get albums method
     * @var bool
     */
    protected $_hasGetAlbums = true;

    /**
     * has get photos methos
     * @var bool
     */
    protected $_hasGetPhotos = true;

    /**
     * @var array
     * define list of remote array if neccessary to call by alias.
     * array('host'=>'', 'login'=>'','logout'=>''),
     *
     */
    protected $_urls = array();

    /**
     * @var user data in session
     */
    protected $_user = null;

    /**
     * max photo request
     * @var int
     */
    protected $_maxPhotoLimit = 20;

    /**
     * max photo request
     * @var int
     */
    protected $_maxAlbumLimit = 20;

    /**
     * provider user id
     * @var string/int
     */
    protected $_uid;

    /**
     * specific SE4/DOLPHIN/PHPFOX user id
     * @var int
     */
    static protected $_viewerId = null;

    /**
     * @return int
     */
    public function getMaxPhotoLimit()
    {
        return $this -> _maxPhotoLimit;
    }

    /**
     * @return int
     */
    public function getMaxAlbumLimit()
    {
        return $this -> _maxAlbumLimit;
    }

    /**
     * constructor
     */
    public function __construct()
    {
        /**
         * cacluate service name
         */
        if (null == $this -> _serviceName)
        {
            $this -> _serviceName = strtolower(str_replace('Ynmediaimporeter_Provider_', '', get_class($this)));
        }

        if (null == $this -> _sessionNameSpace)
        {
            $this -> _sessionNameSpace = $this -> _serviceName;
        }

        $this -> _init();
    }

    /**
     * check token is valid
     * @return bool
     */
    public function isAlive()
    {
        return $this -> isConnected();
    }

    /**
     * @param string|array $data
     * @param string|array $message
     * @param string $filename  a part of file name under
     * @return void.
     */
    public function log($data, $message = null, $filename = 'info.log')
    {
        Ynmediaimporter::log($data, $message, $filename);
    }

    /**
     * get session name space
     * @return string.
     */
    public function getSessionNameSpace()
    {
        if (null == $this -> _sessionNameSpace)
        {
            $this -> _sessionNameSpace = $this -> _serviceName;
        }
        return $this -> _sessionNameSpace;
    }

    /**
     * @param string $namespace
     * @return Ynmediaimporeter_Provider_Abstract
     */
    public function setSessionNameSpace($namespace)
    {
        $this -> _sessionNameSpace = $namespace;
        return $this;
    }

    /**
     * get session in currently scope of this provider.
     * @return array
     */
    public function getSession()
    {
        if (isset($_SESSION['YNMEDIAIMPORTER'][$this -> _sessionNameSpace]))
        {
            return $_SESSION['YNMEDIAIMPORTER'][$this -> _sessionNameSpace];
        }
        return array();

    }

    /**
     * @param array $data
     * @return Ynmediaimporeter_Provider_Abstract
     */
    public function setSession($data)
    {
        $_SESSION['YNMEDIAIMPORTER'][$this -> _sessionNameSpace] = $data;
        return $this;
    }

    /**
     * unset session of this service.
     * @return Ynmediaimporeter_Provider_Abstract
     */
    public function unsetSession()
    {
        if (isset($_SESSION['YNMEDIAIMPORTER'][$this -> _sessionNameSpace]))
        {
            unset($_SESSION['YNMEDIAIMPORTER'][$this -> _sessionNameSpace]);
        }
        return $this;
    }

    /**
     * check status of this connections.
     * XXX
     * TODO: we should define max live time of each session key to know it's
     * alive or has gone.
     * @return bool
     */
    public function isConnected()
    {
        $data = $this -> getSession();
		//print_r($data); exit;
        /**
         * session has not set
         */
        if (empty($data))
        {
            return false;
        }

        /**
         * session has been expired.
         */
        if (!isset($data['connect_data']))
        {
            return false;
        }

        /**
         * session has been expired.
         */
        if (!isset($data['user']))
        {
            return false;
        }

        return true;
    }

    /**
     * initialize
     * this method always called after contruction.
     * @return void
     */
    protected function _init()
    {

    }

    /**
     * ssid send back in post form when finish auth process.
     * @param string $ssid
     * @return Ynmediaimporeter_Provider_Abstract
     */
    public function setSSID($ssid)
    {
        $_SESSION[YNMEDIAIMPORTER_SSID] = (string)$ssid;
        return $this;
    }

    /**
     * remote processdure call to $url with params
     * ssid will be auto append.
     * @param string $url
     * @param array $array
     * @param string $format [html, json,xml].  at this time remote server
     * support only json.
     * @return object
     */
    public function RPC($url, $params = array(), $format = 'json')
    {
        $params = (array)$params;

        if (isset($_SESSION[YNMEDIAIMPORTER_SSID]) && !empty($_SESSION[YNMEDIAIMPORTER_SSID]))
        {
            $params['ssid'] = $_SESSION[YNMEDIAIMPORTER_SSID];
        }

        if (!isset($params['service']))
        {
            $params['service'] = $this -> _serviceName;
        }

        if (!isset($params['platform']))
        {
            $params['platform'] = YNMEDIAIMPORTER_PLATFORM;
        }

        if (strpos($url, 'http') !== 0)
        {
            if (isset($this -> _urls[$url]) && !empty($this -> _urls[$url]))
            {
                $url = $this -> _urls[$url];
            }
            else
            {
                $url = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/' . trim($url, '/');
            }
        }

        $url = $url . '?' . http_build_query($params);

        $result = file_get_contents($url);

        if ('json' == $format)
        {
            if (null != $result)
            {
                return json_decode($result, 1);
            }
            else
            {
                return json_decode('[]');
            }
        }

        $this -> log(array(
            'url' => $url,
            'params' => $params,
            'response' => $result,
        ), 'call remote to service ' . $this -> _serviceName, 'rpcall-' . $this -> _serviceName . '.log');

        return $result;
    }

    /**
     * connect url
     * @return string
     */
    public function getConnectUrl()
    {
    	$url = OW::getRouter()->urlForRoute('ynmediaimporter.connect', array('service' => $this -> _serviceName));
        return $url;
    }

    /**
     * connect url
     * @return string
     */
    public function getDisconnectUrl()
    {
    	$url = OW::getRouter()->urlForRoute('ynmediaimporter.disconnect', array('service' => $this -> _serviceName));
        return $url;
    }

    /**
     * connect url
     * @return string
     */
    public function getMainUrl()
    {
    	$url = OW::getRouter()->urlForRoute('ynmediaimporter.index') . '/' . $this -> _serviceName;
//         $url = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('controller' => $this -> _serviceName), 'ynmediaimporter_extended', 1);
        return $url;
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

        //1. clear persistent data.
        $this -> unsetSession();

        //2. call to remote process
        $this -> RPC('logout');

        return $this;
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

        $this -> log($post, 'do connect', $this -> _serviceName . '-doconnect.log');

        $data = array();

        if (isset($post['ssid']) && !empty($post['ssid']))
        {
            $_SESSION[YNMEDIAIMPORTER_SSID] = $post['ssid'];
        }

        if (isset($post['is_success']) && !empty($post['is_success']))
        {
            $data['is_connected'] = 1;
        }

        if (isset($post['user_data']))
        {
            $data['user'] = json_decode($post['user_data'], 1);
            $data['connect_data'] = $post['ssid'];
        }

        $data['login_time'] = time();
        $data['expired_time'] = time() + 3600;

        /**
         * set to session.
         */
        $this -> setSession($data);

        // process user data key for each.
        return $this;
    }

    /**
     * check if method name is supported by this adapter.
     * @param string $methodName
     * @return bool
     */
    public function supportedMethod($methodName)
    {
        switch($methodName)
        {
            case 'albums' :
            case 'getAlbums' :
                return $this -> _hasGetAlbums;
            case 'getPhotos' :
            case 'photos' :
                return $this -> _hasGetPhotos;
            default :
                return method_exists($this, $methodName);
        }
        return 1;
    }

    /**
     * get remote auth url
     * @param string|bool $callback_url
     * @param array $params, external data, it is helpful to build callback
     * @return string
     */
    public function getAuthUrl($callback_url, $params = array())
    {
        $params['callback'] = $callback_url;
        $params['service'] = $this -> _serviceName;
        $params['platform'] = YNMEDIAIMPORTER_PLATFORM;
        $url = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/' . $this -> _serviceName . '/?' . http_build_query($params);
        return $url;
    }

    /**
     * @param array $params
     * @return objects
     */
    public function _getAlbums($params, $cache = 1)
    {
    	//echo "LONG1"; exit;
        $url = $this -> _urls['getAlbums'];
        return $this -> RPC($url, $params);
    }

    /**
     * @param array $params
     * @return objects
     */
    public function _getPhotos($params, $cache = 1)
    {
		$url = $this -> _urls['getPhotos'];
        return $this -> RPC($url, $params);
    }
    
	/**
     * @param array $params
     * @return objects
     */
    public function _getFavourites($params, $cache = 1)
    {
        $url = $this -> _urls['getFavourites'];
        return $this -> RPC($url, $params);
    }
    
    
    public function getUser()
    {
        if (null == $this -> _user)
        {
            $data = $this -> getSession();
            $this -> _user = $data['user'];
        }
        return $this -> _user;
    }

    /**
     * @return string
     */
    public function getUserDisplayname()
    {

        $user = $this -> getUser();
        return $user['name'];
    }

    public function getUserProfileUrl()
    {
        $user = $this -> getUser();
        return isset($user['link']) ? $user['link'] : '';
    }

    public function getUserAvatarUrl()
    {
        $user = $this -> getUser();
        return isset($user['picture']) ? $user['picture'] : '';
    }

    public function getUserSquareAvatarUrl()
    {
        $user = $this -> getUser();
        return isset($user['picture']) ? $user['picture'] : '';
    }

    public function getUserUId()
    {
        if (null == $this -> _uid)
        {
            $user = $this -> getUser();
            $this -> _uid = isset($user['uid']) ? $user['uid'] : null;
        }
        return $this -> _uid;

    }

    /**
     * @param array $item
     * @param int $userId current viewer id
     * @return hash string
     */
    public function createNodeId()
    {
        return md5(implode(func_get_args()), 0);
    }

    /**
     * get current viewer id
     * @return int
     */
    public function getCurrentViewerId()
    {
        if (null == $this -> _viewerId)
        {
            $this -> _viewerId = OW::getUser()->getId();
        }
        return $this -> _viewerId;
    }

    /**
     * create a key of cache from params
     * ksort for efficient cache engine
     * unset un neecessary value
     * @return string
     */
    public function createCacheKey($params = null)
    {
        if (isset($params['cache']))
        {
            unset($params['cache']);
        }

        $params = (array)$params;
        $params['cache_uid'] = $this -> getUserUId();
        $params['cache_sid'] = $this -> _serviceName;
        return sha1(implode(',', $params), 0);
    }

    /**
     * get cache core objects.
     */
    public function getCache()
    {
        return Ynmediaimporeter::getCache();
    }

    /**
     * load from cache
     */
    public function loadFromCache($key)
    {
    	//echo "stop 1"; exit;
        try
        {
            $ssid = session_id();
            if (Ynmediaimporter::getCache() ==  NULL)
            	return false;
            	
            //$data = Ynmediaimporter::getCache() -> load($ssid);
            $data = Ynmediaimporter::getCache()->load($ssid);

            if (false == $data)
            {
                return false;
            }

            if (!isset($data[$key]))
            {
                return false;
            }

            return $data[$key];

        }
        catch(Exception $e)
        {
            return false;
        }
    }

    /**
     * save to cache
     */
    public function saveToCache($data, $key)
    {
        try
        {
            $ssid = session_id();
            if (Ynmediaimporter::getCache() == NULL)
            	return false;
            	
            $cachedData = Ynmediaimporter::getCache() -> load($ssid);
            if (!$cachedData)
            {
                $cachedData = array();
            }
            $cachedData[$key] = $data;
            Ynmediaimporter::getCache() -> save($cachedData, $ssid);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    
	public function quote($str)
	{
		return "'" . $str . "'";
	}
	
    public function correctNodesStatus($nodes)
    {
        if (empty($nodes))
        {
            return array();
        }
        $id = implode(',', array_map(array(
            $this,
            'quote'
        ), array_keys($nodes)));
        
        $viewerId = intval(self::getViewerId());
        $sql = "select `nid`, `status` from `" . OW_DB_PREFIX . "ynmediaimporter_nodes` where `nid` IN ($id) and `user_id` = {$viewerId}";
        $result = array();
        foreach (OW::getDbo()->queryForList($sql) as $item){	
        	$result[$item['nid']] = $item['status'];
        }
        
        foreach ($result as $id => $status)
        {
            if (isset($nodes[$id]))
            {
                $nodes[$id]['status'] = $status;
            }
        }
        return $nodes;
    }

    /**
     * get albums of owner
     * return is list of album elements.
     * @see get data from..
     * @param array
     * @return array
     */
    public function getData($params = array(), $cache = true)
    {
        $media = isset($params['media']) ? $params['media'] : 'album';

        switch($media)
        {
            case 'photo' :
                return $this -> _getPhotos($params, $cache);
            case 'galleries' :
            case 'gallery' :
                return $this -> _getGalleries($params, $cache);
			case 'favourite' :
                return $this -> _getFavourites($params, $cache);
            case 'photoset' :
            case 'album' :
            case 'default' :
                return $this -> _getAlbums($params, $cache);
        }
    }

    static public function getViewerId()
    {
        if (null === self::$_viewerId)
        {
            self::$_viewerId = OW::getUser()->getId();
        }
        return self::$_viewerId;
    }
        
    /**
     * this method is helpful in some case.
     * 1. picasa
     * 2. flickr
     * 3. instagram 
     */
    public function getLogoutIframeUrl(){
        return '';
    }
}
