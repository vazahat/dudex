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
 * Data Access Object for `vwls_clip` table.  
 * 
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwls.bol
 * @since 1.0
 * 
 */
class VWLS_BOL_ClipDao extends OW_BaseDao
{
    /**
     * @var BOL_UserDao
     */
    private $userDao;
    /**
     * Class instance
     *
     * @var VWLS_BOL_ClipDao
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return VWLS_BOL_ClipDao
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
        return 'VWLS_BOL_Clip';
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
        return OW_DB_PREFIX . 'vwls_clip';
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
     * @return array of VWLS_BOL_Clip
     */
    public function getClipsList( $listtype, $page, $limit )
    {
        $first = ($page - 1 ) * $limit;
        $this->deleteExpiredClip ();

        switch ( $listtype )
        {
            case 'online':
             	$ztime = time();
	            $xtime=$ztime-30;

              $config = OW::getConfig();
              $baseSwfUrl = $config->getValue('vwls', 'baseSwf_url');
    					$url = $baseSwfUrl."ls_status.txt";
          		$onlineArr = array ();

              // inisialisasi CURL
              $cdata = curl_init(); 
              // setting CURL
              curl_setopt($cdata, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($cdata, CURLOPT_URL, $url);
              // read file
              $data = curl_exec($cdata);
              curl_close($cdata);
              $arrDatas = explode('|', $data);
              foreach ($arrDatas as $arrData) {
                $part = explode (':', $arrData);
                if ($part[0]>$xtime) {
                  $partTitle = $part[1];
                  $partUsername = $part[2];
                  $partId = $this->getIdByTitle($partTitle);
                  $partIdUserx = $this->getIdByUsername($partUsername);
                  if ($partIdUserx) $partIdUser = $partIdUserx[0]->id;
                    else {
                    $partIdUser = $this->getIdByRoomname($partUsername);
                    $partIdUser = $partIdUser[0]->userId;
                    }
                  array_push ($onlineArr, $partId.":".$part[2].":".$partIdUser);
                }
              }
              sort ($onlineArr);
          		array_push ($onlineArr, "end:end:end");
              $onlineArr = array_unique ($onlineArr);
              
              // create online user array
              $results = array ();
              $part = explode (':', $onlineArr[0]);
              $roomId = $part[0];
              $count = 0;
              $olUser = "";
              $olUsers = "";  // Id users
            	foreach($onlineArr as $arr) {
                $var1 = explode(":", $arr);
                if ($var1[0]==$roomId) {
                  if ($count<5) {
                    if ($count==0) $olUser = $var1[1]; 
                    else $olUser .= ', '.$var1[1];
                  }
                  $olUsers .= $var1[2]."|";
                  $count ++;
                } else {
                  array_push ($results, $roomId.":".$count.":".$olUser.":".$olUsers); 
                  $roomId = $var1[0];
                  $olUser = $var1[1];
                  $olUsers = $var1[2];
                  $count = 1;
                }
              }


/**              if ($countOA > 2) {
                $part = explode (':', $onlineArr[0]);
                $roomId = $part[0];
                $roomUser = $part[1];
                $count = 0;
                $olUser = "";
                for ($i=1; $i<($countOA-1); $i++) {
                  $part = explode (':', $onlineArr[$i]);
                  if ($part[0] == $roomId) {
                    $olUser .= ":".$part[1];
                  } else array_push ($result, $part[0].$olUser);
                }
              } elseif ($countOA == 2) $result[0] = $roomId.":".$roomUser;
*/
/**		          while(false !== ($file = readdir($handle))){
                $fileAd = $folder.$file.".txt";
                if (date(filemtime($fileAd))>$xtime) {
                  // get room id
                  $roomname = $file;
                  $id = getIdByTitle( $roomname );

                  // get username of online room
                  $onlineUserArr0 = array ();
                  $file_handle = fopen($fileAd, "rb");
                  while (!feof($file_handle) ) {
                    $line_of_text = fgets($file_handle);
                    $parts = explode(':', $line_of_text);
                      if ($parts[0]>$xtime) {
                        array_push ($onlineUserArr0, $parts[1]);
                      }
                    $onlineUserArr = array_unique($onlineUserArr0);
                    $countOu = count ($onlineUserArr);
                    $onlineUser = "";
                    if ($countOu==1) $onlineUser = $onlineUserArr[0];
                    else if ($countOu<5) {
                      for ($i=0; $i<($countOu-2); $i++) {
                        $onlineUser .= $onlineUserArr [$i].", ";
                      }
                      $onlineUser .= $onlineUserArr [$countOu-1];
                    } else {
                      for ($i=0; $i<4; $i++) {
                        $onlineUser .= $onlineUserArr [$i].", ";
                      }
                      $onlineUser .= "+";
                    }
                  }
                  fclose($file_handle);

                  // add "room_id:onlineuser:count"
                  array_push ($onlineArr, $id.":".$onlineUser.":".$countOu);

                }
                else  unlink ($folder."".$file.".txt");
           } 
*/                 closedir ($folder);  
                
                foreach ($results as $result) {
                  $partResult = explode (":", $result);
                  $updatamodifDatetime = $this->updatemodifDatetimeById ($partResult[0], $ztime);
                  $updateOL = $this->updateOnlineById( $partResult[0], "yes" );
                  $updateOLC = $this->updateOnlineCountById( $partResult[0], $partResult[1] );
                  $updateOLU = $this->updateOnlineUserById( $partResult[0], $partResult[2] );
                  $updateOLUS = $this->updateOnlineUsersById( $partResult[0], $partResult[3] );
                }

                $example = new OW_Example();

                $example->andFieldEqual('online', 'yes');
                $example->andFieldEqual('privacy', 'everybody');
                $example->setOrder('`addDatetime` DESC');
                $example->setLimitClause($first, $limit);

                $clipsTmp = $this->findListByExample($example);
                
                foreach ($clipsTmp as $clipTmp) {
                  if ($clipTmp->modifDatetime < $xtime) {
                    $id = $clipTmp->id;
                    $updateOL = $this->updateOnlineById( $id, "no" );
                    $updateOLU = $this->updateOnlineUserById( $id, "0" );                    
                    $updateOLUS = $this->updateOnlineUsersById( $id, "0" );
                  }
                }

                return $this->findListByExample($example);

                break;
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
     * Get user vwls clips list
     *
     * @param int $userId
     * @param int $itemsNum
     * @param int $exclude 
     * @return array of VWLS_BOL_Clip
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
                $featuredDao = VWLS_BOL_ClipFeaturedDao::getInstance();

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

    // online user
    public function updatemodifDatetimeById( $roomId, $modifDatetime )
    {
        $sql = "UPDATE `".$this->getTableName()."` SET `modifDatetime` = :modifDatetime 
            WHERE `id` = :roomId";
        
        $this->dbo->query($sql, array('modifDatetime' => $modifDatetime, 'roomId' => $roomId));
    }

    public function updateOnlineById( $roomId, $online, $onlineUser )
    {
        $sql = "UPDATE `".$this->getTableName()."` SET `online` = :online 
            WHERE `id` = :roomId";
        
        $this->dbo->query($sql, array('online' => $online, 'roomId' => $roomId));
    }

    public function updateOnlineCountById( $roomId, $onlineCount )
    {
        $sql = "UPDATE `".$this->getTableName()."` SET `onlineCount` = :onlineCount 
            WHERE `id` = :roomId";
        
        $this->dbo->query($sql, array('onlineCount' => $onlineCount, 'roomId' => $roomId));
    }

    public function updateOnlineUserById( $roomId, $onlineUser )
    {
        $sql = "UPDATE `".$this->getTableName()."` SET `onlineUser` = :onlineUser 
            WHERE `id` = :roomId";
        
        $this->dbo->query($sql, array('onlineUser' => $onlineUser, 'roomId' => $roomId));
    }

    public function updateOnlineUsersById( $roomId, $onlineUsers )
    {
        $sql = "UPDATE `".$this->getTableName()."` SET `onlineUsers` = :onlineUsers 
            WHERE `id` = :roomId";
        
        $this->dbo->query($sql, array('onlineUsers' => $onlineUsers, 'roomId' => $roomId));
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

    /**
     * Get user Id
     *
     * @param str roomname
     * @return int id
     */
    public function getIdByRoomname( $roomname )
    {

        $sql = "SELECT `".$this->getTableName()."`.`userId` FROM `".$this->getTableName()."` 
            WHERE `title` = '{$roomname}'";
        return $this->dbo->queryForObjectList($sql, $this->getDtoClassNameUser());
//        return $this->dbo->query($sql, array('roomname' => $roomname));

    }

    public function deleteExpiredClip () {
      $config = OW::getConfig();
      $availability = $config->getValue('vwls', 'availability');
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
      
              BOL_CommentService::getInstance()->deleteEntityComments('vwls_comments', $id);
              BOL_RateService::getInstance()->deleteEntityRates($id, 'vwls_rates');
              BOL_TagService::getInstance()->deleteEntityTags($id, 'vwls');
      
              BOL_FlagService::getInstance()->deleteByTypeAndEntityId('vwls_clip', $id);
              
              OW::getEventManager()->trigger(new OW_Event('feed.delete_item', array(
                  'entityType' => 'vwls_comments',
                  'entityId' => $id
              )));            
            }
          }
      }
    }

}
