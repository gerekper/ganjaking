<?php
/**
 * Extra Product Options Settings class
 *
 * @package Extra Product Options/Admin
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Settings_Page' ) ) {

	/**
	 * Extra Product Options Settings class
	 *
	 * Add the plugin settings to the WooCommerce settings
	 *
	 * @package Extra Product Options/Admin
	 * @version 6.0
	 */
	class THEMECOMPLETE_EPO_ADMIN_SETTINGS extends WC_Settings_Page {

		/**
		 * The admin settings internal id
		 *
		 * @var string
		 */
		public $id = '';

		/**
		 * Options
		 *
		 * @var array
		 */
		public $settings_options = [];

		/**
		 * Settings
		 *
		 * @var array
		 */
		public $settings_array = [];

		/**
		 * Tab count
		 *
		 * @var int
		 */
		private $tab_count = 0;

		/**
		 * The single instance of the class
		 *
		 * @var THEMECOMPLETE_EPO_ADMIN_SETTINGS|null $instance
		 */
		protected static $instance = null;

		/**
		 * Ensures only one instance of the class is loaded or can be loaded.
		 *
		 * @since 1.0
		 * @static
		 */
		public static function instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;

		}

		/**
		 * Class Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {

			$this->id               = THEMECOMPLETE_EPO_ADMIN_SETTINGS_ID;
			$this->label            = esc_html__( 'Extra Product Options', 'woocommerce-tm-extra-checkout-options' );
			$this->tab_count        = 0;
			$this->settings_options = THEMECOMPLETE_EPO_SETTINGS()->settings_options();

			foreach ( $this->settings_options as $key => $value ) {
				$this->settings_array[ $key ] = THEMECOMPLETE_EPO_SETTINGS()->create_setting( $key, $value );
			}

			add_filter( 'woocommerce_settings_tabs_array', [ $this, 'add_settings_page' ], 20 );
			add_action( 'woocommerce_settings_' . $this->id, [ $this, 'output' ] );
			add_action( 'woocommerce_settings_save_' . $this->id, [ $this, 'save' ] );

			add_action( 'woocommerce_admin_field_tm_tabs_header', [ $this, 'tm_tabs_header_setting' ] );
			add_action( 'woocommerce_admin_field_tm_title', [ $this, 'tm_title_setting' ] );
			add_action( 'woocommerce_admin_field_tm_html', [ $this, 'tm_html_setting' ] );
			add_action( 'woocommerce_admin_field_tm_sectionend', [ $this, 'tm_sectionend_setting' ] );

			add_action( 'tm_woocommerce_settings_epo_page_options', [ $this, 'tm_settings_hook' ] );
			add_action( 'tm_woocommerce_settings_epo_page_options_end', [ $this, 'tm_settings_hook_end' ] );

			add_action( 'woocommerce_settings_' . $this->id, [ $this, 'tm_settings_hook_all_end' ] );

			add_filter( 'admin_body_class', [ $this, 'admin_body_class' ] );

			add_action( 'admin_footer', [ $this, 'script_templates' ] );
		}

		/**
		 * Print script templates
		 *
		 * @since 1.0
		 */
		public function script_templates() {
			// The check is required in case other plugin do things that don't load the wc_get_template function.
			if ( function_exists( 'wc_get_template' ) ) {
				wc_get_template( 'tc-js-admin-templates.php', [], null, THEMECOMPLETE_EPO_PLUGIN_PATH . '/assets/js/admin/' );
			}

		}

		/**
		 * Add admin body class
		 *
		 * @param string $classes classes.
		 * @since 1.0
		 */
		public function admin_body_class( $classes ) {

			if ( isset( $_GET['hidemenu'] ) || ( isset( $_GET['page'] ) && 'tcepo-settings' === $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$classes .= ' tc-hide-nav woocommerce_page_wc-settings';
			}

			return $classes;

		}

		/**
		 * Prints a tab header
		 *
		 * @param integer $counter the counter.
		 * @param string  $label the label array.
		 * @since 1.0
		 */
		public function tm_echo_header( $counter = 0, $label = '' ) { ?>
			<div class="tm-box">
				<a tabindex="0" class="tab-header <?php echo( 1 === $counter ? 'open' : 'closed' ); ?>" data-id="tmsettings<?php echo esc_attr( $counter ); ?>-tab">
					<?php
					if ( is_array( $label ) ) {
						echo '<i class="tab-header-icon ' . esc_attr( $label[0] ) . '"></i>';
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
		 * @param array $value the value array.
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
					// phpcs:ignore WordPress.Security.EscapeOutput
					echo '<div class="tm-section-desc">' . apply_filters(
						'wc_epo_kses',
						wp_kses(
							$value['desc'],
							[
								'span' => [
									'tabindex'  => true,
									'class'     => true,
									'data-menu' => true,
									'data-*'    => true,
								],
							]
						),
						$value['desc'],
						false
					) . '</div>';
				}
				echo '</div>';
			}

			echo '<div class="tm-table-wrap">';
			echo '<table class="form-table">' . "\n\n";

		}

		/**
		 * Setting row
		 *
		 * @param array $value the value array.
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
						echo apply_filters( 'wc_epo_kses', wp_kses_post( $value['html'] ), $value['html'], false ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</td>
			</tr>
			<?php

		}

		/**
		 * Section tab end
		 *
		 * @param array $value the value array.
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

			echo '<div class="tm-settings-wrap tc-wrapper">';
			echo '<div class="transition tm-tabs">';
			echo '<div class="transition tm-tab-headers tmsettings-tab">';

			echo '<div class="tm-sidebar-head">';
			echo '<h2>' . esc_html__( 'EPO Control Panel', 'woocommerce-tm-extra-checkout-options' ) . ' <span>' . esc_html( THEMECOMPLETE_EPO_VERSION ) . '</span></h2>';
			echo '</div>';

			$counter = 1;
			foreach ( $this->settings_options as $key => $label ) {
				if ( 'other' === $key ) {
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
			echo '<div class="tm-flexcol">';
			echo '<button type="submit" class="tc tc-button tc-reset-button tm-flexcol" type="submit">' . esc_html__( 'Reset settings', 'woocommerce-tm-extra-product-options' ) . '</button>';
			echo '&nbsp;<button type="submit" class="tc tc-button tc-save-button tm-flexcol" type="submit">' . esc_html__( 'Save changes', 'woocommerce-tm-extra-product-options' ) . '</button>';
			echo '</div>';
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

		/**
		 * Additional html
		 *
		 * @return void
		 */
		public function tm_settings_hook_all_end() {
			echo '</div>'; // .tm-tabs-wrapper
			echo '<div class="tm-footer">';
			echo '<div class="tm-flexcol">';
			echo '<button type="submit" class="tc tc-button tc-reset-button tm-flexcol" type="submit">' . esc_html__( 'Reset settings', 'woocommerce-tm-extra-product-options' ) . '</button>';
			echo '&nbsp;<button type="submit" class="tc tc-button tc-save-button tm-flexcol" type="submit">' . esc_html__( 'Save changes', 'woocommerce-tm-extra-product-options' ) . '</button>';
			echo '</div>';
			echo '</div>';
			echo '</div>'; // .transition.tm-tabs
			echo '</div>'; // .tm-settings-wrap
		}

		/**
		 * Get settings array
		 *
		 * @since 1.0
		 */
		public function get_settings() {

			$settings = [];
			$settings = array_merge( $settings, [ [ 'type' => 'tm_tabs_header' ] ] );

			foreach ( $this->settings_array as $key => $value ) {
				$settings = array_merge( $settings, $value );
			}

			return apply_filters(
				'tm_' . $this->id . '_settings',
				$settings
			);

		}
	}

}
