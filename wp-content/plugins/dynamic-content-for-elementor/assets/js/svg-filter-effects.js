(
	function ( $ ) {
		var WidgetElements_SvgFilterEffectsHandler = function ( $scope,$ ) {
			var id_scope = $scope.attr( 'data-id' );
			var feDisp = $scope.find( 'feDisplacementMap#displacement-map' )[ 0 ];			
			var tl = gsap.timeline( {
				repeat: -1,
				repeatDelay: 1
			} );
						
			// pulisco tutto
			if ( elementorFrontend.isEditMode() ) {
				if ( tl ) {
					tl.kill( feDisp );
				}
				$( '.elementor-element[data-id=' + id_scope + '] svg' );
			}
		};
		
		$( window ).on( 'elementor/frontend/init',function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/dyncontel-filtereffects.default',
				WidgetElements_SvgFilterEffectsHandler );
		} );
	}
)( jQuery );
