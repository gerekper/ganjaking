/** 
 * Javascript for Importing Events
 * @version  1.0
 */
jQuery(document).ready(function($){

	// change status on fetched data
		$('#evocsv_events').on('click','.row',function(){
			statusObj = $(this).find('.status');
			status = $(this).attr('data-status');

			if(status == 'ns'){
				$(this).attr('data-status','ss');
				statusObj.removeClass('ns').addClass('ss').attr('title','Selected');
				$(this).find('.input_status').val('ss');

			}else if(status == 'ss'){
				$(this).attr('data-status','ns');
				statusObj.removeClass('ss').addClass('ns').attr('title','Not Selected');
				$(this).find('.input_status').val('ns');
			}

			
		});

	// change status on items all at once
		$('#select_row_options').on('click','a.deselect',function(){
			$('#evocsv_events').find('tr').each(function(){
				$(this).attr('data-status','ns');
				$(this).find('input.input_status').val('ns');
				$(this).find('span.status').attr({'class':'status ns','title':'Not Selected'});
			});
		});
		$('#select_row_options').on('click','a.select',function(){
			$('#evocsv_events').find('tr').each(function(){
				$(this).attr('data-status','ss');
				$(this).find('input.input_status').val('ss');
				$(this).find('span.status').attr({'class':'status ss','title':'Selected'});
			});
		});

	// import selected items
		$('#evocsv_import_selected_items').on('click', function(){
			var form = $(this).closest('form'),
				table = form.find('table');

			selected_row_count = table.find("tr[data-status='ss']").size();

			// update actual running count
			form.find('#evocsv_import_progress p.text i').html(selected_row_count);

			if(selected_row_count==0){
				$('#evocsv_import_errors').html('No events selected for import').fadeIn();
			}else{
				$('#evocsv_import_errors').fadeOut();
				import_event_item(table, 1, selected_row_count, form, 0);	
			}				

		});

	// IMPORT selected events
		function import_event_item(table, index, total, form, failed){

			// if total reached
				if(index > total) return false;

			var ajaxdataa = { },
				datarow = table.find("tr[data-status='ss']").eq( (index-1) );


			if(datarow.length==0) return false;

			datarow.find('.evocsv_event_data_row').each(function(){
				ajaxdataa[ $(this).attr('name') ] = encodeURIComponent( $(this).val() );
			});

			ajaxdataa['action']='evocsv_001';
			ajaxdataa['nonce']=evocsv_ajax_script.postnonce;

			$.ajax({
				beforeSend: function(){	
					form.find('#evocsv_import_progress').show();	
				},
				type: 'POST',
				url: evocsv_ajax_script.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					//console.log(data);
					if(data.status=='success'){
						per = parseInt((index/total)*100);
						form.find('#evocsv_import_progress p.bar span').css('width', per+'%' );
						form.find('#evocsv_import_progress p.text em').html(index);
						datarow.addClass('done');

						if(index <= total){
							import_event_item(table, (index +1), total, form, failed);
						}

						// last item
						if(index == total){
							// show results
							good = total - failed;
							resultsOBJ = form.find('#evocsv_import_results .results');
							resultsOBJ.find('.good em').html(good);
							resultsOBJ.find('.bad em').html(failed);
							$('#evocsv_import_results').fadeIn();

							$('#select_row_options').hide();
							$('#evocsv_import_progress').fadeOut();
						}
					}else{
						failed++;
						form.find('#evocsv_import_progress span.failed').show().find('em').html(failed);
						datarow.addClass('failed');

						// continue with next
						if(index < total){
							import_event_item(table, (index +1), total, form, failed);
						}
					}

					// last item
					if(index == total){
						form.find('#evocsv_import_progress p.text b').removeClass('loading');	
					}
					
				},complete:function(){										
					//form.parent().removeClass('loading');	
				}
			});

		}	
});