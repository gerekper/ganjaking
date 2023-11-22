jQuery( window ).on( 'elementor:init', function() {
	var ControlMultipleBaseItemView = elementor.modules.controls.BaseMultiple,
	ControlTransformsItemView;

	ControlTransformsItemView = ControlMultipleBaseItemView.extend( {
		ui: function() {
			var ui = ControlMultipleBaseItemView.prototype.ui.apply( this, arguments );
			ui.controls = '.elementor-slider-input > input:enabled';
			ui.sliders = '.elementor-slider';
			ui.link = 'button.reset-controls';

			return ui;
		},
		events: function() {
			return _.extend( ControlMultipleBaseItemView.prototype.events.apply( this, arguments ), {
				'click @ui.link': 'onLinkResetTransforms'
			} );
		},

		defaultTransformsValue: {
			'angle': 0,
			'rotate_x': 0,
			'rotate_y': 0,
			'scale': 1,
			'translate_x': 0,
			'translate_y': 0,
			'translate_z': 0
		},
		onLinkResetTransforms: function( event ) {
			event.preventDefault();
			event.stopPropagation();

			this.ui.controls.val('');
			this.updateTransformations();
		},
		onSlideChange: function( event, ui ) {
			var type = event.currentTarget.dataset.input,
				$input = this.ui.input.filter( '[data-setting="' + type + '"]' );

			$input.val( ui.value );

		},

		initSliders: function() {
			var _this = this;
			var value = this.getControlValue();

			_this.ui.sliders.each( function(index, slider) {
				var $slider = jQuery( this ),
					$input = $slider.next( '.elementor-slider-input' ).find( 'input' );
					
					var sliderInstance = noUiSlider.create(slider, {
						start: [value[slider.dataset.input]],
						step: 1,
						range: {
							min: +$input.attr('min'),
							max: +$input.attr('max')
						},
						format: {
							to: function to(sliderValue) {
								return +sliderValue.toFixed(1);
							},
							from: function from(sliderValue) {
								return +sliderValue;
							}
						}
					});

					sliderInstance.on('slide', function (values) {
						var type = sliderInstance.target.dataset.input;

						$input.val(values[0]);

						_this.setValue(type, values[0]);
					});
			} );


		},
		onReady: function() {
			this.initSliders();
		},

		updateTransformations: function() {
			this.fillEmptyTransformations();
			this.updateTransformationsValue();
		},
		fillEmptyTransformations: function() {
			var transformations = this.getPossibleTransformations(),

				$controls = this.ui.controls,
				$sliders = this.ui.sliders,
				defaultTransformsValue = this.defaultTransformsValue;

			transformations.forEach( function( transform, index ) {
				var $slider = $sliders.filter( '[data-input="' + transform + '"]' );
				var $element = $controls.filter( '[data-setting="' + transform + '"]' );

				if ( $element.length && _.isEmpty( $element.val() ) ) {
					$element.val( defaultTransformsValue[transform] );
					$slider[0].noUiSlider.set( defaultTransformsValue[transform] );
				}

			} );
		},

		updateTransformationsValue: function() {
			var currentValue = {},
				transformations = this.getPossibleTransformations(),
				$controls = this.ui.controls,
				$sliders = this.ui.sliders,
				defaultTransformsValue = this.defaultTransformsValue;

			transformations.forEach( function( transform ) {
				var $element = $controls.filter( '[data-setting="' + transform + '"]' );

				currentValue[ transform ] = $element.length ? $element.val() : defaultTransformsValue;

				var $slider = $sliders.filter( '[data-input="' + transform + '"]' );

					$slider[0].noUiSlider.set($element.length ? $element.val() : defaultTransformsValue);

			} );

			this.setValue( currentValue );
		},

		getPossibleTransformations: function() {
			return [
				'angle',
				'rotate_x',
				'rotate_y',
				'scale',
				'translate_x',
				'translate_y',
				'translate_z'
				];
		},
		onInputChange: function( event ) {
			var type = event.currentTarget.dataset.setting,
				$slider = this.ui.sliders.filter( '[data-input="' + type + '"]' );
				$slider[0].noUiSlider.set(this.getControlValue(type));

			this.updateTransformations();
		},

	});
	elementor.addControlView( 'transforms', ControlTransformsItemView );
} );
