jQuery(document).ready(function(){
	jQuery('.userpro_show_content').click(function(){
		var elem = jQuery(this);   
                elem.html('Loading...');
		var params = elem.data('parameters');
		jQuery.ajax({
			url: userpro_ajax_url,
			data: "action=userpro_performance&params="+params,
			dataType: 'JSON',
			type: 'POST',
			error: function(xhr, status, error){
				//userpro_end_load( form );
				//alert("Error in jQuery.ajax while submitting a form:"+error);
			},
			success:function(data){
				elem.html(data.response);
				elem.off('click');
			}
	})
	})
})