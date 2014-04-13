<?php

class BILLINGPAYEER_CTRL_Order extends OW_ActionController
{

    public function form()
    {
        $billingService = BOL_BillingService::getInstance();
        $adapter = new BILLINGPAYEER_CLASS_PayeerAdapter();
        $lang = OW::getLanguage();

        $sale = $billingService->getSessionSale();
        if ( !$sale )
        {
            $url = $billingService->getSessionBackUrl();
            if ( $url != null )
            {
                OW::getFeedback()->warning($lang->text('base', 'billing_order_canceled'));
                $billingService->unsetSessionBackUrl();
                $this->redirect($url);
            }
            else 
            {
                $this->redirect($billingService->getOrderFailedPageUrl());
            }
        }

        $formId = uniqid('order_form-');
        $this->assign('formId', $formId);

        $js = '$("#' . $formId . '").submit()';
        OW::getDocument()->addOnloadScript($js);


        if ( $billingService->prepareSale($adapter, $sale) )
        {
            $sale->totalAmount = floatval($sale->totalAmount);
            $this->assign('sale', $sale);

            $fields = $adapter->getFields(array("sale"=>$sale));
            $this->assign("formaction",$fields["formActionUrl"]);
            unset($fields["formActionUrl"]);
            $this->assign('fields', $fields);
            $this->assign('email', OW::getUser()->getEmail());
            
            
            $masterPageFileDir = OW::getThemeManager()->getMasterPageTemplate('blank');
            OW::getDocument()->getMasterPage()->setTemplate($masterPageFileDir);

            $billingService->unsetSessionSale();
        }
        else
        {
            $productAdapter = $billingService->getProductAdapter($sale->entityKey);

            if ( $productAdapter )
            {
                $productUrl = $productAdapter->getProductOrderUrl();
            }
            
            OW::getFeedback()->warning($lang->text('base', 'billing_order_init_failed'));
            $url = isset($productUrl) ? $productUrl : $billingService->getOrderFailedPageUrl();
            
            $this->redirect($url);
        }
    }

    public function notify()
    {
        if ( empty($_REQUEST['m_sign']) )
        {
            exit;
        }

        $hash = trim($_REQUEST['m_sign']);

        $m_orderid = trim($_REQUEST['m_orderid']);
        $m_status = 'success';

        $billingService = BOL_BillingService::getInstance();
        $adapter = new BILLINGPAYEER_CLASS_PayeerAdapter();

        if ( $adapter->isVerified() )
        {
            $sale = $billingService->getSaleById($m_orderid);

            
            if ( !$sale || !strlen($m_orderid) )
            {
                exit;
            }

            if ( $m_status == 'success' )
            {
                if ( !$billingService->saleDelivered($m_orderid, $sale->gatewayId) )
                {

                    $sale->m_orderid = $m_orderid;

                    
                    if ( $billingService->verifySale($adapter, $sale) )
                    {
 
                        $sale = $billingService->getSaleById($sale->id);
                                
                        $productAdapter = $billingService->getProductAdapter($sale->entityKey);

                        if ( $productAdapter )
                        {

                            $billingService->deliverSale($productAdapter, $sale);
							echo $_POST["m_orderid"] . '|success';
                            die;
                        }
                    }
                } 
                else 
                {
                    echo $_POST["m_orderid"] . '|success';
                    die;
			    }
            }
        }
        else
        {
            echo $_POST["m_orderid"] . '|error';
            die;
        }
    }

    public function completed()
    {
        $hash = $_REQUEST['m_sign'];

        $this->redirect(BOL_BillingService::getInstance()->getOrderCompletedPageUrl($hash));
    }
    
    public function canceled()
    {
        $this->redirect(BOL_BillingService::getInstance()->getOrderCancelledPageUrl());
    }
}
