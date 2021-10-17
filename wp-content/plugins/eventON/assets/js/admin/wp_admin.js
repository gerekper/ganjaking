/*
 * EventON Back end scripts for general backend of wordpress
 * @version 3.1
 */
jQuery(document).ready(function($){	
	
// color picker
	if( $.isFunction($.fn.ColorPicker) ) { 
		$('body').on('click','.colorselector',function(){
			var EE = $(this);
			EE.ColorPicker({
				onBeforeShow: function(){
					$(this).ColorPickerSetColor( $(this).attr('hex'));
				},	
				onChange:function(hsb, hex, rgb){
					//console.log(hex+' '+rgb);
					CIRCLE = $('body').find('.colorpicker_on');
					CIRCLE.css({'backgroundColor': '#' + hex}).attr({'title': '#' + hex, 'hex':hex});

					obj_input = CIRCLE.siblings('input.backender_colorpicker');	
					obj_input.attr({'value':hex});
					
					$('body').trigger('evo_color_select_changed',[ hex, rgb]);
				},	
				onSubmit: function(hsb, hex, rgb, el) {
					var obj_input = $(el).siblings('input.backender_colorpicker');

					if($(el).hasClass('rgb')){
						$(el).siblings('input.rgb').attr({'value':rgb.r+','+rgb.g+','+rgb.b});
						//console.log(rgb);
					}

					obj_input.attr({'value':hex});

					$(el).css('backgroundColor', '#' + hex);
					$(el).attr({'title': '#' + hex, 'hex':hex});
					$(el).ColorPickerHide();

					$('body').find('.colorpicker_on').removeClass('colorpicker_on')
						.trigger('evo_color_select_submitted',[el, hex, rgb]);
				},
				onHide: function(colpkr){
					$('body').find('.colorpicker_on').removeClass('colorpicker_on');
				}
			}).bind('click',function(){
				$(this).addClass('colorpicker_on');
			});

			if( !EE.hasClass('clrO')){
				EE.trigger('click').addClass('clrO');
			}
		});

		$('.colorselector').ColorPicker({
			onBeforeShow: function(){
				$(this).addClass('clrO');
				$(this).ColorPickerSetColor( $(this).attr('hex'));
			},	
			onChange:function(hsb, hex, rgb){
				//console.log(hex+' '+rgb);
				CIRCLE = $('body').find('.colorpicker_on');
				CIRCLE.css({'backgroundColor': '#' + hex}).attr({'title': '#' + hex, 'hex':hex});

				obj_input = CIRCLE.siblings('input.backender_colorpicker');	
				obj_input.attr({'value':hex});

				$('body').trigger('evo_color_select_changed',[hex, rgb]);
			},	
			onSubmit: function(hsb, hex, rgb, el) {
				var obj_input = $(el).siblings('input.backender_colorpicker');

				if($(el).hasClass('rgb')){
					$(el).siblings('input.rgb').attr({'value':rgb.r+','+rgb.g+','+rgb.b});
					//console.log(rgb);
				}

				obj_input.attr({'value':hex});

				$(el).css('backgroundColor', '#' + hex);
				$(el).attr({'title': '#' + hex, 'hex':hex});
				$(el).ColorPickerHide();

				$('body').find('.colorpicker_on').removeClass('colorpicker_on').trigger('evo_color_select_submitted',[el, hex, rgb]);
			},
			onHide: function(colpkr){
				$('body').find('.colorpicker_on').removeClass('colorpicker_on');
			}
		}).bind('click',function(){
			$(this).addClass('colorpicker_on');
		});
	}

	function colorPickMulti(itemid, cp){}

// get location cordinates
	$('body').on('click','.evo_auto_gen_latlng', function(){
		// validation
		var O = $(this);

		$('body').remove('.evo_auto_gen_latlng_m');

		var add = $('body').find('input[name="location_address"]').val();
		if(!add) 
			var add = $('body').find('input[name="term_meta[location_address]"]').val();


		if( !add){
			O.after('<p class="evo_auto_gen_latlng_m" style="padding-top:5px;">Address Missing</p>');
			return;
		}

		// run ajax
		var D = {
			'action': 'eventon_get_latlng',
			'address': add
		};
		$.ajax({
			beforeSend: function(){
				O.parent().addClass('evo_loader');
			},
			url: evo_admin_ajax_handle.ajaxurl, type: 'POST',dataType:'json',
			data: D,
			success:function(data){

				if( data.status == 'good'){

					$('body').find('input[name="location_lat"]').val( data.lat );
					$('body').find('input[name="location_lon"]').val( data.lng );

					$('body').find('input[name="term_meta[location_lat]"]').val( data.lat );
					$('body').find('input[name="term_meta[location_lon]"]').val( data.lng );
				}else{
					if( 'm' in data){
						O.after('<p class="evo_auto_gen_latlng_m" style="padding-top:5px;">'+ data.m +'</p>');
					}
				}
				
			},complete:function(){	
				O.parent().removeClass('evo_loader');
			}
		});
	});

// font awesome selector
	var FA = $('.ajde_fa_icons_selector');
	$('.ajde_fa_icons_selector').remove();
	$('body').append(FA);

	$('.ajde_icons').html('<em class="ajde_icon_close">X</em>');

	var fa_icon_selection = '';
	$('body').on('click','.ajde_icons', function(){
		var poss = $(this).offset();
		// /console.log(poss);
		$('.ajde_fa_icons_selector').css({'top':(poss.top-220)+'px', 'left':(poss.left-68)}).fadeIn('fast');

		fa_icon_selection = $(this);
	});

	// remove icon
		$('body').on('click','i.ajde_icons em', function(){
			$(this).parent().attr({'class':'ajde_icons default'});
			$(this).parent().siblings('input').val('');
		});

	//selection of new font icon
		$('.ajde_fa_icons_selector').on('click','li', function(){

			var icon = $(this).find('i').data('name');
			//console.log(icon);

			fa_icon_selection.attr({'class':'ajde_icons default fa '+icon});
			fa_icon_selection.siblings('input').val(icon);

			$('.ajde_fa_icons_selector').fadeOut('fast');
		});
	// close with click outside popup box when pop is shown
	$(document).mouseup(function (e){
		var container=$('.ajde_fa_icons_selector');
		
			if (!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
			{
				$('.ajde_fa_icons_selector').fadeOut('fast');
			}
		
	});



// LIGHTBOX function 
	// lightbox hide
		$('body').on('click',' .ajde_close_pop_trig',function(){
			hide_popupwindowbox( $(this).closest('.ajde_admin_lightbox') );
		});
		$('body').on('click',' .ajde_close_pop_btn',function(){
			var obj = $(this).parent();
			hide_popupwindowbox( $(this).closest('.ajde_admin_lightbox') );
		});
		
		$(document).mouseup( function(event){
			if( 
		    	$(event.target).hasClass('evo_content_inin')
		    ){
			 	CONTAIN =	$(event.target).find('.ajde_popup');
			 	if(!CONTAIN.hasClass('nooutside')){
			 		CONTAIN.find('.ajde_close_pop_btn').trigger('click');
			 	}
		  	}
		});

		$('body').on('click','.ajde_close_btn',function(){
			$(this).closest('.ajde_close_elm').hide();
			if( $(this).data('remove') !== undefined && $(this).data('remove') == 'yes')
				$(this).closest('.ajde_close_elm').remove();
		});
		
	// trigger hide popup
		$('body').on('evoadmin_lightbox_hide',function(event, lightboxclass){
			lightboxELM = $('.ajde_admin_lightbox.'+lightboxclass);
			hide_popupwindowbox( lightboxELM );
		});
		function hide_popupwindowbox( lightboxELM ){
			if(! lightboxELM.hasClass('show')) return false;
			Close = (lightboxELM.parent().find('.ajde_admin_lightbox.show').length == 1)? true: false;

			lightboxELM.removeClass('show');
			setTimeout( function(){ 
				if(Close){
					$('body').removeClass('evo_overflow');
					$('html').removeClass('evo_overflow');
				}
			}, 300);			
		}

	// OPEN POPUP BOX		
		// everywhere in wp-admin
			$('body').on('click','.ajde_popup_trig', function(){			
				ajde_popup_open( $(this));
			});

	// popup open
		// 2.6.9
		$('body').on('evo_open_admin_lightbox', function(event, lb_class){
			ajde_open_any_lightbox(lb_class);
		});

		function ajde_open_any_lightbox(lb_class){
			LIGHTBOX = $('.ajde_admin_lightbox.'+lb_class).eq(0);

			// if already open
			if(LIGHTBOX.is("visible")===true) return false;

			POPUP = LIGHTBOX.find('.ajde_popup');
			POPUP.find('.message').removeClass('bad good').hide();

			// open lightbox
			LIGHTBOX.addClass('show');	
			$('body').addClass('evo_overflow');
			$('html').addClass('evo_overflow');
		}
		function ajde_popup_open(obj){
			var popc = obj.data('popc');

			// check if specific lightbox requested
			var LIGHTBOX = (typeof popc !== 'undefined' && popc !== false)?
				$('.ajde_admin_lightbox.'+popc).eq(0):$('.ajde_admin_lightbox.regular').eq(0);

			var POPUP = LIGHTBOX.find('.ajde_popup');

			// alter lightbox title
			if( obj.data('t') !== undefined){
				LIGHTBOX.find('.ajde_lightbox_title').html( obj.data('t') );
			}
			
			if(LIGHTBOX.is("visible")===true) return false;

			// append textbox id to popup if given
			if(obj.attr('data-textbox')!==''){
				POPUP.attr({'data-textbox':obj.attr('data-textbox')});
			}

			// dynamic content within the site
				var dynamic_c = obj.attr('data-dynamic_c');
				if(typeof dynamic_c !== 'undefined' && dynamic_c !== false){
					
					var content_id = obj.attr('data-content_id');
					var content = $('#'+content_id).html();
					
					LIGHTBOX.find('.ajde_popup_text').html( content);
				}
			
			// run ajax to load content for the lightbox inside
				if( obj.data('ajax') == 'yes' && obj.data('d') !== 'undefined'){

					var D = {};
					D = obj.data('d');

					$.ajax({
						beforeSend: function(){	},
						url:	evo_admin_ajax_handle.ajaxurl, type: 'POST',dataType:'json',
						data: D,
						success:function(data){
							LIGHTBOX.find('.ajde_popup_text').html( data.html);
						},complete:function(){	hide_pop_loading();	}
					});
				}

			// if content coming from a AJAX file			
				var attr_ajax_url = obj.attr('ajax_url');				
				if(typeof attr_ajax_url !== 'undefined' && attr_ajax_url !== false){
					$.ajax({
						beforeSend: function(){
							show_pop_loading();
						},
						url:attr_ajax_url,
						success:function(data){
							LIGHTBOX.find('.ajde_popup_text').html( data);
						},complete:function(){
							hide_pop_loading();
						}
					});
				}

			// change title if present		
				var poptitle = obj.attr('poptitle');
				if(typeof poptitle !== 'undefined' && poptitle !== false){
					LIGHTBOX.find('.ajde_header p').html(poptitle);
				}
						
			POPUP.find('.message').removeClass('bad good').hide();

			// open lightbox
			LIGHTBOX.addClass('show');	
			$('body').addClass('evo_overflow');
			$('html').addClass('evo_overflow');

			// $('html, body').animate({scrollTop:0}, 700);
			// $('#ajde_popup_bg').fadeIn();
		}
	
	// popup lightbox functions
		// lightbox messages
		$('body').on('ajde_lightbox_show_msg',function(event,message, boxclassname, type, donthide, hideMsg){
			LIGHTBOX = $('.'+boxclassname+'.ajde_admin_lightbox');
			type = (type!='bad')? 'good':'bad';
			LIGHTBOX.find('p.message').removeClass('bad good').addClass(type).html(message).fadeIn();
			
			// hide lightbox if good after 2 seconds
			dh = (donthide !='' && donthide == false ) ? true: false;
			
			if(type=='good' && dh)	setTimeout(function(){  hide_popupwindowbox(LIGHTBOX) }, 2000);

			// hide the message only after 2 seconds
			if( hideMsg)	setTimeout(function(){  LIGHTBOX.find('p.message').hide() }, 2000);
		});

		// hide lightbox message
		$('body').on('ajde_lightbox_hide_msg',function(event,boxclassname){
			LIGHTBOX = $('.'+boxclassname+'.ajde_admin_lightbox');
			LIGHTBOX.find('p.message').fadeOut();			
		});

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

// Event Singular tax term form create new or edit form
		$('body').on('click','.evo_tax_term_form',function(){
			OBJ = $(this);
			PAR = OBJ.closest('.evo_singular_tax_for_event');
			var ajaxdataa = { };
				ajaxdataa['action']='eventon_get_event_tax_term_section';
				ajaxdataa['type']= OBJ.data('type');
				ajaxdataa['tax']=  PAR.data('tax');
				ajaxdataa['eventid']=  PAR.data('eventid');
				ajaxdataa['termid']=  OBJ.data('id');

			$.ajax({
				beforeSend: function(){
					text = OBJ.data('type')=='new'?  evo_admin_ajax_handle.add_new_item: evo_admin_ajax_handle.edit_item;
					$('.evo_term_lightbox').find('.ajde_lightbox_title').html( text );
					$('.evo_term_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						$('.evo_term_lightbox').find('.ajde_popup_text').html( data.content);
					}else{}
				},complete:function(){
					$('.evo_term_lightbox').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});
		
		// get term list
		$('body').on('click','.evo_tax_term_list',function(){
			OBJ = $(this);
			PAR = OBJ.closest('.evo_singular_tax_for_event');
			var ajaxdataa = { };
				ajaxdataa['action']='eventon_event_tax_list';
				ajaxdataa['tax']=  PAR.data('tax');
				ajaxdataa['eventid']=  PAR.data('eventid');
				ajaxdataa['termid']=  OBJ.data('id');

			$.ajax({
				beforeSend: function(){
					$('.evo_term_lightbox').find('.ajde_lightbox_title').html( evo_admin_ajax_handle.select_from_list);
					$('.evo_term_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						$('.evo_term_lightbox').find('.ajde_popup_text').html( data.content);
						$('.evo_term_lightbox').find('select.field').select2();						
					}
				},complete:function(){
					$('.evo_term_lightbox').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});

		// save changes
		$('body').on('click','.evo_term_submit',function(){
			OBJ = $(this);
			PAR = OBJ.closest('.evo_tax_entry');
			var ajaxdataa = { };
				ajaxdataa['action']='eventon_event_tax_save_changes';
				ajaxdataa['tax']=  PAR.data('tax');
				ajaxdataa['eventid']=  PAR.data('eventid');
				ajaxdataa['type']=  PAR.data('type');

			PAR.find('.field').each(function(){
				if($(this).val() != ''){
					ajaxdataa[ $(this).attr('name')]=  $(this).val();
				}
			});

			$.ajax({
				beforeSend: function(){
					$('.evo_term_lightbox').find('.ajde_lightbox_title').html( evo_admin_ajax_handle.select_from_list);
					$('.evo_term_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						$('.evo_term_lightbox').find('.message').html( data.content).show();
						$('.evo_singular_tax_for_event.'+PAR.data('tax')).html(data.htmldata);
					}
				},complete:function(){
					setTimeout(function () {
					   $('.ajde_close_pop_btn').trigger('click');
					}, 2000);					
					$('.evo_term_lightbox').find('.ajde_popup_text').removeClass( 'loading');

					// when setting event location
					if(PAR.data('tax') == 'event_location'){
						var inp = $('body').find('input[name="evcal_gmap_gen"]');
						if( inp.val() == 'no') inp.siblings('span').trigger('click');
					}
				}
			});	
		});

		// remove location
		$('body').on('click','.evo_tax_remove',function(){
			OBJ = $(this);
			PAR = OBJ.closest('.evo_singular_tax_for_event');
			var ajaxdataa = { };
				ajaxdataa['action']='eventon_event_tax_remove';
				ajaxdataa['tax']=  PAR.data('tax');
				ajaxdataa['eventid']=  PAR.data('eventid');
				ajaxdataa['termid']=  OBJ.data('id');

			$.ajax({
				beforeSend: function(){
					PAR.addClass( 'loading');
				},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						PAR.html(data.htmldata);
					}
				},complete:function(){
					PAR.removeClass( 'loading');
				}
			});	
		});

// Related Events
		$('body').
		on('click','.evo_rel_events',function(){
			OBJ = $(this);
			var ajaxdataa = { };
				ajaxdataa['action']='eventon_rel_event_list';
				ajaxdataa['eventid']=  OBJ.data('eventid');
				ajaxdataa['EVs']=  OBJ.siblings('.evo_rel_events_sel_list').val();

			$.ajax({
				beforeSend: function(){
					$('.evo_term_lightbox').find('.ajde_lightbox_title').html( evo_admin_ajax_handle.select_from_list);
					$('.evo_term_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						$('.evo_term_lightbox').find('.ajde_popup_text').html( data.content);
						$('.evo_term_lightbox').find('select.field').select2();						
					}
				},complete:function(){
					$('.evo_term_lightbox').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		})
		.on('click','span.rel_event', function(){
			O = $(this);
			O.toggleClass('select');
		})

		// save related event select
		.on('click','.evo_save_rel_events', function(){

			EV = {};
			HTML = '';

			$(this).closest('.evo_rel_events_form').find('.rel_event.select').each(function(){
				var O = $(this);
				EV[O.data('id')] = O.data('n');
				HTML += "<span class='l' data-id='"+ O.data('id') +"'><span class='t'>" + O.data('t') +"</span><span class='n'>"+ O.data('n') + "</span><i>X</i></span>";
			});

			BOX = $('body').find('.evo_rel_events_box');

			BOX.find('.ev_rel_events_list').html( HTML );
			BOX.find('.evo_rel_events_sel_list').val( JSON.stringify(EV) );

			$('.evo_term_lightbox').find('.message').html( 'Saved related events').show();
			setTimeout(function () {
			   $('.ajde_close_pop_btn').trigger('click');
			}, 2000);	
		})
		// remove related events
		.on('click','.ev_rel_events_list i',function(){
			var rel_box = $(this).closest('.evo_rel_events_box');

			$(this).closest('.l').remove();

			EV = {};
			rel_box.find('span.l').each(function(){
				EV[ $(this).data('id') ] = $(this).find('.n').html();
			});
			rel_box.find('.evo_rel_events_sel_list').val( JSON.stringify( EV ));
		})
		;

// Upload custom images to eventon custom image meta fields
		var file_frame,
			BOX;	
	  
	    $('body').on('click','.custom_upload_image_button',function(event) {
	    	var obj = jQuery(this);
	    	BOX = obj.closest('.evo_metafield_image');

	    	IMG_URL = '';

	    	// choose image
	    	if(obj.hasClass('chooseimg')){

	    		event.preventDefault();

				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					file_frame.open();
					return;
				}
				// Create the media frame.
				file_frame = wp.media.frames.downloadable_file = wp.media({
					title: 'Choose an Image',
					button: {text: 'Use Image',},
					multiple: false
				});

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					attachment = file_frame.state().get('selection').first().toJSON();

					BOX.find('.evo_meta_img').val( attachment.id );
					BOX.find('.image_src img').attr('src', attachment.url ).fadeIn();
					var old_text = obj.attr('value');
					var new_text = obj.data('txt');

					obj.attr({'value': new_text, 'data-txt': old_text, 'class': 'custom_upload_image_button button removeimg'});
				});

				// Finally, open the modal.
				file_frame.open();

			}else{
				
				BOX.find('.evo_meta_img').val( '' );
		  		BOX.find('.image_src img').fadeOut(function(){
		  			$(this).attr('src', '' );
		  		});
		  		var old_text = obj.attr('value');
				var new_text = obj.attr('data-txt');

				obj.attr({'value': new_text, 'data-txt': old_text, 'class': 'custom_upload_image_button button chooseimg'});

				return false;
			}
	    });  

// Add new tag field clearing after
	$('#addtag #submit').click(function () {
	    // Look for a div WordPress produces for an invalid form element
	    if (!$('#addtag .form-invalid').length) {
	        var IF = $('#addtag').find('.evo_metafield_image');

	        var INP = IF.find('.custom_upload_image_button');

	        var old_text = INP.attr('value');
			var new_text = INP.attr('data-txt');


	        INP.removeClass('removeimg').addClass('chooseimg').attr({'value': new_text, 'data-txt': old_text});
	        IF.find('img').fadeOut(function(){
	  			$(this).attr('src', '' );
	  		});

	    }
	});

// Multi Data Types for event type posts
	$('body').on('click','.evomdt_add_new_btn',function(){
		OBJ = $(this);
		var ajaxdataa = { };
			ajaxdataa['action']='evo_mdt';
			ajaxdataa['type']= 'newform';
			ajaxdataa['tax']=  OBJ.data('tax');
			ajaxdataa['eventid']=  OBJ.data('eventid');

		$.ajax({
			beforeSend:function(){	$('.evomdt_new').find('.ajde_popup_text').addClass('evoloading');},
			type: 'POST',
			url:evo_admin_ajax_handle.ajaxurl,
			data: ajaxdataa,
			dataType:'json',
			success:function(data){
				if(data.status=='good'){
					$('.evomdt_new').find('.ajde_popup_text').html( data.content);
				}else{}
			},
			complete:function(){	$('.evomdt_new').find('.ajde_popup_text').removeClass('evoloading');}
		});			
	});

	// edit term
		$('.evomdt_selection').on('click','i.fa-pencil',function(){
			OBJ = $(this);
			var ajaxdataa = { };
				ajaxdataa['action']='evo_mdt';
				ajaxdataa['type']= 'editform';
				ajaxdataa['tax']=  OBJ.closest('ul').data('tax');
				ajaxdataa['eventid']=  OBJ.closest('ul').data('eventid');
				ajaxdataa['termid']=  OBJ.parent().data('termid');

			$.ajax({
				beforeSend:function(){	$('.evomdt_new').find('.ajde_popup_text').addClass('evoloading');},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.evomdt_new').find('.ajde_popup_text').html( data.content);
					}else{}
				},
				complete:function(){	$('.evomdt_new').find('.ajde_popup_text').removeClass('evoloading');}
			});			
		});

	// delete term relationship
		$('.evomdt_selection').on('click','i.fa-close',function(){
			OBJ = $(this);
			EVOMB = OBJ.closest('.evomb_body');
			var ajaxdataa = { };
				ajaxdataa['action']='evo_mdt';
				ajaxdataa['type']= 'removeterm';
				ajaxdataa['tax']=  OBJ.closest('ul').data('tax');
				ajaxdataa['eventid']=  OBJ.closest('ul').data('eventid');
				ajaxdataa['termid']=  OBJ.parent().data('termid');

			$.ajax({
				beforeSend: function(){	EVOMB.addClass('loading'); },
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.'+ ajaxdataa['tax']+'_display_list').html(data.content);
					}else{}
				},complete:function(){	EVOMB.removeClass('loading'); }
			});			
		});

	// submit mdt form
		$('.ajde_admin_lightbox').on('click','.evomdt_new_mdt_submit',function(){
			console.log('t');
			OBJ = $(this);
			FORM = OBJ.closest('.ev_admin_form');
			BOX = OBJ.closest('.ajde_popup_text');

			var ajaxdataa = { };
				ajaxdataa['action']='evo_mdt';
				ajaxdataa['type']= 'save';

			error = 0;
			FORM.find('.field').each(function(){
				THIS = $(this);
				// required field missing
				if( THIS.hasClass('req') && (THIS.val() === undefined || THIS.val()=='')) error++;
				ajaxdataa[ THIS.attr('name')] = THIS.val();
			});

			if(error == 0 ){
				$.ajax({
					beforeSend: function(){	BOX.addClass('loading'); },
					type: 'POST',
					url:evo_admin_ajax_handle.ajaxurl,
					data: ajaxdataa,
					dataType:'json',
					success:function(data){
						if(data.status=='good'){
							$('.'+ ajaxdataa['tax']+'_display_list').html(data.content);
							$('body').trigger('ajde_lightbox_show_msg',[ data.msg,'evomdt_new']);
						}else{	}
					},
					complete:function(){ BOX.removeClass('loading');	}
				});	
			}else{
				msg = 'Required Fields Missing!';
				$('body').trigger('ajde_lightbox_show_msg',[ msg,'evomdt_new','bad']);
			}		
		});
	// select from list
		$('body').on('click','.evomdt_get_list',function(){
			OBJ = $(this);
			var ajaxdataa = { };
				ajaxdataa['action']='evo_mdt';
				ajaxdataa['type']= 'list';
				ajaxdataa['eventid']= OBJ.data('eventid');
				ajaxdataa['tax']= OBJ.data('tax');

			$.ajax({
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.evomdt_list').find('.ajde_popup_text').html( data.content);
					}else{	}
				},complete:function(){		}
			});			
		});	

	// save list
		$('body').on('click','.evomdt_save_list_submit',function(){
			OBJ = $(this);
			BOX = OBJ.closest('.ajde_popup_text');

			var mdt = [];
			OBJ.parent().parent().find('input').each(function() {
				if($(this).is(':checked'))
		    		mdt.push($(this).val());
		    });

			var ajaxdataa = { };
				ajaxdataa['action']='evo_mdt';
				ajaxdataa['type']= 'savelist';
				ajaxdataa['eventid']= OBJ.data('eventid');
				ajaxdataa['tax']= OBJ.data('tax');
				ajaxdataa['mdt']= mdt;

			
			$.ajax({
				beforeSend: function(){	BOX.addClass('loading'); },
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.'+ ajaxdataa['tax']+'_display_list').html(data.content);
							$('body').trigger('ajde_lightbox_show_msg',[ data.msg,'evomdt_list']);
					}else{

					}
				},complete:function(){BOX.removeClass('loading');}
			});		
		});	
	
