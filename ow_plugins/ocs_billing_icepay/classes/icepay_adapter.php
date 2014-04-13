<?php

/**
 * EXHIBIT A. Common Public Attribution License Version 1.0
 * The contents of this file are subject to the Common Public Attribution License Version 1.0 (the "License");
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://opensource.org/licenses/CPAL-1.0. Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for the specific language
 * governing rights and limitations under the License.
 * The Initial Developer of the Original Code is Oxwall CandyStore (http://oxcandystore.com/).
 * All portions of the code written by Oxwall CandyStore are Copyright (c) 2013. All Rights Reserved.

 * EXHIBIT B. Attribution Information
 * Attribution Copyright Notice: Copyright 2013 Oxwall CandyStore. All rights reserved.
 * Attribution Phrase (not exceeding 10 words): Powered by Oxwall CandyStore
 * Attribution URL: http://oxcandystore.com/
 * Graphic Image as provided in the Covered Code.
 * Display of Attribution Information is required in Larger Works which are defined in the CPAL as a work
 * which combines Covered Code or portions thereof with code not governed by the terms of the CPAL.
 */

/**
 * ICEPAY billing gateway adapter class.
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_billing_icepay.classes
 * @since 1.5.1
 */
class OCSBILLINGICEPAY_CLASS_IcepayAdapter implements OW_BillingAdapter
{
    const GATEWAY_KEY = 'ocsbillingicepay';
    
    /**
     * @var BOL_BillingService
     */
    private $billingService;
    
    public function __construct ()
    {
        $this->billingService = BOL_BillingService::getInstance();
    }
    
    public function prepareSale (BOL_BillingSale $sale)
    {
        // ... gateway custom manipulations
        return $this->billingService->saveSale($sale);
    }
    
    public function verifySale (BOL_BillingSale $sale)
    {
        // ... gateway custom manipulations
        return $this->billingService->saveSale($sale);
    }
    
    /**
     * (non-PHPdoc)
     * @see ow_core/OW_BillingAdapter#getFields($params)
     */
    public function getFields ($params = null)
    {
        return array();
    }
    
    /**
     * (non-PHPdoc)
     * @see ow_core/OW_BillingAdapter#getOrderFormUrl()
     */
    public function getOrderFormUrl ()
    {
        return OW::getRouter()->urlForRoute('ocsbillingicepay.order_form');
    }
    
    /**
     * (non-PHPdoc)
     * @see ow_core/OW_BillingAdapter#getLogoUrl()
     */
    public function getLogoUrl ()
    {
        $plugin = OW::getPluginManager()->getPlugin('ocsbillingicepay');
        
        return $plugin->getStaticUrl() . 'img/icepay-logo.png';
    }
    
    public function detectCountry()
    {
        try
        {
            $sql = 'SELECT `cc2` FROM `' . BOL_GeolocationIpToCountryDao::getInstance()->getTableName() . '`
                WHERE inet_aton(:ip) >= ipFrom AND inet_aton(:ip) <= ipTo';

            return OW::getDbo()->queryForColumn($sql, array('ip' => OW::getRequest()->getRemoteAddress()));
        }
        catch ( Exception $e )
        {
            return null;
        }
    }
    
    public function getLanguageByCountry( $country = null )
    {
        if ( $country === null )
        {
            if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
            {
                return strtoupper(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
            }
            else
            {
                $country = '00';
            }
        }

        $langs = array(
            '00'  => 'EN', // default
            'AT' => 'DE', // Austria
            'AU' => 'EN', // Australia
            'BE' => 'NL', // Belgium
            'CA' => 'EN', // Canada
            'CH' => 'DE', // Switzerland
            'CZ' => 'CZ', // Czech Republic
            'DE' => 'DE', // Germany
            'ES' => 'ES', // Spain
            'FR' => 'FR', // France
            'GB' => 'EN', // United Kingdom
            'IT' => 'IT', // Italy
            'LU' => 'DE', // Luxembourg
            'NL' => 'NL', // Netherlands
            'PL' => 'PL', // Poland
            'PT' => 'PT', // Portugal
            'SK' => 'SK', // Slovakia
            'US' => 'EN'  // United States
        );
            
        return isset($langs[$country]) ? $langs[$country] : 'EN';
    }
}