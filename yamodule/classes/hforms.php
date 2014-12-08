<?php

class hforms {

	public $cats;
	public function l($s)
	{
		$mod = Module::getInstanceByName('yamodule');
		return $mod->l($s);
	}

	public function getFormYaPokupki()
	{
		$state = OrderState::getOrderStates(Context::getContext()->language->id);
		$dir = _PS_ADMIN_DIR_;
		$dir = explode('/', $dir);
		$dir = base64_encode(end($dir).'_'.Context::getContext()->cookie->id_employee.'_pokupki');
		$extend = array();
		$carriers = Carrier::getCarriers(Context::getContext()->language->id, true, false, false, null, 5);
		$type = array(
			array(
				'name' => 'POST',
				'id' => 'POST'
			),
			array(
				'name' => 'PICKUP',
				'id' => 'PICKUP'
			),
			array(
				'name' => 'DELIVERY',
				'id' => 'DELIVERY'
			)
		);
		$out = array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Настройки Yandex.Market'),
				'icon' => 'icon-cogs',
				),
			'input' => array(
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Токен для доступа к api магазина'),
						'name' => 'YA_POKUPKI_TOKEN',
						'label' => $this->l('Авторизационный токен Yandex -> Магазин'),
					),
					array(
						'type' => 'checkbox',
						'label' => $this->l('Предоплата'),
						'name' => 'YA_POKUPKI_PREDOPLATA',
						'values' => array(
							'query' => array(
								array(
									'id' => 'YANDEX',
									'name' => $this->l('Оплата при оформлении (только в России)'),
									'val' => 1
								),
								array(
									'id' => 'SHOP_PREPAID',
									'name' => $this->l('Напрямую магазину (только для Украины)'),
									'val' => 1
								),

							),
							'id' => 'id',
							'name' => 'name'
						),
					),
					array(
						'type' => 'checkbox',
						'label' => $this->l('Постоплата'),
						'name' => 'YA_POKUPKI_POSTOPLATA',
						'values' => array(
							'query' => array(
								array(
									'id' => 'CASH_ON_DELIVERY',
									'name' => $this->l('Наличный расчёт при получении товара'),
									'val' => 1
								),
								array(
									'id' => 'CARD_ON_DELIVERY',
									'name' => $this->l('Оплата банковской картой при получении заказа'),
									'val' => 1
								),

							),
							'id' => 'id',
							'name' => 'name'
						),
					),
					array(
						'type' => 'checkbox',
						'label' => $this->l('Настройки'),
						'name' => 'YA_POKUPKI_SET',
						'values' => array(
							'query' => array(
								array(
									'id' => 'CHANGEC',
									'name' => $this->l('Включить смену доставок'),
									'val' => 1
								),

							),
							'id' => 'id',
							'name' => 'name'
						),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Ссылка'),
						'name' => 'YA_POKUPKI_APIURL',
						'label' => $this->l('URL партнёрского api Yandex.Маркет'),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Номер Кампании'),
						'name' => 'YA_POKUPKI_NC',
						'label' => $this->l('Номер Кампании'),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Ваш логин на Yandex'),
						'name' => 'YA_POKUPKI_LOGIN',
						'label' => $this->l('Логин пользоватоеля'),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('ID созданного приложения'),
						'name' => 'YA_POKUPKI_ID',
						'label' => $this->l('ID приложения'),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Пароль доступа к приложениюж'),
						'name' => 'YA_POKUPKI_PW',
						'label' => $this->l('Пароль приложения'),
					),array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => '<a href="https://oauth.yandex.ru/authorize?response_type=code&display=popup&state='.$dir.'&client_id='.Configuration::get('YA_POKUPKI_ID').'">'.$this->l('Получить токен для доступа к Yandex.Покупки').'</a>',
						'name' => 'YA_POKUPKI_YATOKEN',
						'label' => $this->l('Авторизационный токен'),
						'disabled' => true
					),
					array(
						'col' => 6,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Номер пункта самовывоза'),
						'name' => 'YA_POKUPKI_PUNKT',
						'label' => $this->l('Идентификатор пункта самовывоза'),
					),
					array(
						'col' => 6,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Ссылка для обращения к вашему магазину'),
						'name' => 'YA_POKUPKI_APISHOP',
						'label' => $this->l('api магазина'),
					),
				),
				'submit' => array(
					'title' => $this->l('Сохранить'),
				),
			),
		);

		foreach ($carriers as $a)
		{
			$out['form']['input'][] = array(
						'type' => 'select',
						'label' => $this->l('Тип доставки').' '.$a['name'],
						'name' => 'YA_POKUPKI_DELIVERY_'.$a['id_carrier'],
						'desc' =>$this->l('POST - Почта, DELIVERY - Курьерская доставка, PICKUP - Самовывоз'),
						'options' => array(
							'query' => $type,
							'name' => 'name',
							'id' => 'id'
						),
						'class' => 't'
					);
		}

		return $out;
	}

	public function getFormYamoneyMarket()
	{
		return array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Настройки Yandex.Market'),
				'icon' => 'icon-cogs',
				),
			'input' => array(
					array(
						'type' => 'radio',
						'label' => $this->l('Упрощённый yml:'),
						'name' => 'YA_MARKET_SHORT',
						'required' => false,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Включено')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Выключено')
							)
						),
					),
					array(
						'type' => 'categories',
						'label' => $this->l('Категории'),
						'desc' => $this->l('Выберите категории для экспорта. Если нужны подкатегории, отметте их.'),
						'name' => 'YA_MARKET_CATEGORIES',
						'tree' => array(
							'use_search' => false,
							'id' => 'categoryBox',
							'use_checkbox' => true,
							'selected_categories' => $this->cats,
						),
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Выгружать:'),
						'name' => 'YA_MARKET_CATALL',
						'required' => false,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Все категории')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Только выбранные')
							)
						),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Название вашей компании для Yandex.Market'),
						'name' => 'YA_MARKET_NAME',
						'label' => $this->l('Название магазина'),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Стоимость доставки в домашнем регионе'),
						'name' => 'YA_MARKET_DELIVERY',
						'label' => $this->l('Стоимость доставки в домашнем регионе'),
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Тип выгружаемого описания'),
						'name' => 'YA_MARKET_DESC_TYPE',
						'class' => 't',
						'values' => array(
							array(
								'id' => 'NORMAL',
								'value' => 0,
								'label' => $this->l('Полное')
							),
							array(
								'id' => 'SHORT',
								'value' => 1,
								'label' => $this->l('Короткое')
							)
						),
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Доступность'),
						'desc' => $this->l('Доступность товара'),
						'name' => 'YA_MARKET_DOSTUPNOST',
						'is_bool' => false,
						'values' => array(
							array(
								'id' => 'd_0',
								'value' => 0,
								'label' => $this->l('Все доступны')
							),
							array(
								'id' => 'd_1',
								'value' => 1,
								'label' => $this->l('Доступны если > 0, остальные на заказ')
							),
							array(
								'id' => 'd_2',
								'value' => 2,
								'label' => $this->l('Если = 0, не выгружать')
							),
							array(
								'id' => 'd_3',
								'value' => 3,
								'label' => $this->l('Все на заказ')
							)
						)
					),
					array(
						'type' => 'checkbox',
						'label' => $this->l('Настройки'),
						'name' => 'YA_MARKET_SET',
						'values' => array(
							'query' => array(
								array(
									'id' => 'AVAILABLE',
									'name' => $this->l('Экспортировать только товары которые есть в наличии'),
									'val' => 1
								),
								array(
									'id' => 'NACTIVECAT',
									'name' => $this->l('Исключить неактивные категории'),
									'val' => 1
								),
								array(
									'id' => 'HOMECARRIER',
									'name' => $this->l('Использовать поле доставки в домашнем регионе'),
									'val' => 1
								),
								array(
									'id' => 'COMBINATIONS',
									'name' => $this->l('Экспорт комбинаций товара'),
									'val' => 1
								),
								array(
									'id' => 'DIMENSIONS',
									'val' => 1,
									'name' => $this->l('Отображать размеры товара (dimensions)')
								),
								array(
									'id' => 'ALLCURRENCY',
									'val' => 1,
									'name' => $this->l('Выгружать все валюты? (Если нет, выгрузится только поумолчанию)')
								),
								array(
									'id' => 'GZIP',
									'val' => 1,
									'name' => $this->l('gzip сжатие')
								),
								array(
									'id' => 'ROZNICA',
									'val' => 1,
									'name' => $this->l('возможность купить в розничном магазине.')
								),
								array(
									'id' => 'DOST',
									'val' => 1,
									'name' => $this->l(' возможность доставки соответствующего товара.')
								),
								array(
									'id' => 'SAMOVIVOZ',
									'val' => 1,
									'name' => $this->l('возможность зарезервировать и забрать самостоятельно.')
								),

							),
							'id' => 'id',
							'name' => 'name'
						),
					),
					array(
						'col' => 6,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Ссылка на динамический файл прайс листа'),
						'name' => 'YA_MARKET_YML',
						'label' => $this->l('Файл yml'),
					),
					array(
						'col' => 6,
						'class' => 't',
						'type' => 'text',
						'name' => 'YA_MARKET_REDIRECT',
						'label' => $this->l('Редирект ссылка для приложения.'),
					),
				),
			'submit' => array(
					'title' => $this->l('Сохранить'),
				),
				'buttons' => array(
				'generatemanual' => array(
					'title' => $this->l('Генерировать вручную'),
					'name' => 'generatemanual',
					'type' => 'submit',
					'class' => 'btn btn-default pull-right',
					'icon' => 'process-icon-refresh'
				)
			)
			),
		);
	}

	public function getFormYamoneyMetrika()
	{
		$dir = _PS_ADMIN_DIR_;
		$dir = explode('/', $dir);
		$dir = base64_encode(end($dir).'_'.Context::getContext()->cookie->id_employee.'_metrika');
		return array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Настройки модуля Yandex.Metrika'),
				'icon' => 'icon-cogs',
				),
			'input' => array(
					array(
						'type' => 'radio',
						'label' => $this->l('Активность:'),
						'name' => 'YA_METRIKA_ACTIVE',
						'required' => false,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Номер вашего счётчика'),
						'name' => 'YA_METRIKA_NUMBER',
						'label' => $this->l('Номер счётчика'),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('ID приложения с доступом к Yandex.Metrika'),
						'name' => 'YA_METRIKA_ID_APPLICATION',
						'label' => $this->l('ID Приложения'),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Пароль приложения с доступом к Yandex.Metrika'),
						'name' => 'YA_METRIKA_PASSWORD_APPLICATION',
						'label' => $this->l('Пароль приложения'),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => '<a href="https://oauth.yandex.ru/authorize?response_type=code&display=popup&state='.$dir.'&client_id='.Configuration::get('YA_METRIKA_ID_APPLICATION').'">'.$this->l('Получить токен для доступа к Yandex.Metrika').'</a>',
						'name' => 'YA_METRIKA_TOKEN',
						'label' => $this->l('Токен OAuth'),
						'disabled' => true
					),
					array(
						'type' => 'checkbox',
						'label' => $this->l('Настройки'),
						'name' => 'YA_METRIKA_SET',
						'values' => array(
							'query' => array(
								array(
									'id' => 'WEBVIZOR',
									'name' => $this->l('Вебвизор'),
									'val' => 1
								),
								array(
									'id' => 'CLICKMAP',
									'name' => $this->l('Карта кликов'),
									'val' => 1
								),
								array(
									'id' => 'OUTLINK',
									'name' => $this->l('Внешние ссылки, загрузки файлов и отчёт по кнопке "Поделиться"'),
									'val' => 1
								),
								array(
									'id' => 'OTKAZI',
									'name' => $this->l('Точный показатель отказов'),
									'val' => 1
								),
								array(
									'id' => 'HASH',
									'name' => $this->l('Отслеживание хеша в адресной строке браузера'),
									'val' => 1
								),

							),
							'id' => 'id',
							'name' => 'name'
						),
					),
					array(
						'type' => 'checkbox',
						'label' => $this->l('Собирать статистику по следующим цепям:'),
						'name' => 'YA_METRIKA_CELI',
						'values' => array(
							'query' => array(
								array(
									'id' => 'CART',
									'name' => $this->l('Корзина(Посетитель кликнул "Добавить в корзину")'),
									'val' => 1
								),
								array(
									'id' => 'ORDER',
									'name' => $this->l('Заказ(Посетитель оформил заказ)'),
									'val' => 1
								),
								array(
									'id' => 'WISHLIST',
									'name' => $this->l('Вишлист(Посетитель добавил товар в вишлист)'),
									'val' => 1
								)
							),
							'id' => 'id',
							'name' => 'name'
						),
					),
					array(
						'col' => 6,
						'class' => 't',
						'type' => 'text',
						'name' => 'YA_METRIKA_REDIRECT',
						'label' => $this->l('Редирект ссылка для приложения.'),
					),
				),
			'submit' => array(
					'title' => $this->l('Сохранить'),
			),
			),
		);
	}

	public function getFormYamoneyOrg()
	{
		return array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Настройки Yandex.Kassa'),
				'icon' => 'icon-cogs',
				),
			'input' => array(
					array(
						'type' => 'radio',
						'label' => $this->l('Активность:'),
						'name' => 'YA_ORG_ACTIVE',
						'required' => false,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'radio',
						'label' => $this->l('Режим работы:'),
						'name' => 'YA_ORG_TYPE',
						'required' => false,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Рабочий')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Тестовый')
							)
						),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('ShopId Указанный в договоре'),
						'name' => 'YA_ORG_SHOPID',
						'label' => $this->l('ShopID'),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('SCID Указанный в договоре'),
						'name' => 'YA_ORG_SCID',
						'label' => $this->l('SCID'),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Пароль Указанный в договоре'),
						'name' => 'YA_ORG_MD5_PASSWORD',
						'label' => $this->l('ShopPassword'),
					),
					array(
						'type' => 'checkbox',
						'label' => $this->l('Способы оплат'),
						'name' => 'YA_ORG_PAYMENT',
						'values' => array(
							'query' => array(
								array(
									'id' => 'YANDEX',
									'name' => $this->l('Яндекс деньги'),
									'val' => 1
								),
								array(
									'id' => 'CARD',
									'name' => $this->l('Банковская карта'),
									'val' => 1
								),
								array(
									'id' => 'TERMINAL',
									'name' => $this->l('Терминал'),
									'val' => 1
								),
								array(
									'id' => 'MOBILE',
									'name' => $this->l('Мобильный телефон'),
									'val' => 1
								),
								array(
									'id' => 'WEBMONEY',
									'name' => $this->l('Webmoney'),
									'val' => 1
								),
								array(
									'id' => 'SBER',
									'name' => $this->l('Сбербанк'),
									'val' => 1
								),
								array(
									'id' => 'ALFA',
									'name' => $this->l('Алфа-банк'),
									'val' => 1
								),

							),
							'id' => 'id',
							'name' => 'name'
						),
					),
					array(
						'type' => 'checkbox',
						'label' => $this->l('Вести журнал логов'),
						'name' => 'YA_ORG_LOGGING',
						'values' => array(
							'query' => array(
								array(
									'id' => 'ON',
									'name' => ''
								),
							),
							'id' => 'id',
							'name' => 'name'
						),
					),
					array(
						'col' => 6,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Ссылка для checkOrder'),
						'name' => 'YA_ORG_CHECKORDER',
						'label' => $this->l('checkOrder'),
					),
					array(
						'col' => 6,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Ссылка для Payment Aviso'),
						'name' => 'YA_ORG_AVISO',
						'label' => $this->l('PaymentAviso'),
					),
					array(
						'col' => 6,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Ссылка для fail'),
						'name' => 'YA_ORG_FAIL',
						'label' => $this->l('fail'),
					),
					array(
						'col' => 6,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Ссылка для success'),
						'name' => 'YA_ORG_SUCCESS',
						'label' => $this->l('success'),
					),
				),
			'submit' => array(
					'title' => $this->l('Сохранить'),
				),
			),
		);
	}

	public function getFormYamoney()
	{
		return array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Настройки Yandex.Money p2p'),
				'icon' => 'icon-cogs',
				),
			'input' => array(
					array(
						'type' => 'radio',
						'label' => $this->l('Активность:'),
						'name' => 'YA_P2P_ACTIVE',
						'required' => false,
						'class' => 't',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'col' => 4,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Номер вашего кошелька yandex деньги'),
						'name' => 'YA_P2P_NUMBER',
						'label' => $this->l('Номер кошелька'),
					),
					array(
						'col' => 6,
						'class' => 't',
						'type' => 'text',
						'desc' => $this->l('Идентификатор вашего приложения в yandex'),
						'name' => 'YA_P2P_IDENTIFICATOR',
						'label' => $this->l('Идентификатор приложения'),
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('OAuth2 client secret:'),
						'name' => 'YA_P2P_KEY',
						'rows' => 5,
						'cols' => 30,
						'desc' => $this->l('Секретный ключ полученный после создании приложения.'),
						'class' => 't'
					),
					array(
						'type' => 'checkbox',
						'label' => $this->l('Вести журнал логов'),
						'name' => 'YA_P2P_LOGGING',
						'values' => array(
							'query' => array(
								array(
									'id' => 'ON',
									'name' => '',
									'val' => 1
								),
							),
							'id' => 'id',
							'name' => 'name'
						),
					),
					array(
						'col' => 6,
						'class' => 't',
						'type' => 'text',
						'name' => 'YA_P2P_REDIRECT',
						'label' => $this->l('Редирект ссылка для приложения.'),
					),
				),
			'submit' => array(
					'title' => $this->l('Сохранить'),
				),
			),
		);
	}
}