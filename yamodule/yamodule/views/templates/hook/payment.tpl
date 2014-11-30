{if $DATA_P2P && $DATA_P2P['YA_P2P_ACTIVE']}
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<p class="payment_module">
				<a href="{$link->getModuleLink('yamodule', 'redirect', ['type' => 'wallet'])}" title="{l s='Оплата через Яндекс кошелёк' mod='yamodule'}" class="yandex_money_wallet">
					{l s='Оплата через Яндекс кошелёк' mod='yamodule'}
				</a>
			</p>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<p class="payment_module">
				<a href="{$link->getModuleLink('yamodule', 'redirect', ['type' => 'card'])}" title="{l s='Оплата банковской картой' mod='yamodule'}" class="yandex_money_card">
					{l s='Оплата банковской картой' mod='yamodule'}
				</a>
			</p>
		</div>
	</div>
{/if}