const config = require( '../../../config' );

module.exports = {
	'scripts-admin': [
		'core-js/modules/es.array.iterator',
		`${ config.paths.js_src }/admin/index.js`,
	],
};
