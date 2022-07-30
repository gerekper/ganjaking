/**
 * Check for dependencies
 *
 * @param {object} attributeArgs Attribute arguments.
 * @param {object} attributes The attributes.
 * @returns {boolean}
 */

import _ from 'lodash';

const checkForSingleDep = ( attributes, dep, controlType ) => {
	let show = true;

	if ( dep && dep.id && 'value' in dep ) {
		let depValue = dep.value;
		if ( ['toggle', 'checkbox'].includes( controlType ) ) {
			depValue = true === depValue || 'yes' === depValue || 1 === depValue;
		}
		depValue = _.isArray( depValue ) ? depValue : [depValue];

		show = typeof attributes[ dep.id ] !== 'undefined' && depValue.includes( attributes[ dep.id ] );
	}

	return show;
};

export const checkForDeps = ( attributeArgs, attributes ) => {
	const { controlType } = attributeArgs;
	let show              = true;

	if ( attributeArgs.deps ) {
		if ( _.isArray( attributeArgs.deps ) ) {
			for ( let i in attributeArgs.deps ) {
				const singleDep = attributeArgs.deps[ i ];
				show            = checkForSingleDep( attributes, singleDep, controlType );
				if ( !show ) {
					break;
				}
			}
		} else {
			show = checkForSingleDep( attributes, attributeArgs.deps, controlType );
		}
	}

	return show;
};