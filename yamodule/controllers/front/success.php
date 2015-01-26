<?php
class yamodulesuccessModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public $module;

    public function initContent() 
    {
        parent::initContent();
		$log_on = Configuration::get('YA_ORG_LOGGING_ON');
        Tools::getValue('label') ? $data = explode('_', Tools::getValue('label')) : $data = explode('_', Tools::getValue('customerNumber'));
		if(!empty($data) && isset($data[1]))
		{
			$ordernumber = $data['1'];
			$this->context->smarty->assign('ordernumber', $ordernumber);
			$this->context->smarty->assign('time', date('Y-m-d H:i:s '));
			if (!$ordernumber)
			{
				if($log_on)
					$this->module->log_save('yakassa_success: Error '.$this->module->l('Cart number is not specified'));
				$this->setTemplate('error.tpl');
			}
			else
			{
				$cart = new Cart((int)$ordernumber);
				if (!Validate::isLoadedObject($cart))
				{
					if($log_on)
						$this->module->log_save('yakassa_success: Error '.$this->module->l('Shopping cart does not exist'));
					$this->setTemplate('error.tpl');
				}
				else
				{
					$ordernumber = Order::getOrderByCartId($cart->id);
					if (!$ordernumber)
					{
						if($log_on)
							$this->module->log_save('yakassa_success: Error '.$this->module->l('Order number is not specified'));
						$this->setTemplate('error.tpl');
					}
					else
					{
						$order = new Order((int)$ordernumber);
						$customer = new Customer((int)$order->id_customer);
						if ($order->hasBeenPaid())
						{
							if($log_on)
								$this->module->log_save('yakassa_success: #'.$order->id.' '.$this->module->l('Order paid'));
							Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)$order->id_cart.'&id_module='.(int)$this->module->id.'&id_order='.(int)$order->id);
						}
						else
						{
							if($log_on)
								$this->module->log_save('yakassa_success: #'.$order->id.' '.$this->module->l('Order wait payment'));
							$this->setTemplate('waitingPayment.tpl');
						}
					}
				}
			}
		}
		else
		{
			if($log_on)
				$this->module->log_save('yakassa_success: Error '.$this->module->l('Cart number is not specified'));
			$this->setTemplate('error.tpl');
		}
    }

}