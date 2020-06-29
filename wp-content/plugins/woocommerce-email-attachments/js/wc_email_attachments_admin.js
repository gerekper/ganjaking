jQuery(function() {
	
	var last_settings_tab = jQuery('input[name="wc_eatt_last_tab_active"]');
	var last_settings_tab_link = last_settings_tab.val();
	var last_settings_subject = jQuery('input[name="wc_eatt_last_subject_active"]');
	var last_settings_subject_link = last_settings_subject.val();
	
	jQuery('#wc_eatt_select_email_type_sett').on('change', function(){
			var select = jQuery(this);
			if(!select.is(':visible')) return false;
			var container = jQuery(this).closest('#wc_emai_att_settings_container');
			var href = select.val();
			last_settings_subject.val(href);
			container.find('.section_email').hide();
			container.find(href).show();
			return false;
		});
	
	jQuery('#wc_emai_att_settings_container a').on('click', function(){
			var container = jQuery(this).closest('#wc_emai_att_settings_container');
			var href = jQuery(this).attr('href');
			container.find('a').removeClass('current');
			jQuery(this).addClass('current');
			container.find('.section').hide();
			container.find(href).show();
			last_settings_tab.val(href);
			var select = jQuery('#wc_eatt_select_email_type_sett');
			var selectoption = select.find('option[value="' + last_settings_subject_link + '"]');
			if( selectoption.length > 0 )
			{
				selectoption.attr("selected",true);
			}
			select.trigger('change');
			return false;
	});
	 
	
	var sett_container = last_settings_tab.closest('#wc_emai_att_settings_container');
	var tab = sett_container.find(last_settings_tab_link).first();
	
	if( tab.length > 0 )
	{
		jQuery('#wc_emai_att_settings_container a[href="'+ last_settings_tab_link + '"]').trigger('click');
	}
	else
	{
		jQuery('#wc_emai_att_settings_container a:eq(0)').trigger('click');
	}
	
	
			// Uploading and selecting attachment files
	jQuery('form').on( 'click', '.wc_eatt_select_att', function( event ){
		var attachment_file_frame = null;
		
		var eatt_add_button = jQuery(this);
		var eatt_table =  eatt_add_button.prev('table');
		var eatt_tbody = eatt_table.find('tbody[wc_eatt_subject]');
		
		
		var wp_media_post_id = wp.media.model.settings.post.id;					// Store the old id and text
		var wp_media_uploaded_to_this_post = _wpMediaViewsL10n.uploadedToThisPost;
	
		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( attachment_file_frame ) {
			attachment_file_frame.uploader.param( 'post_id', wc_email_attachments.attachment_post_id );
			_wpMediaViewsL10n.uploadedToThisPost = wc_email_attachments.uploaded_for_attachment;
			attachment_file_frame.open();
			return;
		}
					// Set the wp.media post id so the uploader grabs the ID we want when initialised
		wp.media.model.settings.post.id = wc_email_attachments.attachment_post_id;
		_wpMediaViewsL10n.uploadedToThisPost = wc_email_attachments.uploaded_for_attachment;
	
		var wc_ea_attachment_file_states = [
			// Main states.
			new wp.media.controller.Library({
				library:   wp.media.query(),
				multiple:  true,
				title:     eatt_add_button.data('choose'),
				priority:  20,
				filterable: 'all'
			})
		];

		// Create the media frame.
		attachment_file_frame = wp.media.frames.email_att_file = wp.media({
				// Set the title of the modal.
			title: eatt_add_button.data('choose'),
			library: {
					type: ''
				},
			button: {
					text: eatt_add_button.data('update')
				},
			multiple: true,
			states: wc_ea_attachment_file_states
		});

		//	when modal window is closed -> restore original settings
		attachment_file_frame.on( 'close', function() {
			_wpMediaViewsL10n.uploadedToThisPost = wp_media_uploaded_to_this_post;
			wp.media.model.settings.post.id = wp_media_post_id;
		});
		
		// When an image is selected, run a callback.
		attachment_file_frame.on( 'select', function() {
			var selected_ids = [];
			var selection = attachment_file_frame.state().get('selection');
			selection.map( function( attachment ) {

				attachment = attachment.toJSON();
				if ( attachment.id )
					{
						selected_ids.push(attachment.id);
					}
			} );
			// Block write panel and load table lines
			eatt_table.block({message: null, overlayCSS: {background: '#fff url(' + wc_email_attachments.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
					  				
			var senddata = {
				action: 'wc_eatt_get_attachments',
				wc_eatt_att_ids: selected_ids,
				wc_eatt_subject: eatt_tbody.attr('wc_eatt_subject'),
				wc_eatt_product_id: eatt_tbody.attr('wc_eatt_product_id'),
				wc_eatt_new_att: true,
				woocommerce_email_attachments_nonce: wc_email_attachments.woocommerce_email_attachments_nonce
			};

			jQuery.ajax({
				type: "POST",
				url: wc_email_attachments.ajaxurl,
				dataType: 'json',
				cache: false,
				data: senddata,
				success	: function(response, textStatus, jqXHR) {	
						wc_email_attachments.woocommerce_email_attachments_nonce = response.woocommerce_email_attachments_nonce;
						if(response.success){
							eatt_tbody.append(response.html);
							eatt_tbody.find('[data-tip]').tipTip({
											'attribute' : 'data-tip',
											'fadeIn' : 50,
											'fadeOut' : 50,
											'delay' : 200
										});
						}
						else
						{
							alert(response.alert);
						};
					},
				error: function(testObj) {
						alert(wc_email_attachments.alert_ajax_error);
					},
				complete: function(test) {
						eatt_table.unblock();
						_wpMediaViewsL10n.uploadedToThisPost = wp_media_uploaded_to_this_post;
						wp.media.model.settings.post.id = wp_media_post_id;
					}
			});
		});

		// Set post to 0 and set our custom type
		attachment_file_frame.on( 'ready', function() {
			attachment_file_frame.uploader.options.uploader.params = {
				type: 'wc_email_attachment_files'
			};
		});

		// Finally, open the modal.
		attachment_file_frame.open();
	});
	
//	jQuery('form').on( 'click', '.wc_eatt_td_settings', function( event ){
	jQuery('form').on( 'click', '.button_remove', function( event ){
		if(!confirm(wc_email_attachments.remove_file))
			return;
		var tr_remove = jQuery(this).closest('tr');
		tr_remove.remove();
		return true;
	});
	
	jQuery(".tips, .help_tip").tipTip({
    	'attribute' : 'data-tip',
    	'fadeIn' : 50,
    	'fadeOut' : 50,
    	'delay' : 200
    });
	
	jQuery('.wc_email_att_delete_files').on('click', function(){
		var answer = confirm(wc_email_attachments.alert_delete_files);
		return answer;
		});
		
	jQuery('.wc_email_att_select_tips').hide();
	
	jQuery('#update_woocom_email_att_select').on('change', function(){
			var selected = jQuery(this).val();
			jQuery('.wc_email_att_select_tips').hide();
			jQuery(this).closest('.wc_email_att_submit').find('[tip_src="'+selected+'"]').show();
		});
	jQuery('#update_woocom_email_att_select').trigger('change');
	
	jQuery('.wc_eatt_att_table').sortable({
						items: "tbody tr"
				});
});

