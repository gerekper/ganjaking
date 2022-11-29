// JavaScript Document
$jvh = jQuery.noConflict();
$jvh( '.ultimate-margin-inputs' ).on( 'change', function ( e ) {
	$umargin = $jvh( this ).parent();
	let temp = '';
	$umargin
		.find( '.ultimate-margin-inputs' )
		.each( function ( input_index, input ) {
			const margin_parameter = $jvh( input ).attr( 'data-hmargin' );
			let input_value = $jvh( input ).val();
			if ( input_value != '' ) {
				if ( input_value.match( /^[0-9]+$/ ) ) input_value += 'px';
				temp += 'margin-' + margin_parameter + ':' + input_value + ';';
			}
		} );
	$umargin.find( '.ultimate-margin-value' ).val( temp );
} );
$jvh( '.ultimate-margins' ).each( function ( index, element ) {
	$umargin = $jvh( this );
	const ultimate_margin_value = $umargin
		.find( '.ultimate-margin-value' )
		.val();
	if ( ultimate_margin_value != '' ) {
		const vals = ultimate_margin_value.split( ';' );
		$jvh.each( vals, function ( i, vl ) {
			if ( vl != '' ) {
				const splitval = vl.split( ':' );
				const margin_value = splitval[ 1 ];
				const param = splitval[ 0 ].split( '-' );
				const margin_parameter = param[ 1 ];
				$umargin
					.find( '.ultimate-margin-inputs' )
					.each( function ( input_index, input ) {
						const input_margin_parameter = $jvh( input ).attr(
							'data-hmargin'
						);
						if ( margin_parameter == input_margin_parameter )
							$jvh( input ).val( margin_value );
					} );
			}
		} );
	}
} );
