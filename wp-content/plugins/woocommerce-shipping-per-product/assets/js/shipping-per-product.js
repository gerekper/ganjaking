jQuery(function($) {

	var controlled = false;

	$( document.body ).on( 'keyup keydown', function( e ) {
		controlled = e.ctrlKey || e.metaKey;
	});

	var recalculate_orders = function() {
		var $orders = $('[name^="per_product_order"]');
		$orders.each( function( index, el ) {
			$( el ).val( index );
		} );
	};

	$('body')
		.on( 'init_shipping_per_product', function() {
			$('input#_per_product_shipping').change();
			$('input.variable_is_virtual').change();
			$('.enable_per_product_shipping').change();
			$('body').trigger( 'init_shipping_per_product_sortable' );
		})
		.on( 'init_shipping_per_product_sortable', function() {
			$('.per_product_shipping_rules tbody').sortable({
				items:'tr',
				cursor:'move',
				axis:'y',
				scrollSensitivity:40,
				forcePlaceholderSize: true,
				helper: 'clone',
				opacity: 0.65,
				placeholder: 'wc-metabox-sortable-placeholder',
				start:function(event,ui){
					ui.item.css('background-color','#f6f6f6');
				},
				stop:function(event,ui){
					recalculate_orders();
					ui.item.removeAttr('style');
				}
			});
		})
		.on( 'change', 'input#_per_product_shipping', function() {
			if ( $('input#_per_product_shipping').is(':checked') ) {
				$(this).closest( 'div' ).find( '.per_product_shipping_rules' ).show();
			} else {
				$(this).closest( 'div' ).find( '.per_product_shipping_rules' ).hide();
			}
		})
		.on( 'change', 'input.variable_is_virtual', function() {
			if ( $(this).is(':checked') ) {
				$(this).closest('.woocommerce_variation').find( '.enable_per_product_shipping' ).parent( 'label' ).hide();
			} else {
				$(this).closest('.woocommerce_variation').find( '.enable_per_product_shipping' ).parent( 'label' ).show();
			}
		});

	$('.woocommerce_variations')
		.on( 'change', '.enable_per_product_shipping', function() {
			if ( $(this).is(':checked') ) {
				$(this).closest('.woocommerce_variation').find( '.per_product_shipping_rules').show();
			} else {
				$(this).closest('.woocommerce_variation').find( '.per_product_shipping_rules').hide();
			}
		} )
		.on( 'woocommerce_variations_added woocommerce_variations_loaded', function() {
			$('body').trigger( 'init_shipping_per_product' );
		} );

	$( '#woocommerce-product-data' ).on( 'woocommerce_variations_added woocommerce_variations_loaded', function() {
		$('body').trigger( 'init_shipping_per_product' );
	} );

	$('#woocommerce-product-data')
		.on( 'focus', '.per_product_shipping_rules input', function() {
			if ( ! controlled ) {
				$('.per_product_shipping_rules tr').removeClass('current');
			}
			$(this).closest('tr').addClass('current');
		} )
		.on( 'click', '.per_product_shipping_rules input', function() {
			$(this).focus();
		  	return true;
		} )
		.on( 'click', '.per_product_shipping_rules .remove', function() {
			var $tbody = $(this).closest('.per_product_shipping_rules').find('tbody');
			if ( $tbody.find('tr.current').size() > 0 ) {
				$tbody.find('tr.current').find('input').val('');
				$tbody.find('tr.current').hide();
			} else {
				alert( wc_shipping_per_product_params.i18n_no_row_selected );
			}
			return false;
		} )
		.on( 'click', '.per_product_shipping_rules .insert', function() {
			var $tbody = $(this).closest('.per_product_shipping_rules').find('tbody');
			var postid = $(this).data('postid');
			var code = '<tr>\
				<td class="sort">&nbsp;<input type="hidden" value="" name="per_product_order[' + postid + '][new][]" /></td>\
				<td class="country"><input type="text" value="" placeholder="*" name="per_product_country[' + postid + '][new][]" /></td>\
				<td class="state"><input type="text" value="" placeholder="*" name="per_product_state[' + postid + '][new][]" /></td>\
				<td class="postcode"><input type="text" value="" placeholder="*" name="per_product_postcode[' + postid + '][new][]" /></td>\
				<td class="cost"><input type="text" value="0.00" placeholder="0.00" name="per_product_cost[' + postid + '][new][]" /></td>\
				<td class="item_cost"><input type="text" value="0.00" placeholder="0.00" name="per_product_item_cost[' + postid + '][new][]" /></td>\
			</tr>';

			if ( $tbody.find('tr.current').size() > 0 ) {
				$tbody.find('tr.current').last().after( code );
			} else {
				$tbody.append( code );
			}
			recalculate_orders();
			return false;
		} )
		.on( 'click', '.per_product_shipping_rules .export', function() {
			var postid = $(this).data('postid');
			var csv_data = "data:application/csv;charset=utf-8," + wc_shipping_per_product_params.i18n_product_id + "," + wc_shipping_per_product_params.i18n_country_code + "," + wc_shipping_per_product_params.i18n_state + "," + wc_shipping_per_product_params.i18n_postcode + "," + wc_shipping_per_product_params.i18n_cost + "," + wc_shipping_per_product_params.i18n_item_cost + "\n";

			$(this).closest('.per_product_shipping_rules').find('tbody tr').each(function() {
				var row = postid + ',';
				$(this).find('input:not([type=hidden])').each(function() {
					var val = $(this).val();
					if ( ! val )
						val = $(this).attr('placeholder');
					row = row + val + ',';
				});
				row = row.substring( 0, row.length - 1 );
				csv_data = csv_data + row + "\n";
			});

			$(this).attr( 'href', encodeURI( csv_data ) );

			return true;
		} );

	$('body').trigger( 'init_shipping_per_product' );
});
