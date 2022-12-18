function porto_elementor_add_floating_options( settings ) {
	if ( !settings.floating_start_pos || !settings.floating_speed ) {
		return '';
	}
	let floating_options = { 'startPos': settings.floating_start_pos, 'speed': settings.floating_speed };
	if ( !settings.floating_transition || 'yes' == settings.floating_transition ) {
		floating_options['transition'] = true;
	} else {
		floating_options['transition'] = false;
	}
	if ( settings.floating_horizontal ) {
		floating_options['horizontal'] = true;
	} else {
		floating_options['horizontal'] = false;
	}
	if ( settings.floating_duration ) {
		floating_options['transitionDuration'] = parseInt( settings.floating_duration, 10 );
	}
	return ' data-plugin-float-element data-plugin-options=' + JSON.stringify( floating_options );
}

jQuery( document ).ready( function( $ ) {

	if ( $( '.blocks-wrapper' ).length ) {
		$( '#elementor-panel' ).on( 'mousewheel', '#elementor-panel-content-wrapper', function() {
			var $candidateBlocks = $( '#porto-studio-candidate-blocks' );
			if ( $candidateBlocks.length && $( '.blocks-wrapper #s' ).val() ) {
				var top = $candidateBlocks.offset().top - $( this ).offset().top + $candidateBlocks.height() - $( this ).height();
				if ( top <= 10 && !$candidateBlocks.hasClass( 'loading' ) && porto_blocks_total_page >= porto_blocks_cur_page + 1 ) {
					$( '.blocks-wrapper .category-list a.active' ).trigger( 'click', [porto_blocks_cur_page + 1, 'widget-search'] );
					$candidateBlocks.addClass( 'infiniteloading' );
				}
			}
		} );
		$( document.body ).on( 'input', '#elementor-panel-elements-search-input', _.debounce( function() {
			var $this = $( this );
			if ( $this.val().length < 3 ) {
				return;
			}
			$( '#porto-studio-candidate-blocks' ).remove();
			$( '.blocks-wrapper #s' ).val( $this.val() );
			$( '.blocks-wrapper .category-list a.active' ).trigger( 'click', [1, 'widget-search'] );
		}, 300 ) );
	}
	/**
	 * Toolbar
	 * 
	 * @since 2.6.0
	 */
	( function() {

		var isCapture = 0;
		var $toolbar = $( '.porto-toolbar' );
		if ( $toolbar.length == 0 ) {
			return;
		}
		$( document.body ).on( 'mousemove', function( e ) {
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
		var _iframe = $( '#elementor-preview-iframe' );
		if ( _iframe.length ) {
			_iframe.on( 'load', function() {
				_iframe[0].contentWindow.jQuery( 'body' ).on( 'mousemove', function( e ) {
					if ( isCapture == 0 ) {
						return;
					}
					var barHeight = $( '#e-responsive-bar' ).height();
					if ( e.buttons == 1 ) {
						isCapture = 2;
						$toolbar.css( { top: e.clientY + barHeight, left: e.screenX } );
					}
					if ( e.buttons == 0 && isCapture == 2 ) { // bubbling
						isCapture = 0;
						$toolbar.css( { top: e.clientY + barHeight, left: e.screenX } );
					}
				} ).on( 'mouseup', function( e ) {
					if ( isCapture != 2 ) {
						if ( e.target.classList && e.target.classList.contains( 'porto-toolbar-toggle' ) ) {
							$toolbar.toggleClass( 'switched' );
						}
					}
					isCapture = 0;
				} );
			} );
		}

		$( '.go-to-page-css' ).on( 'click', function() {
			if ( typeof $e == 'object' ) {
				$e.route( 'panel/page-settings/settings' );
				elementor.getPanelView().currentPageView.activateSection( 'porto_settings' );
				elementor.getPanelView().currentPageView._renderChildren();
			}
		} );
		$( '.go-to-floating' ).on( 'click', function() {
			if ( typeof $e == 'object' ) {
				if ( elementor.selection.getElements()[0] && elementor.selection.getElements()[0].model ) {
					$e.routes.to( 'panel/editor/porto_custom_tab', {
						model: elementor.selection.getElements()[0].model,
						view: elementor.selection.getElements()[0].view
					} );
				} else {
					window.alert( wp.i18n.__( 'Please select any widget.', 'porto-functionality' ) );
				}
			}
		} );
		$( '.go-to-builder-setting' ).on( 'click', function() {
			if ( typeof $e == 'object' ) {
				if ( typeof porto_builder_condition == 'object' ) {
					var sectionName = 'porto_edit_area';
					$e.route( 'panel/page-settings/settings' );
					if ( 'archive' == porto_builder_condition.builder_type ) {
						sectionName = 'archive_preview_settings';
					} else if ( 'single' == porto_builder_condition.builder_type ) {
						sectionName = 'single_preview_settings';
					} else if ( 'popup' == porto_builder_condition.builder_type ) {
						sectionName = 'porto_popup_settings';
					}
					elementor.getPanelView().currentPageView.activateSection( sectionName );
					elementor.getPanelView().currentPageView._renderChildren();
				} else {
					$e.route( 'panel/menu' );
				}
			}
		} );
	} )();

	elementor.hooks.addFilter( 'panel/elements/regionViews', function( panel ) {
		var categories = panel.categories.options.collection;
		var categoryIndex = categories.findIndex( {
			name: "porto-elements"
		} );

		categoryIndex && categories.add( {
			name: "porto-notice",
			title: wp.i18n.__( 'Porto Library', 'porto-functionality' ),
			defaultActive: 1,
			items: []
		}, {
			at: categoryIndex - 1
		} );
		return panel;
	} );

	if ( typeof Marionette != 'undefined' && Marionette.ItemView && Marionette.Behavior ) {
		class portoStudioItem extends Marionette.ItemView {
			className() {
				return 'elementor-panel-category-items-porto-notice';
			}
			getTemplate() {
				return '#tmpl-porto-elementor-studio-notice';
			}
		}

		class portoStudioHandle extends Marionette.Behavior {
			initialize() {
				if ( 'porto-notice' == this.view.options.model.get( 'name' ) ) {
					this.view.emptyView = portoStudioItem;
				}
			}
		}
		elementor.hooks.addFilter( 'panel/category/behaviors', function( behaviors ) {
			return Object.assign( {}, behaviors, {
				studioNotice: {
					behaviorClass: portoStudioHandle
				}
			} );
		} );
	}
	// add Porto Studio menu
	elementor.on( 'panel:init', function() {
		$( '<div id="porto-elementor-panel-porto-studio" class="elementor-panel-footer-tool tooltip-target" data-tooltip="Porto Studio"><i class="porto-icon-studio" aria-hidden="true"></i><span class="elementor-screen-only">Porto Studio</span></div>' ).insertAfter( '#elementor-panel-footer-saver-preview' ).tipsy( {
			gravity: 's',
			title: function title() {
				return this.getAttribute( 'data-tooltip' );
			}
		} );

	} );

	// elementor.hooks.addFilter( 'element/view', function( ChildView, model, self ) {

	// } );
	elementor.on( 'frontend:init', function() {
		if ( typeof $e != 'undefined' ) {
			$e.commands.on( 'run:before', function( component, command, args ) {
				if ( 'document/elements/delete' == command && args && args.containers && args.containers.length ) {
					args.containers.forEach( function( cnt ) {
						elementorFrontend.hooks.doAction( 'porto_elementor_element_before_delete', cnt.model );
					} );
				}
			} );
			$e.commands.on( 'run:after', function( component, command, args ) {
				if ( 'document/elements/create' == command && args && args.model && args.model.id ) {
					elementorFrontend.hooks.doAction( 'porto_elementor_element_after_add', args.model );
				}

				if ( 'document/elements/move' == command && args && args.container ) {
					elementorFrontend.hooks.doAction( 'porto_elementor_element_after_move', args.container.id );
				}

				if ( 'document/elements/delete' == command && args && args.containers ) {
					elementorFrontend.hooks.doAction( 'porto_elementor_element_after_delete', args.containers );
				}

				if ( 'document/elements/duplicate' == command && args && args.containers ) {
					elementorFrontend.hooks.doAction( 'porto_elementor_element_after_duplicate', args.containers );
				}
			} );
		}

		var custom_css = elementor.settings.page.model.get( 'porto_custom_css' );
		if ( typeof custom_css != 'undefined' ) {
			elementorFrontend.on( 'components:init', function() {
				elementorFrontend.hooks.doAction( 'refresh_dynamic_css', custom_css );
			} );
		}

		var header_type = elementor.settings.page.model.get( 'porto_header_type' );
		if ( 'side' == header_type ) {
			$( '#elementor-preview-responsive-wrapper' ).addClass( 'mobile-width' );
		}

		var popup_width = elementor.settings.page.model.get( 'popup_width' );

		setTimeout( function() {
			typeof popup_width != 'undefined' && elementorFrontend.hooks.doAction( 'refresh_popup_options', 'popup_width', popup_width );
			elementorFrontend.hooks.doAction( 'refresh_popup_options', 'popup_pos_first', $ );
		}, 1000 );

		$( document.body )
			.on( 'input', 'input[data-setting="popup_width"]', function( e ) {
				elementorFrontend.hooks.doAction( 'refresh_popup_options', 'popup_width', $( this ).val() );
			} )
			.on( 'input', 'input[data-setting="popup_pos_horizontal"], input[data-setting="popup_pos_vertical"]', function( e ) {
				elementorFrontend.hooks.doAction( 'refresh_popup_options', $( this ).data( 'settings' ), $ );
			} )
			.on( 'click', '.elementor-control-archive_preview_apply .elementor-button', function( e ) {
				$.post( porto_elementor_vars.ajax_url, {
					action: 'porto_archive_builder_preview_apply',
					nonce: porto_elementor_vars.nonce,
					post_id: ElementorConfig.document.id,
					mode: $( '.elementor-control-archive_preview_type select' ).val(),
				}, function() {
					elementor.reloadPreview();
				} );
			} )
			.on( 'click', '.elementor-control-single_preview_apply .elementor-button', function( e ) {
				$.post( porto_elementor_vars.ajax_url, {
					action: 'porto_single_builder_preview_apply',
					nonce: porto_elementor_vars.nonce,
					post_id: ElementorConfig.document.id,
					mode: $( '.elementor-control-single_preview_type select' ).val(),
				}, function() {
					elementor.reloadPreview();
				} );
			} );


		// edit area width
		var edit_area_width = elementor.settings.page.model.get( 'porto_edit_area_width' );
		if ( edit_area_width ) {
			var getValUnit = function( $arr, $default ) {
				if ( $arr ) {
					if ( $arr['size'] ) {
						return $arr['size'] + ( $arr['unit'] ? $arr['unit'] : 'px' );
					} else {
						return '';
					}
				}
				return typeof $default == 'undefined' ? '' : $default;
			}

			var triggerAction = function( e ) {
				var $selector = $( this );

				if ( e.type == 'mousemove' || e.type == 'click' ) {
					$selector = $selector.closest( '.elementor-control-input-wrapper' ).find( '.elementor-slider-input input' );
				}

				var value = {
					size: $selector.val(),
					unit: $selector.closest( '.elementor-control-input-wrapper' ).siblings( '.elementor-units-choices' ).find( 'input:checked' ).val()
				};

				elementorFrontend.hooks.doAction( 'refresh_edit_area', getValUnit( value ) );
			}

			setTimeout( function() {
				typeof edit_area_width != 'undefined' && elementorFrontend.hooks.doAction( 'refresh_edit_area', getValUnit( edit_area_width ) );
			}, 1000 );

			$( document.body ).on( 'input', '.elementor-control-porto_edit_area_width input[data-setting="size"]', triggerAction )
				.on( 'mousemove', '.elementor-control-porto_edit_area_width .noUi-active', triggerAction )
				.on( 'click', '.elementor-control-porto_edit_area_width .noUi-target', triggerAction );
		}

	} );

	var portoMasonryTimer = null;
	$( document.body )
		.on( 'input', '.elementor-control-width1 input[data-setting="size"]', function( e ) {
			if ( portoMasonryTimer ) {
				clearTimeout( portoMasonryTimer );
			}
			var $this = $( this );
			portoMasonryTimer = setTimeout( function() {
				elementorFrontend.hooks.doAction( 'masonry_refresh', false, $this.val() );
			}, 300 );
		} );
	$( document.body ).on( 'input', 'textarea[data-setting="porto_custom_css"]', function( e ) {
		elementorFrontend.hooks.doAction( 'refresh_dynamic_css', $( this ).val() );
	} ).on( 'click', '.porto-elementor-btn-reload', function( e ) {
		e.preventDefault();
		if ( !elementor.saver.isEditorChanged() ) {
			return false;
		}
		var $this = $( this );
		$this.attr( 'disabled', true );
		setTimeout( function() {
			$this.removeAttr( 'disabled' );
		}, 10000 );
		$e.run( 'document/save/auto', {
			force: true,
			onSuccess: function onSuccess() {
				elementor.reloadPreview();
				elementor.once( 'preview:loaded', function() {
					$e.route( 'panel/page-settings/settings' );
					$this.removeAttr( 'disabled' );
				} );
			}
		} );
	} ).on( 'change', 'select[data-setting="porto_header_type"]', function( e ) {
		if ( 'side' == $( this ).val() ) {
			$( '#elementor-preview-responsive-wrapper' ).addClass( 'mobile-width' );
		} else {
			$( '#elementor-preview-responsive-wrapper' ).removeClass( 'mobile-width' );
		}
	} );
} );