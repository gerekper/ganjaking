window.addEventListener( 'load' , function() {

	document.querySelectorAll( '.gform-settings-field__select_custom select' ).forEach( function( $select ) {

		$select.addEventListener( 'change', function( e ) {

			if ( e.target.value !== 'gf_custom' ) {
				return;
			}

			// Hide drop down, show input.
			$select.style.display = 'none';
			$select.nextSibling.style.display = 'block';

		} );

	} );

	document.querySelectorAll( '.gform-settings-select-custom__reset' ).forEach( function( $button ) {

		$button.addEventListener( 'click', function( e ) {

			// Hide input, show drop down.
			$button.parentNode.style.display = 'none';
			$button.parentNode.previousSibling.value = '';
			$button.parentNode.previousSibling.style.display = 'block';

		} );

	} );

} );
