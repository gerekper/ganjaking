jQuery(document).ready(function($){
	$('#upw_load_more').click(function(){
		upw_load_more_products(this);	
	});
})

jQuery(document).on('click', '.upw-overlay, a.upw-close',function(){
		upw_close_popup();
});
jQuery(document).on('click','.remove_from_wishlist',function(){
	Jquery(document).reload();
});
function show_order_details(elem){
	jQuery('body').append('<div class="upw-overlay"></div>');
	jQuery('body').append('<div class="upw-overlay-loader"></div>');
	var order_id = jQuery(elem).data('order_id');
	jQuery.ajax({
		url:userpro_ajax_url,
		data:'action=upw_get_order_details&order_id='+order_id,
		dataType:'JSON',
		type:'POST',
		success:function(data){
			jQuery('.upw-overlay-loader').remove();
			jQuery('body').append(data.html);
			upw_adjust();
		}
	});
}

function upw_overlay_resize(){
	jQuery('.upw-overlay-content').animate({
		'opacity' : 1,
		'margin-top' : '-' + jQuery('.upw-overlay-content').innerHeight() / 2 + 'px'
	});
}

/* Remove overlay */
function upw_close_popup(){
	jQuery('.tipsy').remove();
	jQuery('.upw-overlay-content, .upw-overlay-loader, .upw-overlay').remove();
}

function upw_adjust(){
	jQuery('.upw-order-body-wrapper').css({'max-height': jQuery('.upw-order-body-wrapper').height()/2 + 'px'});
	
	jQuery('.upw-order-body-wrapper').mCustomScrollbar('destroy');
	jQuery('.upw-order-body-wrapper').mCustomScrollbar({
		theme:"dark-2",
		advanced:{
			updateOnContentResize: true,
			autoScrollOnFocus: false,
		}
	});

	
	upw_overlay_resize();
	
}

function upw_load_more_products(elem){
	
	var paged = parseInt(jQuery('#upw_paged').val())+1;
	jQuery.ajax({
		url:userpro_ajax_url,
		data:'action=upw_get_more_products&paged='+paged+'&shown_products='+jQuery('.upw-product').length,
		dataType:'JSON',
		type:'POST',
		beforeSend:function(data){
			jQuery('#upw_load_more').html('Loading more products...');
		},
		success:function(data){
			if(data.html == '0'){
				jQuery('#upw_load_more').html(data.no_more_text);
				jQuery('#upw_load_more').off('click');
			}
			else{
				jQuery('#upw_load_more').html(data.no_more_text);
				jQuery('.upw-product-wrapper').append(data.html);
			}
			jQuery('#upw_paged').val(paged);
		}
	})
}

jQuery( ".showlogin" ).click(function() {
	jQuery(".wc_userpro_login").toggle();
}); 
