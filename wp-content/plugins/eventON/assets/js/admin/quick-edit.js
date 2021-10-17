/**
 * Eventon event quick edit
 * @version  2.5.2
 */
jQuery(function($){  

	// QUICK EDIT
    $('#the-list').on('click', '.editinline', function(){ 
		inlineEditPost.revert();

		var post_id = jQuery(this).closest('tr').attr('id');
		
		post_id = post_id.replace("post-", "");
		
		var $evo_inline_data = jQuery('#eventon_inline_' + post_id );
		
		// input values
			var FIELDS = ['evcal_start_date','evcal_start_time_hour','evcal_start_time_min','evcal_st_ampm','evcal_end_date','evcal_end_time_hour','evcal_end_time_min','evcal_et_ampm','evcal_subtitle','_evo_date_format','_evo_time_format',
				'_evo_date_format',
				'_evo_time_format',
			];
			for(i=0; i< FIELDS.length; i++){
				VAL = $evo_inline_data.find('.'+  FIELDS[i]).text();
				INPUT = jQuery('.inline-edit-row').find('input[name="'+ FIELDS[i] +'"]');
				INPUT.val( VAL ); 
			}
		
		// yes no fields
			var DATA = [
				'evo_hide_endtime',
				'evcal_allday',
				'_featured','evo_exclude_ev','evcal_gmap_gen',
				'evcal_hide_locname',
				'evo_access_control_location',
				'evo_evcrd_field_org',
			];
			for(i=0; i< DATA.length; i++){
				VAL = $evo_inline_data.find('.'+  DATA[i]).text();

				INPUT = jQuery('.inline-edit-row input[name="'+ DATA[i] +'"]');
				INPUT.val( VAL ); 
				if(VAL == 'yes'){
					INPUT.siblings('span').attr('class','ajde_yn_btn'); 
				}else{
					INPUT.siblings('span').attr('class','ajde_yn_btn NO'); 
				}
			}	

		// SELECT fields
			var sel_D = [
				'_ev_status'
			];	
			for(i=0; i< sel_D.length; i++){
				VAL = $evo_inline_data.find('.'+  sel_D[i]).text();


				sel = jQuery('.inline-edit-row select[name="'+ sel_D[i] +'"]');
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