jQuery( function( $ ) {

    function show_and_hide_pre_order_panel() {
        var is_preorder = $( 'input#_ywpo_preorder:checked' ).length;

        var pre_order_tab = $( '.show_if_preorder' );
        is_preorder ? pre_order_tab.show() : pre_order_tab.hide();
    }

    var $checkboxes = $( 'input#_ywpo_preorder, input#_downloadable, input#_virtual' );
    $checkboxes.change( function() {
        show_and_hide_pre_order_panel();
    }).change();

	var $product_type = $( 'select#product-type' );

    $product_type.change( function () {
		var select_val = $( this ).val();
		if ( 'simple' === select_val ) {
			$( 'input#_ywpo_preorder' ).change();
		} else {
			$( 'input#_ywpo_preorder' ).prop( "checked", false );
		}
	}).change();
	
	var now = new Date();

	$( '#_ywpo_for_sale_date' ).datetimepicker({
		defaultDate: '',
		dateFormat: 'yy/mm/dd',
		minDate: now
	});

	$( 'input._ywpo_price_adjustment' ).change( function() {
		var $radio = $('input._ywpo_price_adjustment:checked');
		if( $radio.val() == 'manual' ){
			$( 'p._ywpo_preorder_price_field' ).show();
			$( 'fieldset._ywpo_adjustment_type_field' ).hide();
			$( 'p._ywpo_price_adjustment_amount_field' ).hide();
		} else {
			$( 'p._ywpo_preorder_price_field' ).hide();
			$( 'fieldset._ywpo_adjustment_type_field' ).show();
			$( 'p._ywpo_price_adjustment_amount_field' ).show();
		}
	}).change();


	////////////// When variations are loaded... ///////////////////

	$( this ).bind( 'woocommerce_variations_loaded', function() {

		$( 'input.variable_is_preorder' ).change( function() {
			var is_preorder = $( this ).is( ':checked' );
			var pre_order_options_div = $( this ).closest( '.woocommerce_variation' ).find( '.show_if_variation_pre_order' );
			is_preorder ? pre_order_options_div.show() : pre_order_options_div.hide();
		}).change();

		var now = new Date();

		$( '.variable_ywpo_for_sale_datetimepicker' ).datetimepicker({
			defaultDate: '',
			dateFormat: 'yy/mm/dd',
			minDate: now
		});


		$( 'input.variable_ywpo_price_adjustment' ).change( function() {
			var name = $( this ).attr( 'name' );
			var $radio = $( this ).closest( '.woocommerce_variation' ).find( 'input.variable_ywpo_price_adjustment:checked' );
			if( $radio.val() == 'manual' ){
				$( this ).closest( '.woocommerce_variation' ).find( 'p.show_if_manual' ).show();
				$( this ).closest( '.woocommerce_variation' ).find( 'fieldset.hide_if_manual' ).hide();
				$( this ).closest( '.woocommerce_variation' ).find( 'p.hide_if_manual' ).hide();
			} else {
				$( this ).closest( '.woocommerce_variation' ).find( 'p.show_if_manual' ).hide();
				$( this ).closest( '.woocommerce_variation' ).find( 'fieldset.hide_if_manual' ).show();
				$( this ).closest( '.woocommerce_variation' ).find( 'p.hide_if_manual' ).show();
			}
		}).change();
	});

});