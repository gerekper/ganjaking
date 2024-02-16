/**
 * Check for dependencies
 *
 * @param {object} attributeArgs Attribute arguments.
 * @param {object} attributes The attributes.
 * @returns {boolean}
 */
export const checkForDeps = ( attributeArgs, attributes ) => {
	const { controlType } = attributeArgs;
	let show              = true;

	if ( attributeArgs.deps && attributeArgs.deps.id && 'value' in attributeArgs.deps ) {
		let depsValue = attributeArgs.deps.value;
		if ( 'toggle' === controlType || 'checkbox' === controlType ) {
			depsValue = true === depsValue || 'yes' === depsValue || 1 === depsValue;
		}
		show = typeof attributes[ attributeArgs.deps.id ] !== 'undefined' && depsValue === attributes[ attributeArgs.deps.id ];
	}

	return show;
};