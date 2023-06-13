/**
 * Javascript: Eventon Active User - Front end script
 * @version  2.3
 */
jQuery(document).ready(function($){


// search
	$('body').on('click','.evo_event_manager_search_trig',function(){
		$(this).closest('.evoau_manager').find('.evoau_search_form').toggle();
	});
	$('body').on('change paste keyup','.evoau_search_form_input',function(){
		var search_val = $(this).val();


		$(this).closest('.evoau_manager').find('.event_name a').each(function(){
			event_name = $(this).html();

			if( search_val == ''){
				$(this).closest('.evoau_manager_row').show();
			}else{
				if( event_name.toLowerCase().indexOf( search_val ) >= 0){

					$(this).closest('.evoau_manager_row').show();
				}else{
					$(this).closest('.evoau_manager_row').hide();
				}
			}
			
		});
	});

// Event Manager Actions
	adjust_sizes();
	$( window ).resize(function() {
		if($('body').find('.evoau_manager_event_section').length==0) return;
		adjust_sizes();
	});
	function adjust_sizes(){
		if($('body').find('.eventon_actionuser_eventslist').length>0){
			EM_width = $('.evoau_manager_event_section').width();
			$('.evoau_manager_event_list').width( (EM_width*2)+2 );
			$('.eventon_actionuser_eventslist').width(EM_width);
			$('.evoau_manager_event').width(EM_width);
			
			if($('.evoau_manager_event_list').css('margin-left') != '0px')
				$('.evoau_manager_event_list').css('margin-left',EM_width*-1);
		}
	}
	

	// load edit event form into event manager
		$('.eventon_actionuser_eventslist').on('click','a.editEvent',function(event){
			event.preventDefault();

			OBJ = $(this);
			
			MANAGER = $('#evoau_event_manager');
			MOVER = MANAGER.find('.evoau_manager_event_list');
			LIST = MANAGER.find('.eventon_actionuser_eventslist');

			// get form html
			var ajaxdataa = {};
	        
	        ajaxdataa['action'] = 'evoau_get_manager_event';
	        ajaxdataa['eid'] = OBJ.data('eid');
	        ajaxdataa['method'] = 'editevent';
	        ajaxdataa['sformat'] = OBJ.data('sformat');
	        ajaxdataa['json'] = MANAGER.find('.evoau_manager_json').data('js');

			$.ajax({
	            beforeSend: function(){     LIST.addClass('evoloading');  },                  
	            url:    evoau_ajax_script.ajaxurl,
	            data:   ajaxdataa,  dataType:'json', type:  'POST',
	            success:function(data){

	            	$('body').trigger('evoau_show_eventdata',[MANAGER, data.html, true]);

	            },complete:function(){ 
	            	LIST.removeClass('evoloading');
	            }
	        });
		});
	
	// move events list functions
		$('body').on('evoau_show_eventdata', function(event, MANAGER, CONTENT, TriggerFormInteractions){
			MANAGER.find('.evoau_manager_event_content').html( CONTENT );

			//load_new_editor('newreply' , $('#evoau_event_manager'), $('.evoau_manager_event'));

			MANAGER.find('#evoau_hidden_editor').remove();
			
			LIST = MANAGER.find('.eventon_actionuser_eventslist');
        	var FORM = MANAGER.find('.evoau_submission_form');
        	MOVER = MANAGER.find('.evoau_manager_event_list');

        	if(TriggerFormInteractions)  _ready_form( FORM.attr('id') );
        	
      		
      		// scroll to top of event list when showing results
        	t = MOVER.position().top - 50;
        	$(window).scrollTop( t);
        	
        	LISTWIDTH = (LIST.width())*-1;
        	MOVER.animate({'margin-left': LISTWIDTH},200);
        	LIST.removeClass('evoloading');
		});

	
	// delete an event
		$('.eventon_actionuser_eventslist').on('click','a.deleteEvent',function(event){
			event.preventDefault();

			OBJ = $(this);

			// stop deleting on disabled
			if( OBJ.data('s') == 'disable') return;
			
			MANAGER = $(this).closest('.evoau_manager');
			BOX = MANAGER.find('.evoau_delete_trigger');

			BOX.find('span.ow').data('eid', $(this).data('eid'));
			BOX.show();

			TOP = BOX.offset();
			WINPOS = OBJ.offset();
			//console.log(WINPOS.top+' '+TOP.top);
			POS = WINPOS.top - TOP.top
			BOX.find('.deletion_message').css({'margin-top':POS});

		});
		$('.evoau_delete_trigger').on('click','span.ow',function(){
			var ajaxdataa = {};
			O = $(this);

			MSG = O.closest('.deletion_message');
			MANAGER = O.closest('.evoau_manager');
	        
	        ajaxdataa['action'] = 'evoau_delete_event';
	        ajaxdataa['eid'] = O.data('eid');
			$.ajax({
	            beforeSend: function(){     MSG.addClass('evoloading');  },                  
	            url:    evoau_ajax_script.ajaxurl,
	            data:   ajaxdataa,  dataType:'json', type:  'POST',
	            success:function(data){
	            	MANAGER.find('.evoau_manager_event_rows').html( data.html);
	            	MANAGER.find('.evoau_delete_trigger').hide();	            	
	            },complete:function(){ 	MSG.removeClass('evoloading');   }
	        });
		});
		$('.evoau_delete_trigger').on('click','span.nehe',function(){
			$(this).closest('.evoau_delete_trigger').hide();
		});
		
	// back to event list
		$('#evoau_event_manager').on('click','a.evoau_back_btn',function(){
			MANAGER = $(this).closest('.evoau_manager');
			MANAGER.find('.evoau_manager_event_list').animate({
				'margin-left':0
			},100,function(){
				MANAGER.find('.evoau_manager_event_content').html('');
				load_manager_events( MANAGER, 'none');			
			});
		});

	// Pagination
		$('.evoau_manager_pagination').on('click','.evoau_paginations',function(){
			OBJ = $(this);
			var manager = OBJ.closest('.evoau_manager');
			
			var SECTION = manager.find('.evoau_manager_event_rows');
			
			direction = OBJ.hasClass('next')? 'next':'prev';
			page = parseInt(SECTION.attr('data-page'));

			if(page == 1 && direction =='prev') return false;

			load_manager_events( manager, direction);			
		});

	// load events for the mamanger
		function load_manager_events(manager, direction){

			var SECTION = manager.find('.evoau_manager_event_rows');
			var page = parseInt(SECTION.attr('data-page'));

			var ajaxdataa = {};
			ajaxdataa['action'] = 'evoau_get_paged_events';
	        ajaxdataa['page'] = page;
	        ajaxdataa['direction'] = direction;
	        ajaxdataa['epp'] = SECTION.data('epp');
	        ajaxdataa['uid'] = SECTION.data('uid');
	        ajaxdataa['pages'] = SECTION.data('pages');
	        ajaxdataa['events'] = SECTION.data('events');

	        // if at max pages
	        if( ajaxdataa.pages == ajaxdataa.page && direction =='next') return false;

			$.ajax({
	            beforeSend: function(){    SECTION.addClass('evoloading');    },                  
	            url:    evoau_ajax_script.ajaxurl,
	            data:   ajaxdataa,  dataType:'json', type:  'POST',
	            success:function(data){
	            	SECTION.attr('data-page', data.next_page);
	            	SECTION.html( data.html);	            	
	            },complete:function(){ 
	            	SECTION.removeClass('evoloading');
	            }
	        });
		}

// Form LOADING
	// load form on page load
		$('body').find('.evoau_form_container.waiting').each(function(){

			var ajaxdataa = {};
			const container = $(this);
	        
	        ajaxdataa['action'] = 'evoau_get_form';
	        ajaxdataa['d'] = container.data('d');

			$.ajax({
	            beforeSend: function(){  },                  
	            url:    evoau_ajax_script.ajaxurl,
	            data:   ajaxdataa,  dataType:'json', type:  'POST',
	            success:function(data){

	               	container.html( data.html );
	                FORM = container.find('.evoau_submission_form');

	                $('body').trigger('evoau_loading_form_content', [FORM] );

	            },complete:function(){ 
	            	container.removeClass('evo_ajax_load_events waiting');
	            }
	        });
		});

	// lightbox form trigger
		$('body').on('click','.evoAU_form_trigger_btn',function(){
			OBJ = $(this);
			LIGHTBOX = $('.evoau_lightbox');
			LIGHTBOX.addClass('show');
			$('body').trigger('evolightbox_show');

			// get form html
			var ajaxdataa = {};
	        
	        ajaxdataa['action'] = 'evoau_get_form';
	        ajaxdataa['eid'] = parseInt(OBJ.data('eid'));
	        ajaxdataa['d'] = OBJ.data('d');

			$.ajax({
	            beforeSend: function(){ 
	                LIGHTBOX.find('.evo_lightbox_body').addClass('evoloading').html('<p class="loading_content"></p>');
	            },                  
	            url:    evoau_ajax_script.ajaxurl,
	            data:   ajaxdataa,  dataType:'json', type:  'POST',
	            success:function(data){

	               	LIGHTBOX.find('.evo_lightbox_body').html( data.html );
	                FORM = LIGHTBOX.find('.evoau_submission_form');

	                //load_new_editor('newreply' , $('#evoau_lightbox_form_btn'), $('#evoau_lightbox'));

	                $('body').trigger('evoau_loading_form_content', [FORM] ); 
	            },complete:function(){ 
	            	LIGHTBOX.find('.evo_lightbox_body').removeClass('evoloading');
	            }
	        });

			reset_form( $('.evoau_submission_form').find('form'), 'midcore');
		});

// FIELDS of the event form	
	// FORM interactive triggers	
		$('body').on('evoau_loading_form_content', function(event,FORM){
			form_id = FORM.attr('id');
			_ready_form(form_id);
		});

		function _ready_form(form_id){
			var FORM = $('#'+form_id);

			console.log($.cookie('evoau_event_submited'));
			
			// WYG editor
				if(FORM.find('textarea.evoau_wyg').length>0){
					
					FORM.find('textarea.evoau_wyg').trumbowyg({
						btns: [
					        ['viewHTML'],
					        ['undo', 'redo'], // Only supported in Blink browsers
					        //['formatting'],
					        ['strong', 'em', 'del'],
					        //['superscript', 'subscript'],
					        ['link'],
					        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
					        ['unorderedList', 'orderedList'],
					        ['removeformat'],
					        ['fullscreen']
					    ],
					    autogrow: true
					});
				}

			// edit date time
				FORM.on('click','.evoau_sh_toggle',function(){
					$(this).closest('p').siblings('.evoau_sh_content').toggle();
				});

			// all day events
				FORM.find('#evcal_allday').on('click',function(){
					var timeselect = FORM.find('.row.event_datetime .evoau_time_edit');
					if ($(this).hasClass('NO')) {
						timeselect.fadeOut();
					}else{
						timeselect.fadeIn();
					}
				});
			// no time event
				FORM.find('#evo_hide_endtime').on('click',function(){
					if ($(this).hasClass('NO')) {
						$('#evoAU_endtime_row').hide();
					}else{
						$('#evoAU_endtime_row').show();
					}
				});
			// virtual end time
				FORM.find('#_evo_virtual_endtime').on('click',function(){
					if ($(this).hasClass('NO')) {
						$('#evoAU_virendtime_row').show();
					}else{
						$('#evoAU_virendtime_row').hide();
					}
				});

			// repeating events section
				FORM.find('#evcal_repeat').on('click',function(){
					if ($(this).hasClass('NO')) {
						$('#evoau_repeat_data').show();
					}else{
						$('#evoau_repeat_data').hide();
					}
				});

				FORM.on('click','.evo_repeat_type_val',function(){
					var O = $(this);
					O.siblings().removeClass('select');
					O.addClass('select');

					var _this_val = O.data('val');
					var _this_v = O.data('v');

					// setting
					FORM.find('.evcal_rep_gap_name').html( _this_v );
					O.siblings('input').val( _this_val );

					if( _this_val == 'custom'){
						O.closest('#evoau_repeat_data').find('.evo_preset_repeat_settings').hide();
						O.closest('#evoau_repeat_data').find('.evo_custom_repeat_settings').show();
					}else{
						O.closest('#evoau_repeat_data').find('.evo_preset_repeat_settings').show();
						O.closest('#evoau_repeat_data').find('.evo_custom_repeat_settings').hide();
					}
				});

				// custom repeat features
					// view all
					FORM.on('click','.evo_repeat_interval_view_all',function(){
						var O = $(this);
						if( O.hasClass('S')){
							$(this).closest('.evo_custom_repeat_settings').find('li.over').hide();
							O.removeClass('S');
						}else{
							$(this).closest('.evo_custom_repeat_settings').find('li').show();
							O.addClass('S');
						}	
					});
					// add new repeat
					FORM.on('click','#evo_add_repeat_interval',function(){
						var new_rp = FORM.find('.evo_repeat_interval_new');
						if( new_rp.is(":visible")){
							// add new custom repeat times
							if( new_rp.find('.datepickerstartdate').val() &&
								new_rp.find('.datepickerenddate').val()
							){		
								var count = 1;
								var ul = new_rp.siblings('.evo_custom_repeat_list');
								if( ul.find('li').length > 0){
									count = parseInt( ul.find('li:last-child').data('cnt'))+1;	
								}

								var SD = new_rp.find('.datepickerstartdate').val();
								var ST = new_rp.find('._start_hour').val() +':'+ new_rp.find('._start_minute').val();
								if( new_rp.data('h24') == 'n') ST += new_rp.find('._start_ampm').val();

								var ED = new_rp.find('.datepickerenddate').val();
								var ET = new_rp.find('._end_hour').val() +':'+ new_rp.find('._end_minute').val();
								if( new_rp.data('h24') == 'n') ET += new_rp.find('._end_ampm').val();


								var html = '<li data-cnt="'+count+'" class="new"><span>from</span>'+ SD +' '+ST+' <span class="e">End</span> '+ ED +' '+ ET +'<em alt="Delete">x</em>';
								html += '<input type="hidden" name="repeat_intervals['+count+'][0]" value="'+ new_rp.find('.evoau_start_alt_date').val()+' '+ ST +'"/>';
								html +='<input type="hidden" name="repeat_intervals['+count+'][1]" value="'+ new_rp.find('.evoau_end_alt_date').val()+' '+ ET +'"/>';
								html +='<input type="hidden" name="repeat_intervals['+count+'][type]" value="dates"></li>';

								$('ul.evo_custom_repeat_list').append(html);

								// reset the date time picker fields

									new_rp.find('.datepickerstartdate').val('').datepicker('option','maxDate',null);
									new_rp.find('.datepickerenddate').val('').datepicker('option','minDate',null);

									//console.log(new_rp.find('.datepickerstartdate').val());
							}else{
								$('.evo_repeat_interval_button').find('span').fadeIn().html(' All fields are required!').delay(2000).fadeOut();
							}
						}else{
							new_rp.show();
						}
					});

					// delete a custom repeat
					FORM.on('click','.evo_custom_repeat_list em',function(){
						var li = $(this).parent();
						li.slideUp(function(){
							li.remove();
						});
					});

			// Image selection
				FORM.find('.evoau_img_input').bind('change focus click',function(){
					var INPUT = $(this),
						BTN = INPUT.siblings('.evoau_img_btn'),
				      	$val = INPUT.val(),
				      	valArray = $val.split('\\'),
				      	newVal = valArray[valArray.length-1],
				     	$fakeFile = INPUT.siblings('.file_holder');

				     console.log(newVal);
				  	
				  	if(newVal !== '') {
				   		var btntext = INPUT.attr('data-text');
				   		
				    	BTN.text( btntext);
				    	
				    	if($fakeFile.length === 0) {
				    	  	BTN.after('<span class="file_holder">' + newVal + '</span>');
				    	} else {
				      		$fakeFile.text(newVal);
				    	}		    	
				  	}
				});
				// remove existing images
					$(FORM).on('click','.evoau_event_image_remove',function(){
						ROW = $(this).closest('.row');
						ROW.find('.evoau_img_preview').hide();
						ROW.find('.evoau_file_field').show();
						//ROW.find('.evoau_img_preview').remove();
						$(this).siblings('input').val('no');
					});
				// run actual input field image when click on span button
					$(FORM).on('click','.evoau_img_btn',function(){
						$(this).parent().find('input').click();
					});

			// date picker
				var dateformat__ = FORM.find('#_evo_date_format').attr('jq');
				date_format = (typeof dateformat__ !== 'undefined' && dateformat__ !== false)?	
				dateformat__: 'dd/mm/yy';
					
				var SOW = FORM.find('.evo_date_time_select').data('sow');
				
				if(FORM.find('.datepickerstartdate').length>0){

					//set date picker local values
						var txtOBJ = FORM.find('.evoau_dp_text');
						MN = txtOBJ.data('mn');
						DN = txtOBJ.data('dn');
						FDN = txtOBJ.data('fdn');
						OT = txtOBJ.data('ot');	

						$.datepicker.regional['EVO'] = {
						    monthNames: MN, // set month names
						    dayNames: FDN, // set more short days names
						    prevText: OT.txtprev,
			    			nextText: OT.txtnext,
						};
						if(DN != '' && DN !== undefined) $.datepicker.regional['EVO'].dayNamesMin = DN;
						$.datepicker.setDefaults($.datepicker.regional['EVO']);

					FORM.find('.datepickerstartdate').each(function(){
						$(this).datepicker({ 
							dateFormat: date_format,
							numberOfMonths: 1,
							firstDay: SOW,
							altField: $(this).siblings('input.alt_date'),
							altFormat: 'yy-mm-dd',
							onClose: function( selectedDate ) {
								var assoc = $(this).data('assoc');
								FORM.find('.datepickerenddate[data-assoc="'+assoc+'"]').datepicker( "option", "minDate", selectedDate );
								FORM.find('.datepickervirdate[data-assoc="'+assoc+'"]').datepicker( "option", "minDate", selectedDate );
						    }					    
						});
					});
				}

				// end date
				if(FORM.find('.datepickerenddate').length>0){
					FORM.find( ".datepickerenddate" ).each(function(){
						$(this).datepicker({ 
							dateFormat: date_format,
							numberOfMonths: 1,
							firstDay: SOW,
							altField: $(this).siblings('input.alt_date'),
							altFormat: 'yy-mm-dd',
							onClose: function( selectedDate ) {
								var assoc = $(this).data('assoc');
								FORM.find('.datepickerstartdate[data-assoc="'+assoc+'"]').datepicker( "option", "maxDate", selectedDate );
					      	}
						});
					});
				}

				// virtual end date
				if(FORM.find('.datepickervirdate').length>0){
					FORM.find( ".datepickervirdate" ).each(function(){
						$(this).datepicker({ 
							dateFormat: date_format,
							numberOfMonths: 1,
							firstDay: SOW,
							altField: $(this).siblings('input.alt_date'),
							altFormat: 'yy-mm-dd',
							onClose: function( selectedDate ) {
								var assoc = $(this).data('assoc');
								FORM.find('.datepickerstartdate[data-assoc="'+assoc+'"]').datepicker( "option", "maxDate", selectedDate );
					      	}
						});
					});
				}

			// color picker
				ITEM = FORM.find('.color_circle');
				ITEM.ColorPicker({
					onBeforeShow: function(){
						$(this).ColorPickerSetColor( $(this).attr('data-hex'));
					},	
					onChange:function(hsb, hex, rgb,el){

						$(this).attr({'backgroundColor': '#' + hex, 'data-hex':hex}).css('background-color','#' + hex);
						ITEM.attr({'backgroundColor': '#' + hex, 'data-hex':hex}).css('background-color','#' + hex);
						set_rgb_min_value(rgb,'rgb', ITEM);
						ITEM.next().find('.evcal_event_color').attr({'value':hex});
					},	
					onSubmit: function(hsb, hex, rgb, el) {					
						var sibb = ITEM.siblings('.evoau_color_picker');
						sibb.find('.evcal_event_color').attr({'value':hex});
						ITEM.css('backgroundColor', '#' + hex);				
						ITEM.ColorPickerHide();
						set_rgb_min_value(rgb,'rgb', ITEM);
					}
				});

			// event status 
				FORM.on('click','.es_sin_val',function(){
					var V = $(this).attr('value');
					var O = $(this);
					var P = O.closest('p');
					P.find('.es_sin_val').removeClass('select');
					O.addClass('select');

					P.find('input').val( V );
					P.siblings('div').hide();
					P.siblings('.'+ V +'_extra').show();
				});
			
			// edit location and organizer
				$(FORM).on('click','.editMeta',function(){
					$(this).closest('.row').find('.enterownrow').slideToggle();
				});
		}
	
	// color picker		
		/** convert the HEX color code to RGB and get color decimal value**/
			function set_rgb_min_value(color,type, ITEM){			
				if( type === 'hex' ) {			
					var rgba = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(color);	
					var rgb = new Array();
					 rgb['r']= parseInt(rgba[1], 16);			
					 rgb['g']= parseInt(rgba[2], 16);			
					 rgb['b']= parseInt(rgba[3], 16);	
				}else{
					var rgb = color;
				}			
				var val = parseInt((rgb['r'] + rgb['g'] + rgb['b'])/3);			
				ITEM.next().find('.evcal_event_color_n').attr({'value':val});
			}

	// click on user interaction field
		$('body').on('change', '.evoau_submission_form .evoau_ui select', function(){
			var value = $(this).val();
			if(value==2){
				$(this).parent().siblings('.evoau_exter').slideDown();
			}else{
				$(this).parent().siblings('.evoau_exter').slideUp();
			}
		});

	// location saved list
		$('body').on('change','.evoau_location_select',function(){
			option = $(this).find(':selected');
			FORM = $(this).closest('form');

			// address
			FORM.find('input[name=evcal_location_name]').attr('value',option.text());
			FORM.find('input[name=location_address]').attr('value',option.attr('data-add'));
			FORM.find('input[name=evcal_location_link]').attr('value',option.attr('data-link'));
			FORM.find('input[name=evo_loc_img_id]').attr('value',option.attr('data-img'));
			
			if(option.attr('data-lat')!= '' && option.attr('data-lat')!== undefined) 
				FORM.find('input[name=event_location_cord]').attr('value',option.attr('data-lat')+','+option.attr('data-lon'));
		});

	// Organizer saved list
		$('body').on('change','.evoau_organizer_select',function(){
			option = $(this).find(':selected');
			FORM = $(this).closest('form');

			FORM.find('input[name=evcal_organizer]').attr('value',option.text());
			FORM.find('input[name=evcal_org_address]').attr('value',option.attr('data-address'));
			FORM.find('input[name=evcal_org_contact]').attr('value',option.attr('data-contact'));
			FORM.find('input[name=evcal_org_exlink]').attr('value',option.attr('data-exlink'));
			FORM.find('input[name=evo_org_img_id]').attr('value',option.attr('data-img'));				
		});

		// enter new organizer or location
			$('body').on('click','.enterNew',function(){
				OBJ = $(this);
				ROW = OBJ.closest('.row'); 
				var txt = OBJ.attr('data-txt'), html = OBJ.html();
				
				// trying to select previously saved
				if(OBJ.hasClass('newitem')){				
					ROW.find('.enterownrow').hide().find('input').val('');
					OBJ.removeClass('newitem');
					OBJ.siblings().show();	
				}else{ // Enter new			
					ROW.find('.enterownrow').show().find('input').val('');
					OBJ.addClass('newitem');	
					OBJ.siblings().hide();	
				}
				// change button text
				if(OBJ.attr('data-st')=='ow')
					OBJ.html(txt).attr('data-txt',html);		
			});

// FORM submission
	$('body').on('click','.evoau_submission_form',function(){
		$(this).removeClass('errorForm');
		$(this).find('.formeMSG').fadeOut();
	});
	
	$('body').on('click','.evoau_event_submission_form_btn',function(e){
		e.preventDefault();

		var form = $(this).closest('form'),
			formp = form.parent(),
			errors = 0,
			msg_obj = form.find('.formeMSG');


			FORM_TYPE = form.find('input[name=form_action]').val()=='editform'?'edit':'new';

		// tiny MCE
			if(typeof tinyMCE !== 'undefined'){
				if(form.find('.event_description').length>0) tinyMCE.triggerSave();
			}			

		var data_arg = {};

		// form notification messages
			var nof = formp.find('.evoau_json');
			nof = nof.data('j');

		reset_form(form);
					
		// check required fields missing
			form.find('.req').each(function(i, el){
				var el = $(this);
				var val = el.val();
				var elname = el.attr('name');

				// hide end time 
				if( !$('#evo_hide_endtime').hasClass('NO') && (elname=='event_end_date' || elname=='event_end_time')) return true;
				
				// no end time
				if( !$('#evcal_allday').hasClass('NO') && ( elname =='event_end_time' || elname == 'event_start_time')) return true;

				if(val.length==0){
					// if required field dependancy is present
					if( el.data('reqd') != '' && el.data('reqd') !== undefined){
						JDATA = el.data('reqd');
						
						FIELD = form.find('[name="'+JDATA.name +'"]');
						if(FIELD.val() == JDATA.value){
							errors++;
							el.closest('.row').addClass('err');	
						}
					}else{
						errors++;
						el.closest('.row').addClass('err');
					}										
				}
			});

		// check for captcha validation
			if(form.find('.au_captcha').length>0){
				var field = form.find('.au_captcha input'),
					cval = field.val();

				validation_fail = false;

				if(cval==undefined || cval.length==0){
					validation_fail = true;
				}else{
					var numbers = ['11', '3', '6', '3', '8'];
					if(numbers[field.attr('data-cal')] != cval )
						validation_fail = true;
				}
				if(validation_fail){
					errors = (errors == 0)? 20:errors+1;
					form.find('.au_captcha').addClass('err');
				}
			}

		// save cookie if submission is limited
			if(form.data('limitsubmission')=='ow' ){
				if($.cookie('evoau_event_submited')=='yes'){
					formp.addClass('errorForm limitSubmission');
					form.find('.inner').slideUp();
					form.find('.evoau_form_messages').html('<p>'+nof.nof6+'</p>').show();
					return false;
				}else{
					$.cookie('evoau_event_submited','yes',{expires:24});
				}			
			}

		// pass correct event descriptions
		if (form.find("#wp-event_descriptionau-wrap").hasClass("tmce-active")){
	        FF = tinyMCE.activeEditor.getContent();
	        form.find('#wp-event_descriptionau-wrap').find('textarea[name="event_description"]').val(FF);
	    }

	    // append additional form data
	    var ajax_data = {};
	    ajax_data['form_atts_data'] = form.closest('.evoau_form_container').data('d');

		
		if(errors==0){
			form.ajaxSubmit({
				beforeSubmit: function(){						
					formp.addClass('evoloadbar bottom');
				},
				data: ajax_data,
				dataType:'json',
				url:evoau_ajax_script.ajaxurl,
				success:function(data, statusText, xhr, $form){
					if(data.status=='good'){
						form.find('.inner').html('');

						// show success msg
						form.find('.evoau_form_messages').html( data.success_message_html );
						formp.addClass('successForm');

						// redirect page after success form submission
						if(form.attr('data-redirect')!='nehe'){
							RDUR = (form.attr('data-rdur') !='' && form.attr('data-rdur')!== undefined)? 
								form.data('rdur'):0;

							RDUR = RDUR*1000;

							setTimeout(function(){
								window.location = form.attr('data-redirect');
							}, RDUR);
						}

						// scroll to top of form to show success message , if not lightbox
						if(!form.hasClass('lightbox') )
							$('html, body').animate({scrollTop: form.offset().top - 80}, 500);

					}else{
						MSG = (data.msg=='bad_nonce')? nof.nof5: eval('nof.' + data.msg);
						msg_obj.html( MSG).fadeIn();
					}
					formp.removeClass('evoloadbar bottom');													
				}
			});			
		}else{
			formp.addClass('errorForm');
			
			//console.log(errors);
			e.preventDefault();
			var msg = (errors==20)? nof.nof2: nof.nof0;
			msg_obj.html(msg).slideDown('fast');
			return false;
		}
	});

// submit another event
	$('body').on('click','.evoau_submit_another_trig',function(){

		const container = $(this).closest('.evoau_form_container');

		var ajaxdataa = {};
        
        ajaxdataa['action'] = 'evoau_get_form';
        ajaxdataa['d'] = container.data('d');

		$.ajax({
            beforeSend: function(){ 
            	container.addClass('evoloading');
            },                  
            url:    evoau_ajax_script.ajaxurl,
            data:   ajaxdataa,  dataType:'json', type:  'POST',
            success:function(data){

               	container.html( data.html );
                FORM = container.find('.evoau_submission_form');

                $('body').trigger('evoau_loading_form_content', [FORM] );

            },complete:function(){ 
            	container.removeClass('evo_ajax_load_events waiting evoloading');
            }
        });

		FORM = $(this).closest('form');

	});

// complete form actions
	function reset_form(form, type){		
		
		form.find('.row').removeClass('err');
		form.parent().removeClass('successForm errorForm');

		form.find('.inner').show();
		form.find('.evoau_success_msg').hide();
		form.find('.evoau_form_messages').html();

		if(type=='hardcore' || type=='midcore'){
			form.find('input[type=text]').val('');
			form.find('input[type=checkbox]').attr('checked', false);
			form.find('textarea').val('');
			$('#evoAU_endtime_row').show();
			$('.evoau_tpicker ').show();

			// select fields
			form.find('select').each(function(){
				$(this).val('-');
			});

			// reset wysiwyg editor
			if (form.find("#wp-event_descriptionau-wrap").hasClass("tmce-active")){
		        tinyMCE.activeEditor.setContent('');
		        tinyMCE.triggerSave();		        
		    }

		    // date field
		    form.find('.datepickerstartdate').datepicker( "option", "maxDate", null );
		    form.find( ".datepickerenddate" ).datepicker( "option", "minDate", null );

			// repeat information
			$('#evcal_allday').addClass('NO').siblings('input').val('no');
			$('#evo_hide_endtime').addClass('NO').siblings('input').val('no');
			$('#evcal_repeat').addClass('NO').siblings('input').val('no');
			$('#evoau_repeat_data').hide();

			// image field
			imgfield = form.find('.evoau_file_field');
			imgfield.find('.file_holder').html('');
		}

		if(type=='hardcore'){
			form.find('.eventon_form_message').removeClass('error_message').fadeOut();
		}
	}

// SUPPORTIVE
	// select2 for location select field
	if (typeof select2 == 'function') { 
		$('.evoau_location_select').select2();
	}

	// increase and reduce quantity
    $('body').on('click','.evoaup_qty_change', function(event){
        OBJ = $(this);
        QTY = parseInt(OBJ.siblings('em').html());
        MAX = OBJ.siblings('input').attr('max');

        var pfd = OBJ.parent().data('pfd');

        (OBJ.hasClass('plu'))?  QTY++: QTY--;

        QTY =(QTY==0)? 1: QTY;
        QTY = (MAX!='' && QTY > MAX)? MAX: QTY;

        // new total price
        var sin_price = OBJ.parent().data('p');
        new_price = sin_price * QTY;

        new_price = get_format_price( new_price, pfd);

        OBJ.closest('.evoaup_purchase').find('.total .value').html( new_price);

        OBJ.siblings('em').html(QTY);
        OBJ.siblings('input').val(QTY);
    });

    // Total formating
        function get_format_price(price, data){

            // price format data
            PF = data;
           
            totalPrice = price.toFixed(PF.numDec); // number of decimals
            htmlPrice = totalPrice.toString().replace('.', PF.decSep);


            if('thoSep' in PF && PF.thoSep.length > 0) {
                htmlPrice = _addThousandSep(htmlPrice, PF.thoSep);
            }
            if(PF.curPos == 'right') {
                htmlPrice = htmlPrice + PF.currencySymbol;
            }
            else if(PF.curPos == 'right_space') {
                htmlPrice = htmlPrice + ' ' + PF.currencySymbol;
            }
            else if(PF.curPos == 'left_space') {
                htmlPrice = PF.currencySymbol + ' ' + htmlPrice;
            }
            else {
                htmlPrice = PF.currencySymbol + htmlPrice;
            }
            return htmlPrice;
        }
        function _addThousandSep(n, thoSep){
            var rx=  /(\d+)(\d{3})/;
            return String(n).replace(/^\d+/, function(w){
                while(rx.test(w)){
                    w= w.replace(rx, '$1'+thoSep+'$2');
                }
                return w;
            });
        };



});

