jQuery(
	function ( $ ) {

		//TinyMCE Button
		tinymce.create(
			'tinymce.plugins.ywctm_icons',
			{
				init: function ( editor ) {
					editor.addButton(
						'ywctm_icons',
						{
							//icon   : 'flag',
							text   : ywctm_btns.button_title,
							tooltip: ywctm_btns.button_title,
							onclick: show_dialog
						}
					);

					function show_dialog() {

						var window,
							icon_list = print_icons_list();

						window = editor.windowManager.open(
							{
								autoScroll: false,
								width     : 690,
								height    : 320,
								title     : ywctm_btns.dialog_title,
								spacing   : 20,
								padding   : 10,
								classes   : 'fontawesome-panel',
								items     : [ {
									type: 'container',
									html: icon_list
								} ],
								buttons   : [ {
									text   : ywctm_btns.close_label,
									onclick: function () {
										window.close();
									}
								} ]
							}
						);

						$( '#mce-modal-block' ).click(
							function () {
								window.close();
							}
						);

						$( '.mce-fontawesome-panel li' ).click(
							function () {
								var icon;

								switch ( $( this ).data( 'font' ) ) {
									case 'FontAwesome':
										icon = '{{fa fa-' + $( this ).data( 'name' ) + '}}';
										break;
									case 'Dashicons':
										icon = '{{dashicons dashicons-' + $( this ).data( 'name' ) + '}}';

										break;
									case 'retinaicon-font':
										icon = '{{retinaicon-font ' + $( this ).data( 'name' ) + '}}';
										break;
								}

								editor.execCommand( 'mceInsertContent', false, icon );
								window.close();

							}
						);

						function print_icons_list() {

							var icon_list = '<ul class="yit-icons-manager-list">';

							for ( var family in ywctm_btns.list ) {
								if ( ywctm_btns.list.hasOwnProperty( family ) ) {
									for ( var code in ywctm_btns.list[ family ] ) {
										if ( ywctm_btns.list[ family ].hasOwnProperty( code ) ) {
											var icon = code.replace( '\\', '&#x' );

											icon_list += '<li data-font="' + family + '" data-icon="' + icon + '" data-key="' + code + '" data-name="' + ywctm_btns.list[ family ][ code ] + '"></li>'
										}
									}
								}

							}

							icon_list += '</ul>';
							return icon_list;
						}

					}

				},

			}
		);

		tinymce.PluginManager.add( 'ywctm_icons', tinymce.plugins.ywctm_icons );

	}
);
