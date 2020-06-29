jQuery(document).ready(function($){

	if( 'easy_pricing_table' != $('[name="wcopc_template"]') ) {
		$('#wcopc_easy_pricing_table_fields').slideUp(0);
	}

	setTimeout(function(){
		$('body.iframe').css({height:'auto'});
	}, 200);

	$('select.ajax_chosen_select_products').ajaxChosen({
		method: 	'GET',
		url: 		ajaxurl,
		dataType: 	'json',
		afterTypeDelay: 100,
		data:		{
			action:  'woocommerce_json_search_products',
			security: wcopc.search_products_nonce
		}
	}, function (data) {

		var terms = {};

		$.each(data, function (i, val) {
			terms[i] = val;
		});

		return terms;
	});

	$('[name="wcopc_template"]').on('change',function(e){
		if( 'easy_pricing_table' == $(this).val() ) {
			$('#wcopc_easy_pricing_table_fields').slideDown();
			$('#wcopc_product_ids_fields').slideUp();
		} else {
			$('#wcopc_easy_pricing_table_fields').slideUp();
			$('#wcopc_product_ids_fields').slideDown();
		}
	});

	$('#wcopc_settings').on('submit',function(e){
		var args = top.tinymce.activeEditor.windowManager.getParams(),
			chosen_template = $('[name="wcopc_template"]:checked').val(),
			custom_shortcode_atts,
			shortcode;

		shortcode  = '[' + args.shortcode;

		if ( 'undefined' !== typeof chosen_template ) {
			shortcode += ' template="' + chosen_template + '"';
		}

		$('#wcopc_settings select').each(function(){
			if($('option:selected',$(this)).length && 'default' != $(this).val() ){
				// if the template is easy pricing tables, don't include product IDs
				if ('wcopc_product_ids'==$(this).attr('id')&&'easy_pricing_table'==chosen_template){
					return true;
				// if the template is not easy pricing tables, don't include easy_pricing_table_id
				} else if('wcopc_easy_pricing_table_id'==$(this).attr('id')&&'easy_pricing_table'!=chosen_template){
					return true;
				}
				shortcode += ' ' + $(this).attr('id').replace('wcopc_','') + '="' + $(this).val() + '"';
			}
		});

		// Allow plugins to add shortcode attributes not in a select box
		custom_shortcode_atts = $('#wcopc_settings').triggerHandler('wcopc_add_shortcode_attributes');

		if ( typeof custom_shortcode_atts !== 'undefined' ) {
			shortcode += custom_shortcode_atts;
		}

		shortcode += ']';

		top.tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
		top.tinymce.activeEditor.windowManager.close();
		e.preventDefault();
	});

	$('#wcopc_cancel').on('click',function(e){
		top.tinymce.activeEditor.windowManager.close();
		e.preventDefault();
	});

	// Tooltips
	$('.tips, .help_tip').tipTip( {
		'attribute' : 'data-tip',
		'fadeIn' : 50,
		'fadeOut' : 50,
		'delay' : 200,
		'maxWidth' : '400px',
		'minWidth' : '400px'
	} );

});
