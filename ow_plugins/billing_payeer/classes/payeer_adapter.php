<?php

/* No direct access */
defined('_OW_') or die('Restricted access');

      
class BILLINGPAYEER_CLASS_PayeerAdapter implements OW_BillingAdapter
{
    const GATEWAY_KEY = 'billingpayeer';

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
      
        return true; //$this->billingService->saveSale($sale);
    }

    public function getFields($params = null)
    {
        $sale = $params["sale"];
        $router = OW::getRouter();
        $ammount = number_format($sale->totalAmount,2, ".", "");
        $desc = base64_encode("Пополнение счета " . OW::getUser()->getEmail());
        $arHash = array(
            $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'm_shop'),
            $sale->id,
            $ammount,
            $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'm_curr'),
            $desc,
            $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'm_key')
        );
        
        $sign = strtoupper(hash('sha256',implode(':', $arHash)));
        
        return array(
            'm_shop' => $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'm_shop'),
            'm_orderid' => $sale->id,
            'm_amount' => $ammount,
			'm_curr' => $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'm_curr'),
            'm_desc' => $desc,
            'm_sign' => $sign,
			'lang' => $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'lang'),			
            'formActionUrl' => $this->getOrderFormActionUrl()
        );
    }

   
    public function getOrderFormUrl()
    {
        return OW::getRouter()->urlForRoute('billing_payeer_order_form');
    }

   
    public function getLogoUrl()
    {
        $plugin = OW::getPluginManager()->getPlugin('billingpayeer');

        return $plugin->getStaticUrl() . 'img/payeer_logo.png';
    }

    private function getOrderFormActionUrl()
    {
        return 'https://payeer.com/api/merchant/m.php';
    }

    public function isVerified()
    {

       $arHash = array(
           $_POST['m_operation_id'],
           $_POST['m_operation_ps'],
           $_POST['m_operation_date'],
           $_POST['m_operation_pay_date'],
           $_POST['m_shop'],
           $_POST['m_orderid'],
           $_POST['m_amount'],
           $_POST['m_curr'],
           $_POST['m_desc'],
           $_POST['m_status'],
           $this->billingService->getGatewayConfigValue(self::GATEWAY_KEY, 'm_key')
       );
       $sign_hash = strtoupper(hash('sha256', implode(":", $arHash)));
       return ($_POST["m_sign"] == $sign_hash) && ($_POST['m_status'] == "success");
    }
}
