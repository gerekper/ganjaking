/**
 * Insert a node after another node
 *
 * @param newNode {Element|NodeList}
 * @param referenceNode {Element|NodeList}
 */
export default function( newNode, referenceNode ) {
	referenceNode.parentNode.insertBefore(
		newNode,
		referenceNode.nextElementSibling
	);
};
