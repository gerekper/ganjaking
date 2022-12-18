/**
 * The options cache of quick access widget in elementor.
 * 
 * @since 2.6.0
 */
var portoQuickAccessCache = {};
jQuery( document ).ready( function( $ ) {
	'use strict';
	if ( typeof elementorFrontend != 'undefined' ) {

		var porto_elementor_init = function() {
			if ( typeof elementor == 'undefined' ) {
				return;
			}

			/**
			 * Make quick access item.
			 * 
			 * @since 2.6.0
			 * @param {*} $qa_node 
			 * @param {*} qa_item 
			 * @param {*} widgetRect 
			 * @param {*} nodeRect 
			 */
			function make_qa_item( $qa_node, qa_item, widgetRect, nodeRect, nonPos = false ) {
				var label = '';
				if ( js_porto_vars && js_porto_vars.quick_access ) {
					label = js_porto_vars.quick_access;
				} else {
					label = 'Click to edit this element.';
				}
				var _temp = $( '<button aria-label="' + label + '" title="' + label + '" class="' + ( nonPos ? 'non-pos ' : '' ) + 'porto-qa-item position-absolute"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg><div class="region"></div></button>' );
				$qa_node.append( _temp.click( function( e ) {
					if ( elementor.getPanelView().currentPageName != 'editor' ) {
						elementor.selection.updatePanelPage();
					}
					parent.$e.routes.to( 'panel/editor/' + qa_item.tab, {
						model: elementor.selection.getElements()[0].model,
						view: elementor.selection.getElements()[0].view
					} );
					elementor.getPanelView().currentPageView.activateSection( qa_item.section );
					elementor.getPanelView().currentPageView._renderChildren();

					if ( qa_item.class ) { // not section
						var $panelSide = parent.jQuery( '#elementor-panel-content-wrapper' );
						var $particularOption = parent.document.querySelector( qa_item.class + ':not(.elementor-hidden-control)' );
						if ( $particularOption ) {
							$panelSide.animate( { scrollTop: $particularOption.offsetTop } );
							$particularOption.classList.add( 'show-qa-option' );
							setTimeout( function() {
								if ( $particularOption ) {
									$particularOption.classList.remove( 'show-qa-option' );
								}
							}, 3000 );
						}
					}
				} ).css( { top: ( nodeRect.top - widgetRect.top - 15 ), left: ( nodeRect.left - widgetRect.left - 15 ) } ) );
				_temp.find( '.region' ).css( { width: nodeRect.width, height: nodeRect.height } );
			}

			/**
			 * Quick Access in Elementor Preview.
			 * 
			 * @since 2.6.0
			 */
			( function quick_access() {
				$( document.body ).on( 'mouseenter', '.elementor-element-editable.elementor-element', function() {

					var $this = $( this ), elementType = $this.data( 'element_type' ), widgetType, widgetControls;
					if ( elementType == 'widget' ) {
						widgetType = $this.data( 'widget_type' );
						if ( widgetType ) {
							widgetType = widgetType.slice( 0, -8 ); // because of .default: porto_price_boxes.default
							if ( elementor.widgetsCache[widgetType] ) {
								widgetControls = elementor.widgetsCache[widgetType].controls;
							}
						}
					} else { // section, column, container
						if ( elementor.config.elements[elementType] ) {
							widgetControls = elementor.config.elements[elementType].controls;
						}
					}
					var widgetKey = elementType == 'widget' ? widgetType : elementType;
					if ( !portoQuickAccessCache[widgetKey] && typeof widgetControls == 'object' ) {
						Object.keys( widgetControls ).forEach( function( controlName ) {
							var widgetControl = widgetControls[controlName];
							if ( widgetControl.qa_selector ) {
								if ( !portoQuickAccessCache[widgetKey] ) {
									portoQuickAccessCache[widgetKey] = [];
								}
								if ( typeof widgetControl.name == 'string' && widgetControl.responsive ) {
									var sliceName = widgetControl.name.slice( -7 );
									if ( sliceName == '_mobile' || sliceName == '_tablet' ) {
										return;
									}
								}
								var controlInfo = {
									selector: widgetControl.qa_selector,
									tab: widgetControl.tab,
									section: widgetControl.name
								};
								if ( widgetControl.type != 'section' ) {
									controlInfo.section = widgetControl.section;
									controlInfo.class = '.elementor-control-' + widgetControl.name;
								}
								portoQuickAccessCache[widgetKey].push( controlInfo );
							}
						} );
					}
					if ( portoQuickAccessCache[widgetKey] ) {
						$this.append( '<div class="porto-quick-access"></div>' );
						var $qa_node = $this.find( '.porto-quick-access' ), widgetRect = this.getBoundingClientRect();
						var nonPosElements = [];
						portoQuickAccessCache[widgetKey].forEach( function( qa_item ) {
							var $el = $this.find( qa_item.selector );
							if ( $el.length ) {
								$el.each( function() {
									var nodeRect = this.getBoundingClientRect(), hasNon = false;
									if ( ( nodeRect.width == 0 && nodeRect.height == 0 ) ) { // display: none;
										nonPosElements.push( [this, qa_item] );
										return;
									}
									if ( $( this ).closest( 'li.has-sub>.popup' ).length ) {
										nonPosElements.push( [this, qa_item] );
										hasNon = true;
									}
									// li.has-sub>.popup => display: block 
									make_qa_item( $qa_node, qa_item, widgetRect, nodeRect, hasNon );
								} );
							}
						} );
						if ( widgetKey == 'porto_sidebar_menu' || widgetKey == 'porto_hb_menu' ) {
							if ( nonPosElements.length ) {
								$this.on( 'mouseenter', '.elementor-widget-container>ul>li.has-sub, .sidebar-menu>li.has-sub, .sidebar-menu .narrow .inner>.sub-menu>li.menu-item-has-children', function( e ) {
									widgetRect = $this.get( 0 ).getBoundingClientRect();
									var $related = $( e.relatedTarget );
									var $focusEl = $( this );
									if ( $related.closest( '.porto-qa-item.non-pos' ).length ) {
										if ( $focusEl.hasClass( 'menu-item-has-children sub' ) ) {
											$focusEl.find( '>.sub-menu' ).css( 'display', '' );
										}
										return;
									}
									if ( $focusEl.hasClass( 'menu-item-has-children sub' ) && $focusEl.find( '>.sub-menu' ).css( 'display' ) == 'block' && !$focusEl.is( ':hover' ) ) {
										return true;
									}
									nonPosElements.forEach( function( nonPosElement ) {
										if ( $focusEl.hasClass( 'menu-item-has-children sub' ) ) {
											if ( !$.contains( $focusEl.get( 0 ), nonPosElement[0] ) ) {
												return;
											}
											if ( !( nonPosElement[0].classList && nonPosElement[0].classList.contains( 'sub-menu' ) ) ) {
												return;
											}
										}
										var nodeRect = nonPosElement[0].getBoundingClientRect();
										if ( nodeRect.width == 0 && nodeRect.height == 0 ) { // display: none;
											return;
										}
										make_qa_item( $qa_node, nonPosElement[1], widgetRect, nodeRect, true );
									} );
								} ).on( 'mouseleave', '.elementor-widget-container>ul>li.has-sub, .sidebar-menu>li.has-sub', function( e ) {
									var $related = $( e.relatedTarget );
									if ( $related.closest( '.porto-qa-item.non-pos' ).length ) {
										$( this ).addClass( 'open' ).find( '.popup' ).css( 'display', 'block' );
										$( this ).find( '.popup>.inner>.sub-menu>li>.sub-menu' ).css( 'display', 'block' );
										return;
									}
									$( this ).removeClass( 'open' );
									$( this ).find( '.popup>.inner>.sub-menu>li>.sub-menu' ).css( 'display', '' );
									var $nonPosNode = $this.find( '.porto-qa-item.non-pos' );
									if ( $nonPosNode.length ) {
										$nonPosNode.remove();
									}
								} );
							}
						}
					}
				} ).on( 'mouseleave', '.elementor-element-editable.elementor-element', function() {
					var $quickAccess = $( this ).find( '.porto-quick-access' );
					if ( $quickAccess.length ) {
						$quickAccess.remove();
					}
					$( this ).off( 'mouseenter mouseleave' );
				} );
				if ( typeof parent.$e != 'undefined' ) {
					parent.$e.commands.on( 'run:before', function( component, command, args ) {
						if ( 'document/elements/toggle-selection' == command && args && args.container ) {
							$( '.elementor-element-editable.elementor-element' ).off( 'mouseenter mouseleave' );
							$( '.porto-quick-access' ).remove();
						}
					} );
				}
			} )();

			/**
			 * Go to Type Builder in Posts Grid Widget.
			 * 
			 * @since 2.6.0
			 */
			( function goToBuilder() {
				$( document.body ).on( 'mouseenter', '[data-tb-id] .porto-tb-item', function() {
					var $this = $( this ), label = '';
					if ( js_porto_vars && js_porto_vars.goto_type ) {
						label = js_porto_vars.goto_type;
					} else {
						label = 'Go To Type Builder';
					}
					$this.append( '<div class="overlay-slidetop"><a href="#" title="' + label + '" aria-label="' + label + '"><i class="fas fa-arrow-right"></i></a></div>' );
				} ).on( 'mouseleave', '[data-tb-id] .porto-tb-item', function() {
					$( this ).find( '.overlay-slidetop' ).remove();
				} );
				$( document.body ).on( 'click', '.porto-tb-item .overlay-slidetop a', function( e ) {
					e.preventDefault();
					e.stopPropagation();
					var _body = parent.document.body;
					var _jQuery = parent.jQuery;
					var src = js_porto_vars.ajax_url.slice( 0, -14 ) + 'post.php?post=' + $( this ).closest( '[data-tb-id]' ).attr( 'data-tb-id' ) + '&action=edit';
					var _modal = _jQuery( _body ).find( '.porto-tb-modal' );
					if ( !_modal.length ) {
						_jQuery( _body ).append( '<div class="porto-tb-modal"><iframe id="porto-tb-iframe" src="' + src + '"></iframe></div>' );
						_modal = _jQuery( _body ).find( '.porto-tb-modal' );
						_modal.find( '#porto-tb-iframe' ).on( 'load', function() {
							$( this.contentWindow.document.body ).addClass( 'porto-tb-preview' );
							_modal.show();
						} );
						_modal.click( function( e ) {
							// if ( e.which == 13 ) {
							$( this ).hide();
							// }
							if ( elementor.selection.getElements()[0] && elementor.selection.getElements()[0].model ) {
								var curModel = elementor.selection.getElements()[0].model;
								if ( curModel.attributes.widgetType && ['porto_posts_grid', 'porto_cp_linked', 'porto_sb_archives', 'porto_archive_posts_grid', 'porto_single_related'].indexOf( curModel.attributes.widgetType ) > -1 ) {
									elementor.selection.getElements()[0].model.renderRemoteServer();
								}
							}
						} );
					} else {
						if ( src != _modal.find( 'iframe' ).attr( 'src' ) ) {
							_modal.find( 'iframe' ).attr( 'src', src );
						} else {
							_modal.show();
						}
					}
				} );
			} )();

			function porto_gcd( a, b ) {
				if ( typeof a == 'undefined' ) {
					return false;
				}
				if ( Array.isArray( a ) ) {
					var len = a.length;
					if ( 1 === len ) {
						return a[0];
					}
					if ( 2 === len ) {
						return porto_gcd( a[0], a[1] );
					} else if ( len > 2 ) {
						return porto_gcd( a.pop(), porto_gcd( a ) );
					}
				} else {
					var max = Math.max( a, b ),
						min = Math.min( a, b ),
						rem = max % min;
					max = min;
					min = rem;
					if ( 0 === rem ) {
						return max;
					} else {
						return porto_gcd( max, min );
					}
				}
			}

			function porto_lcm( a, b ) {
				if ( Array.isArray( a ) ) {
					var len = a.length;
					if ( 1 === len ) {
						return a[0];
					}
					if ( 2 === len ) {
						return porto_lcm( a[0], a[1] );
					} else {
						return porto_lcm( a.pop(), porto_lcm( a ) );
					}
				} else {
					return ( a * b ) / porto_gcd( a, b );
				}
			}

			function initSlider( $el ) {
				if ( !$.fn.themeCarousel ) {
					return;
				}
				$el.removeData( '__carousel' );
				$el.data( 'owl.carousel' ) && $el.owlCarousel( 'destroy' );
				$el.children( '.owl-item' ).remove();
				$el.themeCarousel( $el.data( 'plugin-options' ) );
			}

			// init variables
			var refresh_timer = null,
				refresh_timer1 = null;

			elementorFrontend.hooks.addAction( 'porto_elementor_element_after_add', function( item ) {
				var $this = $( '.elementor-element-' + item.id ),
					$row = $this.closest( '.elementor-row, .elementor-container' ),
					$column = 'widget' == item.elType ? $this.closest( '.elementor-widget-wrap' ) : false;
				if ( 'widget' == item.elType && $column.hasClass( 'owl-carousel' ) ) {
					initSlider( $column );
				} else if ( 'column' == item.elType && $row.hasClass( 'owl-carousel' ) ) { // carousel
					$row.trigger( 'add.owl.carousel', $this );
					$row.trigger( 'refresh.owl.carousel', $this );
				} else if ( 'column' == item.elType && typeof $row.attr( 'data-plugin-masonry' ) != 'undefined' ) { // isotope
					porto_init_creative_layout( $row );
					if ( !( $this.get( 0 ) instanceof HTMLElement ) ) {
						Object.setPrototypeOf( $this.get( 0 ), HTMLElement.prototype );
					}
					$this.addClass( 'porto-grid-item' );
					$row.removeData( '__masonry' );
					if ( $row.data( 'isotope' ) ) {
						$row.isotope( 'destroy' );
					}
					$row.themeMasonry( $row.data( 'plugin-options' ) );
				}

				var $obj = $this.closest( '.mouse-hover-split' );
				if ( $obj.length && $.fn.themePluginHoverSplit ) {
					var ins = $obj.data( '__mousehoversplit' );
					if ( ins ) {
						ins.clearData();
					}
					$obj.themePluginHoverSplit();
				}
			} );

			elementorFrontend.hooks.addAction( 'porto_elementor_element_after_move', function( id ) {
				var $obj = elementor.$previewContents.find( '.section-tabs [data-id="' + id + '"' );
				if ( $obj.length ) {
					//Column Move for Section Tab
					var $container = $obj.closest( '.elementor-container' ),
						$tab_nav = $container.find( '>ul.nav' );
					porto_section_tab_refresh( $tab_nav, $container );
				}

				var $column = $( '.mouse-hover-split [data-id="' + id + '"' );
				if ( $column.length && $.fn.themePluginHoverSplit ) {
					$obj = $column.closest( '.mouse-hover-split' );

					if ( $column.find( '>.split-slide' ).length ) {
						$column.addClass( 'split-slide' );
					}
					var ins = $obj.data( '__mousehoversplit' );
					if ( $obj.length && ins ) {
						ins.clearData();
					}
					$obj.themePluginHoverSplit();
				}
			} );

			elementorFrontend.hooks.addAction( 'porto_elementor_element_after_delete', function( containers ) {
				containers.forEach( function( cnt ) {
					var $tab_nav_item = elementor.$previewContents.find( '.section-tabs li[pane-id="' + cnt.model.id + '"' );
					if ( $tab_nav_item.length ) {
						//remove tab nav
						var $tab_nav = $tab_nav_item.closest( 'ul.nav' ),
							$tab_content = elementor.$previewContents.find( '.section-tabs >.tab-content' ),
							$is_active = $tab_nav_item.hasClass( 'active' ),
							$container = $tab_nav_item.closest( '.elementor-container' );
						$tab_nav_item.remove();
						if ( $is_active ) {
							var $first_nav = $tab_nav.find( '.nav-item' ).eq( 0 );
							$first_nav.addClass( 'active' );
							$first_nav.find( '.nav-link' ).addClass( 'active' );
							$tab_content.find( '>div' ).eq( 0 ).addClass( 'active' );
						}
						porto_section_tab_refresh( $tab_nav, $container );
					}

					var $parent = $( '[data-id="' + cnt.parent.id + '"' ),
						$obj = $parent.find( '.mouse-hover-split' );
					if ( $obj.length && $.fn.themePluginHoverSplit ) {
						var ins = $obj.data( '__mousehoversplit' );
						if ( ins ) {
							ins.clearData();
						}
						$obj.themePluginHoverSplit();
					}

					// Horizontal Scroller
					var $parent = $( '[data-id="' + cnt.parent.id + '"' ),
						$hScroller = $parent.find( '.horizontal-scroller-wrapper' );
					if ( $hScroller.length && $.fn.themePluginHScroller ) {
						var ins = $hScroller.data( '__horizontalscroller' );
						if ( ins ) {
							ins.clearData();
						}
						$hScroller.themePluginHScroller();
					}
				} );
			} );

			elementorFrontend.hooks.addAction( 'porto_elementor_element_after_duplicate', function( containers ) {
				containers.forEach( function( cnt ) {
					var $obj = elementor.$previewContents.find( '.section-tabs [data-id="' + cnt.model.id + '"' );
					// Column Dupplicated Tab
					if ( $obj.length ) {
						var $container = $obj.closest( '.elementor-container' ),
							$tab_nav = $container.find( '>ul.nav' );
						porto_section_tab_refresh( $tab_nav, $container );
					}

					var $column = $( '.mouse-hover-split [data-id="' + cnt.model.id + '"' );
					if ( $column.length && $.fn.themePluginHoverSplit ) {
						$obj = $column.closest( '.mouse-hover-split' );
						var ins = $obj.data( '__mousehoversplit' );
						if ( ins ) {
							ins.clearData();
						}
						$obj.themePluginHoverSplit();
					}
				} );
			} );

			function porto_section_tab_refresh( $tab_nav, $container ) {
				$tab_nav.empty();
				$container.find( '>.tab-content > div' ).each( function() {
					var $pane = $( this ),
						tab_id = $pane.data( 'id' ),
						$widget_wrap = $pane.find( '.elementor-widget-wrap' ),
						icon = $widget_wrap.data( 'tab-icon' ) ? $widget_wrap.data( 'tab-icon' ) : '',
						nav_title = $widget_wrap.data( 'tab-title' ) ? $widget_wrap.data( 'tab-title' ) : porto_elementor_vars.section_tab_title,
						icon_pos = $widget_wrap.data( 'tab-pos' ) ? $widget_wrap.data( 'tab-pos' ) : '',
						html = '';

					$pane.addClass( 'tab-pane' );
					$pane.attr( 'id', 'tab-' + tab_id );

					// active first tab content
					if ( 0 == $pane.index() ) {
						$pane.addClass( 'active' );
					} else if ( $pane.hasClass( 'active' ) ) {
						$pane.removeClass( 'active' );
					}

					if ( icon ) {
						html += '<i class="' + icon + '"></i>';
					}
					html += nav_title;
					$tab_nav.append( '<li class="nav-item ' + ( ( icon && icon_pos ) ? 'nav-icon-' + icon_pos : '' ) + '" pane-id="' + tab_id + '"><a class="nav-link" data-tab="tab-' + tab_id + '">' + html + '</a></li>' );
				} );
			}

			function porto_init_creative_layout( $obj ) {
				var index = $obj.data( 'layout' );
				$obj.children( '.elementor-column' ).addClass( 'porto-grid-item' );
				if ( index ) { // preset layout
					if ( typeof porto_elementor_vars.creative_layouts[parseInt( index, 10 )] == 'undefined' ) {
						return;
					}
					var item_classes = porto_elementor_vars.creative_layouts[Number( index )];
					$obj.children( '.elementor-column' ).each( function( i ) {
						if ( typeof item_classes[i % item_classes.length] != 'undefined' ) {
							var current_classes = $( this ).attr( 'class' ).split( ' ' ),
								new_classes = item_classes[i % item_classes.length];
							for ( var j = 0; j < current_classes.length; j++ ) {
								var c = $.trim( current_classes[j] );
								if ( c && c.indexOf( 'grid-' ) === -1 ) {
									new_classes += ' ' + c;
								}
							}
							new_classes = new_classes.replace( ' porto-grid-item', '' );
							$( this ).attr( 'class', new_classes + ' porto-grid-item' );
						}
					} );
					if ( $obj.prev( 'style[data-id="' + escape( index ) + '"]' ).length < 1 ) {
						var st = '.elementor-element.elementor-element-' + $obj.closest( '.elementor-section' ).data( 'id' );
						$.ajax( {
							url: theme.ajax_url,
							data: {
								action: 'porto_load_creative_layout_style',
								nonce: js_porto_vars.porto_nonce,
								layout: index,
								grid_height: $obj.data( 'grid-height' ),
								spacing: $obj.data( 'spacing' ),
								selector: st
							},
							type: 'post',
							success: function( res ) {
								$obj.prev( 'style' ).remove();
								$( res ).insertBefore( $obj );
								if ( $obj.hasClass( 'elementor-container' ) && $obj.closest( '.elementor-section' ).hasClass( 'elementor-section-boxed' ) ) {
									var css = st + ' .grid-creative{max-width:' + ( Number( porto_elementor_vars.container_width ) - Number( porto_elementor_vars.grid_spacing ) + Number( $obj.data( 'spacing' ) ) ) + 'px}';
									css += '@media (min-width: 992px) and (max-width: ' + ( Number( porto_elementor_vars.container_width ) + Number( porto_elementor_vars.grid_spacing ) - 1 ) + 'px){';
									css += st + ' .grid-creative{max-width:' + ( 960 - Number( porto_elementor_vars.grid_spacing ) + Number( $obj.data( 'spacing' ) ) ) + 'px}';
									css += '}';
									$obj.prev( 'style' ).prepend( css );
								}
								$obj.isotope( 'layout' );
							}
						} );
					}
				} else if ( !$obj.hasClass( 'porto-preset-layout' ) ) { // normal
					var fractions = [],
						denominators = [],
						numerators = [];
					$obj.children().each( function() {
						if ( $( this ).hasClass( 'grid-col-sizer' ) ) {
							return;
						}
						var percent_w = $( this ).children( '.elementor-column-wrap, .elementor-widget-wrap' ).data( 'width' );
						if ( percent_w && percent_w.size ) {
							var arr;
							percent_w = percent_w.size;
							if ( parseFloat( parseInt( percent_w, 10 ) ) === parseFloat( percent_w ) ) { // integer
								arr = [percent_w, 1];
							} else {
								for ( var index = 2; index <= 100; index++ ) {
									var r_w = ( percent_w * index ).toFixed( 1 );
									if ( parseFloat( parseInt( r_w, 10 ) ) === r_w ) { //integer
										var gcd = porto_gcd( r_w, index );
										arr = [r_w / gcd, index / gcd];
									}
								}

								if ( typeof arr == 'undefined' ) {
									percent_w = Math.floor( percent_w * 10 );
									var gcd = porto_gcd( percent_w, 10 );
									arr = [percent_w / gcd, 10 / gcd];
								}
							}
							if ( typeof arr != 'undefined' && -1 === fractions.indexOf( arr ) ) {
								fractions.push( arr );
								numerators.push( arr[0] );
								denominators.push( arr[1] );
							}
						}
					} );

					if ( fractions.length ) {
						var deno_lcm = porto_lcm( denominators ),
							num_gcd = porto_gcd( numerators ),
							unit_num = ( num_gcd / deno_lcm ).toFixed( 4 );
						if ( unit_num >= 0.1 ) {
							$obj.children( '.grid-col-sizer' ).css( { width: unit_num + '%', flex: '0 0 ' + unit_num + '%' } );
						}
					}

					if ( $obj.prev( 'style' ).length < 1 ) {
						$( '<style></style>' ).insertBefore( $obj )
					}
					var st = '.elementor-element.elementor-element-' + $obj.closest( '.elementor-section' ).data( 'id' ),
						css = '@media (min-width: 992px) and (max-width: ' + ( Number( porto_elementor_vars.container_width ) + Number( porto_elementor_vars.grid_spacing ) - 1 ) + 'px){';
					css += st + ' > .elementor-container{max-width:' + ( 960 - Number( porto_elementor_vars.grid_spacing ) + Number( $obj.data( 'spacing' ) ) ) + 'px}';
					css += '}';
					$obj.prev( 'style' ).html( css );

					if ( $obj.data( 'isotope' ) ) {
						$obj.isotope( 'layout' );
					}
				}
			}

			$( '.elementor-row[data-plugin-masonry], .elementor-container[data-plugin-masonry]' ).children( '.elementor-column' ).each( function() {
				if ( !( this instanceof HTMLElement ) ) {
					Object.setPrototypeOf( this, HTMLElement.prototype );
				}
			} );
			/*$('.elementor-row[data-plugin-masonry]').each(function() {
				porto_init_creative_layout($(this));
				$(this).addClass('porto-init');
			});*/

			if ( typeof porto_init == 'function' ) {
				elementorFrontend.hooks.addAction( 'porto_elementor_element_before_delete', function( item ) {
					var $this = $( '.elementor-element-' + item.id ),
						$row = $this.closest( '.elementor-row, .elementor-container' ),
						$column = 'widget' == item.attributes.elType ? $this.closest( '.elementor-widget-wrap' ) : false;
					if ( 'widget' == item.attributes.elType && $column.hasClass( 'owl-carousel' ) ) {
						theme.requestFrame( function() {
							initSlider( $column );
						} );
					} else if ( 'column' == item.attributes.elType && $row.hasClass( 'owl-carousel' ) ) { // carousel
						var index = $this.parent( '.owl-item:not(.cloned)' ).index() - ( $row.find( '.owl-item.cloned' ).length / 2 );
						$row.trigger( 'remove.owl.carousel', index );
						$row.trigger( 'refresh.owl.carousel', $this );
					} else if ( 'column' == item.attributes.elType && typeof $row.attr( 'data-plugin-masonry' ) != 'undefined' && $row.data( 'isotope' ) ) { // isotope
						porto_init_creative_layout( $row );
						$row.isotope( 'remove', $this ).isotope( 'layout' );
					} else if ( window.porto_cursor_effects && window.porto_cursor_effects.length ) {
						window.porto_cursor_effects.forEach( function( i, index ) {
							if ( i.id && 'cursor-element-' + item.id == i.id ) {
								window.porto_cursor_effects.splice( index, 1 );

								var ins = $( document.body ).data( '__cursorEffect' );
								if ( ins ) {
									ins.destroy();
									$( document.body ).removeData( '__cursorEffect' );

									if ( window.porto_cursor_effects.length && $.fn.themePluginCursorEffect ) {
										$( document.body ).themePluginCursorEffect();
									}
								}

								return false;
							}
						} );
					}
				} );

				var porto_widgets = ['porto_blog.default', 'wp-widget-recent_posts-widget.default', 'wp-widget-recent_portfolios-widget.default', 'porto_products.default', 'porto_sb_products.default', 'porto_product_categories.default', 'porto_recent_posts.default', 'shortcode.default', 'porto_portfolios.default', 'porto_button.default', 'porto_ultimate_heading.default', 'porto_recent_members.default', 'porto_recent_portfolios.default', 'porto_circular_bar.default', 'porto_cp_related.default', 'porto_cp_upsell.default', 'porto_image_gallery.default', 'porto_posts_grid.default', 'porto_archive_posts_grid.default', 'porto_single_related.default', 'porto_cp_linked.default', 'porto_faqs.default', 'porto_single_image.default', 'porto_members.default', 'porto_portfolios_category.default'];
				$.each( porto_widgets, function( key, element_name ) {
					elementorFrontend.hooks.addAction( 'frontend/element_ready/' + element_name, function( $obj ) {
						var $iso_obj = $obj.find( '[data-plugin-masonry]' ).length ? $obj.find( '[data-plugin-masonry]' ) : $obj.find( '.posts-masonry .posts-container:not(.manual)' );
						if ( !$iso_obj.length ) {
							$iso_obj = $obj.find( '.page-members .member-row:not(.manual)' );
						}
						if ( !$iso_obj.length ) {
							$iso_obj = $obj.find( '.page-portfolios .portfolio-row:not(.manual)' );
						}
						if ( $iso_obj.length ) {
							$iso_obj.children().each( function() {
								if ( !( this instanceof HTMLElement ) ) {
									if ( 'shortcode.default' == element_name && $iso_obj.data( 'isotope' ) ) {
										$iso_obj.isotope( 'destroy' );
									}
									Object.setPrototypeOf( this, HTMLElement.prototype );
								}
							} );
						}
						porto_init( $obj );

						// hover3d effect
						if ( $.isFunction( $.fn['hover3d'] ) && $obj.find( '.hover-effect-3d' ).length ) {
							$obj.find( '.hover-effect-3d' ).filter( function() {
								if ( $( this ).closest( '.owl-carousel' ).length ) {
									return false;
								}
								return true;
							} ).one( 'mouseover.trigger.hover3d', function() {
								$( this ).each( function() {
									var $this = $( this );

									$this.hover3d( {
										selector: $this.data( 'hover3d-selector' )
									} );
								} );
							} );


							$obj.find( '.owl-carousel' ).filter( function() {
								if ( $( this ).find( '.hover-effect-3d' ).length ) {
									return true;
								}
								return false;
							} ).on( 'initialized.owl.carousel', function() {
								$( this ).find( '.hover-effect-3d' ).one( 'mouseover.trigger.hover3d', function() {
									$( this ).each( function() {
										var $this = $( this );

										$this.hover3d( {
											selector: $this.data( 'hover3d-selector' )
										} );
									} );
								} );
							} );
						}

						// hoverdir effect
						if ( $.isFunction( $.fn['hoverdir'] ) && $obj.find( '.hover-effect-dir' ).length ) {
							var init_hoverdir = function( $dir_obj ) {
								if ( !$dir_obj.length ) {
									return;
								}
								var pluginOptions = $dir_obj.data( 'plugin-options' );

								pluginOptions = $.extend( true, {}, {
									speed: 300,
									easing: 'ease',
									hoverDelay: 0,
									inverse: false,
									hoverElem: '.fill'
								}, pluginOptions );

								$dir_obj.each( function() {
									$( this ).hoverdir( pluginOptions );
								} );
							};

							init_hoverdir( $obj.find( '.hover-effect-dir' ).filter( function() {
								if ( $( this ).closest( '.owl-carousel' ).length ) {
									return false;
								}
								return true;
							} ) );

							$obj.find( '.owl-carousel' ).filter( function() {
								if ( $( this ).find( '.hover-effect-dir' ).length ) {
									return true;
								}
								return false;
							} ).on( 'initialized.owl.carousel', function() {
								init_hoverdir( $( this ).find( '.owl-item' ) );
							} );
						}
					} );
				} );

				elementorFrontend.hooks.addAction( 'frontend/element_ready/section', function( $obj ) {
					var $row = $obj.find( '> .elementor-container > .elementor-row' );
					if ( !$row.length ) {
						$row = $obj.children( '.elementor-container' )
					}
					if ( $row.hasClass( 'porto-carousel' ) ) {
						var $carousel = $obj.find( '> .elementor-container > .porto-carousel, > .porto-carousel' );
						if ( $carousel.data( 'owl.carousel' ) ) {
							$carousel.trigger( 'refresh.owl.carousel' );
						} else {
							$carousel.themeCarousel( $carousel.data( 'plugin-options' ) );
						}
						setTimeout( function() {
							$carousel.trigger( 'refresh.owl.carousel' );
						}, 150 );
					} else if ( typeof $row.attr( 'data-plugin-masonry' ) != 'undefined' ) {
						var $iso_obj = $row;
						$iso_obj.children().each( function() {
							if ( !( this instanceof HTMLElement ) ) {
								Object.setPrototypeOf( this, HTMLElement.prototype );
							}
						} );
						if ( 0 === $iso_obj.children( '.grid-col-sizer' ).index() ) {
							$iso_obj.children( '.grid-col-sizer' ).appendTo( $iso_obj );
						}
						if ( !$iso_obj.hasClass( 'porto-init' ) ) {
							porto_init_creative_layout( $iso_obj );
							$iso_obj.themeMasonry( $iso_obj.data( 'plugin-options' ) );
							$iso_obj.addClass( 'porto-init' );
						} else if ( $iso_obj.data( 'isotope' ) ) {
							$iso_obj.isotope( 'layout' );
						}
					}

					if ( $row.data( 'add_container' ) ) {
						$row.children( '.elementor-column' ).filter( function() {
							if ( $( this ).children( '.porto-ibanner-layer' ).length || $( this ).children().children( '.porto-ibanner-layer' ).length ) {
								return true;
							}
							return false;
						} ).addClass( 'container' );
					}

					if ( $row.hasClass( 'porto-parallax' ) ) {
						var speed = $row.data( 'parallax-speed' );
						var parallaxType = '';
						if ( $row.attr( 'data-parallax-type' ) ) {
							$obj.attr( 'data-parallax-type', 'horizontal' );
							parallaxType = 'horizontal';
						} else {
							$obj.removeAttr( 'data-parallax-type' );
						}
						var opts = { speed: speed },
							parallaxScale = $row.attr( 'data-parallax-scale' ),
							instance = $obj.data( '__parallax' );
						if ( typeof parallaxScale !== 'undefined' ) {
							opts['scale'] = true;
							if ( parallaxScale == 'invert' ) {
								opts['scaleInvert'] = true;
							} else {
								opts['scaleInvert'] = false;
							}
						} else {
							opts['scale'] = opts['scaleInvert'] = false;
						}
						if ( instance && instance.options ) {
							var old_speed = instance.options.speed,
								old_parallax_type = instance.options.parallaxType || '',
								old_parallax_scale = instance.options.scale,
								old_parallax_scaleInvert = instance.options.scaleInvert;
							if ( parseFloat( old_speed ) !== parseFloat( speed ) || ( parallaxType !== old_parallax_type ) || ( opts['scale'] !== old_parallax_scale ) || ( opts['scaleInvert'] !== old_parallax_scaleInvert ) ) {
								instance.disable();
								$obj.removeData( '__parallax' );
								$obj.themeParallax( opts );
							}
						} else {
							$obj.themeParallax( opts );
						}
					}

					var $container = $obj.find( '>.elementor-container' );
					if ( $container.hasClass( 'tabs' ) ) {
						var nav_object = $container.find( '>ul.nav' );
						if ( $container.hasClass( 'after-nav' ) ) {
							$container.append( nav_object );
						}
					}

					// Hover Split
					var $splitWrapper = $obj.find( '.mouse-hover-split' );
					if ( $splitWrapper.length && $.fn.themePluginHoverSplit ) {
						$splitWrapper.themePluginHoverSplit();
					}

					// Horizontal Scroller
					var $hScroller = $obj.find( '.horizontal-scroller-wrapper' );
					if ( $hScroller.length && $.fn.themePluginHScroller ) {
						$hScroller.siblings().appendTo( $hScroller.find( '.horizontal-scroller-items' ) );
						$hScroller.themePluginHScroller();
					}

					initWidgetAddon( $obj );
				} );

				elementorFrontend.hooks.addAction( 'frontend/element_ready/column', function( $obj ) {
					var $row = $obj.closest( '.elementor-row, .elementor-container' );
					if ( $obj.find( '> .elementor-column-wrap > .porto-carousel, > .porto-carousel' ).length ) {
						var $carousel = $obj.find( '> .elementor-column-wrap > .porto-carousel, > .porto-carousel' );
						if ( $carousel.data( 'owl.carousel' ) ) {
							$carousel.trigger( 'refresh.owl.carousel' );
						} else {
							$carousel.themeCarousel( $carousel.data( 'plugin-options' ) );
						}
					}

					var $column_wrap = $obj.children( '.elementor-column-wrap, .elementor-widget-wrap' ),
						$widget_wrap = $obj.children( '.elementor-widget-wrap' );
					if ( !$widget_wrap.length ) {
						$widget_wrap = $column_wrap.children( '.elementor-widget-wrap' );
					}
					if ( $column_wrap.data( 'cont_cls' ) ) {
						$obj.addClass( $column_wrap.data( 'cont_cls' ) );
					}
					if ( $row.hasClass( 'owl-carousel' ) ) {
						if ( refresh_timer ) {
							clearTimeout( refresh_timer );
						}
						refresh_timer = setTimeout( function() {
							$row.removeData( '__carousel' );
							$row.trigger( 'destroy.owl.carousel' );
							$row.themeCarousel( $row.data( 'plugin-options' ) );
						}, 100 );
					} else if ( typeof $row.attr( 'data-plugin-masonry' ) != 'undefined' ) {
						if ( refresh_timer ) {
							clearTimeout( refresh_timer );
						}
						refresh_timer = setTimeout( function() {
							porto_init_creative_layout( $row );
							$row.children().each( function() {
								if ( !( this instanceof HTMLElement ) ) {
									Object.setPrototypeOf( this, HTMLElement.prototype );
								}
							} );
							$row.removeData( '__masonry' );
							if ( $row.data( 'isotope' ) ) {
								$row.isotope( 'destroy' );
							}
							$row.themeMasonry( $row.data( 'plugin-options' ) );
						}, 100 );
					}

					if ( $obj.find( '> .porto-parallax' ).length ) {
						var $parallaxNode = $obj.find( '> .porto-parallax' );
						var speed = $parallaxNode.data( 'parallax-speed' );
						var parallaxType = '';
						if ( $parallaxNode.attr( 'data-parallax-type' ) ) {
							parallaxType = 'horizontal';
						}
						if ( $parallaxNode.data( '__parallax' ) && $parallaxNode.data( '__parallax' ).options ) {
							var old_speed = $parallaxNode.data( '__parallax' ).options.speed;
							var old_parallax_type = $parallaxNode.data( '__parallax' ).options.parallaxType || '';
							if ( parseFloat( old_speed ) !== parseFloat( speed ) || ( parallaxType !== old_parallax_type ) ) {
								$parallaxNode.removeData( '__parallax' );
							}
						}
						$parallaxNode.themeParallax( { speed: speed } );
					}

					if ( $widget_wrap.hasClass( 'porto-ibanner-layer' ) ) {
						if ( $widget_wrap.attr( 'data-wrap_cls' ) ) {
							var classList = $obj[0].classList;
							classList.forEach( function( item ) {
								if ( -1 != item.indexOf( 'porto-ibe-' ) ) {
									$obj.removeClass( item );
								}
							} );


							$obj.addClass( $widget_wrap.attr( 'data-wrap_cls' ) );
							$widget_wrap.removeAttr( 'data-wrap_cls' );
						}
						if ( $widget_wrap.attr( 'data-add_container' ) ) {
							$widget_wrap.removeAttr( 'data-add_container' ).wrap( '<div class="container"></div>' );
						}
					}
					if ( typeof $column_wrap.attr( 'data-appear-animation' ) != 'undefined' ) {
						$column_wrap.themeAnimate();
					} else if ( typeof $widget_wrap.attr( 'data-appear-animation' ) != 'undefined' ) {
						$widget_wrap.themeAnimate();
					}

					if ( $row.data( 'add_container' ) ) {
						if ( $widget_wrap.hasClass( 'porto-ibanner-layer' ) ) {
							$obj.addClass( 'container' );
						} else {
							$obj.removeClass( 'container' );
						}
					}

					if ( $column_wrap.hasClass( 'porto-sticky' ) ) {
						if ( $column_wrap.is( ':visible' ) ) {
							var pluginOptions = $column_wrap.attr( 'data-plugin-options' );
							if ( typeof pluginOptions == 'string' ) {
								try {
									pluginOptions = JSON.parse( pluginOptions.replace( /'/g, '"' ).replace( ';', '' ) );
								} catch ( e ) { }
							}
							$column_wrap.themeSticky( pluginOptions );
						}
					}

					var $widget_wrap = $obj.find( '> .elementor-column-wrap > .elementor-widget-wrap, > .elementor-widget-wrap' );
					if ( typeof $widget_wrap.attr( 'data-plugin-float-element' ) != 'undefined' ) {
						var opts = $widget_wrap.data( 'plugin-options' );
						if ( typeof opts == 'string' ) {
							try {
								opts = JSON.parse( opts.replace( /'/g, '"' ).replace( ';', '' ) );
							} catch ( e ) { }
						}
						$widget_wrap.themePluginFloatElement( opts );
					}

					var $container = $obj.closest( '.elementor-container' );
					if ( $container.hasClass( 'tabs' ) ) {
						var $content = $container.find( '>.tab-content' ),
							$tab_nav = $container.find( '>ul.nav' ),
							icon = $widget_wrap.data( 'tab-icon' ) ? $widget_wrap.data( 'tab-icon' ) : '',
							nav_title = $widget_wrap.data( 'tab-title' ) ? $widget_wrap.data( 'tab-title' ) : porto_elementor_vars.section_tab_title,
							icon_pos = $widget_wrap.data( 'tab-pos' ) ? $widget_wrap.data( 'tab-pos' ) : '',
							html = '',
							tab_id = $obj.data( 'id' );

						// insert Column to tab content
						if ( !$obj.parent().hasClass( 'tab-content' ) ) {
							$content.append( $obj );
						}
						$obj.addClass( 'tab-pane' );
						$obj.attr( 'id', 'tab-' + tab_id );
						// insert tab nav
						if ( icon ) {
							html += '<i class="' + icon + '"></i>';
						}

						html += nav_title;
						if ( $tab_nav ) {
							if ( $tab_nav.find( '[pane-id="' + tab_id + '"]' ).length ) {
								var $nav = $tab_nav.find( '[pane-id="' + $obj.data( 'id' ) + '"]' );
								$nav.removeClass( 'nav-icon-left nav-icon-up' );
								if ( icon && icon_pos ) {
									$nav.addClass( 'nav-icon-' + icon_pos );
								}
								$nav.find( 'a' ).html( html );
							} else {
								$tab_nav.append( '<li class="nav-item ' + ( ( icon && icon_pos ) ? 'nav-icon-' + icon_pos : '' ) + '" pane-id="' + tab_id + '"><a class="nav-link" href="#" data-tab="tab-' + tab_id + '">' + html + '</a></li>' );
							}

						}

						// active first column
						var $active_nav = $tab_nav.find( '.nav-item:first-child' ),
							$active_column = $content.children( 'div:first-child' );
						if ( $active_nav ) {
							$tab_nav.find( '.active' ).removeClass( 'active' )
							$active_nav.addClass( 'active' );
							$active_nav.children( '.nav-link' ).addClass( 'active' );
						}
						if ( $active_column ) {
							$content.find( '>.active' ).removeClass( 'active' );
							$active_column.addClass( 'active' );
						}
					}

					// Mouse Hover Split
					var $splitWrapper = $obj.parent(),
						$widget_wrap = $obj.find( '>.elementor-widget-wrap,>.elementor-column-wrap' ),
						ins = $splitWrapper.data( '__mousehoversplit' );
					if ( $widget_wrap.hasClass( 'split-slide' ) ) {
						if ( !$obj.hasClass( 'split-slide' ) ) {
							$obj.addClass( 'split-slide' );
							if ( $splitWrapper.hasClass( 'mouse-hover-split' ) ) {
								if ( ins ) {
									ins.clearData();
								}
								$splitWrapper.themePluginHoverSplit();
							}
						} else if ( ins ) {
							ins.clearData();
							$splitWrapper.themePluginHoverSplit();
						}
					} else {
						$obj.removeClass( 'split-slide slide-left slide-right' );
						if ( ins ) {
							ins.clearData();
							$splitWrapper.themePluginHoverSplit();
						}
					}

					// Horizontal Scroller
					var $hScroller = $obj.siblings( '.horizontal-scroller-wrapper' );
					if ( !$hScroller.length ) {
						$hScroller = $obj.closest( '.horizontal-scroller-wrapper' );
					}
					if ( $hScroller.length && $.fn.themePluginHScroller ) {
						var ins = $hScroller.data( '__horizontalscroller' );
						if ( ins ) {
							ins.clearData();
						}
						$hScroller.siblings().appendTo( $hScroller.find( '.horizontal-scroller-items' ) );
						$hScroller.themePluginHScroller();
					}

					initWidgetAddon( $obj );
				} );

				elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function( $obj ) {
					initWidgetAddon( $obj );
				} );
			}

			function initWidgetAddon( $obj ) {
				var widget_settings,
					editorElements = null,
					widgetData = {};

				if ( !window.elementor.hasOwnProperty( 'elements' ) ) {
					widget_settings = false;
				}

				editorElements = window.elementor.elements;

				if ( !editorElements.models ) {
					widget_settings = false;
				}

				$.each( editorElements.models, function( index, obj ) {
					if ( $obj.data( 'id' ) == obj.id ) {
						widgetData = obj.attributes.settings.attributes;
						return false;
					}

					$.each( obj.attributes.elements.models, function( index, obj ) {
						if ( $obj.data( 'id' ) == obj.id ) {
							widgetData = obj.attributes.settings.attributes;
							return false;
						}

						$.each( obj.attributes.elements.models, function( index, obj ) {
							if ( $obj.data( 'id' ) == obj.id ) {
								widgetData = obj.attributes.settings.attributes;
								return false;
							}

							$.each( obj.attributes.elements.models, function( index, obj ) {
								if ( $obj.data( 'id' ) == obj.id ) {
									widgetData = obj.attributes.settings.attributes;
									return false;
								}

								$.each( obj.attributes.elements.models, function( index, obj ) {
									if ( $obj.data( 'id' ) == obj.id ) {
										widgetData = obj.attributes.settings.attributes;
										return false;
									}
								} );

							} );
						} );

					} );
				} );

				var widget_settings = {
					mpx: widgetData['mouse_parallax'],
					mpx_inverse: widgetData['mouse_parallax_inverse'],
					mpx_speed: 'object' == typeof widgetData['mouse_parallax_speed'] && widgetData['mouse_parallax_speed']['size'] ? widgetData['mouse_parallax_speed']['size'] : 0.5,
				};

				if ( $obj.data( 'parallax' ) ) {
					$obj.parallax( 'disable' );
					$obj.removeData( 'parallax' );
					$obj.removeData( 'options' );
				}

				if ( $.fn.themePluginInViewportStyle ) {
					var instance = $obj.data( '__inviewportstyle' );
					if ( widgetData['scroll_inviewport'] == 'yes' ) {
						var scrollBg = widgetData['scroll_bg'],
							scrollBgInout = widgetData['scroll_bg_inout'],
							modTop = widgetData['scroll_top_mode'],
							modBottom = widgetData['scroll_bottom_mode'],
							extraPluginOptions = {},
							changed = false;
						if ( scrollBg ) {
							extraPluginOptions['styleIn'] = {
								'background-color': scrollBg,
							};
							if ( instance && scrollBg != instance.options.styleIn['background-color'] ) {
								changed = true;
							}
						}
						if ( scrollBgInout ) {
							extraPluginOptions['styleOut'] = {
								'background-color': scrollBgInout,
							};
							if ( instance && scrollBgInout != instance.options.styleOut['background-color'] ) {
								changed = true;
							}
						}
						if ( modTop ) {
							extraPluginOptions['modTop'] = '-' + modTop + 'px';
							if ( instance && modTop != instance.options.modTop ) {
								changed = true;
							}
						}
						if ( modBottom ) {
							extraPluginOptions['modBottom'] = '-' + modBottom + 'px';
							if ( instance && modBottom != instance.options.modBottom ) {
								changed = true;
							}
						}
						if ( instance ) {
							if ( changed ) {
								instance.disable();
								$obj.removeData( '__inviewportstyle' );
								$obj.themePluginInViewportStyle( extraPluginOptions );
							}
						} else {
							$obj.themePluginInViewportStyle( extraPluginOptions );
						}
					} else if ( instance ) {
						instance.disable();
						$obj.removeData( '__inviewportstyle' );
					}
				}

				if ( 'object' == typeof widget_settings && widget_settings.mpx ) {
					$obj.attr( 'data-plugin', 'mouse-parallax' );

					var settings = {},
						opts;

					if ( 'yes' == widget_settings['mpx_inverse'] ) {
						settings['invertX'] = true;
						settings['invertY'] = true;
					} else {
						settings['invertX'] = false;
						settings['invertY'] = false;
					}

					$obj.attr( 'data-options', JSON.stringify( settings ) );
					$obj.attr( 'data-floating-depth', widget_settings['mpx_speed'] );

					if ( $obj.hasClass( 'elementor-element' ) ) {
						$obj.children( '.elementor-widget-container, .elementor-container, .elementor-widget-wrap, .elementor-column-wrap' ).addClass( 'layer' ).attr( 'data-depth', $obj.attr( 'data-floating-depth' ) );
					} else {
						$obj.children( '.layer' ).attr( 'data-depth', $obj.attr( 'data-floating-depth' ) );
					}

					var pluginOptions = $obj.data( 'options' );
					if ( pluginOptions )
						opts = pluginOptions;

					if ( $.fn.parallax ) {
						new theme.Mouseparallax( $obj, opts );
					} else {
						if ( porto_elementor_vars.js_assets_url ) {
							$( document.createElement( 'script' ) ).attr( 'id', 'jquery-parallax' ).appendTo( 'body' ).attr( 'src', porto_elementor_vars.js_assets_url + '/libs/jquery.parallax.min.js' ).on( 'load', function() {
								new theme.Mouseparallax( $obj, opts );
							} );
						}
					}
					return;
				}

				var ins = $obj.data( '__scroll_parallax' );
				if ( ins ) {
					ins.disable();
					$obj.removeData( '__scroll_parallax' );
				}
				if ( widgetData['scroll_parallax'] ) {
					var parallax_width = widgetData['scroll_parallax_width'];
					if ( !parallax_width ) {
						parallax_width = 40;
					}
					var opts = { cssValueStart: parallax_width && parallax_width.size ? Number( parallax_width.size ) : 40 };
					if ( widgetData['scroll_unit'] ) {
						opts['cssValueUnit'] = widgetData['scroll_unit'];
					}
					if ( $.fn.themeScrollParallax ) {
						$obj.themeScrollParallax( opts );
					} else {
						if ( porto_elementor_vars.shortcodes_url ) {
							$( document.createElement( 'script' ) ).attr( 'id', 'porto-scroll-parallax-js' ).appendTo( 'body' ).attr( 'src', porto_elementor_vars.shortcodes_url + 'assets/js/porto-scroll-parallax.min.js' ).on( 'load', function() {
								$obj.themeScrollParallax( opts );
							} );
						}
					}
				}
				if ( widgetData['particles_img'] && widgetData['particles_img']['url'] ) {
					if ( typeof particlesJS == 'undefined' ) {
						$( document.createElement( 'script' ) ).attr( 'id', 'particles-js' ).appendTo( 'body' ).attr( 'src', porto_elementor_vars.shortcodes_url + 'assets/js/particles.min.js' ).on( 'load', function() {
							$( document.createElement( 'script' ) ).attr( 'id', 'porto-particles-loader-js' ).appendTo( 'body' ).attr( 'src', porto_elementor_vars.shortcodes_url + 'assets/js/porto-particles-loader.min.js' ).on( 'load', function() {
								$( document.body ).trigger( 'porto_init_particles_effect', [$obj] );
							} );
						} );
					} else {
						setTimeout( function() {
							$( document.body ).trigger( 'porto_init_particles_effect', [$obj] );
						}, 300 );
					}
				}
			}

			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_faqs.default', function( $obj ) {
				$obj.find( '.porto-faqs' ).each( function() {
					if ( $( this ).find( '.faq .toggle.active' ).length < 1 ) {
						$( this ).find( '.faq' ).eq( 0 ).find( '.toggle' ).addClass( 'active' );
						$( this ).find( '.faq' ).eq( 0 ).find( '.toggle-content' ).show();
					}
				} );
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_sidebar_menu.default', function( $obj ) {
				theme.SidebarMenu.initialize( $obj.find( '.sidebar-menu:not(.side-menu-accordion)' ) );

				$( '.sidebar-menu.side-menu-accordion' ).themeAccordionMenu( { 'open_one': true } );
			} );

			var portoPostsGridFunc = function( $obj ) {
				var $postGrid = $obj.find( '.porto-posts-grid' );
				if ( theme.InsertHoverImage && $postGrid.length ) {
					var $hoverImage = $postGrid.find( '.thumb-info-full' );
					if ( $hoverImage.length ) {
						$hoverImage.remove();
					}
					$postGrid.find( '[data-hoverlay-image]' ).each( function() {
						theme.InsertHoverImage( $( this ) );
					} );
				}
			}

			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_posts_grid.default', function( $obj ) {
				if ( $obj.find( '.quantity' ).length ) {
					theme.WooQtyField.initialize();
				}
				portoPostsGridFunc( $obj );
			} );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_archive_posts_grid.default', function( $obj ) {
				if ( $obj.find( '.quantity' ).length ) {
					theme.WooQtyField.initialize();
				}
				portoPostsGridFunc( $obj );
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_cp_linked.default', function( $obj ) {
				portoPostsGridFunc( $obj );
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_sb_archives.default', function( $obj ) {
				portoPostsGridFunc( $obj );
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_single_related.default', function( $obj ) {
				var $postGrid = $obj.find( '.porto-posts-grid' );
				if ( theme.InsertHoverImage && $postGrid.length ) {
					var $hoverImage = $postGrid.find( '.thumb-info-full' );
					if ( $hoverImage.length ) {
						$hoverImage.remove();
					}
					$postGrid.find( '[data-hoverlay-image]' ).each( function() {
						theme.InsertHoverImage( $( this ) );
					} );
				}
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_ultimate_heading.default', function( $obj ) {
				// Heading Image Floating
				var tIFloating = $obj.find( '.thumb-info-floating-element-wrapper[data-plugin-tfloating]' );
				if ( $.fn.themePluginTIFloating && tIFloating.length ) {
					var ins = tIFloating.data( '__textimagefloating' );
					if ( ins ) {
						var imgOptions = tIFloating.data( 'plugin-tfloating' )
						if ( ins.options.offset != imgOptions.offset ) {
							ins.clearData();
							tIFloating.themePluginTIFloating();
						}
					} else {
						tIFloating.themePluginTIFloating();
					}
				}
			} );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_fancytext.default', function( $obj ) {
				$( document.body ).trigger( 'porto_init_fancytext', [$obj] );
			} );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_countdown.default', function( $obj ) {
				if ( $obj.find( '.porto_countdown-div' ).length ) {
					let cdate = new Date(), sdate = cdate.getTime() + parseFloat( porto_elementor_vars.gmt_offset ) * 3600 * 1000;
					sdate = new Date( sdate ).toISOString().replace( /(.*)(20[0-9]{2}-[0-9]{2}-[0-9]{2})T([0-9]{2}:[0-9]{2}:[0-9]{2})(.*)/, '$2 $3' );
					$obj.find( '.porto_countdown-div' ).data( 'time-now', sdate.replace( /-/g, '/' ) );
				}
				$( document.body ).trigger( 'porto_init_countdown', [$obj] );
			} );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_scroll_progress.default', function( $obj ) {
				$( document.body ).trigger( 'porto_init_scroll_progress', [$obj] );
			} );

			// header builder
			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_hb_search_form.default', function( $obj ) {
				// Search
				if ( typeof theme.Search !== 'undefined' ) {
					theme.Search.defaults.popup = $( '.searchform-popup' );
					theme.Search.defaults.form = $( '.searchform' );
					theme.Search.initialize();
				}
			} );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_hb_menu.default', function( $obj ) {
				// menu
				if ( typeof theme.MegaMenu !== 'undefined' ) {
					theme.MegaMenu.defaults.menu = $( '.mega-menu' );
					theme.MegaMenu.initialize();
					theme.SidebarMenu.defaults.menu = $( '.sidebar-menu:not(.side-menu-accordion)' );
					// theme.SidebarMenu.defaults.toggle = $( '.widget_sidebar_menu .widget-title .toggle' );
					theme.SidebarMenu.defaults.menu_toggle = $( '#main-toggle-menu .menu-title' );
					theme.SidebarMenu.initialize();
				}
			} );

			if ( typeof porto_woocommerce_init == 'function' ) {
				var porto_woocommerce_widgets = ['porto_products.default', 'porto_sb_products.default', 'porto_product_categories.default', 'porto_cp_related.default'];
				$.each( porto_woocommerce_widgets, function( key, element_name ) {
					elementorFrontend.hooks.addAction( 'frontend/element_ready/' + element_name, function( $obj ) {
						porto_woocommerce_init();
					} );
				} );

				elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_cp_image.default', function( $obj ) {
					theme.WooProductImageSlider.initialize();
				} );

				porto_woocommerce_widgets = ['porto_cp_actions.default', 'porto_cp_add_to_cart.default'];
				$.each( porto_woocommerce_widgets, function( key, element_name ) {
					elementorFrontend.hooks.addAction( 'frontend/element_ready/' + element_name, function( $obj ) {
						theme.WooQtyField.initialize()
					} );
				} );
			}

			elementorFrontend.hooks.addAction( 'masonry_refresh', function( cls, w ) {
				if ( refresh_timer ) {
					clearTimeout( refresh_timer );
				}
				refresh_timer = setTimeout( function() {
					var $obj;
					if ( cls ) {
						$obj = $( '.elementor-column[class="' + cls + '"]' ).parent();
					} else {
						$obj = $( '.elementor-element-editable.porto-grid-item' ).parent();
					}
					if ( $obj.length && $obj.data( 'isotope' ) ) {
						if ( w ) {
							$( '.elementor-element-editable.porto-grid-item' ).children( '.elementor-column-wrap, .elementor-widget-wrap' ).data( 'width' ).size = Number( w );
						}
						porto_init_creative_layout( $obj );
					}
				}, 100 );
			} );

			elementorFrontend.hooks.addAction( 'refresh_dynamic_css', function( css, block_id ) {
				var $obj = $( 'style#porto_elementor_custom_css' );
				if ( !$obj.length ) {
					$obj = $( '<style></style>' ).attr( 'id', 'porto_elementor_custom_css' ).appendTo( 'head' );
				}
				css = css.replace( '/<script.*?\/script>/s', '' );
				if ( typeof block_id == 'undefined' ) {
					$obj.html( css );
				} else if ( -1 === $obj.html().indexOf( css ) ) {
					$obj.html( $obj.html() + css );
				}
			} );

			elementorFrontend.hooks.addAction( 'refresh_popup_options', function( option, value ) {
				if ( 'popup_width' == option ) {
					$( '.elementor.elementor-edit-area' ).css( 'max-width', value + 'px' );
				}
				else {
					var _$ = value;
					var horizontal = parseInt( _$( 'input[data-setting="popup_pos_horizontal"]' ).val(), 10 ),
						vertical = parseInt( _$( 'input[data-setting="popup_pos_vertical"]' ).val(), 10 ),
						editor = $( '.elementor.elementor-edit-area' );
					if ( option == 'popup_pos_first' ) {
						horizontal = elementor.settings.page.model.get( 'popup_pos_horizontal' );
						vertical = elementor.settings.page.model.get( 'popup_pos_vertical' );
					}
					editor.css( { left: '', top: '', right: '', bottom: '', transform: '' } );
					if ( 50 === horizontal ) {
						if ( 50 === vertical ) {
							editor.css( { left: '50%', top: '50%', transform: 'translate(-50%, -50%)' } );
						} else {
							editor.css( { left: '50%', transform: 'translateX(-50%)' } );
						}
					}
					else if ( 50 > horizontal ) {
						editor.css( { left: horizontal + '%' } );
					}
					else {
						editor.css( { right: ( 100 - horizontal ) + '%' } );
					}
					if ( 50 === vertical ) {
						if ( 50 !== horizontal ) {
							editor.css( { top: '50%', transform: 'translateY(-50%)' } );
						}
					}
					else if ( 50 > vertical ) {
						editor.css( { top: vertical + '%' } );
					}
					else {
						editor.css( { bottom: ( 100 - vertical ) + '%' } );
					}
				}
			} );

			elementorFrontend.hooks.addAction( 'refresh_edit_area', function( width ) {
				var $style = $( 'style#porto-edit-area-style' );
				if ( !$style.length ) {
					$( '.elementor-edit-area' ).before( '<style id="porto-edit-area-style"></style' );
					$style = $( 'style#porto-edit-area-style' );
				}
				if ( '' == width ) {
					$style.html( '' );
				} else {
					$style.html( '.elementor-edit-area > .elementor-section-wrap { max-width: ' + parseFloat( js_porto_vars.container_width ) + 'px; margin: 0 auto; }.elementor-section-wrap > .elementor-section { max-width: ' + width + '; }' );
				}
			} );

			$( '.porto-block[data-el_cls]' ).each( function() {
				$( this ).addClass( $( this ).data( 'el_cls' ) ).removeAttr( 'data-el_cls' );
			} );
			['shortcode.default', 'wp-widget-block-widget.default'].forEach( function( element_name ) {
				elementorFrontend.hooks.addAction( 'frontend/element_ready/' + element_name, function( $obj ) {
					$obj.find( '.porto-block[data-el_cls]' ).each( function() {
						$( this ).addClass( $( this ).data( 'el_cls' ) ).removeAttr( 'data-el_cls' );
					} );
				} );
			} );

			if ( typeof elementorFrontend.elementsHandler.elementsHandlers.section[4] == 'function' && elementorFrontend.elementsHandler.elementsHandlers.section[4].prototype.buildSVG ) {
				elementorFrontend.elementsHandler.elementsHandlers.section[4].prototype.onElementChange = function( propertyName ) {
					if ( propertyName.match( /^shape_divider_(top|bottom)_custom$/ ) ) {
						this.buildSVG( propertyName.match( /^shape_divider_(top|bottom)_custom$/ )[1] );
						return;
					}
					var shapeChange = propertyName.match( /^shape_divider_(top|bottom)$/ );
					if ( shapeChange ) {
						this.buildSVG( shapeChange[1] );
						return;
					}
					var negativeChange = propertyName.match( /^shape_divider_(top|bottom)_negative$/ );
					if ( negativeChange ) {
						this.buildSVG( negativeChange[1] );
						this.setNegative( negativeChange[1] );
					}
				}
				elementorFrontend.elementsHandler.elementsHandlers.section[4].prototype.buildSVG = function buildSVG( side ) {
					var baseSettingKey = 'shape_divider_' + side,
						shapeType = this.getElementSettings( baseSettingKey ),
						$svgContainer = this.elements['$' + side + 'Container'];
					$svgContainer.attr( 'data-shape', shapeType );

					if ( !shapeType ) {
						$svgContainer.empty(); // Shape-divider set to 'none'

						return;
					}

					var fileName = shapeType;

					if ( this.getElementSettings( baseSettingKey + '_negative' ) ) {
						fileName += '-negative';
					}

					var svgURL = this.getSvgURL( shapeType, fileName );
					if ( shapeType != 'custom' ) {
						jQuery.get( svgURL, function( data ) {
							$svgContainer.empty().append( data.childNodes[0] );
						} );
					} else {
						this.elements['$' + side + 'Container'].attr( 'data-negative', 'false' );
						var data = this.getElementSettings( baseSettingKey + '_custom' );
						var svgManager = elementor.helpers;
						data = data.value;
						if ( !data.id ) {
							$svgContainer.empty();
							return;
						}

						if ( svgManager._inlineSvg.hasOwnProperty( data.id ) ) {
							data && $svgContainer.empty().html( svgManager._inlineSvg[data.id] );
							return;
						}
						svgManager.fetchInlineSvg( data.url, function( svgData ) {
							if ( svgData ) {
								svgManager._inlineSvg[data.id] = svgData; //$( data ).find( 'svg' )[ 0 ].outerHTML;
								svgData && $svgContainer.empty().html( svgData );
								elementor.channels.editor.trigger( 'svg:insertion', svgData, data.id );
							}
						} );
					}
					this.setNegative( side );
				}
			}

			// image comparison widget
			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_image_comparison.default', function( $obj ) {
				if ( $.fn.portoImageCompare ) {
					$obj.find( '.porto-image-comparison' ).portoImageCompare();
				}
			} );

			// cursor effect widget
			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_cursor_effect.default', function( $obj, aa ) {
				var ins = $( document.body ).data( '__cursorEffect' );
				if ( ins ) {
					ins.destroy();
					$( document.body ).removeData( '__cursorEffect' );
				}

				if ( $.fn.themePluginCursorEffect ) {
					$( document.body ).themePluginCursorEffect();
				} else {
					if ( porto_elementor_vars.shortcodes_url ) {
						$( document.createElement( 'script' ) ).attr( 'id', 'porto-cursor-effect-js' ).appendTo( 'body' ).attr( 'src', porto_elementor_vars.shortcodes_url + 'assets/js/porto-cursor-effect.min.js' ).on( 'load', function() {
							$( document.body ).themePluginCursorEffect();
						} );
					}
				}
			} );

			// stat counter widget
			var refresh_counter_timer = null;
			elementorFrontend.hooks.addAction( 'frontend/element_ready/porto_stat_counter.default', function( $obj ) {
				if ( countUp != "undefined" ) {
					clearTimeout( refresh_counter_timer );
					refresh_counter_timer = setTimeout( function() {
						jQuery( document.body ).trigger( 'porto_refresh_vc_content', [$obj] );
					}, 1000 );
				}
			} );

		}

		if ( elementorFrontend.hooks && typeof elementor != 'undefined' ) {
			porto_elementor_init();
		} else {
			elementorFrontend.on( 'components:init', porto_elementor_init );
		}

		if ( typeof elementorPro != 'object' ) {
			elementorFrontend.on( 'components:init', function() {
				function createHandles() {
					$( '[data-elementor-id]' ).each( function() {
						var $documentElement = $( this );
						if ( $documentElement.hasClass( 'elementor-edit-mode' ) ) {
							return;
						}
						var $existingHandle = $documentElement.children( '.elementor-document-handle' );
						if ( $existingHandle.length ) {
							return;
						}
						var $handle = $( '<div>', { class: 'elementor-document-handle' } ),
							$handleIcon = $( '<i>', { class: 'eicon-edit' } ),
							documentTitle = $documentElement.data( 'elementor-title' ),
							$handleTitle = $( '<div>', { class: 'elementor-document-handle__title' } ).text( documentTitle );
						$handle.append( $handleIcon, $handleTitle );
						$handle.on( 'click', function() {
							elementorCommon.api.internal( 'panel/state-loading' );
							elementorCommon.api.run( 'editor/documents/switch', {
								id: $documentElement.data( 'elementor-id' )
							} ).finally( function() {
								return elementorCommon.api.internal( 'panel/state-ready' );
							} );
						} );
						$documentElement.prepend( $handle );
					} );
				}
				createHandles();
				elementor.on( 'document:loaded', function() {
					createHandles();
				} );
			} );
		}
	}
} );

