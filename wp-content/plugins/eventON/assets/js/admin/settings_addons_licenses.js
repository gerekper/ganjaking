/*
*	Eventon Settings tab - addons and licenses
*	@version: 2.5.1
*/

jQuery(document).ready(function($){

	init();

	// load addon details
		function init(){

			var obj = $('#evo_addons_list');
			var data_arg = {
				action:'eventon_get_addons_list',
			};

			$.ajax({
				beforeSend: function(){	},
				type: 'POST',
				url:the_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){				
					obj.html(data.content);
				}
			});
		}

	// load forms for lightbox
		$('body').on('click','#evo_license_form_trig', function(){
			var ajaxdataa = {};
			ajaxdataa['action'] = 'eventon_admin_get_views';
			ajaxdataa['type'] = 'evo_activation_form';

			$.ajax({
				beforeSend: function(){		},
				type: 'POST',dataType:'json',url:the_ajax_script.ajaxurl,data: ajaxdataa,
				success:function(data){	
					if(data.status=='good'){
						$('.ajde_popup_text').html( data.html );
					}
				},complete: function(){
					hide_pop_loading();
				}
			});
		});
	// load addons license form
		$('#evo_addons_list').on('click','.evo_addon_license_form_trigger',function(){
			var ajaxdataa = {};
			OBJ = $(this);
			ajaxdataa['action'] = 'eventon_admin_get_views';
			ajaxdataa['type'] = 'evo_addon_activation_form';
			data = 'data';
			ajaxdataa[data] = {};
			ajaxdataa[data]['slug'] = OBJ.data('slug');
			ajaxdataa[data]['product_id'] = OBJ.data('product_id');

			$.ajax({
				beforeSend: function(){		},
				type: 'POST',dataType:'json',url:the_ajax_script.ajaxurl,data: ajaxdataa,
				success:function(data){	
					if(data.status=='good'){
						$('.ajde_popup_text').html( data.html );
					}
				},complete: function(){
					hide_pop_loading();
				}
			});
		});


	// License Verification for EventON
		$('body').on('click','.eventon_submit_license',function(){
			validate( $(this) , 'main' );			
		});

		$('body').on('click','.eventonADD_submit_license',function(){	
			validate( $(this), 'addon' );
		});

		function validate(OBJ, TYPE){
			$('.ajde_popup').find('.message').removeClass('bad good');
			
			var ajaxdataa = {};
			var parent_pop_form = OBJ.parent().parent();
			
			// field validation
				errors = 0;
				parent_pop_form.find('input.fields').each(function(){
					if($(this).val()==''){
					 	errors++;
					}else{
						ajaxdataa[ $(this).attr('name')] = $(this).val();		
					}						
				});

				if(errors >0 ){
					show_pop_bad_msg('All fields are required! Please try again.');
					return false;
				}
							
			parent_pop_form.find('.message').hide();
			var slug = parent_pop_form.find('.eventon_slug').val();	
			var error = false;	
			if(TYPE == 'addon'){
				var id = parent_pop_form.find('.eventon_id').val();	
			}		
			
			// validate key format					
				ajaxdataa['action'] = 'eventon_validate_license';
				ajaxdataa['type'] = TYPE;

				$.ajax({
					beforeSend: function(){	show_pop_loading();	},
					type: 'POST',dataType:'json',url:the_ajax_script.ajaxurl,data: ajaxdataa,
					success:function(data){	
						show_pop_good_msg( data.msg);
						hide_pop_loading();
						
						if(data.status=='good'){							
							setTimeout(function () {
							   $('.ajde_close_pop_btn').trigger('click');
							}, 5000);
						}else{
							setTimeout(function () {
							   $('.ajde_close_pop_btn').trigger('click');
							}, 15000);
						}

						if(data.html!=''){
							SLUG = ajaxdataa.slug;
							BOX = $("#evoaddon_"+SLUG);
							BOX.replaceWith( data.html );
						}
					},complete:function(){	}
				});
		}

		// Reattempt remote activation
			$('#evo_addons_list').on('click','.evo_retry_remote_activation',function(){
				ADDON = $(this).closest('.addon');

				var ajaxdataa = {};
				ajaxdataa['action'] = 'eventon_revalidate_license';
				ajaxdataa['slug'] = ADDON.data('slug');
				ajaxdataa['product_id'] = ADDON.data('product_id');
				ajaxdataa['type'] = 'addon';

				$.ajax({
					beforeSend: function(){	ADDON.addClass('evoloading'); },
					type: 'POST',dataType:'json',url:the_ajax_script.ajaxurl,data: ajaxdataa,
					success:function(data){							
						if(data.html!=''){
							SLUG = ajaxdataa.slug;
							BOX = $("#evoaddon_"+SLUG);
							BOX.replaceWith( data.html );
						}
						alert( data.msg);
					},complete:function(){ ADDON.removeClass('evoloading'); }
				});
			});
	
	// deactivate eventon products
		// eventon
		$('body').on('click', '#evoDeactLic', function(){			
			deactivate_product( $(this) , 'main' );
		});
		$('body').on('click', '.evo_deact_adodn',function(){
			deactivate_product( $(this) , 'addon' );
		});

		function deactivate_product(OBJ, type){
			var data_arg = {
				action:'eventon_deactivate_product',
				type: type
			};
			var addon = OBJ.closest('.addon');

			if(type == 'main'){
			}else{				
				data_arg['key']			= addon.attr('data-key');
				data_arg['slug']		= addon.attr('data-slug');
				data_arg['email']		= addon.attr('data-email');
				data_arg['product_id']	= addon.attr('data-product_id');
			}

			$.ajax({
				beforeSend: function(){ addon.addClass('evoloading');	},
				type: 'POST',
				url:the_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					if(data.html !=''){
						addon.replaceWith( data.html );
					}
					alert( data.msg);
				},complete:function(){	addon.removeClass('evoloading');	}
			});
		}

		
	// popup lightbox functions
		function show_pop_bad_msg(msg){
			$('.ajde_popup').find('.message').removeClass('bad good').addClass('bad').html(msg).fadeIn();
		}
		function show_pop_good_msg(msg){
			$('.ajde_popup').find('.message').removeClass('bad good').addClass('good').html(msg).fadeIn();
		}
		
		function show_pop_loading(){
			$('.ajde_popup_text').css({'opacity':0.3});
			$('#ajde_loading').fadeIn();
		}
		function hide_pop_loading(){
			$('.ajde_popup_text').css({'opacity':1});
			$('#ajde_loading').fadeOut(20);
		}
});