jQuery(document).ready(function() {

	/* Create a process */
	jQuery(document).on('click', '.userpro_mu_create', function(e){
		e.preventDefault();
		elem = jQuery(this);
		name = jQuery('#userpro_mu_name').val();
		form = jQuery('#results-wrap').find('form');
		if (name != ''){
			jQuery('img.upadmin-load-inline').show();
			jQuery.ajax({
				url: ajaxurl,
				data: form.serialize() + '&action=userpro_mu_create&name='+name,
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
					jQuery('img.upadmin-load-inline').hide();
					jQuery('#results-wrap').empty().html( data.result );
					elem.removeClass('userpro_mu_create').addClass('userpro_mu_start').html( elem.data('create') );
					jQuery('#userpro_mu_name').val('');
				}
			});
		}
		return false;
	});
	
	/* Start a process */
	jQuery(document).on('click', '.userpro_mu_start', function(e){
		e.preventDefault();
		elem = jQuery(this);
		if (jQuery('#userpro_mu_name').val() != ''){
			jQuery('img.upadmin-load-inline').show();
			jQuery.ajax({
				url: ajaxurl,
				data: 'action=userpro_mu_getfields',
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
					jQuery('img.upadmin-load-inline').hide();
					elem.addClass('userpro_mu_create').removeClass('userpro_mu_start').html( elem.data('ready') );
					jQuery('#results-wrap').html(data.res).slideDown();
				}
			});
		} else {
			jQuery('#userpro_mu_name').focus();
		}
		return false;
	});
	
});

function save_edit_form(title,elm){
	
	var str = jQuery(".userpro_mu_form").serialize();
	jQuery('img.upadmin-load-inline').show();
	jQuery.ajax({
		url:ajaxurl,
		data:str+'&action=userpro_mu_create&name='+title,
		dataType:'JSON',
		type:'POST',
		success:function(data){
			jQuery('img.upadmin-load-inline').hide();
			jQuery('#results-wrap').html(jQuery(elm).data('edit')).slideDown();	
					
		}
	});
}

function up_delete_form( title, e, elm ){
	e.preventDefault();
	var res = window.confirm( "Are you sure you want to delete the form?" );
	
	if(res){
		jQuery.ajax({
		url:ajaxurl,
		data:'action=userpro_mu_delete_form&name='+title,
		dataType:'JSON',
		type:'POST',
		success:function(data){
			jQuery(elm).closest('tr').fadeOut().remove();	
			alert(data.result);
		}
	});
	}
}
