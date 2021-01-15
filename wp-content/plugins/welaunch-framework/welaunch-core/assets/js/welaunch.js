/* global welaunch, tinyMCE, ajaxurl */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.ajax_save = function( button ) {
		var $data;
		var $nonce;

		var overlay           = $( document.getElementById( 'welaunch_ajax_overlay' ) );
		var $notification_bar = $( document.getElementById( 'welaunch_notification_bar' ) );
		var $parent           = $( button ).parents( '.welaunch-wrap-div' ).find( 'form' ).first();

		overlay.fadeIn();

		// Add the loading mechanism.
		$( '.welaunch-action_bar .spinner' ).addClass( 'is-active' );
		$( '.welaunch-action_bar input' ).attr( 'disabled', 'disabled' );

		$notification_bar.slideUp();

		$( '.welaunch-save-warn' ).slideUp();
		$( '.welaunch_ajax_save_error' ).slideUp(
			'medium',
			function() {
				$( this ).remove();
			}
		);

		// Editor field doesn't auto save. Have to call it. Boo.
		if ( welaunch.optName.hasOwnProperty( 'editor' ) ) {
			$.each(
				welaunch.optName.editor,
				function( $key ) {
					var editor;

					if ( 'undefined' !== typeof ( tinyMCE ) ) {
						editor = tinyMCE.get( $key );

						if ( editor ) {
							editor.save();
						}
					}
				}
			);
		}

		$data = $parent.serialize();

		// Add values for checked and unchecked checkboxes fields.
		$parent.find( 'input[type=checkbox]' ).each(
			function() {
				var chkVal;

				if ( 'undefined' !== typeof $( this ).attr( 'name' ) ) {
					chkVal = $( this ).is( ':checked' ) ? $( this ).val() : '0';

					$data += '&' + $( this ).attr( 'name' ) + '=' + chkVal;
				}
			}
		);

		if ( 'welaunch_save' !== button.attr( 'name' ) ) {
			$data += '&' + button.attr( 'name' ) + '=' + button.val();
		}

		$nonce = $parent.attr( 'data-nonce' );

		$.ajax(
			{ type: 'post',
				dataType: 'json',
				url: ajaxurl,
				data: {
					action:     welaunch.optName.args.opt_name + '_ajax_save',
					nonce:      $nonce,
					'opt_name': welaunch.optName.args.opt_name,
					data:       $data
				},
				error: function( response ) {
					$( '.welaunch-action_bar input' ).removeAttr( 'disabled' );

					if ( true === welaunch.optName.args.dev_mode ) {
						console.log( response.responseText );

						overlay.fadeOut( 'fast' );
						$( '.welaunch-action_bar .spinner' ).removeClass( 'is-active' );
						alert( welaunch.optName.ajax.alert );
					} else {
						welaunch.optName.args.ajax_save = false;

						$( button ).click();
						$( '.welaunch-action_bar input' ).attr( 'disabled', 'disabled' );
					}
				},
				success: function( response ) {
					var $save_notice;

					if ( response.action && 'reload' === response.action ) {
						location.reload( true );
					} else if ( 'success' === response.status ) {
						$( '.welaunch-action_bar input' ).removeAttr( 'disabled' );
						overlay.fadeOut( 'fast' );
						$( '.welaunch-action_bar .spinner' ).removeClass( 'is-active' );
						welaunch.optName.options  = response.options;
						welaunch.optName.errors   = response.errors;
						welaunch.optName.warnings = response.warnings;
						welaunch.optName.sanitize = response.sanitize;

						$notification_bar.html( response.notification_bar ).slideDown( 'fast' );
						if ( null !== response.errors || null !== response.warnings ) {
							$.welaunch.notices();
						}

						if ( null !== response.sanitize ) {
							$.welaunch.sanitize();
						}

						$save_notice = $( document.getElementById( 'welaunch_notification_bar' ) ).find( '.saved_notice' );

						$save_notice.slideDown();
						$save_notice.delay( 4000 ).slideUp();
					} else {
						$( '.welaunch-action_bar input' ).removeAttr( 'disabled' );
						$( '.welaunch-action_bar .spinner' ).removeClass( 'is-active' );
						overlay.fadeOut( 'fast' );
						$( '.wrap h2:first' ).parent().append( '<div class="error welaunch_ajax_save_error" style="display:none;"><p>' + response.status + '</p></div>' );
						$( '.welaunch_ajax_save_error' ).slideDown();
						$( 'html, body' ).animate(
							{ scrollTop: 0 },
							'slow'
						);
					}
				}
			}
		);

		return false;
	};
})( jQuery );

/* jshint unused:false */

function colorValidate( field ) {
	'use strict';

	var value = jQuery( field ).val();

	var hex = colorNameToHex( value );
	if ( hex !== value.replace( '#', '' ) ) {
		return hex;
	}

	return value;
}

