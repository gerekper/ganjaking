/**
 * Admin Script
 * @version  0.1
 */
jQuery(document).ready(function($){

	const text_1 = 'Total Blocks';

// BOOKING EDITOR
	function draw_editor(json){
		HTML = '';

		var DS = $('body').find('.evobo_admin_data').data('dataset');

		if(json && json != ''){

			HTML += "<span class='editor_header'><em class='evobo_editor_tal'>" + $(json).length + ' '+ text_1+ "</em> <em class='icon'><i class='evobo_editor_view_sel fa fa-bars select'></i><i class='evobo_editor_view_sel  fa fa-columns'></i></em></span>";
		}

		var slots_count = 0;
		$.each(json, function(year, months){
			$.each(months, function(month, days){
				
				month_name = days['name'];
				HTML += '<span class="month">'+month_name+ ', '+ year +'</span>';
				
				$.each(days, function(date, slots){
					IND = 1;
					if( date == 'name') return true;

					HTML += '<div class="date">';
					HTML += '<b class="day">' + date + " <i>"+ slots['day']+"</i></b>";
					HTML += "<span class='line_slots'>";

					$.each(slots, function(booking_index, BD){

						if( booking_index == 'day') return true;

						var a_class = ('a' in BD && BD.a !== undefined && BD.a != null ) ? 
							'has':'';

						HTML += '<span class="line '+a_class+'" data-bid="'+ BD.index +'"><em class="slot evobo_slot" >'+BD.times+'</em><span class="other">';

						HTML += '<span><b>'+BD.c+'</b> <i>'+DS.t.left+'</i></span>';
						HTML += '<span><b>'+BD.p+'</b></span>';

						if( a_class != ''){
							HTML += '<span><b>'+ (Object.keys(BD.a).length ) +'</b> <i>'+ DS.t.attendees +'</i></span>';
							$.each(BD.a, function(tid, AD){

							});
						} 

						HTML += '<i class="del evobo_delete_slot" data-bid="'+ BD.index +'">x</i>';
						HTML += '<i class="edit evobo_edit_slot fa fa-pencil" data-bid="'+ BD.index +'"></i>';

						HTML +='</span></span>';
						IND++;
						slots_count++;
					});
					HTML += "</span>";
					HTML += "</div>";
				});
			});
        });

		$('.evobo_lightbox').find('.evoboE_slots').html( HTML );
		$('.evobo_lightbox').find('.evobo_editor_tal').html(slots_count +" "+ text_1);
		$('.evobo_lightbox').find('.evoboE_slots').sortable({	
			items: '.line',		
            update: function(event, ui){ 	change_blocks_list();		}
		});
	}

	// selecting different view styles
		$('body').on('click','.evobo_editor_view_sel',function(){
			if($(this).hasClass('select') ) return;

			$(this).addClass('select');
			$(this).siblings().removeClass('select');

			const slots = $(this).closest('.evoboE_slots');

			if( $(this).hasClass('fa-bars')){
				slots.removeClass('compact');
				slots.find('.line .other').show();
			}else{
				slots.addClass('compact');
				slots.find('.line .other').hide();
			}
		});

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
						draw_editor( data.block_json );
					}else{}
				},complete:function(){
					$('.evobo_lightbox.ajde_admin_lightbox ').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});

	// generate time slots form
		$('body').on('click','.evobo_slot_generator',function(){
			ds = _get_dataset();

			var ajaxdataa = { };
				ajaxdataa['action']='evobo_load_generator';
				ajaxdataa['eid']=  ds.eid;
				ajaxdataa['wcid']=  ds.wcid;

			$.ajax({
				beforeSend: function(){ $('.evobo_lightbox_2').find('.ajde_popup_text').addClass( 'loading');	},	
				type: 'POST',
				url:evobo_admin_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.evobo_lightbox_2').find('.ajde_popup_text').html( data.content);
					}
				},complete:function(){
					$('.evobo_lightbox_2').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});
	// generate blocks
		$('body').on('click','.evobo_generate_slots', function(){
			var F = $(this).closest('.evobo_form');

			var ajaxdataa = {};
			F.find('input').each(function(){
				if($(this).val() !=='') ajaxdataa[ $(this).attr('name')] = encodeURIComponent( $(this).val() );
			});
			F.find('select').each(function(){
				if($(this).val() !=='') ajaxdataa[ $(this).attr('name')] = encodeURIComponent( $(this).val() );
			});

			ds = _get_dataset();
			ajaxdataa['action']='evobo_generate_slots';
			ajaxdataa['eid'] = ds.eid;
			ajaxdataa['wcid'] = ds.wcid;
			ajaxdataa['all_vo_data'] = F.find('.evovo_all_vo_data').data('all_vo_data');

			$('body').trigger('ajde_lightbox_hide_msg',['evobo_lightbox_2']);

			if( !('event_start_date' in ajaxdataa) ){
				$('body').trigger('ajde_lightbox_show_msg',['Missing required fields!', 'evobo_lightbox_2','bad']);
			}else{
				$.ajax({
					beforeSend: function(){ 
						$('.evobo_lightbox_2').find('.ajde_popup_text').addClass( 'loading');
					},					
					url:	evobo_admin_ajax_script.ajaxurl,
					data: 	ajaxdataa,	dataType:'json', type: 	'POST',
					success:function(data){	
						if( data.json){
							draw_editor(data.json);
						}
						
						$('body').find('.evoboE_form_container').html('');
						$('body').trigger('ajde_lightbox_show_msg',[data.msg , 'evobo_lightbox_2','good', false, true]);
					},complete:function(){ 	
						$('.evobo_lightbox_2').find('.ajde_popup_text').removeClass( 'loading');
					}
				});
			}
		});
	
	// Add new Time Slot
		$('body').on('click','a.evobo_add_new_slot',function(){
			get_form( 'new',$(this));
		});

	// Edit time slot
		$('body').on('click', '.evobo_slot', function(){
			get_form( 'edit',$(this));
		});
		$('body').on('click', '.evobo_edit_slot', function(){
			get_form( 'edit',$(this));
		});
	// delete time slot
		$('body').on('click', '.evobo_delete_slot', function(){
			_delete_timeslot( $(this));
		});
	// delete all time slot
		$('body').on('click', '.evobo_slot_delete_all', function(){

			var yes = confirm( $(this).data('t') );
			if( yes == true){
				delete_allslots( $(this));
			} 
		});

	//view attendees
		$('body').on('click','.evobo_view_attendees',function(){
			ds = _get_dataset();
			ajaxdataa = {};
			ajaxdataa['action']='evobo_view_all_attendees';
			ajaxdataa['eid'] = ds.eid;
			ajaxdataa['wcid'] = ds.wcid;
			$.ajax({
				beforeSend: function(){	$('.evobo_lightbox_2').find('.ajde_popup_text').addClass( 'loading');},
				type: 'POST',
				url:evobo_admin_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.evobo_lightbox_2').find('.ajde_popup_text').html( data.content);
					}
				},complete:function(){	$('.evobo_lightbox_2').find('.ajde_popup_text').removeClass( 'loading');	}
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
			// open lightbox
			$('body').trigger('evo_open_admin_lightbox',['evobo_lightbox_2']);

			ds = _get_dataset();
			

			var ajaxdataa = { };
				ajaxdataa['action']='evobo_get_form';
				ajaxdataa['type']= type;
				ajaxdataa['eid']=  ds.eid;
				ajaxdataa['wcid']=  ds.wcid;

			if(type == 'edit'){
				O.closest('.evoboE_slots').find('.slot').removeClass('select');
				O.closest('span.slot').addClass('select');
				ajaxdataa['index']=  O.closest('.line').data('bid');
			}	
				

			$.ajax({
				beforeSend: function(){
					$('.evobo_lightbox_2').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evobo_admin_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.evobo_lightbox_2').find('.ajde_popup_text').html( data.content);
					}
				},complete:function(){
					$('.evobo_lightbox_2').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		}

	// save booking block data
		$('body').on('click','.evobo_form_submission',function(){
			BTN = $(this);
			OBJ = BTN.closest('.evobo_add_block_form');
			UL = $('body').find('ul.evobo_blocks_list');

			OBJ.find('.message').hide();
			$('body').trigger('ajde_lightbox_hide_msg',['evobo_lightbox_2']);

			if(
				OBJ.find('input[name=event_start_date_x]').val() &&
				OBJ.find('input[name=event_end_date_x]').val() &&
				( (BTN.data('type') == 'tbp' && OBJ.find('input[name=p]').val() ) || (BTN.data('type') != 'tbp'))
			){

				ds = _get_dataset();

				index = '';

				var ajaxdataa = {};
				OBJ.find('input').each(function(){
					if($(this).val() !=='') ajaxdataa[ $(this).attr('name')] = encodeURIComponent( $(this).val() );
				});
				OBJ.find('select').each(function(){
					if($(this).val() !=='') ajaxdataa[ $(this).attr('name')] = encodeURIComponent( $(this).val() );
				});

				ajaxdataa['action']='evobo_save_booking_block';
				ajaxdataa['index'] = BTN.data('bid');
				ajaxdataa['eid'] = ds.eid;
				ajaxdataa['wcid'] = ds.wcid;
				ajaxdataa['type'] = BTN.data('type');
				ajaxdataa['all_vo_data'] = OBJ.find('.evovo_all_vo_data').data('all_vo_data');
				
				$.ajax({
					beforeSend: function(){ 
						$('.evobo_lightbox_2').find('.ajde_popup_text').addClass( 'loading');
					},					
					url:	evobo_admin_ajax_script.ajaxurl,
					data: 	ajaxdataa,	dataType:'json', type: 	'POST',
					success:function(data){						
						if( data.json) draw_editor(data.json );						
						$('body').find('.evoboE_form_container').html('');
						$('body').trigger('ajde_lightbox_show_msg',[data.msg , 'evobo_lightbox_2','good', true, true]);

						// calculate total booking blocks and update ticket stock
						$('#exotc_cap input').val( data.all_block_capacity);

					},complete:function(){ 	
						$('.evobo_lightbox_2').find('.ajde_popup_text').removeClass( 'loading');
					}
				});				
			}else{
				$('body').trigger('ajde_lightbox_show_msg',['Missing required fields!', 'evobo_lightbox_2','bad']);				
			}
		});
	
	// change the order of the blocks
		function change_blocks_list(){
			
			var ajaxdataa = {};	
			index = 'index';
			ajaxdataa[index] = {};
			ds = _get_dataset();		

			$('.evobo_lightbox').find('span.line').each(function(imte){
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
					if( data.json) draw_editor(data.json);
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
			ajaxdataa['index'] = OBJ.data('bid');
			ajaxdataa['eid'] = ds.eid;
			ajaxdataa['wcid'] = ds.wcid;
			
			$.ajax({
				beforeSend: function(){ $('.evobo_lightbox').find('.ajde_popup_text').addClass( 'loading');	},
				url:	evobo_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if( data.json){
						 draw_editor(data.json);
					}else{
						$('body').find('.evoboE_slots').html('');
					}
					$('body').trigger('ajde_lightbox_show_msg',[data.msg , 'evobo_lightbox','good', true, true]);
				},complete:function(){ 	
					$('.evobo_lightbox').find('.ajde_popup_text').removeClass( 'loading');
				}
			});
		}

	// delete all slots
		function delete_allslots( OBJ ){
			var ajaxdataa = {};	
			ds = _get_dataset();		

			ajaxdataa['action']='evobo_delete_all';
			ajaxdataa['eid'] = ds.eid;
			ajaxdataa['wcid'] = ds.wcid;
			
			$.ajax({
				beforeSend: function(){ $('.evobo_lightbox').find('.ajde_popup_text').addClass( 'loading');	},
				url:	evobo_admin_ajax_script.ajaxurl,
				data: 	ajaxdataa,	dataType:'json', type: 	'POST',
				success:function(data){
					if( data.json) draw_editor(data.json);
					$('body').find('.evoboE_slots').html('');
					$('body').trigger('ajde_lightbox_show_msg',[data.msg , 'evobo_lightbox','good', true, true]);
				},complete:function(){ 	
					$('.evobo_lightbox').find('.ajde_popup_text').removeClass( 'loading');
				}
			});
		}

// Evo VO integration
	$('body').on('evovo_admin_voform_submitted',function(event, data, BTN){
		var new_stock = 0;
		if( 'all_vo_data' in data){
			if( 'variation' in data.all_vo_data){
				$.each(data.all_vo_data.variation, function(ind, itm){
					new_stock += parseInt(itm.stock);
				});
			}
		}

		if( new_stock >0){
			$('body').find('.evobo_add_block_form').find('input[name="capacity"]').val( new_stock );
		}

		console.log(new_stock);
	});

// Seating integration
	// apply blocks to seats
		$('body').on('click','.evobo_apply_toseats',function(){
			var DS = $('body').find('.evobo_admin_data').data('dataset');

			OBJ = $(this);
			var ajaxdataa = { };
				ajaxdataa['action']='evobo_apply_seats';
				ajaxdataa['data']=  DS;

			$.ajax({
				beforeSend: function(){
					$('.evobo_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evobo_admin_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					$('body').trigger('ajde_lightbox_show_msg',[data.msg , 'evobo_lightbox',data.status, false, true]);
					
				},complete:function(){
					$('.evobo_lightbox.ajde_admin_lightbox ').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});

// SUPPORTIVE
	function _close_form(){
		E = $('.evobo_lightbox').find('.evobo_editor');
		E.find('.evoboE_form_container').removeClass('visible').html('');
		E.find('span.slot').removeClass('select');
	}
	function _show_form(data){
		$('.evobo_lightbox').find('.evoboE_form_container').html( data ).addClass('visible');
	}

});