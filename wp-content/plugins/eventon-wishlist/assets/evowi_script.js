/**
 * Javascript for wishlist
 * @version  1.2
 */
jQuery(document).ready(function($){
	
	$('body').on('click', 'span.evowi_wi_area', function(event){

		event.preventDefault();
		event.stopPropagation();

		OBJ = $(this).closest('.evowi');

		var ajaxdataa = {};

		newStatus = (OBJ.hasClass('notlisted'))? 'add':'remove';

		ajaxdataa['action']= 'evowi_change_wishlist';				
		ajaxdataa['ei']= OBJ.data('ei');				
		ajaxdataa['ri']= OBJ.data('ri');				
		ajaxdataa['pl']= window.location.href;				
		ajaxdataa['newstatus']= newStatus; 
		ajaxdataa['sc']= OBJ.closest('.ajde_evcal_calendar').evo_shortcode_data(); 
		
		$.ajax({
			beforeSend: function(){	OBJ.addClass('loading');	},
			type: 'POST',
			url:evowi_ajax_script.ajaxurl,
			data: ajaxdataa,
			dataType:'json',
			success:function(data){
				if(data.status=='good'){
					if(OBJ.hasClass('notlisted')){
						OBJ.attr('class','evowi wishlisted');
						OBJ.html(data.html);
					}else{
						OBJ.attr('class','evowi notlisted');
						OBJ.html(data.html);
					}
				}else{

					if( data.type == 'notloggedin'){
						EVO_LIGHTBOX = $('.evo_lightbox.evowl_lightbox');
						EVO_LIGHTBOX.addClass('show');
						$('body').trigger('evolightbox_show');
						$('body').find('.evowl_lightbox_body').html( data.content);
					}else{
						OBJ.html(data.message);
					}
					
				}
			},complete:function(){
				OBJ.removeClass('loading');
			}
		});	

		
	});

	// load dynamic count for wishlist manager
		$('body').on('evo_init_ajax_completed',function(){
			setTimeout(function(){
				if( $('body').find('.evowi_wl_manager').length > 0 ){
					var ev_count = $('.evowi_wl_manager').find('.eventon_list_event').length;
					$('.evowi_wl_manager').find('.evowi_stats b').html( ev_count);
				}
			}, 1000);
			
		});
		

});