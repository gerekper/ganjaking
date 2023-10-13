( function( $, api ) {

	api.controlConstructor['kia-range'] = api.Control.extend(
		{

			ready: function() {
				var control = this;

				this.container.on(
					'change',
					'input[data-input-type="range"]',
					function() {
							value = $( this ).val();
							$( this ).prev( '.kia-range__number-input' ).val( value );
							control.setting.set( value );
					}
				);

				$( '.kia-range__reset' ).on(
					'click',
					function() {
						var
						input        = $( this ).prev( $( 'input[data-input-type="range"]' ) ),
						defaultValue = input.data( 'default-value' );

						input.val( defaultValue );

						var value = input.val();
						input.prev( '.kia-range__number-input' ).val( value );
						input.change();
					}
				);

				$( '.kia-range__number-input' ).on(
					'keyup',
					function() {
						if (this.value !== '') {
							if (parseInt(this.value) < parseInt(this.min)) {
							  this.value = this.min;
							}
							if (parseInt(this.value) > parseInt(this.max)) {
							  this.value = this.max;
							}
						}
					}
				);

			}
		}
	);

} )( jQuery, wp.customize );
