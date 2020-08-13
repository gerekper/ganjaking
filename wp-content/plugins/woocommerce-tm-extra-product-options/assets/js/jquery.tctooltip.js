/**
 * jquery.tctooltip.js
 *
 * @version: v1.0
 * @author: themeComplete
 *
 * Created by themeComplete
 *
 * Copyright (c) 2019 themeComplete http://themecomplete.com
 */
( function( window, $ ) {
	'use strict';

	var ToolTip = function( dom, options ) {
		this.targets = $( dom );

		this.settings = $.extend( {}, $.fn.tcToolTip.defaults, options );

		if ( this.targets.length > 0 ) {
			this.init();
			return this;
		}

		return false;
	};

	var TMEPOJS;

	$( document ).ready( function() {
		TMEPOJS = window.TMEPOJS || { tm_epo_global_tooltip_max_width: '340px' };
	} );

	ToolTip.prototype = {
		constructor: ToolTip,

		removeTooltip: function( target, tooltip ) {
			var settings = this.settings;

			if ( target.data( 'is_moving' ) ) {
				return;
			}

			tooltip.removeClass( settings.fadin ).addClass( settings.fadeout );

			tooltip.animate(
				{
					opacity: 0
				},
				settings.speed,
				function() {
					$( this ).remove();
				}
			);

			if ( target.data( 'tmtip-title' ) && target.data( 'tm-tip-html' ) === undefined && ! target.attr( 'data-tm-tooltip-html' ) ) {
				target.attr( 'title', target.data( 'tmtip-title' ) );
			}

			$( window ).off( 'scroll.tcToolTip resize.tcToolTip' );
		},

		initTooltip: function( target, tooltip, nofx ) {
			var settings = this.settings;
			var tip;
			var scroll;
			var pos_left;
			var pos_top;
			var pos_from_top;
			var original_pos_left;

			if ( target && tooltip && target.length === 1 && tooltip.length === 1 && target.data( 'tm-has-tm-tip' ) === 1 ) {
				if ( nofx === 1 ) {
					if ( target.data( 'tm-tip-html' ) !== undefined ) {
						tip = target.data( 'tm-tip-html' );
					} else if ( target.attr( 'data-tm-tooltip-html' ) ) {
						tip = target.attr( 'data-tm-tooltip-html' );
					} else {
						tip = target.attr( 'title' );
					}

					tooltip.html( tip );
					target.data( 'is_moving', true );
				}

				tooltip.find( 'aside' ).hide();

				if ( TMEPOJS.tm_epo_global_tooltip_max_width === '' ) {
					// 50: average scrollbar width. Needed to avoid flickering width issues on mobile.
					if ( $( window ).width() <= tooltip.outerWidth() * 1.2 ) {
						tooltip.css( 'max-width', ( $( window ).width() / 1.2 ) + 'px' );
					} else {
						tooltip.css( 'max-width', '340px' );
					}
				} else {
					if ( TMEPOJS.tm_epo_global_tooltip_max_width.isNumeric() ) {
						TMEPOJS.tm_epo_global_tooltip_max_width = TMEPOJS.tm_epo_global_tooltip_max_width + 'px';
					}
					tooltip.css( 'max-width', TMEPOJS.tm_epo_global_tooltip_max_width );
				}

				tooltip.find( 'aside' ).show();

				scroll = $.epoAPI.dom.scroll();
				pos_left = target.offset().left + ( target.outerWidth() / 2 ) - ( tooltip.outerWidth() / 2 );
				pos_top = target.offset().top - tooltip.outerHeight() - 10;
				pos_from_top = target.offset().top - scroll.top - tooltip.outerHeight() - 10;
				original_pos_left = pos_left;

				if ( pos_left < 0 ) {
					pos_left = target.offset().left + ( target.outerWidth() / 2 ) - 20;
					tooltip.addClass( 'left' );
				} else {
					tooltip.removeClass( 'left' );
				}
				if ( original_pos_left >= 0 && pos_left + tooltip.outerWidth() > $( window ).width() ) {
					pos_left = target.offset().left - tooltip.outerWidth() + ( target.outerWidth() / 2 ) + 20;
					tooltip.addClass( 'right' );
				} else {
					tooltip.removeClass( 'right' );
				}
				if ( pos_top < 0 || pos_from_top < 0 ) {
					pos_top = target.offset().top + target.outerHeight();
					tooltip.addClass( 'top' );
				} else {
					tooltip.removeClass( 'top' );
				}

				$( window ).trigger( 'tm_tooltip_show' );

				if ( nofx ) {
					tooltip.css( {
						left: pos_left,
						top: pos_top
					} );
					target.data( 'is_moving', false );
				} else {
					tooltip
						.css( {
							left: pos_left,
							top: pos_top
						} )
						.removeClass( settings.fadeout )
						.addClass( settings.fadin );
				}
			}
		},

		show: function( target ) {
			var tooltip;
			var tip;
			var img;

			if ( target.data( 'is_moving' ) ) {
				return;
			}

			if ( target.data( 'tm-has-tm-tip' ) === 1 ) {
				if ( target.data( 'tm-tip-html' ) !== undefined ) {
					tip = target.data( 'tm-tip-html' );
					if ( target.attr( 'title' ) ) {
						target.data( 'tmtip-title', target.attr( 'title' ) );
					}
					target.removeAttr( 'title' );
				} else if ( target.attr( 'data-tm-tooltip-html' ) ) {
					tip = target.attr( 'data-tm-tooltip-html' );
					if ( target.attr( 'title' ) ) {
						target.data( 'tmtip-title', target.attr( 'title' ) );
					}
					target.removeAttr( 'title' );
				} else {
					tip = target.attr( 'title' );
				}

				if ( tip !== undefined ) {
					$( '#tm-tooltip' ).remove();

					tooltip = $( '<div id="tm-tooltip" class="tm-tip tm-animated"></div>' );
					tooltip.css( 'opacity', 0 ).html( tip ).appendTo( 'body' );

					img = tooltip.find( 'img' );
					if ( img.length > 0 ) {
						img.on( 'load', this.initTooltip.bind( this, target, tooltip ) );
					}

					this.initTooltip( target, tooltip );

					$( window ).on( 'scroll.tcToolTip resize.tcToolTip', this.initTooltip.bind( this, target, tooltip ) );

					target.data( 'is_moving', false );

					target.on( 'tmmovetooltip', this.initTooltip.bind( this, target, tooltip, 1 ) );
					target.on( 'mouseleave tmhidetooltip', this.removeTooltip.bind( this, target, tooltip ) );

					target.closest( 'label' ).on( 'mouseleave tmhidetooltip', this.removeTooltip.bind( this, target, tooltip ) );

					tooltip.on( 'click', this.removeTooltip.bind( this, target, tooltip ) );
				}
			}

			return false;
		},

		init: function() {
			var that = this;

			if ( this.targets.length > 0 ) {
				this.targets.toArray().forEach( function( element ) {
					var target;
					var is_swatch;
					var is_swatch_desc;
					var is_swatch_lbl_desc;
					var is_swatch_img;
					var is_swatch_img_lbl;
					var is_swatch_img_desc;
					var is_swatch_img_lbl_desc;
					var tip;
					var label;
					var desc;
					var descHTML;
					var get_img_src;
					var findlabel;
					var is_hide_label;

					target = $( element );

					if ( target.data( 'tm-has-tm-tip' ) === undefined ) {
						is_swatch = target.attr( 'data-tm-tooltip-swatch' );
						is_swatch_desc = target.attr( 'data-tm-tooltip-swatch-desc' );
						is_swatch_lbl_desc = target.attr( 'data-tm-tooltip-swatch-lbl-desc' );
						is_swatch_img = target.attr( 'data-tm-tooltip-swatch-img' );
						is_swatch_img_lbl = target.attr( 'data-tm-tooltip-swatch-img-lbl' );
						is_swatch_img_desc = target.attr( 'data-tm-tooltip-swatch-img-desc' );
						is_swatch_img_lbl_desc = target.attr( 'data-tm-tooltip-swatch-img-lbl-desc' );

						target.data( 'tm-has-tm-tip', 1 );

						if ( target.attr( 'data-original' ) !== undefined ) {
							get_img_src = target.attr( 'data-original' );
						} else if ( target.attr( 'src' ) !== undefined ) {
							get_img_src = target.attr( 'src' );
						} else {
							get_img_src = target[ 0 ].src;
						}

						label = target.closest( '.tmcp-field-wrap' );
						if ( label.length === 0 ) {
							label = target.closest( '.cpf_hide_element' );
						}
						if ( label.length === 0 ) {
							label = target.closest( '.cpf-section' ).find( '.tc-section-inner-wrap' );
						}
						findlabel = label.find( '.checkbox-image-label,.radio-image-label,.tm-tip-html' );

						if ( findlabel.length === 0 ) {
							findlabel = label.next( '.checkbox-image-label,.radio-image-label,.tm-tip-html' );
						}
						label = findlabel;

						findlabel = $( label );

						is_hide_label = target.attr( 'data-tm-hide-label' ) === 'yes' || target.attr( 'data-tm-hide-label' ) === undefined || findlabel.is( '.tm-tip-html' );

						descHTML = '';
						desc = target.closest( '.tmcp-field-wrap' );
						desc = desc.find( '[data-tm-tooltip-html]' );
						if ( desc.length === 0 ) {
							desc = target.closest( '.tmcp-field-wrap' ).find( '.tc-inline-description' );
							if ( desc.length > 0 ) {
								descHTML = desc.html();
							}
						} else {
							descHTML = desc.attr( 'data-tm-tooltip-html' );
						}

						if ( is_swatch ) {
							tip = findlabel.html();
						} else if ( is_swatch_desc ) {
							tip = '<aside>' + descHTML + '</aside>';
						} else if ( is_swatch_lbl_desc ) {
							tip = '<aside>' + findlabel.html() + '</aside><aside>' + descHTML + '</aside>';
						} else if ( is_swatch_img ) {
							tip = '<img src="' + get_img_src + '">';
						} else if ( is_swatch_img_lbl ) {
							tip = '<img src="' + get_img_src + '"><aside>' + findlabel.html() + '</aside>';
						} else if ( is_swatch_img_desc ) {
							tip = '<img src="' + get_img_src + '"><aside>' + descHTML + '</aside>';
						} else if ( is_swatch_img_lbl_desc ) {
							tip = '<img src="' + get_img_src + '"><aside>' + findlabel.html() + '</aside><aside>' + descHTML + '</aside>';
						}

						if ( tip !== undefined ) {
							target.data( 'tm-tip-html', tip );
							if ( is_hide_label ) {
								findlabel.hide();
							}
						}

						// The following two methods are here for dynamic tooltip support
						if ( target.attr( 'data-tm-tooltip-html' ) ) {
							tip = target.attr( 'data-tm-tooltip-html' );
						} else {
							tip = target.attr( 'title' );
						}

						target.on( 'tc-tooltip-html-changed', function() {
							if ( target.attr( 'data-tm-tooltip-html' ) ) {
								target.show();
							} else {
								target.hide();
							}
						} );

						target.closest( 'label' ).on( 'mouseenter tmshowtooltip', that.show.bind( that, target ) );
						target.on( 'mouseenter tmshowtooltip', that.show.bind( that, target ) );
					}
				} );
			}
		}
	};

	$.fn.tcToolTip = function( option ) {
		var methodReturn;
		var targets = $( this );
		var data;
		var options;
		var ret;
		var hasAtLeastOneNonToolTip = targets
			.map( function() {
				return $( this ).data( 'tctooltip' ) || '';
			} )
			.get()
			.some( function( value ) {
				return value === '';
			} );

		if ( typeof option === 'object' ) {
			options = option;
		} else {
			options = {};
		}

		if ( hasAtLeastOneNonToolTip ) {
			data = new ToolTip( this, options );
			targets.data( 'tctooltip', data );
		}

		if ( typeof option === 'string' ) {
			methodReturn = data[ option ].apply( data, [] );
		}

		if ( methodReturn === undefined ) {
			ret = targets;
		} else {
			ret = methodReturn;
		}

		return ret;
	};

	$.fn.tcToolTip.defaults = {
		fadin: 'fadeIn',
		fadeout: 'fadeout',
		speed: 1500
	};

	$.fn.tcToolTip.instances = [];

	$.fn.tcToolTip.Constructor = ToolTip;

	$.tcToolTip = function( targets, options ) {
		var data = false;
		var hasAtLeastOneNonToolTip;

		targets = targets || $( '.tm-tooltip' );
		hasAtLeastOneNonToolTip = targets
			.map( function() {
				return $( this ).data( 'tctooltip' ) || '';
			} )
			.get()
			.some( function( value ) {
				return value === '';
			} );
		if ( hasAtLeastOneNonToolTip ) {
			data = new ToolTip( targets, options );
			targets.data( 'tctooltip', data );
		}

		return data;
	};
}( window, window.jQuery ) );
