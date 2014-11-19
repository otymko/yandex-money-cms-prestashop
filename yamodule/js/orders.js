$(document).ready(function(){
	$('#btn_save').live('click', function(){
		var text = $(this).parent().find('textarea').val();
		var th = this;
		var id_order = $(this).parent().find('textarea').attr('name').replace('comment_', '');
		$.post('', {message: text, id_order: id_order, save_comment: 1}).done(function(data){
				alert('Сообщение успешно добавлено!');
				$(th).parent().find('textarea').val('');
		});
	});
	
	$('#open_map_orders').live('click', function(){
		if($(this).attr('checked'))
			$('#map_orders').slideDown('slow');
		else
			$('#map_orders').slideUp('slow');
	});
});