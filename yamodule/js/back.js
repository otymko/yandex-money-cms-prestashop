/**
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(document).ready(function()
{
	$('.changec_submit').live('click', function(){
		var id_carrier = $('.sel_delivery').val();
		var pr_incl = $('input#price_incl').val();
		var pr_excl = $('input#price_excl').val();
		$.ajax({
			url: ajaxurl + 'actions.php',
			type: 'POST',
			data: {pr_incl :pr_incl, pr_excl : pr_excl, new_carrier : id_carrier, id_o : id_order, action : 'change_order', tkn: tkn, idm: idm},
			cache: false,
			dataType: 'json',
			beforeSend: function() {
				$('.change_carr').append('<div id="fade"></div>');
				$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();
				$('#circularG').fadeIn();
			},
			success: function(data, textStatus, jqXHR)
			{
				console.log(data);
				if(!data.status && data.error != ''){
					$('#circularG').fadeOut(function(){
						$('.change_carr #fade').fadeOut(function(){
							$(this).remove();
							alert(data.error);
						});
					});
				}else{
					$('#circularG').fadeOut(function(){
						$('.change_carr #fade').fadeOut(function(){
							$(this).remove();
							location.reload();
						});
					});
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log('ERRORS: ' + textStatus);
			}
		});
	});
	
	$('.sel_delivery').live('change', function(){
		var id_carrier = $(this).val();
		if(id_carrier == 0)
		{
			alert(notselc);
			$('input#price_incl, input#price_excl').val('');
		}
		else
		{
			$.ajax({
				url: ajaxurl + 'actions.php',
				type: 'POST',
				data: {new_carrier : id_carrier, id_o : id_order, action : 'load_price', tkn: tkn, idm: idm},
				cache: false,
				dataType: 'json',
				beforeSend: function() {
					$('.change_carr').append('<div id="fade"></div>');
					$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();
					$('#circularG').fadeIn();
				},
				success: function(data, textStatus, jqXHR)
				{
					console.log(data);
					if(data.error){
						$('input#price_incl, input#price_excl').val('');
						$('#circularG').fadeOut(function(){
							$('.change_carr #fade').fadeOut(function(){
								$(this).remove();
								alert(data.error);
							});
						});
					}else{
						$('input#price_incl').val(data.price_with_tax);
						$('input#price_excl').val(data.price_without_tax);
						$('#circularG').fadeOut(function(){
							$('.change_carr #fade').fadeOut(function(){
								$(this).remove();
							});
						});
					}
				},
				error: function(jqXHR, textStatus, errorThrown){
					console.log('ERRORS: ' + textStatus);
				}
			});
		}
	});
});