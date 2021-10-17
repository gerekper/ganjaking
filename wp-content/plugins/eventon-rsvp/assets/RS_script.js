/**
 * Javascript: RSVP Events Calendar
 * @version  2.6.5
 */
jQuery(document).ready(function($){
	
	init();	

	var JDATA = {};	
	var submit_open = false;
	
	// INITIATE script
		function init(){		
			$('body').on('click','.evoRS_status_option_selection span',function(){
				show_rsvp_form( $(this), $(this).attr('data-val'), 'submit');
			});
		}

	// load JSON DATA for event's rsvp into local object
		function load_json_to_local(O){
			JDATA = O.closest('.evo_metarow_rsvp').find('.evors_jdata').data('j');
		}	
	
	// RSVP form interactions
		// change RSVP status within the form
			$('body').on('click', 'span.evors_choices', function(){
				OBJ = $(this);

				VAL = OBJ.attr('data-val');
				SUBMISSION_FORM = OBJ.closest('form.evors_submission_form');
				SUBMISSION_FORM.attr('class','evors_submission_form  rsvp_'+VAL);
				OBJ.siblings().removeClass('set');
				OBJ.addClass('set');

				OBJ.siblings('input').val( VAL );

			});
		// close RSVP form from incard close button
			$('body').on('click','.evors_incard_close',function(){
				PAR = $(this).parent();
				PAR.slideUp(function(){
					PAR.html('');
				});
				// reset any selected RSVP choices
				$(this).closest('.evo_metarow_rsvp').find('.evors_choices').removeClass('set');
			});

		// checkbox field
			$('body').on('click', '.evors_checkbox_field', function(){
				O = $(this);
				if(O.hasClass('checked')){
					O.removeClass('checked');
					O.siblings('input').val('no');
				}else{
					O.addClass('checked');
					O.siblings('input').val('yes');
				}
			});
		
	// RSVP from eventtop
		$('body').on('click', '.evors_rsvpiable span.evors_choices', function(event){
			event.preventDefault();
			event.stopPropagation();

			load_json_to_local( $(this) );


			var obj = $(this),
				rsvp = obj.parent(),
				ajaxdataa = {};

			ajaxdataa['rsvp']= obj.data('val');				
			ajaxdataa['lang']= rsvp.data('lang'); 
			ajaxdataa['uid']= rsvp.data('uid'); 
			ajaxdataa['updates']= 'no';	
			ajaxdataa['action']='the_ajax_evors_a7';
			ajaxdataa['repeat_interval']=rsvp.data('ri');
			ajaxdataa['e_id']= parseInt(rsvp.data('eid'));


			var event_uid = 'event_'+ ajaxdataa.e_id +'_'+ ajaxdataa.repeat_interval;
			
			$.ajax({
				beforeSend: function(){	rsvp.addClass('loading');	},
				type: 'POST',
				url:evors_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='0'){
						$('body').trigger('evors_new_rsvp_eventtop');

						// update events eventwhere
						$('body').find('.eventon_list_event.'+event_uid).each(function(){
							$(this).find('.evors_rsvpiable').html(data.message).addClass('success');
							$(this).find('.evors_eventtop_section_data').replaceWith(data.content);

							// update event card
							if( data.card_content !== undefined){
								$(this).find('.evors_eventcard_content').html( data.card_content);
							}
						});			
						
					}else{
						rsvp.append('<span class="error">'+data.message+'</span>');
					}
				},complete:function(){
					rsvp.removeClass('loading');
				}
			});	
		});
	
	// RSVP form submissions & update existing
		$('body').on('click', '.evors_submit_rsvpform_btn', function(){	

			var obj = O = $(this),
				ajaxdataa = { },
				form = obj.closest('form.evors_submission_form'),
				FORMPAR = form.parent(),
				formSection = form.parent(),
				error = 0,
				formType = form.find('input[name="formtype"]').val();

			
			// reset form error messages
				rsvp_hide_notifications();
				FORMPAR.parent().removeClass('error');
			
			// validation
				// run through each rsvp field
					form.find('.input').each(function(index){
						iO = $(this);

						$(this).removeClass('err');
						
						// required checkbox field
						if( iO.hasClass('req') && iO.hasClass('checkbox') && iO.val()=='no'){
							error = 1;
							iO.siblings('em').addClass('err');
						}

						// check required fields filled
						if( iO.hasClass('req') && iO.val()=='' && iO.is(":visible")){
							error = 1;
							iO.addClass('err');
						}						

						if( $(this).val() == '' ) return true;
						//ajaxdataa[ $(this).attr('name') ] = encodeURIComponent( $(this).val() );					
					});

				// validate email
					if(error==0){
						var thisemail = form.find('input[name=email]');
						if(!is_email(thisemail.val().trim() )){
							thisemail.addClass('err');
							rsvp_error('err2','','', form); // invalid email address
							error = 2;
						}
					}	
				// capacity check
					if(error==0){
						if(formType=='update'){
							pastVal = parseInt(form.find('input[name=count]').attr('data-pastval'));
							newVal = parseInt(form.find('input[name=count]').val());
							compareCount = (pastVal>newVal)? 0: newVal-pastVal;
						}else{							
							compareCount =  parseInt(form.find('input[name=count]').val());
						}
						
						if(form.find('.rsvp_status span.set').data('val')!='n' 
							&& JDATA.cap
							&& JDATA.cap !='na' 
							&& compareCount > parseInt(JDATA.cap) 
						){
							error=4;
							form.find('input[name=count]').addClass('err');
							rsvp_error('err9','','',form);
						}
						// max count limit
						if( JDATA.precap!='na' && 
							(parseInt(form.find('input[name=count]').val()) > parseInt(JDATA.precap) ) 
						){
							error=4;
							form.find('input[name=count]').addClass('err');
							rsvp_error('err10','','',form);
						}
					}			
				// validate human
					if(error==0){
						var human = rsvp_validate_human( form.find('input.captcha') );
						if(!human){
							error=3;
							rsvp_error('err6','','',form);
						}
					}	

			// if form type is wl-remove
				if(formType == 'wl-remove') error = 0;			
				
			if(error==0){
				var updates = form.find('.updates input').attr('checked');
					updates = (updates=='checked')? 'yes':'no';

				ajaxdataa['action']='the_ajax_evors';
				form.ajaxSubmit({			
				//$.ajax({
					beforeSend: function(){	form.parent().addClass('loading');	},
					type: 'POST',url:evors_ajax_script.ajaxurl,data: ajaxdataa,dataType:'json',
					success:function(data){
						if(data.status=='0'){

							
							FORMPAR.parent().html(data.message);
							EVENTCARD = $('body').find('.event_'+data.e_id+'_'+ data.ri );

							// if lightbox event
							lb_eventcard = $('body').find('.evo_lightbox_body.event_'+ data.e_id+'_'+ data.ri);
							
							// update event top data
								if( 'data_content_eventtop' in data){
									// lightbox
									if(lb_eventcard.find('.evors_eventtop_section_data').length>0){
										lb_eventcard.find('.evors_eventtop_section_data').replaceWith(	data.data_content_eventtop	);
									}

									// calednar
									if(EVENTCARD.find('.evors_eventtop_section_data').length>0){
										EVENTCARD.find('.evors_eventtop_section_data').replaceWith(	data.data_content_eventtop	);
									}
								}
								if('data_content_eventtop_your' in data){
									// lightbox
									if(lb_eventcard.find('.evors_eventop_rsvped_data').length>0){
										lb_eventcard.find('.evors_eventop_rsvped_data').replaceWith(	data.data_content_eventtop_your	);
									}
									// calendar
									if(EVENTCARD.find('.evors_eventop_rsvped_data').length>0){
										EVENTCARD.find('.evors_eventop_rsvped_data').replaceWith(	data.data_content_eventtop_your	);
									}
								}

							// update Event Card
								if(data.e_id){					
									if(data.data_content_eventcard != ''){
										EVENTCARD.find('.evors_eventcard_content').html( data.data_content_eventcard);
										lb_eventcard.find('.evors_eventcard_content').html( data.data_content_eventcard);
									}
								}
							// update event manager stuff
								if( $('body').find('#rsvp_event_'+data.e_id).length>0 && data.new_rsvp_text){
									STATUS = $('#rsvp_event_'+data.e_id).find('span.rsvpstatus');
									STATUS.html( data.new_rsvp_text);
									STATUS.attr('class','rsvpstatus status_'+data.new_rsvp_text);
								}	

							// update virtual event information
								if( $('body').find('.evo_vir_data').length>0){
									const vir_data_box =  $('body').find('.evo_vir_data');

									if( vir_data_box.data('single') !== undefined 
										&& vir_data_box.data('single') == 'y' && 
										vir_data_box.data('refresh')
									){
										extra_data = {};
										extra_data['refresh_main'] = 'yy';
										$('body').trigger('evo_refresh_designated_elm',[ vir_data_box,'evo_vir_data', extra_data]);
									}
								}
								

						}else{
							var passedRsvppd = (data.status)? 'err'+data.status:'err7';
							rsvp_error(passedRsvppd, '', data.message,form);
						}
					},complete:function(){	form.parent().removeClass('loading');	}
				});				
			}else if(error==1){	rsvp_error('err','','',form);	}	
		});
	
	// capacity check real-time
		$('body').on('change','input.evors_rsvp_count',function(){	
			O = $(this);
			
			// reset
				$('.evors_lightbox_body').removeClass('error');
				$(this).removeClass('err');
				rsvp_hide_notifications();

			// get form type
				FORM = O.closest('form');
				formtype = FORM.find('input[name=formtype]').val();

			
			CAP = JDATA.cap;
			PERCAP = JDATA.precap;
			FORM = O.closest('.evors_submission_form');

			VAL = parseInt(O.val());
			passedVAL = parseInt(O.data('passed'));

			ERROR = false;


			// change RSVP form
			if( formtype == 'update'){
				// no space left but waitlist
				if(CAP == 'wl' && VAL> passedVAL){
					$(this).addClass('err');
					rsvp_error('err9','','',FORM); 
					ERROR = true;
				}

				changeVAL =  VAL - passedVAL;

				// increasing count
				if(CAP != 'wl' && changeVAL>0 && changeVAL> parseInt(CAP) ){
					$(this).addClass('err');
					rsvp_error('err9','','',FORM); 
					ERROR = true;
				}
			}else{
				// check avialable space vs party size
					if(VAL > parseInt(CAP) && CAP!= 'na'){
						$(this).addClass('err');
						rsvp_error('err9','','',FORM); 
						ERROR = true;
					}
				// check per each rsvp capacity vs party size
					if(VAL > parseInt(PERCAP) && PERCAP!= 'na'){
						$(this).addClass('err');
						rsvp_error('err10','','',FORM); 
						ERROR = true;
					}
			}						

			if(!ERROR){
			// if valid capacity add additional guests
				guestNames = O.closest('.evors_submission_form').find('.form_guest_names');
				if(VAL>1){
					
					maskField = '<input class="regular input" name="names[]" type="text">';			
					inputHolder = guestNames.find('.form_guest_names_list');
					ExistInputCount = inputHolder.find('input').length;
					
					// add or remove input fields
					if( (VAL-1) > ExistInputCount){ // add
						fieldsNeed = VAL-1-ExistInputCount;
						appender ='';
						for(x=0; x<fieldsNeed; x++){
							appender += maskField;
						}
						inputHolder.append(appender);
					}else{
						fieldsNeed = VAL-2;
						inputHolder.find('input').each(function(index){
							if(index> fieldsNeed) $(this).remove();
						});
					}
					guestNames.show();
				}else{
					guestNames.hide();
				}
			}
		});
	
	// CHANGE RSVP
		// change a RSVP
			$("body").on('click','.evors_change_rsvp_trig',function(){				
				OBJ = $(this);
				PAR = OBJ.parent();
				show_rsvp_form(OBJ, '','update');
			});

		// from successful rsvped page
			$('body').on('click','#call_change_rsvp_form',function(){
				OBJ = $(this);
				PAR = OBJ.parent();
				show_rsvp_form(OBJ, '','update');
			});
		// From rsvp manager
			$('.eventon_rsvp_rsvplist').on('click','.update_rsvp',function(){
				OBJ = $(this);
				PAR = OBJ.parent();

				// load Json data					
				JDATA = OBJ.parent().siblings('.evors_jdata').data('j');
				console.log(JDATA);

				show_rsvp_form(OBJ, '','update');
			});

	// Show RSVP lightbox form
		function show_rsvp_form(OBJ, RSVP,formtype, extra_data){
			var ajaxdataa = {};
			ajaxdataa['action']='evors_get_rsvp_form';

			ROW = OBJ.closest('.evo_metarow_rsvp');
			if(OBJ.closest('.evo_metarow_rsvp').length) load_json_to_local( OBJ);

			if(JDATA){
				$.each(JDATA, function(index, val){
					ajaxdataa[index] = val;
				});
			}

			if( extra_data && extra_data !== undefined){
				$.each(extra_data, function(ind, vall){
					ajaxdataa[ ind ] = vall;
				});
			}

			ajaxdataa['rsvp'] = RSVP;
			ajaxdataa['formtype'] = formtype;
			FORMNEST = OBJ.closest('.evors_forms').parent();
			
			$.ajax({
				beforeSend: function(){ 
					loading(OBJ);	
				},					
				url:	evors_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if(data.status=='good'){
						// show form inside eventcard
						if( ajaxdataa.incard =='yes'){
							ROW.find('.evors_incard_form')
								.removeClass('error')
								.html( data.content )
								.slideDown();							
						}else{
							$('.evors_lightbox')
								.find('.evo_lightbox_body')
								.removeClass('error')
								.html( data.content );
							$('.evors_lightbox.evo_lightbox').addClass('show');
							$('body').trigger('evolightbox_show');
						}
						
					}else{
						// error notice ***
					}
				},complete:function(){ 
					completeloading(OBJ);	
					FORMNEST.closest('.evorow').removeClass('loading');
				}
			});
		}

		// during ajax eventcard loading
			function loading(obj){	
				obj.closest('.evorow').addClass('loading');	
				obj.closest('.trig_evo_loading').addClass('evoloading');	
				obj.closest('p.rsvpmanager_event').addClass('loading');	
			}
			function completeloading(obj){
				obj.closest('.evorow').removeClass('loading');
				obj.closest('.trig_evo_loading').removeClass('evoloading');
				obj.closest('p.rsvpmanager_event').removeClass('loading');	
			}
		
		// Find RSVP
			$('body').on('click','.evors_findrsvp_form_btn', function(){
				var obj = $(this);			
				var form = obj.closest('form.evors_findrsvp_form');
				FORM_PAR = obj.closest('.evors_forms');
				var error = 0;

				// run through each rsvp field
					form.find('.input').each(function(index){
						// check required fields filled
						if( $(this).hasClass('req') && $(this).val()=='' ){
							error = 1;
						}
					});							
				if(error=='1'){
					rsvp_error('err','','',form);
				}else{
					var ajaxdataa = {};
					ajaxdataa['action']='evors_find_rsvp_form';
					form.ajaxSubmit({
						beforeSend: function(){ 	FORM_PAR.addClass('loading');		},
						url:	evors_ajax_script.ajaxurl,
						data: 	ajaxdataa,	dataType:'json', type: 	'POST',
						success:function(data){
							if(data.status=='good'){
								FORM_PAR.parent().removeClass('error').addClass('t');
								FORM_PAR.parent().html( data.content );
								
							}else{
								rsvp_error('err5','','',form);
							}
						},complete:function(){ 	FORM_PAR.removeClass('loading');	}
					});
				}				
			});
			
	// hover over guests list icons
		$('body').on('mouseover','.evors_whos_coming span.initials', function(){
			OBJ = $(this);
			EM = OBJ.parent().find('em.tooltip');
			TEXT = OBJ.data('name');

			POS = OBJ.position();
			
			EM.css({'left':(POS.left+20), 'top':(POS.top-30)}).html(TEXT).show();

		});
		$('body').on('mouseout','.evors_whos_coming span', function(){
			OBJ = $(this);
			EM = OBJ.parent().find('em.tooltip');
			EM.hide();
		});

	// Buddypress profile linking
		$('body').on('click','.evors_whos_coming span',function(){
			LINK = $(this).data('link');

			if(LINK != 'na')
				window.open(LINK, '_blank');
		});

	

