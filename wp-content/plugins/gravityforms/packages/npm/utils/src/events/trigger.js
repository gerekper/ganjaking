import assign from '../data/object-assign';

export default function( opts ) {
	let event;
	const options = assign(
		{
			data: {},
			el: document,
			event: '',
			native: true,
		},
		opts
	);

	if ( options.native ) {
		event = document.createEvent( 'HTMLEvents' );
		event.initEvent( options.event, true, false );
	} else {
		try {
			event = new window.CustomEvent( options.event, {
				detail: options.data,
			} );
		} catch ( e ) {
			event = document.createEvent( 'CustomEvent' );
			event.initCustomEvent( options.event, true, true, options.data );
		}
	}

	options.el.dispatchEvent( event );
};
