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
	})
});