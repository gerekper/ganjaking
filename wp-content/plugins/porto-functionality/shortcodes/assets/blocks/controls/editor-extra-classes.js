export const portoAddHelperClasses = function( elClass, clientId ) {
	if ( typeof elClass == 'undefined' ) {
		return elClass;
	}
	elClass = elClass.trim();
	if ( !elClass || !clientId ) {
		return elClass;
	}
	const c_arr = ['d-inline-block', 'd-sm-inline-block', 'd-md-inline-block', 'd-lg-inline-block', 'd-xl-inline-block', 'd-none', 'd-sm-none', 'd-md-none', 'd-lg-none', 'd-xl-none', 'd-block', 'd-sm-block', 'd-md-block', 'd-lg-block', 'd-xl-block', 'd-sm-flex', 'd-md-flex', 'd-lg-flex', 'd-xl-flex', 'col-auto', 'col-md-auto', 'col-lg-auto', 'col-xl-auto', 'flex-1', 'flex-none', 'flex-grow-1', 'flex-sm-grow-1', 'flex-md-grow-1', 'flex-lg-grow-1', 'flex-xl-grow-1'];
	const remove_c_arr = ['ml-auto', 'ms-auto', 'mr-auto', 'me-auto', 'mx-auto', 'ml-sm-auto', 'ms-sm-auto', 'mr-sm-auto', 'me-sm-auto', 'mx-sm-auto', 'ml-md-auto', 'ms-md-auto', 'mr-md-auto', 'me-md-auto', 'mx-md-auto', 'ml-lg-auto', 'ms-lg-auto', 'mr-lg-auto', 'me-lg-auto', 'mx-lg-auto', 'ml-xl-auto', 'ms-xl-auto', 'mr-xl-auto', 'me-xl-auto', 'mx-xl-auto', 'h-100', 'h-50', 'w-100', 'float-start', 'float-end', 'pull-left', 'pull-right', 'float-left', 'float-right', 'me-lg-4', 'pe-lg-1'];
	/*for ( var i = 1; i <= 12; i++ ) {
		remove_c_arr.push( 'col-' + i );
		remove_c_arr.push( 'col-sm-' + i );
		remove_c_arr.push( 'col-md-' + i );
		remove_c_arr.push( 'col-lg-' + i );
		remove_c_arr.push( 'col-xl-' + i );
	}*/
	var blockObj = null,
		iframe = document.querySelector( '[name="editor-canvas"]' );
	if ( iframe && iframe.contentDocument ) {
		blockObj = iframe.contentDocument.getElementById( 'block-' + clientId );
	} else {
		blockObj = document.getElementById( 'block-' + clientId );
	}
	if ( blockObj ) {
		blockObj.setAttribute( 'data-class', '' );
		elClass.split( ' ' ).forEach( function( cls ) {
			cls = cls.trim();
			if ( cls && ( -1 !== c_arr.indexOf( cls ) || -1 !== remove_c_arr.indexOf( cls ) ) ) {
				// blockObj.classList.add( cls );
				blockObj.setAttribute( 'data-class', blockObj.getAttribute( 'data-class' ) + cls + ' ' );

				/*if ( -1 !== remove_c_arr.indexOf( cls ) ) {
					elClass = elClass.replace( cls, '' ).trim();
				}*/
			}
		} );
	}

	return elClass;
};