/**
 * Eventon Sync admin script
 * @version  1.1
 */
jQuery(document).ready(function($){

// General table stuff
	// select and deselect rows
		$('#fetched_events').on('click','span.status_check',function(){
			OBJ = $(this);
			if(OBJ.hasClass('selected')){
				OBJ.removeClass('selected');
				OBJ.addClass('unselected');
				OBJ.siblings('input').val('ns');
			}else{
				OBJ.removeClass('unselected');
				OBJ.addClass('selected');
				OBJ.siblings('input').val('ss');
			}
		});
	$('.detailed_report').on('click',function(){
		$(this).parent().parent().find('.detailed_status').toggle();
	});
	// change status on items all at once
		$('.evosy_action_buttons').on('click','a.deselect',function(){
			$('#fetched_events').find('td.column-status').each(function(){
				CURRENT_St = $(this).find('input').val();
				if(CURRENT_St != 'as'){
					$(this).find('input[name="status"]').val('ns');
					$(this).find('span').attr({'class':'status_check unselected'});
				}
			});
		});
		$('.evosy_action_buttons').on('click','a.select',function(){
			$('#fetched_events').find('tr').each(function(){
				CURRENT_St = $(this).find('input').val();
				if(CURRENT_St != 'as'){
					$(this).find('input[name="status"]').val('ss');
					$(this).find('span').attr({'class':'status_check selected'});
				}
			});
		});

// FETCH EVENTS
	$('.evosync_fetch_init').on('click',function(){
		BOX = $(this).closest('.inside');
		CONTAINER = BOX.find('.evosy_fetched_events');

		// get sources
			var ajaxdataa = {};
			ajaxdataa['action']= 'sync_get_streams';	
			ajaxdataa['source']= $(this).data('source');	

			$.ajax({
				beforeSend: function(){
					BOX.addClass('gathering');	
					$('body').trigger('evo_show_settings_loader');
				},
				type: 'POST',
				url:evosy_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					BOX.removeClass('gathering');	
					if(data.status == 'good' && data.content){						
						fetch_from_source(data.content, 0, BOX, ajaxdataa.source);
					}else{ 
						// no streams present
					}
				}
			});
	});

	var completed = 0;
	var fetched = 0;
	var failed = 0;
	function fetch_from_source(items, index, BOX, source){
		var ajaxdataa = {};
		ajaxdataa['action']= 'sync_fetch_from_source';	
		ajaxdataa['source']= source;
		ajaxdataa['type']= items[index]['type'];
		ajaxdataa['id']= items[index]['id'];

		CONTAINER = BOX.find('.evosy_fetched_events');

		STATUS = CONTAINER.find('.status');

		if(items[index]!== undefined || items[index] != ''){
		
			$.ajax({
				beforeSend: function(){	
					if(index==0){ 
						BOX.addClass('fetching');	
						CONTAINER.find('.status').show();						
					}
					CONTAINER.find('.status_inside').append('<em>Processing '+items[index]['type']+': <i>'+items[index]['id']+'</i></em>');
				},
				type: 'POST',
				url:evosy_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					//console.log(data);
					if( !data){
						failed++;

						message = 'No data returned from source!';
						
						CONTAINER.find('.status_inside').append("<em class='fail'>--> Failed: "+ items[index]['id'] + ' - <i>Reason: '+ message +"</i></em>");
						STATUS.find('span.fai b').html(failed);		
					}else{
						if( data.status=='good'){
						
							// append HTML
							$('#fetched_events').show();
							
							$('#fetched_events').find('tbody').append(data.html);
							VAL = parseInt(STATUS.find('span.com b').html());
							STATUS.find('span.com b').html(VAL+1);

							completed++;

							fetched += parseInt(data.events); // fetched events count
							STATUS.find('span.fet b').html(fetched);
							//console.log(fetched);
							
						}else{
						// failed
							failed++;

							message = data.message;
							
							CONTAINER.find('.status_inside').append("<em class='fail'>--> Failed: "+ items[index]['id'] + ' - <i>Reason: '+ message +"</i></em>");
							STATUS.find('span.fai b').html(failed);												
						}
					}
					

					// continuation
						if(index < items.length-1){
							fetch_from_source(items, (index+1),  BOX,source);
						}

					// when last item is processed
					if(index == items.length-1){
						if(fetched>0) CONTAINER.find('.evosy_action_buttons').show();
						CONTAINER.find('.evosy_upon_fetched_show').show();
						$('body').trigger('evo_hide_settings_loader');
						BOX.attr('class','inside fetchingcompleted');
					}
				},complete:function(){}
			});	
		}
	}

