<form target="" name="yamoney_form{$pt|escape:'html'}" action="https://{if !$DATA_ORG['YA_ORG_TYPE']}demo{/if}money.yandex.ru/eshop.xml" method="post">
	<input type="hidden" name="cms_name" value="ya_prestashop">
	<input type="hidden" value="KASSA_{$id_cart|escape:'html'}" name="label" />
	<input type="hidden" value="{$pt|escape:'html'}" name="paymentType" />
	<input type="hidden" name="shopId" value="{$DATA_ORG['YA_ORG_SHOPID']|escape:'html'}"/>
	<input type="hidden" name="scid" value="{$DATA_ORG['YA_ORG_SCID']|escape:'html'}"/>
	<input type="hidden" name="sum" value="{$total_to_pay|escape:'html'}"/>
	<input type="hidden" name="customerNumber" value="KASSA_{$id_cart|escape:'html'}"/>
	<input type="hidden" name="shopSuccessURL" value="{$link->getModuleLink('yamodule', 'success', [], true)|escape:'html'}"/>
	<input type="hidden" name="shopFailURL" value="{$link->getPageLink('order.php', true, null, 'step=3')|escape:'html'}"/>
	<input type="submit" style="display: none;" value="{l s='Click here to proceed with the payment' mod='yamodule'}"/>
	<input name="cps_phone" value="{$address->phone_mobile}" type="hidden"/> 
	<input name="cps_email" value="{$customer->email}" type="hidden"/> 
</form>
{literal}
	<script>
			document.yamoney_form{/literal}{$pt|escape:'html'}{literal}.submit();
	</script>
{/literal}