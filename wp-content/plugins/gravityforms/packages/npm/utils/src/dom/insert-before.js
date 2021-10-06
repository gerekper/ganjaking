/**
 * Insert a node before another node
 *
 * @param newNode {HTMLElement|NodeList}
 * @param referenceNode {HTMLElement|NodeList}
 */

export default function( newNode, referenceNode ) {
	referenceNode.parentNode.insertBefore( newNode, referenceNode );
};
