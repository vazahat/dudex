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
class SPONSORS_CLASS_SponsorProductAdapter implements OW_BillingProductAdapter {

    const PRODUCT_KEY = 'sponsors_sponsor';
    const RETURN_ROUTE = 'sponsors_index';

    public function getProductKey() {
        return self::PRODUCT_KEY;
    }

    public function getProductOrderUrl() {
        return OW::getRouter()->urlForRoute(self::RETURN_ROUTE);
    }

    public function deliverSale(BOL_BillingSale $sale) {
        $extraData = $sale->getExtraData();

        $sponsor = new SPONSORS_BOL_Sponsor();
        $sponsor->name = $extraData->sponsorName;
        $sponsor->email = $extraData->sponsorEmail;
        $sponsor->website = $extraData->sponsorWebsite;
        $sponsor->image = $extraData->sponsorImage;
        $sponsor->price = $extraData->sponsorAmount;
        $sponsor->userId = $sale->userId;
        $sponsor->status = $extraData->status;
        $sponsor->validity = $extraData->validity;
        $sponsor->timestamp = time();

        if (SPONSORS_BOL_Service::getInstance()->addSponsor($sponsor)) {
            if ($extraData->status == '1')
                OW::getFeedback()->info(OW::getLanguage()->text('sponsors', 'sponsor_live_notification'));
            else
                OW::getFeedback()->info(OW::getLanguage()->text('sponsors', 'sponsor_live_notification_after_approval'));

            return true;
        }

        return false;
    }

}