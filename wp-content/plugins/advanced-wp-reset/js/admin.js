jQuery(function($) {

	var $reset_warning = $("#DBR_dialog");
	$reset_warning.dialog({
		'dialogClass'   : 'wp-dialog',
		'modal'         : true,
		'width'			: 500,
		'autoOpen'      : false,
		'closeOnEscape' : true,
		'buttons'       : {
			"Close": function() {
				$(this).dialog('close');
			},
			"Continue": function() {
				$('form[id="DBR_form"]').submit();
			}
		}
	});

	$("#DBR_reset_button").click(function(event) {
		var $reset_confirmation_text = $('#DBR_reset_comfirmation').attr('value');
		if($reset_confirmation_text == 'reset'){
			event.preventDefault();
			$reset_warning.dialog('open');
		}
	});

});