/**
 * FAQ Post type scripts
 *
 * @package YITH\FAQPluginForWordPress\Assets\JS
 */

jQuery(
	function ( $ ) {

		$( '.column-enable .on_off' ).on(
			'change',
			function () {

				var container = $( this ).parent(),
					faq_id    = $( this ).attr( 'id' ).replace( 'enable_', '' );

				container
					.addClass( 'processing' )
					.block(
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
					{
						action : 'yfwp_enable_switch',
						faq_id : faq_id,
						enabled: $( this ).val(),
						nonce  : $( '#nonce_enable_' + faq_id ).val()
					},
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
								order : $( '#the-list' ).sortable( 'serialize' ),
								nonce : $( '#_wpnonce' ).val()
							}
						);
					}
				}
			);
		} else {
			$( '.column-drag' ).hide();
		}

	}
);
