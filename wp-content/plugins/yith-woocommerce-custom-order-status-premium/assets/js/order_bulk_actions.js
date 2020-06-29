jQuery(function($){

	var top_select				= $('#bulk-action-selector-top'),
		bottom_select			= $('#bulk-action-selector-bottom'),
		my_custom_status		= localized_obj['my_custom_status'],
		mark_text				= localized_obj['mark_text'];

	for(var status in my_custom_status){
		$('<option>').val('mark_' + status).text(mark_text + ' ' + my_custom_status[status] ).appendTo(top_select);
		$('<option>').val('mark_' + status).text(mark_text + ' ' + my_custom_status[status] ).appendTo(bottom_select);
	}
});