// settings
	// themes section
		$('.evo_theme_selection select').on('change',function(){
			var theme = $(this).val();
			
			// switch to default
			if(theme =='default'){
				$('.colorselector ').each(function(){
					var item = $(this).siblings('input');
					item.attr({'value': item.attr('default') });
					$(this).attr({'style':'background-color:#'+item.attr('default'), 'hex':item.attr('default')});					
				});
				$('.evo_theme').find('span').each(function(){
					$(this).attr({'style':'background-color:#'+ $(this).attr('data-default')});
				});
	
			}else{
				themeSel = JSON.parse( $('#evo_themejson').html());

				// each theme array
				$.each(themeSel, function(i, item){			
					if(item.name== theme){
						$.each(item.content, function(key, value){
							var thisItem = $('body').find('input[name='+key+']');
							thisItem.val(value);

							if(!value.includes(','))
								thisItem.siblings('span.colorselector').attr({'style':'background-color:#'+value, 'hex':value});

							$('.evo_theme').find('span[name='+key+']').attr({'style':'background-color:#'+value});
						});
					}
				});

			}
		});
	// google maps styles section
	// @since	2.2.22
		$('p.evcal_gmap_style select').on('change', function(){

			baseurl = 'https://snazzy-maps-cdn.azureedge.net/assets/';
			var styles = {
				'default':'58-simple-labels.png?v=20150113051357',
				paleretrogold : '86430-pale-retro-gold.png',
				richblack : '2720-rich-black.png?v=20150113113807',
				apple : '42-apple-maps-esque.png?v=20150113070431',
				blueessence : '/61-blue-essence.png?v=20150113072113',
				coolgrey : '80-cool-grey.png',
				shift : '27-shift-worker.png?v=20150113052049',
				bluewater : '25-blue-water.png?v=20150113093754',
				bentley : '43-bentley.png?v=20150113085831',
				hotpink : '24-hot-pink.png?v=20150113074419',
				muted : '91-muted-monotone.png?v=20150113093728',
				vintageyellowlight : '78128-vintage-yellow-light.png',
				avacado : '35-avocado-world.png?v=20150113094526',
			};

			var gmapSTY = $(this).val();
			var obj = $(this).siblings('i').find('span');
			var url = obj.attr('data-url');

			var styleVAL = '';
			// get url for map image
			$.each(styles, function(index, value){
				if( index == gmapSTY){
					styleVAL = baseurl+value;
				}
			});

			obj.css({'background':'url('+styleVAL+') center center no-repeat','display':'block','height':'150px','margin-top':'10px','max-width':'600px'});
			obj.parent().css({'opacity':'1'});
		});
	// Export settings
		$('body').on('click','#evo_settings_import',function(event){
			event.preventDefault();
			OBJ = $(this);

			OBJ.parent().siblings('.import_box').fadeIn();

			var form = document.getElementById('evo_settings_import_form');
			var fileSelect = document.getElementById('file-select');
			var box = $('#import_box');
			msg = box.find('.msg');
			msg.hide();

			$('#evo_settings_import_form').submit(function(event) {
			  	event.preventDefault();
			  	// Update button text.
			  	msg.html('Processing.').slideDown();

			  	var data = null;
			  	var files = fileSelect.files;
			  	var file = fileSelect.files[0];

			  	//console.log(file);
			  	if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
			      	alert('The File APIs are not fully supported in this browser.');
			      	return;
			    }

			  	if( file.name.indexOf('.json') == -1 ){
			  		msg.html('Only accept JSON file format.');
			  	}else{
			  		var reader = new FileReader();
				  	reader.readAsText(file);
		            reader.onload = function(event) {
		                var jsonData = event.target.result ;

		                // console.log(jsonData);
		                // console.log( $.parseJSON( jsonData) );
		             
		                $.ajax({
							beforeSend: function(){	},
							type: 'POST',
							url:evo_admin_ajax_handle.ajaxurl,
							data: {	
								action:'eventon_import_settings',
								nonce: evo_admin_ajax_handle.postnonce,
								jsondata: $.parseJSON( jsonData)
							},
							dataType:'json',
							success:function(data){
								msg.html(data.msg);
							},complete:function(){	}
						});
		            };
		            reader.onerror = function() {
		            	msg.html('Unable to read file.');
		            };
			  	}
			});
		});

