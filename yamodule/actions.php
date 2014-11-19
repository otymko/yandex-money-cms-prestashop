<?php
/**
 * @category  Front Office Features
 * @author    Maxim Bespechalnih <2343319@gmail.com>
 * @copyright Maxim Bespechalnih 2014
*/

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/yamodule.php');
$yamodule = new yamodule();
$return = array();
if (Tools::GetValue('action'))
{
	$action = Tools::GetValue('action');
	$token = Tools::encrypt('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)Tools::getValue('idm'));
	if (strcasecmp($token, Tools::getValue('tkn')) == 0){
		switch ($action)
		{
			case 'load_price':
				$return = $yamodule->processLoadPrice();
				break;
			case 'change_order':
				$return = $yamodule->processChangeCarrier();
				break;
		}
	}
	else
	{
		$return['errors'] = $yamodule->l('Invalid Security Token !');
	}
	die(Tools::jsonEncode($return));
}