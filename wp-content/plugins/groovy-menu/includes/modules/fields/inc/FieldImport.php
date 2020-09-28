<?php

namespace GroovyMenu;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuFieldImport
 */
class FieldImport extends \GroovyMenu\FieldField {

	/**
	 * Render
	 */
	public function renderField() {
		?>
		<div class="gm-gui__module__ui gm-gui__module__import">
			<input type="file" name="import"/>
			<input type="hidden" value="" name="is-import" class="gm-is-import"/>
			<button type="submit" value="import" class="gm-gui__import-button"><?php esc_html_e( 'Import', 'groovy-menu' ); ?></button>
		</div>
		<?php
	}
}
