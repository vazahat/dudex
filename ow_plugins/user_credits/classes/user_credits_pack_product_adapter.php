<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * User credits product adapter class.
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.ow_plugins.user_credits.classes
 * @since 1.0
 */
class USERCREDITS_CLASS_UserCreditsPackProductAdapter implements OW_BillingProductAdapter
{
    const PRODUCT_KEY = 'user_credits_pack';

    const RETURN_ROUTE = 'usercredits.buy_credits';

    public function getProductKey()
    {
        return self::PRODUCT_KEY;
    }

    public function getProductOrderUrl()
    {
        return OW::getRouter()->urlForRoute(self::RETURN_ROUTE);
    }

    public function deliverSale( BOL_BillingSale $sale )
    {
        $packId = $sale->entityId;
        
        $creditsService = USERCREDITS_BOL_CreditsService::getInstance();
        
        $pack = $creditsService->findPackById($packId);
        
        if ( !$pack )
        {
            return false;
        }
        
        if ( $creditsService->increaseBalance($sale->userId, $pack->credits) )
        {
            $creditsService->logAction(0, $sale->userId, $pack->credits);
            
            return true;
        }
        
        return false;
    }
}