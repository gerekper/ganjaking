/**
 * Javascript for wishlist
 * @version  0.1
 */
jQuery(document).ready(function($){
	
	$('body').on('click', 'span.evowi_wi_area i', function(event){

		event.preventDefault();
		event.stopPropagation();

		OBJ = $(this).closest('.evowi');

		var ajaxdataa = {};

		newStatus = (OBJ.hasClass('notlisted'))? 'add':'remove';

		ajaxdataa['action']= 'evowi_change_wishlist';				
		ajaxdataa['ei']= OBJ.data('ei');				
		ajaxdataa['ri']= OBJ.data('ri');				
		ajaxdataa['pl']= OBJ.data('pl');				
		ajaxdataa['newstatus']= newStatus; 
		
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

});