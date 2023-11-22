/** 
 * Javascript for Importing Events
 * @version  2.0
 */
jQuery(document).ready(function($){
	var BB = $('body');

	// trigger run
		BB.on('click','.evoics_triger_fileup',function(event){
			event.preventDefault();

			LB = $(this).closest('.evo_lightbox');
			LB.evo_lightbox_hide_msg();

			const par = $(this).closest('.evoics_import_content');
			var files = $('#evoics_ics_file').prop('files');
			const acceptable_file_type = $('#evoics_ics_file').data('file_type');
			var file = files[0];

			
			if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
				alert('The File APIs are not fully supported in this browser.');
			    return;
			}

			if( file === undefined ){
				LB.evo_lightbox_show_msg({
					'type':'bad','message':'Missing File!'
				}); return;
		    }

		    // only ics files
		    if( file.name.indexOf( acceptable_file_type ) == -1 ){
		    	LB.evo_lightbox_show_msg({
					'type':'bad',
					'message':'Only accept '+acceptable_file_type+' file format.'
				}); return;	  		
		  	}


			//ajaxdata['file'] = $('#evoics_ics_file').prop('files');
			//console.log(ajaxdata);

			$(this).evo_ajax_lightbox_form_submit({
				'uid':'evoics_import_file_go',
				'lightbox_key':'evoics_import',
				'load_new_content':true,
	  			'load_new_content_id':'evoics_import_content_form',
	  			'ajax_action':'evoics_process_uploaded_ics',
			});
		});

	// apply more options changes from SP
		BB.on('click','.evoics_trig_more_options',function(){

			const el = $(this);
			var spv = {};

			$('#evops_content').find('input, select').each(function(){
				const n = $(this).attr('name');				
				$('#evoics_fetched_events').find('input.'+ n).val( $(this).val() );

				spv[ n ] = $(this).val();

				// modify the time for events
				if( n == 'evoics_time_mod'){
					if( $(this).val() != '0'){
						$('#evoics_fetched_events').find('i.addz').remove();
						$('#evoics_fetched_events').find('span.event_start_time').after( "<i class='addz'>"+$(this).val() +" min</i>" );
						$('#evoics_fetched_events').find('span.event_end_time').after( "<i class='addz'>"+$(this).val() +" min</i>" );

						$('#evoics_fetched_events').find('input.time_adds').val( $(this).val() );
					}
				}
			});
			
			BB.evo_savevals_sidepanel( spv);
			BB.evo_close_sidepanel();

		});

	// view report
		BB.on('click','#evoics_view_jobdetails',function(){
			BB.find('.evoics_log').toggle();
		});

	// processed events list functions
	$.fn.evoics_processed_events = function(){
		LIST = this;

		// select deselect all
			LIST.on('click','.evoics_sel_desel_trig',function(){
				if( $(this).hasClass('checked') ){
					$(this).toggleClass('checked').find('i').attr('class','fa fa-minus');

					LIST.find('tr').each(function(){
						TR = $(this);
						if(!TR.find('span.status').hasClass('as')){
							TR.attr({'class':'regular-row ns'});
							TR.find('input.input_status').val('ns');
							TR.find('span.status').attr({'class':'status ns','title':'Not Selected'});
						}				
					});
				}else{

					$(this).toggleClass('checked').find('i').attr('class','fa fa-check');
					LIST.find('tr').each(function(){
						TR = $(this);
						if(!TR.find('span.status').hasClass('as')){
							TR.attr({'class':'regular-row ss'});
							TR.find('input.input_status').val('ss');
							TR.find('span.status').attr({'class':'status ss','title':'Selected'});
						}				
					});

				}
			});

		// change status
			LIST.on('click','span.status',function(){
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

		// edit times
			LIST.on('click','.column-start_date_time span',function(e){
				e.stopPropagation();
				dt_editing( $(this));	
			});
			LIST.on('click','.column-end_date_time span',function(e){
				e.stopPropagation();
				dt_editing( $(this));	
			});
			LIST.on('keypress','.column-start_date_time',function(e){
				if(e.which == 13)	dt_saving( $(this));
			});
			LIST.on('keypress','.column-end_date_time',function(e){
				if(e.which == 13)	dt_saving( $(this));
			});

			var dt_editing = function( t ){
				if(!t.hasClass('editing')){				
					v = t.html();
					nh = "<input value='"+ v+"'/>";
					t.html( nh).addClass('editing');
				}
			}
			var dt_saving = function( obj){
				obj.find('input').each(function(){
					i = $(this);
					nv = i.val();
					sp = i.parent();
					sp.html( nv).removeClass('editing');
					sp.closest('tr').find('input[name="events['+ sp.data('i') +']['+ sp.attr('class') +']"]').val( nv);
				});
				
			}
		
		// IMPORT
			LIST.on('click', '#evoics_import_selected_items',function(){
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
	}
	
	// interaction after file process
		BB
		.on('evo_ajax_success_evoics_import_file_go',function(event, OO, data, el){
			BB.find('#evoics_import_content').evoics_processed_events();
		}).on('evo_ajax_success_evoics_import_remote_file_go',function(event, OO, data, el){
			BB.find('#evoics_import_content').evoics_processed_events();
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
					
				},complete:function(){	}
			});

		}

});