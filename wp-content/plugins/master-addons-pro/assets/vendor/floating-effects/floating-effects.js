/* global elementorModules */
/* global elementorFrontend */

(function ($, w) {
	var $window = $( w );

	function debounce(func, wait, immediate) {
		var timeout;
		return function () {
			var context = this, args = arguments;
			var later   = function () {
				timeout = null;
				if ( ! immediate ) {
					func.apply( context, args );
				}
			};
			var callNow = immediate && ! timeout;
			clearTimeout( timeout );
			timeout = setTimeout( later, wait );
			if ( callNow ) {
				func.apply( context, args );
			}
		};
	}

	$window.on(
		'elementor/frontend/init',
		function () {

			var ExtensionHandler = elementorModules.frontend.handlers.Base.extend(
				{
					onInit: function () {
						elementorModules.frontend.handlers.Base.prototype.onInit.apply( this, arguments );
						this.widgetContainer = this.$element.find( '.elementor-widget-container' )[0];
						this.initFloatingEffects();
					},

					getDefaultSettings: function () {
						return {
							targets: this.widgetContainer,
							loop: true,
							direction: 'alternate',
							easing: 'easeInOutSine',
						};
					},

					onElementChange: function (changedProp) {
						if ( changedProp.indexOf( 'jltma_floating_effects' ) !== -1 ) {
							this.runOnElementChange();
						}
					},

					getConfig: function (key) {
						return this.getElementSettings( 'jltma_floating_effects_' + key );
					},

					initFloatingEffects: function () {
						var config = this.getDefaultSettings();

						var isDisabledOnReduced = this.getElementSettings('jltma_transform_reduced_motion');
						if ( window.matchMedia( '(prefers-reduced-motion)' ).matches && isDisabledOnReduced ) {
							return false;
						}

						if ( this.getConfig( 'translate_toggle' ) ) {
							if ( this.getConfig( 'translate_x.size' ) || this.getConfig( 'translate_x.sizes.to' ) ) {
								config.translateX = {
									value: [ this.getConfig( 'translate_x.sizes.from' ) || 0, this.getConfig( 'translate_x.size' ) || this.getConfig( 'translate_x.sizes.to' ) ],
									duration: this.getConfig( 'translate_duration.size' ),
									delay: this.getConfig( 'translate_delay.size' ) || 0
								};
							}
							if ( this.getConfig( 'translate_y.size' ) || this.getConfig( 'translate_y.sizes.to' ) ) {
								config.translateY = {
									value: [ this.getConfig( 'translate_y.sizes.from' ) || 0, this.getConfig( 'translate_y.size' ) || this.getConfig( 'translate_y.sizes.to' ) ],
									duration: this.getConfig( 'translate_duration.size' ),
									delay: this.getConfig( 'translate_delay.size' ) || 0
								};
							}
						}

						if ( this.getConfig( 'rotate_toggle' ) ) {
							if ( this.getConfig( 'rotate_x.size' ) || this.getConfig( 'rotate_x.sizes.to' ) ) {
								config.rotateX = {
									value: [ this.getConfig( 'rotate_x.sizes.from' ) || 0, this.getConfig( 'rotate_x.size' ) || this.getConfig( 'rotate_x.sizes.to' ) ],
									duration: this.getConfig( 'rotate_duration.size' ),
									delay: this.getConfig( 'rotate_delay.size' ) || 0
								};
							}
							if ( this.getConfig( 'rotate_y.size' ) || this.getConfig( 'rotate_y.sizes.to' ) ) {
								config.rotateY = {
									value: [ this.getConfig( 'rotate_y.sizes.from' ) || 0, this.getConfig( 'rotate_y.size' ) || this.getConfig( 'rotate_y.sizes.to' ) ],
									duration: this.getConfig( 'rotate_duration.size' ),
									delay: this.getConfig( 'rotate_delay.size' ) || 0
								};
							}
							if ( this.getConfig( 'rotate_z.size' ) || this.getConfig( 'rotate_z.sizes.to' ) ) {
								config.rotateZ = {
									value: [ this.getConfig( 'rotate_z.sizes.from' ) || 0, this.getConfig( 'rotate_z.size' ) || this.getConfig( 'rotate_z.sizes.to' ) ],
									duration: this.getConfig( 'rotate_duration.size' ),
									delay: this.getConfig( 'rotate_delay.size' ) || 0
								};
							}
						}

						if ( this.getConfig( 'scale_toggle' ) ) {
							if ( this.getConfig( 'scale_x.size' ) || this.getConfig( 'scale_x.sizes.to' ) ) {
								config.scaleX = {
									value: [ this.getConfig( 'scale_x.sizes.from' ) || 0, this.getConfig( 'scale_x.size' ) || this.getConfig( 'scale_x.sizes.to' ) ],
									duration: this.getConfig( 'scale_duration.size' ),
									delay: this.getConfig( 'scale_delay.size' ) || 0
								};
							}
							if ( this.getConfig( 'scale_y.size' ) || this.getConfig( 'scale_y.sizes.to' ) ) {
								config.scaleY = {
									value: [ this.getConfig( 'scale_y.sizes.from' ) || 0, this.getConfig( 'scale_y.size' ) || this.getConfig( 'scale_y.sizes.to' ) ],
									duration: this.getConfig( 'scale_duration.size' ),
									delay: this.getConfig( 'scale_delay.size' ) || 0
								};
							}
						}

						if ( this.getConfig( 'translate_toggle' ) || this.getConfig( 'rotate_toggle' ) || this.getConfig( 'scale_toggle' ) ) {
							this.widgetContainer.style.setProperty( 'will-change', 'transform' );
							this.animation = anime( config );
						}
					},

					runOnElementChange: debounce(
						function () {
							this.animation && this.animation.restart();
							this.initFloatingEffects();
						},
						200
					),
				}
			);

			elementorFrontend.hooks.addAction(
				'frontend/element_ready/widget',
				function ($scope) {
					elementorFrontend.elementsHandler.addHandler( ExtensionHandler, {$element: $scope} );
				}
			);
		}
	);
}(jQuery, window));