// settings event card designer
// EventON v4.0
	$.fn.evo_card_designer = function (options) {
		var designer = $(this);
		var fields = $(designer).find('.evocard_design_holder');
		var selector = $(designer).find('#evo_card_field_selector');
		var def_color = designer.data('dc');

		var init = function(){
			interactions();
			field_selector();

			if( fields.length> 0 ){
				fields.sortable({
					update: function(e, ul){
						run_row_numbering();
					}
				});
			}
			
		}

		var interactions = function(){
			// new row
			$('.ecd_add_rows').on('click',function(){
				var count = parseInt($(this).data('c'));
				var holder_boxes = parseInt($(this).data('hc'));
				var holder_location = $(this).data('hl');

				html = "<p class='ecd_row "+ ( holder_location ) +"' data-r=''><span class='ecd_row_in'>";

				// normal boxes
				for (var i = 1; i <= count; i++) {
					html += "<span class='ecd_row_box' data-b='"+ i +"'><span class='ecd_set_field'>+ Set Field</span></span>";
				}

				// before after boxes count
				if( holder_boxes ){
					html += "<span class='ecd_row_box_h'>";
					for (var i = 1; i <= holder_boxes; i++) {
						html += "<span class='ecd_row_box' data-b='"+ holder_location + i +"'><span class='ecd_set_field'>+ Set Field</span></span>";
					}
					html += "</span>";
				}


				html +="</span><i class='fa fa-minus-circle ecd_del_row'></i></p>";

				$(fields).append( html );
				run_row_numbering();
			});

			// remove row
				$(designer).on('click','.ecd_del_row', function(){
					// items in row		
					$(this).parent().find('.ecd_row_box').each(function(){
						if( $(this).data('n') !== undefined && $(this).data('n') != ''){
							add_item_to_selector( $(this).data('n') , $(this).find('em').html() );			
						}			
					});

					// remove row
					$(this).closest('.ecd_row').remove();
					run_row_numbering();
				});

			// remove a box
				$(designer).on('click','.ecdad_act .fa-minus-circle',function(){
					var row = $(this).closest('.ecd_row');
					var box = $(this).closest('.ecd_row_box');
					
					add_item_to_selector( box.data('n') , box.find('em').html() );

					box.html( "<span class='ecd_set_field'>+ Set Field</span>" )
						.data('n','').attr('data-n', '' )
						.data('h','').attr('data-h', '' )
						.data('c','').attr('data-c', '' );
					run_row_numbering();	
				});

			// move row up down
				$(designer).on('click','.fa-chevron-circle-up',function(){
					var RR = $(this).closest('.ecd_row');
					PR = RR.prev();
					RR.insertBefore( PR );
					run_row_numbering();
				});
				$(designer).on('click','.fa-chevron-circle-down',function(){
					var RR = $(this).closest('.ecd_row');
					PR = RR.next();
					RR.insertAfter( PR );
					run_row_numbering();
				});

			// toggle visibility
				$(designer).on('click','.fa.vis',function(){
					if($(this).hasClass('fa-eye')){
						$(this).removeClass('fa-eye').addClass('fa-eye-slash');
						$(this).closest('.ecd_row_box').data('h','y').addClass('hidden');
					}else{
						$(this).removeClass('fa-eye-slash').addClass('fa-eye');
						$(this).closest('.ecd_row_box').data('h','').removeClass('hidden');
					}
					run_row_numbering();
				});

			// color picker
			$('body').on('evo_color_select_submitted', function(event, el, hex, rgb){
				//console.log(hex);
				if( $(el).hasClass('clr')){
					$(el).closest('.ecd_row_box').data('c', hex).attr('data-c', hex);
					run_row_numbering();
				}
			});
			$('body').on('evo_color_select_changed',function(event, hex, rgb){
				var cO = $('body').find('.colorpicker_on');
				if( cO.hasClass('clr')){
					if( hex != def_color ) cO.siblings('.clr_reset').removeClass('dn');

					cO.closest('.ecd_row_box').data('c', hex).attr('data-c', hex);
					run_row_numbering();
				}
			});
			// reset to default color
			$(designer).on('click','.clr_reset',function(){
				$(this).siblings('.clr')
					.css('background-color', '#'+ def_color)
					.attr('hex', def_color);
				$(this).closest('.ecd_row_box').data('c', def_color)
					.attr('data-c', def_color);
				$(this).addClass('dn');
				run_row_numbering();
			});
			
		}

		
		var field_selector = function(){
			$(designer).on('click','.ecd_set_field', function(){
				$(designer).find('.adding_field').removeClass('adding_field');
				$(this).parent().addClass('adding_field');
				selector.addClass('focus');
			});
			// selector > designer
			$('#evo_card_field_selector_f').on('click','span',function(){
				if( !selector.hasClass('focus') ) return;

				var html = "<span class='ecd_act1'><i class='vis fa fa-eye'></i><span class='colorselector clr' hex='"+def_color+"' style='background-color:#"+def_color+"' title=''></span> <span class='clr_reset dn' data-hex='"+def_color+"' style='background-color:#"+def_color+"' title=''></span> </span>"+
					"<em>"+ $(this).html() +"</em><span class='ecdad_act'><i class='fa fa-minus-circle'></i></span>";

				var box = $(designer).find('.adding_field');

				// append to the designer box
				box.html( html )
					.data('n', $(this).data('n') )
					.attr('data-n', $(this).data('n') )
					.removeClass('adding_field');

				// remove from selector and hide selector, show no fields message if
				$(this).remove();

				if( $('#evo_card_field_selector_f').find('span').length == 0){
					selector.find('.nothing').show();
				}
				selector.removeClass('focus');

				// load new fields
				run_row_numbering();
			});

			// cancel selector
			$('#evo_card_field_selector_c').on('click',function(){
				selector.removeClass('focus');
				$(designer).find('.adding_field').removeClass('adding_field');
			});
		}

		var add_item_to_selector = function(f, n){
			var HH = $('#evo_card_field_selector_f').html();
			HH +=  "<span data-n='"+ f +"'>"+ n +"</span>";
			$('#evo_card_field_selector_f').html( HH );
			
			// check if selector content to be shown
			if( $('#evo_card_field_selector_f').html() == ''){
				selector.find('.nothing').show();
			}else{
				selector.find('.nothing').hide();
			}
		}
		
		var run_row_numbering = function(){
			var count = 1;
			var ecl = {};

			var def_color = designer.data('dc');

			$(designer).find('.ecd_row').each(function(){
				$(this).data('r', count).attr('data-r', count);

				ecl[ count ] = {};
				$(this).find('.ecd_row_box').each(function(){
					ecl[ count ][ $(this).data('b') ] = {
						'n': $(this).data('n'),
						'h': $(this).data('h'),
						'c': ( def_color == $(this).data('c') ? '' : $(this).data('c'))
					};
				});

				count++;
			});

			$('#evo_card_fields').val( JSON.stringify(ecl) );
		}

		init();
	};

	$('.evo_card_designer').evo_card_designer();

