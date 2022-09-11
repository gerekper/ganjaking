/**
 * Eventon event quick edit
 * @version  2.5.2
 */
jQuery(function($){  

	// QUICK EDIT
    $('#the-list').on('click', '.editinline', function(){ 
		inlineEditPost.revert();

		var post_id = $(this).closest('tr').attr('id');		
		post_id = post_id.replace("post-", "");
		
		var event_data = $('#eventon_inline_' + post_id );
		//const edit_row = $('#edit-'+ post_id);
		const edit_row = $('.inline-edit-row');
		
		// input values
			var FIELDS = ['evcal_start_date','evcal_start_time_hour','evcal_start_time_min','evcal_st_ampm','evcal_end_date','evcal_end_time_hour','evcal_end_time_min','evcal_et_ampm','evcal_subtitle','_evo_date_format','_evo_time_format',
				'_evo_date_format',
				'_evo_time_format',
			];
			for(i=0; i< FIELDS.length; i++){
				VAL = event_data.find('.'+  FIELDS[i]).text();
				INPUT = edit_row.find('input[name="'+ FIELDS[i] +'"]');
				INPUT.val( VAL ); 
			}

		
		// RESETS
			edit_row.find('.evo_event_start_time').show();
			edit_row.find('.evo_longer_events_notice').hide();

		// yes no fields
			var DATA = [
				'evo_hide_endtime',
				'evcal_allday',
				'_featured','evo_exclude_ev','evcal_gmap_gen',
				'evcal_hide_locname',
				'evo_access_control_location',
				'evo_evcrd_field_org',
				'evo_year_long', '_evo_month_long'
			];
			for(i=0; i< DATA.length; i++){
				VAL = event_data.find('.'+  DATA[i]).text();

				INPUT = edit_row.find('input[name="'+ DATA[i] +'"]');
				INPUT.val( VAL ); 
				if(VAL == 'yes'){
					INPUT.siblings('span').attr('class','ajde_yn_btn'); 

					// if month long
					if( DATA[i] == '_evo_month_long'){

						edit_row.find('.evo_longer_events_notice').show();

						edit_row.find('.evo_event_start_time').hide();
						edit_row.find('.evo_event_end_time').hide();
					}

				}else{
					INPUT.siblings('span').attr('class','ajde_yn_btn NO'); 
				}
			}	

		// SELECT fields
			var sel_D = [
				'_ev_status'
			];	
			for(i=0; i< sel_D.length; i++){
				VAL = event_data.find('.'+  sel_D[i]).text();


				sel = edit_row.find('select[name="'+ sel_D[i] +'"]');
				sel.find('option[value="'+ VAL +'"]' ).attr('selected',true); 
			}	
    }); 

	// BULK EDIT
	$( '#wpbody' ).on( 'change', '#eventon-fields-bulk .inline-edit-group .change_to', function() {

		if ( 0 < $( this ).val() ) {
			$( this ).closest( 'div' ).find( '.change-input' ).show();
		} else {
			$( this ).closest( 'div' ).find( '.change-input' ).hide();
		}

	});

    
});  