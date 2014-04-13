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
class EVENTX_BOL_Event extends OW_Entity {

    public $title;
    public $location;
    public $description;
    public $createTimeStamp;
    public $startTimeStamp;
    public $endTimeStamp;
    public $userId;
    public $whoCanView;
    public $whoCanInvite;
    public $maxInvites = 0;
    public $status = 1;
    public $image = null;
    public $endDateFlag = false;
    public $startTimeDisabled = false;
    public $endTimeDisabled = false;
    public $importId = 0;
    public $importStatus = 0;

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation($location) {
        $this->location = $location;
    }

    public function getCreateTimeStamp() {
        return $this->createTimeStamp;
    }

    public function setCreateTimeStamp($createTimeStamp) {
        $this->createTimeStamp = $createTimeStamp;
    }

    public function getStartTimeStamp() {
        return $this->startTimeStamp;
    }

    public function setStartTimeStamp($startTimeStamp) {
        $this->startTimeStamp = $startTimeStamp;
    }

    public function getEndTimeStamp() {
        return $this->endTimeStamp;
    }

    public function setEndTimeStamp($endTimeStamp) {
        $this->endTimeStamp = $endTimeStamp;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function getWhoCanView() {
        return $this->whoCanView;
    }

    public function setWhoCanView($whoCanView) {
        $this->whoCanView = $whoCanView;
    }

    public function getWhoCanInvite() {
        return $this->whoCanInvite;
    }

    public function setWhoCanInvite($whoCanInvite) {
        $this->whoCanInvite = $whoCanInvite;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function getEndDateFlag() {
        return $this->endDateFlag;
    }

    public function setEndDateFlag($flag) {
        $this->endDateFlag = (boolean) $flag;
    }

    public function getStartTimeDisable() {
        return $this->startTimeDisabled;
    }

    public function setStartTimeDisable($flag) {
        $this->startTimeDisabled = (boolean) $flag;
    }

    public function getEndTimeDisable() {
        return $this->endTimeDisabled;
    }

    public function setEndTimeDisable($flag) {
        $this->endTimeDisabled = (boolean) $flag;
    }

    public function getMaxInvites() {
        return (int) $this->maxInvites;
    }

    public function setMaxInvites($maxInvites) {
        $this->maxInvites = $maxInvites;
    }

}
