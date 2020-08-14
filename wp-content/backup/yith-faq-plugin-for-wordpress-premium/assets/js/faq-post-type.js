jQuery(
	function ( $ ) {

		$( '.column-enable .on_off' ).change(
			function () {

				var container = $( this ).parent();
				var data      = {
					action : 'yfwp_enable_switch',
					faq_id : $( this ).attr( 'id' ).replace( 'enable_', '' ),
					enabled: $( this ).val()
				};

				container.addClass( 'processing' );
				container.block(
					{
						message   : null,
						overlayCSS: {
							background: '#fff',
							opacity   : 0.6
						}
					}
				);

				$.post(
					yith_faq_post_type.ajax_url,
					data,
					function () {

						container
							.removeClass( 'processing' )
							.unblock();

					}
				);

			}
		);

		if ( ! yith_faq_post_type.is_order_by ) {

			$( 'table.posts #the-list, table.pages #the-list' ).sortable(
				{
					'items' : 'tr',
					'axis'  : 'y',
					'helper': function ( e, ui ) {
						ui.children().children().each(
							function () {
								$( this ).width( $( this ).width() );
							}
						);
						return ui;
					},
					'update': function () {
						$.post(
							yith_faq_post_type.ajax_url,
							{
								action: 'yfwp_order_faqs',
								order : $( '#the-list' ).sortable( 'serialize' )
							}
						);
					}
				}
			);

		}

	}
);
