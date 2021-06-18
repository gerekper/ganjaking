function up_update_conversation( content, chat_with ){

	jQuery('.userpro-conv').find('.userpro-conv-ajax').remove();
	jQuery('.userpro-conv').removeClass('loading').prepend( content );

	jQuery('form.userpro-send-chat').find('input[type=button],button').hide();
	jQuery('form.userpro-send-chat').show();
	jQuery('form.userpro-send-chat').find('input#chat_with').val( chat_with );

	jQuery('.userpro-conv').height('auto');

	up_msg_adjust();

	userpro_chosen();

	jQuery('.userpro-conv-ajax').load(function(){
		jQuery(this).mCustomScrollbar("scrollTo", "bottom",{scrollInertia:0});
		jQuery('.userpro-conv-ajax').css({'opacity': 1});
	});
				
}

function up_msg_overlay( chat_from, chat_with ) {

	up_msg_cancel();
	
	jQuery('body').append('<div class="userpro-msg-overlay"></div>');
	jQuery('body').append('<div class="userpro-msg-overlay-loader"></div>');
	
	/* prepare ajax file */
	str = 'action=userpro_init_chat&chat_from=' + chat_from + '&chat_with=' + chat_with;
	jQuery.ajax({
		url: userpro_ajax_url,
		data: str,
		dataType: 'JSON',
		type: 'POST',
		success:function(data){
			if (jQuery('.userpro-msg-overlay-loader').length == 1) {
				jQuery('.userpro-msg-overlay-loader').remove();
				jQuery('body').append( data.html );

				/* limit textarea */
				jQuery('.userpro-msg-overlay-content textarea').autoResize({
					animate: {enabled: true},
					maxHeight: '90px'
				});
				
				/* update overlay, responsive, etc */
				up_msg_overlay_resize();
				userpro_chosen();
				
			}
		}
	});
	
}

function up_msg_overlay_show( user_id ) {

	up_msg_cancel();
	
	jQuery('body').append('<div class="userpro-msg-overlay"></div>');
	jQuery('body').append('<div class="userpro-msg-overlay-loader"></div>');
	
	/* prepare ajax file */
	str = 'action=userpro_show_chat&user_id=' + user_id;
	jQuery.ajax({
		url: userpro_ajax_url,
		data: str,
		dataType: 'JSON',
		type: 'POST',
		success:function(data){
			if (jQuery('.userpro-msg-overlay-loader').length == 1) {
				jQuery('.userpro-msg-overlay-loader').remove();
				jQuery('body').append( data.html );
				
				/* fancy textarea */
				jQuery('.userpro-msg-overlay-content textarea').autoResize({
					animate: {enabled: true},
					maxHeight: '90px'
				});
				
				/* limit content by scrollbar */
				up_msg_adjust();
				userpro_chosen();
				
			}
		}
	});
	
}

/*************************************Code for broadcast**************************************************************************/
function up_broadcast_msg( user_id ) {

	up_msg_cancel();
	
	jQuery('body').append('<div class="userpro-msg-overlay"></div>');
	jQuery('body').append('<div class="userpro-msg-overlay-loader"></div>');
	
	/* prepare ajax file */
	str = 'action=userpro_broadcast_msg&user_id=' + user_id;
	jQuery.ajax({
		url: userpro_ajax_url,
		data: str,
		dataType: 'JSON',
		type: 'POST',
		success:function(data){
			if (jQuery('.userpro-msg-overlay-loader').length == 1) {
				jQuery('.userpro-msg-overlay-loader').remove();
				jQuery('body').append( data.html );

				/* limit textarea */
				jQuery('.userpro-msg-overlay-content textarea').autoResize({
					animate: {enabled: true},
					maxHeight: '90px'
				});
				
				/* update overlay, responsive, etc */
				up_msg_overlay_resize();
				userpro_chosen();
				
			}
		}
	});
	
}

