<?php
/**
 * Info Field.
 *
 * @package     weLaunchFramework/Fields
 * @author      Dovy Paukstys & Kevin Provance (kprovance)
 * @version     4.0.0
 */

defined( 'ABSPATH' ) || exit;

// Don't duplicate me!
if ( ! class_exists( 'weLaunch_Info', false ) ) {

	/**
	 * Main weLaunch_info class
	 *
	 * @since       1.0.0
	 */
	class weLaunch_Info extends weLaunch_Field {

		/**
		 * Set field and value defaults.
		 */
		public function set_defaults() {
			$defaults = array(
				'title'  => '',
				'desc'   => '',
				'indent' => false,
				'notice' => true,
				'style'  => '',
				'color'  => '',
			);

			$this->field = wp_parse_args( $this->field, $defaults );
		}

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {
			$styles = array(
				'normal',
				'info',
				'warning',
				'success',
				'critical',
				'custom',
			);

			if ( ! in_array( $this->field['style'], $styles, true ) ) {
				$this->field['style'] = 'normal';
			}

			if ( 'custom' === $this->field['style'] ) {
				if ( ! empty( $this->field['color'] ) ) {
					$this->field['color'] = 'border-color:' . $this->field['color'] . ';';
				} else {
					$this->field['style'] = 'normal';
					$this->field['color'] = '';
				}
			} else {
				$this->field['color'] = '';
			}

			if ( empty( $this->field['desc'] ) && ! empty( $this->field['default'] ) ) {
				$this->field['desc'] = $this->field['default'];
				unset( $this->field['default'] );
			}

			if ( empty( $this->field['desc'] ) && ! empty( $this->field['subtitle'] ) ) {
				$this->field['desc'] = $this->field['subtitle'];
				unset( $this->field['subtitle'] );
			}

			if ( empty( $this->field['desc'] ) ) {
				$this->field['desc'] = '';
			}

			if ( empty( $this->field['raw_html'] ) ) {
				if ( true === $this->field['notice'] ) {
					$this->field['class'] .= ' welaunch-notice-field';
				} else {
					$this->field['class'] .= ' welaunch-info-field';
				}

				$this->field['style'] = 'welaunch-' . $this->field['style'] . ' ';
			}

			// Old shim, deprecated arg.
			if ( isset( $this->field['sectionIndent'] ) ) {
				$this->field['indent'] = $this->field['sectionIndent'];
			}
			$indent = ( isset( $this->field['indent'] ) && $this->field['indent'] ) ? ' form-table-section-indented' : '';

			echo '</td></tr></table>';
			echo '<div 
					id="info-' . esc_attr( $this->field['id'] ) . '" 
					class="' . ( isset( $this->field['icon'] ) && ! empty( $this->field['icon'] ) && true !== $this->field['icon'] ? 'hasIcon ' : '' ) . esc_attr( $this->field['style'] ) . ' ' . esc_attr( $this->field['class'] ) . ' welaunch-field-' . esc_attr( $this->field['type'] ) . esc_attr( $indent ) . '"' . ( ! empty( $this->field['color'] ) ? ' style="' . esc_attr( $this->field['color'] ) . '"' : '' ) . '>';

			if ( ! empty( $this->field['raw_html'] ) && $this->field['raw_html'] ) {
				echo wp_kses_post( $this->field['desc'] );
			} else {
				if ( isset( $this->field['title'] ) && ! empty( $this->field['title'] ) ) {
					$this->field['title'] = '<b>' . wp_kses_post( $this->field['title'] ) . '</b><br/>';
				}

				if ( isset( $this->field['icon'] ) && ! empty( $this->field['icon'] ) && true !== $this->field['icon'] ) {
					echo '<p class="welaunch-info-icon"><i class="' . esc_attr( $this->field['icon'] ) . ' icon-large"></i></p>';
				}

				if ( isset( $this->field['raw'] ) && ! empty( $this->field['raw'] ) ) {
					echo wp_kses_post( $this->field['raw'] );
				}

				if ( ! empty( $this->field['title'] ) || ! empty( $this->field['desc'] ) ) {
					echo '<p class="welaunch-info-desc">' . wp_kses_post( $this->field['title'] ) . wp_kses_post( $this->field['desc'] ) . '</p>';
				}
			}

			echo '</div>';
			echo '<table class="form-table no-border" style="margin-top: 0;">';
			echo '<tbody>';
			echo '<tr style="border-bottom:0; display:none;">';
			echo '<th style="padding-top:0;"></th>';
			echo '<td style="padding-top:0;">';
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function enqueue() {
			if ( $this->parent->args['dev_mode'] ) {
				wp_enqueue_style(
					'welaunch-field-info-css',
					weLaunch_Core::$url . 'inc/fields/info/welaunch-info.css',
					array(),
					$this->timestamp,
					'all'
				);
			}
		}
	}
}

class_alias( 'weLaunch_Info', 'weLaunchFramework_Info' );
