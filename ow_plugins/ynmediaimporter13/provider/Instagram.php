<?php

class Ynmediaimporter_Provider_Instagram extends Ynmediaimporeter_Provider_Abstract
{

    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @var string
     */
    protected $_serviceName = 'instagram';

    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @var string
     */
    protected $_hasGetAlbums = false;

    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @var string
     */
    protected function _init()
    {
        /**
         * set urls for connection.
         */
        $this -> _urls['host'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/instagram';
        $this -> _urls['login'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/instagram';
        $this -> _urls['logout'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/instagram/logout';
        $this -> _urls['getPhotos'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/instagram/photos';
        $this -> _urls['getAlbums'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/instagram/photos';
    }

    /**
     * @return string
     */
    public function getUserDisplayname()
    {

        $user = $this -> getUser();
        if (isset($user['full_name']) && !empty($user['full_name']))
        {
            return $user['full_name'];
        }

        if (isset($user['username']) && !empty($user['username']))
        {
            return $user['username'];
        }

        return $user['id'];

    }

    public function getUserProfileUrl()
    {
        return 'https://instagram.com/accounts/manage_access';
    }

    public function getUserAvatarUrl()
    {
        $user = $this -> getUser();
        return $user['profile_picture'];
    }

    public function getUserSquareAvatarUrl()
    {
        $user = $this -> getUser();
        return $user['profile_picture'];
    }

    public function getUserUid()
    {
        $user = $this -> getUser();
        return $user['id'];
    }

    public function _getAlbums($params, $cache = 1)
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
            'extra' => 'my',
            'limit' => YNMEDIAIMPORTER_PER_PAGE,
            'media' => 'photo',
            'uid' => $this -> getUserUId(),
            'aid' => '',
            'provider' => 'instagram',
        ), $params);

        if ($cache && ($data = $this -> loadFromCache($this ->createCacheKey($params))) !== false)
        {
	        return array(
		        $this -> correctNodesStatus($data['result']),
		        $data['params'],
		        $data['media'],
	        );
        }

        $url = $this -> _urls['getPhotos'];

        $params['album'] = $album = $params['aid'];
        $uid = isset($params['uid']) ? $params['uid'] : $this -> getUserUid();

        $response = $this -> RPC($url, $params);

        /* recorrec rows format*/
        $result = array();

        foreach ($response['data'] as $row)
        {
            $nodeId = $this -> createNodeId('instagram', 'photo', $row['id']);

            $result[$nodeId] = array(
                'nid' => $nodeId,
                'id' => $row['id'],
                'uid' => $row['user']['id'],
                'aid' => '',
                'photo_count' => 1,
                'media' => 'photo',
                'media_parent' => '',
                'provider' => 'instagram',
                'title' => $row['caption'],
                'src_thumb' => $row['images']['thumbnail']['url'],
                'src_small' => $row['images']['low_resolution']['url'],
                'src_medium' => $row['images']['standard_resolution']['url'],
                'src_big' => $row['images']['standard_resolution']['url'],
                'description' => '',
                'status' => 0,
            );
        }

        if (isset($response['pagination']['next_max_id']))
        {
            $params['max_id'] = $response['pagination']['next_max_id'];
        }

        unset($response);

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

    public function getLogoutIframeUrl()
    {
        return base64_encode('https://instagram.com/accounts/logout/');
    }

}
