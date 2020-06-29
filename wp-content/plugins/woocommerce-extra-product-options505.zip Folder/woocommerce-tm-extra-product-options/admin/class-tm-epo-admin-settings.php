<?php
/**
 * Extra Product Options Settings class
 *
 * Add the plugin settings to the WooCommerce settings
 *
 * @package Extra Product Options/Admin
 * @version 4.8
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Settings_Page' ) ) {

	class THEMECOMPLETE_EPO_ADMIN_SETTINGS extends WC_Settings_Page {

		public $settings_options = array();
		public $settings_array = array();
		private $tab_count = 0;

		/**
		 * The single instance of the class
		 *
		 * @since 1.0
		 */
		protected static $_instance = NULL;

		/**
		 * Ensures only one instance of the class is loaded or can be loaded.
		 *
		 * @since 1.0
		 * @static
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;

		}

		/**
		 * Class Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {

			$this->id               = THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID;
			$this->label            = esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-product-options' );
			$this->tab_count        = 0;
			$this->settings_options = THEMECOMPLETE_EPO_SETTINGS()->settings_options();

			foreach ( $this->settings_options as $key => $value ) {
				$this->settings_array[ $key ] = THEMECOMPLETE_EPO_SETTINGS()->get_setting_array( $key, $value );
			}

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );

			add_action( 'woocommerce_admin_field_tm_tabs_header', array( $this, 'tm_tabs_header_setting' ) );
			add_action( 'woocommerce_admin_field_tm_title', array( $this, 'tm_title_setting' ) );
			add_action( 'woocommerce_admin_field_tm_html', array( $this, 'tm_html_setting' ) );
			add_action( 'woocommerce_admin_field_tm_sectionend', array( $this, 'tm_sectionend_setting' ) );

			add_action( 'tm_woocommerce_settings_' . 'epo_page_options', array( $this, 'tm_settings_hook' ) );
			add_action( 'tm_woocommerce_settings_' . 'epo_page_options' . '_end', array( $this, 'tm_settings_hook_end' ) );

			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'tm_settings_hook_all_end' ) );

			add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_css_code', array( $this, 'tm_return_raw' ), 10, 3 );
			add_action( 'woocommerce_admin_settings_sanitize_option_tm_epo_js_code', array( $this, 'tm_return_raw' ), 10, 3 );

			add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		}

		/**
		 * Add admin body class
		 *
		 * @since 1.0
		 */
		public function admin_body_class( $classes ) {

			if ( isset( $_GET["hidemenu"] ) || ( isset( $_GET["page"] ) && $_GET["page"] === "tcepo-settings" ) ) {
				$classes .= ' tc-hide-nav woocommerce_page_wc-settings';
			}

			return $classes;

		}

		/**
		 * Returns the provided raw value
		 *
		 * @since 1.0
		 */
		public function tm_return_raw( $value, $option, $raw_value ) {
			return $raw_value;
		}

		/**
		 * Prints a tab header
		 *
		 * @since 1.0
		 */
		public function tm_echo_header( $counter = 0, $label = "" ) { ?>
            <div class="tm-box">
                <a tabindex="0" class="tab-header <?php echo( $counter == 1 ? 'open' : 'closed' ); ?>" data-id="tmsettings<?php echo esc_attr( $counter ); ?>-tab">
					<?php
					if ( is_array( $label ) ) {
						echo "<i class=\"tab-header-icon " . esc_attr( $label[0] ) . "\"></i>";
						echo esc_html( $label[1] );
					} else {
						echo esc_html( $label );
					}
					?>
                    <span class="tcfa tm-arrow2 tcfa-angle-down2"></span>
                </a>
            </div>
			<?php
		}

		/**
		 * Section tab start
		 *
		 * @since 1.0
		 */
		public function tm_title_setting( $value ) {

			if ( ! empty( $value['id'] ) ) {
				do_action( 'tm_woocommerce_settings_' . sanitize_title( $value['id'] ) );
			}

			if ( ! empty( $value['title'] ) ) {
				if ( is_array( $value['title'] ) ) {
					$value['title'] = $value['title'][1];
				}
				echo '<div class="tm-section-title">' . esc_html( $value['title'] );
				if ( ! empty( $value['desc'] ) ) {
					echo '<div class="tm-section-desc">' . apply_filters( 'wc_epo_kses', wp_kses( $value['desc'], array( "span" => array( "tabindex" => TRUE, "class" => TRUE, "data-menu" => TRUE, "data-*" => TRUE ) ) ), $value['desc'], FALSE ) . '</div>';
				}
				echo '</div>';
			}

			echo '<div class="tm-table-wrap">';
			echo '<table class="form-table">' . "\n\n";

		}

		/**
		 * Setting row
		 *
		 * @since 1.0
		 */
		public function tm_html_setting( $value ) {

			if ( ! isset( $value['id'] ) ) {
				$value['id'] = '';
			}

			if ( ! isset( $value['title'] ) ) {
				$value['title'] = isset( $value['name'] ) ? $value['name'] : '';
			}

			if ( ! empty( $value['id'] ) ) {
				do_action( 'tm_woocommerce_settings_' . sanitize_title( $value['id'] ) );
			}
			?>
            <tr valign="top">
                <td colspan="2" class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
					<?php
					if ( ! empty( $value['html'] ) ) {
						echo apply_filters( 'wc_epo_kses', wp_kses_post( $value['html'] ), $value['html'], FALSE );
					}
					?>
                </td>
            </tr>
			<?php

		}

		/**
		 * Section tab end
		 *
		 * @since 1.0
		 */
		public function tm_sectionend_setting( $value ) {

			echo '</table>';
			echo '</div>'; // .tm-table-wrap

			if ( ! empty( $value['id'] ) ) {
				do_action( 'tm_woocommerce_settings_' . sanitize_title( $value['id'] ) . '_end' );
			}

		}

		/**
		 * Right panel start
		 *
		 * @since 1.0
		 */
		public function tm_tabs_header_setting() {

			echo '<div class="tm-settings-wrap tm_wrapper">';
			echo '<div class="transition tm-tabs">';
			echo '<div class="transition tm-tab-headers tmsettings-tab">';

			echo '<div class="tm-sidebar-head">';
			echo '<h2>' . esc_html__( 'EPO Control Panel', 'woocommerce-tm-extra-checkout-options' ) . ' <span>' . THEMECOMPLETE_EPO_VERSION . '</span></h2>';
			echo '</div>';

			$counter = 1;
			foreach ( $this->settings_options as $key => $label ) {
				if ( $key == "other" ) {
					$_other_settings = THEMECOMPLETE_EPO_SETTINGS()->get_other_settings_headers();
					foreach ( $_other_settings as $h_key => $h_label ) {
						$this->tm_echo_header( $counter, $h_label );
						$counter ++;
					}
				} else {
					$this->tm_echo_header( $counter, $label );
					$counter ++;
				}
			}

			echo '</div>';
			echo '<div class="tm-tabs-wrapper">';
			echo '<div class="header tm-flexrow tm-justify-content-between"><h3 class="tm-flexcol">' . esc_html__( 'Extra Product Options Settings', 'woocommerce-tm-extra-product-options' ) . '</h3>';
			echo '<button type="submit" class="tc tc-button tc-save-button tm-flexcol" type="submit">' . esc_html__( 'Save changes', 'woocommerce-tm-extra-product-options' ) . '</button>';
			echo '</div>';

		}

		/**
		 * Section wrap start
		 *
		 * @since 1.0
		 */
		public function tm_settings_hook() {
			$this->tab_count ++;
			echo '<div class="transition tm-tab tmsettings' . esc_attr( $this->tab_count ) . '-tab">';
		}

		/**
		 * Section wrap end
		 *
		 * @since 1.0
		 */
		public function tm_settings_hook_end() {
			echo '</div>';
		}

		public function tm_settings_hook_all_end() {
			echo '</div>'; // .tm-tabs-wrapper
			echo '<div class="tm-footer"><button type="submit" class="tc tc-button tc-save-button" type="submit">' . esc_html__( 'Save changes', 'woocommerce-tm-extra-product-options' ) . '</button></div>';
			echo '</div>'; // .transition.tm-tabs
			echo '</div>'; //.tm-settings-wrap
		}

		/**
		 * Get settings array
		 *
		 * @since 1.0
		 */
		public function get_settings() {

			$settings = array();
			$settings = array_merge( $settings, array( array( 'type' => 'tm_tabs_header' ) ) );

			foreach ( $this->settings_array as $key => $value ) {
				$settings = array_merge( $settings, $value );
			}

			return apply_filters( 'tm_' . $this->id . '_settings',
				$settings
			);

		}
	}

}
