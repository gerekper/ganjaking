/*global $, woocommerce_shipping_flat_rate_box_rows, ajaxurl */
( function( $, data, wp, ajaxurl ) {

	var wc_flat_rate_box_rows_row_template = wp.template( 'flat-rate-box-row-template' ),
		$boxes_table                    = $( '#flat_rate_boxes' ),
		$boxes                          = $boxes_table.find( 'tbody.flat_rate_boxes' );

	var wc_flat_rate_box_rows = {
		init: function() {
			$boxes_table
				.on( 'click', 'a.add-box', this.onAddRate )
				.on( 'click', 'a.remove', this.onRemoveRate )

			var boxes_data = $boxes.data( 'boxes' );

			$( boxes_data ).each( function( i ) {
				var size = $boxes.find( '.flat_rate_box' ).length;
				$boxes.append( wc_flat_rate_box_rows_row_template( {
					box:  boxes_data[ i ],
					index: size
				} ) );
			} );

			$boxes.sortable( {
				items: 'tr',
				cursor: 'move',
				axis: 'y',
				handle: 'td',
				scrollSensitivity: 40,
				helper: function(e,ui){
					ui.children().each( function() {
						$( this ).width( $(this).width() );
					});
					ui.css( 'left', '0' );
					return ui;
				},
				start: function( event, ui ) {
					ui.item.css('background-color','#f6f6f6');
				},
				stop: function( event, ui ) {
					ui.item.removeAttr( 'style' );
					wc_flat_rate_box_rows.reindexRows();
				}
			} );
		},
		onAddRate: function( event ) {
			event.preventDefault();
			var target = $boxes;
			var size   = target.find( '.flat_rate_box' ).length;

			target.append( wc_flat_rate_box_rows_row_template( {
				box:  {
					box_id: '',
					box_length: '',
					box_width: '',
					box_height: '',
					box_max_weight: '',
					box_cost: '',
					box_cost_per_weight_unit: '',
					box_cost_percent: ''
				},
				index: size
			} ) );
		},
		onRemoveRate: function( event ) {
			event.preventDefault();
			if ( confirm( data.i18n.delete_rates ) ) {
				var box_ids  = [];

				$boxes.find( 'tr td.check-column input:checked' ).each( function( i, el ) {
					var box_id = $(el).closest( 'tr.flat_rate_box' ).find( '.box_id' ).val();
					box_ids.push( box_id );
					$(el).closest( 'tr.flat_rate_box' ).addClass( 'deleting' );
				});

				var ajax_data = {
					action: 'woocommerce_flat_rate_box_delete',
					box_id: box_ids,
					security: data.delete_box_nonce
				};

				$.post( ajaxurl, ajax_data, function(response) {
					$( 'tr.deleting').fadeOut( '300', function() {
						$( this ).remove();
					} );
				});
			}
		},
		reindexRows: function() {
			var loop = 0;
			$boxes.find( 'tr' ).each( function( index, row ) {
				$('input.text, input.checkbox, select.select', row ).each( function( i, el ) {
					var t = $(el);
					t.attr( 'name', t.attr('name').replace(/\[([^[]*)\]/, "[" + loop + "]" ) );
				});
				loop++;
			});
		}
	};

	wc_flat_rate_box_rows.init();

})( jQuery, woocommerce_shipping_flat_rate_box_rows, wp, ajaxurl );