/* Prepare message */
function up_broadcast_msg_before_sending() {
	jQuery('.userpro-broadcast').addClass('inprogress');
	jQuery(".userpro-broadcast input[type=submit]").attr("disabled","disabled");
	jQuery('.userpro-broadcast .userpro-msg-submit img').show();
}

/* This is triggered when message is sent with form.userpro-send-chat */
function up_broadcast_msg_clear(){
	jQuery('.userpro-broadcast').removeClass('inprogress');
	jQuery('.userpro-broadcast textarea').val('');
	jQuery('.userpro-broadcast .userpro-msg-submit img').hide();
	jQuery('.userpro-broadcast textarea').autoResize({
		animate: {enabled: true},
		maxHeight: '90px'
	});
}

/* Remove overlay */
function up_broadcast_msg_cancel(){
	jQuery('.tipsy').remove();
	jQuery('.userpro-msg-overlay-content, .userpro-msg-overlay-loader, .userpro-msg-overlay').remove();
}

/* Cancel reply */
function up_broadcast_msg_cancel_quick_reply(){
	jQuery('.userpro-broadcast').css({'display' : 'none'});
}


/*************************************Code end**************************************************************************/

/* JS responsive */
function up_msg_adjust(){
	
	if ( jQuery(window).width() > 800) {
	jQuery('.userpro-msg-body.alt').css({'max-height': jQuery(window).height() - 300 + 'px'});
	} else {
	jQuery('.userpro-msg-body.alt').css({'max-height': jQuery('.userpro-msg-overlay-content').innerHeight() - jQuery('.userpro-msg-user').innerHeight() - 15 + 'px'});
	}
	
	jQuery('.userpro-msg-body.alt').mCustomScrollbar('destroy');
	jQuery('.userpro-msg-body.alt').mCustomScrollbar({
		theme:"dark-2",
		advanced:{
			updateOnContentResize: true,
			autoScrollOnFocus: false,
		}
	});
        
	if ( jQuery(window).width() > 800) {
		jQuery('.userpro-conv-ajax').css({'height' :  jQuery(window).height() - 400 - jQuery('.userpro-send-form').innerHeight() + 'px' });
	} else {
		jQuery('.userpro-conv-ajax').css({'height' :  jQuery(window).height() - jQuery('.userpro-msg-user').innerHeight() - 200 - jQuery('.userpro-send-form').innerHeight() + 'px' });
	}
        console.log(jQuery('.userpro-conv-ajax').hasClass('up_scroll_down'));
	if(jQuery('.userpro-conv-ajax').hasClass('up_scroll_down') == false){
            //alert('in');
	jQuery('.userpro-conv-ajax').mCustomScrollbar('destroy');
	jQuery('.userpro-conv-ajax').mCustomScrollbar({
		theme:"dark-2",
		advanced:{
			updateOnContentResize: true,
		}
	});
    }
    else {
	jQuery('.up_scroll_down').scrollTop(jQuery('.up_scroll_down')[0].scrollHeight);
    }
	up_msg_overlay_resize();
	
}

/* Responsive overlay */
function up_msg_overlay_resize(){
	jQuery('.userpro-msg-overlay-content').animate({
		'opacity' : 1,
		'margin-top' : '-' + jQuery('.userpro-msg-overlay-content').innerHeight() / 2 + 'px'
	});
}

/* Remove overlay */
function up_msg_cancel(){
	jQuery('.tipsy').remove();
	jQuery('.userpro-msg-overlay-content, .userpro-msg-overlay-loader, .userpro-msg-overlay').remove();
}

/* Cancel reply */
function up_msg_cancel_quick_reply(){
	jQuery('.userpro-send-chat').css({'display' : 'none'});
}

/* Prepare message */
function up_msg_before_sending() {
	jQuery('.userpro-send-chat').addClass('inprogress');
	jQuery(".userpro-send-chat input[type=submit]").attr("disabled","disabled");
	jQuery('.userpro-send-chat .userpro-msg-submit img').show();
}

