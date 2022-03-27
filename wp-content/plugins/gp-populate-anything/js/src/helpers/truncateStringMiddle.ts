export default function truncateStringMiddle( str: string ) {
	const maxLength = 50;

	if ( ! str ) {
		return str;
	}

	if ( str.length > maxLength ) {
		return (
			str.substr( 0, maxLength * 0.45 ) +
			' ... ' +
			str.substr( str.length - maxLength * 0.4, str.length )
		);
	}

	return str;
}
