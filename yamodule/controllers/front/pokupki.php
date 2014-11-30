<?php

include_once(dirname(__FILE__).'/../../classes/partner.php');
class YamodulepokupkiModuleFrontController extends ModuleFrontController
{
	public $display_header = false;
	public $display_column_left = false;
	public $display_column_right = false;
	public $display_footer = false;
	public $ssl = true;

	public function postProcess()
	{
		parent::postProcess();
		$type = Tools::getValue('type');
		$func = Tools::getValue('func');
		$arr = array($type, $func);
		$arr = array_merge($arr, $_REQUEST);
		$dd = serialize($arr);
		$this->module->log_save('pokupki '.$dd);
		$key = Tools::getValue('auth-token');
		$sign = Configuration::get('YA_POKUPKI_TOKEN');
		if (Tools::strtoupper($sign) != Tools::strtoupper($key))
		{
			header('HTTP/1.0 404 Not Found');
			echo '<h1>Wrong token</h1>';
			exit;
		}
		else
		{
			$json = file_get_contents("php://input");
			//$json = '{"cart":{"currency":"RUR","items":[{"feedId":383880,"offerId":"34c265","feedCategoryId":"3","offerName":"Dr.Web Антивирус","count":1}],"delivery":{"region":{"id":13,"name":"Тамбов","type":"CITY","parent":{"id":10802,"name":"Тамбовская область","type":"SUBJECT_FEDERATION","parent":{"id":3,"name":"Центральный федеральный округ","type":"COUNTRY_DISTRICT","parent":{"id":225,"name":"Россия","type":"COUNTRY"}}}}}}}';
			//res
			//$json = '{"order":{"id":169006,"fake":true,"currency":"RUR","paymentType":"POSTPAID","paymentMethod":"CASH_ON_DELIVERY","delivery":{"type":"PICKUP","price":0,"serviceName":"My carrier","id":"3","dates":{"fromDate":"18-11-2014","toDate":"18-11-2014"},"region":{"id":213,"name":"Москва","type":"CITY","parent":{"id":1,"name":"Москва и Московская область","type":"SUBJECT_FEDERATION","parent":{"id":3,"name":"Центральный федеральный округ","type":"COUNTRY_DISTRICT","parent":{"id":225,"name":"Россия","type":"COUNTRY"}}}},"outlet":{"id":999}},"items":[{"feedId":383880,"offerId":"34c259","feedCategoryId":"3","offerName":"Dr.Web Антивирус","price":2550,"count":2,"delivery":true}],"notes":"при"}}';
			//$json = '{"order":{"id":169006,"fake":true,"currency":"RUR","paymentType":"POSTPAID","paymentMethod":"CASH_ON_DELIVERY","status":"CANCELLED","substatus":"RESERVATION_EXPIRED","creationDate":"18-11-2014 17:04:09","itemsTotal":5100,"total":5100,"delivery":{"type":"PICKUP","price":0,"serviceName":"My carrier","id":"3","dates":{"fromDate":"18-11-2014","toDate":"18-11-2014"},"region":{"id":213,"name":"Москва","type":"CITY","parent":{"id":1,"name":"Москва и Московская область","type":"SUBJECT_FEDERATION","parent":{"id":3,"name":"Центральный федеральный округ","type":"COUNTRY_DISTRICT","parent":{"id":225,"name":"Россия","type":"COUNTRY"}}}},"outlet":{"id":999}},"items":[{"feedId":383880,"offerId":"34c259","feedCategoryId":"3","offerName":"Dr.Web Антивирус","price":2550,"count":2}],"notes":"при"}}';
			//war unpaid
			//$json = '{"order":{"id":169026,"fake":true,"currency":"RUR","paymentType":"PREPAID","paymentMethod":"YANDEX","delivery":{"type":"PICKUP","price":0,"serviceName":"My carrier","id":"3","dates":{"fromDate":"18-11-2014","toDate":"18-11-2014"},"region":{"id":213,"name":"Москва","type":"CITY","parent":{"id":1,"name":"Москва и Московская область","type":"SUBJECT_FEDERATION","parent":{"id":3,"name":"Центральный федеральный округ","type":"COUNTRY_DISTRICT","parent":{"id":225,"name":"Россия","type":"COUNTRY"}}}},"outlet":{"id":999}},"items":[{"feedId":383880,"offerId":"34c259","feedCategoryId":"3","offerName":"Dr.Web Антивирус","price":2550,"count":2,"delivery":true}],"notes":"при"}}';
			//$json = '{"order":{"id":169026,"fake":true,"currency":"RUR","paymentType":"PREPAID","paymentMethod":"YANDEX","status":"UNPAID","creationDate":"18-11-2014 17:04:56","itemsTotal":5100,"total":5100,"delivery":{"type":"PICKUP","price":0,"serviceName":"My carrier","id":"3","dates":{"fromDate":"18-11-2014","toDate":"18-11-2014"},"region":{"id":213,"name":"Москва","type":"CITY","parent":{"id":1,"name":"Москва и Московская область","type":"SUBJECT_FEDERATION","parent":{"id":3,"name":"Центральный федеральный округ","type":"COUNTRY_DISTRICT","parent":{"id":225,"name":"Россия","type":"COUNTRY"}}}},"outlet":{"id":999}},"items":[{"feedId":383880,"offerId":"34c259","feedCategoryId":"3","offerName":"Dr.Web Антивирус","price":2550,"count":2}],"notes":"при"}}';
			//$json = '{"order":{"id":169026,"fake":true,"currency":"RUR","paymentType":"PREPAID","paymentMethod":"YANDEX","status":"PROCESSING","creationDate":"18-11-2014 17:04:56","itemsTotal":5100,"total":5100,"delivery":{"type":"PICKUP","price":0,"serviceName":"My carrier","id":"3","dates":{"fromDate":"18-11-2014","toDate":"18-11-2014"},"region":{"id":213,"name":"Москва","type":"CITY","parent":{"id":1,"name":"Москва и Московская область","type":"SUBJECT_FEDERATION","parent":{"id":3,"name":"Центральный федеральный округ","type":"COUNTRY_DISTRICT","parent":{"id":225,"name":"Россия","type":"COUNTRY"}}}},"outlet":{"id":999}},"buyer":{"id":"rgBF2K6vX3uDcTYOFBLAqA==","lastName":"Zubakov","firstName":"Roman","phone":"9876543","email":"r.zubakov@yandex.ru"},"items":[{"feedId":383880,"offerId":"34c259","feedCategoryId":"3","offerName":"Dr.Web Антивирус","price":2550,"count":2}],"notes":"при"}}';
			$this->module->log_save('pokupki'.$json);
			if (!$json){
				header('HTTP/1.0 404 Not Found');
				echo '<h1>No data posted</h1>';
				exit;
			}
			else
			{
				header('Content-type:application/json;  charset=utf-8');
				$partner = new Partner();
				$data = Tools::jsonDecode($json);
				if($type == 'cart')
					$response = $partner->requestItems($data);
				elseif($type == 'order')
				{
					if($func == 'accept')
						$response = $partner->orderAccept($data);
					elseif($func == 'status')
						$partner->alertOrderStatus($data);
				}
				else
				{
					header('HTTP/1.0 404 Not Found');
					echo '<h1>Wrong controller</h1>';
					exit;
				}
			}
		}
	}
}