/* This is triggered when message is sent with form.userpro-send-chat */
function up_msg_clear(){
	jQuery('.userpro-send-chat,.userpro-send-chat-widget').removeClass('inprogress');
	jQuery('.userpro-send-chat textarea,.userpro-send-chat-widget textarea').val('');
	jQuery('.userpro-send-chat .userpro-msg-submit img,.userpro-send-chat-widget .userpro-msg-submit img').hide();
	jQuery('.userpro-send-chat textarea,.userpro-send-chat-widget textarea').autoResize({
		animate: {enabled: true},
		maxHeight: '90px'
	});
}

function up_connection_msg(user_id){

	up_msg_cancel();
	
	jQuery('body').append('<div class="userpro-msg-overlay"></div>');
	jQuery('body').append('<div class="userpro-msg-overlay-loader"></div>');
	
	/* prepare ajax file */
	str = 'action=up_connection_msg&user_id=' + user_id;
	jQuery.ajax({
		url: userpro_ajax_url,
		data: str,
		dataType: 'JSON',
		type: 'POST',
		success:function(data){
			if (jQuery('.userpro-msg-overlay-loader').length == 1) {
				jQuery('.userpro-msg-overlay-loader').remove();
				jQuery('body').append( data.html );

				/* limit textarea */
				jQuery('.userpro-msg-overlay-content textarea').autoResize({
					animate: {enabled: true},
					maxHeight: '90px'
				});
				
				/* update overlay, responsive, etc */
				up_msg_overlay_resize();
				userpro_chosen();
				
			}
		}
	});
	
}


/* Prepare message */
function up_connection_msg_before_sending() {
	jQuery('.userpro-connection').addClass('inprogress');
	jQuery(".userpro-connection input[type=submit]").attr("disabled","disabled");
	jQuery('.userpro-connection .userpro-msg-submit img').show();
}

/* This is triggered when message is sent with form.userpro-send-chat */
function up_connection_msg_clear(){
	jQuery('.userpro-connection').removeClass('inprogress');
	jQuery('.userpro-connection textarea').val('');
	jQuery('.userpro-connection .userpro-msg-submit img').hide();
	jQuery('.userpro-connection textarea').autoResize({
		animate: {enabled: true},
		maxHeight: '90px'
	});
}

/* Remove overlay */
function up_connection_msg_cancel(){
	jQuery('.tipsy').remove();
	jQuery('.userpro-msg-overlay-content, .userpro-msg-overlay-loader, .userpro-msg-overlay').remove();
}

/* Cancel reply */
function up_connection_msg_cancel_quick_reply(){
	jQuery('.userpro-connection').css({'display' : 'none'});
}


