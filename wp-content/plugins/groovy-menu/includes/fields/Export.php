<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuFieldExport
 */
class GroovyMenuFieldExport extends GroovyMenuFieldField {

	public function renderField() {
		?>
		<div class="gm-gui__module__ui gm-gui__module__export">
			<input type="button" class="gm-gui__export-button" value="Export"/>
		</div>
		<?php
	}
}
