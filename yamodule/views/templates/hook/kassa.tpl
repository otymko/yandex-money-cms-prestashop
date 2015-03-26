{if $DATA_ORG && $DATA_ORG['YA_ORG_ACTIVE']}
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<p class="payment_module">
				<a class='ym_{$pt|escape:'html'}' href="{$link->getModuleLink('yamodule', 'redirectk', ['type' => {$pt|escape:'html'}], true)}" title="{l s='Yandex.Money' mod='yamodule'}">
					{$buttontext|escape:'html'}
				</a>
			</p>
		</div>
	</div>
{/if}