<?php
/**
 * Class YITH_WCBK_Printer
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Printer' ) ) {
	/**
	 * Class YITH_WCBK_Printer
	 */
	class YITH_WCBK_Printer {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Print fields
		 *
		 * @param array $args Arguments.
		 */
		public function print_fields( $args = array() ) {
			$args = apply_filters( 'yith_wcbk_printer_print_fields_args', $args );
			if ( isset( $args['type'] ) ) {
				$args = array( $args );
			}
			foreach ( $args as $field_args ) {
				$this->print_field( $field_args );
			}
		}

		/**
		 * Print single field
		 *
		 * @param array $args Arguments.
		 */
		public function print_field( $args = array() ) {
			$default_args = array(
				'type'              => '',
				'id'                => '',
				'name'              => '',
				'value'             => '',
				'class'             => '',
				'custom_attributes' => '',
				'for'               => '',
				'options'           => array(),
				'data'              => array(),
				'title'             => '',
				'fields'            => array(),
				'help_tip'          => '',
				'help_tip_alt'      => '',
				'desc'              => '',
				'desc_box'          => '',
				'section_html_tag'  => 'div',
			);
			$args         = apply_filters( 'yith_wcbk_printer_print_field_args', $args );

			if ( isset( $args['id'] ) && ! isset( $args['name'] ) ) {
				$args['name'] = $args['id'];
			}

			$args = wp_parse_args( $args, $default_args );

			$type         = $args['type'];
			$title        = $args['title'];
			$help_tip     = $args['help_tip'];
			$help_tip_alt = $args['help_tip_alt'];
			$desc         = $args['desc'];
			$desc_box     = $args['desc_box'];

			if ( is_array( $args['custom_attributes'] ) ) {
				$args['custom_attributes'] = yith_plugin_fw_html_attributes_to_string( $args['custom_attributes'] );
			}

			if ( ! empty( $title ) && 'checkbox' !== $type ) {
				$this->print_field(
					array(
						'type'  => 'label',
						'value' => $title,
						'for'   => $args['id'],
					)
				);
			}

			switch ( $type ) {
				case 'section':
					$fields = $args['fields'];
					unset( $args['fields'] );

					$args['type'] = 'section-start';
					$this->print_field( $args );

					if ( ! empty( $fields ) ) {
						$this->print_fields( $fields );
					} elseif ( ! empty( $args['value'] ) ) {
						$this->print_field(
							array(
								'type'  => 'html',
								'value' => $args['value'],
							)
						);
					}

					$args['type'] = 'section-end';
					$this->print_field( $args );
					break;
				default:
					if ( ! empty( $args['yith-field'] ) ) {
						unset( $args['yith-field'] );
						$show_container = $args['yith-wcbk-field-show-container'] ?? true;
						if ( isset( $args['yith-wcbk-field-show-container'] ) ) {
							unset( $args['yith-wcbk-field-show-container'] );
						}

						yith_plugin_fw_get_field( $args, true, $show_container );
					} elseif ( file_exists( YITH_WCBK_TEMPLATE_PATH . 'printer/types/' . $type . '.php' ) ) {
						wc_get_template( 'printer/types/' . $type . '.php', $args, '', YITH_WCBK_TEMPLATE_PATH );
					}
					break;
			}

			if ( ! empty( $title ) && 'checkbox' === $type ) {
				$this->print_field(
					array(
						'type'  => 'label',
						'value' => $title,
						'for'   => $args['id'],
					)
				);
			}

			if ( ! in_array( $type, array( 'section', 'section-start' ), true ) ) {
				if ( ! empty( $help_tip ) ) {
					$this->print_field(
						array(
							'type'  => 'help-tip',
							'value' => $help_tip,
						)
					);
				} elseif ( ! empty( $help_tip_alt ) ) {
					$this->print_field(
						array(
							'type'  => 'help-tip-alt',
							'value' => $help_tip_alt,
						)
					);
				} elseif ( ! empty( $desc ) ) {
					$this->print_field(
						array(
							'type'             => 'section',
							'value'            => $desc,
							'class'            => 'description',
							'section_html_tag' => 'span',
						)
					);
				}

				if ( ! empty( $desc_box ) ) {
					$this->print_field(
						array(
							'type'  => 'desc-box',
							'value' => $desc_box,
						)
					);
				}
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCBK_Printer class
 *
 * @return YITH_WCBK_Printer
 */
function yith_wcbk_printer() {
	return YITH_WCBK_Printer::get_instance();
}