// Settings > Support
	$('#troubleshoot_videos').on('click', function(){
		$(this).siblings('.troubleshoot_videos').toggle();
	});

	$('.evotrouble_left').on('click','h5',function(){
		$(this).next('p').toggle();
	});


// Diagnose
	$('#evo_send_test_email').on('click', function(){
		if($('#evo_admin_test_email_address').val() == undefined|| $('#evo_admin_test_email_address').val() ==''){
			$('#evodiagnose_message').html('Email Address Missing!');
		}else{
			$.ajax({
				beforeSend: function(){	$('#evodiagnose_message').html('Sending Test Email...')},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: {	
					action:'eventon_admin_test_email',
					nonce: evo_admin_ajax_handle.postnonce,
					email: $('#evo_admin_test_email_address').val()
				},
				dataType:'json',
				success:function(data){
					$('#evodiagnose_message').html(data.msg);
				},complete:function(){	}
			});
		}		
	});

	// load environment data
		$('#evo_load_environment').on('click',function(){
			O = $(this);
			
			if($('.evo_environment').hasClass('done')) return;

			$.ajax({
				beforeSend: function(){	$('.evo_environment').html('<span>Loading...</span>');},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: {	
					action:'eventon_admin_get_environment',
					nonce: evo_admin_ajax_handle.postnonce,
				},
				dataType:'json',
				success:function(data){
					$('.evo_environment').html(data.html);
				},complete:function(){	}
			});

		});

	// load status log
		$('#evo_load_log').on('click', function(){
			O = $(this);
			var LB = $('.evo_log_lightbox');
			
			$.ajax({
				beforeSend: function(){	},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: {	
					action:'eventon_admin_system_log',
					nonce: evo_admin_ajax_handle.postnonce,
				},
				dataType:'json',
				success:function(data){
					LB.find('.ajde_popup_text').html( data.html );
				},complete:function(){	}
			});
		});

