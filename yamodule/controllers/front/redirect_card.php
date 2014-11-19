<?php
use \YandexMoney\Externalpayment;
use \YandexMoney\API;
include_once(dirname(__FILE__).'/../../lib/api.php');
include_once(dirname(__FILE__).'/../../lib/external_payment.php');
class yamoduleredirect_cardModuleFrontController extends ModuleFrontController
{
    public $display_header = true;
    public $display_column_left = true;
    public $display_column_right = false;
    public $display_footer = true;
    public $ssl = true;

    public function postProcess()
    {
        parent::postProcess();
		$this->log_on = Configuration::get('YA_P2P_LOGGING_ON');
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
                Tools::redirect('index.php?controller=order&step=1');

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
                Tools::redirect('index.php?controller=order&step=1');
                
        $this->myCart=$this->context->cart;
        $total_to_pay = $cart->getOrderTotal(true);
		$rub_currency_id = Currency::getIdByIsoCode('RUB');
		if($cart->id_currency != $rub_currency_id)
		{
			$from_currency = new Currency($cart->id_currency);
			$to_currency = new Currency($rub_currency_id);
			$total_to_pay = Tools::convertPriceFull($total_to_pay, $from_currency, $to_currency);
		}
        if ($total_to_pay > 0 && $total_to_pay < 1)
            $total_to_pay_limit = '1.00';
        else
            $total_to_pay_limit = number_format($total_to_pay, 2, '.', '');
        $total_to_pay = number_format($total_to_pay, 2, '.', '');
		$this->module->payment_status = false;
        $code = Tools::getValue('code');
        $cnf = Tools::getValue('cnf');
        if (empty($code))
			Tools::redirect('index.php?controller=order&step=3');
		elseif(!empty($code) && $cnf)
		{
			$comment = $message = $this->module->l('total:').$total_to_pay.$this->module->l(' rub');
			$response = ExternalPayment::getInstanceId(Configuration::get('YA_P2P_IDENTIFICATOR'));
			if($response->status == "success")
			{
				if($this->log_on)
					$this->module->log_save('card_redirect:  '.$this->module->l('get instance success'));
				$instance_id = $response->instance_id;
				$external_payment = new ExternalPayment($instance_id);
				$payment_options = array(
					"pattern_id" => "p2p",
					"to" => Configuration::get('YA_P2P_NUMBER'),
					"amount_due" => $total_to_pay,
					"comment" => trim($comment),
					"message" => trim($message),
					"label" => $this->context->cart->id,
					// "test_payment" => true,
					// "test_card" => 'available',
					// "test_result" => 'in_progress',
				);
				$response = $external_payment->request($payment_options);					
				if($response->status == "success")
				{
					if($this->log_on)
						$this->module->log_save('card_redirect:  '.$this->module->l('request success'));
					$request_id = $response->request_id;
					$this->context->cookie->ya_encrypt_CRequestId = urlencode(base64_encode($request_id));
					$this->context->cookie->write();
					
					do{
						$process_options = array(
							"request_id" => $request_id,
							'ext_auth_success_uri' => $this->context->link->getModuleLink('yamodule', 'payment_card', array(), true),
							// 'ext_auth_fail_uri' => $this->context->link->getPageLink('order', true, null, 'step=3')
							'ext_auth_fail_uri' => $this->context->link->getModuleLink('yamodule', 'payment_card', array(), true)
						);	
						
						$result = $external_payment->process($process_options);						
						if($result->status == "in_progress") {
							sleep(1);
						}
					}while ($result->status == "in_progress");
					if($result->status == 'ext_auth_required')
					{
						$url = sprintf("%s?%s", $result->acs_uri, http_build_query($result->acs_params));
						if($this->log_on)
							$this->module->log_save('card_redirect:  '.$this->module->l('redirect to').' '.$url);
						Tools::redirect($url , '');
						exit;
					}
					elseif($result->status == 'refused')
					{
						$this->errors[] = $this->module->descriptionError($resp->error) ? $this->module->descriptionError($resp->error) : $result->error;
						if($this->log_on)
							$this->module->log_save('card_redirect:refused '.$this->module->descriptionError($resp->error) ? $this->module->descriptionError($resp->error) : $result->error);
						$this->module->payment_status = 102;
					}
				}
			}
		}
    }
	
    public function initContent()
    {
        parent::initContent();
		$cart = $this->context->cart;
		$this->context->smarty->assign(array(
			'payment_link' => '',
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $this->module->getCurrency((int)$cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'this_path' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
		));

		$this->setTemplate('card.tpl');
    }   
}
