jQuery(document).ready(function($){
	
	$('.dashboard-side').click(function(){
		load_content(this);
	});

	jQuery(document).on('keyup keydown', 'input[type=password]', function(e){
		userpro_dash_password_strength_meter( jQuery(this) );
	});

	$('#userpro-dash-logout').find('a').removeClass('userpro-small-link');
	jQuery(document).on('submit', '.userpro-dashboard form', function(e){
		e.preventDefault();
		var form = jQuery(this);
		form.find('input,textarea').each(function(){
				jQuery(this).trigger('blur');
			});
			
			form.find('select').each(function(){
				jQuery(this).trigger('change');
			});
			
			form.find('select[data-required=1],textarea[data-required=1]').each(function(){
				if ( !jQuery(this).val() ) {
					userpro_client_error_irregular( jQuery(this), jQuery(this).parents('.userpro-input'), jQuery(this).parents('.userpro-dashboard').data('required_text') );
				} else {
					userpro_client_valid( jQuery(this).find("select"), jQuery(this).parents('.userpro-input') );
				}
			});
			
			form.find('.userpro-radio-wrap[data-required=1]').each(function(){
				if ( !jQuery(this).find("input:radio").is(":checked") ) {
					userpro_client_error_irregular( '', jQuery(this).parents('.userpro-input'), jQuery(this).parents('.userpro-dashboard').data('required_text') );
				} else {
					userpro_client_valid( jQuery(this).find("input:radio"), jQuery(this).parents('.userpro-input') );
				}
			});
			
			form.find('.userpro-checkbox-wrap[data-required=1]').each(function(){
				if ( !jQuery(this).find("input:checkbox").is(":checked") ) {
					userpro_client_error_irregular( '', jQuery(this).parents('.userpro-input'), jQuery(this).parents('.userpro-dashboard').data('required_text') );
				} else {
					userpro_client_valid( jQuery(this).find("input:checkbox"), jQuery(this).parents('.userpro-input') );
				}
			});
			
			form.find('.userpro-maxwidth[data-required=1]').each(function(){
				if ( !jQuery(this).find("input:checkbox").is(":checked") ) {
					userpro_client_error_irregular( '', jQuery(this).find('.userpro-input'), jQuery(this).data('required_msg') );
				} else {
					userpro_client_valid( jQuery(this).find("input:checkbox"), jQuery(this).find('.userpro-input') );
				}
			});
			
			if (form.find('.userpro-warning').length > 0 || form.find('.warning').length > 0){
				form.find('.userpro-section').each(function(){
					jQuery(this).find('.userpro-section-warning').remove();
					if (jQuery(this).nextUntil('div.userpro-column').find('.userpro-warning').length > 0) {
						jQuery(this).css({'display': 'block'});
						jQuery(this).append('<ins class="userpro-section-warning">Please correct fields</ins>');
						jQuery(this).find('.userpro-section-warning').fadeIn();
					}
				});
				form.find('.userpro-warning:first').parents('.userpro-input').find('input').focus();
				return false;
			} else {
				form.find('.userpro-section').each(function(){
					jQuery(this).find('.userpro-section-warning').remove();
		});
		}
		userpro_init_load( form );
		form.parents('.userpro-dashboard').find('img.userpro-loading').show().addClass('inline');
		form_data = jQuery(this).parents('.userpro-dashboard').data();
		shortcode = '[userpro';
		jQuery.each( form_data, function(key, value) {
			shortcode = shortcode + ' ' + key + '=' + '"' + value + '"';
		});
		shortcode = shortcode + ']';
		
		// username
		if (jQuery(this).parents('.userpro-dashboard').find('.userpro-profile-img-btn a').data('up_username')) {
			up_username = jQuery(this).parents('.userpro-dashboard').find('.userpro-profile-img-btn a').data('up_username');
		} else {
			up_username = 0;
		}
		jQuery.ajax({
			url: userpro_ajax_url,
			data: form.serialize() + "&action=userpro_process_form&template="+form_data['template']+"&group="+form_data[ form_data['template'] + '_group' ]+"&shortcode="+encodeURIComponent(shortcode)+'&up_username='+up_username,
			dataType: 'JSON',
			type: 'POST',
			error: function(xhr, status, error){
				userpro_end_load( form );
				alert("Error in jQuery.ajax while submitting a form:"+error);
			},
			success:function(data){
				userpro_end_load( form );
				form.parents('.userpro-dashboard').find('img.userpro-loading').hide().removeClass('inline');
				if (data && data.error){
					
					var i = 0;
					jQuery.each( data.error, function(key, value) {
						i++;
						if(key=='antispam' || key=='user_email'){
							element = form.find('.userpro-field[data-key="'+key+'"]').find('input[type=text]');
						}
						else{
						element = form.find('.userpro-field[data-key="'+key+'"]').find('input');
						}						
						parent = element.parents('.userpro-input');
						if (element.attr('type') == 'radio' || element.attr('type') == 'checkbox' ){
							userpro_client_error_irregular( element, element.parents('.userpro-input'), value );
						} else {
							if (i==1) element.focus();
							userpro_client_error( element, element.parents('.userpro-input'), value );
						}
						
						if (key == 'userpro_editor') {
							if (form.find('.userpro-field-editor .userpro-input').find('.userpro-warning').length){
							form.find('.userpro-field-editor .userpro-input').find('.userpro-warning').html(value);
							form.find('.userpro-field-editor .userpro-input').find('.userpro-warning').css({'top' : '0px', 'opacity' : '1'});
							} else {
							form.find('.userpro-field-editor .userpro-input').append('<div class="userpro-warning"><i class="userpro-icon-caret-up"></i>' + value + '</div>');
							form.find('.userpro-field-editor .userpro-input').find('.userpro-warning').css({'top' : '0px', 'opacity' : '1'});
							}
						   }
						});
					}
					
					/* show modal confirmation */
					if (form_data['template'] == 'edit'){
						userpro_overlay_confirmation( form.parents('.userpro-dashboard').data('modal_profile_saved') );
					}

				/* custom message */
				if (data && data.custom_message && data.custom_message != '' ){
					form.parents('.userpro-dashboard').find('.userpro-body').find('.userpro-message').remove();
					form.parents('.userpro-dashboard').find('.userpro-body').prepend( data.custom_message );
				}
			}
		});
	});

	jQuery(document).on('click', '.userpro-dashboard form:not(.userpro-search-form) .userpro-input .userpro-button.red', function(e){
		jQuery(this).parents('.userpro-input').find('.userpro-pic-none').show();
		if ( jQuery(this).parents('.userpro-input').find('img.default').length) {
		jQuery(this).parents('.userpro-input').find('img.default').show();
		jQuery(this).parents('.userpro-input').find('img.modified').remove();
		} else {
		
			if (jQuery(this).parents('.userpro-dashboard').find('div.userpro-pic-post_featured_image').length) {
			jQuery(this).parents('.userpro-input').find('img.modified').addClass('no_feature').attr('src', jQuery(this).parents('.userpro-input').data('placeholder') );
			} else {
			jQuery(this).parents('.userpro-input').find('img.modified').attr('src', '' );
			}
			
		}
		if ( jQuery(this).parents('.userpro-input').find('.userpro-file-input').length) {
			jQuery(this).parents('.userpro-input').find('.userpro-file-input').remove();
		}
		jQuery(this).parents('.userpro-input').find('input:hidden').val( '' );
		jQuery(this).fadeOut();
		
		// re-validate
		jQuery(this).parents('.userpro-input').find('input:hidden').each(function(){
			jQuery(this).trigger('blur');
		});

	});

	/**
		custom radio buttons
	**/
	jQuery(document).on('click', '.userpro-dashboard input[type=radio]', function(e){
		var field = jQuery(this).parents('.userpro-input');
		field.find('span').removeClass('checked');
		jQuery(this).parents('label').find('span').addClass('checked');
	});
	
	/**
		custom checkbox buttons
	**/
	jQuery(document).on('change', '.userpro-dashboard input[type=checkbox]', function(e){
		if (jQuery(this).is(':checked')) {
			jQuery(this).parents('label').find('span').addClass('checked');
		} else {
			jQuery(this).parents('label').find('span').removeClass('checked');
		}
	});
	

	function load_content(elem){
		
		var id = $(elem).data('id');
		$('.uploadPic-box').removeClass('uploadPic-box-selected');
		$(elem).find('.uploadPic-box').addClass('uploadPic-box-selected');
		$('.dashboardRight').hide();
		$('#'+id).css({'display':'inline-block'});
			
	}	

	function userpro_dash_password_strength_meter(element){
		var meter = element.parents('.userpro-dashboard').find(".userpro-field[data-key^='passwordstrength']");
		var meter_data = meter.find('span.strength-text').data();
		var meter_text = meter.find('span.strength-text');
		var password = element.val();
		var LOWER = /[a-z]/,
			UPPER = /[A-Z]/,
			DIGIT = /[0-9]/,
			DIGITS = /[0-9].*[0-9]/,
			SPECIAL = /[^a-zA-Z0-9]/,
			SAME = /^(.)\1+$/;
		var lower = LOWER.test(password),
			upper = UPPER.test( password.substring(0, 1).toLowerCase() + password.substring(1) ),
			digit = DIGIT.test(password),
			digits = DIGITS.test(password),
			special = SPECIAL.test(password);
		if (meter.length > 0 ) {
			if  ( password.length < 8 ) {
				meter.find('.strength-plain').removeClass('fill');
				meter_text.html( meter_data['too_short'] );
				return 0;
			} else if ( SAME.test(password) ) {
				meter.find('.strength-plain').removeClass('fill');
				meter.find('.strength-plain:eq(0)').addClass('fill');
				meter_text.html( meter_data['very_weak'] );
				return 1;
			} else if ( lower && upper && digit && special ) {
				meter.find('.strength-plain').removeClass('fill');
				meter.find('.strength-plain').addClass('fill');
				meter_text.html( meter_data['very_strong'] );
				return 5;
			} else if ( lower && upper && digit || lower && digits || upper && digits || special ) {
				meter.find('.strength-plain').removeClass('fill');
				meter.find('.strength-plain:eq(0),.strength-plain:eq(1),.strength-plain:eq(2),.strength-plain:eq(3)').addClass('fill');
				meter_text.html( meter_data['strong'] );
				return 4;
			} else if (lower && upper || lower && digit || upper && digit) {
				meter.find('.strength-plain').removeClass('fill');
				meter.find('.strength-plain:eq(0),.strength-plain:eq(1),.strength-plain:eq(2)').addClass('fill');
				meter_text.html( meter_data['good'] );
				return 3;
			} else {
				meter.find('.strength-plain').removeClass('fill');
				meter.find('.strength-plain:eq(0),.strength-plain:eq(1)').addClass('fill');
				meter_text.html( meter_data['weak'] );
				return 2;
			}	
		}
	}

	jQuery(document).on('blur', '.userpro-dashboard input', function(e){
	
		var element = jQuery(this);
		var parent = element.parents('.userpro-input');
		var required = element.data('required');
		var ajaxcheck = element.data('ajaxcheck');
		var original_elem = element.parents('.userpro-dashboard').find('input[type=password]:first');
		var original = element.parents('.userpro-dashboard').find('input[type=password]:first').val();
	
		if (required == 1) {
			
			if ( element.val().replace(/^\s+|\s+$/g, "").length == 0) {
				userpro_client_error( element, element.parents('.userpro-input'), element.parents('.userpro-dashboard').data('required_text') );
			} else if (ajaxcheck) {
				userpro_side_validate( element, element.val(), ajaxcheck );
			} else {
				userpro_client_valid(element, element.parents('.userpro-input'));
			}
			
			if ( jQuery(this).attr('type') == 'password') { // only if field is password
				if ( element.val().replace(/^\s+|\s+$/g, "").length == 0) {
					userpro_client_error( element, element.parents('.userpro-input'), element.parents('.userpro-dashboard').data('required_text') );
				} else if ( element.val().length < 8 ) {
					userpro_client_error( element, element.parents('.userpro-input'), element.parents('.userpro-dashboard').data('password_too_short') );
				} else if ( userpro_password_strength_meter( element ) < 3 ) {
					userpro_client_error( element, element.parents('.userpro-input'), element.parents('.userpro-dashboard').data('password_not_strong') );
				} else {
					userpro_client_valid(element, element.parents('.userpro-input'));
				}
			}

		} else if ( element.attr('type') == 'password' && original_elem && original && original_elem.parents('.userpro-input').find('.userpro-warning').length == 0 ) {
			if (element.val().replace(/^\s+|\s+$/g, "").length == 0) {
				userpro_client_error( element, element.parents('.userpro-input'), element.parents('.userpro-dashboard').data('required_text') );
			} else if ( element.val().length < 8 ) {
				userpro_client_error( element, element.parents('.userpro-input'), element.parents('.userpro-dashboard').data('password_too_short') );
			} else if ( userpro_password_strength_meter( element ) < 3 ) {
				userpro_client_error( element, element.parents('.userpro-input'), element.parents('.userpro-dashboard').data('password_not_strong') );
			} else if ( original != element.val() ) {
				userpro_client_error( element, element.parents('.userpro-input'), jQuery(this).parents('.userpro-dashboard').data('passwords_do_not_match') );
			} else {
				userpro_client_valid(element, element.parents('.userpro-input'));
			}
		} else if ( ( element.attr('type') == 'password' && original ) || ( element.attr('type') == 'password' && element.parents('.userpro-dashboard').data('template') == 'change' ) ) {
			if (element.val().replace(/^\s+|\s+$/g, "").length == 0) {
				userpro_client_error( element, element.parents('.userpro-input'), element.parents('.userpro-dashboard').data('required_text') );
			} else if ( element.val().length < 8 ) {
				userpro_client_error( element, element.parents('.userpro-input'), element.parents('.userpro-dashboard').data('password_too_short') );
			} else if ( userpro_password_strength_meter( element ) < 3 ) {
				userpro_client_error( element, element.parents('.userpro-input'), element.parents('.userpro-dashboard').data('password_not_strong') );
			} else if ( original != element.val() ) {
				userpro_client_error( element, element.parents('.userpro-input'), jQuery(this).parents('.userpro-dashboard').data('passwords_do_not_match') );
			} else {
				userpro_client_valid(element, element.parents('.userpro-input'));
			}
		} else if (element.attr('type') == 'password' && original == '' && element.val() == '' ){
			userpro_clear_input(element);
		} else if ( ajaxcheck && element.val() ){
			userpro_side_validate( element, element.val(), ajaxcheck );
		} else if ( ajaxcheck && !element.val() ){
			userpro_clear_input( element );
		} else if ( element.val() && element.data('type') == 'antispam'){
			userpro_clear_input(element);
		} else if ( !ajaxcheck && element.attr('type') == 'text' ) {
			userpro_clear_input(element);

		}
		else if ( element.val() && element.data('sitekey') != ''){            //userpro_clear_input(element);
       }
		
	});

	jQuery('#userpro-add-widget').on('click', function(e){ 
				jQuery.ajax({
					url: ajaxurl,
					data: "action=upd_add_new_widget",
					dataType: 'JSON',
					type: 'POST',
					success:function(data){
						jQuery('#userpro-add-widget').hide();
						jQuery('#userpro-add-widget').after(data.html);
						jQuery('#userpro-widget-save').on('click',updb_widget_save);
					} 
				});
			
	});
	
	function updb_widget_save(t){
		
			var widget_id_save = jQuery(t).parent().parent().attr('id');
			
			if (typeof(widget_id_save) == 'undefined'){
				if( jQuery("#custom-widget-title").val() != "" && jQuery("#custom-widget-content").val() != "" ){
				 	var widget_title = jQuery('#custom-widget-title').val();
					var widget_content = jQuery('#custom-widget-content').val();
					
					var widget_id = widget_title.replace(/ /g,"_");
					
					jQuery.ajax({
						url: ajaxurl,
						data: "action=upd_save_new_widget&widget_id="+widget_id+"&widget_title="+widget_title+"&widget_content="+widget_content,
						dataType: 'JSON',
						type: 'POST',
						success:function(data){
							location.reload();
							//
							
						} 
					});
				}else{
					alert('Please enter the details for custom widget');
					jQuery("#custom-widget-title").focus();
				}
			}else{
				var widget_title = jQuery('#'+widget_id_save).find('.widget-title .widget-edit').val();
				var widget_content = jQuery('#'+widget_id_save).find('.widget-content .widget-edit').val();
				if( widget_title != "" && widget_content != "" ){
					jQuery.ajax({
						url: ajaxurl,
						data: "action=upd_save_new_widget&widget_id_save="+widget_id_save+"&widget_title="+widget_title+"&widget_content="+widget_content,
						dataType: 'JSON',
						type: 'POST',
						success:function(data){
							location.reload();					
						} 
					});
				}else{
					alert('Please enter the details for custom widget');
					jQuery("#custom-widget-title").focus();
				}
			}	
	}
	
	function updb_widget_edit(t){
		
		var widget_id = jQuery(t).parent().parent().attr('id');
		
		jQuery('#userpro-add-widget').hide();
			
		jQuery('#'+widget_id).find('.widget-title .display-widget-title').hide();
		jQuery('#'+widget_id).find('.widget-content .display-widget-content').hide();
		
		jQuery('#'+widget_id).find('.widget-title .widget-edit').show();
		jQuery('#'+widget_id).find('.widget-content .widget-edit').show();
		
		jQuery('#'+widget_id).find('.widget-action .widget_edit_btn').hide();
		
		jQuery('#'+widget_id).find('.widget-action div.widget_save_btn').css({"display":"inline-block"});
		
	}
	
	function updb_widget_delete(t){
		
		var widget_id = jQuery(t).parent().parent().attr('id');
				
		jQuery.ajax({
			url: ajaxurl,
			data: "action=updb_delete_widget&widget_id="+widget_id,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				location.reload();				
			} 
		});
	}
	
	jQuery(".widget_edit_btn").click( function(){
		updb_widget_edit(this);			
	});
	jQuery(".widget_save_btn").click( function(){
		updb_widget_save(this);	
	});
	jQuery(".widget_delete_btn").click( function(){
		var result;
		result = window.confirm("Are you sure you want to delete this widget ?");
		if(result){
			updb_widget_delete(this);
		}
	});
	
});