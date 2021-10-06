export default function( functionToCheck ) {
	return functionToCheck && {}.toString.call( functionToCheck ) === '[object Function]';
}