function colorNameToHex( colour ) {
	'use strict';

	var tcolour = colour.replace( /^\s\s*/, '' ).replace( /\s\s*$/, '' ).replace( '#', '' );

	var colours = {
		'aliceblue': '#f0f8ff',
		'antiquewhite': '#faebd7',
		'aqua': '#00ffff',
		'aquamarine': '#7fffd4',
		'azure': '#f0ffff',
		'beige': '#f5f5dc',
		'bisque': '#ffe4c4',
		'black': '#000000',
		'blanchedalmond': '#ffebcd',
		'blue': '#0000ff',
		'blueviolet': '#8a2be2',
		'brown': '#a52a2a',
		'burlywood': '#deb887',
		'cadetblue': '#5f9ea0',
		'chartreuse': '#7fff00',
		'chocolate': '#d2691e',
		'coral': '#ff7f50',
		'cornflowerblue': '#6495ed',
		'cornsilk': '#fff8dc',
		'crimson': '#dc143c',
		'cyan': '#00ffff',
		'darkblue': '#00008b',
		'darkcyan': '#008b8b',
		'darkgoldenrod': '#b8860b',
		'darkgray': '#a9a9a9',
		'darkgreen': '#006400',
		'darkkhaki': '#bdb76b',
		'darkmagenta': '#8b008b',
		'darkolivegreen': '#556b2f',
		'darkorange': '#ff8c00',
		'darkorchid': '#9932cc',
		'darkred': '#8b0000',
		'darksalmon': '#e9967a',
		'darkseagreen': '#8fbc8f',
		'darkslateblue': '#483d8b',
		'darkslategray': '#2f4f4f',
		'darkturquoise': '#00ced1',
		'darkviolet': '#9400d3',
		'deeppink': '#ff1493',
		'deepskyblue': '#00bfff',
		'dimgray': '#696969',
		'dodgerblue': '#1e90ff',
		'firebrick': '#b22222',
		'floralwhite': '#fffaf0',
		'forestgreen': '#228b22',
		'fuchsia': '#ff00ff',
		'gainsboro': '#dcdcdc',
		'ghostwhite': '#f8f8ff',
		'gold': '#ffd700',
		'goldenrod': '#daa520',
		'gray': '#808080',
		'green': '#008000',
		'greenyellow': '#adff2f',
		'honeydew': '#f0fff0',
		'hotpink': '#ff69b4',
		'indianred ': '#cd5c5c',
		'indigo ': '#4b0082',
		'ivory': '#fffff0',
		'khaki': '#f0e68c',
		'lavender': '#e6e6fa',
		'lavenderblush': '#fff0f5',
		'lawngreen': '#7cfc00',
		'lemonchiffon': '#fffacd',
		'lightblue': '#add8e6',
		'lightcoral': '#f08080',
		'lightcyan': '#e0ffff',
		'lightgoldenrodyellow': '#fafad2',
		'lightgrey': '#d3d3d3',
		'lightgreen': '#90ee90',
		'lightpink': '#ffb6c1',
		'lightsalmon': '#ffa07a',
		'lightseagreen': '#20b2aa',
		'lightskyblue': '#87cefa',
		'lightslategray': '#778899',
		'lightsteelblue': '#b0c4de',
		'lightyellow': '#ffffe0',
		'lime': '#00ff00',
		'limegreen': '#32cd32',
		'linen': '#faf0e6',
		'magenta': '#ff00ff',
		'maroon': '#800000',
		'mediumaquamarine': '#66cdaa',
		'mediumblue': '#0000cd',
		'mediumorchid': '#ba55d3',
		'mediumpurple': '#9370d8',
		'mediumseagreen': '#3cb371',
		'mediumslateblue': '#7b68ee',
		'mediumspringgreen': '#00fa9a',
		'mediumturquoise': '#48d1cc',
		'mediumvioletred': '#c71585',
		'midnightblue': '#191970',
		'mintcream': '#f5fffa',
		'mistyrose': '#ffe4e1',
		'moccasin': '#ffe4b5',
		'navajowhite': '#ffdead',
		'navy': '#000080',
		'oldlace': '#fdf5e6',
		'olive': '#808000',
		'olivedrab': '#6b8e23',
		'orange': '#ffa500',
		'orangered': '#ff4500',
		'orchid': '#da70d6',
		'palegoldenrod': '#eee8aa',
		'palegreen': '#98fb98',
		'paleturquoise': '#afeeee',
		'palevioletred': '#d87093',
		'papayawhip': '#ffefd5',
		'peachpuff': '#ffdab9',
		'peru': '#cd853f',
		'pink': '#ffc0cb',
		'plum': '#dda0dd',
		'powderblue': '#b0e0e6',
		'purple': '#800080',
		'red': '#ff0000',
		'welaunch': '#01a3e3',
		'rosybrown': '#bc8f8f',
		'royalblue': '#4169e1',
		'saddlebrown': '#8b4513',
		'salmon': '#fa8072',
		'sandybrown': '#f4a460',
		'seagreen': '#2e8b57',
		'seashell': '#fff5ee',
		'sienna': '#a0522d',
		'silver': '#c0c0c0',
		'skyblue': '#87ceeb',
		'slateblue': '#6a5acd',
		'slategray': '#708090',
		'snow': '#fffafa',
		'springgreen': '#00ff7f',
		'steelblue': '#4682b4',
		'tan': '#d2b48c',
		'teal': '#008080',
		'thistle': '#d8bfd8',
		'tomato': '#ff6347',
		'turquoise': '#40e0d0',
		'violet': '#ee82ee',
		'wheat': '#f5deb3',
		'white': '#ffffff',
		'whitesmoke': '#f5f5f5',
		'yellow': '#ffff00',
		'yellowgreen': '#9acd32'
	};

	if ( 'undefined' !== colours[tcolour.toLowerCase()] ) {
		return colours[tcolour.toLowerCase()];
	}

	return colour;
}

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.expandOptions = function( parent ) {
		var trigger = parent.find( '.expand_options' );
		var width   = parent.find( '.welaunch-sidebar' ).width() - 1;
		var id      = $( '.welaunch-group-menu .active a' ).data( 'rel' ) + '_section_group';

		if ( trigger.hasClass( 'expanded' ) ) {
			trigger.removeClass( 'expanded' );
			parent.find( '.welaunch-main' ).removeClass( 'expand' );

			parent.find( '.welaunch-sidebar' ).stop().animate(
				{ 'margin-left': '0px' },
				500
			);

			parent.find( '.welaunch-main' ).stop().animate(
				{ 'margin-left': width },
				500,
				function() {
					parent.find( '.welaunch-main' ).attr( 'style', '' );
				}
			);

			parent.find( '.welaunch-group-tab' ).each(
				function() {
					if ( $( this ).attr( 'id' ) !== id ) {
						$( this ).fadeOut( 'fast' );
					}
				}
			);

			// Show the only active one.
		} else {
			trigger.addClass( 'expanded' );
			parent.find( '.welaunch-main' ).addClass( 'expand' );

			parent.find( '.welaunch-sidebar' ).stop().animate(
				{ 'margin-left': - width - 113 },
				500
			);

			parent.find( '.welaunch-main' ).stop().animate(
				{ 'margin-left': '-1px' },
				500
			);

			parent.find( '.welaunch-group-tab' ).fadeIn(
				'medium',
				function() {
					$.welaunch.initFields();
				}
			);
		}

		return false;
	};
})( jQuery );

/* global welaunch, welaunch_change */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.initEvents = function( el ) {
		var stickyHeight;

		el.find( '.welaunch-presets-bar' ).on(
			'click',
			function() {
				window.onbeforeunload = null;
			}
		);

		// Customizer save hook.
		el.find( '#customize-save-button-wrapper #save' ).on(
			'click',
			function() {

			}
		);

		el.find( '#toplevel_page_' + welaunch.optName.args.slug + ' .wp-submenu a, #wp-admin-bar-' + welaunch.optName.args.slug + ' a.ab-item' ).click(
			function( e ) {
				var url;

				if ( ( el.find( '#toplevel_page_' + welaunch.optName.args.slug ).hasClass( 'wp-menu-open' ) ||
					$( this ).hasClass( 'ab-item' ) ) &&
					! $( this ).parents( 'ul.ab-submenu:first' ).hasClass( 'ab-sub-secondary' ) &&
					$( this ).attr( 'href' ).toLowerCase().indexOf( welaunch.optName.args.slug + '&tab=' ) >= 0 ) {

					url = $( this ).attr( 'href' ).split( '&tab=' );

					e.preventDefault();

					el.find( '#' + url[1] + '_section_group_li_a' ).click();

					$( this ).parents( 'ul:first' ).find( '.current' ).removeClass( 'current' );
					$( this ).addClass( 'current' );
					$( this ).parent().addClass( 'current' );

					return false;
				}
			}
		);

		// Save button clicked.
		el.find( '.welaunch-action_bar input, #welaunch-import-action input' ).on(
			'click',
			function( e ) {
				if ( $( this ).attr( 'name' ) === welaunch.optName.args.opt_name + '[defaults]' ) {

					// Defaults button clicked.
					if ( ! confirm( welaunch.optName.args.reset_confirm ) ) {
						return false;
					}
				} else if ( $( this ).attr( 'name' ) === welaunch.optName.args.opt_name + '[defaults-section]' ) {

					// Default section clicked.
					if ( ! confirm( welaunch.optName.args.reset_section_confirm ) ) {
						return false;
					}
				} else if ( 'import' === $( this ).attr( 'name' ) ) {
					if ( ! confirm( welaunch.optName.args.import_section_confirm ) ) {
						return false;
					}
				}

				window.onbeforeunload = null;

				if ( true === welaunch.optName.args.ajax_save ) {
					$.welaunch.ajax_save( $( this ) );
					e.preventDefault();
				} else {
					location.reload( true );
				}
			}
		);

		$( '.expand_options' ).click(
			function( e ) {
				var tab;

				var container = el;

				e.preventDefault();

				if ( $( container ).hasClass( 'fully-expanded' ) ) {
					$( container ).removeClass( 'fully-expanded' );

					tab = $.cookie( 'welaunch_current_tab_' + welaunch.optName.args.opt_name );

					el.find( '#' + tab + '_section_group' ).fadeIn(
						200,
						function() {
							if ( 0 !== el.find( '#welaunch-footer' ).length ) {
								$.welaunch.stickyInfo(); // Race condition fix.
							}

							$.welaunch.initFields();
						}
					);
				}

				$.welaunch.expandOptions( $( this ).parents( '.welaunch-container:first' ) );

				return false;
			}
		);

		if ( el.find( '.saved_notice' ).is( ':visible' ) ) {
			el.find( '.saved_notice' ).slideDown();
		}

		$( document.body ).on(
			'change',
			'.welaunch-field input, .welaunch-field textarea, .welaunch-field select',
			function() {
				if ( $( '.welaunch-container-typography select' ).hasClass( 'ignore-change' ) ) {
					return;
				}
				if ( ! $( this ).hasClass( 'noUpdate' ) && ! $( this ).hasClass( 'no-update' ) ) {
					welaunch_change( $( this ) );
				}
			}
		);

		stickyHeight = el.find( '#welaunch-footer' ).height();

		el.find( '#welaunch-sticky-padder' ).css(
			{ height: stickyHeight }
		);

		el.find( '#welaunch-footer-sticky' ).removeClass( 'hide' );

		if ( 0 !== el.find( '#welaunch-footer' ).length ) {
			$( window ).scroll(
				function() {
					$.welaunch.stickyInfo();
				}
			);

			$( window ).resize(
				function() {
					$.welaunch.stickyInfo();
				}
			);
		}

		el.find( '.saved_notice' ).delay( 4000 ).slideUp();
	};
})( jQuery );

