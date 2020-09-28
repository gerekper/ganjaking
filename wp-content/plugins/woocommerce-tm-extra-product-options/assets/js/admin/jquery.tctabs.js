/**
 * jQuery Tabs plugin
 *
 * Creates a tabular interface
 * Internal use only
 *
 * Copyright 2000-2019 themeComplete
 */

( function( $ ) {
	'use strict';

	var localStorage = $.epoAPI.util.getStorage( 'localStorage' );
	var confirm = window.confirm;

	$.fn.tcTabs = function( options ) {
		var elements = this;
		var tm_tab_add_header_events;
		var last = [];
		var current = [];

		if ( elements.length === 0 ) {
			return;
		}

		options = $.extend(
			{
				headers: '.tm-tab-headers',
				header: '.tab-header',
				addheader: '.tm-add-tab',
				classdown: 'tcfa-angle-down',
				classup: 'tcfa-angle-up',
				animationclass: 'appear',
				dataattribute: 'data-id',
				dataopenattribute: 'data-id',
				selectedtab: 'auto',
				showonhover: false,
				useclasstohide: true,
				afteraddtab: null,
				beforemovetab: null,
				aftermovetab: null,
				deletebutton: false,
				deletebuttonhtml: '<h4 class="tm-del-tab"><span class="tcfa tcfa-times"></span></h4>',
				deleteheader: '.tm-del-tab',
				deleteconfirm: false,
				beforedeletetab: null,
				afterdeletetab: null
			},
			options
		);

		tm_tab_add_header_events = function( t, index, tc, header ) {
			var additional_events = '';

			header.on( 'closetab.tmtabs', function() {
				var _tab = t.find( $( this ).data( 'tab' ) );

				$( this ).removeClass( 'closed open' ).addClass( 'closed' );
				$( this )
					.find( '.tm-arrow' )
					.removeClass( options.classdown + ' ' + options.classup )
					.addClass( options.classdown );
				if ( options.useclasstohide ) {
					_tab.addClass( 'tm-hide' ).removeClass( 'tm-show' );
				} else {
					_tab.hide();
				}
				_tab.removeClass( 'tm-animated ' + options.animationclass );
				$( window ).trigger( 'tc-closetab.tmtabs', {
					header: $( this ),
					tab: _tab
				} );
			} );

			header.on( 'opentab.tmtabs', function() {
				var _tab = t.find( $( this ).data( 'tab' ) );

				$( this ).removeClass( 'closed open' ).addClass( 'open' );
				$( this )
					.find( '.tm-arrow' )
					.removeClass( options.classdown + ' ' + options.classup )
					.addClass( options.classup );

				if ( options.useclasstohide ) {
					_tab.removeClass( 'tm-hide' ).addClass( 'tm-show' );
				} else {
					_tab.show();
				}
				_tab.removeClass( 'tm-animated ' + options.animationclass ).addClass( 'tm-animated ' + options.animationclass );
				current[ index ] = $( this ).data( 'tab' );
				$( window ).trigger( 'tc-opentab.tmtabs', {
					header: $( this ),
					tab: current[ index ],
					table: _tab
				} );
			} );

			if ( options.showonhover === true || typeof options.showonhover === 'function' ) {
				additional_events = ' mouseover';
			}

			header.on( 'keydown.tmtabs', function( e ) {
				var $this = $( this );
				var prevnext;

				if ( e.keyCode === 13 ) {
					$( this ).trigger( 'click.tmtabs' );
				}
				if ( e.keyCode === 40 ) {
					prevnext = $( this ).closest( '.tm-box' ).next().find( options.header );
				}
				if ( e.keyCode === 38 ) {
					prevnext = $( this ).closest( '.tm-box' ).prev().find( options.header );
				}
				if ( prevnext && prevnext.length ) {
					$this.blur();
					prevnext.focus().trigger( 'click' );
					e.preventDefault();
				}
			} );

			header.on( 'click.tmtabs' + additional_events, function( e ) {
				e.preventDefault();
				if ( e.type === 'mouseover' && typeof options.showonhover === 'function' && ! options.showonhover.call() ) {
					return;
				}
				if ( current[ index ] === $( this ).data( 'tab' ) ) {
					$( window ).trigger( 'tc-isopentab.tmtabs', {
						header: $( this ),
						tab: current[ index ],
						table: t.find( current[ index ] )
					} );
					return;
				}
				if ( last[ index ] ) {
					$( last[ index ] ).trigger( 'closetab.tmtabs' );
				}
				$( this ).trigger( 'opentab.tmtabs' );
				last[ index ] = $( this );
				if ( localStorage ) {
					localStorage.setItem( 'tmadmintab-' + tc, $( this ).attr( options.dataopenattribute ) );
				}
				$( window ).trigger( 'tc-tmtabs-clicked', {
					tc: tc,
					options: options,
					header: $( this ),
					tab: current[ index ],
					table: t.find( current[ index ] )
				} );
			} );

			if ( options.deletebutton ) {
				header.after( options.deletebuttonhtml );
				header
					.closest( '.tm-box' )
					.find( options.deleteheader )
					.on( 'click.tmtabs', function() {
						var $t;
						var $tab;

						if ( t.find( options.headers + ' ' + options.header ).length < 2 ) {
							return;
						}
						if ( options.deleteconfirm ) {
							if ( ! confirm( window.TMEPOGLOBALADMINJS.i18n_builder_delete ) ) {
								return;
							}
						}

						$t = $( this );
						$tab = t.find( '.' + $t.closest( '.tm-box' ).find( options.header ).attr( options.dataattribute ) );

						if ( typeof options.beforedeletetab === 'function' ) {
							options.beforedeletetab.call( t, $t, $tab );
						}

						$tab.remove();
						$t.closest( '.tm-box' ).remove();

						if ( typeof options.afterdeletetab === 'function' ) {
							options.afterdeletetab.call( t );
						}
					} );
			}
		};

		return elements.each( function( index ) {
			var t = $( this );
			var tc = t.attr( 'class' );
			var headers = t.find( options.headers + ' ' + options.header );
			var ohp = 0;
			var ohpid = '';
			var init_open = 0;
			var add_counter = 0;
			var _selected_tab;
			var vars = {};
			var initialIndex;

			if ( headers.length === 0 ) {
				return;
			}

			window.location.href.replace( /[?&]+([^=&]+)=([^&]*)/gi, function( m, key, value ) {
				vars[ key ] = value;
			} );

			last[ index ] = false;
			current[ index ] = '';

			t.data( 'tm-has-tmtabs', 1 );

			headers.each( function( i, header ) {
				var id;

				header = $( header );
				id = '.' + header.attr( options.dataattribute );
				header.data( 'tab', id );
				if ( options.useclasstohide ) {
					t.find( id ).addClass( 'tm-hide' ).removeClass( 'tm-show' );
				} else {
					t.find( id ).hide();
				}
				t.find( id ).data( 'state', 'closed' );
				if ( ! init_open && header.is( '.open' ) ) {
					header.removeClass( 'closed open' ).addClass( 'open' ).data( 'state', 'open' );
					header
						.find( '.tm-arrow' )
						.removeClass( options.classdown + ' ' + options.classup )
						.addClass( options.classup );
					if ( options.useclasstohide ) {
						t.find( id ).removeClass( 'tm-hide' ).addClass( 'tm-show' );
					} else {
						t.find( id ).show();
					}
					t.find( id ).data( 'state', 'open' );
					init_open = 1;
					current[ index ] = id;
					last[ index ] = header;
				} else {
					header.removeClass( 'closed open' ).addClass( 'closed' ).data( 'state', 'closed' );
				}

				tm_tab_add_header_events( t, index, tc, header );
			} );

			t.find( options.headers + ':not(.section_elements ' + options.headers + ',.tm-settings-wrap ' + options.headers + ',.builder_element_wrap ' + options.headers + ')' ).sortable( {
				containment: 'parent',
				cursor: 'move',
				items: '.tm-box:not(.tm-add-box)',
				start: function( e, ui ) {
					var $tab;
					$tab = t.find( '.' + ui.item.closest( '.tm-box' ).find( options.header ).attr( options.dataattribute ) );
					ohp = ui.item.index();
					ohpid = ui.item.find( options.header ).attr( 'data-id' );

					initialIndex = $.tmEPOAdmin.find_index( true, $tab.find( '.bitem' ).first() );

					if ( typeof options.beforemovetab === 'function' ) {
						options.beforemovetab.call( this, ohp, $tab, initialIndex );
					}
				},
				stop: function( e, ui ) {
					var all_headers = t.find( options.headers + ' ' + options.header );
					var original_item;
					var new_index;
					var replaced_item;
					var $tab;

					$tab = t.find( '.' + ui.item.closest( '.tm-box' ).find( options.header ).attr( options.dataattribute ) );
					all_headers.each( function( i ) {
						$( this ).html( parseInt( i, 10 ) + 1 );
					} );
					original_item = t.find( '.tm-slider-wizard-tab.' + ohpid );
					new_index = t
						.find( options.headers + ' ' + options.header + "[data-id='" + ohpid + "']" )
						.parent()
						.index();
					replaced_item = t.find( '.tm-slider-wizard-tab' ).eq( new_index );
					if ( new_index > ohp ) {
						replaced_item.after( original_item );
					} else if ( new_index < ohp ) {
						replaced_item.before( original_item );
					}
					if ( typeof options.aftermovetab === 'function' ) {
						options.aftermovetab.call( this, new_index, ohp, $tab, initialIndex );
					}
				},
				cancel: '.tm-add-box',
				forcePlaceholderSize: true,
				tolerance: 'pointer'
			} );

			t.find( options.addheader ).on( 'click.tmtabs', function( e ) {
				var last_header = t.find( options.headers + ' ' + options.header ).last();
				var id = last_header.attr( options.dataattribute );
				var last_tab = t.find( '.' + id );
				var new_header = last_header.tcClone().off( 'closetab.tmtabs opentab.tmtabs click.tmtabs' );
				var new_tab = last_tab.tcClone().empty();
				var newid = id + '-' + add_counter;

				e.preventDefault();

				add_counter += 1;

				new_header
					.html( t.find( options.headers + ' ' + options.header ).length + 1 )
					.removeClass( 'closed open' )
					.addClass( 'closed' )
					.data( 'tab', '.' + newid )
					.data( 'state', 'closed' )
					.attr( options.dataattribute, newid );
				new_tab.removeClass( id ).addClass( newid );
				if ( options.useclasstohide ) {
					new_tab.addClass( 'tm-hide' ).removeClass( '.tm-show' );
				} else {
					new_tab.hide();
				}
				new_tab.removeClass( 'tm-animated ' + options.animationclass );

				last_header.closest( '.tm-box' ).after( new_header );

				new_header.wrap( '<div class="tm-box"></div>' );

				tm_tab_add_header_events( t, index, tc, new_header );
				last_tab.after( new_tab );
				if ( typeof options.afteraddtab === 'function' ) {
					options.afteraddtab.call( this, new_header, new_tab );
				}
			} );

			if ( options.selectedtab === 'auto' ) {
				if ( localStorage ) {
					_selected_tab = localStorage.getItem( 'tmadmintab-' + tc );
				}
				if ( vars.selected_tab !== undefined ) {
					_selected_tab = vars.selected_tab;
				}
				if ( _selected_tab === undefined || _selected_tab === null ) {
					_selected_tab = $( options.header ).eq( 0 ).attr( options.dataopenattribute );
				}
				if ( ! $( options.header + '[' + options.dataopenattribute + '="' + _selected_tab + '"]' ).is( ':visible' ) ) {
					$( options.header ).eq( 0 ).trigger( 'click.tmtabs' );
				} else {
					$( options.header + '[' + options.dataopenattribute + '="' + _selected_tab + '"]' ).trigger( 'click.tmtabs' );
				}
			} else if ( options.selectedtab !== false ) {
				_selected_tab = parseInt( options.selectedtab, 10 );
				t.find( options.header + ':eq(' + _selected_tab + ')' ).trigger( 'click.tmtabs' );
			}
		} );
	};

	$.fn.tmtabs = $.fn.tcTabs;
}( window.jQuery ) );