// ADDONS
	// action  user event manager
		// show rsvp stats for events
			$('#evoau_event_manager').on('click','a.load_rsvp_stats',function(event){
				event.preventDefault();					
				O = $(this);

				var data_arg = {
					eid: O.data('eid'),
					ri: O.data('ri'),
					data: O.closest('.evoau_manager').find('.evoau_manager_json').data('js')
				};

				load_rsvp_stats( O , data_arg);
			});
		// refresh rsvp data
			$(document).on('click','.evorsau_refresh_data',function(){
				O = $(this);
				var data_arg = {
					eid: O.closest('.evoau_manager_continer').data('eid'),
					ri: O.closest('.evoau_manager_continer').data('ri'),
					data: O.closest('.evoau_manager').find('.evoau_manager_json').data('js')
				};

				load_rsvp_stats( O , data_arg, true);
			});

			function load_rsvp_stats(O, data_arg, refresh){
				data_arg['action'] = 'evors_ajax_get_auem_stats';
				MANAGER = O.closest('.evoau_manager');
				$.ajax({
					beforeSend: function(){
						MANAGER.find('.trig_evo_loading').addClass('evoloading');
						MANAGER.find('.eventon_actionuser_eventslist').addClass('evoloading');
					},
					type: 'POST',
					url:evors_ajax_script.ajaxurl,
					data: data_arg,
					dataType:'json',
					success:function(data){
						if( refresh){
							MANAGER.find('.evoau_manager_event_content').html( data.html );
						}else{
							$('body').trigger('evoau_show_eventdata',[MANAGER, data.html, true]);
						}
						
	           		},complete:function(){ 
						MANAGER.find('.eventon_actionuser_eventslist').removeClass('evoloading');
						MANAGER.find('.trig_evo_loading').removeClass('evoloading');
					}
				});
			}

		// find an attendee
			$(document).on('click','.evorsau_trig_find_attendee',function(){
				$(this).parent().siblings('.evorsau_find_rsvp').toggle();
			});
			$(document).on('change paste keyup','input.evorsau_find_attendee',function(){
				var O = $(this);
				var val = O.val();
				const section = O.closest('.evorsau_attendee_list');

				section.find('li').each(function(){
					var show = false;
					// if rsvp id typed and it matches
					if( val == $(this).data('rsvpid')) show = true;
					if( val == $(this).data('e')) show = true;
					if( $(this).data('e').includes( val)) show = true;
					if( val == '') show = true;

					(show) ? $(this).show(): $(this).hide();
				});
			});

		// register attendee on the spot
			$('body').on('click','.evorsau_trig_rsvp_form',function(){
				OBJ = $(this);
				// load Json data					
				JDATA = OBJ.siblings('.evors_jdata').data('j');

				var extra_data = {};
				extra_data['loginuser'] = 'no';
				show_rsvp_form(OBJ, '','submit', extra_data);
			});

		// checkin guests
			$('.evoau_manager_event').on('click','span.checkin',function(){
				var obj = $(this);
				var PAR = obj.closest('.evorsau_attendee_list');

				if(!PAR.hasClass('checkable')) return false;

				var status = obj.attr('data-status');

				status = (status=='' || status=='check-in')? 'checked':'check-in';

				var data_arg = {
					action: 'the_ajax_evors_f4',
					rsvp_id: obj.attr('data-id'),
					status:  status,
					nonce: PAR.find('input#evors_nonce').val()
				};
				$.ajax({
					beforeSend: function(){
						obj.html( obj.html()+'...');
					},
					type: 'POST',
					url:evors_ajax_script.ajaxurl,
					data: data_arg,
					dataType:'json',
					success:function(data){
						//alert(data);
						obj.attr({'data-status':status}).html(data.new_status_lang)
						.removeAttr('class')
						.addClass(status+' checkin');
					}
				});
			});
	// waitlist 
	// Remove from waitlist
		$('body').on('click','.evorsw_remove_wl',function(){

			FORM = $(this).closest('form');

			// update form values to remove from waitlist
			FORM.find('input[name="formtype"]').val('wl-remove');

			// submit form
			FORM.find('.evors_submit_rsvpform_btn').trigger('click');
			
		});

// Supporting functions		
		// show error messages
			function rsvp_error(code, type, message, O){
				FORM = O.closest('.evors_forms');
				F = FORM.find('form');

				if(message == '' || message === undefined){
					var C = FORM.find('.evors_msg_').data('j');
					var classN = (type== undefined || type=='error' || type == '')? 'err':type;
					message = C.codes[code]
				}				
				FORM.find('.notification').addClass(classN).show().find('p').html(message);
				FORM.parent().addClass('error');
				F.addClass('error');
			}

		// hide form messages
			function rsvp_hide_notifications(){
				$('.evors_lightbox_body').find('.notification').hide();
			}		
		// validate humans
			function rsvp_validate_human(field){
				if(field==undefined){
					return true;
				}else{
					var numbers = ['11', '3', '6', '3', '8'];
					if(numbers[field.attr('data-cal')] == field.val() ){
						return true;
					}else{ return false;}
				}				
			}
				
		function is_email(email){
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  		return regex.test(email);
		}
});