jQuery(document).ready(function(){
	jQuery('.test-fraud').on('click',function(e){
		var data_id = jQuery(this).attr('data_id');
		jQuery.ajax({
	        url: ajaxurl,
	        type : "POST",
	        data: {
	            'action':'my_action',
	            'order_id':data_id,
	        },
	        success:function(data) {
	            // This outputs the result of the ajax request
	            console.log(data);
	        },
	        error: function(errorThrown){
	            console.log(errorThrown);
	        }
	    });     
	    e.preventDefault();
	}); 
	
	jQuery('#wc_settings_anti_fraud_whitelist').on('focusout',function(){
		
		//var blacklistemail = jQuery('#wc_settings_anti_fraudblacklist_emails').val();
		var whitelistemail = jQuery('#wc_settings_anti_fraud_whitelist').val();
		jQuery.ajax({
	        url: ajaxurl,
	        type : "POST",  
	        data: {
	            'action':'check_blacklist_whitelist',
				'whitelist':whitelistemail,
	        },
	        success:function(result) {
	            
	            console.log(result);
	        },
	        error: function(errorThrown){
	            console.log(errorThrown);
	        }
	    });
	});	

	jQuery(function() {
		const params = new URL(window.location.href).searchParams;
		if (params.get('page') == 'wc-settings' && params.get('tab') == 'wc_af' && params.get('section') == 'need_support') {
			jQuery('.submit').hide();
		}
	})

});