( function( $ ) {
	$( window ).on( 'load', function() {
		// Header and Footer Type preset
		if ( typeof elementorFrontend != 'undefined' && window.top.porto_builder_condition && window.top.porto_builder_condition.builder_type && ( window.top.porto_builder_condition.builder_type == 'header' || window.top.porto_builder_condition.builder_type == 'footer' ) ) {
			window.top.elementor.presetsFactory.getPresetSVG = function getPresetSVG( preset, svgWidth, svgHeight, separatorWidth ) {
				var _ = window.top._;
				if ( _.isEqual( preset, ['flex-1', 'flex-auto'] ) ) {
					var svg = document.createElement( 'svg' );
					var protocol = 'http';
					svg.setAttribute( 'viewBox', '0 0 88.3 44.2' );
					svg.setAttributeNS( protocol + '://www.w3.org/2000/xmlns/', 'xmlns:xlink', protocol + '://www.w3.org/1999/xlink' );
					svg.innerHTML = '<rect fill="#D5DADF" width="73.8" height="44.2"></rect> <rect x="75.5" fill="#D5DADF" width="12.8" height="44.2"></rect> <text transform="matrix(1 0 0 1 8.5 25.9167)" fill="#A7A9AC" font-family="Segoe Script" font-size="12">For ' + window.top.porto_builder_condition.builder_type + '</text>'
					return svg;
				}
				else if ( _.isEqual( preset, ['flex-1', 'flex-auto', 'flex-1'] ) ) {
					var svg = document.createElement( 'svg' );
					var protocol = 'http';
					svg.setAttribute( 'viewBox', '0 0 88.3 44.2' );
					svg.setAttributeNS( protocol + '://www.w3.org/2000/xmlns/', 'xmlns:xlink', protocol + '://www.w3.org/1999/xlink' );
					svg.innerHTML = '<rect fill="#D5DADF" width="35" height="44.2"></rect><rect x="53.4" fill="#D5DADF" width="35" height="44.2" ></rect><rect x="36.9" fill="#D5DADF" width="14.5" height="44.2"></rect><text transform="matrix(1 0 0 1 8.5 25.9167)" fill="#A7A9AC" font-family="Segoe Script" font-size="12">For ' + window.top.porto_builder_condition.builder_type + '</text>';
					return svg;
				}
				else if ( _.isEqual( preset, ['flex-auto', 'flex-1', 'flex-auto'] ) ) {
					var svg = document.createElement( 'svg' );
					var protocol = 'http';
					svg.setAttribute( 'viewBox', '0 0 88.3 44.2' );
					svg.setAttributeNS( protocol + '://www.w3.org/2000/xmlns/', 'xmlns:xlink', protocol + '://www.w3.org/1999/xlink' );
					svg.innerHTML = '<rect fill="#D5DADF" width="11.5" height="44.2"></rect><rect x="59.2" fill="#D5DADF" width="29.2" height="44.2"></rect><rect x="13.7" fill="#D5DADF" width="43.5" height="44.2"></rect> <text transform="matrix(1 0 0 1 8.5 25.9167)" fill="#A7A9AC" font-family="Segoe Script" font-size="12">For ' + window.top.porto_builder_condition.builder_type + '</text></svg>'
					return svg;
				}
				svgWidth = svgWidth || 100;
				svgHeight = svgHeight || 50;
				separatorWidth = separatorWidth || 2;

				var absolutePresetValues = this.getAbsolutePresetValues( preset ),
					presetSVGPath = this._generatePresetSVGPath( absolutePresetValues, svgWidth, svgHeight, separatorWidth );

				return this._createSVGPreset( presetSVGPath, svgWidth, svgHeight );
			}
		}
	} );
} )( window.jQuery );