/* global welaunch */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.initFields = function() {
		$( '.welaunch-group-tab:visible' ).find( '.welaunch-field-init:visible' ).each(
			function() {
				var tr;
				var th;

				var type = $( this ).attr( 'data-type' );

				if ( 'undefined' !== typeof welaunch.field_objects && welaunch.field_objects[type] && welaunch.field_objects[type] ) {
					welaunch.field_objects[type].init();
				}

				if ( 'undefined' !== typeof welaunch.field_objects.pro && ! $.isEmptyObject( welaunch.field_objects.pro[type] ) && welaunch.field_objects.pro[type] ) {
					welaunch.field_objects.pro[type].init();
				}

				if ( ! welaunch.customizer && $( this ).hasClass( 'welaunch_remove_th' ) ) {
					tr = $( this ).parents( 'tr:first' );
					th = tr.find( 'th:first' );

					if ( th.html() && th.html().length > 0 ) {
						$( this ).prepend( th.html() );
						$( this ).find( '.welaunch_field_th' ).css( 'padding', '0 0 10px 0' );
					}

					$( this ).parent().attr( 'colspan', '2' );

					th.remove();
				}
			}
		);
	};
})( jQuery );

/* global welaunch, document */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$( document ).ready(
		function() {
			var opt_name;
			var li;

			var tempArr = [];

			$.fn.isOnScreen = function() {
				var win;
				var viewport;
				var bounds;

				if ( ! window ) {
					return;
				}

				win = $( window );
				viewport = {
					top: win.scrollTop()
				};

				viewport.right = viewport.left + win.width();
				viewport.bottom = viewport.top + win.height();

				bounds = this.offset();

				bounds.right = bounds.left + this.outerWidth();
				bounds.bottom = bounds.top + this.outerHeight();

				return ( ! ( viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom ) );
			};

			$( 'fieldset.welaunch-container-divide' ).css( 'display', 'none' );

			// Weed out multiple instances of duplicate weLaunch instance.
			if ( welaunch.customizer ) {
				$( '.wp-full-overlay-sidebar' ).addClass( 'welaunch-container' );
			}

			$( '.welaunch-container' ).each(
				function() {
					opt_name = $.welaunch.getOptName( this );

					if ( $.inArray( opt_name, tempArr ) === -1 ) {
						tempArr.push( opt_name );
						$.welaunch.checkRequired( $( this ) );
						$.welaunch.initEvents( $( this ) );
					}
				}
			);

			$( '.welaunch-container' ).on(
				'click',
				function() {
					opt_name = $.welaunch.getOptName( this );
				}
			);

			if ( undefined !== welaunch.optName ) {
				$.welaunch.disableFields();
				$.welaunch.hideFields();
				$.welaunch.disableSections();
				$.welaunch.initQtip();
				$.welaunch.tabCheck();
				$.welaunch.notices();
			}
		}
	);

	$.welaunch.disableSections = function() {
		$( '.welaunch-group-tab' ).each(
			function() {
				if ( $( this ).hasClass( 'disabled' ) ) {
					$( this ).find( 'input, select, textarea' ).attr( 'name', '' );
				}
			}
		);
	};

	$.welaunch.disableFields = function() {
		$( 'label[for="welaunch_disable_field"]' ).each(
			function() {
				$( this ).parents( 'tr' ).find( 'fieldset:first' ).find( 'input, select, textarea' ).attr( 'name', '' );
			}
		);
	};

	$.welaunch.hideFields = function() {
		$( 'label[for="welaunch_hide_field"]' ).each(
			function() {
				var tr = $( this ).parent().parent();

				$( tr ).addClass( 'hidden' );
			}
		);
	};

	$.welaunch.getOptName = function( el ) {
		var metabox;
		var li;
		var optName;
		var item = $( el );

		if ( welaunch.customizer ) {
			optName = item.find( '.welaunch-customizer-opt-name' ).data( 'opt-name' );
		} else {
			optName = $( el ).parents( '.welaunch-wrap-div' ).data( 'opt-name' );
		}

		// Compatibility for metaboxes
		if ( undefined === optName ) {
			metabox = $( el ).parents( '.postbox' );
			if ( 0 === metabox.length ) {
				metabox = $( el ).parents( '.welaunch-metabox' );
			}
			if ( 0 !== metabox.length ) {
				optName = metabox.attr( 'id' ).replace( 'welaunch-', '' ).split( '-metabox-' )[0];
				if ( undefined === optName ) {
					optName = metabox.attr( 'class' )
					.replace( 'welaunch-metabox', '' )
					.replace( 'postbox', '' )
					.replace( 'welaunch-', '' )
					.replace( 'hide', '' )
					.replace( 'closed', '' )
					.trim();
				}
			} else {
				optName = $( '.welaunch-ajax-security' ).data( 'opt-name' );
			}
		}
		if ( undefined === optName ) {
			optName = $( el ).find( '.welaunch-form-wrapper' ).data( 'opt-name' );
		}

		// Shim, let's just get an opt_name shall we?!
		if ( undefined === optName ) {
			optName = welaunch.opt_names[0];
		}

		if ( undefined !== optName ) {
			welaunch.optName = window['welaunch_' + optName.replace( /\-/g, '_' )];
		}

		return optName;
	};

	$.welaunch.getSelector = function( selector, fieldType ) {
		if ( ! selector ) {
			selector = '.welaunch-container-' + fieldType + ':visible';
			if ( welaunch.customizer ) {
				selector = $( document ).find( '.control-section-welaunch.open' ).find( selector );
			} else {
				selector = $( document ).find( '.welaunch-group-tab:visible' ).find( selector );
			}
		}
		return selector;
	};
})( jQuery );