jQuery(document).ready(function() {

	/* Changing textarea state */
	jQuery(document).on('change keyup paste', 'form.userpro-send-chat textarea', function(event){
		var message = jQuery(this).val();
		
		var emptyTextarea = false
		jQuery('form.userpro-send-chat textarea').each(function(){
			if(message.replace(/\n/g, "") =="" || jQuery.trim(message) == 0 )
			{
				emptyTextarea = true;
			}
		});
		if(!emptyTextarea)
		{
			jQuery('.userpro-msg-result').hide();
			if (event.keyCode != 13) {
				jQuery(".userpro-send-chat input[type=submit]").removeAttr('disabled');
			}
		} else {
			jQuery(".userpro-send-chat input[type=submit]").attr("disabled","disabled");
		}
	});

	/* Enter to send message - SHIFT enter for new line */
	jQuery(document).on('keypress', 'form.userpro-send-chat textarea', function(event) {
		if (event.keyCode == 13 || event.shiftKey)
		{	
			var objDiv = document.getElementById("chat_body");
			objDiv.scrollTop = objDiv.scrollHeight;	
				
		}	
		
	 	var message = jQuery(this).val();
				
		if (event.keyCode == 13 && !event.shiftKey && obj.value==1) {
			
			event.preventDefault();
			var emptyTextarea = false
			if(message.replace(/\n/g, "") =="" || jQuery.trim(message) == 0 ){
					emptyTextarea = true;
			}
			
			if(!emptyTextarea)
			{
				jQuery('.userpro-msg-result').hide();
				jQuery('form.userpro-send-chat').submit();
			} else {
				jQuery(".userpro-send-chat input[type=submit]").attr("disabled","disabled");
			}
			return false;
		}
		
	});
	
	/* Responsive JS
	jQuery(window).smartresize(function(){
		up_msg_adjust();
	}); */

	/* Delete conversation */
	jQuery(document).on('click','.userpro-msg-delete a',function(){
		var chat_from = jQuery(this).data('chat_from');
		var chat_with = jQuery(this).data('chat_with');
		jQuery(this).parents('.userpro-msg-col').fadeOut(function(){ jQuery(this).remove() });
		jQuery.ajax({
			url: userpro_ajax_url,
			data: "action=userpro_delete_conversation&chat_from=" + chat_from + "&chat_with=" + chat_with,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
			
			}
		});
	});

	/* Back to inbox */
	jQuery(document).on('click', 'a.userpro-back-to-inbox',function(){
		jQuery(this).hide();
		jQuery('form.userpro-send-chat').hide().appendTo('.userpro-msg-body');
		jQuery('.userpro-conv').remove();
		jQuery('.userpro-msg-body').show();
		up_msg_overlay_resize();
	});
	
	/* read conversation */
	jQuery(document).on('click', '.userpro-msg-col:not(.disabled)', function(){
		
		jQuery(".userpro-msg-field textarea").css("min-height","50px");
		jQuery('.userpro-msg-body').hide();
		jQuery('.userpro-msg-body').after('<div class="userpro-conv"></div>');
		jQuery('.userpro-msg-result').hide();
		jQuery('form.userpro-send-chat').hide();
		jQuery('form.userpro-send-chat').appendTo('.userpro-conv');
		jQuery('.userpro-conv').animate({'height' : '200px'}).addClass('loading');
		var chat_from = jQuery(this).data('chat_from');
		var chat_with = jQuery(this).data('chat_with');
		jQuery.ajax({
			url: userpro_ajax_url,
			data: "action=userpro_view_conversation&chat_from=" + chat_from + "&chat_with=" + chat_with,
			dataType: 'JSON',
			type: 'POST',
			error: function(){

			},
			success:function(data){

				jQuery('a.userpro-back-to-inbox').fadeIn();
				up_update_conversation( data.html, chat_with );
				
			}
		});
		
	});

	jQuery(document).on('click', '.userpro-msg-user-name span.block', function(e){
		var block_user = jQuery(this).data('block_user');
		var user = jQuery(this).data('user');
		var chat_with= jQuery(this).data('user');
		var retVal = confirm("Are you sure you want to block this user? (Once you block the user, all conversation will be deleted !)");
   	        if( retVal == true ){
		jQuery.ajax({
			url: userpro_ajax_url,
			data: "action=userpro_block_user&block_user=" + block_user+ "&user=" + user ,
			dataType: 'JSON',
			type: 'POST',
			error: function(){

			},
			success:function(data){

				alert("User blocked");
				jQuery('.userpro-msg-body').hide();
				jQuery('.userpro-msg-body').after('<div class="userpro-conv"></div>');
				jQuery('.userpro-msg-result').hide();
				jQuery('form.userpro-send-chat').hide();
				jQuery('.userpro-notifier').hide(); 
				jQuery('.userpro-msg-user').hide(); 
				jQuery('.userpro-msg-overlay').hide(); 
				
				
				
			}
		});
		}
		return false;
	});

	
	/* init Quick Reply */
	jQuery(document).on('click', '.userpro-msg-user-name span.bubble', function(e){
		e.stopPropagation();
		jQuery(this).parents('.userpro-msg-col').addClass('disabled');
		var elem = jQuery(this).parents('.userpro-msg-user-info');
		jQuery('.userpro-msg-result').hide();
		jQuery('form.userpro-send-chat').appendTo( elem ).show();
		jQuery('form.userpro-send-chat').find('input[type=button],button').show();
		var chat_with = jQuery(this).data('chat_with');
		jQuery('form.userpro-send-chat').find('input#chat_with').val( chat_with );
		jQuery('form.userpro-send-chat').find('textarea').focus();
		return false;
	});
	
	/* Show chats notifier */
	if (jQuery('.userpro-notifier').length) {
		jQuery('.userpro-notifier').delay(1000).animate({'bottom' : '0px'}, 300).animate({'bottom' : '-10px'}, 300).animate({'bottom' : '-4px'}, 300, function() { jQuery('.userpro-notifier-thumbs').fadeIn('slow'); });
	}
	
	/* Init chat */
	jQuery(document).on('click', '.userpro-init-chat', function(){
		chat_from = jQuery(this).data('chat_from');
		chat_with = jQuery(this).data('chat_with');
		up_msg_overlay( chat_from, chat_with );
	});
	
	
	jQuery(document).on('click', '.userpro-unblock-user', function(){
		var elem = jQuery(this);
		user_id = jQuery(this).data('user_id');
		jQuery.ajax({
			url: userpro_ajax_url,
			data: "action=userpro_unblock_user&user_id=" + user_id,
			type: 'POST',
			error: function(){

			},
			success:function(data){

				jQuery(".unblock_user").hide();
			
				 elem.parent().parent().append(data);
			}
		});
	
	});
	/* Show chat */
	jQuery(document).on('click', '.userpro-show-chat', function(){
		user_id = jQuery(this).data('user_id');
		up_msg_overlay_show( user_id );
	});
	
	/* Submitting message */
	jQuery(document).on('submit', 'form.userpro-send-chat:not(.inprogress)', function(e){
		e.preventDefault();
		form = jQuery(this);
		
		/* before sending message */
		up_msg_before_sending();
		
		var chat_with = form.find('input#chat_with').val();
		
		/* sending message */
		jQuery.ajax({
			url: userpro_ajax_url,
			data: form.serialize() + "&action=userpro_start_chat",
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				
				up_msg_clear();
				
				if (jQuery('.userpro-conv').length == 0){
					jQuery('.userpro-msg-result').html(data.message).fadeIn('slow');
				} else {
					up_update_conversation( data.html, chat_with );
				}

			}
		});
		return false;
	});
	
	/* Close overlay */
	jQuery(document).on('click', '.userpro-msg-overlay, a.userpro-msg-close',function(){
		up_msg_cancel();
                location.reload();
	});

	/* Cancel message */
	jQuery(document).on('click', '.userpro-send-chat input[type=button]',function(){
		if (jQuery(this).parents('.userpro-msg-user-info').length > 0){
			jQuery(this).parents('.userpro-msg-col').removeClass('disabled');
			up_msg_cancel_quick_reply();
		} else {
			up_msg_cancel();
		}
	});
	
	
	jQuery(document).on('mouseenter', '.userpro-msg-user-name span.block', function(){
		parent = jQuery(this).parents('.userpro-msg-col');
		parent.find('.userpro-msg-user-name span.block-text').show();
		parent.find('.userpro-msg-view').hide();
	});
	
	jQuery(document).on('mouseleave', '.userpro-msg-user-name span.block', function(){
		parent = jQuery(this).parents('.userpro-msg-col');
		parent.find('.userpro-msg-user-name span.block-text').hide();
		parent.find('.userpro-msg-view').show();
	});

	
	/* Quick reply hover */
	jQuery(document).on('mouseenter', '.userpro-msg-user-name span.bubble', function(){
		parent = jQuery(this).parents('.userpro-msg-col');
		parent.find('.userpro-msg-user-name span.bubble-text').show();
		parent.find('.userpro-msg-view').hide();
	});
	
	jQuery(document).on('mouseleave', '.userpro-msg-user-name span.bubble', function(){
		parent = jQuery(this).parents('.userpro-msg-col');
		parent.find('.userpro-msg-user-name span.bubble-text').hide();
		parent.find('.userpro-msg-view').show();
	});
