/**
 * Admin Script
 * @version  0.1
 */
jQuery(document).ready(function($){


// BOOKING EDITOR
	function draw_editor(){
		DATA_ = $('body').find('.evobo_admin_data');
		DATA = DATA_.data('json');
		HTML = '';

		//console.log( DATA );

		$.each(DATA, function(year, months){
			HTML += '<span class="year">'+year+'</span>';  

			$.each(months, function(month, days){
				
				month_name = days['name'];
				HTML += '<span class="month">'+month_name+'</span>';
				
				$.each(days, function(day, slots){
					IND = 1;
					if( day == 'name') return true;

					$.each(slots, function(booking_index, times){

						if( booking_index == 'day') return true;
						
						HTML += '<span class="slot" data-bid="'+ times.index +'"><b>'+ (IND == 1? day:'') +'</b><em class="time">'+times.times+'</em>';
						HTML += '<span class="slot_actions"><em class="delete evobo_delete_slot">x</em> <em class="edit evobo_edit_slot"><i class="fa fa-pencil"></i></em><em class="edit evobo_view_attendees"><i class="fa fa-user"></i></em></span>';
						HTML +='</span>';
						IND++;
					});
				});
			});
        });

		$('.evobo_lightbox').find('.evoboE_slots').html( HTML );
		$('.evobo_lightbox').find('.evoboE_slots').sortable({	
			items: '.slot',		
            update: function(event, ui){
            	change_blocks_list();
			}
		});
	}

// Bookings
	// date and time picker
		var date_format = $('#evcal_dates').attr('date_format');
		var time_format = ($('body').find('input[name=_evo_time_format]').val()=='24h')? 'H:i':'h:i:A';
	
	// Load Editor
		$('body').on('click','.evobo_block_item', function(){
			OBJ = $(this);
			var ajaxdataa = { };
				ajaxdataa['action']='evobo_load_editor';
				ajaxdataa['eid']=  OBJ.data('eid');
				ajaxdataa['wcid']=  OBJ.data('wcid');

			$.ajax({
				beforeSend: function(){
					$('.evobo_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evobo_admin_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){	
						$('.evobo_lightbox').find('.ajde_popup_text').html( data.content);
						draw_editor(  );
					}else{}
				},complete:function(){
					$('.evobo_lightbox.ajde_admin_lightbox ').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});
	
	// Add new Time Slot
		$('body').on('click','a.evobo_add_new_slot',function(){
			get_form( 'new',$(this));
		});

	// Edit time slot
		$('body').on('click', '.evobo_edit_slot', function(){
			get_form( 'edit',$(this));
		});
		$('body').on('click','a.evobo_cancel_form',function(){
			_close_form();
		});	
	// delete time slot
		$('body').on('click', '.evobo_delete_slot', function(){
			_delete_timeslot( $(this));
		});

	//view attendees
		$('body').on('click','.evobo_view_attendees',function(){
			ds = _get_dataset();
			O = $(this);
			O.closest('.evoboE_slots').find('.slot').removeClass('select');
			O.closest('span.slot').addClass('select');

			ajaxdataa = {};
			ajaxdataa['action']='evobo_get_attendees';
			ajaxdataa['eid'] = ds.eid;
			ajaxdataa['wcid'] = ds.wcid;
			ajaxdataa['index'] = O.closest('.slot').data('bid');
			$.ajax({
				beforeSend: function(){
					$('.evobo_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evobo_admin_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						_show_form( data.content);
					}
				},complete:function(){
					$('.evobo_lightbox.ajde_admin_lightbox ').find('.ajde_popup_text').removeClass( 'loading');
				}
			});			
		});
		$('body').on('click','.evoboE_hide_form',function(){
			_close_form();
		});

	// get the base dataset
		function _get_dataset(){
			E = $('body').find('.evobo_admin_data');
			return E.data('dataset');
		}

	// get the booking block form
		function get_form( type, O){
			ds = _get_dataset();
			O.closest('.evoboE_slots').find('.slot').removeClass('select');
			O.closest('span.slot').addClass('select');

			var ajaxdataa = { };
				ajaxdataa['action']='evobo_get_form';
				ajaxdataa['type']= type;
				ajaxdataa['eid']=  ds.eid;
				ajaxdataa['wcid']=  ds.wcid;

			if(type == 'edit')	
				ajaxdataa['index']=  O.closest('.slot').data('bid');

			$.ajax({
				beforeSend: function(){
					$('.evobo_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evobo_admin_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.evobo_lightbox').find('.evoboE_form_container').html( data.content);
						process_date_time_picker( ds.tf, ds.dfj );
					}
				},complete:function(){
					$('.evobo_lightbox.ajde_admin_lightbox ').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		}
	
	// process date and timer picker
		function process_date_time_picker(tf, df){
			$('.evobo_lightbox').find('input[name=sd]').datepicker({
				dateFormat: df,
				beforeShow: function(input, inst) {
			       $('#ui-datepicker-div').removeClass(function() {
			           return $('input').get(0).id; 
			       });
			       $('#ui-datepicker-div').addClass('booking_datepicker');
			   	},
				numberOfMonths: 2,
				onClose: function( selectedDate , obj) {

					// update end time
					ENDdateOBJ = $('.evobo_lightbox').find('input[name=ed]');
					ENDdateOBJ.datepicker( "option", "minDate", selectedDate );
					ENDdateOBJ.addClass('test');

			    }
				//minDate: data.other.min_date,
				//maxDate: data.other.max_date
			});
			$('.evobo_lightbox').find('input[name=ed]').datepicker({
				dateFormat: df,
				beforeShow: function(input, inst) {
			       $('#ui-datepicker-div').removeClass(function() {
			           return $('input').get(0).id; 
			       });
			       $('#ui-datepicker-div').addClass('booking_datepicker');
			   	},
				numberOfMonths: 2,
				//minDate: data.other.min_date,
				//maxDate: data.other.max_date
			});
			$('.evobo_lightbox').find('input.evobo_time_selection').timepicker({'step': 10,'timeFormat':tf});
		}

	// save new time based pricing blcok
		$('body').on('click','.evobo_form_submission',function(){
			BTN = $(this);
			OBJ = BTN.closest('.evobo_add_block_form');
			UL = $('body').find('ul.evobo_blocks_list');

			OBJ.find('.message').hide();

			if(
				OBJ.find('input[name=sd]').val() &&
				OBJ.find('input[name=st]').val() &&
				OBJ.find('input[name=ed]').val() &&
				OBJ.find('input[name=et]').val() &&
				( (BTN.data('type') == 'tbp' && OBJ.find('input[name=p]').val() ) || (BTN.data('type') != 'tbp'))
			){

				ds = _get_dataset();

				index = '';

				var ajaxdataa = {};
				OBJ.find('input').each(function(){
					if($(this).val() !=='') ajaxdataa[ $(this).attr('name')] = encodeURIComponent( $(this).val() );
				});

				// if saving edits
				if(BTN.data('type')=='edit'){
					index = BTN.data('index');
				}

				ajaxdataa['action']='evobo_save_booking_block';
				ajaxdataa['index'] = index;
				ajaxdataa['eid'] = ds.eid;
				ajaxdataa['wcid'] = ds.wcid;
				ajaxdataa['type'] = BTN.data('type');
				ajaxdataa['date_format'] = ds.df;
				ajaxdataa['time_format'] = ds.tf;
				
				$.ajax({
					beforeSend: function(){ 
						$('.evobo_lightbox').find('.ajde_popup_text').addClass( 'loading');
					},					
					url:	evobo_admin_ajax_script.ajaxurl,
					data: 	ajaxdataa,	dataType:'json', type: 	'POST',
					success:function(data){						
						if( data.json){
							_update_slots_json( data.json );
							draw_editor();
						}
						
						$('body').find('.evoboE_form_container').html('');
						$('body').trigger('ajde_lightbox_show_msg',[data.msg , 'evobo_lightbox','good', true, true]);

					},complete:function(){ 	
						$('.evobo_lightbox').find('.ajde_popup_text').removeClass( 'loading');
					}
				});				
			}else{
				$('body').trigger('ajde_lightbox_show_msg',['Missing required fields!', 'evobo_lightbox','bad']);
			}
		});
	
	// change the order of the blocks
		function change_blocks_list(){
			
			var ajaxdataa = {};	
			index = 'index';
			ajaxdataa[index] = {};
			ds = _get_dataset();		

			$('.evobo_lightbox').find('span.slot').each(function(imte){
				if( $(this).data('bid') === undefined) return true;
				ajaxdataa[index][imte] = $(this).data('bid');
			});		

			ajaxdataa['action']='evobo_arrange_block';
			ajaxdataa['eid'] = ds.eid;
			ajaxdataa['wcid'] = ds.wcid;
			
			$.ajax({
				beforeSend: function(){ 
					$('.evobo_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},					
				url:	evobo_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if( data.json){
						_update_slots_json( data.json );
						draw_editor();
					}
					$('body').trigger('ajde_lightbox_show_msg',[data.msg , 'evobo_lightbox','good', true, true]);
				},complete:function(){ 	
					$('.evobo_lightbox.ajde_admin_lightbox ').find('.ajde_popup_text').removeClass( 'loading');
				}
			});
			
		}

	// delete a time based price block
		function _delete_timeslot(OBJ){
			var ajaxdataa = {};	
			ds = _get_dataset();		

			ajaxdataa['action']='evobo_delete_block';
			ajaxdataa['index'] = OBJ.closest('.slot').data('bid');
			ajaxdataa['eid'] = ds.eid;
			ajaxdataa['wcid'] = ds.wcid;
			
			$.ajax({
				beforeSend: function(){ 
					$('.evobo_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},					
				url:	evobo_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if( data.json){
						_update_slots_json( data.json );
						draw_editor();
					}
					$('body').trigger('ajde_lightbox_show_msg',[data.msg , 'evobo_lightbox','good', true, true]);
				},complete:function(){ 	
					$('.evobo_lightbox.ajde_admin_lightbox ').find('.ajde_popup_text').removeClass( 'loading');
				}
			});
		}

// SUPPORTIVE
	function _close_form(){
		E = $('.evobo_lightbox').find('.evobo_editor');
		E.find('.evoboE_form_container').removeClass('visible').html('');
		E.find('span.slot').removeClass('select');
	}
	function _show_form(data){
		$('.evobo_lightbox').find('.evoboE_form_container').html( data ).addClass('visible');
	}
	function _update_slots_json( data){
		D = $('body').find('.evobo_admin_data');
		D.data('json', data);
	}

});