/* global welaunch */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.sanitize = function() {
		if ( welaunch.optName.sanitize && welaunch.optName.sanitize.sanitize ) {
			$.each(
				welaunch.optName.sanitize.sanitize,
				function( sectionID, sectionArray ) {
					sectionID = null;
					$.each(
						sectionArray.sanitize,
						function( key, value ) {
							$.welaunch.fixInput( key, value );
						}
					);
				}
			);
		}
	};

	$.welaunch.fixInput = function( key, value ) {
		var val;
		var input;
		var inputVal;
		var ul;
		var li;

		if ( 'multi_text' === value.type ) {
			ul = $( '#' + value.id + '-ul' );
			li = $( ul.find( 'li' ) );

			li.each(
				function() {
					input    = $( this ).find( 'input' );
					inputVal = input.val();

					if ( inputVal === value.old ) {
						input.val( value.current );
					}
				}
			);

			return;
		}

		input = $( 'input#' + value.id + '-' + key );

		if ( 0 === input.length ) {
			input = $( 'input#' + value.id );
		}

		if ( 0 === input.length ) {
			input = $( 'textarea#' + value.id + '-textarea' );
		}

		if ( input.length > 0 ) {
			val = '' === value.current ? value.default : value.current;

			$( input ).val( val );
		}
	};

	$.welaunch.notices = function() {
		if ( welaunch.optName.errors && welaunch.optName.errors.errors ) {
			$.each(
				welaunch.optName.errors.errors,
				function( sectionID, sectionArray ) {
					sectionID = null;
					$.each(
						sectionArray.errors,
						function( key, value ) {
							$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).addClass( 'welaunch-field-error' );
							if ( 0 === $( '#' + welaunch.optName.args.opt_name + '-' + value.id ).parent().find( '.welaunch-th-error' ).length ) {
								$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).append( '<div class="welaunch-th-error">' + value.msg + '</div>' );
							} else {
								$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).parent().find( '.welaunch-th-error' ).html( value.msg ).css( 'display', 'block' );
							}

							$.welaunch.fixInput( key, value );
						}
					);
				}
			);

			$( '.welaunch-container' ).each(
				function() {
					var totalErrors;

					var container = $( this );

					// Ajax cleanup.
					container.find( '.welaunch-menu-error' ).remove();

					totalErrors = container.find( '.welaunch-field-error' ).length;

					if ( totalErrors > 0 ) {
						container.find( '.welaunch-field-errors span' ).text( totalErrors );
						container.find( '.welaunch-field-errors' ).slideDown();
						container.find( '.welaunch-group-tab' ).each(
							function() {
								var sectionID;
								var subParent;

								var total = $( this ).find( '.welaunch-field-error' ).length;
								if ( total > 0 ) {
									sectionID = $( this ).attr( 'id' ).split( '_' );

									sectionID = sectionID[0];
									container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).prepend( '<span class="welaunch-menu-error">' + total + '</span>' );
									container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).addClass( 'hasError' );

									subParent = container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).parents( '.hasSubSections:first' );

									if ( subParent ) {
										subParent.find( '.welaunch-group-tab-link-a:first' ).addClass( 'hasError' );
									}
								}
							}
						);
					}
				}
			);
		}

		if ( welaunch.optName.warnings && welaunch.optName.warnings.warnings ) {
			$.each(
				welaunch.optName.warnings.warnings,
				function( sectionID, sectionArray ) {
					sectionID = null;
					$.each(
						sectionArray.warnings,
						function( key, value ) {
							$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).addClass( 'welaunch-field-warning' );

							if ( 0 === $( '#' + welaunch.optName.args.opt_name + '-' + value.id ).parent().find( '.welaunch-th-warning' ).length ) {
								$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).append( '<div class="welaunch-th-warning">' + value.msg + '</div>' );
							} else {
								$( '#' + welaunch.optName.args.opt_name + '-' + value.id ).parent().find( '.welaunch-th-warning' ).html( value.msg ).css( 'display', 'block' );
							}

							$.welaunch.fixInput( key, value );
						}
					);
				}
			);

			$( '.welaunch-container' ).each(
				function() {
					var sectionID;
					var subParent;
					var total;
					var totalWarnings;

					var container = $( this );

					// Ajax cleanup.
					container.find( '.welaunch-menu-warning' ).remove();

					totalWarnings = container.find( '.welaunch-field-warning' ).length;

					if ( totalWarnings > 0 ) {
						container.find( '.welaunch-field-warnings span' ).text( totalWarnings );
						container.find( '.welaunch-field-warnings' ).slideDown();
						container.find( '.welaunch-group-tab' ).each(
							function() {
								total = $( this ).find( '.welaunch-field-warning' ).length;

								if ( total > 0 ) {
									sectionID = $( this ).attr( 'id' ).split( '_' );

									sectionID = sectionID[0];
									container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).prepend( '<span class="welaunch-menu-warning">' + total + '</span>' );
									container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).addClass( 'hasWarning' );

									subParent = container.find( '.welaunch-group-tab-link-a[data-key="' + sectionID + '"]' ).parents( '.hasSubSections:first' );

									if ( subParent ) {
										subParent.find( '.welaunch-group-tab-link-a:first' ).addClass( 'hasWarning' );
									}
								}
							}
						);
					}
				}
			);
		}
	};
})( jQuery );

/* global welaunch */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.initQtip = function() {
		var classes;

		// Shadow.
		var shadow    = '';
		var tipShadow = welaunch.optName.args.hints.tip_style.shadow;

		// Color.
		var color    = '';
		var tipColor = welaunch.optName.args.hints.tip_style.color;

		// Rounded.
		var rounded    = '';
		var tipRounded = welaunch.optName.args.hints.tip_style.rounded;

		// Tip style.
		var style    = '';
		var tipStyle = welaunch.optName.args.hints.tip_style.style;

		// Get position data.
		var myPos = welaunch.optName.args.hints.tip_position.my;
		var atPos = welaunch.optName.args.hints.tip_position.at;

		// Tooltip trigger action.
		var showEvent = welaunch.optName.args.hints.tip_effect.show.event;
		var hideEvent = welaunch.optName.args.hints.tip_effect.hide.event;

		// Tip show effect.
		var tipShowEffect   = welaunch.optName.args.hints.tip_effect.show.effect;
		var tipShowDuration = welaunch.optName.args.hints.tip_effect.show.duration;

		// Tip hide effect.
		var tipHideEffect   = welaunch.optName.args.hints.tip_effect.hide.effect;
		var tipHideDuration = welaunch.optName.args.hints.tip_effect.hide.duration;

		if ( $().qtip ) {
			if ( true === tipShadow ) {
				shadow = 'qtip-shadow';
			}

			if ( '' !== tipColor ) {
				color = 'qtip-' + tipColor;
			}

			if ( true === tipRounded ) {
				rounded = 'qtip-rounded';
			}

			if ( '' !== tipStyle ) {
				style = 'qtip-' + tipStyle;
			}

			classes = shadow + ',' + color + ',' + rounded + ',' + style + ',welaunch-qtip';
			classes = classes.replace( /,/g, ' ' );

			// Gotta be lowercase, and in proper format.
			myPos = $.welaunch.verifyPos( myPos.toLowerCase(), true );
			atPos = $.welaunch.verifyPos( atPos.toLowerCase(), false );

			$( 'div.welaunch-dev-qtip' ).each(
				function() {
					$( this ).qtip(
						{
							content: {
								text: $( this ).attr( 'qtip-content' ),
								title: $( this ).attr( 'qtip-title' )
							}, show: {
								effect: function() {
									$( this ).slideDown( 500 );
								},
								event: 'mouseover'
							}, hide: {
								effect: function() {
									$( this ).slideUp( 500 );
								},
								event: 'mouseleave'
							}, style: {
								classes: 'qtip-shadow qtip-light'
							}, position: {
								my: 'top center',
								at: 'bottom center'
							}
						}
					);
				}
			);

			$( 'div.welaunch-hint-qtip' ).each(
				function() {
					$( this ).qtip(
						{
							content: {
								text: $( this ).attr( 'qtip-content' ),
								title: $( this ).attr( 'qtip-title' )
							}, show: {
								effect: function() {
									switch ( tipShowEffect ) {
										case 'slide':
											$( this ).slideDown( tipShowDuration );
											break;
										case 'fade':
											$( this ).fadeIn( tipShowDuration );
											break;
										default:
											$( this ).show();
											break;
									}
								},
								event: showEvent
							}, hide: {
								effect: function() {
									switch ( tipHideEffect ) {
										case 'slide':
											$( this ).slideUp( tipHideDuration );
											break;
										case 'fade':
											$( this ).fadeOut( tipHideDuration );
											break;
										default:
											$( this ).hide( tipHideDuration );
											break;
									}
								},
								event: hideEvent
							}, style: {
								classes: classes
							}, position: {
								my: myPos,
								at: atPos
							}
						}
					);
				}
			);

			$( 'input[qtip-content]' ).each(
				function() {
					$( this ).qtip(
						{
							content: {
								text: $( this ).attr( 'qtip-content' ),
								title: $( this ).attr( 'qtip-title' )
							},
							show: 'focus',
							hide: 'blur',
							style: classes,
							position: {
								my: myPos,
								at: atPos
							}
						}
					);
				}
			);
		}
	};

	$.welaunch.verifyPos = function( s, b ) {
		var split;
		var paramOne;
		var paramTwo;

		// Trim off spaces.
		s = s.replace( /^\s+|\s+$/gm, '' );

		// Position value is blank, set the default.
		if ( '' === s || - 1 === s.search( ' ' ) ) {
			if ( true === b ) {
				return 'top left';
			} else {
				return 'bottom right';
			}
		}

		// Split string into array.
		split = s.split( ' ' );

		// Evaluate first string.  Must be top, center, or bottom.
		paramOne = b ? 'top' : 'bottom';

		if ( 'top' === split[0] || 'center' === split[0] || 'bottom' === split[0] ) {
			paramOne = split[0];
		}

		// Evaluate second string.  Must be left, center, or right.
		paramTwo = b ? 'left' : 'right';

		if ( 'left' === split[1] || 'center' === split[1] || 'right' === split[1] ) {
			paramTwo = split[1];
		}

		return paramOne + ' ' + paramTwo;
	};
})( jQuery );