// display loading 
	$('body').on('evo_show_settings_loader',function(){
		$('.evo_settings_page_loader').addClass('show');
	});
	$('body').on('evo_hide_settings_loader',function(){
		$('.evo_settings_page_loader').removeClass('show');
	});
// import/update selected events		
	$('.evosy_action_buttons').on('click','a.process',function(){

		BOX = $(this).closest('.inside');
		CONTAINER = BOX.find('.evosy_fetched_events');
		TABLE = $('#fetched_events');

		sync_imported = $(this).data('sync');

		import_new = TABLE.find('tr input[value="ss"]').length;
		import_synced = TABLE.find('tr input[value="as"]').length;
		total_to_import = import_new + import_synced;
		MSG = $('.evosy_json_msg').data('msg');

		if( import_new==0 && import_synced>0 && sync_imported == 'no' ){
			BOX.find('.final_status').show();
			BOX.find('.final_status p').html( MSG.M002 );

			CONTAINER.find('.import_status').show();
			BOX.attr('class','inside fetchingcompleted importingcompleted');

			// sycn for deleted
			$('body').trigger('evosy_sync_deleted');

		}else if(import_new == 0 && import_synced == 0){
			BOX.find('.final_status').show();
			BOX.find('.final_status p').html( MSG.M002 );
			CONTAINER.find('.import_status').show();
			BOX.attr('class','inside fetchingcompleted importingcompleted');

			// sycn for deleted
			$('body').trigger('evosy_sync_deleted');

		}else{	
			// start the event importing			
			CONTAINER.find('.import_status').show();			
			import_event(TABLE, 1,  BOX, sync_imported);
		}		
	});

		var p_completed = 0;
		var p_failed = 0;
		var p_skipped = 0;
		function import_event(table, index,  BOX, sync_imported){
			CONTAINER = BOX.find('.evosy_fetched_events');

			// if total reached
			Total_table_rows = table.find("tr.regular-row").length;
			if(index > Total_table_rows) return false;

			var ajaxdataa = {},
				EVENTDATA = {},
				datarow = table.find("tr.regular-row").eq( (index-1) );

			if(datarow.length==0) return false;

			// collect input data
				datarow.find('input').each(function(){
					EVENTDATA[ $(this).attr('name') ] = encodeURIComponent( $(this).val() );
				});
				datarow.find('textarea').each(function(){
					EVENTDATA[ $(this).attr('name') ] = encodeURIComponent( $(this).val() );
				});

			STATUS = CONTAINER.find('.import_status');

			// for the first importing event
			if( index == 1){
				BOX.addClass('importing');		
				$('body').trigger('evo_show_settings_loader');			
			}

			// skip an event
			if( EVENTDATA.status == 'ns' || (EVENTDATA.status == 'as' && sync_imported=='no')){
				p_skipped++;
				import_event(table, (index +1), BOX, sync_imported);
				STATUS.find('span.ski b').html(p_skipped);	
				datarow.addClass('skipped');	
			}else{
				ajaxdataa['action'] = 'sync_process_events';
				ajaxdataa['event_data'] = EVENTDATA;
				
				$.ajax({
					beforeSend: function(){									
						CONTAINER.find('.import_status_inside').append('<em>Processing: <i>'+EVENTDATA.id+'</i></em>');	
					},
					type: 'POST',
					url: evosy_ajax_script.ajaxurl,
					data: ajaxdataa,
					dataType:'json',
					success:function(data){
						if(data.status =='good'){
							//per = parseInt((index/total)*100);
							datarow.addClass('done');
							p_completed++;
							STATUS.find('span.com b').html(p_completed);

							CONTAINER.find('.import_status_inside')
								.append("<em class='com'>Imported: "+ EVENTDATA.id + ( (data.event_id !='na')? ' as Event ID '+ data.event_id: '') + "</em>");

							// update event with link to event
							if( data.event_link!= 'na'){
								datarow.find('td.column-name').html( 
									"<a href='"+data.event_link+"'>" + decodeURIComponent(ajaxdataa.event_data.name) +"</a>"
								);
							}

							// update event id of the imported event
								if( data.event_id!= 'na'){
									datarow.find('td.column-status').html( 
										'<input type="hidden" name="importedid" value="'+data.event_id+'"><span class="imported"><a href="'+data.event_link+'" target="_blank">Imported</a></span>'
									);
								}
							
						}else{ // Failed

							p_failed++;
							CONTAINER.find('.import_status_inside').append("<em class='fail'>Failed: "+ EVENTDATA.id + ' - <i>Reason: '+data.content+"</i></em>");
							STATUS.find('span.fai b').html(p_failed);							
							datarow.addClass('failed');
						}

						// continuation
							if(index <= Total_table_rows){
								import_event(table, (index +1),  BOX, sync_imported);
							}

					},complete:function(){


					}
				});
			}

			// last item
				if(index == Total_table_rows){		

					MSG = $('.evosy_json_msg').data('msg');

					BOX.find('.final_status').show();
					BOX.find('.final_status p').html( MSG.M001 );

					BOX.attr('class','inside fetchingcompleted importingcompleted');	
					$('body').trigger('evo_hide_settings_loader');

					// sycn for deleted
					$('body').trigger('evosy_sync_deleted');
				}

		}

		$('body').on('evosy_sync_deleted',function(){
			sync_for_deleted_events();
		});

		function sync_for_deleted_events(){
			TABLE = $('#fetched_events');


			// already imported google calendar events
			SOURCE = $('.evosy_fetched_events').data('source');

			// check if settings to sync delete is enabled
			if( $('.evosy_fetched_events').data('syncdel') == 'no' ) return false;

			if( SOURCE != 'google') return false;

			has_imported_events = false;

			
			var EVENTSDATA = [];
			TABLE.find('td.column-status ').each(function(){
				// skip event rows without imported id
				if( $(this).find('input[name="importedid"]').length < 1 ) return true;

				Event_id = $(this).find('input[name="importedid"]').val();
				EVENTSDATA.push( Event_id );
				has_imported_events = true;
			});

			// if there are already imported events
			if( has_imported_events){

				MSG = $('.evosy_json_msg').data('msg');
				var ajaxdataa = {};
				ajaxdataa['action'] = 'sync_delete_synced';
				ajaxdataa['events'] = EVENTSDATA;
				
				$.ajax({
					beforeSend: function(){									
						$('body').trigger('evo_show_settings_loader');
						BOX.find('.final_status').show();
						BOX.find('.final_status p').append( '<em>' +MSG.M003 +"</em>");
					},
					type: 'POST',
					url: evosy_ajax_script.ajaxurl,
					data: ajaxdataa,
					dataType:'json',
					success:function(data){					

						add = ' '+ data.count + ' Events Deleted!';
						BOX.find('.final_status').show();
						BOX.find('.final_status p').append( '<em>' +MSG.M004 +add +"</em>");

					},complete:function(){	$('body').trigger('evo_hide_settings_loader'); }
				});

			}

		}

