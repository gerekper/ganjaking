<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class FieldInlineEnd
 */
class FieldInlineEnd extends \GroovyMenu\FieldField {

	/**
	 * Footer of inline field
	 */
	public function render() {
		?>
		</div>
		<?php
	}
}
