<?php

class metrika {
	public $url = 'https://oauth.yandex.ru/';
	public $url_api = 'https://api-metrika.yandex.ru/management/v1/';
	public $client_id;
	public $state;
	public $errors;
	public $number;
	public $client_secret;
	public $code;
	public $token;
	public $context;
	
	public function __construct()
	{
		$this->context = Context::getContext();
		$this->client_id = Configuration::get('YA_METRIKA_ID_APPLICATION');
		$this->number = Configuration::get('YA_METRIKA_NUMBER');
		$this->client_secret = Configuration::get('YA_METRIKA_PASSWORD_APPLICATION');
		$this->state = 'Test_1';
		$this->token = Configuration::get('YA_METRIKA_TOKEN') ? Configuration::get('YA_METRIKA_TOKEN') : '';
		$this->module = Module::getInstanceByName('yamodule');
	}
	
	public function run()
	{
		$this->code = Tools::getValue('code');
		$error = Tools::getValue('error');
		if($error == ''){
			if(empty($this->token)){
				$this->errors = 'Пустой Токен!';
				return false;
				if(empty($this->code))
					$this->getCode();
				// elseif(!empty($this->code) && empty($this->token))
					// $this->getToken();
			}
			else
				return true;
		}
		else
		{
			$this->errors = 'error #'.$error.' error description: '.Tools::getValue('error_description');
			return false;
		}
	}
	
	public function getToken($type = 'def')
	{
		$params = array(
			'grant_type' => 'authorization_code',
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
			'code' => $this->code
		);
		$response = $this->post($this->url.'token', array(), $params, 'POST');
		$data = Tools::jsonDecode($response->body);
		if($response->status_code == 200){
			$this->token = $data->access_token;
			if($type == 'metrika')
				Configuration::updateValue('YA_METRIKA_TOKEN', $this->token);
			elseif($type == 'pokupki')
				Configuration::updateValue('YA_POKUPKI_YATOKEN', $this->token);
		}else{
			$this->errors = 'error #'.$response->status_code.' error description: '.$data->error_description.' '.$data->error;
		}
	}
	
	public function getCode()
	{
		$params = array(
			'response_type' => 'code',
			'client_id' => $this->client_id,
			'state' => $this->state
		);
		$params = http_build_query($params);
		Tools::redirect($this->url.'authorize?'.$params);
	}
	
	// Все счётчики
	public function getAllCounters()
	{
		return $this->SendResponse('counters', array(), array(), 'GET');
	}
	
	// Конкретный счётчик
	public function getCounter()
	{
		return $this->SendResponse('counter/'.$this->number, array(), array(), 'GET');
	}
	
	// Проверка кода счётчика
	public function getCounterCheck()
	{
		return $this->SendResponse('counter/'.$this->number.'/check', array(), array(), 'GET');
	}
	
	// Все цели счётчика
	public function getCounterGoals()
	{
		return $this->SendResponse('counter/'.$this->number.'/goals', array(), array(), 'GET');
	}
	
	// Конкретная цель
	public function getCounterGoal($goal)
	{
		return $this->SendResponse('counter/'.$this->number.'/goal/'.$goal, array(), array(), 'GET');
	}
	
	// Добавление цели
	public function addCounterGoal($params)
	{
		return $this->SendResponse('counter/'.$this->number.'/goals', array(), $params, 'POSTJSON');
	}
	
	// Удаление цели
	public function deleteCounterGoal($goal)
	{
		return $this->SendResponse('counter/'.$this->number.'/goal/'.$goal, array(), array(), 'DELETE');
	}
	
	// Редактирование счётчика
	public function editCounter()
	{
		$params = array('counter'=>array(
			'goals_remove' => 0,
			'code_options' => array(
				'clickmap' => (string) Configuration::get('YA_METRIKA_SET_CLICKMAP'),
				'external_links' => (string) Configuration::get('YA_METRIKA_SET_OUTLINK'),
				'visor' => (string) Configuration::get('YA_METRIKA_SET_WEBVIZOR'),
				'denial' => (string) Configuration::get('YA_METRIKA_SET_OTKAZI'),
				'track_hash' => (string) Configuration::get('YA_METRIKA_SET_HASH'),
			)
		));
		if(count($params)){
			return $this->SendResponse('counter/'.$this->number, array(), $params, 'PUT');
		}
	}
	
	public function SendResponse($to, $headers, $params, $type, $pretty = 1)
	{
		$response = $this->post($this->url_api.$to.'?pretty='.$pretty.'&oauth_token='.$this->token, $headers, $params, $type);
		$data = Tools::jsonDecode($response->body);
		if($response->status_code == 200){
			return $data; 
		}else{
			$this->module->log_save($response->body);
		}
	}
	
	public static function post($url, $headers, $params, $type){
		$curlOpt = array(
			CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLINFO_HEADER_OUT => 1,
			CURLOPT_USERAGENT => 'php-market',
        );
		
		switch (strtoupper($type)){
            case 'DELETE':
                $curlOpt[CURLOPT_CUSTOMREQUEST] = "DELETE";
            case 'GET':
                if (!empty($params))
                    $url .= (strpos($url, '?')===false ? '?' : '&') . http_build_query($params);
            break;
            case 'PUT':
				$headers[] = 'Content-Type: application/x-yametrika+json';
                $body = Tools::jsonEncode($params);
                $fp = fopen('php://temp/maxmemory:256000', 'w');
                if (!$fp)
                    throw new YandexApiException('Could not open temp memory data');
                fwrite($fp, $body);
                fseek($fp, 0);
                $curlOpt[CURLOPT_PUT] = 1;
                $curlOpt[CURLOPT_BINARYTRANSFER] = 1;
                $curlOpt[CURLOPT_INFILE] = $fp; // file pointer
                $curlOpt[CURLOPT_INFILESIZE] = strlen($body);
            break;
            case 'POST':
				$headers[] = 'Content-Type: application/x-www-form-urlencoded';
                $curlOpt[CURLOPT_HTTPHEADER] = $headers;
                $curlOpt[CURLOPT_POST] = true;
                $curlOpt[CURLOPT_POSTFIELDS] = http_build_query($params);
            break;
            case 'POSTJSON':
				$headers[] = 'Content-Type: application/x-yametrika+json';
                $curlOpt[CURLOPT_HTTPHEADER] = $headers;
                $curlOpt[CURLOPT_POST] = true;
                $curlOpt[CURLOPT_POSTFIELDS] = Tools::jsonEncode($params);
            break;
            default:
                throw new YandexApiException("Unsupported request method '$method'");
        }
        $curl = curl_init($url);
        curl_setopt_array($curl, $curlOpt);
        $rbody = curl_exec($curl);
        $errno = curl_errno($curl);
        $error = curl_error($curl);
        $rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // Tools::d(curl_getinfo($curl, CURLINFO_HEADER_OUT));
		curl_close($curl);
		$result = new stdClass();
		$result->status_code = $rcode;
		$result->body = $rbody;
		return $result;
	}
}