jQuery(document).ready(function() {
	upt_limit_tags();
});
jQuery(document).ajaxComplete(function(){
	upt_limit_tags();
});

function upt_limit_tags (){
	jQuery(".userpro-field-tags").find('.chosen-select').chosen('destroy');
	jQuery(".userpro-field-tags .chosen-select").chosen({
		max_selected_options:userpro_tags_script_data.userpro_limit_tags,
		disable_search_threshold: 10,
		width: '100%'
	});	
}