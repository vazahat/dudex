<?php

/**
 * Social Publisher Service Class.
 *
 * @author trunglt
 * @package ow.plugin.ynsocialpublisher.bol
 * @since 1.01
 */
class YNSOCIALPUBLISHER_BOL_Service
{
    /**
     * @var YNSOCIALPUBLISHER_BOL_UsersettingDao
     */
    private $_usersettingDao;

    /**
     * Singleton instance.
     *
     * @var YNSOCIALPUBLISHER_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return YNSOCIALPUBLISHER_BOL_Service
     */
    public static function getInstance()
    {
        if (self::$classInstance === null)
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct()
    {
        $this->_usersettingDao = YNSOCIALPUBLISHER_BOL_UsersettingDao::getInstance();
    }

    public function getEnabledPlugins()
    {
        $adminConfigs = OW::getConfig()->getValues('ynsocialpublisher');

        foreach ($adminConfigs as $key => $values)
        {
            $isPluginActive = OW::getPluginManager()->isPluginActive($key);
            if (!$isPluginActive)
            {
                unset($adminConfigs[$key]);
            }
            else {
                $adminConfigs[$key] = json_decode($values, true);
            }
        }

        return $adminConfigs;
    }

    public function getUsersetting($userId, $key)
    {
        // user setting
        $userSetting = $this->findByUserIdOrKey($userId, $key);
        // admin setting
        $adminConfigs = json_decode(OW::getConfig()->getValue('ynsocialpublisher', $key), true);
        // return setting
        $setting = array();
        // if do not find this setting in user, use setting in admin
        if (!empty($adminConfigs['active']) && OW::getPluginManager()->isPluginActive($key))
        {
            $setting['userId'] = $userId;
            $setting['key'] = $key;
            $setting['providers'] = array();
            $setting['adminProviders'] = $adminConfigs['providers'];
            if (!$userSetting)
            {
                //$setting['option'] = YNSOCIALPUBLISHER_BOL_UsersettingDao::OPTIONS_NOT_ASK;
                $setting['option'] = YNSOCIALPUBLISHER_BOL_UsersettingDao::OPTIONS_ASK;
                // providers
                $setting['providers'] = $adminConfigs['providers'];
            }
            else
            {
                $setting['option'] = $userSetting->option;
                // providers
                $setting['providers'] = array_intersect(json_decode($userSetting->providers,true), $adminConfigs['providers']);
            }
        }

       return $setting;
    }

    /**
     * Saves or updates post
     *
     * @param YNSOCIALPUBLISHER_BOL_Usersetting $usersettingDto
     */
    public function saveOrUpdateUsersetting( $usersettingDto )
    {
        $this->_usersettingDao->save($usersettingDto);
    }

    public function findByUserIdOrKey($userId, $key = '')
    {
        return $this->_usersettingDao->findUsersetting($userId, $key);
    }

}