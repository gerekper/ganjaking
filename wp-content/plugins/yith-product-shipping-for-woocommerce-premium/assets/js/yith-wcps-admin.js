jQuery(function($) {

	/*
	 *	Show/Hide shipping costs table
	 */

	$('body').on( 'change', 'input#_yith_product_shipping', function() {
		if ( $('input#_yith_product_shipping').is(':checked') ) { $(this).closest( 'div' ).find( '.yith_product_shipping_rows' ).show(); }
		else { $(this).closest( 'div' ).find( '.yith_product_shipping_rows' ).hide(); }
	});

	/*
	 *	Show/Hide shipping costs table on variations
	 */

	$('.woocommerce_variations').on( 'change', '.enable_yith_product_shipping', function() {
		if ( $(this).is(':checked') ) { $(this).closest('.woocommerce_variation').find( '.yith_product_shipping_rows').show(); }
		else { $(this).closest('.woocommerce_variation').find( '.yith_product_shipping_rows').hide(); }
	});

	/*
	 *	Hide shipping costs table if virtual variation
	 */

	$('body').on( 'change', 'input.variable_is_virtual', function() {
		if ( $(this).is(':checked') ) { $(this).closest('.woocommerce_variation').find( '.enable_yith_product_shipping' ).parent( 'label' ).hide(); }
		else { $(this).closest('.woocommerce_variation').find( '.enable_yith_product_shipping' ).parent( 'label' ).show(); }
	});

	/*
	 *	Insert a new row
	 */

	$('body').on( 'click', '.yith_product_shipping_rows .insert', function() {
		var $tbody = $(this).closest('.yith_product_shipping_rows').find('tbody');
		var postid = $(this).data('postid');

		var show_table_role		= $(this).data('show_table_role');
		var show_table_price	= $(this).data('show_table_price');
		var show_table_qty		= $(this).data('show_table_qty');
		var show_table_weight	= $(this).data('show_table_weight');
		var show_table_taxy		= $(this).data('show_table_taxy');
		var show_table_geo		= $(this).data('show_table_geo');

		var hide_table_role		= show_table_role	? '' : ' style="display:none;"';
		var hide_table_price	= show_table_price	? '' : ' style="display:none;"';
		var hide_table_qty		= show_table_qty	? '' : ' style="display:none;"';
		var hide_table_weight	= show_table_weight	? '' : ' style="display:none;"';
		var hide_table_taxy		= show_table_taxy	? '' : ' style="display:none;"';
		var hide_table_geo		= show_table_geo	? '' : ' style="display:none;"';


		var code = '<tr class="new"><td class="sort"><span class="dashicons dashicons-move"></span></td>';
		if ( false ) { // temporary disabled
			code += '<td class="product_id"><input type="text" value="" placeholder="*" /></td>';
		}

		// role
		code += '<td class="cart_total role"' + hide_table_role + '><input type="text" value="" placeholder="Save to edit" disabled="disabled"" /></td>';

		// price
		code += '<td class="cart_total min"' + hide_table_price + '><input type="text" value="" placeholder="0.00" name="yith_product_min_cart_total[new][]" /></td>\
				<td class="cart_total max"' + hide_table_price + '><input type="text" value="" placeholder="&infin;" name="yith_product_max_cart_total[new][]" /></td>';

		// quantity
		code += '<td class="cart_qty min"' + hide_table_qty + '><input type="text" value="1" placeholder="1" name="yith_product_min_cart_qty[new][]" /></td>\
			<td class="cart_qty max"' + hide_table_qty + '><input type="text" value="" placeholder="&infin;" name="yith_product_max_cart_qty[new][]" /></td>\
			<td class="quantity min"' + hide_table_qty + '><input type="text" value="1" placeholder="1" name="yith_product_min_quantity[new][]" /></td>\
			<td class="quantity max"' + hide_table_qty + '><input type="text" value="" placeholder="&infin;" name="yith_product_max_quantity[new][]" /></td>';

		// weight
		code += '<td class="weight min"' + hide_table_weight + '><input type="text" value="0" placeholder="0" name="yith_product_min_weight[new][]" /></td>\
			<td class="weight max"' + hide_table_weight + '><input type="text" value="" placeholder="&infin;" name="yith_product_max_weight[new][]" /></td>';
		
		// taxy
		code += '<td class="taxonomy"' + hide_table_taxy + '><input type="text" placeholder="Save to edit" disabled="disabled" /></td>\
			<td class="taxonomy"' + hide_table_taxy + '><input type="text" placeholder="Save to edit" disabled="disabled" /></td>';

		// geolocation
		code += '<td class="geo_exclude"' + hide_table_geo + '><input type="text" value="" placeholder="Save to edit" disabled="disabled"" /></td>\
			<td class="country_code"' + hide_table_geo + '><input type="text" value="" placeholder="Save to edit" disabled="disabled"" /></td>\
			<td class="state_code"' + hide_table_geo + '><input type="text" value="" placeholder="Save to edit" disabled="disabled" /></td>\
			<td class="postal_code"' + hide_table_geo + '><input type="text" value="" placeholder="*" name="yith_product_postal_code[new][]" /></td>';

		// costs
		code += '<td class="shipping_cost"><input type="text" value="" placeholder="0.00" name="yith_product_shipping_cost[new][]" /></td>\
			<td class="product_cost"><input type="text" value="" placeholder="0.00" name="yith_product_product_cost[new][]" /></td>\
			<td class="unique_cost"><input type="text" value="" placeholder="0.00" name="yith_product_unique_cost[new][]" /></td>\
		</tr>';

		if ( $tbody.find('tr.current').size() > 0 ) { $tbody.find('tr.current').after( code ); }
		else { $tbody.append( code ); }
		return false;
	});

	/*
	 *	Focus a row
	 */

	$('body').on( 'click', '.yith_product_shipping_rows input', function() {
		$(this).focus();
	  	return true;
	});

	$('body').on( 'click', '.yith_product_shipping_rows tr', function() {
		$('.yith_product_shipping_rows tr').removeClass('current');
		$(this).addClass('current');
		$('.yith_product_shipping_rows .remove').removeClass('disabled');
	});

	/*
	 *	Focus a row and show "Remove" button
	 */

	$('body').on( 'focus', '.yith_product_shipping_rows input', function() {
		$('.yith_product_shipping_rows tr').removeClass('current');
		$(this).closest('tr').addClass('current');
		$('.yith_product_shipping_rows .remove').removeClass('disabled');
	});

	/*
	 *	Remove a row and hide "Remove" button
	 */

	$('body').on( 'click', '.yith_product_shipping_rows .remove', function() {
		var $tbody = $(this).closest('.yith_product_shipping_rows').find('tbody');
		if ( $tbody.find('tr.current').size() > 0 ) {
			$tbody.find('tr.current').find('input').val('');
			$tbody.find('tr.current').hide();
			$('.yith_product_shipping_rows .remove').addClass('disabled');
		}
		return false;
	});

	/**
	 * On Load
	 */
	$('body').on( 'yith_product_shipping_init', function() {
		$('input.variable_is_virtual').change();
		$('input#_yith_product_shipping').change();
		$('input.enable_yith_product_shipping').change();
		$('body').trigger( 'yith_product_shipping_init_sortable' );
	});
	$('body').on( 'yith_product_shipping_init_sortable', function() { $('.yith_product_shipping_rows.sortable tbody').sortable( { items:'tr', axis:'y' } ); });
	$('body').trigger( 'yith_product_shipping_init' );
	$('body').on( 'woocommerce_variations_added woocommerce_variations_loaded', function() { $('body').trigger( 'yith_product_shipping_init' ); });
	$('.woocommerce_variations').on( 'woocommerce_variations_added woocommerce_variations_loaded', function() { $('body').trigger( 'yith_product_shipping_init' ); });

	/**
	 * Country States
	 */
	$('.country_code p.countries select').on('change', function(){
		var select = $(this).closest('tr').find('.state_code select');
		select.addClass('disabled');
		var data = {
			action: 'yith_wc_country_states',
			country: $(this).val()
		};
		$.post( ajaxurl, data, function(response) {
			select.html( response );
			select.removeClass('disabled');
		});
	});

});
