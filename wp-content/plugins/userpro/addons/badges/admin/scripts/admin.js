jQuery(document).ready(function() {

	/* Delete achievement badge */
	jQuery(document).on('click', '.userpro-badge-remove', function(e){
		btype = jQuery(this).data('btype');
		bid = jQuery(this).data('bid');
		td = jQuery(this).parents('tr');
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_delete_achievement_badge&btype=' + btype + '&bid=' + bid,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				td.fadeOut();
			},
			error: function(data){
				alert('error');
			}
		});
		return false;
	});
	
	/* Badge selection */
	jQuery(document).on('click', '.userpro-admin-badge', function(e){
		jQuery('.userpro-admin-badge').removeClass('active');
		jQuery(this).addClass('active');
		jQuery('#badge_url').val( jQuery(this).find('img').attr('src') );
	});
	
	/* conditional fields */
	jQuery('table[data-type=conditional]').hide();
	jQuery('table[rel=' + jQuery('#badge_method').val() + ']').show();
	
	jQuery(document).on('change', '#badge_method', function(e){
		jQuery('table[data-type=conditional]').hide();
		jQuery('table[rel=' + jQuery(this).val() + ']').show();
		jQuery('table[rel=' + jQuery(this).val() + ']').find('select').removeClass("chzn-done").css('display', 'inline').data('chosen', null);
		jQuery('table[rel=' + jQuery(this).val() + ']').find("*[class*=chzn], .chosen-container").remove();
		jQuery('table[rel=' + jQuery(this).val() + ']').find(".chosen-select").chosen({
			disable_search_threshold: 10
		});
	});
	
	/* Delete user badge */
	jQuery(document).on('click', '.userpro-delete-badge', function(e){
		e.preventDefault();
		user_id = jQuery(this).data('user');
		badge_url = jQuery(this).data('url');
		element = jQuery(this);
		
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_delete_user_badge&user_id=' + user_id + '&badge_url=' + badge_url,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				element.parents('div.userpro-user-badge').fadeOut();
			},
			error: function(data){
				alert('error');
			}
		});
		return false;
	});
	
	jQuery('#remove_badge_from_all_users').change(function(){
		var selected_badge = jQuery(this).val();
		jQuery('#delete_badge').show();
	});
	
	jQuery("#delete_badge").click(function(){
		var res = window.confirm("Are you sure you want to remove selected badge from all users?");
		if(res){
			var selected_badge = jQuery('#remove_badge_from_all_users').val();
			jQuery('#userpro-delete-badge-loading').show();
			jQuery(this).attr('disabled');
			jQuery.ajax({
				url: ajaxurl,
				data: 'action=userpro_delete_all_user_badge&selected_badge=' + selected_badge,
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
					jQuery('#userpro-delete-badge-loading').hide();
					jQuery(this).removeAttr('disabled');
					alert("Removed badge from all users successfully");
					//element.parents('div.userpro-user-badge').fadeOut();
				},
				error: function(data){
					alert('error');
				}
			});
		}
	})
	
});

jQuery(document).ready(function($){
	 
	 
    var custom_uploader;
 
 
    $('#upload_image_button').click(function(e) {
 
        e.preventDefault();
 
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#upload_image').val(attachment.url);
            $('#badge_url').val(attachment.url);
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });
 
    
 
});