// LANGUAGE SETTINGS
	// language tab
		$('.eventon_cl_input').focus(function(){
			$(this).parent().addClass('onfocus');
		});
		$('.eventon_cl_input').blur(function(){
			$(this).parent().removeClass('onfocus');
		});
	
	// change language
		$('#evo_lang_selection').change(function(){
			var val = $(this).val();
			var url = $(this).attr('url');
			window.location.replace(url+'?page=eventon&tab=evcal_2&lang='+val);
		});

	// duplicate editing
		if( $('body').find('.eventon_cl_input').length>0){
			$('.eventon_cl_input').on('change paste keyup',function(){
				const n = $(this).data('n');
				$('body').find('.eventon_cl_input.'+ n).val( $(this).val() );
			});
		}
	
	// toggeling language subheaders
		$('.evo_settings_toghead').on('click',function(){
			$(this).next('.evo_settings_togbox').toggle();
			$(this).toggleClass('open');
		});
	// export language
		$('body').on('click','.evo_lang_export_btn', function(){
			string = {};
			var tmpArr = [];
  			var tmpStr = '';
			var csvData = [];

			type = $(this).data('t');

			if(type == 'var'){
				$('#evcal_2').find('input').each(function(){
					csvData.push( $(this).attr('name')+','+ $(this).val());
				});
			}else{
				$('#evcal_2').find('input').each(function(){
					field = $(this).attr('for');
					if( field === undefined) field = $(this).attr('name');
					csvData.push( field +','+ $(this).val());
				});
			}
			

			var output = csvData.join('\n');
		  	var uri = 'data:application/csv;charset=UTF-8,' + encodeURIComponent(output);
		  	//window.open(uri);
		  	$(this).attr({
		  		'download':'evo_lang_'+$('#evo_lang_selection').val()+'.csv',
		  		'href':uri,
		  		'target':'_blank'
		  	});
		});

	// import language
		$('body').on('click','#evo_lang_import',function(){
			$('#import_box').fadeIn();

			var form = document.getElementById('file-form');
			var fileSelect = document.getElementById('file-select');
			var uploadButton = document.getElementById('upload-button');
			var box = $('#import_box');
			msg = box.find('.msg');
			msg.hide();

			$('#file-form').submit(function(event) {
				  	event.preventDefault();
				  	// Update button text.
				  	
				  	msg.html('Processing.').slideDown();

				  	var data = null;
				  	var files = fileSelect.files;
				  	var file = fileSelect.files[0];

				  	//console.log(file);
				  	if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
				      	alert('The File APIs are not fully supported in this browser.');
				      	return;
				    }

				  	if( file.name.indexOf('.csv') == -1 ){
				  		msg.html('Incorrect file format.');
				  	}else{
				  		var reader = new FileReader();
					  	reader.readAsText(file);
			            reader.onload = function(event) {
			                var csvData = event.target.result;

			                var allTextLines = csvData.split(/\r\n|\n/);
			                //console.log(allTextLines[0]);
			                for (var i=0; i<allTextLines.length; i++) {
			                	var data = allTextLines[i].split(',');

			                	// update new values

			                	$('#evcal_2').find('input[name="'+data[0]+'"]').val(data[1]); // for vars
			                	$('#evcal_2').find('input[for="'+data[0]+'"]').val(data[1]); // for text strings
			                	
			                	msg.html('Updating language values.');   
				        	}

				        	msg.html('Language fields updated. Please save changes.');   
			            };
			            reader.onerror = function() {
			            	msg.html('Unable to read file.');
			            };
				  	}
			});
		});
		$('body').on('click','#import_box #close',function(){
			$('#import_box').fadeOut();
		});
		

		function processData(allText) {
		    var allTextLines = allText.split(/\r\n|\n/);
		    var headers = allTextLines[0].split(',');
		    var lines = [];

		    for (var i=1; i<allTextLines.length; i++) {
		        var data = allTextLines[i].split(',');
		        if (data.length == headers.length) {

		            var tarr = [];
		            for (var j=0; j<headers.length; j++) {
		                tarr.push(headers[j]+":"+data[j]);
		            }
		            lines.push(tarr);
		        }
		    }
		    console.log(lines);
		}

});