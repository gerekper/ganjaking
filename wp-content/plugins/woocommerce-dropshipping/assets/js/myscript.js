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

	var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        jQuery('.wc-dropship-setting-tabs li').removeClass('active');
        jQuery('.drop-setting-section').removeClass('active');
        jQuery('[data-id="' + activeTab + '"]').addClass('active');
        jQuery('#' + activeTab).addClass('active');
    }

    // Click event to handle tab switching
    jQuery('.wc-dropship-setting-tabs li').click(function() {
        jQuery('.wc-dropship-setting-tabs li').removeClass('active');
        jQuery('.drop-setting-section').removeClass('active');
        jQuery(this).addClass('active');
        var tabId = jQuery(this).data('id');
        jQuery('#' + tabId).addClass('active');

        // Store active tab in local storage
        localStorage.setItem('activeTab', tabId);
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


	/* Related to Price Calculator */

	jQuery(document).on('focusout', '.from_val', function() {

        var current = jQuery(this).val();
        var to_value = jQuery(this).closest('tr').find('.dynamic_to_value').val();
       	var to_value_numb = Number(to_value);
       	var current_numb = Number(current);
       
        if (current_numb > to_value_numb) {

        	 if (to_value_numb != '') {
        	 	
        		alert('"To" must always be greater than or equal to the "From" value.');
        		jQuery(this).val('');
        	 }
        }
    });
       
   jQuery(document).on('focusout', '.to_val', function() {

        var current1 = jQuery(this).val();
        var from_value = jQuery(this).closest('tr').find('.dynamic_from_value').val();
        var from_value_numb = Number(from_value);
       	var current1_numb = Number(current1);
        if (current1_numb < from_value_numb) {
        	alert('"To" must always be greater than or equal to the "From" value.');
        	jQuery(this).val('');
        	
        } 
        
    });

	jQuery(document).on('change', '.bar_cal', function() {
		//$(".dynamic_to_value").on("keyup change", function(e) {
		
		var prft_prcnt_val = jQuery('#profit_percent_value').val();
		var prft_dolr_val = jQuery('#profit_doller_value').val();
		var fee_prcnt_val = jQuery('#fee_percent_value').val();
		var fee_dolr_val = jQuery('#fee_doller_value').val();

		if (prft_prcnt_val != '') {
			prft_prcnt_val = prft_prcnt_val;
		} else {

			prft_prcnt_val = 0;
		}

		if (prft_dolr_val != '') {

			prft_dolr_val = prft_dolr_val;
		} else {

			prft_dolr_val = 0;
		}

		if (fee_prcnt_val != '') {

			fee_prcnt_val = fee_prcnt_val;
		} else {

			fee_prcnt_val = 0;
		}

		if (fee_dolr_val != '') {

			fee_dolr_val = fee_dolr_val;
		} else {

			fee_dolr_val = 0;
		}

		var pcnt_profit = parseFloat(prft_prcnt_val) / parseFloat(100) * parseFloat(100);
		
		var all_prft_some = parseFloat(100) + parseFloat(pcnt_profit) + parseFloat(prft_dolr_val) + parseFloat(fee_dolr_val);
		var final_some = parseFloat(all_prft_some) / parseFloat(100) * parseFloat(100);

		var devide_left = parseFloat(100) - parseFloat(fee_prcnt_val);

		var right_calculesn = parseFloat(final_some) * parseFloat(100) / parseFloat(devide_left);
		var final_prcnt_fee = parseFloat(right_calculesn) - parseFloat(final_some);
		
		jQuery('#final_prcnt_fee').text('$' + final_prcnt_fee.toFixed(2));
		jQuery('#fee_dolr_val_fix').text('$' + fee_dolr_val);

		jQuery('#fee_prcnt_val').text(fee_prcnt_val + '% fee');
		jQuery('#fee_dolr_val').text('$' + fee_dolr_val + ' fee');
		
		jQuery('#right_calculesn').text('$'+right_calculesn.toFixed(2));
		var green = parseFloat(100) - parseFloat(prft_prcnt_val);
		var blue = prft_prcnt_val;
		var nevy_blue = prft_dolr_val;
		jQuery('#blue_progress').text(blue + '%');
		
		if (prft_prcnt_val != 0 || prft_dolr_val != 0) {
			jQuery("#blue_progress").append('<span id="profir_margin">Profit Margin </span>');
		}

		jQuery('#percent_fee_bar').text('$' + nevy_blue);

		jQuery('#green_progress').css("width", green+ '%');
		jQuery('#blue_progress').css("width", blue+ '%');
		jQuery('#percent_fee_bar').css("width", nevy_blue+ '%');
		both = parseFloat(blue) + parseFloat(nevy_blue);

		if (parseFloat(green) > 90 || both < 12) {
			jQuery('#profir_margin').css("right", '0'); 
		}

		if (parseFloat(green) < 20 || both > 90) {
			jQuery('#profir_margin').css("right", '0'); 
		}
				
	});

	jQuery('.wc-dropship-setting-tabs li#prices_cal').click(function(){
		if(jQuery('#prices_cal').hasClass('active')){ 
		 	var prft_prcnt_vals = jQuery('#profit_percent_value').val();
			var prft_dolr_vals = jQuery('#profit_doller_value').val();

		 	var green = parseFloat(100) - parseFloat(prft_prcnt_vals);
			var bluee = parseFloat(prft_prcnt_vals);
			both = parseFloat(bluee) + parseFloat(prft_dolr_vals);
			
			if (parseFloat(green) > 90 || both < 12) {
				jQuery('#profir_margin').css("right", '0'); 
			}
		 	if (parseFloat(green) < 15 || bluee > 90) {

				jQuery('#profir_margin').css("right", '0'); 
			}
		}
	});
	
	$('#dynamic_profit_margin').click(function (){

	   if($('#dynamic_profit_margin').is(':checked')){
	   		$('#profit_percent_value').prop('disabled', true);
			$('#profit_doller_value').prop('disabled', true);
			$('.dynamic_profit_margin_section').show();
	   } else {
			$('.dynamic_profit_margin_section').hide();
			$('#profit_percent_value').prop('disabled', false);
			$('#profit_doller_value').prop('disabled', false);
			$('.requiredClass').prop('required', false);
	   }

	});

	$(document).on('keyup change', '.dynamic_to_value', function() {
		//$(".dynamic_to_value").on("keyup change", function(e) {
		var valexeed = $(this).val();
		if (valexeed > 9999999) {
			$("#amount_message").css('display', 'block');
			$("#amount_message").css('color', 'red');
			$('#addMoreRows').prop('disabled', true);
		} else {
			$('#addMoreRows').prop('disabled', false);
			$("#amount_message").css('display', 'none');
		}
	});
	
    $(document).ready(function(){ 
		
		$(document).on('focusout', '.clone_tds', function() {
			
			var returnValue = convertFieldsToString("clone_tds");
			console.log(returnValue);
			//var returnValue2 = convertFieldsToArray("clone_tds");
			//console.log(returnValue2);
			
			document.getElementById("profit_margin_hidden").innerHTML = returnValue;
			
		});
		
	});
		
	function convertFieldsToString(elementclass){
		var arr = "";
		var nCount = 0;
		var nIndex = 0;
		
		$("."+elementclass).get().forEach(function(entry, index, array) {
			
			var vValue = $(array[index]).val().trim();
			var vElement = $(array[index]).attr('data');
			if(vValue == ""){ 
				vValue = "null";
				$(array[index]).css('border','3px solid red');
			} else {
				$(array[index]).css('border','1px solid black');
			}
			
			
			if(index === 0){
				arr = vValue + "_";
			} else if((index+1) % 4 === 0){
				arr = arr + vValue + "~";
			} else {
				arr = arr + vValue + "_";
			}
			
			nCount++;
			
		});
		
		// remove last character:
		arr = arr.slice(0, -1);
		
		//console.log("===== Total Items ====");console.log(arr.length);
		//console.log("===== All Items ====");console.log(arr);
		return arr;
	}
		
	function convertFieldsToArray(elementclass){
		var arr = [];
		var nCount = 0;
		var nIndex = 0;
		
		$("."+elementclass).get().forEach(function(entry, index, array) {
			
			var vValue = $(array[index]).val();
			var vElement = $(array[index]).attr('data');
			
			if(vValue == ""){ 
				vValue = "null";
				$(array[index]).css('border','3px solid red');
			} else {
				$(array[index]).css('border','1px solid black');
			}
			
			if(arr[nIndex] === undefined){
				arr[nIndex] = [];
			}
			arr[nIndex][vElement] = vValue;
			
			if((index+1) % 4 === 0){
				nIndex++;
			}
			nCount++;
			
		});
		
		//console.log("===== Total Items ====");console.log(arr.length);
		//console.log("===== All Items ====");console.log(arr);
		
		return arr;
	}

	jQuery(document).on('click', '#addMoreRows', function() {
	    //get prev ele to clone
	    var $trrow = jQuery('#tr_clone').find('tr').length;
	    if ($trrow == 2) {
			jQuery('#removeRows').prop('disabled', false);
			//alert("You can't remove this rule but you can edit this rule!");

	    }
    	var clone_this = jQuery(this).parents('#tr_clone tr').prev('#tr_clone tr'); //get prev ele to clone
        var max_rows = clone_this[0].dataset.max_rows;
        var current_index = clone_this[0].dataset.index; //get current index
        var next = parseInt(current_index) + 1; //increment ele index

    	if (current_index < max_rows) {

	        clone_this[0].dataset.index = next; //increment ele index
	        
	        clone_this.find('fieldset input.dynamic_from_value').attr('name', 'dynamic_from_value[' + next + ']');
	        clone_this.find('fieldset input.dynamic_from_value').attr('id', 'dynamic_from_value_' + next );
	        
	        clone_this.find('fieldset input.dynamic_to_value').attr('name', 'dynamic_to_value[' + next + ']');
	        clone_this.find('fieldset input.dynamic_to_value').attr('id', 'dynamic_to_value_' + next );
	        
	        clone_this.find('fieldset input.dynamic_profit_percent_value').attr('name', 'dynamic_profit_percent_value[' + next + ']');
	        clone_this.find('fieldset input.dynamic_profit_percent_value').attr('id', 'dynamic_profit_percent_value_' + next );
	        
	        clone_this.find('fieldset input.dynamic_profit_doller_value').attr('name', 'dynamic_profit_doller_value[' + next + ']');
	        clone_this.find('fieldset input.dynamic_profit_doller_value').attr('id', 'dynamic_profit_doller_value_' + next );


	        jQuery(this).parents('#tr_clone tr').before(clone_this.clone()); // add new mapping row by cloing prev table

	        clone_this[0].dataset.index = current_index; //fix for prev table
	        
	        clone_this.find('fieldset input.dynamic_from_value').attr('name', 'dynamic_from_value[' + current_index + ']');
	        clone_this.find('fieldset input.dynamic_from_value').attr('id', 'dynamic_from_value_' + current_index );
	        
	        clone_this.find('fieldset input.dynamic_to_value').attr('name', 'dynamic_to_value[' + current_index + ']');
	        clone_this.find('fieldset input.dynamic_to_value').attr('id', 'dynamic_to_value_' + current_index );
	        
	        clone_this.find('fieldset input.dynamic_profit_percent_value').attr('name', 'dynamic_profit_percent_value[' + current_index + ']');
	        clone_this.find('fieldset input.dynamic_profit_percent_value').attr('id', 'dynamic_profit_percent_value_' + current_index );
	        
	        clone_this.find('fieldset input.dynamic_profit_doller_value').attr('name', 'dynamic_profit_doller_value[' + current_index + ']');
	        clone_this.find('fieldset input.dynamic_profit_doller_value').attr('id', 'dynamic_profit_doller_value_' + current_index );
	        var clone_this = jQuery(this).parents('#tr_clone tr').prev('#tr_clone tr');
        	clone_this.find("input").val("");
    	}
	   
	});

	jQuery(document).on('click', "#removeRows", function () {
		var $trrow = jQuery('#tr_clone').find('tr').length;
		if ($trrow == 2) {
			jQuery('#removeRows').prop('disabled', true);
			alert('The first dynamic rule cannot be removed. However, if you want, you can modify this first rule.');
			
        } else {
        	if(confirm('This will delete the last range. Are you sure you want to delete the last row from the list?') == true) {
        		var clone_this = jQuery(this).parents('#tr_clone tr').prev('#tr_clone tr');
	        	jQuery(clone_this).remove();
				
				// on remove of the row, modify hidden textarea filed value accordingly
				var returnValue = convertFieldsToString("clone_tds");
				console.log(returnValue);
				document.getElementById("profit_margin_hidden").innerHTML = returnValue;
				
	        	jQuery('#removeRows').prop('disabled', false)
        	}
	    }

     });
});

