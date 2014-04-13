<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class SPONSORS_BOL_SponsorDao extends OW_BaseDao {

    protected function __construct() {
        parent::__construct();
    }

    private static $classInstance;

    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function getDtoClassName() {
        return 'SPONSORS_BOL_Sponsor';
    }

    public function getTableName() {
        return OW_DB_PREFIX . 'sponsors_sponsor_details';
    }

    public function isMemberSponsor($userId) {
        $example = new OW_Example();
        $example->andFieldEqual('userId', $userId);
        $example->andFieldEqual('status', 1);
        if ($this->countByExample($example) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getSponsors($count, $forAdmin = 1, $checkValidty = 1) {
        $example = new OW_Example();

        if ($forAdmin != 1) {
            $example->andFieldEqual('status', 1);
        }

        $example->setOrder('price DESC,timestamp DESC');

        $sponsors = $this->findListByExample($example);
        $userService = BOL_UserService::getInstance();
        $allSponsors = array();
        $imagePath = OW::getPluginManager()->getPlugin('sponsors')->getUserFilesUrl();

        foreach ($sponsors as $sponsor) {
            $id = $sponsor->id;

            if ($checkValidty) {
                if ((time() - $sponsor->timestamp) / 86400 > $sponsor->validity) {
                    $this->disapprove($id);
                    continue;
                }
            }

            $allSponsors[$id]['id'] = $id;
            $allSponsors[$id]['name'] = $sponsor->name;
            $allSponsors[$id]['email'] = $sponsor->email;
            $allSponsors[$id]['website'] = $sponsor->website;
            $allSponsors[$id]['image'] = $imagePath . $sponsor->image;
            $allSponsors[$id]['status'] = $sponsor->status;
            $allSponsors[$id]['price'] = $sponsor->price;
            $allSponsors[$id]['userId'] = $sponsor->userId;
            $allSponsors[$id]['userName'] = $userService->getDisplayName($sponsor->userId);
            $allSponsors[$id]['userUrl'] = $userService->getUserUrl($sponsor->userId);
            $allSponsors[$id]['timestamp'] = $sponsor->timestamp;
            $allSponsors[$id]['validity'] = $sponsor->validity;

            if ($sponsor->status == 1)
                $allSponsors[$id]['activityUrl'] = OW::getRouter()->urlFor('SPONSORS_CTRL_Admin', 'disapprove', array('id' => $id));
            else
                $allSponsors[$id]['activityUrl'] = OW::getRouter()->urlFor('SPONSORS_CTRL_Admin', 'approve', array('id' => $id));

            $allSponsors[$id]['editUrl'] = OW::getRouter()->urlFor('SPONSORS_CTRL_Admin', 'edit', array('id' => $id));
            $allSponsors[$id]['deleteUrl'] = OW::getRouter()->urlFor('SPONSORS_CTRL_Admin', 'delete', array('id' => $id));
        }

        if ($count > 0) {
            $allSponsors = array_slice($allSponsors, 0, $count);
        }

        return $allSponsors;
    }

    public function findSponsorById($id) {
        return $this->findById($id);
    }

    public function delete($id) {
        $this->deleteById($id);
    }

    public function disapprove($id) {
        $sql = "UPDATE `" . $this->getTableName() . "` SET `status` = 0 WHERE `id` = :id";

        $this->dbo->query($sql, array('id' => $id));
    }

    public function approve($id) {
        $sql = "UPDATE `" . $this->getTableName() . "` SET `status` = 1 WHERE `id` = :id";

        $this->dbo->query($sql, array('id' => $id));
    }

}