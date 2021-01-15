<?php
/**
 * Customizer Control.
 *
 * @package     weLaunch Framework/Extensions
 * @version     3.5
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Customizer_Control', false ) ) {

	/**
	 * Class weLaunch_Customizer_Control
	 */
	class weLaunch_Customizer_Control extends WP_Customize_Control {

		/**
		 * Field render.
		 */
		public function render() {

			$this->welaunch_id = str_replace( 'customize-control-', '', 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) ) );
			$class          = 'customize-control welaunch-group-tab welaunch-field customize-control-' . $this->type;
			$opt_name_arr   = explode( '[', $this->id );
			$opt_name       = $opt_name_arr[0];
			$field_id       = str_replace( ']', '', $opt_name_arr[1] );

			$section = weLaunch_Helpers::section_from_field_id( $opt_name, $field_id );

			if ( isset( $section['disabled'] ) && true === $section['disabled'] ) {
				$class .= ' disabled';
			}

			if ( isset( $section['hidden'] ) && true === $section['hidden'] ) {
				$class .= ' hidden';
			}

			?>
			<li id="<?php echo esc_attr( $this->welaunch_id ); ?>-li" class="<?php echo esc_attr( $class ); ?>">
				<?php if ( 'repeater' !== $this->type ) { ?>
					<input
						type="hidden"
						data-id="<?php echo esc_attr( $this->id ); ?>"
						data-key="<?php echo esc_attr( str_replace( $opt_name . '-', '', $this->welaunch_id ) ); ?>"
						class="welaunch-customizer-input"
						id="customizer_control_id_<?php echo esc_attr( $this->welaunch_id ); ?>" <?php echo esc_url( $this->link() ); ?>
						value=""/>
				<?php } ?>
				<?php $this->render_content(); ?>
			</li>
			<?php
		}

		/**
		 * Redner content hook.
		 */
		public function render_content() {
			// phpcs:ignore WordPress.NamingConventions.ValidHookName
			do_action( 'welaunch/advanced_customizer/control/render/' . $this->welaunch_id, $this );
		}

		/**
		 * Label output.
		 */
		public function label() {
			// The label has already been sanitized in the Fields class, no need to re-sanitize it.
			echo( $this->label ); // phpcs:ignore WordPress.Security.EscapeOutput
		}

		/**
		 * Description output.
		 */
		public function description() {
			if ( ! empty( $this->description ) ) {
				// The description has already been sanitized in the Fields class, no need to re-sanitize it.
				echo '<span class="description customize-control-description">' . esc_html( $this->description ) . '</span>';
			}
		}

		/**
		 * Title output.
		 */
		public function title() {
			echo '<span class="customize-control-title">';
			$this->label();
			$this->description();
			echo '</span>';
		}
	}
}
