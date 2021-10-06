const config = require( '../../../config' );

module.exports = {
	'scripts-theme': [
		'core-js/modules/es.array.iterator',
		`${ config.paths.js_src }/theme/index.js`,
	],
};