/****************************************Code for broadcast message**************************************************/
	jQuery(document).on('click', '.userpro-broadcast-msg', function(){
		user_id = jQuery(this).data('user_id');
		up_broadcast_msg( user_id );
	});

	jQuery(document).on('submit', 'form.userpro-broadcast:not(.inprogress)', function(e){
		e.preventDefault();
		form = jQuery(this);
		
		/* before sending message */
		up_broadcast_msg_before_sending();
		
		var user_id = form.find('input#user_id').val();
		var confirmAns = confirm(translated_text_for_alert);
		if(confirmAns==true)
		{
			var elem = '<div class="userpro-msg-notice">Broadcasting message...</div>';
			jQuery('.userpro-msg-result').html(elem).fadeIn('slow');
			jQuery.ajax({
				url:userpro_ajax_url,
				data: form.serialize() + "&action=userpro_broadcast",
				type: 'POST',
				success:function(data){
					if(jQuery('.userpro-msg-new').length>0){
						up_broadcast_msg_clear();
					}	
					if (jQuery('.userpro-conv').length == 0){
						jQuery('.userpro-msg-notice').html(data);
					} 
					window.clearInterval(check_int);
					},
				});
		var check_int = setInterval(function(){
				jQuery.ajax({
					url : userpro_ajax_url,
					data : 'action=userpro_check_broadcast_progress',
					type : 'POST',
					success:function(data){
						if(jQuery('.userpro-msg-new').length>0){
							up_broadcast_msg_clear();
						}	
						if (jQuery('.userpro-conv').length == 0){
							jQuery('.userpro-msg-notice').html(data);
						} 
					}
				})
			},30000);
		}
		else
		{
			jQuery('.userpro-broadcast .userpro-msg-submit img').hide();
			jQuery(".userpro-broadcast input[type=submit]").removeAttr('disabled');
			jQuery('form.userpro-broadcast').removeClass('inprogress');
		}
		return false;
	});

	jQuery(document).on('change keyup paste', 'form.userpro-broadcast textarea', function(event){
		var message = jQuery(this).val();
		
		var emptyTextarea = false
		jQuery('form.userpro-broadcast textarea').each(function(){
			if(message.replace(/\n/g, "") =="" || jQuery.trim(message) == 0 )
			{
				emptyTextarea = true;
			}
		});
		if(!emptyTextarea)
		{
			jQuery('.userpro-msg-result').hide();
			if (event.keyCode != 13) {
				jQuery(".userpro-broadcast input[type=submit]").removeAttr('disabled');

			}
		} else {
			jQuery(".userpro-broadcast input[type=submit]").attr("disabled","disabled");
		}
	});

	jQuery(document).on('keypress', 'form.userpro-broadcast textarea', function(event) {
		var message = jQuery(this).val();
		
		if (event.keyCode == 13 && !event.shiftKey) {
			event.preventDefault();
			var emptyTextarea = false
			if(message.replace(/\n/g, "") =="" || jQuery.trim(message) == 0 ){
					emptyTextarea = true;
			}
			if(!emptyTextarea)
			{
				jQuery('.userpro-msg-result').hide();
				jQuery('form.userpro-broadcast').submit();
			} else {
				jQuery(".userpro-broadcast input[type=submit]").attr("disabled","disabled");
			}
			return false;
		}
	});
	
	/* Cancel message */
	jQuery(document).on('click', '.userpro-broadcast input[type=button]',function(){
		if (jQuery(this).parents('.userpro-msg-user-info').length > 0){
			jQuery(this).parents('.userpro-msg-col').removeClass('disabled');
			up_broadcast_msg_cancel_quick_reply();
		} else {
			up_broadcast_msg_cancel();
		}
	});
	
	/* Code for sending message to connections */
	
	jQuery(document).on('click', '.userpro-msg-connections',function(){
		user_id = jQuery(this).data('user_id');
		up_connection_msg( user_id );
	});
	
	
	
	jQuery(document).on('submit', 'form.userpro-connection:not(.inprogress)', function(e){
		e.preventDefault();
		form = jQuery(this);
		
		/* before sending message */
		up_connection_msg_before_sending();
		
		var user_id = form.find('input#user_id').val();
		var confirmAns = confirm(translated_text_for_connection_msg_alert);
		if(confirmAns==true)
		{
			var elem = '<div class="userpro-msg-notice">Sending message to connections...</div>';
			jQuery('.userpro-msg-result').html(elem).fadeIn('slow');
			jQuery.ajax({
				url:userpro_ajax_url,
				data: form.serialize() + "&action=userpro_msg_connection",
				type: 'POST',
				success:function(data){
					if(jQuery('.userpro-msg-new').length>0){
						up_connection_msg_clear();
					}	
					if (jQuery('.userpro-conv').length == 0){
						jQuery('.userpro-msg-notice').html(data);
					} 
				},
			});
		}
		else
		{
			jQuery('.userpro-connection .userpro-msg-submit img').hide();
			jQuery(".userpro-connection input[type=submit]").removeAttr('disabled');
			jQuery('form.userpro-connection').removeClass('inprogress');
		}
		return false;
	});

	jQuery(document).on('change keyup paste', 'form.userpro-connection textarea', function(event){
		var message = jQuery(this).val();
		
		var emptyTextarea = false
		jQuery('form.userpro-connection textarea').each(function(){
			if(message.replace(/\n/g, "") =="" || jQuery.trim(message) == 0 )
			{
				emptyTextarea = true;
			}
		});
		if(!emptyTextarea)
		{
			jQuery('.userpro-msg-result').hide();
			if (event.keyCode != 13) {
				jQuery(".userpro-connection input[type=submit]").removeAttr('disabled');

			}
		} else {
			jQuery(".userpro-connection input[type=submit]").attr("disabled","disabled");
		}
	});

	jQuery(document).on('keypress', 'form.userpro-connection textarea', function(event) {
		var message = jQuery(this).val();
		
		if (event.keyCode == 13 && !event.shiftKey) {
			event.preventDefault();
			var emptyTextarea = false
			if(message.replace(/\n/g, "") =="" || jQuery.trim(message) == 0 ){
					emptyTextarea = true;
			}
			if(!emptyTextarea)
			{
				jQuery('.userpro-msg-result').hide();
				jQuery('form.userpro-connection').submit();
			} else {
				jQuery(".userpro-connection input[type=submit]").attr("disabled","disabled");
			}
			return false;
		}
	});
	
	/* Cancel message */
	jQuery(document).on('click', '.userpro-connection input[type=button]',function(){
		if (jQuery(this).parents('.userpro-msg-user-info').length > 0){
			jQuery(this).parents('.userpro-msg-col').removeClass('disabled');
			up_connection_msg_cancel_quick_reply();
		} else {
			up_connection_msg_cancel();
		}
	});
	
	
	
	/* Code end for sending message to connections */
	
	
	/* Send Message via Dashboard Widget */
	jQuery("#send-message").click( function(){
	
		upm_send_message_widget(this);	
	});
		
	function upm_send_message_widget(elm){
		if( jQuery("#chat_body").val() != "" ){
			
			form = jQuery(".userpro-send-chat-widget");
			
			var chat_with = form.find('input#chat_with').val();
			
			/* sending message */
			jQuery.ajax({
				url: userpro_ajax_url,
				data: form.serialize() + "&action=userpro_start_chat",
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
					
					up_msg_clear();
					
					if (jQuery('.userpro-conv').length == 0){
						jQuery('.userpro-msg-result').html(data.message).fadeIn('slow');
					} else {
						up_update_conversation( data.html, chat_with );
					}
				}
			});
			return false;
		}
		else{
			alert("Please enter a message");
		}
	}
});


/****************************************Code end**************************************************/