// Run scheduled fetching job manually
	$('.evosy_run_cron').on('click',function(){
		OBJ = $(this);
		INSIDE = OBJ.closest('.inside');
		PAR = INSIDE.find('.evosy_schedule_actions');

		var ajaxdataa = {};
		ajaxdataa['action']= 'sync_run_cron_job';	
		ajaxdataa['id']= $(this).data('id');	
		ajaxdataa['sig']= $(this).data('sig');	
		ajaxdataa['time']= $(this).data('time');	

		$.ajax({
			beforeSend: function(){
				$('body').trigger('evo_show_settings_loader');
			},
			type: 'POST',
			url:evosy_ajax_script.ajaxurl,
			data: ajaxdataa,
			dataType:'json',
			success:function(data){
				INSIDE.addClass('manual_cron_complete');
				PAR.append(data.content);
			},
			complete:function(){
				$('body').trigger('evo_hide_settings_loader');
			}
		});
	});

// click edit fetched event times
	$('#fetched_events').on('click','td.column-start_time, td.column-end_time',function(){
		if(!$(this).hasClass('editable')){
			OBJ = $(this);
			time = OBJ.find('input').val();
			OBJ.html('<input type="text" style="font-size:12px" name="'+OBJ.find('input').attr('name')+'" value="'+time+'"/>').addClass('editable');
			
			INPUT = OBJ.find('input');
			INPUT.focus();
			INPUT.keypress(function(e) {
			    if (e.which == 13) {
			    	OBJ.html( INPUT.val() + '<input type="hidden" name="'+INPUT.attr('name')+'" value="'+INPUT.val()+'"/>' ).removeClass('editable');
				    e.preventDefault();					    
			    }
			});
		}
		
	}).on('blur','td.column-start_time, td.column-end_time',function(){
		OBJ = $(this);
		OBJ.removeClass('editable');
		INPUT = OBJ.find('input');

		OBJ.html(INPUT.val() + '<input type="hidden" name="'+INPUT.attr('name')+'" value="'+INPUT.val()+'"/>');

	});

});