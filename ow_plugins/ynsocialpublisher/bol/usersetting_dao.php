<?php

/**
 * Data Access Object for `usersetting` table.
 *
 * @author trunglt
 * @package ow.plugin.ynsocialpublisher.bol
 * @since 1.01
 */
class YNSOCIALPUBLISHER_BOL_UsersettingDao extends OW_BaseDao
{
    const OPTIONS_ASK = 0;
    const OPTIONS_AUTO = 1;
    const OPTIONS_NOT_ASK = 2;
    /**
     * Constructor.
     */
    protected function __construct()
    {
        parent::__construct();
    }
    /**
     * Singleton instance.
     * @var YNSOCIALPUBLISHER_BOL_UsersettingDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return YNSOCIALPUBLISHER_BOL_UsersettingDao
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /*
     * (non-PHPdoc) @see OW_BaseDao::getDtoClassName()
     */
    public function getDtoClassName()
    {
        return 'YNSOCIALPUBLISHER_BOL_Usersetting';
    }

    /*
     * (non-PHPdoc) @see OW_BaseDao::getTableName()
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'ynsocialpublisher_usersetting';
    }

    public function findByUserId( $userId, $cacheLifeTime = 0, $tags = array() )
    {
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE `userId` = ?';

        return $this->dbo->queryForObject($sql, $this->getDtoClassName(), array((int) $userId), $cacheLifeTime, $tags);
    }

    public function findUsersetting($userId, $key)
    {
        $example = new OW_Example();
        $example->andFieldEqual('userId', $userId);
        if (!empty($key))
        {
            $example->andFieldEqual('key', $key);
        }
        return $this->findObjectByExample($example);
    }

}