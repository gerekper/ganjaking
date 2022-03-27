import 'core-js/es/array/includes'; /* Polyfill */
import 'core-js/es/array/filter'; /* Polyfill */
import 'core-js/es/object/assign'; /* Polyfill */
import 'core-js/es/object/values'; /* Polyfill */
import 'core-js/es/object/entries'; /* Polyfill */
import 'core-js/es/number/is-finite'; /* Polyfill */
import 'core-js/es/string/starts-with'; /* Polyfill */
import 'es6-promise/auto'; /* Polyfill */

import GPFUPField from './GPFUPField';

for( const prop in window ) {
	if ( window.hasOwnProperty( prop ) &&
		( prop.indexOf( 'GPFUP_FORM_INIT_' ) === 0 )
	) {
		for ( const gpfupFieldInitSettings of window[prop] as unknown as GPFUPFieldInitSettings[] ) {
			const { formId, fieldId } = gpfupFieldInitSettings;

			window[`GPFUP_${formId}_${fieldId}`] = new GPFUPField(gpfupFieldInitSettings);
		}
	}
}
