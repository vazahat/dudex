<?php

class Ynmediaimporter_Provider_Link extends Ynmediaimporeter_Provider_Abstract
{
    /**
     * overwrite property from abstract class
     * @see Ynmediaimporeter_Provider_Abstract
     * @var string
     */
    protected $_serviceName = 'link';
    
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
        $this -> _urls['host'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/link';
        $this -> _urls['getPhotos'] = YNMEDIAIMPORTER_CENTRALIZE_HOST . '/link/photos';
    }
    
}
