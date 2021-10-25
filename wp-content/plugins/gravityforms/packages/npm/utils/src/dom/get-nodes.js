import convertElements from './convert-elements';

/**
 * Should be used at all times for getting nodes throughout our app. Please use the data-js attribute whenever possible
 *
 * @param selector The selector string to search for. If arg 4 is false (default) then we search for [data-js="selector"]
 * @param convert Convert the NodeList to an array? Then we can Array.forEach directly. Uses convertElements from above
 * @param node Parent node to search from. Defaults to document
 * @param custom Is this a custom selector where we don't want to use the data-js attribute?
 * @return {NodeList|HTMLElement}
 */

export default function(
	selector = '',
	convert = false,
	node = document,
	custom = false
) {
	const selectorString = custom ? selector : `[data-js="${ selector }"]`;
	let nodes = node.querySelectorAll( selectorString );
	if ( convert ) {
		nodes = convertElements( nodes );
	}
	return nodes;
};
