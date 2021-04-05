<?php
/**
 * Storefront Powerpack Customizer Checkout Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Customizer_Checkout' ) ) :

	/**
	 * The Customizer class.
	 */
	class SP_Customizer_Checkout extends SP_Customizer {

		/**
		 * The id of this section.
		 *
		 * @const string
		 */
		const POWERPACK_CHECKOUT_SECTION = 'sp_checkout';

		/**
		 * Returns an array of the Storefront Powerpack setting defaults.
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function setting_defaults() {
			return $args = array(
				'sp_checkout_layout'           => 'default',
				'sp_distraction_free_checkout' => false,
				'sp_two_step_checkout'         => false,
				'sp_visit_checkout_prompt'     => '',
			);
		}

		/**
		 * Customizer Controls and Settings
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @since 1.0.0
		 */
		public function customize_register( $wp_customize ) {
			$is_checkout_block_active = SP_Helpers::is_checkout_block_active();

			/**
			 * Checkout Section
			 */
			$wp_customize->add_section(
				self::POWERPACK_CHECKOUT_SECTION,
				array(
					'title'    => __( 'Checkout', 'storefront-powerpack' ),
					'panel'    => self::POWERPACK_PANEL,
					'priority' => 30,
				)
			);

			/**
			 * Only include the following options if the WooCommerce Blocks checkout is not active.
			 */
			if ( ! $is_checkout_block_active ) {
				/**
				 * Checkout Layout
				 */
				$wp_customize->add_setting(
					'sp_checkout_layout',
					array(
						'sanitize_callback' => 'storefront_sanitize_choices',
					)
				);

				$wp_customize->add_control(
					new SP_Buttonset_Control(
						$wp_customize,
						'sp_checkout_layout',
						array(
							'label'    => __( 'Checkout layout', 'storefront-powerpack' ),
							'section'  => self::POWERPACK_CHECKOUT_SECTION,
							'settings' => 'sp_checkout_layout',
							'type'     => 'select',
							'priority' => 10,
							'choices'  => array(
								'default'             => 'Default',
								'stacked'             => 'Stacked',
								'two-column-addreses' => 'Columns',
							),
						)
					)
				);

				/**
				 * Two Step Checkout
				 */
				$wp_customize->add_setting(
					'sp_two_step_checkout',
					array(
						'sanitize_callback' => 'storefront_sanitize_checkbox',
					)
				);

				$wp_customize->add_control(
					new WP_Customize_Control(
						$wp_customize,
						'sp_two_step_checkout',
						array(
							'label'       => __( 'Two Step Checkout', 'storefront-powerpack' ),
							'description' => __( 'Separates the customer details collection form, and the order summary / payment details form in to two separate pages.', 'storefront-powerpack' ),
							'section'     => self::POWERPACK_CHECKOUT_SECTION,
							'settings'    => 'sp_two_step_checkout',
							'type'        => 'checkbox',
							'priority'    => 30,
						)
					)
				);
			}

			/**
			 * Distraction Free Checkout
			 */
			$wp_customize->add_setting( 'sp_distraction_free_checkout', array(
				'sanitize_callback' => 'storefront_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'sp_distraction_free_checkout', array(
				'label'       => __( 'Distraction Free Checkout', 'storefront-powerpack' ),
				'description' => __( 'Removes all clutter from the checkout, allowing the customer to focus entirely on that procedure.', 'storefront-powerpack' ),
				'section'     => self::POWERPACK_CHECKOUT_SECTION,
				'settings'    => 'sp_distraction_free_checkout',
				'type'        => 'checkbox',
				'priority'    => 20,
				)
			) );

			/**
			 * A prompt to visit the checkout page.
			 */
			if ( class_exists( 'Arbitrary_Storefront_Control' ) ) {
				$wp_customize->add_setting( 'sp_visit_checkout_prompt', array(
					'sanitize_callback' => 'sanitize_text_field',
				) );

				$wp_customize->add_control( new Arbitrary_Storefront_Control( $wp_customize, 'sp_visit_checkout_prompt', array(
					'description'     => '<div class="sp-section-notice"><span class="dashicons dashicons-info"></span>' . __( 'These settings do not affect the page you\'re currently previewing. Visit the checkout page to see their effects.', 'storefront-powerpack' ) . '</div>',
					'section'         => self::POWERPACK_CHECKOUT_SECTION,
					'type'            => 'text',
					'settings'        => 'sp_visit_checkout_prompt',
					'active_callback' => array( $this, 'is_not_checkout' ),
					'priority'        => 1,
				) ) );
			}
		}

		/**
		 * Is not checkout callback
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_not_checkout() {
			if ( ! is_checkout() ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Is not default checkout layout callback
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_not_default_layout() {
			if ( true === get_theme_mod( 'sp_distraction_free_checkout' ) || true === get_theme_mod( 'sp_two_step_checkout' ) ) {
				return false;
			}

			return true;
		}
	}

endif;

return new SP_Customizer_Checkout();