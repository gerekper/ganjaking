/**
 * Admin Script
 * @version  1.4
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

						HTML += '<span class="line '+a_class+'" data-bid="'+ BD.index +'">';
						HTML += "<em class='bid'>#"+ BD.index +"</em>";
						HTML += '<em class="slot evobo_slot" >'+BD.times+'</em><span class="other">';

						HTML += '<span><b>'+BD.c+'</b> <i>'+DS.t.left+'</i></span>';
						HTML += '<span><b>'+BD.p+'</b></span>';

						if( a_class != ''){
							HTML += '<span><b>'+ (Object.keys(BD.a).length ) +'</b> <i>'+ DS.t.attendees +'</i></span>';
							$.each(BD.a, function(tid, AD){

							});
						} 

						// VO 
						if( 'vo_opt' in BD ){
							HTML += "<span class='evobo_po evopadl10' title='Has Options'><i class='fa fa-plug'></i></span>";
						}
						if( 'vo_var' in BD ){
							HTML += "<span class='evobo_po evopadl10' title='Has Variations'><i class='fa fa-random'></i></span>";
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
		$('body').on('evo_ajax_success_evobo_manager',function(event, OO, data){
			if(data.status=='good'){	
				draw_editor( data.block_json );
			}
		});

	// generate blocks
		$('body').on('evo_ajax_success_evobo_generate_blocks',function(event, OO, data){
			if( data.json)	draw_editor(data.json);
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
			var ajaxdataa = {};	
			ds = _get_dataset();	
			OBJ = $(this);

			ajaxdataa['action']='evobo_delete_block';
			ajaxdataa['index'] = OBJ.data('bid');
			ajaxdataa['eid'] = ds.eid;
			ajaxdataa['wcid'] = ds.wcid;

			OBJ.evo_admin_get_ajax({
				'ajaxdata':ajaxdataa,
				'lightbox_key':'evobo_lightbox',
				'uid':'evobo_delete_block',
				'load_new_content':false,
			});	
		});
		$('body').on('evo_ajax_success_evobo_delete_block',function(event, OO, data){
			if( data.json){
				 draw_editor(data.json);
			}else{
				$('body').find('.evoboE_slots').html('');
			}
		});

	// delete all time slot
		$('body').on('click', '.evobo_slot_delete_all', function(){

			var yes = confirm( $(this).data('t') );
			if( yes == true){
				var ajaxdataa = {};	
				ds = _get_dataset();		

				ajaxdataa['action']='evobo_delete_all';
				ajaxdataa['eid'] = ds.eid;
				ajaxdataa['wcid'] = ds.wcid;
				
				$(this).evo_admin_get_ajax({
					'ajaxdata':ajaxdataa,
					'lightbox_key':'evobo_lightbox',
					'uid':'evobo_delete_all_blocks',
					'load_new_content':false,
				});				
			}
		})
		.on('evo_ajax_success_evobo_delete_all_blocks',function(event, OO, data){
			if( data.json){
				 draw_editor(data.json);
			}else{
				$('body').find('.evoboE_slots').html('');
			}
		});
		
	// get the booking block form
		function get_form( type, O, o_title){
			
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
				
			var title = 'Booking Block Editor';
			if( o_title !='' && o_title !== undefined ) title = o_title;

			O.evo_lightbox_open({
				'd':ajaxdataa,
				'ajax':'yes',
				'lbc':'evobo_editor',
				'lightbox_loader':false,
				'uid':'evobo_editor_open',
				't': title
			});			
		}

	// get the base dataset
		function _get_dataset(){
			E = $('body').find('.evobo_admin_data');
			return E.data('dataset');
		}

	// save booking block data
		$('body').on('evo_ajax_success_evobo_save_block',function(event, OO, data){
			if( data.json) draw_editor(data.json );						
			$('body').find('.evoboE_form_container').html('');
		});
			
	// change the order of the blocks
		function change_blocks_list(){
			
			var ajaxdataa = {};	
			index = 'index';
			ajaxdataa[index] = {};
			ds = _get_dataset();	

			LB = $('body').find(".evobo_lightbox");

			LB.find('span.line').each(function(imte){
				if( $(this).data('bid') === undefined) return true;
				ajaxdataa[index][imte] = $(this).data('bid');
			});		

			ajaxdataa['action']='evobo_arrange_block';
			ajaxdataa['eid'] = ds.eid;
			ajaxdataa['wcid'] = ds.wcid;

			OBJ.evo_admin_get_ajax({
				'ajaxdata':ajaxdataa,
				'lightbox_key':'evobo_lightbox',
				'uid':'evobo_rearrange_blocks',
				'load_new_content':false,
			});			
		}
		$('body').on('evo_ajax_success_evobo_rearrange_blocks',function(event, OO, data){
			if( data.json) draw_editor(data.json);
		});
	
// Evo VO integration
	$('body').on('evo_ajax_success_evovo_save_vo_form',function(event, OO, data){
		if( 'total_block_cap' in data && data.total_block_cap > 0 ){
			$('body').find('form.evobo_block_editor_form').find('input[name="capacity"]').val( data.total_block_cap );
		}
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