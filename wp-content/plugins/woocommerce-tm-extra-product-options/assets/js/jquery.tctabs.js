/**
 * jQuery Tabs plugin
 * Creates a tabular interface
 *
 * @param {Object} $ The jQuery object
 * @version: v1.1
 * @author: ThemeComplete
 *
 * Copyright 2022 ThemeComplete
 */
( function( $ ) {
	'use strict';

	var localStorage = $.epoAPI.util.getStorage( 'localStorage' );
	var confirm = window.confirm;

	var Tabs = function( dom, options ) {
		this.elements = $( dom );
		this.last = [];
		this.current = [];
		this.enableEvents = true;
		this.backup = [];

		this.options = $.extend( {}, $.fn.tcTabs.defaults, options );

		if ( this.elements.length > 0 ) {
			this.init();
			return this;
		}

		return false;
	};

	Tabs.prototype = {
		constructor: Tabs,

		set: function( options ) {
			this.options = $.extend( {}, this.options, options );
			this.init();
		},

		addHeaderEvents: function( t, index, tc, header, theaderswrap, theaders, scrollLeftButton, scrollRightButton, state ) {
			var additional_events = '';
			var ithis = this;

			header.off( 'closetab.tmtabs' ).on( 'closetab.tmtabs', function() {
				var $this = $( this );
				var _tab = t.find( $this.data( 'tab' ) );

				$this.removeClass( 'closed open' ).addClass( 'closed' );
				$this
					.find( '.tm-arrow' )
					.removeClass( ithis.options.classdown + ' ' + ithis.options.classup )
					.addClass( ithis.options.classdown );
				if ( ithis.options.useclasstohide ) {
					_tab.addClass( 'tm-hide' ).removeClass( 'tm-show' );
				} else {
					_tab.hide();
				}
				_tab.removeClass( 'tm-animated ' + ithis.options.animationclass );
				$( window ).trigger( 'tc-closetab.tmtabs', {
					header: $this,
					tab: _tab
				} );
			} );

			header.off( 'opentab.tmtabs' ).on( 'opentab.tmtabs', function() {
				var $this = $( this );
				var _tab = t.find( $this.data( 'tab' ) );

				$this.removeClass( 'closed open' ).addClass( 'open' );
				$this
					.find( '.tm-arrow' )
					.removeClass( ithis.options.classdown + ' ' + ithis.options.classup )
					.addClass( ithis.options.classup );

				if ( ithis.options.useclasstohide ) {
					_tab.removeClass( 'tm-hide' ).addClass( 'tm-show' );
				} else {
					_tab.show();
				}
				_tab.removeClass( 'tm-animated ' + ithis.options.animationclass ).addClass( 'tm-animated ' + ithis.options.animationclass );
				ithis.current[ index ] = $this.data( 'tab' );
				$( window ).trigger( 'tc-opentab.tmtabs', {
					header: $this,
					tab: ithis.current[ index ],
					table: _tab
				} );
				ithis.scrollIntoView( t, $this, state );
			} );

			if ( ithis.options.showonhover === true || typeof ithis.options.showonhover === 'function' ) {
				additional_events = ' mouseover';
			}

			header.off( 'keydown.tmtabs' ).on( 'keydown.tmtabs', function( e ) {
				var $this = $( this );
				var prevnext;

				if ( e.keyCode === 13 ) {
					$this.trigger( 'click.tmtabs' );
				}
				if ( e.keyCode === 40 ) {
					prevnext = $this.closest( '.tm-box' ).next().find( '.' + ithis.options.header );
				}
				if ( e.keyCode === 38 ) {
					prevnext = $this.closest( '.tm-box' ).prev().find( '.' + ithis.options.header );
				}
				if ( prevnext && prevnext.length ) {
					$this.trigger( 'blur' );
					prevnext.trigger( ' focus' ).trigger( 'click' );
					e.preventDefault();
				}
			} );

			header.off( 'click.tmtabs' ).on( 'click.tmtabs' + additional_events, function( e ) {
				var $this = $( this );
				e.preventDefault();
				if ( e.type === 'mouseover' && typeof ithis.options.showonhover === 'function' && ! ithis.options.showonhover.call() ) {
					return;
				}
				if ( ithis.current[ index ] === $this.data( 'tab' ) ) {
					$( window ).trigger( 'tc-isopentab.tmtabs', {
						header: $this,
						tab: ithis.current[ index ],
						table: t.find( ithis.current[ index ] )
					} );
					return;
				}
				if ( ithis.last[ index ] ) {
					$( ithis.last[ index ] ).trigger( 'closetab.tmtabs' );
				}
				$this.trigger( 'opentab.tmtabs' );
				ithis.last[ index ] = $this;
				if ( localStorage ) {
					localStorage.setItem( 'tmadmintab-' + tc, $this.attr( ithis.options.dataopenattribute ) );
				}
				$( window ).trigger( 'tc-tmtabs-clicked', {
					tc: tc,
					options: ithis.options,
					header: $this,
					tab: ithis.current[ index ],
					table: t.find( ithis.current[ index ] )
				} );
			} );

			if ( this.options.deletebutton ) {
				header.append( this.options.deletebuttonhtml );
				header
					.find( '.' + this.options.deleteheader )
					.off( 'click.tmtabs' )
					.on( 'click.tmtabs', function( e ) {
						var $t = $( this );
						var $tmbox = $t.closest( '.tm-box' );
						var $header = $tmbox.find( '.' + ithis.options.header );
						var $tab = t.find( '.' + $header.attr( ithis.options.dataattribute ) );

						e.stopPropagation();

						if ( t.find( '.' + ithis.options.headers + ' ' + '.' + ithis.options.header ).length < 2 ) {
							return;
						}
						if ( ithis.options.deleteconfirm ) {
							if ( ! confirm( window.TMEPOGLOBALADMINJS.i18n_builder_delete ) ) {
								return;
							}
						}

						if ( typeof ithis.options.beforedeletetab === 'function' ) {
							ithis.options.beforedeletetab.call( t, $t, $tab );
						}

						$tab.remove();
						if ( $header.is( '.open' ) ) {
							if ( $tmbox.next().find( '.' + ithis.options.header ).is( '.closed' ) ) {
								$tmbox.next().find( '.' + ithis.options.header ).trigger( 'click.tmtabs' );
							} else if ( $tmbox.prev().find( '.' + ithis.options.header ).is( '.closed' ) ) {
								$tmbox.prev().find( '.' + ithis.options.header ).trigger( 'click.tmtabs' );
							}
						}
						$tmbox.remove();

						ithis.checkSize( theaderswrap, theaders, scrollLeftButton, scrollRightButton, state, true );
						if ( typeof ithis.options.afterdeletetab === 'function' ) {
							ithis.options.afterdeletetab.call( t );
						}
					} );
			} else {
				header.find( '.' + this.options.deleteheader ).remove();
			}

			if ( this.options.editbutton ) {
				header.append( ithis.options.editbuttonhtml );
				header
					.find( '.' + ithis.options.editheader )
					.off( 'click.tmtabs' )
					.on( 'click.tmtabs', function( event ) {
						var $t;
						var $tab;
						if ( ithis.enableEvents && typeof ithis.options.oneditbutton === 'function' ) {
							event.stopPropagation();
							$t = $( this );
							$tab = t.find( '.' + $t.closest( '.tm-box' ).find( '.' + ithis.options.header ).attr( ithis.options.dataattribute ) );
							ithis.options.oneditbutton.call( t, $t, $tab );
						}
					} );
			} else {
				header.find( '.' + this.options.editheader ).remove();
			}
		},

		initHeader: function( header, t, index, theaderswrap, theaders, scrollLeftButton, scrollRightButton, state ) {
			var id;
			var init_open = 0;
			var tc = t.attr( 'class' );

			header = $( header );
			id = '.' + header.attr( this.options.dataattribute );
			header.data( 'tab', id );
			if ( this.options.useclasstohide ) {
				t.find( id ).addClass( 'tm-hide' ).removeClass( 'tm-show' );
			} else {
				t.find( id ).hide();
			}
			t.find( id ).data( 'state', 'closed' );
			if ( ! init_open && header.is( '.open' ) ) {
				header.removeClass( 'closed open' ).addClass( 'open' ).data( 'state', 'open' );
				header
					.find( '.tm-arrow' )
					.removeClass( this.options.classdown + ' ' + this.options.classup )
					.addClass( this.options.classup );
				if ( this.options.useclasstohide ) {
					t.find( id ).removeClass( 'tm-hide' ).addClass( 'tm-show' );
				} else {
					t.find( id ).show();
				}
				t.find( id ).data( 'state', 'open' );
				init_open = 1;
				this.current[ index ] = id;
				this.last[ index ] = header;
			} else {
				header.removeClass( 'closed open' ).addClass( 'closed' ).data( 'state', 'closed' );
			}

			this.addHeaderEvents( t, index, tc, header, theaderswrap, theaders, scrollLeftButton, scrollRightButton, state );
		},

		checkSize: function( theaderswrap, theaders, scrollLeftButton, scrollRightButton, state, setScrollWidth ) {
			var panelWidth;
			var openedHeader;
			var props = [];
			var hiddenParents = theaders.parents().addBack().not( ':visible' );

			// When tabs element is hidden its width cannot be calcualted
			// so we revert the visibility for this procedure only
			hiddenParents.each( function() {
				var $this = $( this );
				var styleDisplay = this.style.display;
				var cssDisplay = $this.css( 'display' );
				var styleVisibility = this.style.visibility;
				var cssVisibility = $this.css( 'visibility' );
				var obj;
				var did = false;
				obj = {
					styleDisplay: styleDisplay,
					cssDisplay: cssDisplay,
					styleVisibility: styleVisibility,
					cssVisibility: cssVisibility,
					display: null,
					visibility: null,
					element: $this
				};
				if ( styleDisplay === 'none' ) {
					this.style.display = '';
					obj.display = true;
					if ( $this.css( 'display' ) === 'none' ) {
						this.style.display = 'block';
					}
					did = true;
				}
				if ( styleVisibility === 'hidden' ) {
					this.style.visibility = '';
					obj.visibility = true;
					if ( $this.css( 'visibility' ) === 'hidden' ) {
						this.style.visibility = 'visible';
					}
					did = true;
				}

				if ( did ) {
					props.push( obj );
				}
			} );

			panelWidth = Math.round( theaders.outerWidth() );

			if ( theaders.is( '.has-scroll-arrows' ) ) {
				theaders.removeClass( '.has-scroll-arrows' );
				panelWidth = Math.round( theaderswrap.outerWidth() );
			}

			if ( setScrollWidth ) {
				openedHeader = theaders.find( '.' + this.options.header + '.open' );
				if ( openedHeader.length ) {
					openedHeader.removeClass( 'open' );
				}
				state.scrollWidth = theaders[ 0 ].scrollWidth;
				if ( openedHeader.length ) {
					openedHeader.addClass( 'open' );
				}
			}

			if ( state.scrollWidth > panelWidth ) {
				scrollRightButton.addClass( 'scroll-arrow-show' );
				scrollLeftButton.addClass( 'scroll-arrow-show' );
				theaders.addClass( 'has-scroll-arrows' );

				if ( state.scrollWidth - panelWidth === theaders.scrollLeft() ) {
					scrollRightButton.addClass( 'scroll-arrow-disabled scroll-arrow-right-disabled' );
				} else {
					scrollRightButton.removeClass( 'scroll-arrow-disabled scroll-arrow-right-disabled' );
				}
				if ( theaders.scrollLeft() === 0 ) {
					scrollLeftButton.addClass( 'scroll-arrow-disabled scroll-arrow-left-disabled' );
				} else {
					scrollLeftButton.removeClass( 'scroll-arrow-disabled scroll-arrow-left-disabled' );
				}
			} else {
				scrollRightButton.removeClass( 'scroll-arrow-show' );
				scrollLeftButton.removeClass( 'scroll-arrow-show' );
				theaders[ 0 ].scrollLeft = 0;
				theaders.removeClass( 'has-scroll-arrows' );
			}

			props.forEach( function( o ) {
				if ( o.display ) {
					o.element[ 0 ].style.display = o.styleDisplay;
				}
				if ( o.visibility ) {
					o.element[ 0 ].style.visibility = o.styleVisibility;
				}
			} );
		},

		scrollIntoView: function( t, header, state ) {
			var left = state.scrollPos;
			var theaders = t.find( '.' + this.options.headers );
			var scrollWidth = theaders.width();

			if ( theaders.is( '.has-scroll-arrows' ) && header && typeof ( header ) !== 'undefined' && header.position() && typeof ( header.position() ) !== 'undefined' ) {
				if ( header.position().left < 0 ) {
					state.scrollPos = Math.max( left + header.position().left + 1, 0 );
					theaders.animate( { scrollLeft: ( state.scrollPos + 1 ) + 'px' }, this.options.scrollDuration );
				} else if ( ( header.position().left + header.outerWidth() ) > scrollWidth ) {
					state.scrollPos = Math.min( left + ( ( header.position().left + header.outerWidth() ) - scrollWidth ), theaders[ 0 ].scrollWidth - theaders.outerWidth() );
					theaders.animate( { scrollLeft: ( state.scrollPos - 1 ) + 'px' }, this.options.scrollDuration );
				}
			}
		},

		initElement: function( t, index, state, uniqid ) {
			var ithis = this;
			var tc = t.attr( 'class' );
			var theaders;
			var theaderswrap;
			var scrollLeftButton;
			var scrollRightButton;
			var headers = t.find( '.' + this.options.headers + ' ' + '.' + this.options.header );
			var ohp = 0;
			var ohpid = '';
			var _selected_tab;
			var vars = {};
			var initialIndex;
			var pressHoldTimeout;

			if ( headers.length === 0 ) {
				return;
			}

			theaders = t.find( '.' + this.options.headers );
			theaders.wrap( '<div class="' + this.options.headersWrap + '"></div>' );
			theaderswrap = t.find( '.' + this.options.headersWrap );

			if ( this.options.scroll ) {
				theaderswrap.html( '<div class="tc-scroll-left-arrow"></div>' + theaderswrap.html() + '<div class="tc-scroll-right-arrow"></div>' );
				headers = t.find( '.' + this.options.headers + ' ' + '.' + this.options.header );
				theaders = t.find( '.' + this.options.headers );
				scrollLeftButton = t.find( '.tc-scroll-left-arrow' );
				scrollRightButton = t.find( '.tc-scroll-right-arrow' );

				// Mousewheel support, if present.
				if ( typeof $.fn.mousewheel === 'function' ) {
					theaders.mousewheel( function( event, delta ) {
						// Only do mousewheel scrolling if scrolling is necessary
						if ( scrollRightButton.css( 'display' ) !== 'none' ) {
							this.scrollLeft -= ( delta * 30 );
							state.scrollPos = this.scrollLeft;
							event.preventDefault();
						}
					} );
				}

				// Set initial scroll position
				theaders.animate( { scrollLeft: state.scrollPos + 'px' }, 0 );

				this.checkSize( theaderswrap, theaders, scrollLeftButton, scrollRightButton, state, true );
				$( window ).off( 'resize.tmtabs' + uniqid ).on( 'resize.tmtabs' + uniqid, function() {
					ithis.checkSize( theaderswrap, theaders, scrollLeftButton, scrollRightButton, state, true );
				} );

				t.off( 'refresh.tmtabs' + uniqid ).on( 'refresh.tmtabs' + uniqid, function() {
					ithis.checkSize( theaderswrap, theaders, scrollLeftButton, scrollRightButton, state, true );
				} );

				// Document load
				$( function() {
					ithis.checkSize( theaderswrap, theaders, scrollLeftButton, scrollRightButton, state, true );
				} );

				scrollRightButton.off( 'mousedown.tmtabs mouseup.tmtabs mouseleave.tmtabs mouseover.tmtabs mouseout.tmtabs' )
					.on( 'mousedown.tmtabs', function( e ) {
						var scrollRightFunc = function() {
							var left = theaders.scrollLeft();
							state.scrollPos = Math.min( left + ithis.options.scrollDistance, state.scrollWidth - theaders.outerWidth() );
							theaders.animate(
								{
									scrollLeft: state.scrollPos + 'px'
								},
								ithis.options.scrollDuration,
								function() {
									ithis.checkSize( theaderswrap, theaders, scrollLeftButton, scrollRightButton, state );
								}
							);
						};
						e.stopPropagation();
						scrollRightFunc();

						pressHoldTimeout = setInterval( function() {
							scrollRightFunc();
						}, ithis.options.scrollDuration );
					} ).on( 'mouseup.tmtabs mouseleave.tmtabs', function() {
						clearInterval( pressHoldTimeout );
					} ).on( 'mouseover.tmtabs', function() {
						$( this ).addClass( 'scroll-arrow-over scroll-arrow-right-over' );
					} ).on( 'mouseout.tmtabs', function() {
						$( this ).removeClass( 'scroll-arrow-over scroll-arrow-right-over' );
					} );

				scrollLeftButton.off( 'mousedown.tmtabs mouseup.tmtabs mouseleave.tmtabs mouseover.tmtabs mouseout.tmtabs' )
					.on( 'mousedown.tmtabs', function( e ) {
						var scrollLeftFunc = function() {
							var left = theaders.scrollLeft();
							state.scrollPos = Math.max( left - ithis.options.scrollDistance, 0 );
							theaders.animate(
								{
									scrollLeft: state.scrollPos + 'px'
								},
								ithis.options.scrollDuration,
								function() {
									ithis.checkSize( theaderswrap, theaders, scrollLeftButton, scrollRightButton, state );
								}
							);
						};
						e.stopPropagation();
						scrollLeftFunc();

						pressHoldTimeout = setInterval( function() {
							scrollLeftFunc();
						}, ithis.options.scrollDuration );
					} ).on( 'mouseup.tmtabs mouseleave.tmtabs', function() {
						clearInterval( pressHoldTimeout );
					} ).on( 'mouseover.tmtabs', function() {
						$( this ).addClass( 'scroll-arrow-over scroll-arrow-left-over' );
					} ).on( 'mouseout.tmtabs', function() {
						$( this ).removeClass( 'scroll-arrow-over scroll-arrow-left-over' );
					} );
			}

			window.location.href.replace( /[?&]+([^=&]+)=([^&]*)/gi, function( m, key, value ) {
				vars[ key ] = value;
			} );

			this.last[ index ] = false;
			this.current[ index ] = '';

			t.data( 'tm-has-tmtabs', 1 );

			headers.each( function( i, header ) {
				ithis.initHeader( header, t, index, theaderswrap, theaders, scrollLeftButton, scrollRightButton, state );
			} );

			if ( this.options.sortabletabs ) {
				t.find( '.' + this.options.headers + ':not(.section_elements ' + '.' + this.options.headers + ',.tm-settings-wrap ' + '.' + this.options.headers + ',.builder-element-wrap ' + '.' + this.options.headers + ')' ).sortable( {
					cursor: 'move',
					items: '.tm-box:not(.tm-add-box)',
					start: function( e, ui ) {
						var $tab;
						$tab = t.find( '.' + ui.item.closest( '.tm-box' ).find( '.' + ithis.options.header ).attr( ithis.options.dataattribute ) );
						ohp = ui.item.index();
						ohpid = ui.item.find( '.' + ithis.options.header ).attr( ithis.options.dataopenattribute );

						initialIndex = $.tmEPOAdmin.find_index( true, $tab.find( '.bitem' ).first() );

						if ( typeof ithis.options.beforemovetab === 'function' ) {
							ithis.options.beforemovetab.call( this, ohp, $tab, initialIndex );
						}
						ithis.enableEvents = false;
					},
					stop: function( e, ui ) {
						var original_item;
						var new_index;
						var replaced_item;
						var $tab;

						$tab = t.find( '.' + ui.item.closest( '.tm-box' ).find( '.' + ithis.options.header ).attr( ithis.options.dataattribute ) );
						original_item = t.find( '.' + ithis.options.slide + '.' + ohpid );
						new_index = t
							.find( '.' + ithis.options.headers + ' ' + '.' + ithis.options.header + '[' + ithis.options.dataopenattribute + '=\'' + ohpid + '\']' )
							.parent()
							.index();
						replaced_item = t.find( '.' + ithis.options.slide ).eq( new_index );
						if ( new_index > ohp ) {
							replaced_item.after( original_item );
						} else if ( new_index < ohp ) {
							replaced_item.before( original_item );
						}
						if ( typeof ithis.options.aftermovetab === 'function' ) {
							ithis.options.aftermovetab.call( this, new_index, ohp, $tab, initialIndex );
						}
						ithis.enableEvents = true;
					},
					cancel: '.tm-add-box',
					forcePlaceholderSize: true,
					revert: 200,
					placeholder: 'tm-box pl',
					tolerance: 'pointer'
				} );
			}

			if ( this.options.addbutton ) {
				theaderswrap.append( this.options.addbuttonhtml );
				t.addClass( 'has-add-button' ).find( '.' + this.options.addheader ).off( 'click.tmtabs' ).on( 'click.tmtabs', function( e ) {
					var allHeaders = t.find( '.' + ithis.options.headers + ' ' + '.' + ithis.options.header );
					var last_header = allHeaders.last();
					var id = last_header.attr( ithis.options.dataattribute );
					var last_tab = t.find( '.' + id );
					var new_header = last_header.tcClone().off( 'closetab.tmtabs opentab.tmtabs click.tmtabs' );
					var new_tab = last_tab.tcClone().empty();
					var newid = ithis.options.slide + allHeaders.length;

					e.preventDefault();

					new_header
						.html( '<span class="tab-text">' + ( t.find( '.' + ithis.options.headers + ' ' + '.' + ithis.options.header ).length + 1 ) + '</span>' )
						.removeClass( 'closed open' )
						.addClass( 'closed' )
						.data( 'tab', '.' + newid )
						.data( 'state', 'closed' )
						.attr( ithis.options.dataattribute, newid );
					new_tab.removeClass( id ).addClass( newid );
					if ( ithis.options.useclasstohide ) {
						new_tab.addClass( 'tm-hide' ).removeClass( '.tm-show' );
					} else {
						new_tab.hide();
					}
					new_tab.removeClass( 'tm-animated ' + ithis.options.animationclass );

					last_header.closest( '.tm-box' ).after( new_header );

					new_header.wrap( '<div class="tm-box"></div>' );

					ithis.addHeaderEvents( t, index, tc, new_header, theaderswrap, theaders, scrollLeftButton, scrollRightButton, state );
					last_tab.after( new_tab );
					if ( ithis.options.scroll ) {
						// ithis.checkSize( theaderswrap, theaders, scrollLeftButton, scrollRightButton, state, true );
						theaders.animate(
							{
								scrollLeft: new_header.width() + 'px'
							},
							ithis.options.scrollDuration,
							function() {
								ithis.checkSize( theaderswrap, theaders, scrollLeftButton, scrollRightButton, state, true );
							}
						);
					}
					if ( typeof ithis.options.afteraddtab === 'function' ) {
						ithis.options.afteraddtab.call( this, new_header, new_tab );
					}
				} );
			} else {
				theaderswrap.find( '.' + this.options.addheader ).remove();
			}

			if ( this.options.selectedtab === 'auto' ) {
				if ( localStorage ) {
					_selected_tab = localStorage.getItem( 'tmadmintab-' + tc );
				}
				if ( vars.selected_tab !== undefined ) {
					_selected_tab = vars.selected_tab;
				}
				if ( vars.menu !== undefined ) {
					_selected_tab = vars.menu;
				}
				if ( _selected_tab === undefined || _selected_tab === null ) {
					_selected_tab = t.find( '.' + this.options.header ).eq( 0 ).attr( this.options.dataopenattribute );
				}
				if ( ! t.find( '.' + this.options.header + '[' + this.options.dataopenattribute + '="' + _selected_tab + '"]' ).is( ':visible' ) ) {
					t.find( '.' + this.options.header ).eq( 0 ).trigger( 'click.tmtabs' );
				} else {
					t.find( '.' + this.options.header + '[' + this.options.dataopenattribute + '="' + _selected_tab + '"]' ).trigger( 'click.tmtabs' );
				}
			} else if ( this.options.selectedtab !== false ) {
				_selected_tab = parseInt( this.options.selectedtab, 10 );
				t.find( '.' + this.options.header + ':eq(' + _selected_tab + ')' ).trigger( 'click.tmtabs' );
			}
		},

		init: function() {
			var ithis = this;
			this.elements.each( function( index ) {
				var t = $( this );
				var display;
				var state = {};
				var backup = t.html();

				if ( ithis.backup[ index ] ) {
					t.html( ithis.backup[ index ] );
				} else {
					ithis.backup[ index ] = backup;
				}
				state.scrollPos = 0;
				state.scrollWidth = 0;
				display = t.css( 'display' );
				t.show();
				ithis.initElement( t, index, state, $.epoAPI.math.uniqueid( '' ) );
				t.css( 'display', display );
			} );
		}
	};

	$.fn.tcTabs = function( option ) {
		var methodReturn;
		var targets = $( this );
		var data = targets.data( 'tctabs' );
		var options;
		var ret;
		var args = $.makeArray( arguments );
		args.splice( 0, 1 );

		if ( typeof option === 'object' ) {
			options = option;
		} else {
			options = {};
		}

		if ( ! data ) {
			data = new Tabs( this, options );
			targets.data( 'tctabs', data );
		}

		if ( typeof option === 'string' ) {
			methodReturn = data[ option ].apply( data, args );
		}

		if ( methodReturn === undefined ) {
			ret = targets;
		} else {
			ret = methodReturn;
		}

		return ret;
	};

	$.fn.tcTabs.defaults = {
		headers: 'tm-tab-headers',
		headersWrap: 'tc-tab-headers-wrap',
		header: 'tab-header',
		slide: 'tc-tab-slide',

		classdown: 'tcfa-angle-down',
		classup: 'tcfa-angle-up',
		animationclass: 'appear',

		dataattribute: 'data-id',
		dataopenattribute: 'data-id',
		selectedtab: 'auto',
		showonhover: false,
		useclasstohide: true,
		sortabletabs: true,

		addbutton: false,
		addheader: 'tm-add-tab',
		addbuttonhtml: '<div class="tm-add-tab"><span class="tmicon tcfa tcfa-plus"></span></div>',
		afteraddtab: null,

		beforemovetab: null,
		aftermovetab: null,

		deletebutton: false,
		deletebuttonhtml: '<div class="tm-del-tab"><span class="tcfa tcfa-times"></span></div>',
		deleteheader: 'tm-del-tab',
		deleteconfirm: false,
		beforedeletetab: null,
		afterdeletetab: null,

		editbutton: false,
		editbuttonhtml: '<span class="tm-edit-tab"><span class="tcfa tcfa-edit"></span></span>',
		editheader: 'tm-edit-tab',
		oneditbutton: null,

		scroll: true,
		scrollDistance: 300,
		scrollDuration: 300
	};

	$.fn.tcTabs.Constructor = Tabs;

	$.fn.tmtabs = $.fn.tcTabs;
}( window.jQuery ) );
