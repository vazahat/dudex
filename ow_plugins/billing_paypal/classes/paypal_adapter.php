<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Paypal billing gateway adapter class.
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.ow_plugins.billing_paypal.classes
 * @since 1.0
 */
class BILLINGPAYPAL_CLASS_PaypalAdapter implements OW_BillingAdapter
{
    const GATEWAY_KEY = 'billingpaypal';

    /**
     * @var BOL_BillingService
     */
    private $billingService;

    public function __construct()
    {
        $this->billingService = BOL_BillingService::getInstance();
    }

    public function prepareSale( BOL_BillingSale $sale )
    {
        // ... gateway custom manipulations

        return $this->billingService->saveSale($sale);
    }

    public function verifySale( BOL_BillingSale $sale )
    {
        // ... gateway custom manipulations

        return $this->billingService->saveSale($sale);
    }

    /**
     * (non-PHPdoc)
     * @see ow_core/OW_BillingAdapter#getFields($params)
     */
    public function getFields( $params = null )
    {
        $router = OW::getRouter();

        return array(
            'return_url' => $router->urlForRoute('billing_paypal_completed'),
            'cancel_return_url' => $router->urlForRoute('billing_paypal_canceled'),
            'notify_url' => OW::getRouter()->urlForRoute('billing_paypal_notify'),
            'business' => $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'business'),
            'formActionUrl' => $this->getOrderFormActionUrl()
        );
    }

    /**
     * (non-PHPdoc)
     * @see ow_core/OW_BillingAdapter#getOrderFormUrl()
     */
    public function getOrderFormUrl()
    {
        return OW::getRouter()->urlForRoute('billing_paypal_order_form');
    }

    /**
     * (non-PHPdoc)
     * @see ow_core/OW_BillingAdapter#getLogoUrl()
     */
    public function getLogoUrl()
    {
        $plugin = OW::getPluginManager()->getPlugin('billingpaypal');

        return $plugin->getStaticUrl() . 'img/paypal_logo.gif';
    }

    /**
     * Returns Paypal gateway script url (sandbox or live)
     * 
     * @return string
     */
    private function getOrderFormActionUrl()
    {
        $sandboxMode = $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'sandboxMode');

        return $sandboxMode ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
    }

    /**
     * Posts data back to PayPal for order verification
     *  
     * @param array $post
     * @return boolean
     */
    public function isVerified( $post )
    {
        $sandboxMode = $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'sandboxMode');
        $hostname = $sandboxMode ? 'www.sandbox.paypal.com' : 'www.paypal.com';

        $nvpStr = '';
        foreach ( $post as $key => $value )
        {
            $value = urlencode(stripslashes($value));
            $nvpStr .= "$key=$value&";
        }
        $nvpStr .= 'cmd=_notify-validate';

        // post back to PayPal for validation
        $headers  = "POST /cgi-bin/webscr HTTP/1.1\r\n";
        $headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $headers .= "Content-Length: " . strlen($nvpStr) . "\r\n";
        $headers .= "Host: " . $hostname . "\r\n";
        $headers .= "Connection: close\r\n\r\n";
        $fp = fsockopen($hostname, 80, $errno, $errstr, 30);

        if ( !$fp )
        {
            return false;
        }

        fputs($fp, $headers . $nvpStr);

        $str = '';
        while ( !feof($fp) )
        {
            $str .= trim(fgets($fp, 2048));
        }
        fclose($fp);

        return $sandboxMode ? true : (mb_strstr($str, 'VERIFIED') !== false);
    }
}