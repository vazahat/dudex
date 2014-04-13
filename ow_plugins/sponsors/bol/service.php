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
class SPONSORS_BOL_Service {

    private static $classInstance;
    private $dao;

    public static function getInstance() {
        if (self::$classInstance === null) {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    private function __construct() {
        $this->dao = SPONSORS_BOL_SponsorDao::getInstance();
    }

    public function addSponsor($sponsor) {
        $this->dao->save($sponsor);

        if ($sponsor->id)
            return true;
        else
            return false;
    }

    public function getSponsors($count = 0, $forAdmin = 1, $checkValidty = 1) {
        return $this->dao->getSponsors($count, $forAdmin, $checkValidty);
    }

    public function deleteSponsor($id) {
        $this->dao->deleteById($id);
    }

    public function isMemberSponsor($userId) {
        return $this->dao->isMemberSponsor($userId);
    }

    public function delete($id) {
        $this->dao->deleteById($id);
    }

    public function disapprove($id) {
        $this->dao->disapprove($id);
    }

    public function approve($id) {
        $this->dao->approve($id);
    }

    public function findSponsorById($id) {
        return $this->dao->findSponsorById($id);
    }

    public function sendExpiryEmail() {
        $config = OW::getConfig();
        $subject = OW::getLanguage()->text('sponsors', 'reminder_subject');
        $content = OW::getLanguage()->text('sponsors', 'reminder_content');
        $sitemail = $config->getValue('base', 'site_email');
        $sitename = $config->getValue('base', 'site_name');
        $mails = array();

        $example = new OW_Example();
        $example->andFieldEqual('status', 1);
        $example->andFieldGreaterThan('price', 0);

        $sponsors = $this->dao->findListByExample($example);

        foreach ($sponsors as $sponsor) {
            $cutoffDay = $sponsor->validity - (int) OW::getConfig()->getValue('sponsors', 'cutoffDay');

            if (((time() - $sponsor->timestamp) / 86400 > $cutoffDay) && $cutoffDay > 0) {
                $mail = OW::getMailer()->createMail();

                $mail->addRecipientEmail($sponsor->email);
                $mail->setSender($sitemail, $sitename);
                $mail->setSubject($subject);
                $mail->setHtmlContent($content);

                $textContent = strip_tags(preg_replace("/\<br\s*[\/]?\s*\>/", "\n", $content));
                $mail->setTextContent($textContent);

                $mails[] = $mail;
            }
        }

        if (count($mails) > 0) {
            OW::getMailer()->addListToQueue($mails);
        }
    }

}
