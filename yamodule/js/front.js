$(document).ready(function(){
	if(celi_wishlist)
	{
		if (typeof WishlistCart != 'undefined')
			var old_WishlistCart = WishlistCart;
		WishlistCart = function (id, action, id_product, id_product_attribute, quantity, id_wishlist)
		{
			old_WishlistCart(id, action, id_product, id_product_attribute, quantity, id_wishlist);
			$.ajax({
				type: 'POST',
				url: baseDir + 'modules/yamodule/action.php?rand=' + new Date().getTime(),
				headers: { "cache-control": "no-cache" },
				async: true,
				cache: false,
				dataType : "json",
				data: 'action=add_wishlist&id_product=' + id_product + '&quantity=' + quantity + '&token=' + static_token + '&id_product_attribute=' + id_product_attribute,
				success: function(data)
				{
					metrikaReach('metrikaWishlist', data.params);
				}
			});
		}
	}
	
	if(celi_cart)
	{
		var old_addCart = ajaxCart.add;
		ajaxCart.add = function (idProduct, idCombination, addedFromProductPage, callerElement, quantity, wishlist)
		{
			old_addCart(idProduct, idCombination, addedFromProductPage, callerElement, quantity, wishlist);
			$.ajax({
				type: 'POST',
				url: baseDir + 'modules/yamodule/action.php?rand=' + new Date().getTime(),
				headers: { "cache-control": "no-cache" },
				async: true,
				cache: false,
				dataType : "json",
				data: 'action=add_cart&id_product=' + idProduct + '&quantity=' + quantity + '&token=' + static_token + '&id_product_attribute=' + idCombination,
				success: function(data)
				{
					metrikaReach('metrikaCart', data.params);
				}
			});
		}
	}
});

function metrikaReach(goal_name, params) {
	for (var i in window) {
		if (/^yaCounter\d+/.test(i)) {
			window[i].reachGoal(goal_name, params);
		}
	}
}