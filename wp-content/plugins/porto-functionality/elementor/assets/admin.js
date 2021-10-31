function porto_elementor_add_floating_options( settings ) {
	if ( !settings.floating_start_pos || !settings.floating_speed ) {
		return '';
	}
	let floating_options = { 'startPos': settings.floating_start_pos, 'speed': settings.floating_speed };
	if ( !settings.floating_transition || 'yes' == settings.floating_transition ) {
		floating_options[ 'transition' ] = true;
	} else {
		floating_options[ 'transition' ] = false;
	}
	if ( settings.floating_horizontal ) {
		floating_options[ 'horizontal' ] = true;
	} else {
		floating_options[ 'horizontal' ] = false;
	}
	if ( settings.floating_duration ) {
		floating_options[ 'transitionDuration' ] = parseInt( settings.floating_duration, 10 );
	}
	return ' data-plugin-float-element data-plugin-options=' + JSON.stringify( floating_options );
}

jQuery( document ).ready( function ( $ ) {
	// add Porto Studio menu
	elementor.on( 'panel:init', function () {
		$( '<div id="porto-elementor-panel-porto-studio" class="elementor-panel-footer-tool tooltip-target" data-tooltip="Porto Studio"><i class="porto-icon-studio" aria-hidden="true"></i><span class="elementor-screen-only">Porto Studio</span></div>' ).insertAfter( '#elementor-panel-footer-saver-preview' ).tipsy( {
			gravity: 's',
			title: function title() {
				return this.getAttribute( 'data-tooltip' );
			}
		} );
	} );

	elementor.on( 'frontend:init', function () {
		var custom_css = elementor.settings.page.model.get( 'porto_custom_css' );
		if ( typeof custom_css != 'undefined' ) {
			setTimeout( function () {
				elementorFrontend.hooks.doAction( 'refresh_dynamic_css', custom_css );
			}, 1000 );
		}

		var header_type = elementor.settings.page.model.get( 'porto_header_type' );
		if ( 'side' == header_type ) {
			$( '#elementor-preview-responsive-wrapper' ).addClass( 'mobile-width' );
		}

		var popup_width = elementor.settings.page.model.get( 'popup_width' );

		setTimeout( function () {
			typeof popup_width != 'undefined' && elementorFrontend.hooks.doAction( 'refresh_popup_options', 'popup_width', popup_width );
			elementorFrontend.hooks.doAction( 'refresh_popup_options', 'popup_pos_first', $ );
		}, 1000 );

		$( document.body )
			.on( 'input', 'input[data-setting="popup_width"]', function ( e ) {
				elementorFrontend.hooks.doAction( 'refresh_popup_options', 'popup_width', $( this ).val() );
			} )
			.on( 'input', 'input[data-setting="popup_pos_horizontal"], input[data-setting="popup_pos_vertical"]', function ( e ) {
				elementorFrontend.hooks.doAction( 'refresh_popup_options', $( this ).data( 'settings' ), $ );
			} );

        // edit area width
        var edit_area_width = elementor.settings.page.model.get( 'porto_edit_area_width' );
        if( edit_area_width ) {
        	var getValUnit = function ( $arr, $default ) {
	            if ( $arr ) {
	                if ( $arr['size'] ) {
	                    return $arr[ 'size' ] + ( $arr[ 'unit' ] ? $arr[ 'unit' ] : 'px' );
	                } else {
	                    return '';
	                }
	            }
	            return typeof $default == 'undefined' ? '' : $default;
	        }

            var triggerAction = function(e) {
                var $selector = $(this);

                if ( e.type=='mousemove' || e.type=='click' ) {
                    $selector = $selector.closest('.elementor-control-input-wrapper').find('.elementor-slider-input input');
                }

                var value = { 
                        size: $selector.val(),
                        unit: $selector.closest( '.elementor-control-input-wrapper' ).siblings( '.elementor-units-choices' ).find( 'input:checked' ).val()
                    };

                elementorFrontend.hooks.doAction( 'refresh_edit_area', getValUnit(value) );
            }

            setTimeout( function () {
                typeof edit_area_width != 'undefined' && elementorFrontend.hooks.doAction( 'refresh_edit_area', getValUnit( edit_area_width ) );
            }, 1000 );

            $( document.body ).on('input', '.elementor-control-porto_edit_area_width input[data-setting="size"]', triggerAction )
                              .on('mousemove', '.elementor-control-porto_edit_area_width .noUi-active', triggerAction)
                              .on('click', '.elementor-control-porto_edit_area_width .noUi-target', triggerAction);
		}

	} );

	var portoMasonryTimer = null;
	$( document.body ).on( 'input', '.elementor-control-width1 input[data-setting="size"]', function ( e ) {
		if ( portoMasonryTimer ) {
			clearTimeout( portoMasonryTimer );
		}
		var $this = $( this );
		portoMasonryTimer = setTimeout( function () {
			elementorFrontend.hooks.doAction( 'masonry_refresh', false, $this.val() );
		}, 300 );
	} );

	$( document.body ).on( 'input', 'textarea[data-setting="porto_custom_css"]', function ( e ) {
		elementorFrontend.hooks.doAction( 'refresh_dynamic_css', $( this ).val() );
	} ).on( 'click', '.porto-elementor-btn-reload', function ( e ) {
		e.preventDefault();
		if ( !elementor.saver.isEditorChanged() ) {
			return false;
		}
		var $this = $( this );
		$this.attr( 'disabled', true );
		setTimeout( function () {
			$this.removeAttr( 'disabled' );
		}, 10000 );
		$e.run( 'document/save/auto', {
			force: true,
			onSuccess: function onSuccess() {
				elementor.reloadPreview();
				elementor.once( 'preview:loaded', function () {
					$e.route( 'panel/page-settings/settings' );
					$this.removeAttr( 'disabled' );
				} );
			}
		} );
	} ).on( 'change', 'select[data-setting="porto_header_type"]', function ( e ) {
		if ( 'side' == $( this ).val() ) {
			$( '#elementor-preview-responsive-wrapper' ).addClass( 'mobile-width' );
		} else {
			$( '#elementor-preview-responsive-wrapper' ).removeClass( 'mobile-width' );
		}
	} );

} );