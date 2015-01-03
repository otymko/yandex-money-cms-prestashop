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
{literal}
<script type="text/javascript">
(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter27737730 = new Ya.Metrika({ id:27737730 }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script><noscript><div><img src="//mc.yandex.ru/watch/27737730" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
{/literal}