jQuery('.wc-dropship-setting-tabs li').click(function(){
	var tabId = jQuery(this).data('id');
	
	if (tabId == 'price_calculator_options') {
		jQuery('head').append('<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
		jQuery('.packing-slip-sections h4').css('top','-15px');
		jQuery('h3').css('font-size', '1.3rem');
		//jQuery('h3').css('line-height', '2.75');

		jQuery('body').css('color', '#3c434a');
		jQuery('body').css('background-color', '#f0f0f1');
		jQuery('body').css('font-size', '13px');
		jQuery('body').css('font-family', '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif');
		jQuery('table').css('border-collapse', 'initial');
		jQuery('.form-table .mappingBlocks td').css('padding', '5px 25px');
		jQuery('.btn').css('padding', '4px');
		jQuery('.btn').css('font-size', '13px');
		jQuery('label').css('margin-bottom', '0');

	} else {

		jQuery('link[href*="bootstrap.min.css"]').attr("disabled", "true");
		jQuery('.packing-slip-sections h4').css('top','-35px');
		jQuery('h3').css('font-size', '');
		jQuery('.form-table td').css('padding', '');
		jQuery('body').css('color', '');
		jQuery('body').css('background-color', '');
		jQuery('body').css('font-size', '');
		jQuery('body').css('font-family', '');
		jQuery('table').css('border-collapse', '');
		jQuery('.btn').css('font-size', '');
		jQuery('.btn').css('padding', '');
		jQuery('label').css('margin-bottom', '');
	}
  
});

/* Related to Price Calculator End*/

jQuery(document).ready(function(){
    jQuery(".order_button_email").click(function(){
        jQuery(".order_button_email").attr("checked", "checked");
    });
});

jQuery(document).ready(function(){
    jQuery('.hide_client_info_Suppliers').on('change', function(){
        if(jQuery('.hide_client_info_Suppliers:checked').length){

            jQuery('.store_add_shipping_add').prop('disabled', true);
            jQuery('.store_add_shipping_add').prop('checked', false);
            return;
        }

        jQuery('.store_add_shipping_add').prop('disabled', false);
    });
});

jQuery(document).ready(function () {
	// jQuery("#myDataTable").DataTable();
	document.getElementById('searchInput').addEventListener('input', function () {
		let filter, table, tr, td, i, j, txtValue;
		filter = this.value.toUpperCase();
		table = document.getElementById('the-list');
		tr = table.getElementsByTagName('tr');

		for (i = 0; i < tr.length; i++) {
			let rowVisible = false;
			td = tr[i].getElementsByTagName('td');

			for (j = 0; j < td.length; j++) {
				txtValue = td[j].textContent || td[j].innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					rowVisible = true;
					break;
				}
			}

			if (rowVisible) {
				tr[i].style.display = '';
			} else {
				tr[i].style.display = 'none';
			}
		}
	});
});