<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Data Access Object for `vwvr_clip` table.  
 * 
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwvr.bol
 * @since 1.0
 * 
 */
class VWVR_BOL_ClipDao extends OW_BaseDao
{
    /**
     * @var BOL_UserDao
     */
    private $userDao;
    /**
     * Class instance
     *
     * @var VWVR_BOL_ClipDao
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return VWVR_BOL_ClipDao
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * Class constructor
     *
     */
    protected function __construct()
    {
        parent::__construct();
        $this->userDao = BOL_UserDao::getInstance();
    }

    /**
     * @see OW_BaseDao::getDtoClassName()
     *
     * @return string
     */
    public function getDtoClassName()
    {
        return 'VWVR_BOL_Clip';
    }

    /**
     * @see OW_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassNameUser()
    {
        return $this->userDao->getDtoClassName();
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     * @return string
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'vwvr_clip';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableNameUser()
    {
        return $this->userDao->getTableName();
    }

    /**
     * Get clips list (featured|latest|toprated)
     *
     * @param string $listtype
     * @param int $page
     * @param int $limit
     * @return array of VWVR_BOL_Clip
     */
    public function getClipsList( $listtype, $page, $limit )
    {
        $first = ($page - 1 ) * $limit;
        $this->deleteExpiredClip ();

        switch ( $listtype )
        {
            case 'latest':
                $example = new OW_Example();

                $example->andFieldEqual('status', 'approved');
                $example->andFieldEqual('privacy', 'everybody');
                $example->setOrder('`addDatetime` DESC');
                $example->setLimitClause($first, $limit);

                return $this->findListByExample($example);

                break;
        }
    }

    /**
     * Get user vwvr clips list
     *
     * @param int $userId
     * @param int $itemsNum
     * @param int $exclude 
     * @return array of VWVR_BOL_Clip
     */
    public function getUserClipsList( $userId, $page, $itemsNum, $exclude )
    {
        $first = ($page - 1 ) * $itemsNum;

        $example = new OW_Example();

        $example->andFieldEqual('status', 'approved');
        $example->andFieldEqual('userId', $userId);

        if ( $exclude )
        {
            $example->andFieldNotEqual('id', $exclude);
        }

        $example->setOrder('`addDatetime` DESC');
        $example->setLimitClause($first, $itemsNum);

        return $this->findListByExample($example);
    }

    /**
     * Counts clips
     *
     * @param string $listtype
     * @return int
     */
    public function countClips( $listtype )
    {
        switch ( $listtype )
        {
/**            case 'featured':
                $featuredDao = VWVR_BOL_ClipFeaturedDao::getInstance();

                $query = "
                    SELECT COUNT(`c`.`id`)       
                    FROM `" . $this->getTableName() . "` AS `c`
                    LEFT JOIN `" . $featuredDao->getTableName() . "` AS `f` ON ( `c`.`id` = `f`.`clipId` )
                    WHERE `c`.`status` = 'approved' AND `c`.`privacy` = 'everybody' AND `f`.`id` IS NOT NULL
                ";

                return $this->dbo->queryForColumn($query);

                break;
*/
            case 'latest':
                $example = new OW_Example();

                $example->andFieldEqual('status', 'approved');
                $example->andFieldEqual('privacy', 'everybody');

                return $this->countByExample($example);

                break;
        }
    }

    /**
     * Counts clips added by a user
     *
     * @param int $userId
     * @return int
     */
    public function countUserClips( $userId )
    {
        $example = new OW_Example();

        $example->andFieldEqual('userId', $userId);
        $example->andFieldEqual('status', 'approved');

        return $this->countByExample($example);
    }
    
    public function findByUserId( $userId )
    {
        $example = new OW_Example();

        $example->andFieldEqual('userId', $userId);

        return $this->findIdListByExample($example);
    }

    public function updatePrivacyByUserId( $userId, $privacy )
    {
        $sql = "UPDATE `".$this->getTableName()."` SET `privacy` = :privacy 
            WHERE `userId` = :userId";
        
        $this->dbo->query($sql, array('privacy' => $privacy, 'userId' => $userId));
    }

    /**
     * Get room id
     *
     * @param str roomname
     * @return int id
     */
    public function getIdByTitle( $title )
    {
        $ex = new OW_Example();
        $ex->andFieldEqual('title', $title);

        return $this->findIdByExample($ex);
    }

    /**
     * Get user Id
     *
     * @param str username
     * @return int id
     */
    public function getIdByUsername( $username )
    {

        $query = "SELECT `{$this->getTableNameUser()}`.`id` FROM `{$this->getTableNameUser()}` WHERE `{$this->getTableNameUser()}`.`username` = '{$username}'";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassNameUser());
    }

    public function deleteExpiredClip () {
      $config = OW::getConfig();
      $availability = $config->getValue('vwvr', 'availability');
      if ($availability != 0) {
          // get all clips
          $example = new OW_Example();
          $example->andFieldEqual('status', 'approved');
          $example->andFieldEqual('privacy', 'everybody');
          $example->setOrder('`addDatetime` DESC');
          $clips = $this->findListByExample($example);

          // if modifDatetime > $avTime, delete clip
          $avTime = $availability * 86400; // second
          $expTime = time () - $avTime;
          foreach ($clips as $clip) {
            if ($clip->modifDatetime < $expTime) {
              $id = $clip->id;
              $this->deleteById($id);
      
              BOL_CommentService::getInstance()->deleteEntityComments('vwvr_comments', $id);
              BOL_RateService::getInstance()->deleteEntityRates($id, 'vwvr_rates');
              BOL_TagService::getInstance()->deleteEntityTags($id, 'vwvr');
      
              BOL_FlagService::getInstance()->deleteByTypeAndEntityId('vwvr_clip', $id);
              
              OW::getEventManager()->trigger(new OW_Event('feed.delete_item', array(
                  'entityType' => 'vwvr_comments',
                  'entityId' => $id
              )));            
            }
          }
      }
    }

}
