<div id="tabs">
	<ul>
		<li><a href="#moneyorg">{l s='Yandex.Касса' mod='yamodule'}</a></li>
		<li><a href="#money">{l s='Yandex.Деньги p2p' mod='yamodule'}</a></li>
		<li><a href="#metrika">{l s='Yandex.Метрика' mod='yamodule'}</a></li>
		<li><a href="#market">{l s='Yandex.Маркет' mod='yamodule'}</a></li>
		<li><a href="#marketp">{l s='Yandex.Покупки Маркет' mod='yamodule'}</a></li>
	</ul>
	<div id="money">
		<div class="errors">{$p2p_status}</div>
		{$money_p2p}
	</div>
	<div id="moneyorg">
		<div class="errors">{$org_status}</div>
		{$money_org}
	</div>
	<div id="metrika">
		<div class="errors">{$metrika_status}</div>
		{$money_metrika}
		<div id="iframe_container"></div>
	</div>
	<div id="market">
		<div class="errors">{$market_status}</div>
		{$money_market}
	</div>
	<div id="marketp">
		<div class="errors">{$pokupki_status}</div>
		{$money_marketp}
	</div>
</div>