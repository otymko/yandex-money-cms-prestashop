<?php
use \YandexMoney\Externalpayment;
use \YandexMoney\API;
include_once(dirname(__FILE__).'/../../lib/api.php');
include_once(dirname(__FILE__).'/../../lib/external_payment.php');
class yamoduleredirect_walletModuleFrontController extends ModuleFrontController
{
    public $display_header = true;
    public $display_column_left = true;
    public $display_column_right = false;
    public $display_footer = true;
    public $ssl = true;
    public $error;

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
		if($cart->id_currency != $rub_currency_id){
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
        if (empty($code)){
			Tools::redirect('index.php?controller=order&step=3');
        }elseif(!empty($code) && $cnf){
			$comment = $message = $this->module->l('total:').$total_to_pay.$this->module->l(' rub');
			$response = API::getAccessToken(Configuration::get('YA_P2P_IDENTIFICATOR'), $code, $this->context->link->getModuleLink('yamodule', 'redirect', array(), true), Configuration::get('YA_P2P_KEY'));
			$token = $response->access_token;
			if($token == ''){
				$scope = array(
					"payment.to-account(\"".Configuration::get('YA_P2P_NUMBER')."\",\"account\").limit(,".$total_to_pay_limit.")",
					"money-source(\"wallet\",\"card\")"
				);
				
				if($this->log_on)
					$this->module->log_save('wallet_redirect: '.$this->module->l('Type wallet'));
				$auth_url = API::buildObtainTokenUrl(Configuration::get('YA_P2P_IDENTIFICATOR'), $this->context->link->getModuleLink('yamodule', 'redirect_wallet', array(), true), $scope);
				if($this->log_on)
					$this->module->log_save('wallet_redirect: url = '.$auth_url);
				Tools::redirect($auth_url , '');
			}
			
			$api = new API($token);
			$rarray = array(
				'pattern_id' => 'p2p',
				'to' => Configuration::get('YA_P2P_NUMBER'),
				'amount_due' => $total_to_pay,
				'comment' => trim($comment),
				'message' => trim($message),
				'label' => $this->context->cart->id,
			);

			$request_payment = $api->requestPayment($rarray);
			switch($request_payment->status){
				case 'success':
					if($this->log_on)
						$this->module->log_save('wallet_redirect: '.$this->module->l('request success'));
					$this->context->cookie->ya_encrypt_token = urlencode(base64_encode($token));
					$this->context->cookie->ya_encrypt_RequestId = urlencode(base64_encode($request_payment->request_id));
					$this->context->cookie->write();
					$this->module->payment_link = $this->context->link->getModuleLink('yamodule', 'redirect', array(), true);
					do{
						$process_payment = $api->processPayment(array(
							"request_id" => $request_payment->request_id,
						));						
						if($process_payment->status == "in_progress") {
							sleep(1);
						}
					}while ($process_payment->status == "in_progress");
										
					$this->updateStatus($process_payment);
					$this->error = false;
					break;
				case 'refused':
					if($this->log_on)
						$this->module->log_save('wallet_redirect: '.$this->module->l('request refused'));
					$this->errors[] = $this->module->descriptionError($request_payment->error);
					if($this->log_on)
						$this->module->log_save('wallet_redirect: refused '.$this->module->descriptionError($request_payment->error));
						$this->error = true;
					break;
				case 'hold_for_pickup':
					if($this->log_on)
						$this->module->log_save('wallet_redirect: '.$this->module->l('hold_for_pickup'));
					$this->errors[] = $this->module->l('Получатель перевода не найден, будет отправлен перевод до востребования. Успешное выполнение.');
					if($this->log_on)
						$this->module->log_save('wallet_redirect: hold_for_pickup '.$this->module->l('Получатель перевода не найден, будет отправлен перевод до востребования. Успешное выполнение.'));
						$this->error = true;
					break;
					
			}		
		}
    }
    
	public function updateStatus(&$resp)
	{
		if ($resp->status == 'success')
		{
			$this->module->validateOrder((int)$this->context->cart->id, Configuration::get('PS_OS_PAYMENT'), $this->context->cart->getOrderTotal(true, Cart::BOTH), 'Yandex.Деньги', NULL, array(), NULL, false, $this->context->cart->secure_key);
			$this->context->cookie->ya_encrypt_token = '';
			$this->context->cookie->ya_encrypt_RequestId = '';
			$this->context->cookie->write();
			if($this->log_on)
				$this->module->log_save('wallet_redirect: #'.$this->module->currentOrder.' '.$this->module->l('Order success'));
			Tools::redirect($this->context->link->getPageLink('order-confirmation').'&id_cart='.$this->context->cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$this->context->cart->secure_key);
			
		}
		else
		{ 
			$this->errors[] = $this->module->descriptionError($resp->error);
			if($this->log_on)
				$this->module->log_save('wallet_redirect: Error '.$this->module->descriptionError($resp->error));
			$this->module->payment_status = 102;
			$this->error = true;
		}
    }
	
    public function initContent()
    {
        parent::initContent();
		$cart = $this->context->cart;
		$this->context->smarty->assign(array(
			'payment_link' => '',
			'card_allowed' => 1,
			'wallet_alowed' => 1,
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $this->module->getCurrency((int)$cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'this_path' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
			'errors' => $this->errors
		));

		if($this->error)
			$this->setTemplate('wallet_error.tpl');
		else
			$this->setTemplate('wallet.tpl');
    }   
}
