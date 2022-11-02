jQuery( "#input-dialog-date" ).datepicker({ dateFormat: 'yy-mm-dd' });

function open_dropshipper_dialog(my_id) {
	jQuery('#input-dialog-date').val(jQuery('#dropshipper_shipping_info_'+my_id+' .dropshipper_date').html());
	jQuery('#input-dialog-trackingnumber').val(jQuery('#dropshipper_shipping_info_'+my_id+' .dropshipper_tracking_number').html());
	jQuery('#input-dialog-shippingcompany').val(jQuery('#dropshipper_shipping_info_'+my_id+' .dropshipper_shipping_company').html());
	jQuery('#input-dialog-notes').val(jQuery('#dropshipper_shipping_info_'+my_id+' .dropshipper_notes').html());
	jQuery('#input-dialog-template').dialog({
		title: 'Shipping Info',
		buttons: [{
			text: 'Save',
			click: function() {
				js_save_dropshipper_shipping_info(my_id, {
					date: jQuery('#input-dialog-date').val(),
					tracking_number: jQuery('#input-dialog-trackingnumber').val(),
					shipping_company: jQuery('#input-dialog-shippingcompany').val(),
					notes: jQuery('#input-dialog-notes').val()
				});
				jQuery( this ).dialog( "close" );
			}
		}]
	});
} 

function js_save_dropshipper_shipping_info(my_order_id, my_info) {
	var data = {
		action: 'dropshipper_shipping_info_edited',
		id: my_order_id,
		info: my_info
	};
	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, data, function(response) {
		if(response == 'true'){
			jQuery('#dropshipper_shipping_info_'+my_order_id+' .dropshipper_date').html(jQuery('#input-dialog-date').val());
			jQuery('#dropshipper_shipping_info_'+my_order_id+' .dropshipper_tracking_number').html(jQuery('#input-dialog-trackingnumber').val());
			jQuery('#dropshipper_shipping_info_'+my_order_id+' .dropshipper_shipping_company').html(jQuery('#input-dialog-shippingcompany').val());
			jQuery('#dropshipper_shipping_info_'+my_order_id+' .dropshipper_notes').html(jQuery('#input-dialog-notes').val());
			location.reload();
		}
	});
}


// Ajax callback for send aliexpress API key in admin mailbox

jQuery(document).ready(function() { 
   jQuery("#generate_ali_key").click(function () {
	    var data = {
	        'action': 'email_ali_api_key',

	    };
	    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	    jQuery.post(ajaxurl, data, function(response) {
	        // Output the response which should be 'Hellow World'
	        alert('Your Woo AliExpress API Key has been sent to your admin email id. Please check your inbox/spam folder!');
	        jQuery('#ali_api_key').html('Your Woo AliExpress API Key for '+document.location.hostname+' is: <b>'+ response+'</b>');
	        jQuery('#hide_key').show();
	    });

    });
});

jQuery(document).ready(function() { 
	
	jQuery( '.drop_color' ).wpColorPicker(); 

	jQuery(document).on('click', '.hidecbe', function() {


	  	    var data = {
	        'action': 'hide_cbe_message',
			'cbe_hideoption' : 'yes'

	    };
	    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	    jQuery.post(ajaxurl, data, function(response) {
	        // Output the response which should be 'Hellow World'
	       // alert('Your Woo AliExpress API Key has been sent to your admin email id. Please check your inbox/spam folder!');
	        //jQuery('#ali_api_key').html('Your Woo AliExpress API Key for '+document.location.hostname+' is: <b>'+ response+'</b>');
	        jQuery('#cbe_message').hide();
	    });

    });
	
	jQuery('.wc-dropship-setting-tabs li').click(function(){
		jQuery('.wc-dropship-setting-tabs li').removeClass('active');
		jQuery('.drop-setting-section').removeClass('active');
		jQuery(this).addClass('active');
		var tabId = jQuery(this).data('id');
		jQuery('#'+tabId).addClass('active');
	});
});
// Ajax callback for Aliexpress related product open in diffrent tab

jQuery(document).ready(function($) { 
   $("#send_supplier_email").click(function () {
	   
	   var termval = [];
	   $('.term_id_value:checked').each(function() {
			termval.push($(this).val()); 
	   });
	   if(termval !=''){	
		   $(this).html('Sending...');		
		   var ajaxurl = $(this).data('ajax-url');
		   var order_id = $(this).data('order-id');	   
		   $.ajax({
				url: ajaxurl,
				data: { termval : termval, order_id : order_id, action : 'send_supplier_email' },
				type: 'post',
				dataType: 'json',
				success: function(response) {
					$("#send_supplier_email").html('Successfully Send');		
				},
				error: function(e){
					$("#send_supplier_email").html('Successfully Send');
				}
			});
	   }else{
		   alert('Please select at least one supplier');
	   }
	});
	
	$('.miscellaneous_packing_slip_options_master_checkbox').click(function () {
		if($(this).is(':checked')){
			if(document.getElementById("email_order_note").value == ''){
				document.getElementById("email_order_note").value = "Please see the attached PDF. Thank you!";
			}
		}else{
			document.getElementById("email_order_note").value = "";
		}
	   /*if($(this).is(':checked')){
		  $('.miscellaneous_packing_slip_options_checkbox_false').removeAttr("disabled");
		  $('.miscellaneous_packing_slip_options_checkbox').removeAttr("disabled");	
		  $('.miscellaneous_packing_slip_options_checkbox').prop('checked', true);
		  $('.inner-toggle').show();
	   }else{
		  $('.inner-toggle').hide(); 
		  $('.miscellaneous_packing_slip_options_checkbox_false').attr("disabled", true);
		  $('.miscellaneous_packing_slip_options_checkbox').attr("disabled", true);
		  $('.miscellaneous_packing_slip_options_checkbox').prop('checked', false);
		  $('.miscellaneous_packing_slip_options_checkbox').removeProp( "luggageCode" );
	   }*/
	});
	
	$('.miscellaneous_packing_slip_options_checkbox').click(function () {
		var dataId = $(this).data('id');
	   if($(this).is(':checked')){	
		  $('.'+dataId).show();
	   }else{
		  $('.'+dataId).hide(); 
	   }
	   
	});

	$('.view_order').click(function () {
		var dataId = $(this).data('id');
	   if($(this).is(':checked')){	
		  $('.'+dataId).show();
	   }else{
		  $('.'+dataId).hide(); 
	   }
	   
	});
	
	$('#show_logo').click(function (){
	   if($('#show_logo').is(':checked')){
		  $('.show_logo').show();
	   }else{
		  $('.show_logo').hide(); 
	   }
	});
	
});

$(document).ready(function(){
    $(".order_button_email").click(function(){
        $(".order_button_email").attr("checked", "checked");
    });
});

/*jQuery(document).ready(function() { 
   jQuery("#opmc_ali_place_order").click(function () {
	   var order_id = jQuery('#order_id').val();	   
	   var	$ = jQuery;
	   var ajaxurl = jQuery("#opmc_ali_place_order").attr("url");
	    data = { action: 'get_order_data', id: order_id};
	    $.ajax({
	        url: ajaxurl,
	        data:data,
	        type: 'post',
	        dataType: 'json',
	        success: function(response) {
	    		for (var i = 0; i < response.length; i++) {

	                window.open(response[i], '_blank');
	        	}
		    }

		});
	});
});*/