<?php
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/yamodule.php');
require_once(dirname(__FILE__) . '/classes/callback.php');
$yamodule = new yamodule();
$m = new Metrika();
$code = Tools::getValue('code');
$error = Tools::getValue('error');
$state = Tools::getValue('state');
$response = $m->run();
if($error == ''){
	$state = explode('_', base64_decode($state));
	$type = $state[2];
	$m->code = $code;
	$m->getToken($type);
	Tools::redirect(_PS_BASE_URL_.__PS_BASE_URI__.$state[0].'/'.Context::getContext()->link->getAdminLink('AdminModules', false).($m->errors ? '&error='.base64_encode($m->errors) : '').'&configure=yamodule&tab_module=payments_gateways&module_name=yamodule&token='.Tools::getAdminToken('AdminModules'.(int)Tab::getIdFromClassName('AdminModules').(int)$state[1]));
}
else
	die('error #'.$error.' error description: '.Tools::getValue('error_description'));
?>