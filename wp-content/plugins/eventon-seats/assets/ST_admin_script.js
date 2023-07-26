/**
 * Javascript: Seats Admin scripts
 * @version  1.2.1
 */
jQuery(document).ready(function($){	

	// INIT
	runningAJAX = false;
	const body = BODY = $('body');
	LBS = $('#evo_lightboxes');
	lb1 = body.find('.evost_lightbox');
	lb2 = $('.evost_lightbox_secondary');
	json_map_data = '';
	var map_temp = '';
	var local_attendees = ''; var temp_attendees = '';

	// LOAD seat map editor HTML view
		body.on('evo_ajax_success_evost_seatmap_editor',function(event, OO, data){
			json_map_data = data.j;
			map_temp = data.template;
		
			lb1 = $('body').find('.evo_lightbox.evost_lightbox');

			temp_attendees = data.temp_attendees;
			local_attendees = data.attendees;

			$('body').trigger('evost_draw_seat_map');
			$('body').trigger('evost_process_settings');	

		});

	// when changes made the seat map
		body.on('seatmap_changed', function(){
			$('body').find('.evosteditor_footer').addClass('needsave');
		});
		body.on('seatmap_saved', function(){
			$('body').find('.evosteditor_footer').removeClass('needsave');
		});

	// OPEN FORMS
		BODY.on('click','.evost_trigger_lb2',function(event){
			const d = $(this).data('d');
			BODY.trigger('evost_open_form',[ d.type, d.method, $(this), d.t ]);
		});
		$('body').on('evost_open_form', function(event, type, method, OBJ, o_title){
		
			h = $('.evosteditor_header');
			j = get_hj();

			if( method != 'new'){
				s_id = _hj_get_prop( 'section_id');
				// section positions
					if(j.item_type == 'section' ){						
						O = lb1.find('#evost_section_'+s_id);
						j['top'] = parseInt(O.css('top'));
						j['left'] = parseInt(O.css('left'));
						j['ang'] = parseInt(O.data('ang'));
					}

				// build hierachy
				if(type =='row'){				
					j['row_id'] = $(OBJ).data('id');
					j['section_id'] = s_id;
				}
				if(type =='seat'){
					j['seat_id'] = $(OBJ).data('id');
					j['row_id'] = $(OBJ).parent().data('id');
					j['section_id'] = s_id;
				}
			}

			if( type =='settings') j['item_type'] = 'settings';

			var ajaxdataa = {};
			ajaxdataa['action']='evost_editor_forms';
			ajaxdataa['method']= method;
			ajaxdataa['data'] = j;

			// pass seat attendees
			if( ajaxdataa.data.item_type == 'seat'){
				var slug = ajaxdataa.data.section_id+'-'+ajaxdataa.data.row_id+'-'+ajaxdataa.data.seat_id;
				ajaxdataa['data']['seat_slug'] = slug;
				ajaxdataa.data['attendee'] = {};

				if( 'tickets' in local_attendees){
					$.each( local_attendees.tickets, function(index, data){
						if( data.oDD.seat_slug == slug){

							console.log( data.oDD.seat_slug+' '+slug);
							ajaxdataa.data['attendee'] = data;
						}
					});
				}
			}

			var title = 'Seat Map Editing';
			if( o_title !='' && o_title !== undefined ) title = o_title;

			$('body').trigger('evo_lightbox_trigger',[{
				'uid':'evost_seatmap_editing',
				't': title,
				'lbc':'evost_lightbox_secondary',
				'lightbox_loader':false,
				'ajax':'yes',
				'd':ajaxdataa
			}]);

		});

		body.on('evo_ajax_success_evost_seatmap_editing',function(event, OO, data){
			if(data.status=='good'){

				LB2 = $('body').find('.evo_lightbox.evost_lightbox_secondary');
				//console.log(OO);

				ajaxdataa = OO.ajaxdata;

				// hide delete button
					if( ajaxdataa.method == 'new'){
						LB2.find('.evost_delete_item').hide();
					}else{
						LB2.find('.evost_delete_item').show();
					}

				// for section condition based fields
				if(ajaxdataa.data.item_type != 'settings'){
					f = LB2.find('.evost_editor_form');
					f.find('.evost_form_if_start').each(function(){
						v = f.find('select[name="'+ $(this).attr('name')+'"]').val();
						a = $(this).data('val');

						c = false;
						$.each(a, function(i, val){
							if( val == v) c = true;
						});

						$(this).toggle(c);
					});

					// for on change
					f.find('select').on('change',function(){
						v = $(this).val();
						n = $(this).attr('name');

						f.find('.evost_form_if_start[name="'+n+'"]').each(function(){
							a = $(this).data('val');

							c = false;
							$.each(a, function(i, val){
								if( val == v) c = true;
							});

							$(this).toggle(c);
						});
					});

					// icon selection
					f.find('.evost_icon').on('click','i.fa', function(){
						fa = $(this).data('val');
						icp = $(this).closest('p');
						icp.find('.selected_icons i').attr('class','fa '+fa);
						icp.find('.selected_icons').show();
						icp.find('.icon_area').hide();
						icp.find('input').val( fa );
					});
					f.on('click','.evost_form_remove_icon',function(){
						p = $(this).closest('p');
						p.find('input').val('');
						p.find('.selected_icons').hide();
						p.find('.selected_icons i').attr('class','fa');
						p.find('.icon_area').show();

					});

					// number change
					f.on('click','.evost_form_number_change',function(){
						p = $(this).closest('p');
						c = parseInt(p.find('input').val());
						add = $(this).hasClass('plus')? true: false;

						c = add? c+1: c-1;
						c = c<1? 1: c;
						p.find('input').val( c );
						p.find('i').html( c );
					});
				}


			}else{}
		});
	
	// SUBMIT FORM
		$('body').on('click','.evost_save_form',function(){
			if(runningAJAX) return false;
			f = $(this).closest('.evost_editor_form');

			var ajaxdataa = {};
			ajaxdataa['action']='evost_save_editor_forms';
		
			ajaxdataa['formdata'] = {};
			ajaxdataa['otherdata'] = {};

			// validate required fields
				validated = true;
					f.find('.req').each(function(){
						if($(this).is(':visible') && !$(this).val()){						
							$(this).addClass('error');
							validated = false;
						}else{
							$(this).removeClass('error');
						}
					});

				if(!validated){
					show_lb_msg('Required Fields Missing!', 'bad', '', false);
					return false;
				}

			// generate data
				f.find('input').each( function(index, data){
					if( $(this).attr('name') === undefined) return true;
					ajaxdataa.formdata[ $(this).attr('name') ] = $(this).val();
				});
				f.find('textarea').each( function(index, data){
					if( $(this).attr('name') === undefined) return true;
					ajaxdataa.formdata[ $(this).attr('name') ] = $(this).val();
				});

				f.find('select').each( function(index, data){
					ajaxdataa.formdata[ $(this).attr('name') ] = $(this).val();
				});

				ajaxdataa['data'] = j;

			$(this).evo_admin_get_ajax({
				'ajaxdata':ajaxdataa,
				'lightbox_key':'evost_lightbox_secondary',
				'uid':'evost_submit_form',
				'load_new_content':false,
				'hide_lightbox':2000
			});		
			
		});

		body.on('evo_ajax_success_evost_submit_form',function(event, OO, data){
			if(data.status=='good'){

				afd = OO.ajaxdata.formdata;

				// when settings saved
				if( afd.item_type == 'settings'){
					b = LBS.find('.evost_settings_btn');
					b.data('j', data.settings_data);							
					show_lb_msg(data.msg);
				}else{ 

					// redraw map 
					json_map_data = data.j;
					_classes = 'editing '+ (afd.item_type=='row'?'rowedit':'') + 
						(afd.item_type=='seat'?'seatedit':'');
					$('body').trigger('evost_draw_seat_map', [ afd.section_id, _classes]);
										
					if(afd.item_type=='section' && afd.method != 'new' && data.j[ afd.section_id ]['section_name']!== undefined){
						sn = data.j[ afd.section_id ]['section_name'];
						LBS.find('.primary_stage b').html( sn );
					}

					show_lb_msg(data.msg, '', '', false);
				}

				$('body').trigger('evost_process_settings'); // update map settings
			}else{}
		});

	// DELETE item
		LBS.on('click','.evost_delete_section',function(){
			s_id = _hj_get_prop( 'section_id');
			var ajaxdataa =  {}
			ajaxdataa['action']= 'evost_delete_item';
			ajaxdataa['formdata']= get_hj();
						
			$(this).evo_admin_get_ajax({
				'ajaxdata':ajaxdataa,
				'lightbox_key':'evost_lightbox',
				'uid':'evost_delete_item',
				'load_new_content':false,
				'hide_lightbox':false
			});		

		});
		LBS.on('click','.evost_delete_item',function(){
			if(runningAJAX) return false;
			var ajaxdataa = {};
			ajaxdataa['action']='evost_delete_item';
			ajaxdataa.formdata = {};
			f = $(this).closest('.evost_editor_form');

			f.find('input').each( function(index, data){
				o = $(this);
				if(o.val() !== undefined || o.val()!=''){
					ajaxdataa.formdata[ o.attr('name') ] = o.val();
				}
			});

			$(this).evo_admin_get_ajax({
				'ajaxdata':ajaxdataa,
				'lightbox_key':'evost_lightbox_secondary',
				'uid':'evost_delete_item',
				'load_new_content':false,
				'hide_lightbox':false
			});			
		});
		body.on('evo_ajax_success_evost_delete_item',function(event, OO, data){
			if(data.status=='good'){
				_hj_clear();
				json_map_data = data.j;
				$('body').trigger('evost_draw_seat_map');
				LBS.find('.evosteditor_content').trigger('click');	// go back to main editor
			}else{}
		});
	
	// CLICK on SECTION
		LBS.on('click','.evosteditor_content',function(event){
			event.stopPropagation();
			if($(this).hasClass('editing')){
				// HIDE
				if(!$(event.target).is('.evost_section.editing') && 
					$(event.target).closest('.evost_section.editing').length ==0 
				){
					// disable row and seat editing for sections
						e = $(event.target).closest('.evosteditor_content');
						e.removeClass('editing');
						e.find('.evost_section').removeClass('editing rowedit seatedit');

					$('body').trigger('evost_close_header_secondary');
					$('body').trigger('evost_calculate_stats');	

					$('body').trigger('evost_hide_triad_header',['section_id']);	
					disableInteractStuff();				
				}
			}else{
				// SHOW
				if($(event.target).closest('.evost_section').length >0) {
					s = $(event.target).closest('.evost_section');
					// location of section actions
					sectiontop = parseInt(s.css('top'));
					height = $('.evosteditor_content').height();

					additionalClass = (sectiontop+100 > height)?'top':'';

					// set values
					_hj_set_prop( 'section_id', s.data('id') ); 
					_hj_set_prop( 'item_type', 'section' ); 

					$('body').trigger('evost_load_header_secondary');
					s.addClass('editing');
					$('body').trigger('evost_calculate_stats');

					s_id = _hj_get_prop( 'section_id');
					$('body').trigger('evost_show_triad_header',['section_id', s_id]);	
					draggableStuff();
				}				
			}
		});
	
	// Header JSON data handling
		function _hj_set_prop(field, value){
			h = $('.evosteditor_header');
			hj = h.data('j');

			if( hj === undefined) hj = {};
			hj[field] = value;
			h.data('j', hj);
		}
		function _hj_get_prop(field){
			h = $('.evosteditor_header');
			hj = h.data('j');
			if( hj === undefined) return false;
			if( hj[field] === undefined ) return false;
			if( hj[field] == '') return false;
			return hj[field];
		}
		function _hj_del_prop(field){
			h = $('.evosteditor_header');
			hj = h.data('j');
			if( hj === undefined) return true;
			if( hj[field] === undefined ) return true;
			if( hj[field] == '') return true;
			hj[field] = '';
			h.data('j', hj); // set
		}
		function _hj_clear(){
			h = $('.evosteditor_header');
			hj = h.data('j');
			if( hj === undefined) return true;
			hj_ = {};
			hj_['event_id'] = hj.event_id;
			hj_['wcid'] = hj.wcid;
			h.data('j', hj_); // set
		}
		function get_hj(){
			return $('body').find('.evosteditor_header').data('j');
		}

	// LIGHTBOX actions
		$('body').on('evost_close_lb2', function(event, clearHJ){
			lb2.find('.ajde_close_pop_btn').trigger('click');
			if( clearHJ) _hj_clear();
		});

	// HEADER actions
		// Show/Hide triad header data
			$('body').on('evost_show_triad_header', function(event, field, value){
				TH = lb1.find('.evosteditor_sub_header');

				TH.find('.'+field+' b').html( value);
				TH.find('.'+field).removeClass('hidden');
			});
			$('body').on('evost_hide_triad_header', function(event, field){
				TH = lb1.find('.evosteditor_sub_header');
				TH.find('.'+field).addClass('hidden');
			});
		// show the secondary header
			$('body').on('evost_load_header_secondary', function(event){
				h = $('.evosteditor_header');				
				s_id = _hj_get_prop( 'section_id');
				s = lb1.find('#evost_section_'+s_id);

				j = json_map_data;
				//console.log(j[s_id]);
				s_type = __hasVal(j, s_id)? j[s_id]['type']:'def';

				h.find('.primary_stage b').html( s.data('name') );
				sd = $('.evosteditor_header').find('.secondary');
				sd.show();

				// hide add new button
				$('.evosteditor_header').find('.evost_new_section').hide();

				// hide row and seat selection for sections
				cond = ( s_type == 'una' || s_type == 'aoi' || s_type == 'boo')? false: true;

				sd.find('.evost_focus_item').toggle(cond);
				$('.evosteditor_content').addClass('editing');			
			});

		// hiding the secondary header
			$('body').on('evost_close_header_secondary', function(event){
				h = $('.evosteditor_header');
				$('.evosteditor_header').find('.secondary').hide();
				$('.evosteditor_content').removeClass('editing');
				$('.evosteditor_header').find('a.evost_focus_item.evost_edit_section').trigger('click');
				// show add new button
				$('.evosteditor_header').find('.evost_new_section').show();

				
				_hj_clear();
			});
			$('body').on('evost_header_highlight_btn', function(event, btn){
				if(!$(btn).hasClass('evost_focus_item')) return false;
				$(btn).siblings('a.evost_focus_item').removeClass('select');
				$(btn).addClass('select');
			});

		
		// section editing
			$('.evo_lightboxes').on('click','.evosteditor_header .evost_edit_section',function(){
				h = $('.evosteditor_header');
				s_id = _hj_get_prop( 'section_id');
				s = lb1.find('#evost_section_'+s_id);				

				$('body').trigger('evost_header_highlight_btn',[$(this)]);

				s.find('.seat').each(function(){	$(this).attr('title', '');	});
				s.find('.evost_row').each(function(){	$(this).attr('title', '');	});

				h.find('.evost_section_only').show();
				s.removeClass('seatedit');
				s.removeClass('rowedit');
				_hj_set_prop( 'item_type', 'section' ); 

				// adjust hover tooltip values
					s.attr('tip', s.data('name'));
					s.find('.evost_row').each(function(){						
						$(this).removeAttr('tip');
						$(this).find('.seat').each(function(){
							$(this).removeAttr('tip');
						});					
					});
			});

		// row editing
			$('.evo_lightboxes').on('click','.evosteditor_header .evost_edit_row',function(){
				h = $('.evosteditor_header');
				s_id = _hj_get_prop( 'section_id');
				s = lb1.find('#evost_section_'+s_id);		

				$('body').trigger('evost_header_highlight_btn',[$(this)]);

				s.find('.seat').each(function(){	$(this).attr('title', '');	});
				s.find('.evost_row').each(function(){	$(this).attr('title', $(this).data('row-name'));	});

				h.find('.evost_section_only').hide();
				s.removeClass('seatedit');
				s.addClass('rowedit');
				_hj_set_prop( 'item_type', 'row' ); 

				// adjust the hover over tooltip values for rows
					s.removeAttr('tip');
					s.find('.evost_row').each(function(){						
						$(this).attr('tip', $(this).data('index'));
						$(this).find('.seat').each(function(){
							$(this).removeAttr('tip');
						});					
					});
			});

			// click on a row to open row edit form
			LBS.on('click','.rowedit .evost_row',function(){
				$('body').trigger('evost_open_form',['row','edit', $(this)]);
			});
		// seat editing
			LBS.on('click','.evosteditor_header .evost_edit_seat',function(){
				s_id = _hj_get_prop( 'section_id');
				s = lb1.find('#evost_section_'+s_id);		

				$('body').trigger('evost_header_highlight_btn',[$(this)]);

				s.addClass('seatedit');
				s.removeClass('rowedit');
				_hj_set_prop( 'item_type', 'seat' ); 

				h = $('.evosteditor_header');
				h.find('.evost_section_only').hide();
				s.find('.seat').each(function(){	$(this).attr('title', $(this).data('snumber'));	});
				s.find('.evost_row').each(function(){	$(this).attr('title', '');	});

				// adjust the hover over tooltip values for rows
					s.removeAttr('tip');
					s.find('.evost_row').each(function(){
						$(this).removeAttr('tip');
						$(this).find('.seat').each(function(){
							$(this).attr('tip', $(this).data('number'));
						});					
					});

			});
			LBS.on('click','.seatedit .seat',function(){
				$('body').trigger('evost_open_form',['seat','edit', $(this)]);
			});

		// ADD NEW section
			body.on('click','.evost_new_section',function(){
				_hj_set_prop('item_type','section');
				$('body').trigger('evost_open_form',['section','new', $(this)]);
			});
			body.on('click','.evost_edit_selected_section',function(){
				_hj_set_prop('item_type','section');
				$('body').trigger('evost_open_form',['section','edit', $(this), $(this).data('t')]);
			});

		// ROTATE
			LBS.on('click','.evost_rotate_l',function(){
				this_LB = $(this).closest('.evo_lightbox');
				s_id = _hj_get_prop( 'section_id');
				s = this_LB.find('#evost_section_'+s_id);		
				
				if(s === undefined) return false;

				current_angle = parseInt(s.data('ang'));
				current_angle = (current_angle % 15 == 0)? current_angle: 0; // check current angle for multiple of 45 degrees
				new_angle = (current_angle<360)? current_angle+15: 0;
				_rotate_section(s,current_angle, new_angle);

			});
			LBS.on('click','.evost_rotate_r',function(){
				this_LB = $(this).closest('.evo_lightbox');
				s_id = _hj_get_prop( 'section_id');
				s = this_LB.find('#evost_section_'+s_id);
				if(s === undefined) return false;

				current_angle = parseInt(s.data('ang'));
				current_angle = (current_angle % 15 == 0)? current_angle: 0; // check current angle for multiple of 45 degrees
				new_angle = (current_angle <= 0)? 360-15: current_angle-15;
				_rotate_section(s,current_angle, new_angle);

			});

			function _rotate_section(s, old_angle, new_angle){
				s.data('ang', new_angle);
				s.removeClass('turn'+current_angle);
				s.addClass('turn'+new_angle);
			}

		// Duplicate
			LBS.on('click','.evost_dup',function(){
				this_LB = $(this).closest('.evo_lightbox');
				s_id = _hj_get_prop( 'section_id');
				hj = this_LB.find('.evosteditor_header').data('j');

				var ajaxdataa = {};
				ajaxdataa['action']='evost_duplicate_section';
				ajaxdataa['data'] = hj;
				
				$.ajax({
					beforeSend: function(){ 
						this_LB.find('.evolb_content').addClass( 'loading');
					},	
					url:	evost_admin_ajax_script.ajaxurl,
					data: 	ajaxdataa,	dataType:'json', type: 	'POST',
					success:function(data){
						if(data.status=='good'){
							json_map_data = data.j;
							$('body').trigger('evost_draw_seat_map', [s_id, 'editing']);
							show_lb_msg(data.msg,'good','ed');
						}else{}
					},complete:function(){ 
						this_LB.find('.evolb_content').removeClass( 'loading');
					}
				});
			});

		// view attendees
			LBS.on('click','.evost_attendees',function(){
				$('body').trigger('evo_open_admin_lightbox',['evotx_lightbox']);

				s_id = _hj_get_prop( 'section_id');

				AT_data = {};

				// filter attendees list for select seat data
				newtickets = {};
				newtickets['tickets'] = {};
				
				if(local_attendees){
					$.each(local_attendees.tickets, function(tn, td){
						$.each(td, function(field, value){

							if(field == 'oDD'){
								if(value.seat_slug === undefined) return true;
								if(value.seat_slug =='') return true;
								if( !value.seat_slug.match( s_id)) return true;

								newtickets.tickets[tn] = td;
							}	
						});
					});
				}

				AT_data['attendees'] = newtickets;
				AT_data['temp'] = temp_attendees;

				$('body').evotxDrawAttendees(AT_data);

				local_attendees = local_attendees;
			});

	// Save editor changes
		$('body').on('click','.evost_save_seating_changes',function(){
			j = json_map_data;

			e = LBS.find('.evost_sections_container');
			hj = LBS.find('.evosteditor_header').data('j');

			s = {};
			// section positions and location data
			e.find('span.evost_section').each(function(){
				O = $(this);
				s_id = O.data('id');
				s[ s_id] = {};
				s[ s_id]['ang'] = O.data('ang');
				s[ s_id]['top'] = parseInt(O.css('top'));
				s[ s_id]['left'] = parseInt(O.css('left'));
				s[ s_id]['w'] = parseInt(O.css('width'));
				s[ s_id]['h'] = parseInt(O.css('height'));
				s[s_id]['type'] = j[s_id]['type'];
			});

			var ajaxdataa = {};
			ajaxdataa['action']='evost_editor_save_changes';
			ajaxdataa['data'] = hj;
			ajaxdataa['s'] = s;
			if( $('body').find('.evobost_block_id').length>0) 
				ajaxdataa['block_id'] = $('body').find('.evobost_block_id').val();

			$(this).evo_admin_get_ajax({
				'ajaxdata':ajaxdataa,
				'lightbox_key':'evost_lightbox',
				'uid':'evost_editor_save_changes',
				'load_new_content':false,
			});	
			
		});	
		body.on('evo_ajax_success_evost_editor_save_changes',function(event, OO, data){
			if(data.status=='good'){
				$('body').trigger('seatmap_saved');
			}else{}
		});
	
	// MAP DRAWING
		$('body').on('evost_draw_seat_map',function(event, section_id, classes){
			c = LBS.find('.evost_sections_container');
			c.evostMapDrawer({
				json: json_map_data,
				section_id: section_id,
				classes: classes,
				temp: map_temp,
				end: 'admin'
			});	

		});
		$('body').on('evost_after_map_drawn',function(){
			$('body').trigger('evost_calculate_stats');
		});

	// trigger draggable
		$('body').on('evost_draggables',function(event){
			draggableStuff();	
		});

	// Calculate map stats
		$('body').on('evost_calculate_stats',function(event){
			sh = lb1.find('.evosteditor_sub_header');

			seat = 0;
			j = json_map_data;
			ed = lb1.find('.evost_section.editing').length;

			// current section id
			ps = ed>0 ? lb1.find('.evost_section.editing').data('id'): false;

			sold_count = 0;
			inprogress_count = 0;


			// sold seats count
			if(local_attendees ){
				$.each(local_attendees.tickets, function(tn, td){

					if(!td.hasOwnProperty('oDD')) return true;
					if(!td.hasOwnProperty('oS')) return true;

					if( td.oS != 'completed'){
						return true;
					}else{
						inprogress_count++;
						if( ps && !oDD.seat_slug.match( ps)) return true;						
					} 
					
					oDD = td.oDD;

					if(!oDD.hasOwnProperty('seat_slug')) return true;
					if(!oDD.seat_slug) return true;

					if( ps && !oDD.seat_slug.match( ps)) return true;

					sold_count++;
				});
			}

			// seat count
				$.each(j, function(sid, sd){

					// if a section is selected show seats for just that section
					if( ps && sid != ps) return true;

					if( sd.type=='una'){
						seat += parseInt(sd.capacity);
					}else{
						//console.log( sd.rows);
						$.each(sd.rows, function(rid, rd){
							$.each( rd.seats, function(sid, sd){
								seat +=1;
							});
						});
					}
				});

			// add to html
			sh.find('.seat_count b').html( seat );
			sh.find('.seat_sold b').html( sold_count );
			sh.find('.seat_inprogress b').html( inprogress_count );

		});

	// SETTINGS
		
		// process seat map settings
			$('body').on('evost_process_settings', function(event){
				$('.evost_sections_container').evostMapSettings({
					json:lb1.find('.evost_settings_btn').data('j'),
					end:'admin' 
				});				
			});

		// select background image
			var file_frame,
				BOX;
		  
		    $('body').on('click','.evost_select_image ',function(event) {
		    	var obj = jQuery(this);

		    	const LB = obj.closest('.evo_lightbox');
		    	const LB1 = $('body').find('.evo_lightbox.evost_lightbox');

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
						
						LB.find('.evost_seat_img').val( attachment.id );
						LB.find('.evost_img_holder').html( '<img src="'+attachment.url+'" style="width:200px"/>' );
						LB1.find('.evost_sections_container').css('background-image', 'url('+attachment.url+')' );
						var old_text = obj.attr('value');
						var new_text = obj.data('txt');

						obj.attr({'value': new_text, 'data-txt': old_text, 'class': 'evost_select_image evo_admin_btn removeimg'});
					});

					// Finally, open the modal.
					file_frame.open();

				}else{
					
					LB.find('.evost_seat_img').val( '' );
					LB.find('.evost_img_holder').html('');
			  		LB1.find('.evost_sections_container').css('background-image', '' );

			  		var old_text = obj.attr('value');
					var new_text = obj.attr('data-txt');

					obj.attr({'value': new_text, 'data-txt': old_text, 'class': 'evost_select_image evo_admin_btn chooseimg'});

					return false;
				}
		    }); 
	
		// color picker
			$('body').on('click','.evost_color_picker',function(){
				colorPickMulti($(this));
			});
			function colorPickMulti(cp){
				
				$(cp).ColorPicker({
					onBeforeShow: function(){
						//$(this).ColorPickerSetColor( $(this).attr('hex'));
					},	
					onChange:function(hsb, hex, rgb, el){
						//console.log(hex+' '+rgb);
						CIRCLE = $('body').find('.colorpicker_on');
						CIRCLE.css({'backgroundColor': '#' + hex}).attr({'title': '#' + hex, 'hex':hex});

						obj_input = CIRCLE.siblings('input.backender_colorpicker');	
						obj_input.attr({'value':hex});
					},	
					onSubmit: function(hsb, hex, rgb, el) {
						var obj_input = $(el).siblings('input');

						if($(el).hasClass('rgb')){
							//$(el).siblings('input.rgb').attr({'value':rgb.r+','+rgb.g+','+rgb.b});
							//console.log(rgb);
						}

						obj_input.attr({'value':hex});

						$(el).css('backgroundColor', '#' + hex);
						$(el).attr({'title': '#' + hex, 'hex':hex});
						$(el).ColorPickerHide();

						$('body').find('.colorpicker_on').removeClass('colorpicker_on');
					},
					onHide: function(colpkr){
						$('body').find('.colorpicker_on').removeClass('colorpicker_on');
					},
			    }).bind('click',function(){
					$(this).addClass('colorpicker_on');
				});

				if( !EE.hasClass('clrO')){
					EE.trigger('click').addClass('clrO');
				}
			}

		// download seat map data
			$('body').on('click','.evost_download_data',function(){

				$("<a />", {
				    "download": "seatmap_data.json",
				    "href" : "data:application/json," + encodeURIComponent(JSON.stringify( json_map_data ))
				}).appendTo("body")
				  .click(function() {
				     $(this).remove()
				})[0].click();
			});

		// upload seat map JSON data
			$('body').on('evo_data_uploader_submitted', function(event, reader_event, msg_elm, upload_box){

				if( $(upload_box).data('id') != 'evo_seatdata_upload') return;
				
				               
                var new_json_map_data = $.parseJSON(reader_event.target.result);

                // load new map data into local map data object
                json_map_data = new_json_map_data;
                _hj_set_prop( 'item_type', 'section' ); 

                // redraw the seat map using new seat data				                
                $('body').trigger('evost_draw_seat_map');				               

	        	// save new map into meta field 
	        	var ajaxdataa = {};
				ajaxdataa['action']='evost_save_uploaded_map';
				ajaxdataa['data'] = json_map_data;
				ajaxdataa['data_back'] = get_hj();

				$(this).evo_admin_get_ajax({
					'ajaxdata':ajaxdataa,
					'lightbox_key':'evost_upload_data',
					'uid':'evost_upload_data',
					'load_new_content':false,
				});	

			});
			BODY.on('evo_ajax_success_evost_upload_data',function(event, OO, data){
				$('body').trigger('seatmap_saved');
			});

		// clear all seat map data
			$('body').on('click','.evo_data_clear_trigger',function(){
				const LB = $('body').find('.evost_lightbox_secondary');

				var ajaxdataa = {};
				ajaxdataa['action']='evost_clear_map';
				ajaxdataa['data'] = get_hj();

				$(this).evo_admin_get_ajax({
					'ajaxdata':ajaxdataa,
					'lightbox_key':'evost_lightbox_secondary',
					'uid':'evost_clear_all_data',
					'load_new_content':false,
				});	
				
			});
			BODY.on('evo_ajax_success_evost_clear_all_data',function(event, OO, data){
				if(data.status=='good'){
					json_map_data = '';
					$('body').trigger('evost_draw_seat_map');
					$('body').trigger('seatmap_saved');	
				}else{}
			});

		// make all seats available for reserve
			$('body').on('click','.evo_data_av_trigger',function(){
				
				var ajaxdataa = {};
				ajaxdataa['action']='evost_make_all_av';
				ajaxdataa['data'] = get_hj();

				$(this).evo_admin_get_ajax({
					'ajaxdata':ajaxdataa,
					'lightbox_key':'evost_lightbox_secondary',
					'uid':'evost_all_available',
					'load_new_content':false,
				});					
			});
			BODY.on('evo_ajax_success_evost_all_available',function(event, OO, data){

				if(data.status=='good'){
					json_map_data = data.new_map_data;
					$('body').trigger('evost_draw_seat_map');
				}
				$('body').trigger('seatmap_saved');
			});
			
	// ADDONS
		// bookings for seats
			$('body').on('evo_ajax_success_evobs_load_block_seats',function(event, OO, data){
			if(data.status=='good'){
				json_map_data = data.j;
				map_temp = data.template;
						
				temp_attendees = data.temp_attendees;
				local_attendees = data.attendees;

				$('body').trigger('evost_draw_seat_map');
			}
		});
	



	// SUPPORTIVE
		// drag and resize
		draggableStuff();	
		function draggableStuff(){
			$('.evost_section.editing').draggable({
				disabled: false,
				containment: $('.evosteditor_content'),
				stop: function(event, ui){
					$('body').trigger('seatmap_changed');
				}
			});
			$('.evost_section.editing.type_una').resizable({
				disabled: false,
				containment: $('.evosteditor_content'),
				stop: function(event, ui){
					$('body').trigger('seatmap_changed');
				}
			});
			$('.evost_section.editing.type_boo').resizable({
				disabled: false,
				containment: $('.evosteditor_content'),
				stop: function(event, ui){
					$('body').trigger('seatmap_changed');
				}
			});
			$('.evost_section.editing.type_aoi').resizable({
				disabled: false,
				containment: $('.evosteditor_content'),
				stop: function(event, ui){
					$('body').trigger('seatmap_changed');
				}
			});
		}
		function disableInteractStuff(){
			$('.evost_section').draggable({disabled:true});
			$('.evost_section.type_una').resizable({disabled: false});
			$('.evost_section.type_una').resizable('disable');
			$('.evost_section.type_aoi').resizable({disabled: false});
			$('.evost_section.type_aoi').resizable('disable');
		}
		// tool tips
		$('.evo_lightboxes').tooltip({
			selector:'[tip]',
			items:'[tip]',
			track:false,
			tooltipClass: "evost_tooltip",
			content: function(){
				return $(this).attr('tip');
			},
			position: {
		        my: "center bottom-20",
		        at: "center top",
		        using: function( position, feedback ) {
		          $( this ).css( position );
		          $(this).addClass(feedback.vertical);
		          $(this).addClass(feedback.horizontal);
		        }
		      }
		}).tooltip('open');
		
		function checkCondition(v1, operator, v2) {
	        switch(operator) {
	            case '==':
	                return (v1 == v2);
	            case '===':
	                return (v1 === v2);
	            case '!==':
	                return (v1 !== v2);
	            case '<':
	                return (v1 < v2);
	            case '<=':
	                return (v1 <= v2);
	            case '>':
	                return (v1 > v2);
	            case '>=':
	                return (v1 >= v2);
	            case '&&':
	                return (v1 && v2);
	            case '||':
	                return (v1 || v2);
	            default:
	                return false;
	        }
	    }

	// show message
		function show_lb_msg(text, type, lb_, clearHJ){
			lb = (lb_ === 'ed')? 'evost_lightbox': 'evost_lightbox_secondary';
			lb = $('.'+lb);
			m = lb.find('p.message');
			m.html(text);
			c =( type =='bad')? true: false;
			m.toggleClass('bad', c);
			m.show(0).delay(3000).hide(0, function(){
				if( !$(this).is(':visible')) return false;
				if( lb_!= 'ed'){
					clearHJ = clearHJ? true: false;
					$('body').trigger('evost_close_lb2',[clearHJ]);
				} 
			});
		}

	function __hasVal(obj, key){
	        return obj.hasOwnProperty(key);
	    }

});