( function( $, api ) {

	api.controlConstructor['kia-radio-image'] = api.Control.extend(
		{

			ready: function() {
				var control = this;

				this.container.on(
					'change',
					'input:radio',
					function() {
							value = $( this ).val();
							control.setting.set( value );
					}
				);

			}
		}
	);

} )( jQuery, wp.customize );
