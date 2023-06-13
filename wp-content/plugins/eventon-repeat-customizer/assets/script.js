/**
 * Javascript: Repeat customizer
 * @version  2.8.8
 */
jQuery(document).ready(function($){

	// LOAD repeat customization data
		$('body').on('click','.back_to_all_repeats',function(){
			load_all_repeats( $(this).parent().data('eid') );
		});
		$('.rep_customizer_lb').on('click',function(){
			load_all_repeats( $(this).data('eid'));
		});
		function load_all_repeats(eid){
			LB = $('.ajde_admin_lightbox.evorc_lightbox');
			var ajaxdataa = {};
			ajaxdataa['action']='evorc_customizer';
			ajaxdataa['event_id'] = eid;
			
			$.ajax({
				beforeSend: function(){  LB.find('.ajde_popup_text').addClass('loading'); },	
				url:	evorc_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){						
					LB.find('.ajde_popup_text').html( data.html );
				},complete:function(){ LB.find('.ajde_popup_text').removeClass('loading');	}
			});
		}

	var evorc_file_frame;

	// main single repeat function 
	$.fn.evorc_repeat = function (options){

		// el is the lightbox '.evorc_lightbox'
		var el = this,
			defaults = {
				'html':'',
				'json':{}
			},
			BOX,
			opt = {};

		file_frame = evorc_file_frame;

		var init = function(){

			opt = $.extend({},defaults, options);

			el.find('.ajde_popup_text').html( opt.html );

			// other triggers
			$('body').trigger('evo_page_run_colorpicker_setup');

			interactions();
		};

		var interactions = function(){

			// event image
			$('body').on('click','.evorc_select_image',function(event){
				var obj = $(this);
		    	BOX = obj.siblings('.evorc_event_image_holder');

		    	event.preventDefault();

				// If the media frame already exists, reopen it.
				if ( file_frame ) {	file_frame.open();		return;	}
				
				// Create the media frame.
				file_frame = wp.media.frames.downloadable_file = wp.media({
					title: 'Choose Event Image',
					button: {text: 'Use Image'},
					multiple: false
				});

				file_frame.on('open',function(){
					var selection = file_frame.state().get('selection');
				    var selected = BOX.find('input').val();
				    if (selected) {
				        selection.add(wp.media.attachment(selected));
				    }
				});

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {

					var selection = file_frame.state().get('selection');
			        selection.map( function( attachment ) {
			            attachment = attachment.toJSON();
			            loadselectimage(attachment, BOX, file_frame);		

		            });
				});

				// Finally, open the modal.
				file_frame.open();
			});
			// remove image from gallery
			    $('body').on('click', '.remove_event_add_img', function(){
			    	$(this).closest('.evorc_event_image_holder').html('');
			    });

			// status change
				$('body').on('change','.evorc_ev_status select',function(){
					var row = $(this).closest('.evo_elm_row').siblings('._status_reason');
					var val = $(this).val();
					var rowD = row.find('label').data('l');

					if( val == 'scheduled'){
						row.hide();
						row.find('textarea').hide();
					}else{
						row.show();
						row.find('label').html( rowD[ val ][1] );
						row.find('textarea').attr('name', rowD[ val ][0] ).show();

						// set textarea value is saved
						var fval = get_field_val( rowD[ val ][0] );
						row.find('textarea').val( fval );
					}			
				});
		};

		var loadselectimage = function(attachment, BOX, file_frame){
			//console.log('yy');
			imgURL = (attachment.sizes.thumbnail && attachment.sizes.thumbnail.url !== undefined)? attachment.sizes.thumbnail.url: attachment.url;

			caption = (attachment.caption!== undefined)? attachment.caption: 'na';

			imgEL = "<span><input type='hidden' name='event_image' value='"+attachment.id+"'/><b class='remove_event_add_img'>X</b><img src='"+imgURL+"'></span>";
						
			BOX.html(imgEL);	

			//file_frame.trigger('close');			
		}

		var get_field_val = function(field){
			if( !opt.json ) return '';
			return (field in opt.json)? opt.json[field]: '';
		}

		init();

	}

	// SELECT RI
		$('body').on('click','.evorc_ri_row',function(event){
			O = $(this);

			if(O.hasClass('ev')) return false; 

			LB = $('.ajde_admin_lightbox.evorc_lightbox');
			var ajaxdataa = {};
			ajaxdataa['action']='evorc_get_ri_data';
			ajaxdataa['eid'] = O.parent().data('eid');
			ajaxdataa['ri'] = O.data('ri');

			$.ajax({
				beforeSend: function(){ LB.find('.ajde_popup_text').addClass('loading');	},	
				url:	evorc_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					
					LB.evorc_repeat({
						html: data.html,
						json: data.json
					});

				},complete:function(){ LB.find('.ajde_popup_text').removeClass('loading');	}
			});
		});

	// SAVE
		$('body').on('click','.evorc_save',function(){
			var O = $(this);
			var LB = $('.ajde_admin_lightbox.evorc_lightbox');
			var F = O.closest('.form');

			var ajaxdataa = {};
			ajaxdataa['action']='evorc_save_ri_data';
			ajaxdataa['eid'] = F.data('eid');
			ajaxdataa['ri'] = F.data('ri');

			ajaxdataa['D'] = {};

			// input
			F.find('input').each(function(){
				if( $(this).attr('name') === undefined || $(this).attr('name') == '') return;
				ajaxdataa['D'][ $(this).attr('name') ] = $(this).val();
			});

			// select field
			F.find('select').each(function(){
				if( $(this).attr('name') === undefined || $(this).attr('name') == '') return;
				ajaxdataa['D'][ $(this).attr('name') ] = $(this).val();
			});

			// textareas
			F.find('textarea').each(function(){
				if( $(this).attr('name') === undefined || $(this).attr('name') == '') return;
				ajaxdataa['D'][ $(this).attr('name') ] = $(this).val();
			});	
			
			$.ajax({
				beforeSend: function(){ LB.find('.ajde_popup_text').addClass('loading');	},	
				url:	evorc_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
						
					LB.find('.ajde_popup_text').html( data.html );

					if(data.status == 'good'){
						m = LB.find('p.message');
						m.html( data.msg );
						m.show(0).delay(3000).hide(0);
					}

				},complete:function(){ LB.find('.ajde_popup_text').removeClass('loading');	}
			});
		});

});