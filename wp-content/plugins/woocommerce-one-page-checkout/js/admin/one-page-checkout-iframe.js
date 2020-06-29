jQuery(document).ready(function($){

	var $product_ids = $( document.getElementById( 'wcopc_product_ids' ) );
	var $category_ids = $( document.getElementById( 'wcopc_category_ids' ) );
	var $template = $( '[name="wcopc_template"]' );

	if( 'easy_pricing_table' != $template ) {
		$('#wcopc_easy_pricing_table_fields').slideUp(0);
	}

	setTimeout(function(){
		$('body.iframe').css({height:'auto'});
	}, 200);

	$template.on('change',function(e){
		var $tableFields = $('#wcopc_easy_pricing_table_fields');
		var $productFields = $('#wcopc_product_ids_fields');

		if( 'easy_pricing_table' == $(this).val() ) {
			$tableFields.slideDown();
			$productFields.slideUp();
		} else {
			$tableFields.slideUp();
			$productFields.slideDown();
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

		// If the template is easy pricing tables, don't include product or category IDs.
		if ( 'easy_pricing_table' !== chosen_template ) {
			// Handle the select2 multi select input (its not a standard select field like chosen)
			if ( $product_ids.select2( 'val' ) ) {
				shortcode += ' product_ids="' + $product_ids.select2( 'val' ) + '"';
			}
			if ( $category_ids.select2( 'val' ) ) {
				shortcode += ' category_ids="' + $category_ids.select2( 'val' ) + '"';
			}
		}

		// Append the easy pricing table id
		if ( $('#wcopc_easy_pricing_table_id').length > 0 && 'easy_pricing_table' == chosen_template ) {
			shortcode += ' easy_pricing_table_id="' + $('#wcopc_easy_pricing_table_id').val() + '"';
		}

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
		'maxWidth' : '200px',
	} );
});
