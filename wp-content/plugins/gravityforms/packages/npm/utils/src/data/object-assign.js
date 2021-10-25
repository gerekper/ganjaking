export default function() {
	const resObj = {};
	for ( let i = 0; i < arguments.length; i += 1 ) {
		const obj = arguments[ i ];
		const keys = Object.keys( obj );
		for ( let j = 0; j < keys.length; j += 1 ) {
			resObj[ keys[ j ] ] = obj[ keys[ j ] ];
		}
	}
	return resObj;
}
