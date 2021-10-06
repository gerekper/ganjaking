const del = require( 'del' );
const config = require( '../config' );

const getIconPaths = ( target = 'admin' ) => ([
	`${ config.paths.root }/dev/icons/${ target }`,
	`${ config.paths.fonts }gform-icons-${ target }.*`,
	`${ config.paths.css_src }${ target }/icons/_icons.pcss`,
	`${ config.paths.css_src }${ target }/variables/_icons.pcss`,
]);

module.exports = {
	adminIconsStart() {
		return del( getIconPaths() );
	},
	adminIconsEnd() {
		return del( [
			`${ config.paths.root }gform-icons-admin*.zip`,
		] );
	},
	themeIconsStart() {
		return del( getIconPaths( 'theme' ) );
	},
	themeIconsEnd() {
		return del( [
			`${ config.paths.root }gform-icons-theme*.zip`,
		] );
	},
};
