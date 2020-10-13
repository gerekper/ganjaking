<?php

namespace WPML\PB\Gutenberg\StringsInBlock\DOMHandler;

class HtmlBlock extends StandardBlock {

	/**
	 * @param \DOMNode $element
	 * @param string   $context
	 *
	 * @return array
	 */
	protected function getInnerHTML( \DOMNode $element, $context ) {
		$innerHTML = $element instanceof \DOMText
			? $element->nodeValue
			: $this->getInnerHTMLFromChildNodes( $element, $context );

		return [
			$this->removeCdataFromStyleTag( html_entity_decode( $innerHTML ) ),
			'AREA'
		];
	}
}
