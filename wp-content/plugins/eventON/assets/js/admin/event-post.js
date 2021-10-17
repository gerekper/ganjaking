/** 
 * @version  2.8.9
 */
jQuery(document).ready(function($){

	var date_format = $('#evcal_dates').attr('date_format');
	var time_format = ($('body').find('input[name=_evo_time_format]').val()=='24h')? 'H:i':'h:i:A';
	var RTL = $('body').hasClass('rtl');

	// event status
		$('body').on('evo_row_select_selected', function(event, P,V){
			if(P.hasClass('es_values')){
				P.siblings('div').hide();
				P.siblings('.'+ V +'_extra').show();
			}
		});
		

	// virtual event
		var vir_val = $('.evo_eventedit_virtual_event').val();

		//console.log(vir_val);
		if( vir_val == 'zoom'){
			$('#evo_virtual_details').find('.zoom_connect').show();
		}

		$('.evo_eventedit_virtual_event').on('change',function(){
			var V = $(this).val();
			var section = $('#evo_virtual_details');
			
			var L = $(this).find('option:selected').data('l');
			var P = $(this).find('option:selected').data('p');
			var O = $(this).find('option:selected').data('o');

			// zoom connect
			if(V == 'zoom'){
				section.find('.zoom_connect').show();
			}else{
				section.find('.zoom_connect').hide();
			}

			// jitsi connect
			if(V == 'jitsi'){ section.find('.jitsi_connect').show(); }
			else{section.find('.jitsi_connect').hide();			}

			section.find('p.vir_link label').html( L);
			section.find('p.vir_link').val();
			section.find('p.vir_pass label').html( P);
			if(O !== undefined)
				section.find('p.vir_link em').html( O );
		});

	// jitsi
		$('body').on('click','.trig_jitsi', function(){
			var ajaxdataa_ = {};
			ajaxdataa_['action']='evo_jitsi_settings';
			ajaxdataa_['eid'] = $(this).data('eid');
			var LB = $('.evo_gen_lightbox');

			$.ajax({
				beforeSend: function(){ LB.find('.ajde_popup_text').addClass('evo_loader');},
				url:	the_ajax_script.ajaxurl,
				data: 	ajaxdataa_,	dataType:'json', type: 	'POST',
				success:function(data){
					LB.find('.ajde_popup_text').html( data.content );
				},
				complete:function(){ 
					LB.find('.ajde_popup_text').removeClass('evo_loader');
				}
			});
		});
		$('body').on('click','.evo_jitsi_save',function(){

			var FORM = $(this).closest('form'),
				dataajax = {},
				LB = $('.evo_gen_lightbox');

			if( $(this).hasClass('del')) FORM.find('input.form_type').val('delete');

			FORM.ajaxSubmit({
				beforeSubmit: function(){	LB.find('.ajde_popup_text').addClass('evo_loader');	},
				dataType: 	'json',
				url: 		the_ajax_script.ajaxurl,
				type: 	'POST',
				success:function(data){
					if( data.status == 'good'){
												
						$('body').trigger('ajde_lightbox_show_msg',[ data.msg, 'evo_gen_lightbox','good',true]);

						$('body').find('input[name="_vir_url"]').val( data.join_url );
						$('body').find('input[name="_vir_pass"]').val( data.pass );
						
					}else{
						$('body').trigger('ajde_lightbox_show_msg',[ data.msg, 'evo_gen_lightbox','bad']);
					}

					// update meeting id
					if( 'id' in data){
						FORM.find('input[name="_evoz_mtg_id"]').val( data.id);
					}
				},
				complete:function(){
					LB.find('.ajde_popup_text').removeClass('evo_loader');
				}
			});

		});

	// ZOOM
		$('body').on('click','.trig_zoom',function(){
			var ajaxdataa_ = {};
			ajaxdataa_['action']='evo_zoom_settings';
			ajaxdataa_['eid'] = $(this).data('eid');
			var LB = $('.evo_gen_lightbox');

			$.ajax({
				beforeSend: function(){ LB.find('.ajde_popup_text').addClass('evo_loader');},
				url:	the_ajax_script.ajaxurl,
				data: 	ajaxdataa_,	dataType:'json', type: 	'POST',
				success:function(data){
					LB.find('.ajde_popup_text').html( data.content );
				},
				complete:function(){ 
					LB.find('.ajde_popup_text').removeClass('evo_loader');
				}
			});
		});	

		// connect to the api
		$('body').on('click','.evoz_connect',function(){

			var FORM = $(this).closest('form'),
				dataajax = {},
				LB = $('.evo_gen_lightbox');

			if( $(this).hasClass('del')) FORM.find('input.form_type').val('delete');

			FORM.ajaxSubmit({
				beforeSubmit: function(){						
					LB.find('.ajde_popup_text').addClass('evo_loader');
				},
				dataType: 	'json',
				url: 		the_ajax_script.ajaxurl,
				type: 	'POST',
				success:function(data){
					if( data.status == 'good'){
						FORM.find('.evoz_mtg_id').show().find('a').html( data.id )
							.attr('href','https://zoom.us/meeting/'+ data.id);
						$('body').trigger('ajde_lightbox_show_msg',[ data.msg, 'evo_gen_lightbox','good',true]);

						$('body').find('input[name="_vir_url"]').val( data.join_url );
						$('body').find('input[name="_vir_pass"]').val( data.pass );
						
					}else{
						$('body').trigger('ajde_lightbox_show_msg',[ data.msg, 'evo_gen_lightbox','bad']);
					}

					// update meeting id
					if( 'id' in data){
						FORM.find('input[name="_evoz_mtg_id"]').val( data.id);
					}

					// delete meeting
					if(data.type == 'delete' && data.status == 'good'){
						FORM.find('.evoz_mtg_id').hide();
						FORM.find('input[name="_evoz_mtg_id"]').val('');
						$('body').find('input[name="_vir_url"]').val( '' );
						$('body').find('input[name="_vir_pass"]').val( '' );
					}

					// action html replace
					if( 'action_html' in data){
						FORM.find('.actions').html( data.action_html );
					}
				},
				complete:function(){
					LB.find('.ajde_popup_text').removeClass('evo_loader');
				}
			});

		});

	// meta box sections
	// click hide and show
		$('#evo_mb').on('click','.evomb_header',function(){			
			var box = $(this).siblings('.evomb_body');			
			if(box.hasClass('closed')){
				$(this).removeClass('closed');
				box.slideDown('fast').removeClass('closed');
			}else{
				$(this).addClass('closed');
				box.slideUp('fast').addClass('closed');
			}
			update_eventEdit_meta_boxes_values();
		});
		
		function update_eventEdit_meta_boxes_values(){
			var box_ids ='';
			
			$('#evo_mb').find('.evomb_body').each(function(){				
				if($(this).hasClass('closed'))
					box_ids+=$(this).attr('box_id')+',';
			});		
			$('#evo_collapse_meta_boxes').val(box_ids);
		}
	
	// location picker
		$('#evcal_location_field').on('change',function(){
			var option = $('option:selected', this);

			// if a legit value selected
			if($(this).val()!='' && $(this).val()!= '-'){
				$('#evcal_location_name').val( $(this).val());
				$('#evcal_location').val( option.data('address')  );
				$('#evcal_lat').val( option.data('lat')  );
				$('#evcal_lon').val( option.data('lon')  );
				$('#evo_location_tax').val( option.data('tid')  );
				$('#evcal_location_link').val( option.data('link')  );

				$('#evo_loc_img_id').val( option.data('loc_img_id')  );
				if(option.data('loc_img_src')){
					$('.evo_metafield_image .evo_loc_image_src img').attr('src', option.data('loc_img_src') ).fadeIn();
				}else{
					$('.evo_metafield_image .evo_loc_image_src img').fadeOut();
				}
			}else{
				// if select a saved location picked open empty fields
				$(this).closest('.evcal_location_data_section').find('.evoselectfield_saved_data').slideToggle();
			}

			// if select saved field selected
				if($(this).val()=='-'){
					$(this).closest('.evcal_location_data_section').find('input[type=text]').attr('value','').val('');
					$('.evo_metafield_image .evo_loc_image_src img').fadeOut();
					$('#evo_location_tax').val('');
				}
		});
		// location already entered info edit button
			$('body').on('click','.evoselectfield_data_view', function(){
				$(this).parent().parent().find('.evoselectfield_saved_data').slideToggle();
			});

	// organizer picker
		$('#evcal_organizer_field').on('change',function(){
			var option = $('option:selected', this);

			if($(this).val()!=''){
				$('#evcal_organizer_name').val( $(this).val());
				$('#evcal_org_contact').val( option.data('contact')  );
				$('#evo_org_img_id').val( option.data('img')  );	
				$('#evo_organizer_tax_id').val( option.data('tid')  );
				$('#evcal_org_address').val( option.data('address')  );
				$('#evcal_org_exlink').val( option.data('exlink')  );

				yesno = option.data('exlinkopen');
				yesno = (yesno!='')? yesno: 'no';
				$('#_evocal_org_exlink_target').next('input').val( yesno  );
				$('#_evocal_org_exlink_target').attr('class','ajde_yn_btn '+ (yesno.toUpperCase()));

				if(option.data('imgsrc')){
					$('.evo_metafield_image .evo_org_image_src img').attr('src', option.data('imgsrc') ).fadeIn();	
				}else{
					$('.evo_metafield_image .evo_org_image_src img').fadeOut();
				}
			}
			// if select saved field selected
				if($(this).val()=='-'){
					$(this).closest('.evcal_location_data_section').find('input[type=text]').attr('value','').val('');
					$('.evo_metafield_image .evo_org_image_src img').fadeOut();
					$('#evo_organizer_tax_id').val('');
				}
		});
	
	/** COLOR picker **/	
		$('#color_selector').ColorPicker({		
			color: get_default_set_color(),
			onChange:function(hsb, hex, rgb,el){
				set_hex_values(hex,rgb);
			},onSubmit: function(hsb, hex, rgb, el) {
				set_hex_values(hex,rgb);
				$(el).ColorPickerHide();
			}		
		});
		
			function set_hex_values(hex,rgb){
				var el = $('#evColor');
				el.find('.evcal_color_hex').html(hex);
				$('#evcal_event_color').attr({'value':hex});
				el.css({'background-color':'#'+hex});		
				set_rgb_min_value(rgb,'rgb');
			}
			
			function get_default_set_color(){
				var colorraw =$('#evColor').css("background-color");
						
				var def_color =rgb2hex( colorraw);	
					//alert(def_color);
				return def_color;
			}
		
	//event color
		$('.evcal_color_box').click(function(){		
			$(this).addClass('selected');
			var new_hex = $(this).attr('color');
			var new_hex_var = '#'+new_hex;
			
			set_rgb_min_value(new_hex_var,'hex');		
			$('#evcal_event_color').val( new_hex );
			
			$('#evColor').css({'background-color':new_hex_var});
			$('.evcal_color_hex').html(new_hex);
			
		});
	
	/** convert the HEX color code to RGB and get color decimal value**/
		function set_rgb_min_value(color,type){
			
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
			
			$('#evcal_event_color_n').attr({'value':val});
		}
		
		function rgb2hex(rgb){
			
			if(rgb=='1'){
				return;
			}else{
				if(rgb!=='' && rgb){
					rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
					
					return "#" +
					("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
					("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
					("0" + parseInt(rgb[3],10).toString(16)).slice(-2);
				}
			}
		}
		
	/** User interaction meta field 	 **/
		// new window
		$('#evo_new_window_io').click(function(){
			var curval = $(this).hasClass('selected');
			if(curval){
				$(this).removeClass('selected');
				$('#evcal_exlink_target').val('no');
			}else{
				$(this).addClass('selected');
				$('#evcal_exlink_target').val('yes');
			}
		});
		 
		$('.evcal_db_ui').click(function(){
			var val = $(this).attr('value');
			$('#evcal_exlink_option').val(val);
			
			$('.evcal_db_ui').removeClass('selected');
			$(this).addClass('selected');
			
			var link = $(this).attr('link');		
			var linkval = $(this).attr('linkval');
			var opval = $(this).attr('value');
			
			if(link=='yes'){			
				$('#evcal_exlink').show();
				if(linkval!=''){
					$('#evcal_exlink').val(linkval);
				}
			}
			
			// slide down event card
			if(opval=='1' || opval=='3'|| opval=='X'){
				$('#evo_new_window_io').removeClass('selected');
				$('#evcal_exlink_target').val('no');
				$('#evcal_exlink').hide().attr({value:''});
				$('#evo_new_window_io').hide();
			}else{
				$('#evo_new_window_io').show();
			}
		});
		
	// repeating events UI	
		// frequency
		$('body').on('click','span.evo_repeat_type_val',function(){

			O = $(this);
			
			json = O.closest('.evo_editevent_repeat_field').data('t');

			var field = O.attr('value');
			field = json[ field ];

			$('.evo_preset_repeat_settings').show();
			$('.repeat_weekly_only').hide();
			$('.repeat_monthly_only').hide();

			// monthly
			if(field =='months'){
				$('.evo_rep_month').show();

				// show or hide day of week
				var field_x = $('.values.evp_repeat_rb').find("span.select").attr('value');
				var condition = (field_x=='dow');
				$('.repeat_monthly_modes').toggle(condition);
												
				$('.repeat_information').hide();
				$('.repeat_monthly_only').show();
			
			}else if(field =='weeks'){
				$('.evo_rep_week').show();

				// show or hide day of week
				var field_x = $('.values.evp_repeat_rb_wk').find("span.select").attr('value');
				var condition = (field_x=='dow');
				$('.evo_rep_week_dow').toggle(condition);
				
				$('.repeat_information').hide();
				$('.repeat_weekly_only').show();
			
			}else if(field=='custom'){// custom repeating patterns
				$('.evo_preset_repeat_settings').hide();
				$('.repeat_information').show();
			}else{
				$('.evo_rep_month').hide();				
				$('.repeat_monthly_modes').hide();
				$('.repeat_information').hide();
			}
			$('#evcal_re').html(field);
		});

		// custom repeat interval function
			$('.evo_repeat_interval_new .ristD').datepicker({
				dateFormat: date_format,
				altField: $('input.ristD_h'),
				altFormat: 'yy/mm/dd',
				isRTL: RTL,
				onSelect: function( selectedDate , obj) {
					var date = $(this).datepicker('getDate');

					// update end time
					$( ".rietD" ).datepicker( "setDate", date );
					$( ".rietD" ).datepicker( "option", "minDate", date );
				}
			});
			$('.evo_repeat_interval_new .rietD').datepicker({
				dateFormat: date_format,
				altField: $('input.rietD_h'),
				altFormat: 'yy/mm/dd',
				isRTL: RTL,
				onSelect: function( selectDate, obj){
					//$( ".ristD" ).datepicker( "option", "maxDate", selectedDate );
				}
			});
		
			$('.evo_repeat_interval_new .ristT, .evo_repeat_interval_new .rietT').timepicker({
				'step': 5,
				'timeFormat':time_format
			});

		// adding a new custom repeat interval
		// @since 2.2.24
		// @updated 2.5.3
			$('#evo_add_repeat_interval').on('click',function(){
				var obj = $('.evo_repeat_interval_new');

				// if the add new RI form is not visible
				if(!obj.is(':visible')){
					obj.slideDown();
				}else{


					if( obj.find('.datepickernew_repeat_startdate').val() &&
						obj.find('.datepickernew_repeat_enddate').val() 
					){		
						if($('ul.evo_custom_repeat_list').find('li').length > 0){
							count = parseInt($('ul.evo_custom_repeat_list li:last-child').data('cnt'))+1;	
						}else{
							count = 1;
						}

						var start_date_red = obj.find('.datepickernew_repeat_startdate').val();
						var end_date_red = obj.find('.datepickernew_repeat_enddate').val();

						var start_date = obj.find('.evo_new_repeat_start_alt_date').val();
						var end_date = obj.find('.evo_new_repeat_end_alt_date').val();

						var start_time = obj.find('._new_repeat_start_hour').val()
							+':'+obj.find('._new_repeat_start_minute').val()
							+	( obj.find('._new_repeat_start_ampm').val() !== undefined ? ':'+ obj.find('._new_repeat_start_ampm').val(): '' );

						var end_time = obj.find('._new_repeat_end_hour').val()
							+':'+obj.find('._new_repeat_end_minute').val()
							+ ( obj.find('._new_repeat_end_ampm').val() !== undefined ? ':'+ obj.find('._new_repeat_end_ampm').val(): '' );

						var html = '<li data-cnt="'+count+'" class="new"><span>from</span>'
							+ start_date_red +' '+start_time
							+' <span class="e">End</span>'
							+ end_date_red +' '+ end_time +'<em alt="Delete">x</em>';
						html += '<input type="hidden" name="repeat_intervals['+count+'][0]" value="'+ start_date +' '+start_time+'"/>';
						html +='<input type="hidden" name="repeat_intervals['+count+'][1]" value="'+ end_date +' '+end_time+'"/>';
						html +='<input type="hidden" name="repeat_intervals['+count+'][type]" value="dates"></li>';


						$('ul.evo_custom_repeat_list').append(html);

						// release time locks on date picker
						obj.find('input.evo_dpicker').datepicker('option','minDate',null);
						obj.find('input.evo_dpicker').datepicker('option','maxDate',null);


					}else{
						$('.evo_repeat_interval_button').find('span').fadeIn().html(' All fields are required!').delay(2000).fadeOut();
					}
				}
			});

		// delete a repeat interval
			$('.evo_custom_repeat_list').on('click','li em',function(){
				LI = $(this).closest('li');
				LI.slideUp(function(){
					LI.remove();
				});
			});

		// show all repeat intervals
			$('.evo_repeat_interval_view_all').on('click',function(){
				if($(this).attr('data-show')=='no'){
					$('.evo_custom_repeat_list').find('li.over').slideDown();
					$(this).attr({'data-show':'yes'}).html('View Less');
				}else{
					$('.evo_custom_repeat_list').find('li.over').slideUp();
					$(this).attr({'data-show':'no'}).html('View All');
				}
			});

		// repeat by value from select field
		// show correct info based on this selection
		$('body').on('evo_row_select_selected',function(e, P, val, vals){
			if(P.hasClass('repeat_mode_selection')){
				cond = (val == 'dow');
				P.parent().siblings('.repeat_modes').toggle( cond);				
			}
		});
		
		
	// end time hide or not
		$('#evo_hide_endtime').click(function(){
			// yes
			if($(this).hasClass('NO')){
				$('.evo_date_time_elem.evo_end').animate({'opacity':'0.5'});
			}else{
				$('.evo_date_time_elem.evo_end').animate({'opacity':'1'});
			}
		});
	// All day or not
		$('#evcal_allday').click(function(){
			// yes
			if($(this).hasClass('NO')){
				$('.evo_datetimes .evo_time_edit').animate({'opacity':'0.5'});
			}else{
				$('.evo_datetimes .evo_time_edit').show().animate({'opacity':'1'});
			}
		});
		
	//date picker on	
		$('body').on('evo_elm_datepicker_onselect',function(event, OBJ, selectedDate, date){
			// regular event start end dates
			if( $(OBJ).attr('id') == 'evo_end_date_457973'){
				$('body').find( "#evo_start_date_457973" ).datepicker( "option", "maxDate", selectedDate );      	
			}

			// custom repeat new start end date
			if( $(OBJ).attr('id') == 'evo_new_repeat_end_date_478933'){
				$('body').find( "#evo_new_repeat_start_date_478933" ).datepicker( "option", "maxDate", selectedDate );      	
			}

			if( $(OBJ).attr('id') == 'evo_start_date_457973'){

				var dayOfWeek = date.getUTCDay();
				
				// save event year based off start event date
   				$('.evo_days_list').find('.opt[value='+dayOfWeek+']' ).addClass('select');
   				$('.evo_days_list').find('input').val(function(){
   					return  (this.value? this.value +',': '') + dayOfWeek;
   				});
			}
		});

	// update event post meta data on real time
		$('body').on('evo_update_event_metadata',function(event, eid, values, evomb_body){
			var ajaxdataa_ = {};
			ajaxdataa_['action']='eventon_eventpost_update_meta';
			ajaxdataa_['eid'] = eid;
			ajaxdataa_['values'] = values;

			$.ajax({
				beforeSend: function(){ 
					evomb_body.addClass( 'loading');
				},
				url:	the_ajax_script.ajaxurl,
				data: 	ajaxdataa_,	dataType:'json', type: 	'POST',
				success:function(data){},
				complete:function(){ 				
					evomb_body.removeClass( 'loading');
				}
			});
		});
	
	/* Event Images */
		var file_frame,
			BOX;
	  
	    $('body').on('click','.evo_add_more_images',function(event){

	    	var obj = $(this);
	    	BOX = obj.siblings('.evo_event_images');

	    	event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( file_frame ) {	file_frame.open();		return;	}
			
			// Create the media frame.
			file_frame = wp.media.frames.downloadable_file = wp.media({
				title: 'Choose an Image',
				button: {text: 'Use Image',},
				multiple: true
			});

			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {

				var selection = file_frame.state().get('selection');
		        selection.map( function( attachment ) {
		            attachment = attachment.toJSON();
		            loadselectimage(attachment, BOX);
		           
	            });

				//attachment = file_frame.state().get('selection').first().toJSON();
				//loadselectimage(attachment, BOX);
			});

			// Finally, open the modal.
			file_frame.open();
	    }); 

		function loadselectimage(attachment, BOX){
			imgURL = (attachment.sizes.thumbnail && attachment.sizes.thumbnail.url !== undefined)? attachment.sizes.thumbnail.url: attachment.url;

			caption = (attachment.caption!== undefined)? attachment.caption: 'na';

			imgEL = "<span data-imgid='"+attachment.id+"'><b class='remove_event_add_img'>X</b><img title='"+caption+"' data-imgid='"+attachment.id+"' src='"+imgURL+"'></span>";

						
			BOX.find('.evo_event_image_holder').append(imgEL);
			update_image_ids(BOX);

			$('body').trigger('evo_event_images_notice',[ 'Image Added!', 'good', BOX]);
				
		}

	    // remove image from gallery
		    $('body').on('click', '.remove_event_add_img', function(){
		    	BOX = $(this).closest('.evo_event_images');
		    	$(this).parent().remove();
		    	update_image_ids(BOX);
		    });

		// drggable and sorting image order
			$('.evo_event_image_holder').sortable({
				update: function(e, ul){
					BOX = $(this).closest('.evo_event_images');
					update_image_ids(BOX);
				}
			});

		// update the image ids 
		    function update_image_ids(obj){
		    	var imgIDs='',
		    		INPUT = obj.find('input');

		    	C= 1;
		    	obj.find('img').each(function(index){
		    		imgid = $(this).attr('data-imgid');
		    		if(imgid){
		    			imgIDs = (imgIDs? imgIDs:'') + imgid+',';
		    			C++;
		    		}
		    	});
		    	INPUT.val(imgIDs);
	    	
		    }

		$('body').on('evo_event_images_notice', function(event, MSG, TYPE, BOX){
			if( TYPE == 'bad') BOX.siblings('.evo_event_images_notice').addClass('bad');
			BOX.siblings('.evo_event_images_notice').html( MSG ).addClass('show').delay(2000)
				.queue(function(next){
					$(this).removeClass('show');
					if( TYPE == 'bad') $(this).removeClass('bad');
					next();
				});
		});
	
		var upariam = 3;
});