/* jshint unused:false */
/* global welaunch */

var confirmOnPageExit = function( e ) {

	// Return; // ONLY FOR DEBUGGING.
	// If we haven't been passed the event get the window.event.
	'use strict';

	var message;

	e = e || window.event;

	message = welaunch.optName.args.save_pending;

	// For IE6-8 and Firefox prior to version 4.
	if ( e ) {
		e.returnValue = message;
	}

	window.onbeforeunload = null;

	// For Chrome, Safari, IE8+ and Opera 12+.
	return message;
};

function welaunch_change( variable ) {
	'use strict';

	(function( $ ) {
		var rContainer;
		var opt_name;
		var parentID;
		var id;
		var th;
		var subParent;
		var errorCount;
		var errorsLeft;
		var warningCount;
		var warningsLeft;

		variable = $( variable );

		rContainer = $( variable ).parents( '.welaunch-container:first' );

		if ( welaunch.customizer ) {
			opt_name = $( '.welaunch-customizer-opt-name' ).data( 'opt-name' );
		} else {
			opt_name = $.welaunch.getOptName( rContainer );
		}

		$( 'body' ).trigger( 'check_dependencies', variable );

		if ( variable.hasClass( 'compiler' ) ) {
			$( '#welaunch-compiler-hook' ).val( 1 );
		}

		parentID = $( variable ).closest( '.welaunch-group-tab' ).attr( 'id' );

		// Let's count down the errors now. Fancy.  ;).
		id = parentID.split( '_' );

		id = id[0];

		th        = rContainer.find( '.welaunch-group-tab-link-a[data-key="' + id + '"]' ).parents( '.welaunch-group-tab-link-li:first' );
		subParent = $( '#' + parentID + '_li' ).parents( '.hasSubSections:first' );

		if ( $( variable ).parents( 'fieldset.welaunch-field:first' ).hasClass( 'welaunch-field-error' ) ) {
			$( variable ).parents( 'fieldset.welaunch-field:first' ).removeClass( 'welaunch-field-error' );
			$( variable ).parent().find( '.welaunch-th-error' ).slideUp();

			errorCount = ( parseInt( rContainer.find( '.welaunch-field-errors span' ).text(), 0 ) - 1 );

			if ( errorCount <= 0 ) {
				$( '#' + parentID + '_li .welaunch-menu-error' ).fadeOut( 'fast' ).remove();
				$( '#' + parentID + '_li .welaunch-group-tab-link-a' ).removeClass( 'hasError' );
				$( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.welaunch-field-errors' ).slideUp();
				$( variable ).parents( '.welaunch-container:first' ).find( '.welaunch-field-errors' ).slideUp();
				$( '#welaunch_metaboxes_errors' ).slideUp();
			} else {
				errorsLeft = ( parseInt( th.find( '.welaunch-menu-error:first' ).text(), 0 ) - 1 );

				if ( errorsLeft <= 0 ) {
					th.find( '.welaunch-menu-error:first' ).fadeOut().remove();
				} else {
					th.find( '.welaunch-menu-error:first' ).text( errorsLeft );
				}

				rContainer.find( '.welaunch-field-errors span' ).text( errorCount );
			}

			if ( 0 !== subParent.length ) {
				if ( 0 === subParent.find( '.welaunch-menu-error' ).length ) {
					subParent.find( '.hasError' ).removeClass( 'hasError' );
				}
			}
		}

		if ( $( variable ).parents( 'fieldset.welaunch-field:first' ).hasClass( 'welaunch-field-warning' ) ) {
			$( variable ).parents( 'fieldset.welaunch-field:first' ).removeClass( 'welaunch-field-warning' );
			$( variable ).parent().find( '.welaunch-th-warning' ).slideUp();

			warningCount = ( parseInt( rContainer.find( '.welaunch-field-warnings span' ).text(), 0 ) - 1 );

			if ( warningCount <= 0 ) {
				$( '#' + parentID + '_li .welaunch-menu-warning' ).fadeOut( 'fast' ).remove();
				$( '#' + parentID + '_li .welaunch-group-tab-link-a' ).removeClass( 'hasWarning' );
				$( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.welaunch-field-warnings' ).slideUp();
				$( variable ).parents( '.welaunch-container:first' ).find( '.welaunch-field-warnings' ).slideUp();
				$( '#welaunch_metaboxes_warnings' ).slideUp();
			} else {

				// Let's count down the warnings now. Fancy.  ;).
				warningsLeft = ( parseInt( th.find( '.welaunch-menu-warning:first' ).text(), 0 ) - 1 );

				if ( warningsLeft <= 0 ) {
					th.find( '.welaunch-menu-warning:first' ).fadeOut().remove();
				} else {
					th.find( '.welaunch-menu-warning:first' ).text( warningsLeft );
				}

				rContainer.find( '.welaunch-field-warning span' ).text( warningCount );
			}

			if ( 0 !== subParent.length ) {
				if ( 0 === subParent.find( '.welaunch-menu-warning' ).length ) {
					subParent.find( '.hasWarning' ).removeClass( 'hasWarning' );
				}
			}
		}

		// Don't show the changed value notice while save_notice is visible.
		if ( rContainer.find( '.saved_notice:visible' ).length > 0 ) {
			return;
		}

		if ( ! welaunch.optName.args.disable_save_warn ) {
			rContainer.find( '.welaunch-save-warn' ).slideDown();
			window.onbeforeunload = confirmOnPageExit;
		}
	})( jQuery );
}

/* jshint unused:false */

function welaunch_hook( object, functionName, callback, before ) {
	'use strict';

	(function( originalFunction ) {
		object[functionName] = function() {
			var returnValue;

			if ( true === before ) {
				callback.apply( this, [returnValue, originalFunction, arguments] );
			}

			returnValue = originalFunction.apply( this, arguments );

			if ( true !== before ) {
				callback.apply( this, [returnValue, originalFunction, arguments] );
			}

			return returnValue;
		};
	}( object[functionName] ) );
}

/* global welaunch */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.makeBoolStr = function( val ) {
		if ( 'false' === val || false === val || '0' === val || 0 === val || null === val || '' === val ) {
			return 'false';
		} else if ( 'true' === val || true === val || '1' === val || 1 === val ) {
			return 'true';
		} else {
			return val;
		}
	};

	$.welaunch.checkRequired = function( el ) {
		$.welaunch.required();

		$( 'body' ).on(
			'change',
			'.welaunch-main select, .welaunch-main radio, .welaunch-main input[type=checkbox], .welaunch-main input[type=hidden]',
			function() {
				$.welaunch.check_dependencies( this );
			}
		);

		$( 'body' ).on(
			'check_dependencies',
			function( e, variable ) {
				e = null;
				$.welaunch.check_dependencies( variable );
			}
		);

		if ( welaunch.customizer ) {
			el.find( '.customize-control.welaunch-field.hide' ).hide();
		}

		el.find( '.welaunch-container td > fieldset:empty,td > div:empty' ).parent().parent().hide();
	};

	$.welaunch.required = function() {

		// Hide the fold elements on load.
		// It's better to do this by PHP but there is no filter in tr tag , so is not possible
		// we going to move each attributes we may need for folding to tr tag.
		$.each(
			welaunch.opt_names,
			function( x ) {
				$.each(
					window['welaunch_' + welaunch.opt_names[x].replace( /\-/g, '_' )].folds,
					function( i, v ) {
						var div;
						var rawTable;

						var fieldset = $( '#' + welaunch.opt_names[x] + '-' + i );

						fieldset.parents( 'tr:first, li:first' ).addClass( 'fold' );

						if ( 'hide' === v ) {
							fieldset.parents( 'tr:first, li:first' ).addClass( 'hide' );

							if ( fieldset.hasClass( 'welaunch-container-section' ) ) {
								div = $( '#section-' + i );

								if ( div.hasClass( 'welaunch-section-indent-start' ) ) {
									$( '#section-table-' + i ).hide().addClass( 'hide' );
									div.hide().addClass( 'hide' );
								}
							}

							if ( fieldset.hasClass( 'welaunch-container-info' ) ) {
								$( '#info-' + i ).hide().addClass( 'hide' );
							}

							if ( fieldset.hasClass( 'welaunch-container-divide' ) ) {
								$( '#divide-' + i ).hide().addClass( 'hide' );
							}

							if ( fieldset.hasClass( 'welaunch-container-raw' ) ) {
								rawTable = fieldset.parents().find( 'table#' + welaunch.opt_names[x] + '-' + i );
								rawTable.hide().addClass( 'hide' );
							}
						}
					}
				);
			}
		);
	};

	$.welaunch.getContainerValue = function( id ) {
		var value = $( '#' + welaunch.optName.args.opt_name + '-' + id ).serializeForm();

		if ( null !== value && 'object' === typeof value && value.hasOwnProperty( welaunch.optName.args.opt_name ) ) {
			value = value[welaunch.optName.args.opt_name][id];
		}

		if ( $( '#' + welaunch.optName.args.opt_name + '-' + id ).hasClass( 'welaunch-container-media' ) ) {
			value = value.url;
		}

		return value;
	};

	$.welaunch.check_dependencies = function( variable ) {
		var current;
		var id;
		var container;
		var isHidden;

		if ( null === welaunch.optName.required ) {
			return;
		}

		current = $( variable );
		id      = current.parents( '.welaunch-field:first' ).data( 'id' );

		if ( ! welaunch.optName.required.hasOwnProperty( id ) ) {
			return;
		}

		container = current.parents( '.welaunch-field-container:first' );
		isHidden  = container.parents( 'tr:first' ).hasClass( 'hide' );

		if ( ! container.parents( 'tr:first' ).length ) {
			isHidden = container.parents( '.customize-control:first' ).hasClass( 'hide' );
		}

		$.each(
			welaunch.optName.required[id],
			function( child ) {
				var div;
				var rawTable;
				var tr;

				var current       = $( this );
				var show          = false;
				var childFieldset = $( '#' + welaunch.optName.args.opt_name + '-' + child );

				tr = childFieldset.parents( 'tr:first' );

				if ( 0 === tr.length ) {
					tr = childFieldset.parents( 'li:first' );
				}

				if ( ! isHidden ) {
					show = $.welaunch.check_parents_dependencies( child );
				}

				if ( true === show ) {

					// Shim for sections.
					if ( childFieldset.hasClass( 'welaunch-container-section' ) ) {
						div = $( '#section-' + child );

						if ( div.hasClass( 'welaunch-section-indent-start' ) && div.hasClass( 'hide' ) ) {
							$( '#section-table-' + child ).fadeIn( 300 ).removeClass( 'hide' );
							div.fadeIn( 300 ).removeClass( 'hide' );
						}
					}

					if ( childFieldset.hasClass( 'welaunch-container-info' ) ) {
						$( '#info-' + child ).fadeIn( 300 ).removeClass( 'hide' );
					}

					if ( childFieldset.hasClass( 'welaunch-container-divide' ) ) {
						$( '#divide-' + child ).fadeIn( 300 ).removeClass( 'hide' );
					}

					if ( childFieldset.hasClass( 'welaunch-container-raw' ) ) {
						rawTable = childFieldset.parents().find( 'table#' + welaunch.optName.args.opt_name + '-' + child );
						rawTable.fadeIn( 300 ).removeClass( 'hide' );
					}

					tr.fadeIn(
						300,
						function() {
							$( this ).removeClass( 'hide' );
							if ( welaunch.optName.required.hasOwnProperty( child ) ) {
								$.welaunch.check_dependencies( $( '#' + welaunch.optName.args.opt_name + '-' + child ).children().first() );
							}

							$.welaunch.initFields();
						}
					);

					if ( childFieldset.hasClass( 'welaunch-container-section' ) || childFieldset.hasClass( 'welaunch-container-info' ) ) {
						tr.css( { display: 'none' } );
					}
				} else if ( false === show ) {
					tr.fadeOut(
						100,
						function() {
							$( this ).addClass( 'hide' );
							if ( welaunch.optName.required.hasOwnProperty( child ) ) {
								$.welaunch.required_recursive_hide( child );
							}
						}
					);
				}

				current.find( 'select, radio, input[type=checkbox]' ).trigger( 'change' );
			}
		);
	};

	$.welaunch.required_recursive_hide = function( id ) {
		var div;
		var rawTable;
		var toFade;

		toFade = $( '#' + welaunch.optName.args.opt_name + '-' + id ).parents( 'tr:first' );
		if ( 0 === toFade ) {
			toFade = $( '#' + welaunch.optName.args.opt_name + '-' + id ).parents( 'li:first' );
		}

		toFade.fadeOut(
			50,
			function() {
				$( this ).addClass( 'hide' );

				if ( $( '#' + welaunch.optName.args.opt_name + '-' + id ).hasClass( 'welaunch-container-section' ) ) {
					div = $( '#section-' + id );

					if ( div.hasClass( 'welaunch-section-indent-start' ) ) {
						$( '#section-table-' + id ).fadeOut( 50 ).addClass( 'hide' );
						div.fadeOut( 50 ).addClass( 'hide' );
					}
				}

				if ( $( '#' + welaunch.optName.args.opt_name + '-' + id ).hasClass( 'welaunch-container-info' ) ) {
					$( '#info-' + id ).fadeOut( 50 ).addClass( 'hide' );
				}

				if ( $( '#' + welaunch.optName.args.opt_name + '-' + id ).hasClass( 'welaunch-container-divide' ) ) {
					$( '#divide-' + id ).fadeOut( 50 ).addClass( 'hide' );
				}

				if ( $( '#' + welaunch.optName.args.opt_name + '-' + id ).hasClass( 'welaunch-container-raw' ) ) {
					rawTable = $( '#' + welaunch.optName.args.opt_name + '-' + id ).parents().find( 'table#' + welaunch.optName.args.opt_name + '-' + id );
					rawTable.fadeOut( 50 ).addClass( 'hide' );
				}

				if ( welaunch.optName.required.hasOwnProperty( id ) ) {
					$.each(
						welaunch.optName.required[id],
						function( child ) {
							$.welaunch.required_recursive_hide( child );
						}
					);
				}
			}
		);
	};

	$.welaunch.check_parents_dependencies = function( id ) {
		var show = '';

		if ( welaunch.optName.required_child.hasOwnProperty( id ) ) {
			$.each(
				welaunch.optName.required_child[id],
				function( i, parentData ) {
					var parentValue;

					i = null;

					if ( $( '#' + welaunch.optName.args.opt_name + '-' + parentData.parent ).parents( 'tr:first' ).hasClass( 'hide' ) ) {
						show = false;
					} else if ( $( '#' + welaunch.optName.args.opt_name + '-' + parentData.parent ).parents( 'li:first' ).hasClass( 'hide' ) ) {
						show = false;
					} else {
						if ( false !== show ) {
							parentValue = $.welaunch.getContainerValue( parentData.parent );

							show = $.welaunch.check_dependencies_visibility( parentValue, parentData );
						}
					}
				}
			);
		} else {
			show = true;
		}

		return show;
	};

	$.welaunch.check_dependencies_visibility = function( parentValue, data ) {
		var show       = false;
		var checkValue = data.checkValue;
		var operation  = data.operation;
		var arr;

		if ( $.isPlainObject( parentValue ) ) {
			parentValue = Object.keys( parentValue ).map(
				function( key ) {
					return [key, parentValue[key]];
				}
			);
		}

		switch ( operation ) {
			case '=':
			case 'equals':
				if ( $.isArray( parentValue ) ) {
					$( parentValue[0] ).each(
						function( idx, val ) {
							idx = null;

							if ( $.isArray( checkValue ) ) {
								$( checkValue ).each(
									function( i, v ) {
										i = null;
										if ( $.welaunch.makeBoolStr( val ) === $.welaunch.makeBoolStr( v ) ) {
											show = true;

											return true;
										}
									}
								);
							} else {
								if ( $.welaunch.makeBoolStr( val ) === $.welaunch.makeBoolStr( checkValue ) ) {
									show = true;

									return true;
								}
							}
						}
					);
				} else {
					if ( $.isArray( checkValue ) ) {
						$( checkValue ).each(
							function( i, v ) {
								i = null;

								if ( $.welaunch.makeBoolStr( parentValue ) === $.welaunch.makeBoolStr( v ) ) {
									show = true;
								}
							}
						);
					} else {
						if ( $.welaunch.makeBoolStr( parentValue ) === $.welaunch.makeBoolStr( checkValue ) ) {
							show = true;
						}
					}
				}
				break;

			case '!=':
			case 'not':
				if ( $.isArray( parentValue ) ) {
					$( parentValue[0] ).each(
						function( idx, val ) {
							idx = null;

							if ( $.isArray( checkValue ) ) {
								$( checkValue ).each(
									function( i, v ) {
										i = null;

										if ( $.welaunch.makeBoolStr( val ) !== $.welaunch.makeBoolStr( v ) ) {
											show = true;

											return true;
										}
									}
								);
							} else {
								if ( $.welaunch.makeBoolStr( val ) !== $.welaunch.makeBoolStr( checkValue ) ) {
									show = true;

									return true;
								}
							}
						}
					);
				} else {
					if ( $.isArray( checkValue ) ) {
						$( checkValue ).each(
							function( i, v ) {
								i = null;

								if ( $.welaunch.makeBoolStr( parentValue ) !== $.welaunch.makeBoolStr( v ) ) {
									show = true;
								}
							}
						);
					} else {
						if ( $.welaunch.makeBoolStr( parentValue ) !== $.welaunch.makeBoolStr( checkValue ) ) {
							show = true;
						}
					}
				}
				break;

			case '>':
			case 'greater':
			case 'is_larger':
				if ( parseFloat( parentValue ) > parseFloat( checkValue ) ) {
					show = true;
				}
				break;

			case '>=':
			case 'greater_equal':
			case 'is_larger_equal':
				if ( parseFloat( parentValue ) >= parseFloat( checkValue ) ) {
					show = true;
				}
				break;

			case '<':
			case 'less':
			case 'is_smaller':
				if ( parseFloat( parentValue ) < parseFloat( checkValue ) ) {
					show = true;
				}
				break;

			case '<=':
			case 'less_equal':
			case 'is_smaller_equal':
				if ( parseFloat( parentValue ) <= parseFloat( checkValue ) ) {
					show = true;
				}
				break;

			case 'contains':
				if ( $.isPlainObject( parentValue ) ) {
					parentValue = Object.keys( parentValue ).map(
						function( key ) {
							return [key, parentValue[key]];
						}
					);
				}

				if ( $.isPlainObject( checkValue ) ) {
					checkValue = Object.keys( checkValue ).map(
						function( key ) {
							return [key, checkValue[key]];
						}
					);
				}

				if ( $.isArray( checkValue ) ) {
					$( checkValue ).each(
						function( idx, val ) {
							var breakMe = false;
							var toFind  = val[0];
							var findVal = val[1];

							idx = null;

							$( parentValue ).each(
								function( i, v ) {
									var toMatch  = v[0];
									var matchVal = v[1];

									i = null;

									if ( toFind === toMatch ) {
										if ( findVal === matchVal ) {
											show    = true;
											breakMe = true;

											return false;
										}
									}
								}
							);

							if ( true === breakMe ) {
								return false;
							}
						}
					);
				} else {
					if ( parentValue.toString().indexOf( checkValue ) !== - 1 ) {
						show = true;
					}
				}
				break;

			case 'doesnt_contain':
			case 'not_contain':
				if ( $.isPlainObject( parentValue ) ) {
					arr = Object.keys( parentValue ).map(
						function( key ) {
							return parentValue[key];
						}
					);

					parentValue = arr;
				}

				if ( $.isPlainObject( checkValue ) ) {
					arr = Object.keys( checkValue ).map(
						function( key ) {
							return checkValue[key];
						}
					);

					checkValue = arr;
				}

				if ( $.isArray( checkValue ) ) {
					$( checkValue ).each(
						function( idx, val ) {
							idx = null;

							if ( parentValue.toString().indexOf( val ) === - 1 ) {
								show = true;
							}
						}
					);
				} else {
					if ( parentValue.toString().indexOf( checkValue ) === - 1 ) {
						show = true;
					}
				}
				break;

			case 'is_empty_or':
				if ( '' === parentValue || checkValue === parentValue ) {
					show = true;
				}
				break;

			case 'not_empty_and':
				if ( '' !== parentValue && checkValue !== parentValue ) {
					show = true;
				}
				break;

			case 'is_empty':
			case 'empty':
			case '!isset':
				if ( ! parentValue || '' === parentValue || null === parentValue ) {
					show = true;
				}
				break;

			case 'not_empty':
			case '!empty':
			case 'isset':
				if ( parentValue && '' !== parentValue && null !== parentValue ) {
					show = true;
				}
				break;
		}

		return show;
	};
})( jQuery );

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.stickyInfo = function() {
		var stickyWidth = $( '.welaunch-main' ).innerWidth() - 20;
		var $width      = $( '#welaunch-sticky' ).offset().left;

		$( '.welaunch-save-warn' ).css( 'left', $width + 'px' );

		if ( ! $( '#info_bar' ).isOnScreen() && ! $( '#welaunch-footer-sticky' ).isOnScreen() ) {
			$( '#welaunch-footer' ).css(
				{ position: 'fixed', bottom: '0', width: stickyWidth, right: 21 }
			);

			$( '#welaunch-footer' ).addClass( 'sticky-footer-fixed' );
			$( '#welaunch-sticky-padder' ).show();
		} else {
			$( '#welaunch-footer' ).css(
				{ background: '#eee', position: 'inherit', bottom: 'inherit', width: 'inherit' }
			);

			$( '#welaunch-sticky-padder' ).hide();
			$( '#welaunch-footer' ).removeClass( 'sticky-footer-fixed' );
		}
		if ( ! $( '#info_bar' ).isOnScreen() ) {
			$( '#welaunch-sticky' ).addClass( 'sticky-save-warn' );
		} else {
			$( '#welaunch-sticky' ).removeClass( 'sticky-save-warn' );
		}
	};
})( jQuery );

