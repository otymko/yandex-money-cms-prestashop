<?php

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

$context = Context::getContext();
$action = Tools::getValue('action');
$add = (!strcmp($action, 'add') ? 1 : 0);
$id_product = (int)Tools::getValue('id_product');
$quantity = (int)Tools::getValue('quantity');
$id_product_attribute = (int)Tools::getValue('id_product_attribute');
$result = array();
$data = array();
$result['data'] = date('Y-m-d H:i:s');
$result['action'] = $action;
$product = new Product($id_product, false, $context->cookie->id_lang);
$result['name'] = $product->name;
$result['quantity'] = $quantity ? $quantity : 1;
$result['price'] = $product->price;
if($id_product_attribute)
{
	$combination = new Combination($id_product_attribute);
	$attributes = $combination->getAttributesName($context->cookie->id_lang);
	$name = array();
	foreach($attributes as $a)
	{
		$name[] = $a['name'];
	}
	
	$name = implode(' ', $name);
	$result['name'] = $result['name'].' '.$name;
	$result['price'] = $result['price'] + $combination->price;
}

$result['price'] = Tools::displayPrice($result['price']);
$data['params'] = $result;
$data['number'] = Configuration::get('YA_METRIKA_NUMBER');
die(Tools::jsonEncode($data));