/* =========================================================
 * templates-preview.js v1.0.0
 * =========================================================
 * Copyright 2015 WPBakery
 *
 * WPBakery Page Builder template preview
 * ========================================================= */
/* global vc */
(function ( $ ) {
	'use strict';
	if ( window.vc && vc.visualComposerView ) {
		// unset Draggable
		window.vc.visualComposerView.prototype.setDraggable = function () {
		};
		// unset Sortable
		window.vc.visualComposerView.prototype.setSortable = function () {
		};
		// unset Sortable
		window.vc.visualComposerView.prototype.setSorting = function () {
		};
		// unset save
		window.vc.visualComposerView.prototype.save = function () {
		};
		// unset controls checks for scroll
		window.vc.visualComposerView.prototype.navOnScroll = function () {
		};

		window.vc.visualComposerView.prototype.addElement = function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
		};

		window.vc.visualComposerView.prototype.addTextBlock = function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
		};

		window.vc.shortcode_view.prototype.events = {};
		window.vc.shortcode_view.prototype.editElement = function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
		};
		window.vc.shortcode_view.prototype.clone = function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
		};
		window.vc.shortcode_view.prototype.addElement = function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
		};
		window.vc.shortcode_view.prototype.deleteShortcode = function ( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}
		};
		window.vc.shortcode_view.prototype.setEmpty = function () {
		};
		window.vc.visualComposerView.prototype.events = {};
		//vc.shortcode_view.prototype.designHelpersSelector = '[data-js-handler-design-helper]';

		// update backend getView
		window.vc.visualComposerView.prototype.getView = function ( model ) {
			var view;
			if ( _.isObject( vc.map[ model.get( 'shortcode' ) ] ) && _.isString( vc.map[ model.get( 'shortcode' ) ].js_view ) && vc.map[ model.get( 'shortcode' ) ].js_view.length && !_.isUndefined(
				window[ window.vc.map[ model.get( 'shortcode' ) ].js_view ] ) ) {
				try {
					var viewConstructor = window[ window.vc.map[ model.get( 'shortcode' ) ].js_view ];
					viewConstructor.prototype.events = {};
					viewConstructor.prototype.setSortable = function () {
					};
					viewConstructor.prototype.setSorting = function () {
					};
					viewConstructor.prototype.setDropable = function () {
					};
					viewConstructor.prototype.editElement = function ( e ) {
						if ( e && e.preventDefault ) {
							e.preventDefault();
						}
					};
					viewConstructor.prototype.clone = function ( e ) {
						if ( e && e.preventDefault ) {
							e.preventDefault();
						}
					};
					viewConstructor.prototype.addElement = function ( e ) {
						if ( e && e.preventDefault ) {
							e.preventDefault();
						}
					};
					viewConstructor.prototype.deleteShortcode = function ( e ) {
						if ( e && e.preventDefault ) {
							e.preventDefault();
						}
					};
					viewConstructor.prototype.setEmpty = function () {
					};
					viewConstructor.prototype.events = {};
					//	viewConstructor.prototype.designHelpersSelector = '[data-js-handler-design-helper]';
					view = new viewConstructor( { model: model } );
				} catch ( err ) {
					if ( window.console && window.console.warn ) {
						window.console.warn( 'template preview getView error', err );
					}
				}
			} else {
				window.vc.shortcode_view.prototype.events = {};
				view = new vc.shortcode_view( { model: model } );
			}
			model.set( { view: view } );
			return view;
		};

		window.vc.visualComposerView.prototype.initializeAccessPolicy = function () {
			this.accessPolicy = {
				be_editor: true,
				fe_editor: false,
				classic_editor: false
			};
		};
	}

	if ( window.VcGitemView ) {
		window.VcGitemView.prototype.setDropable = function () {
		};
		window.VcGitemView.prototype.setDraggable = function () {
		};
		window.VcGitemView.prototype.setDraggableC = function () {
		};

	}

	if ( window.vc && window.vc.events ) {
		window.vc.events.on( 'shortcodeView:ready', function ( view ) {
			if ( window.VcGitemView ) {
				view.$el.find( '.vc_control-btn.vc_element-name.vc_element-move .vc_btn-content' ).attr( 'style', 'cursor:pointer !important;' + 'padding-left: 10px !important;' );
				view.$el.find( '.vc_control-btn.vc_element-name.vc_element-move .vc_btn-content .vc-c-icon-dragndrop' ).hide();
				if ( 'vc_gitem' === view.model.get( 'shortcode' ) ) {
					view.$el.find( '.vc_gitem-add-c-col:not(.vc_zone-added)' ).remove();
				}
			}
			if ( view.$el ) {
				// remove TTA section append
				view.$el.find( '.vc_tta-section-append' ).remove();
				// remove old TTA tour append
				view.$el.find( '.add_tab_block' ).remove();
				view.$el.find( '.tab_controls' ).remove();
				// remove single image "add-image" link
				view.$el.find( '.column_edit_trigger' ).remove();
			}
		} );
	}

	window.vc.events.on( 'app.addAll', function () {
		if ( parent && parent.vc ) {
			parent.vc.templates_panel_view.setTemplatePreviewSize();
		}
	} );
	$( window ).on( 'resize', function () {
		parent.vc.templates_panel_view.setTemplatePreviewSize();
	} );
})( window.jQuery );