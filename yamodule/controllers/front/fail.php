<?php
class yamodulefailModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
	{
		parent::initContent();

		$cart = $this->context->cart;
		if (!$this->module->checkCurrency($cart))
			Tools::redirect('index.php?controller=order');
 
		$this->context->smarty->assign(array(
			'this_path' => $this->module->getPathUri(),
			'this_path_bw' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
			'post' => (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) ? $_POST : array()
		));

		$this->setTemplate('payment_fail.tpl');
	}
}