/* global welaunch */

(function( $ ) {
	'use strict';

	$.welaunch = $.welaunch || {};

	$.welaunch.tabCheck = function() {
		var link;
		var tab;
		var sTab;
		var cookieName;
		var opt_name;

		$( '.welaunch-group-tab-link-a' ).click(
			function() {
				var elements;
				var index;
				var el;
				var relid;
				var oldid;
				var cookieName;
				var boxIndex;
				var parentID;
				var newParent;

				link = $( this );

				if ( link.parent().hasClass( 'empty_section' ) && link.parent().hasClass( 'hasSubSections' ) ) {
					elements = $( this ).closest( 'ul' ).find( '.welaunch-group-tab-link-a' );
					index    = elements.index( this );

					link = elements.slice( index + 1, index + 2 );
				}

				el    = link.parents( '.welaunch-container:first' );
				relid = link.data( 'rel' ); // The group ID of interest.
				oldid = el.find( '.welaunch-group-tab-link-li.active:first .welaunch-group-tab-link-a' ).data( 'rel' );
				opt_name = $.welaunch.getOptName( el );

				if ( oldid === relid ) {
					return;
				}

				cookieName = '';

				if ( ! link.parents( '.postbox-container:first' ).length ) {
					$( '#currentSection' ).val( relid );

					cookieName = 'welaunch_current_tab_' + welaunch.optName.args.opt_name;
				} else {
					el.prev( '#currentSection' ).val( relid );

					boxIndex = el.data( 'index' );

					if ( '' !== boxIndex ) {
						cookieName = 'welaunch_metabox_' + boxIndex + '_current_tab_' + welaunch.optName.args.opt_name;
					}
				}

				// Set the proper page cookie.
				$.cookie(
					cookieName,
					relid,
					{
						expires: 7,
						path: '/'
					}
				);

				if ( el.find( '#' + relid + '_section_group_li' ).parents( '.welaunch-group-tab-link-li' ).length ) {
					parentID = el.find( '#' + relid + '_section_group_li' ).parents( '.welaunch-group-tab-link-li' ).attr( 'id' ).split( '_' );
					parentID = parentID[0];
				}

				el.find( '#toplevel_page_' + welaunch.optName.args.slug + ' .wp-submenu a.current' ).removeClass( 'current' );
				el.find( '#toplevel_page_' + welaunch.optName.args.slug + ' .wp-submenu li.current' ).removeClass( 'current' );

				el.find( '#toplevel_page_' + welaunch.optName.args.slug + ' .wp-submenu a' ).each(
					function() {
						var url = $( this ).attr( 'href' ).split( '&tab=' );

						if ( url[1] === relid || url[1] === parentID ) {
							$( this ).addClass( 'current' );
							$( this ).parent().addClass( 'current' );
						}
					}
				);

				if ( el.find( '#' + oldid + '_section_group_li' ).find( '#' + oldid + '_section_group_li' ).length ) {
					el.find( '#' + oldid + '_section_group_li' ).addClass( 'activeChild' );
					el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' );
				} else if ( el.find( '#' + relid + '_section_group_li' ).parents( '#' + oldid + '_section_group_li' ).length || el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).find( '#' + relid + '_section_group_li' ).length ) {
					if ( el.find( '#' + relid + '_section_group_li' ).parents( '#' + oldid + '_section_group_li' ).length ) {
						el.find( '#' + oldid + '_section_group_li' ).addClass( 'activeChild' ).removeClass( 'active' );
					} else {
						el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
						el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
					}
					el.find( '#' + relid + '_section_group_li' ).removeClass( 'activeChild' ).addClass( 'active' );
				} else {
					setTimeout(
						function() {
							el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
						},
						1
					);

					if ( el.find( '#' + oldid + '_section_group_li' ).find( 'ul.subsection' ).length ) {
						el.find( '#' + oldid + '_section_group_li' ).find( 'ul.subsection' ).slideUp(
							'fast',
							function() {
								el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' ).removeClass( 'activeChild' );
							}
						);

						newParent = el.find( '#' + relid + '_section_group_li' ).parents( '.hasSubSections:first' );

						if ( newParent.length > 0 ) {
							el.find( '#' + relid + '_section_group_li' ).removeClass( 'active' );
							relid = newParent.find( '.welaunch-group-tab-link-a:first' ).data( 'rel' );

							if ( newParent.hasClass( 'empty_section' ) ) {
								newParent.find( '.subsection li:first' ).addClass( 'active' );
								el.find( '#' + relid + '_section_group_li' ).removeClass( 'active' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
								newParent = newParent.find( '.subsection li:first' );
								relid     = newParent.find( '.welaunch-group-tab-link-a:first' ).data( 'rel' );
							} else {
								el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
							}
						}
					} else if ( el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).length ) {
						if ( ! el.find( '#' + oldid + '_section_group_li' ).parents( '#' + relid + '_section_group_li' ).length ) {
							el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).slideUp(
								'fast',
								function() {
									el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
									el.find( '#' + oldid + '_section_group_li' ).parents( '.welaunch-group-tab-link-li' ).removeClass( 'active' ).removeClass( 'activeChild' );
									el.find( '#' + relid + '_section_group_li' ).parents( '.welaunch-group-tab-link-li' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
									el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
								}
							);
						} else {
							el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
						}
					} else {
						el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );

						if ( el.find( '#' + relid + '_section_group_li' ).parents( '.welaunch-group-tab-link-li' ).length ) {
							setTimeout(
								function() {
									el.find( '#' + relid + '_section_group_li' ).parents( '.welaunch-group-tab-link-li' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
								},
								50
							);

							el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
						}
					}
				}

				// Show the group.
				el.find( '#' + oldid + '_section_group' ).hide();

				el.find( '#' + relid + '_section_group' ).fadeIn(
					200,
					function() {
						if ( 0 !== el.find( '#welaunch-footer' ).length ) {
							$.welaunch.stickyInfo(); // Race condition fix.
						}

						$.welaunch.initFields();
					}
				);

				$( '#toplevel_page_' + welaunch.optName.args.slug ).find( '.current' ).removeClass( 'current' );
			}
		);

		if ( undefined !== welaunch.optName.last_tab ) {
			$( '#' + welaunch.optName.last_tab + '_section_group_li_a' ).click();

			return;
		}

		tab = decodeURI( ( new RegExp( 'tab=(.+?)(&|$)' ).exec( location.search ) || [''])[1] );

		if ( '' !== tab ) {
			if ( $.cookie( 'welaunch_current_tab_get' ) !== tab ) {
				$.cookie(
					'welaunch_current_tab',
					tab,
					{
						expires: 7,
						path: '/'
					}
				);

				$.cookie(
					'welaunch_current_tab_get',
					tab,
					{
						expires: 7,
						path: '/'
					}
				);

				$.cookie(
					'welaunch_current_tab_' + welaunch.optName.args.opt_name,
					tab,
					{
						expires: 7,
						path: '/'
					}
				);

				$( '#' + tab + '_section_group_li' ).click();
			}
		} else if ( '' !== $.cookie( 'welaunch_current_tab_get' ) ) {
			$.removeCookie( 'welaunch_current_tab_get' );
		}

		$( '.welaunch-container' ).each(
			function() {
				var boxIndex;

				if ( ! $( this ).parents( '.postbox-container:first' ).length ) {
					opt_name = $( '.welaunch-ajax-security' ).data( 'opt-name' );

					cookieName = 'welaunch_current_tab_' + opt_name;

					sTab = $( this ).find( '#' + $.cookie( cookieName ) + '_section_group_li_a' );
				} else {
					opt_name = $.welaunch.getOptName( this );

					boxIndex = $( this ).data( 'index' );

					if ( '' === boxIndex ) {
						boxIndex = 0;
					}

					cookieName = 'welaunch_metabox_' + boxIndex + '_current_tab_' + opt_name;

					sTab = $( this ).find( '#' + $.cookie( cookieName ) + '_section_group_li_a' );
				}

				// Tab the first item or the saved one.
				if ( null === $.cookie( cookieName ) || 'undefined' === typeof ( $.cookie( cookieName ) ) || 0 === sTab.length ) {
					$( this ).find( '.welaunch-group-tab-link-a:first' ).click();
				} else {
					sTab.click();
				}
			}
		);
	};
})( jQuery );
