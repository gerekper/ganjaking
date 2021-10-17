/** 
 * Javascript for Importing Events
 * @version  1.0
 */
jQuery(document).ready(function($){

	// view report
		$('#evoics_view_jobdetails').on('click',function(){
			$('.evoics_log').toggle();
		});
	// change status on fetched data
		$('#evoics_events').on('click','span.status',function(){
			OBJ = $(this);
			TR = OBJ.closest('tr');			

			if( OBJ.hasClass('ss')){
				TR.removeClass('ss').addClass('ns').attr('data-status','ns');
				OBJ.attr({'class':'status ns','title':'Not Selected'});
				TR.find('input.input_status').val('ns');

			}else if(OBJ.hasClass('ns')){
				TR.removeClass('ns').addClass('ss').attr('data-status','ss');
				OBJ.attr({'class':'status ss','title':'Selected'});
				TR.find('input.input_status').val('ss');
			}
		});

	// change status on items all at once
		// deselect all
		$('#select_row_options').on('click','a.deselect',function(){
			$('#evoics_events').find('tr').each(function(){
				TR = $(this);
				if(!TR.find('span.status').hasClass('as')){
					TR.attr({'class':'regular-row ns'});
					TR.find('input.input_status').val('ns');
					TR.find('span.status').attr({'class':'status ns','title':'Not Selected'});
				}				
			});
		});
		// select all
		$('#select_row_options').on('click','a.select',function(){
			$('#evoics_events').find('tr').each(function(){
				TR = $(this);
				if(!TR.find('span.status').hasClass('as')){
					TR.attr({'class':'regular-row ss'});
					TR.find('input.input_status').val('ss');
					TR.find('span.status').attr({'class':'status ss','title':'Selected'});
				}				
			});
		});

	// click field to edit 

		$('.column-start_date_time').on('click','span',function(e){
			e.stopPropagation();
			dt_editing( $(this));	
		});
		$('.column-end_date_time').on('click','span',function(e){
			e.stopPropagation();
			dt_editing( $(this));	
		});
		function dt_editing( t){
			if(!t.hasClass('editing')){				
				v = t.html();
				nh = "<input value='"+ v+"'/>";
				t.html( nh).addClass('editing');
			}
		}		
		$('.column-start_date_time').keypress(function(e){
			if(e.which == 13)	dt_saving( $(this));
		});
		$('.column-end_date_time').keypress(function(e){
			if(e.which == 13)	dt_saving( $(this));
		});
		function dt_saving( obj){
			i = obj.find('input');
			nv = i.val();
			sp = i.parent();
			sp.html( nv).removeClass('editing');
			f = sp.attr('class');
			sp.closest('tr').find('input[name="events['+ sp.data('i') +']['+f+']"]').val( nv);
		}

	// import selected items
		$('#evoics_import_selected_items').on('click', function(){
			var DATA_SECTION = $(this).closest('.evoics_data_section'),
				table = DATA_SECTION.find('table');

			selected_row_count = table.find("tr.ss").size();


			// update actual running count
			DATA_SECTION.find('#evoics_import_progress p.text i').html(selected_row_count);

			if(selected_row_count==0){
				$('#evoics_import_errors').html('No events selected for import').fadeIn();
			}else{
				$('#evoics_import_errors').fadeOut();
				import_event_item(table, 1, selected_row_count, 0, 0);	
			}				

		});

	// IMPORT selected events
		function import_event_item(table, index, total, failed, skipped){

			// if total reached
				if(index > total) return false;

			var ajaxdataa = { },
				datarow = table.find("tr.ss").eq( (index-1) );

			if(datarow.length==0) return false;

			SECTION = $('.evoics_data_section');

			datarow.find('.evoics_event_data_row').each(function(){
				ajaxdataa[ $(this).attr('name') ] = encodeURIComponent( $(this).val() );
			});

			ajaxdataa['action']='evoics_001';
			ajaxdataa['nonce'] = evoics_ajax_script.postnonce;

			$.ajax({
				beforeSend: function(){	
					$('#evoics_import_progress').show();	
				},
				type: 'POST',
				url: evoics_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					//console.log(data);
					if(data.status=='success'){
						per = parseInt((index/total)*100);
						$('#evoics_import_progress').find('p.bar span').css('width', per+'%' );						
						datarow.addClass('done');

						// update event link
						if(data.event_link != ''){
							NAME = datarow.find('td.column-event_name span').html();
							datarow.find('td.column-event_name span').html( "<a href='"+data.event_link+"'>"+NAME+"</a>");
						}

						if(index <= total){
							import_event_item(table, (index +1), total, failed, skipped);
						}
					}else{
						if(data.status =='skipped'){
							skipped++;
							$('#evoics_import_progress span.skipped').show();
							$('#evoics_import_progress span.skipped').find('em').html(skipped);
							datarow.addClass('skipped');
						}else{
							failed++;
							$('#evoics_import_progress span.failed').show().find('em').html(failed);
							datarow.addClass('failed');
						}

						// continue with next
						if(index < total){
							import_event_item(table, (index +1), total, failed, skipped);
						}
					}

					$('#evoics_import_progress em.processed').html(index);

					// last item
					if(index == total){
						// last item
						good = total - failed -skipped;
						resultsOBJ = $('#evoics_import_results');
						resultsOBJ.find('.good em').html(good);
						resultsOBJ.find('.bad em').html(failed);
						resultsOBJ.find('.skipped em').html(skipped);
						SECTION.find('#evoics_import_results').fadeIn();

						SECTION.find('#select_row_options').hide();
						SECTION.find('#evoics_import_progress').fadeOut();
						$('#evoics_import_progress p.text b').removeClass('evoloading');	
					}
					
				},complete:function(){						
				}
			});

		}

	// Run scheduled fetching job manually
	$('.evoics_run_cron').on('click',function(){
		OBJ = $(this);
		FORM = OBJ.closest('form');
		ITM = FORM.find('.evoics_schedule_actions');

		var ajaxdataa = {};
		ajaxdataa['action']= 'evoics_002';		
		ajaxdataa['nonce']= OBJ.data('nonce');		

		$.ajax({
			beforeSend: function(){
				ITM.addClass('evoloading');
			},
			type: 'POST',
			url:evoics_ajax_script.ajaxurl,
			data: ajaxdataa,
			dataType:'json',
			success:function(data){
				ITM.append(data.content);
			},
			complete:function(){
				ITM.removeClass('evoloading');
			}
		});
	});	
});