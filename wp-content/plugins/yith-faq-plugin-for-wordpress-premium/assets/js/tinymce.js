jQuery(
	function ( $ ) {

		tinymce.create(
			'tinymce.plugins.yfwp_shortcode',
			{
				init: function ( editor ) {
					editor.addButton(
						'yfwp_shortcode',
						{
							icon   : 'yfwp_shortcode',
							tooltip: yfwp_shortcode.title,
							onclick: show_dialog
						}
					);

				},

			}
		);

		tinymce.PluginManager.add( 'yfwp_shortcode', tinymce.plugins.yfwp_shortcode );

		function show_dialog() {

			var window = tinymce.activeEditor.windowManager.open(
				{
					autoScroll: false,
					width     : 850,
					height    : 600,
					title     : yfwp_shortcode.title,
					spacing   : 0,
					padding   : 0,
					classes   : 'faq-panel',
					items     : [ {
						type: 'container',
						html: yfwp_shortcode.content
					} ],
					buttons   : [ {
						text   : yfwp_shortcode.insert_btn_text,
						classes: 'button-primary',
						onclick: function () {
							tinymce.activeEditor.execCommand( 'mceInsertContent', false, $( '#yit_faq_options_shortcode' ).val() );
							window.close();
						}
					}, {
						text   : yfwp_shortcode.close_btn_text,
						classes: 'button-secondary',
						onclick: function () {
							window.close();
						}
					} ]
				}
			);

			$( document.body )
				.trigger( 'yith-framework-enhanced-select-init' )
				.trigger( 'yith_fields_init' )
				.trigger( 'init_deps' );

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

			$( '#yit_faq_options_style' ).trigger( 'change' );

			$( '#mce-modal-block' ).click(
				function () {
					window.close();
				}
			);
		}

		if ( window.QTags !== undefined ) {

			QTags.addButton(
				'yfwp_shortcode',
				yfwp_shortcode.title,
				function () {
					show_dialog();
				}
			);

		}

	}
);
