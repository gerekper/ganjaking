jQuery(
	function ( $ ) {

		$( document ).ready(
			function () {
				if ( window.location.href.indexOf( 'tab=color' ) === -1 ) {
					$( '.yit-admin-panel-content-wrap' ).find( 'input[type=submit]' ).remove();
				}
			}
		);

		$( document ).on(
			'change',
			'#yit_faq_options_enable_search_box, #yit_faq_options_enable_category_filter, #yit_faq_options_page_size, #yit_faq_options_icon_size, #yit_faq_options_style, #yit_faq_options_show_icon, #yit_faq_options_categories',
			function () {
				add_parameter()
			}
		);

		$( document ).on(
			'click',
			'.yit-icons-manager-wrapper',
			function () {
				add_parameter()
			}
		);

		$( document ).on(
			'change keyup input keydown keypress click mousemove',
			'.ui-slider-horizontal',
			function () {
				add_parameter()
			}
		);

		$( document ).on(
			'change',
			'#yit_faq_options_show_icon',
			function () {

				if ( $( 'input[name="yit_faq_options[show_icon]"]:checked' ).val() === 'off' || $( 'input[name="yit_faq_options[style]"]:checked' ).val() === 'list' ) {
					$( '#yit_faq_options_icon_size-container' ).closest( 'tr' ).hide();
					$( '#yit_faq_options_icon-container' ).closest( 'tr' ).hide();
				} else {
					$( '#yit_faq_options_icon_size-container' ).closest( 'tr' ).show( 500 );
					$( '#yit_faq_options_icon-container' ).closest( 'tr' ).show( 500 );
				}

			}
		);

		$( document ).on(
			'change',
			'#yit_faq_options_style',
			function () {

				if ( $( 'input[name="yit_faq_options[style]"]:checked' ).val() === 'list' ) {
					$( '#yit_faq_options_show_icon-container' ).closest( 'tr' ).hide();
					$( '#yit_faq_options_icon_size-container' ).closest( 'tr' ).hide();
					$( '#yit_faq_options_icon-container' ).closest( 'tr' ).hide();
				} else {
					$( '#yit_faq_options_show_icon-container' ).closest( 'tr' ).show( 500 );
					$( '#yit_faq_options_show_icon' ).trigger( 'change' )

				}

			}
		);

		function add_parameter() {

			var shortcode        = '[yith_faq]',
				search_box       = $( '#yit_faq_options_enable_search_box' ).val(),
				category_filters = $( '#yit_faq_options_enable_category_filter' ).val(),
				choose_style     = $( 'input[name="yit_faq_options[style]"]:checked' ).val(),
				page_size        = $( '#yit_faq_options_page_size' ).val(),
				categories       = $( '#yit_faq_options_categories' ).val(),
				show_icon        = $( 'input[name="yit_faq_options[show_icon]"]:checked' ).val(),
				icon_size        = $( '#yit_faq_options_icon_size' ).val(),
				icon             = $( '#yit_faq_options_icon' ).val(),
				args             = [];

			if ( search_box === 'yes' ) {
				args.push( 'search_box="on"' );
			}

			if ( category_filters === 'yes' ) {
				args.push( 'category_filters="on"' );
			}

			if ( choose_style !== 'list' ) {
				args.push( 'style="' + choose_style + '"' );
			}

			if ( categories !== null && categories.length > 0 ) {
				args.push( 'categories="' + categories.join( ',' ) + '"' );
			}

			if ( page_size !== '10' ) {
				args.push( 'page_size="' + page_size + '"' );
			}

			if ( show_icon !== 'off' && choose_style !== 'list' ) {
				args.push( 'show_icon="' + show_icon + '"' );
			}

			if ( icon_size !== '14' && show_icon !== 'off' ) {
				args.push( 'icon_size="' + icon_size + '"' );
			}

			if ( icon !== 'yfwp:plus' && show_icon !== 'off' ) {
				args.push( 'icon="' + icon + '"' );
			}

			if ( typeof args !== 'undefined' && args.length > 0 ) {
				shortcode = '[yith_faq ' + args.join( ' ' ) + ']';
			}

			$( '#yit_faq_options_shortcode' ).val( shortcode );

		}

	}
);
