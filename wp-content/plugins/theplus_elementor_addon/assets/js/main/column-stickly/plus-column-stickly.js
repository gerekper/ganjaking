/* plus extra Column Sticky option*/
( function( $ ) {
	'use strict';
	var PlusExtra = {
		init: function() {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/column', PlusExtra.plus_Sticky_Column );
		},
		plus_Sticky_Column: function( $scope ) {
			var $target  = $scope,
				$window  = $( window ),
				column_widget_Id = $target.data( 'id' ),
				editMode = Boolean( elementorFrontend.isEditMode() ),
				settings = {},
				stickyInst = null,
				stickyInstOptions = {
					topSpacing: 40,
					bottomSpacing: 40,
					containerSelector: '.elementor-container',
					innerWrapperSelector: '.elementor-column'
				};
				
			if ( ! editMode ) {
				settings = $target.data( 'plus-sticky-column-settings' );

				if ( $target.hasClass( 'plus-sticky-column-sticky' ) ) {
					$target.css("-webkit-align-self","flex-start").css("-ms-flex-item-align","start").css("align-self","flex-start");
					if ( -1 !== settings['stickyOn'].indexOf( elementorFrontend.getCurrentDeviceMode() ) ) {

						stickyInstOptions.topSpacing = settings['topSpacing'];
						stickyInstOptions.bottomSpacing = settings['bottomSpacing'];

						$target.data( 'stickyColumnInit', true );
						stickyInst = new StickySidebar( $target[0], stickyInstOptions );

						$window.on( 'resize.PlusExtraColumnSticky orientationchange.PlusExtraColumnSticky', PlusExtraTools.debounce( 50, columnResizeDebounce ) );
					}
				}
			} else {
				settings = PlusExtra.columnStickySettings( column_widget_Id );

				if ( 'true' === settings['sticky'] ) {
					$target.addClass( 'plus-sticky-column-sticky' );
					$target.css("-webkit-align-self","flex-start").css("-ms-flex-item-align","start").css("align-self","flex-start");
					if ( -1 !== settings['stickyOn'].indexOf( elementorFrontend.getCurrentDeviceMode() ) ) {
						stickyInstOptions.topSpacing = settings['topSpacing'];
						stickyInstOptions.bottomSpacing = settings['bottomSpacing'];

						$target.data( 'stickyColumnInit', true );
						stickyInst = new StickySidebar( $target[0], stickyInstOptions );

						$window.on( 'resize.PlusExtraColumnSticky orientationchange.PlusExtraColumnSticky', PlusExtraTools.debounce( 50, columnResizeDebounce ) );
					}
				}
			}

			function columnResizeDebounce() {
				var Device_Mode = elementorFrontend.getCurrentDeviceMode(),					
					isInitColumn            = $target.data( 'stickyColumnInit' ),
					availableDevices  = settings['stickyOn'] || [];
					
				if ( -1 !== availableDevices.indexOf( Device_Mode ) ) {

					if ( ! isInitColumn ) {
						$target.data( 'stickyColumnInit', true );
						stickyInst = new StickySidebar( $target[0], stickyInstOptions );
						stickyInst.updateSticky();
					}
				} else {
					$target.data( 'stickyColumnInit', false );
					stickyInst.destroy();
				}
			}

		},

		columnStickySettings: function( column_widget_Id ) {
			var editorElements = null,
				columnDataOpt     = {};

			if ( ! window.elementorFrontend.hasOwnProperty( 'elements' ) ) {
				return false;
			}

			editorElements = window.elementorFrontend.elements;

			if ( ! editorElements.models ) {
				return false;
			}

			$.each( editorElements.models, function( index, obj ) {

				$.each( obj.attributes.elements.models, function( index, obj ) {
					if ( column_widget_Id == obj.id ) {
						columnDataOpt = obj.attributes.settings.attributes;
					}
				} );

			} );
			
			return {				
				'topSpacing': columnDataOpt['plus_sticky_top_spacing'] || 40,
				'bottomSpacing': columnDataOpt['plus_sticky_bottom_spacing'] || 40,
				'sticky': columnDataOpt['plus_column_sticky'] || false,
				'stickyOn': columnDataOpt['plus_sticky_enable_on'] || [ 'desktop', 'tablet', 'mobile']
			}
		},

	};

	$( window ).on( 'elementor/frontend/init', PlusExtra.init );

	var PlusExtraTools = {
		debounce: function( threshold, callback ) {
			var timeout;

			return function debounced( $event ) {
				function delayed() {
					callback.call( this, $event );
					timeout = null;
				}

				if ( timeout ) {
					clearTimeout( timeout );
				}

				timeout = setTimeout( delayed, threshold );
			};
		}
	}

}( jQuery, window.elementorFrontend ) );

/* plus extra Column Sticky option*/