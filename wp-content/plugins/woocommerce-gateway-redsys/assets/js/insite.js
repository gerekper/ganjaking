/*
* Copyright: (C) 2013 - 2021 José Conti
*/
jQuery(document).ready( function() {
	if ( token.value ) {
		jQuery.ajax({
			type : "post",
			url : insiteajax.ajaxurl,
			data : {
				action: "check_token_insite_from_actio",
				token : token.value
			},
			success: function(response) {
				if(response.type == "success") {
					jQuery("#like_counter").html(response.like_count);
				} else {
					alert("Your like could not be added");
				}
			}
		})
	}
});