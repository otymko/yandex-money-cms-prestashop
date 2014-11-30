$(document).ready(function(){
	$('#tabs').tabs();
	var view = $.totalStorage('tab_ya');
	if(view == null)
		$.totalStorage('tab_ya', 'money');
	else
		$('.ui-tabs-nav li a[href="#'+ view +'"]').click();
	
	$('.ui-tabs-nav li').live('click', function(){
		var view = $(this).find('a').first().attr('href').replace('#', '');
		$.totalStorage('tab_ya', view);
	});
});

function strpos( haystack, needle, offset){
    var i = haystack.indexOf( needle, offset );
    return i >= 0 ? i : false;
}