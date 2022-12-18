/**
 * The options cache of quick access widget in wpb frontend editor.
 * 
 * @since 2.6.0
 */
var portoQuickAccessCache = {};

jQuery( document ).ready( function( $ ) {
	'use strict';

	$( 'body' ).on( 'tabsbeforeactivate', '.wpb_tour_tabs_wrapper', function( e, ui ) {
		ui.oldTab.removeClass( 'active' );
		ui.newTab.addClass( 'active' );
	} );

	$( '.compose-mode .vc_controls-bc .vc_control-btn-append' ).each( function() {
		$( this ).insertAfter( $( this ).closest( '.vc_controls' ).find( '.vc_control-btn-prepend' ) );
	} );

	if ( window.parent.vc && window.parent.vc.events ) {
		/**
		 * Make quick access item.
		 * 
		 * @since 2.6.0
		 * @param {*} $qa_node 
		 * @param {*} qa_item 
		 * @param {*} widgetRect 
		 * @param {*} nodeRect 
		 */
		function make_qa_item( self, $qa_node, qa_item, widgetRect, nodeRect, nonPos = false ) {
			var elementDialog = self.$el, _temp = $( '<button aria-label="' + js_porto_vars.quick_access + '" title="' + js_porto_vars.quick_access + '" class="' + ( nonPos ? 'non-pos ' : '' ) + 'porto-qa-item position-absolute"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg><div class="region"></div></button>' );
			$qa_node.append( _temp.click( function( e ) {

				elementDialog.find( 'li.vc_edit-form-tab-control button' ).filter( function() {
					return $( this ).text() == qa_item.tab;
				} ).trigger( 'click' );
				if ( qa_item.class ) {
					var $panelSide = elementDialog.find( '.vc_ui-panel-content-container' );
					var $particularOption = elementDialog[0].querySelector( qa_item.class + ':not(.vc_dependent-hidden)' );
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
		 * Quick Access in WPB Frontned Editor.
		 * 
		 * @since 2.6.0
		 */
		( function quick_access( self ) {
			if ( !( typeof self == 'object' ) ) {
				return;
			}
			$( document.body ).on( 'mouseenter', '.vc_element', function() {
				if ( !( self.model && self.model.id ) ) {
					return;
				}
				var $this = $( this );
				if ( $this.attr( 'data-model-id' ) != self.model.id ) {
					return;
				}
				var widgetControls = self.model.settings.params;
				var widgetKey = self.model.settings.base;
				if ( !portoQuickAccessCache[widgetKey] && typeof widgetControls == 'object' ) {
					widgetControls.forEach( function( widgetControl ) {
						if ( widgetControl.qa_selector ) {
							if ( !portoQuickAccessCache[widgetKey] ) {
								portoQuickAccessCache[widgetKey] = [];
							}
							var controlInfo = {
								selector: widgetControl.qa_selector,
								tab: widgetControl.group ? widgetControl.group : wp.i18n.__( 'General', 'js_composer' ),
								class: '.vc_shortcode-param[data-vc-shortcode-param-name="' + widgetControl.param_name + '"]'
							};
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
								make_qa_item( self, $qa_node, qa_item, widgetRect, nodeRect, hasNon );
							} );
						}
					} );
					if ( widgetKey == 'porto_sidebar_menu' || widgetKey == 'porto_hb_menu' ) {
						if ( nonPosElements.length ) {
							$this.on( 'mouseenter', '>ul>li.has-sub, .sidebar-menu>li.has-sub, .sidebar-menu .narrow .inner>.sub-menu>li.menu-item-has-children', function( e ) {
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
									make_qa_item( self, $qa_node, nonPosElement[1], widgetRect, nodeRect, true );
								} );
							} ).on( 'mouseleave', '>ul>li.has-sub, .sidebar-menu>li.has-sub', function( e ) {
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
			} ).on( 'mouseleave', '.vc_element', function() {
				if ( !( self.model && self.model.id ) ) {
					return;
				}
				if ( $( this ).attr( 'data-model-id' ) != self.model.id ) {
					return;
				}
				var $quickAccess = $( this ).find( '.porto-quick-access' );
				if ( $quickAccess.length ) {
					$quickAccess.remove();
				}
				$( this ).off( 'mouseenter mouseleave' );
			} );
		} )( window.parent.vc.edit_element_block_view );

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
				theme.$selectEl = $( this ).closest( '.vc_element' );
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
						if ( parent.vc.builder && typeof theme.$selectEl == 'object' && theme.$selectEl.length ) {
							parent.vc.builder.update( parent.vc.shortcodes._byId[theme.$selectEl.attr( 'data-model-id' )] );
							theme.$selectEl = '';
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

		/**
		 * Toolbar
		 * 
		 * @since 2.6.0
		 */
		( function() {

			var isCapture = 0;
			var _$ = parent.jQuery;
			var $toolbar = _$( '.porto-toolbar' );
			var vcBarHeight = _$( '#vc_navbar' ).height();
			if ( $toolbar.length == 0 ) {
				return;
			}
			_$( 'body' ).on( 'mousemove', function( e ) {
				if ( isCapture == 0 ) {
					return;
				}
				if ( e.buttons == 1 ) { // primary mouse button was pressed.
					isCapture = 2;
					$toolbar.css( { top: e.pageY, left: e.pageX } );
				}
			} ).on( 'mouseup', function( e ) {
				if ( isCapture != 2 ) {
					if ( e.target.classList && e.target.classList.contains( 'porto-toolbar-toggle' ) ) {
						$toolbar.toggleClass( 'switched' );
					}
				}
				isCapture = 0;
			} ).on( 'mousedown', function( e ) {
				if ( e.target.classList && e.target.classList.contains( 'porto-toolbar-toggle' ) ) {
					isCapture = 1;
				}
			} );

			$( 'body' ).on( 'mousemove', function( e ) {
				if ( isCapture == 0 ) {
					return;
				}
				if ( e.buttons == 1 ) {
					isCapture = 2;
					$toolbar.css( { top: e.clientY + vcBarHeight, left: e.screenX } );
				}
				if ( e.buttons == 0 && isCapture == 2 ) { // bubbling
					isCapture = 0;
					$toolbar.css( { top: e.clientY + vcBarHeight, left: e.screenX } );
				}
			} ).on( 'mouseup', function( e ) {
				if ( isCapture != 2 ) {
					if ( e.target.classList && e.target.classList.contains( 'porto-toolbar-toggle' ) ) {
						$toolbar.toggleClass( 'switched' );
					}
				}
				isCapture = 0;
			} );

			_$( '.go-to-page-css' ).on( 'click', function() {
				_$( '#vc_post-settings-button' ).trigger( 'click' );
			} );
			_$( '.go-to-builder-setting' ).on( 'click', function() {
				_$( '#porto-editor-area-button' ).trigger( 'click' );
			} );
		} )();

		window.parent.vc.events.on( 'shortcodes:add', function( model ) {
			var parent_id = model.attributes.parent_id;
			if ( !parent_id ) {
				return;
			}
			var parent = window.parent.vc.shortcodes.get( parent_id );
			if ( parent && 'porto_carousel' == parent.attributes.shortcode ) {
				var $obj = $( '[data-model-id="' + parent.attributes.id + '"]' ).children( '.owl-carousel' );
				if ( $obj.length ) {
					$obj.removeData( '__carousel' );
					$obj.trigger( 'destroy.owl.carousel' );
				}
			}
		} );

		window.parent.vc.events.on( 'shortcodeView:ready', function( e ) {
			var shortcode = e.attributes.shortcode;
			if ( 'porto_scroll_progress' == shortcode ) {
				if ( $( 'script#porto-scroll-progress-js' ).length ) {
					$( document.body ).trigger( 'porto_init_scroll_progress', [e.view.$el] );
				} else {
					$( document.createElement( 'script' ) ).attr( 'id', 'porto-scroll-progress-js' ).appendTo( 'body' ).attr( 'src', js_porto_vars.ajax_loader_url.replace( '/images/ajax-loader@2x.gif', '/js/libs/porto-scroll-progress.min.js' ) ).on( 'load', function() {
						$( document.body ).trigger( 'porto_init_scroll_progress', [e.view.$el] );
					} );
				}
			} else if ( 'vc_row' == shortcode && e.attributes.params ) {
				if ( e.attributes.params.particles_effect && e.attributes.params.particles_img ) {
					e.view.$el.find( '.particles-wrapper:not(:first-child)' ).remove();

					if ( typeof particlesJS == 'undefined' ) {
						$( document.createElement( 'script' ) ).attr( 'id', 'particles-js' ).appendTo( 'body' ).attr( 'src', porto_wpb_vars.shortcodes_url + 'assets/js/particles.min.js' ).on( 'load', function() {
							$( document.createElement( 'script' ) ).attr( 'id', 'porto-particles-loader-js' ).appendTo( 'body' ).attr( 'src', porto_wpb_vars.shortcodes_url + 'assets/js/porto-particles-loader.min.js' ).on( 'load', function() {
								$( document.body ).trigger( 'porto_init_particles_effect', [e.view.$el] );
							} );
						} );
					} else {
						$( document.body ).trigger( 'porto_init_particles_effect', [e.view.$el] );
					}
				} else {
					e.view.$el.find( '.particles-wrapper' ).remove();
				}

				var $splitWrapper = $( document.body ).find( '[data-model-id="' + e.attributes.id + '"] >.mouse-hover-split' );
				if ( 'yes' == e.attributes.params.hover_split ) {
					if ( $splitWrapper.length && typeof $splitWrapper.data( '__mousehoversplit' ) == 'undefined' ) {
						$splitWrapper.themePluginHoverSplit();
					}
				} else {
					if ( typeof $splitWrapper.data( '__mousehoversplit' ) != 'undefined' ) {
						$splitWrapper.data( '__mousehoversplit' ).clearData();
					}
				}
			} else if ( 'porto_cursor_effect' == shortcode && e.attributes.params && e.id ) {
				var $shortcode_cls_obj = e.view.$el.find( '.shortcode-class' );
				if ( typeof window.porto_cursor_effects == 'undefined' ) {
					window.porto_cursor_effects = [];
				}
				window.porto_cursor_effects.forEach( function( i, index ) {
					if ( i.model_id && e.id == i.model_id ) {
						window.porto_cursor_effects.splice( index, 1 );
						return false;
					}
				} );

				var inner_icon = e.attributes.params.inner_icon;
				if ( 'simpleline' == e.attributes.params.icon_type ) {
					inner_icon = e.attributes.params.icon_simpleline;
				} else if ( 'porto' == e.attributes.params.icon_type ) {
					inner_icon = e.attributes.params.icon_porto;
				}
				window.porto_cursor_effects.push( { model_id: e.id, id: $shortcode_cls_obj.length ? $shortcode_cls_obj.text() : '', selector: e.attributes.params.selector ? e.attributes.params.selector.replace( '&gt;', '>' ) : '', hover_effect: e.attributes.params.hover_effect || 'plus', icon: inner_icon, cursor_w: e.attributes.params.cursor_w || '' } );
				$shortcode_cls_obj.remove();

				var ins = $( document.body ).data( '__cursorEffect' );
				if ( ins ) {
					ins.destroy();
					$( document.body ).removeData( '__cursorEffect' );

					if ( window.porto_cursor_effects.length && $.fn.themePluginCursorEffect ) {
						$( document.body ).themePluginCursorEffect();
					}
				}
			} else if ( 'vc_pie' == shortcode && e.attributes.params && e.attributes.params.type && 'custom' == e.attributes.params.type ) {
				porto_init( e.view.$el );
			} else if ( 'porto_countdown' == shortcode && e.attributes.params ) {
				var $obj = e.view.$el;
				var $countdown_div = $obj.find( '.porto_countdown-div' );
				if ( $countdown_div.length ) {
					let cdate = new Date(), sdate = cdate.getTime() + parseFloat( $countdown_div.data( 'time-zone' ) ) * 3600 * 1000;
					sdate = new Date( sdate ).toISOString().replace( /(.*)(20[0-9]{2}-[0-9]{2}-[0-9]{2})T([0-9]{2}:[0-9]{2}:[0-9]{2})(.*)/, '$2 $3' );
					$countdown_div.data( 'time-now', sdate.replace( /-/g, '/' ) );
				}
				$( document.body ).trigger( 'porto_init_countdown', [$obj] );
			} else if ( 'porto_image_comparison' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				if ( $.fn.portoImageCompare && $obj.find( '.porto-image-comparison' ).length ) {
					$obj.find( '.porto-image-comparison' ).portoImageCompare();
				}
			} else if ( 'porto_blog' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				porto_init( $obj );
			} else if ( 'porto_content_box' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				var $icon = $obj.find( '.box-content>.icon-featured:not(:first-child)' );
				if ( $icon.length ) {
					$icon.remove();
				}
			} else if ( 'porto_sidebar_menu' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				theme.SidebarMenu.initialize( $obj.find( '.sidebar-menu:not(.side-menu-accordion)' ) );

				$( '.sidebar-menu.side-menu-accordion' ).themeAccordionMenu( { 'open_one': true } );
			} else if ( 'porto_hb_menu' == shortcode && e.attributes.params ) {
				// var $obj = $( e.view.$el );
				// menu
				if ( typeof theme.MegaMenu !== 'undefined' ) {
					theme.MegaMenu.defaults.menu = $( '.mega-menu' );
					theme.MegaMenu.initialize();
					theme.SidebarMenu.defaults.menu = $( '.sidebar-menu:not(.side-menu-accordion)' );
					// theme.SidebarMenu.defaults.toggle = $( '.widget_sidebar_menu .widget-title .toggle' );
					theme.SidebarMenu.defaults.menu_toggle = $( '#main-toggle-menu .menu-title' );
					theme.SidebarMenu.initialize();
				}
			} else if ( 'vc_column' == shortcode && e.attributes.params ) {
				if ( $.fn.themePluginHoverSplit ) {
					if ( 'yes' == e.attributes.params.split_layer ) {
						var $splitSlide = $( document.body ).find( '[data-model-id="' + e.attributes.id + '"]' ),
							$splitWrapper = $splitSlide.closest( '.mouse-hover-split' );
						$splitSlide.addClass( 'split-slide' );
						var ins = $splitWrapper.data( '__mousehoversplit' );
						if ( ins ) {
							ins.clearData();
						}
						$splitWrapper.themePluginHoverSplit();
					}
				}
			} else if ( ( 'vc_custom_heading' == shortcode || 'porto_ultimate_heading' == shortcode ) && e.attributes.params ) {
				var $floatingImage = $( document.body ).find( '[data-model-id="' + e.attributes.id + '"]' );
				if ( $floatingImage.length ) {
					var $floatingWrapper = $floatingImage.find( '.thumb-info-floating-element-wrapper[data-plugin-tfloating]' );
					// Text Hover Floating Image
					if ( $.fn.themePluginTIFloating && $floatingWrapper.length ) {
						var ins = $floatingWrapper.data( '__textimagefloating' );
						if ( ins ) {
							ins.clearData();
						}
						$floatingWrapper.themePluginTIFloating();
					}
				}
			} else if ( ( 'porto_hscroller' == shortcode && e.attributes.params ) ) {
				if ( $.fn.themePluginHScroller ) {
					var $hScroller = $( document.body ).find( '[data-model-id="' + e.attributes.id + '"] .horizontal-scroller-wrapper' ),
						ins = $hScroller.data( '__horizontalscroller' );
					if ( ins ) {
						ins.clearData();
					}
					// Horizontal Scroller
					$hScroller.themePluginHScroller();
				}
			} else if ( 'porto_ab_posts_grid' == shortcode || 'porto_tb_posts' == shortcode || 'porto_tb_archives' == shortcode ) {
				var $postWrap = $( e.view.$el ).find( '.porto-posts-grid' );
				if ( theme.InsertHoverImage && $postWrap.length ) {
					var $hoverImage = $postWrap.find( '.thumb-info-full' );
					if ( $hoverImage.length ) {
						$hoverImage.remove();
					}
					$postWrap.find( '[data-hoverlay-image]' ).each( function() {
						theme.InsertHoverImage( $( this ) );
					} );
				}
			}
		} );

		window.parent.vc.events.on( 'shortcodeView:destroy', function( model ) {
			var parent_id = model.attributes.parent_id;
			if ( !parent_id ) {
				return;
			}
			var parent = window.parent.vc.shortcodes.get( parent_id );
			if ( parent ) {
				if ( 'porto_carousel' == parent.attributes.shortcode ) {
					var $obj = $( '[data-model-id="' + parent.attributes.id + '"]' ).children( '.owl-carousel' );
					if ( $obj.length ) {
						$obj.removeData( '__carousel' );
						$obj.trigger( 'destroy.owl.carousel' );
						$obj.children( '.owl-item:empty' ).remove();
						$obj.themeCarousel( $obj.data( 'plugin-options' ) );
					}
				}
			}

			if ( 'porto_cursor_effect' == model.attributes.shortcode && window.porto_cursor_effects && window.porto_cursor_effects.length ) {
				window.porto_cursor_effects.forEach( function( i, index ) {
					if ( i.model_id && model.id == i.model_id ) {
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
		window.parent.vc.edit_element_block_view.on( 'afterRender', function() {
			var $el = this.$el,
				widgets = ['porto_ultimate_heading', 'porto_buttons', 'porto_image_comparison', 'porto_interactive_banner', 'vc_custom_heading', 'vc_btn', 'porto_countdown', 'vc_single_image'];
			if ( $.inArray( $el.attr( 'data-vc-shortcode' ), widgets ) >= 0 ) {
				$el.find( 'select' ).each( function() {
					var $this = $( this ),
						el_class = $this.attr( 'class' ),
						index_last = el_class.indexOf( '_dynamic_source' );
					if ( index_last >= 0 ) {
						var index_first = el_class.lastIndexOf( ' ', index_last );
						if ( index_first == -1 ) {
							index_first = 0;
						}
						var field_name = el_class.substring( index_first, index_last ).trim(),
							field_index = field_name.indexOf( '_' ),
							field_type = '';
						if ( field_index > 0 ) {
							field_type = field_name.substring( 0, field_index );
						} else {
							field_type = field_name;
						}
						if ( field_type == 'field' || field_type == 'link' || field_type == 'image' ) {
							porto_wpb_dynamic_execute( $el, field_type, field_name );
						}
					}
				} );
			}
		} );
		function porto_wpb_dynamic_execute( $el, field_type, field_name ) {
			var $dynamic_source_object = $el.find( 'select.' + field_name + '_dynamic_source' ),
				dynamic_source = $dynamic_source_object.val(),
				$dynamic_content = $el.find( 'select.' + field_name + '_dynamic_content' );
			porto_wpb_dyanmic_content( dynamic_source, field_type, $dynamic_content );

			$dynamic_source_object.on( 'change', function() {
				dynamic_source = $( this ).val();
				if ( field_type == 'field' ) {
					porto_wpb_dynamic_enable_subcontent( $el, $dynamic_content.val(), 'post_date', 'date_format' );
				}
				porto_wpb_dyanmic_content( dynamic_source, field_type, $dynamic_content );
			} );

			// Format date format
			if ( field_type == 'field' ) {
				porto_wpb_dynamic_enable_subcontent( $el, $dynamic_content.val(), 'post_date', 'date_format' );
			}

			$dynamic_content.on( 'change', function() {
				if ( field_type == 'field' ) {
					porto_wpb_dynamic_enable_subcontent( $el, $dynamic_content.val(), 'post_date', 'date_format' );
				}
			} );
		}

		function porto_wpb_dynamic_enable_subcontent( $el, dynamic_content_option, content_value, shortcode_param ) {
			var $sub_content = $el.find( '[data-vc-shortcode-param-name="' + shortcode_param + '"]' ),
				$sub_content_select = $el.find( '[name="' + shortcode_param + '"]' );
			if ( $sub_content.length ) {
				if ( content_value == dynamic_content_option ) {
					if ( $sub_content.hasClass( 'vc_dependent-hidden' ) ) {
						$sub_content.removeClass( 'vc_dependent-hidden' );
						$sub_content_select.val( $sub_content_select.attr( 'value' ) );
					}
				} else {
					$sub_content.addClass( 'vc_dependent-hidden' );
					$sub_content_select.val( '' );
				}
			}
		}

		function porto_wpb_dyanmic_content( dynamic_source, field_type, $dynamic_content ) {
			$dynamic_content.find( '*' ).remove();
			if ( '' != dynamic_source && 'meta_field' != dynamic_source && $dynamic_content.length && !$dynamic_content.hasClass( '.vc_dependent-hidden' ) && porto_wpb_vars[dynamic_source] ) {
				if ( porto_wpb_vars[dynamic_source][field_type] ) {
					var $contents = porto_wpb_vars[dynamic_source][field_type],
						keys = Object.keys( $contents ),
						attribute = $dynamic_content.attr( 'data-option' ), selected_content = false,
						__ = wp.i18n.__;

					if ( keys.length ) {
						$dynamic_content.append( '<option class="" value="">' + __( 'Select Source...', 'porto' ) + '</option>' );
						for ( let index = 0; index < keys.length; index++ ) {
							var selected = '';
							if ( keys[index] == attribute ) {
								selected = 'selected="selected"';
								selected_content = true;
							}
							$dynamic_content.append( '<option class="' + keys[index] + '" value="' + keys[index] + '" ' + selected + '>' + $contents[keys[index]] + '</option>' );
						}
					}
					if ( selected_content ) {
						$dynamic_content.val( attribute ).addClass( attribute );
					}
				}
			}
		}
	}
} );