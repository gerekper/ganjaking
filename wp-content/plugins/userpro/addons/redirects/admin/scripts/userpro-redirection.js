jQuery(document).ready(function() {

	/* add new redirect */
	jQuery(document).on('submit', '.userpro_new_redirect', function(e){
		e.preventDefault();
		form = jQuery(this);
		form.find('.upadmin-load-inline').show();
		form.find('.upadmin-errors').empty();
		jQuery.ajax({
			url: ajaxurl,
			data: form.serialize() + '&action=userpro_new_redirect',
			dataType: 'JSON',
			type: 'POST',
			success:function(data){

				form.find('.upadmin-load-inline').hide();
				
				if (data.error){
					form.find('.upadmin-errors').html(data.error);
				} else {
					form.find('.upadmin-redirects table').html(data.html);
				}
				
			}
		});
		return false;
	});
	
	/* remove a redirect */
	jQuery(document).on('click', '.remove-redirect-rule', function(e){
		e.preventDefault();
		var key = jQuery(this).data('k');
		var form = jQuery(this).parents('form');
		var type = form.find('input[type=hidden]#type').val();
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_remove_redirect&key='+key+'&type='+type,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				
				form.find('.upadmin-redirects table').html(data.html);
				
			}
		});
		return false;
	});
	
});