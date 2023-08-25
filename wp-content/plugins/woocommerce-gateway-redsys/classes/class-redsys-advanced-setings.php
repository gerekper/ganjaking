<?php
/**
 * Redsys Advanced Settings
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 23.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Redsys_Advanced_Settings' ) ) :

	class Redsys_Advanced_Settings {

		public static function init() {
			add_filter( 'woocommerce_settings_tabs_array', array( __CLASS__, 'add_settings_tab' ), 50 );
			add_action( 'woocommerce_settings_tabs_redsys_advanced', array( __CLASS__, 'settings_tab' ) );
			add_action( 'woocommerce_settings_save_redsys_advanced', array( __CLASS__, 'save_settings' ) );
			add_action( 'woocommerce_sections_redsys_advanced', array( __CLASS__, 'output_sections' ) );
			add_action( 'woocommerce_admin_field_redsysradiopatern', __CLASS__ . '::redsys_radio_pattern' );
			add_action( 'woocommerce_admin_field_redsysradioborder', __CLASS__ . '::redsys_radio_border' );
			add_action( 'woocommerce_admin_field_redsysradiomcenter', __CLASS__ . '::redsys_radio_mcenter' );
			add_action( 'woocommerce_admin_field_redsysradioframe', __CLASS__ . '::redsys_radio_frame' );
		}

		public static function add_settings_tab( $settings_tabs ) {
			$settings_tabs['redsys_advanced'] = esc_html__( 'Redsys Advanced', 'woocommerce-redsys' );
			return $settings_tabs;
		}

		public static function settings_tab() {
			global $current_section;
			$sections      = self::get_sections();
			$first_section = array_keys( $sections )[0];
			if ( $current_section == '' ) {
				$current_section = $first_section;
			}
			woocommerce_admin_fields( self::get_settings( $current_section ) );
		}

		public static function save_settings() {
			global $current_section;
			$sections      = self::get_sections();
			$first_section = array_keys( $sections )[0];
			if ( $current_section == '' ) {
				$current_section = $first_section;
			}
			woocommerce_update_options( self::get_settings( $current_section ) );
		}

		public static function output_sections() {
			global $current_section;
			$sections = self::get_sections();
			if ( empty( $sections ) ) {
				return;
			}
			echo '<ul class="subsubsub">';
			$array_keys = array_keys( $sections );
			foreach ( $array_keys as $id ) {
				echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=redsys_advanced&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . esc_html( $sections[ $id ] ) . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
			}
			echo '</ul><br class="clear" />';
			echo '
			<style>
			.radio_redsys{
				display: none !important;
			}
			.label_redsys {
				opacity: 0.2;
				padding-left: 12px;
			}
			label {
				cursor: pointer;
			}
			.radio_redsys:checked + label {
				opacity: 1;
			}
			</style>';
		}

		public static function get_sections() {
			$sections = array(
				''                   => esc_html__( 'General', 'woocommerce-redsys' ),
				'push_notifications' => esc_html__( 'Push Notifications', 'woocommerce-redsys' ),
				'sequential_invoice' => esc_html__( 'Sequential invoice number', 'woocommerce-redsys' ),
				'qr_codes'           => esc_html__( 'QR Codes', 'woocommerce-redsys' ),
				'saved_cards'        => esc_html__( 'Saved Cards (Tokenization)', 'woocommerce-redsys' ),
			);
			return $sections;
		}
		/**
		 * Add radio pattern
		 *
		 * @param string $value performated value.
		 */
		public static function redsys_radio_pattern( $value ) {
			$option_value = get_option( 'redsys_qr_type' );
			if ( ! $option_value ) {
				$option_value = 'default';
			}
			?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<fieldset>
					<ul>
					<?php
					foreach ( $value['options'] as $key => $val ) {
						$image = REDSYS_PLUGIN_URL_P . 'assets/images/svg/pattern/' . $val;
						?>
							<input type="radio" class="radio_redsys" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $option_value ); ?>/>
							<label class="label_redsys" for="<?php echo esc_html( $key ); ?>"> <img src="<?php echo esc_url( $image ); ?>"></label>
						<?php
					}
					?>
					</ul>
				</fieldset>
			</td>
		</tr>
			<?php
		}
		/**
		 * Add radio border pattern
		 *
		 * @param string $value performated value.
		 */
		public static function redsys_radio_border( $value ) {
			$option_value = get_option( 'redsys_qr_border' );
			if ( ! $option_value ) {
				$option_value = 'default';
			}
			?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<fieldset>
					<ul>
					<?php
					foreach ( $value['options'] as $key => $val ) {
						$image = REDSYS_PLUGIN_URL_P . 'assets/images/svg/border/' . $val;
						?>
							<input type="radio" class="radio_redsys" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $option_value ); ?>/>
							<label class="label_redsys" for="<?php echo esc_html( $key ); ?>"> <img src="<?php echo esc_url( $image ); ?>"></label>
						<?php
					}
					?>
					</ul>
				</fieldset>
			</td>
		</tr>
			<?php
		}
		/**
		 * Add radio mcenter pattern
		 *
		 * @param string $value performated value.
		 */
		public static function redsys_radio_mcenter( $value ) {
			$option_value = get_option( 'redsys_qr_mcenter' );
			if ( ! $option_value ) {
				$option_value = 'default';
			}
			?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<fieldset>
					<ul>
					<?php
					foreach ( $value['options'] as $key => $val ) {
						$image = REDSYS_PLUGIN_URL_P . 'assets/images/svg/mcenter/' . $val;
						?>
							<input type="radio" class="radio_redsys" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $option_value ); ?>/>
							<label class="label_redsys" for="<?php echo esc_html( $key ); ?>"> <img src="<?php echo esc_url( $image ); ?>"></label>
						<?php
					}
					?>
					</ul>
				</fieldset>
			</td>
		</tr>
			<?php
		}
		/**
		 * Add radio frame pattern
		 *
		 * @param string $value performated value.
		 */
		public static function redsys_radio_frame( $value ) {
			$option_value = get_option( 'redsys_qr_frame' );
			if ( ! $option_value ) {
				$option_value = 'none';
			}
			?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<fieldset>
					<ul>
					<?php
					foreach ( $value['options'] as $key => $val ) {
						$image = REDSYS_PLUGIN_URL_P . 'assets/images/svg/frame/' . $val;
						?>
							<input type="radio" class="radio_redsys" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $option_value ); ?>/>
							<label class="label_redsys" for="<?php echo esc_html( $key ); ?>"> <img src="<?php echo esc_url( $image ); ?>"></label>
						<?php
					}
					?>
					</ul>
				</fieldset>
			</td>
		</tr>
			<?php
		}

		public static function get_settings( $section = null ) {
			switch ( $section ) {
				case 'push_notifications':
					$readonly = array(
						'checked'  => 'checked',
						'disabled' => 'disabled',
					);
					$settings = array(
						array(
							'title' => esc_html__( 'Redsys Push Notifications (by José Conti)', 'woocommerce-redsys' ),
							'type'  => 'title',
							'id'    => 'wc_settings_tab_redsys_sort_push_title',
						),
						array(
							'title'   => esc_html__( 'Enable Push Notifications', 'woocommerce-redsys' ),
							'type'    => 'checkbox',
							'default' => 'no',
							'id'      => 'wc_settings_tab_redsys_sort_push_is_active',
						),
						array(
							'title' => esc_html__( 'Access Token', 'woocommerce-redsys' ),
							'type'  => 'text',
							'id'    => 'wc_settings_tab_redsys_sort_push_access_token',
						),
						array(
							'title' => esc_html__( 'Mobile App ID', 'woocommerce-redsys' ),
							'type'  => 'text',
							'id'    => 'wc_settings_tab_redsys_sort_push_mobile_app_id',
						),
						array(
							'title' => esc_html__( 'Identifier', 'woocommerce-redsys' ),
							'type'  => 'text',
							'desc'  => esc_html__( 'Your mobile with country code, Ex, 34666666666', 'woocommerce-redsys' ),
							'id'    => 'wc_settings_tab_redsys_sort_push_identifier',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'wc_settings_tab_redsys_sort_push_section_end',
						),
						array(
							'title' => esc_html__( 'Notification Types', 'woocommerce-redsys' ),
							'type'  => 'title',
							'id'    => 'wc_settings_tab_redsys_sort_push_notifications_title',
						),
						array(
							'title'             => __( 'Errors', 'woocommerce-redsys' ),
							'type'              => 'checkbox',
							'default'           => 'yes',
							'custom_attributes' => $readonly,
							'id'                => 'wc_settings_tab_redsys_sort_push_notify_errors',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'wc_settings_tab_redsys_sort_push_notifications_section_end',
						),
					);
					break;
				case 'sequential_invoice':
					$settings = array(
						array(
							'title' => __( 'Sequential Invoice Numbers', 'woocommerce-redsys' ),
							'type'  => 'title',
							'desc'  => '',
							'id'    => 'wc_settings_tab_redsys_sort_invoices_title',
						),
						array(
							'title'   => esc_html__( 'Activate Sequential Invoice numbers', 'woocommerce-redsys' ),
							'type'    => 'checkbox',
							'label'   => esc_html__( 'Activate Sequential Invoice numbers.', 'woocommerce-redsys' ),
							'default' => 'no',
							'desc'    => esc_html__( 'Activate Sequential Invoice numbers', 'woocommerce-redsys' ),
							'id'      => 'wc_settings_tab_redsys_sort_invoices_is_active',
						),
						array(
							'name' => esc_html__( 'First Invoice Number', 'woocommerce-redsys' ),
							'type' => 'text',
							'desc' => esc_html__( 'Add here the first invoice number. By Default is number 1. Save this number before activate it. Example 345 ', 'woocommerce-redsys' ),
							'id'   => 'wc_settings_tab_redsys_sort_invoices_first_invoice_number',
						),
						array(
							'name' => esc_html__( 'Invoice Number Length', 'woocommerce-redsys' ),
							'type' => 'text',
							'desc' => esc_html__( 'The Invoice number length, this is not required. Example 10, the result will be 0000000345', 'woocommerce-redsys' ),
							'id'   => 'wc_settings_tab_redsys_sort_invoices_length_invoice_number',
						),
						array(
							'name' => __( 'Prefix Invoice Number', 'woocommerce-redsys' ),
							'type' => 'text',
							'desc' => sprintf( __( 'Add here a prefix invoice number, this is not required. Example WC-, the result will be WC-0000000345. Pattern are allowed ex. {Y} this will add the current year. You will find all patterns %1$sshere%2$s.', 'woocommerce-redsys' ), '<a href="https://docs.woocommerce.com/document/redsys-servired-sermepa-gateway/" target="_blank">', '</a>' ),
							'id'   => 'wc_settings_tab_redsys_sort_invoices_prefix_invoice_number',
						),
						array(
							'name' => esc_html__( 'Postfix Invoice Number', 'woocommerce-redsys' ),
							'type' => 'text',
							'desc' => sprintf( __( 'Add here a postfix invoice number, this is not required. Example -2015 the result will be WC-0000000345-2015. Pattern are allowed ex. {Y} this will add the current year. You will find all patterns %1$sshere%2$s.', 'woocommerce-redsys' ), '<a href="https://docs.woocommerce.com/document/redsys-servired-sermepa-gateway/" target="_blank">', '</a>' ),
							'id'   => 'wc_settings_tab_redsys_sort_invoices_postfix_invoice_number',
						),
						array(
							'title'   => esc_html__( 'Reset Invoice Number', 'woocommerce-redsys' ),
							'type'    => 'checkbox',
							'label'   => esc_html__( 'Reset Invoice Number.', 'woocommerce-redsys' ),
							'default' => 'no',
							'desc'    => esc_html__( 'If you enable Reset Invoice Number, every January 1st the invoice number will be reset and will start again with number 1. Is very important that if you enable this option, you use a prefix or postfix year pattern {Y}.', 'woocommerce-redsys' ),
							'id'      => 'wc_settings_tab_redsys_sort_invoices_reset_invoice_number',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'wc_settings_tab_redsys_sort_invoices_section_end',
						),
					);

					break;
				case 'qr_codes':
					$settings = array(
						array(
							'name' => esc_html__( 'QR Codes (by José Conti)', 'woocommerce-redsys' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'redsys_qr_title',
						),
						array(
							'title'   => esc_html__( 'Enable QR Codes', 'woocommerce-redsys' ),
							'type'    => 'checkbox',
							'label'   => esc_html__( 'Enable QR Codes.', 'woocommerce-redsys' ),
							'default' => 'no',
							'desc'    => sprintf( esc_html__( 'Enable QR Codes, WooCommerce Redsys Gateway licence is needed', 'woocommerce-redsys' ) ),
							'id'      => 'redsys_qr_active',
						),
						array(
							'title'       => esc_html__( 'User redsys.joseconti.com', 'woocommerce-redsys' ),
							'type'        => 'text',
							'description' => esc_html__( 'User redsys.joseconti.com', 'woocommerce-redsys' ),
							'default'     => '',
							'id'          => 'redsys_qr_user_redsys_jc',
						),
						array(
							'title'       => esc_html__( 'QR Code Pattern', 'woocommerce-redsys' ),
							'type'        => 'redsysradiopatern',
							'description' => esc_html__( 'Select QR Code pattern.', 'woocommerce-redsys' ),
							'default'     => 'default_type',
							'id'          => 'redsys_qr_type',
							'options'     => array(
								'default_type'             => 'default.svg',
								'circle_type'              => 'circle.svg',
								'dot_type'                 => 'dot.svg',
								'star_type'                => 'star.svg',
								'diamond_type'             => 'diamond.svg',
								'sparkle_type'             => 'sparkle.svg',
								'danger_type'              => 'danger.svg',
								'cross_type'               => 'cross.svg',
								'plus_type'                => 'plus.svg',
								'x_type'                   => 'x.svg',
								'heart_type'               => 'heart.svg',
								'shake_type'               => 'shake.svg',
								'blob_type'                => 'blob.svg',
								'special-circle-orizz_type' => 'special-circle-orizz.svg',
								'special-circle-vert_type' => 'special-circle-vert.svg',
								'special-circle_type'      => 'special-circle.svg',
								'special-diamond_type'     => 'special-diamond.svg',
								'ribbon_type'              => 'ribbon.svg',
								'oriental_type'            => 'oriental.svg',
								'ellipse_type'             => 'ellipse.svg',
							),
						),
						array(
							'title'       => esc_html__( 'QR Code Marker border', 'woocommerce-redsys' ),
							'type'        => 'redsysradioborder',
							'description' => esc_html__( 'Select QR Code Marker border.', 'woocommerce-redsys' ),
							'default'     => 'default_border',
							'id'          => 'redsys_qr_border',
							'options'     => array(
								'default_border'      => 'default.svg',
								'flurry_border'       => 'flurry.svg',
								'sdoz_border'         => 'sdoz.svg',
								'drop_in_border'      => 'drop_in.svg',
								'drop_border'         => 'drop.svg',
								'dropeye_border'      => 'dropeye.svg',
								'dropeyeleft_border'  => 'dropeyeleft.svg',
								'dropeyeleaf_border'  => 'dropeyeleaf.svg',
								'dropeyeright_border' => 'dropeyeright.svg',
								'squarecircle_border' => 'squarecircle.svg',
								'circle_border'       => 'circle.svg',
								'rounded_border'      => 'rounded.svg',
								'flower_border'       => 'flower.svg',
								'flower_in_border'    => 'flower_in.svg',
								'leaf_border'         => 'leaf.svg',
								'3-corners_border'    => '3-corners.svg',
								'vortex_border'       => 'vortex.svg',
								'dots_border'         => 'dots.svg',
								'bruised_border'      => 'bruised.svg',
								'canvas_border'       => 'canvas.svg',
							),
						),
						array(
							'title'       => esc_html__( 'QR Code Marker center', 'woocommerce-redsys' ),
							'type'        => 'redsysradiomcenter',
							'description' => esc_html__( 'Select QR Code Marker center.', 'woocommerce-redsys' ),
							'default'     => 'default_mcenter',
							'id'          => 'redsys_qr_mcenter',
							'options'     => array(
								'default_mcenter'        => 'default.svg',
								'flurry_mcenter'         => 'flurry.svg',
								'sdoz_mcenter'           => 'sdoz.svg',
								'drop_in_mcenter'        => 'drop_in.svg',
								'drop_mcenter'           => 'drop.svg',
								'dropeye_mcenter'        => 'dropeye.svg',
								'circle_mcenter'         => 'circle.svg',
								'rounded_mcenter'        => 'rounded.svg',
								'sun_mcenter'            => 'sun.svg',
								'star_mcenter'           => 'star.svg',
								'diamond_mcenter'        => 'diamond.svg',
								'danger_mcenter'         => 'danger.svg',
								'cross_mcenter'          => 'cross.svg',
								'plus_mcenter'           => 'plus.svg',
								'x_mcenter'              => 'x.svg',
								'heart_mcenter'          => 'heart.svg',
								'vortex_mcenter'         => 'vortex.svg',
								'sparkle_dot_mcenter'    => 'sparkle_dot.svg',
								'9-dots_mcenter'         => '9-dots.svg',
								'9-dots-fat_mcenter'     => '9-dots-fat.svg',
								'flower_mcenter'         => 'flower.svg',
								'elastic_mcenter'        => 'elastic.svg',
								'diagonal_mcenter'       => 'diagonal.svg',
								'ropes_mcenter'          => 'ropes.svg',
								'ropes-vertical_mcenter' => 'ropes-vert.svg',
								'bruised_mcenter'        => 'bruised.svg',
							),
						),
						array(
							'title'       => esc_html__( 'QR Code Frame', 'woocommerce-redsys' ),
							'type'        => 'redsysradioframe',
							'description' => esc_html__( 'Select QR Code Frame.', 'woocommerce-redsys' ),
							'default'     => 'none_frame',
							'id'          => 'redsys_qr_frame',
							'options'     => array(
								'none_frame'           => 'none.svg',
								'bottom_frame'         => 'bottom.svg',
								'top_frame'            => 'top.svg',
								'balloon-bottom_frame' => 'balloon-bottom.svg',
								'balloon-top_frame'    => 'balloon-top.svg',
								'ribbon-bottom_frame'  => 'ribbon-bottom.svg',
								'ribbon-top_frame'     => 'ribbon-top.svg',
								'phone_frame'          => 'phone.svg',
								'cine_frame'           => 'cine.svg',
							),
						),
						array(
							'title'       => esc_html__( 'Frame label', 'woocommerce-redsys' ),
							'type'        => 'text',
							'description' => esc_html__( 'Text to show in frame', 'woocommerce-redsys' ),
							'default'     => esc_html__( 'SCAN ME', 'woocommerce-redsys' ),
							'id'          => 'redsys_qr_framelabel',
						),
						array(
							'title'       => esc_html__( 'Label Font', 'woocommerce-redsys' ),
							'type'        => 'select',
							'description' => esc_html__( 'Select Label Font', 'woocommerce-redsys' ),
							'default'     => 'AbrilFatface.svg',
							'options'     => array(
								'AbrilFatface.svg'        => 'AbrilFatface',
								'CormorantGaramond.svg'   => 'CormorantGaramond',
								'FredokaOne.svg'          => 'FredokaOne',
								'Galindo.svg'             => 'Galindo',
								'OleoScript.svg'          => 'OleoScript',
								'PlayfairDisplay.svg'     => 'PlayfairDisplay',
								'Shrikhand.svg'           => 'Shrikhand',
								'ZCOOLKuaiLe-Regular.svg' => 'ZCOOLKuaiLe',
							),
							'id'          => 'redsys_qr_label_font',
						),
						array(
							'title'       => esc_html__( 'Color Background', 'woocommerce-redsys' ),
							'type'        => 'text',
							'description' => esc_html__( 'Background #ffffff', 'woocommerce-redsys' ),
							'default'     => '#ffffff',
							'class'       => 'colorpick',
							'id'          => 'redsys_qr_backcolor',
						),
						array(
							'title'       => esc_html__( 'Color Foreground', 'woocommerce-redsys' ),
							'type'        => 'text',
							'description' => esc_html__( 'Foreground #000000', 'woocommerce-redsys' ),
							'default'     => '#000000',
							'class'       => 'colorpick',
							'id'          => 'redsys_qr_frontcolor',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'princial_section_end',
						),
						array(
							'name' => esc_html__( 'Logo', 'woocommerce-redsys' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'redsys_qr_logo_section',
						),
						array(
							'title'       => esc_html__( 'Logo Background Image', 'woocommerce-redsys' ),
							'type'        => 'text',
							'description' => esc_html__( 'Logo Background Image', 'woocommerce-redsys' ),
							'default'     => '',
							'id'          => 'redsys_qr_optionlogo',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'logo_section_end',
						),
						array(
							'name' => esc_html__( 'Gradient', 'woocommerce-redsys' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'redsys_qr_gradient_section',
						),
						array(
							'title'   => esc_html__( 'Enable Gradient Color', 'woocommerce-redsys' ),
							'type'    => 'checkbox',
							'label'   => esc_html__( 'Enable Gradient Color.', 'woocommerce-redsys' ),
							'default' => 'no',
							'id'      => 'redsys_qr_gradient_active',
						),
						array(
							'title'       => esc_html__( 'Second color', 'woocommerce-redsys' ),
							'type'        => 'text',
							'description' => esc_html__( 'Second color #8900d5', 'woocommerce-redsys' ),
							'default'     => '#8900d5',
							'class'       => 'colorpick',
							'id'          => 'redsys_qr_gradient_color',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'gradient_section_end',
						),
						array(
							'name' => esc_html__( 'Marker Custom Color', 'woocommerce-redsys' ),
							'type' => 'title',
							'desc' => '',
							'id'   => 'redsys_qr_marker_color_section',
						),
						array(
							'title'   => esc_html__( 'Enable Marker Custom Color', 'woocommerce-redsys' ),
							'type'    => 'checkbox',
							'label'   => esc_html__( 'Enable Marker Custom Color.', 'woocommerce-redsys' ),
							'default' => 'no',
							'id'      => 'redsys_qr_marker_color_active',
						),
						array(
							'title'       => esc_html__( 'Marker border color', 'woocommerce-redsys' ),
							'type'        => 'text',
							'description' => esc_html__( 'Marker border color', 'woocommerce-redsys' ),
							'default'     => '#000000',
							'class'       => 'colorpick',
							'id'          => 'redsys_qr_marker_out_color',
						),
						array(
							'title'       => esc_html__( 'Marker center color', 'woocommerce-redsys' ),
							'type'        => 'text',
							'description' => esc_html__( 'Marker center color', 'woocommerce-redsys' ),
							'default'     => '#000000',
							'class'       => 'colorpick',
							'id'          => 'redsys_qr_marker_in_color',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'marker_color_section_end',
						),
					);
					break;
				case 'saved_cards':
					$settings = array(
						array(
							'title' => esc_html__( 'Saved Cards', 'woocommerce-redsys' ),
							'type'  => 'title',
							'desc'  => '',
							'id'    => 'title_saved_card',
						),
						array(
							'title'   => esc_html__( 'Send email to the customer.', 'woocommerce-redsys' ),
							'type'    => 'checkbox',
							'label'   => esc_html__( 'Send email to the customer.', 'woocommerce-redsys' ),
							'default' => 'no',
							'desc'    => esc_html__( 'Send email to the customer when the card is about to expire this month.', 'woocommerce-redsys' ),
							'id'      => 'send_emails_to_customer_expired_card',
						),
						array(
							'title'   => esc_html__( 'Delete Credit Cards', 'woocommerce-redsys' ),
							'type'    => 'checkbox',
							'label'   => __( 'Delete expired cards.', 'woocommerce-redsys' ),
							'default' => 'no',
							'desc'    => esc_html__( 'Delete expired cards. This will keep a cleaner database, and users will see that they have no cards. If you have enabled the previous functionality, they were also notified that their card was about to expire.', 'woocommerce-redsys' ),
							'id'      => 'remove_expired_card',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'saved_cards_end',
						),
					);
					break;
				default:
					$settings = array(
						array(
							'title' => esc_html__( 'General Settings (Coming soon)', 'woocommerce-redsys' ),
							'type'  => 'title',
							'id'    => 'general_options',
						),
						// ...
						array(
							'type' => 'sectionend',
							'id'   => 'general_options',
						),
					);
					break;
			}

			return $settings;
		}
	}

	add_action( 'init', array( 'Redsys_Advanced_Settings', 'init' ) );

endif;

if ( 'yes' === get_option( 'wc_settings_tab_redsys_sort_invoices_is_active' ) ) {
	add_filter( 'manage_edit-shop_order_columns', 'redsys_add_invoice_number' );
	add_action( 'manage_shop_order_posts_custom_column', 'redsys_add_invoice_number_value', 2 );
	add_filter( 'manage_edit-shop_order_sortable_columns', 'redsys_add_invoice_number_sortable_colum' );
	add_filter( 'manage_woocommerce_page_wc-orders_columns', 'redsys_add_invoice_number' );
	add_action( 'manage_woocommerce_page_wc-orders_custom_column', 'redsys_add_invoice_number_value_hpos', 20, 2 );
	add_filter( 'manage_woocommerce_page_wc-orders_sortable_columns', 'redsys_add_invoice_number_sortable_colum' );
	// add_action(   'woocommerce_email_before_order_table', 'redsys_add_invoice_number_to_customer_email' );
	add_action( 'woocommerce_payment_complete', 'redsys_sort_invoice_orders' );
	add_action( 'woocommerce_order_status_processing', 'redsys_sort_invoice_orders_admin' );
	add_action( 'woocommerce_order_status_completed', 'redsys_sort_invoice_orders_admin' );
	if ( ! is_admin() ) {
		// add_filter( 'woocommerce_order_number', 'redsys_show_invoice_number', 10, 2 );
	}
}
/**
 * Add invoice number to the order list.
 *
 * @param array $columns Add Invocien Number to the order list.
 */
function redsys_add_invoice_number( $columns ) {

	$new_column = ( is_array( $columns ) ) ? $columns : array();
	unset( $new_column['wc_actions'] );

	// edit this for you column(s)
	// all of your columns will be added before the actions colums.
	$new_column['invoice_number'] = __( 'Invoice Number', 'woocommerce-redsys' );

	// stop editing.
	$new_column['wc_actions'] = $columns['wc_actions'];
	return $new_column;
}
/**
 * Add invoice number to the order list.
 *
 * @param array $column column.
 */
function redsys_add_invoice_number_value( $column ) {
	global $post;

	$invoice_number = WCRed()->get_order_meta( $post->ID, '_invoice_order_redsys', true );

	if ( 'invoice_number' === $column ) {
		echo ( ! empty( $invoice_number ) ? esc_html( $invoice_number ) : esc_html__( 'No invoice n&#176;', 'woocommerce-redsys' ) );
	}
}
/**
 * Add invoice number to the order list.
 *
 * @param array $column column.
 * @param int   $order_id order id.
 */
function redsys_add_invoice_number_value_hpos( $column, $order_id ) {

	$order = wc_get_order( $order_id );

	$invoice_number = WCRed()->get_order_meta( $order->get_id(), '_invoice_order_redsys', true );

	if ( 'invoice_number' === $column ) {
		echo ( ! empty( $invoice_number ) ? esc_html( $invoice_number ) : esc_html__( 'No invoice n&#176;', 'woocommerce-redsys' ) );
	}
}
/**
 * Sort by invoice number.
 *
 * @param array $columns columns.
 */
function redsys_add_invoice_number_sortable_colum( $columns ) {

	$custom = array(
		'invoice_number' => '_invoice_order_redsys',
	);
	return wp_parse_args( $custom, $columns );
}

/**
 * Add invoice number to customer email.
 *
 * @param int $order_id order.
 */
function redsys_sort_invoice_orders( $order_id ) {

	$reset_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_reset_invoice_number' );
	if ( 'yes' === $reset_invoice_number ) {
		redsys_check_current_year();
	}

	$last_invoice_number           = get_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number' );
	$before_prefix_invoice_number  = get_option( 'wc_settings_tab_redsys_sort_invoices_prefix_invoice_number' );
	$before_postfix_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_postfix_invoice_number' );
	$length_invoice_number         = get_option( 'wc_settings_tab_redsys_sort_invoices_length_invoice_number' );
	$prefix_invoice_number         = redsys_use_patterns( $before_prefix_invoice_number );
	$postfix_invoice_number        = redsys_use_patterns( $before_postfix_invoice_number );
	$get_invoice_if_exist          = WCRed()->get_order_meta( $order_id, '_invoice_order_redsys', true );

	if ( empty( $get_invoice_if_exist ) ) {
		if ( ! empty( $last_invoice_number ) ) {
			settype( $last_invoice_number, 'integer' );
		}
		if ( empty( $last_invoice_number ) ) {
			// Check if there is a option with the first invoice number.
			$first_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_first_invoice_number' );
			if ( empty( $first_invoice_number ) ) {
				$invoice_number = 1;
				update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
			} else {
				settype( $first_invoice_number, 'integer' );
				$invoice_number = $first_invoice_number;
				update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
			}
		} else {
			$invoice_number = ++$last_invoice_number;
			update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
		}
		if ( ! empty( $length_invoice_number ) && ( strlen( $invoice_number ) < $length_invoice_number ) ) {
			$invoice_number_long = str_pad( $invoice_number, $length_invoice_number, '0', STR_PAD_LEFT );
		} else {
			$invoice_number_long = $invoice_number;
		}
		$final_invoice_number = $prefix_invoice_number . $invoice_number_long . $postfix_invoice_number;
		WCRed()->update_order_meta( $order_id, '_invoice_order_redsys', $final_invoice_number );
	}
}

/**
 * Add invoice number to customer email.
 *
 * @param int $order_id order id.
 */
function redsys_sort_invoice_orders_admin( $order_id ) {

	$reset_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_reset_invoice_number' );
	if ( 'yes' === $reset_invoice_number ) {
		redsys_check_current_year();
	}

	$last_invoice_number           = get_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number' );
	$before_prefix_invoice_number  = get_option( 'wc_settings_tab_redsys_sort_invoices_prefix_invoice_number' );
	$before_postfix_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_postfix_invoice_number' );
	$length_invoice_number         = get_option( 'wc_settings_tab_redsys_sort_invoices_length_invoice_number' );
	$prefix_invoice_number         = redsys_use_patterns( $before_prefix_invoice_number );
	$postfix_invoice_number        = redsys_use_patterns( $before_postfix_invoice_number );
	$get_invoice_if_exist          = WCRed()->get_order_meta( $order_id, '_invoice_order_redsys', true );

	if ( empty( $get_invoice_if_exist ) ) {
		if ( ! empty( $last_invoice_number ) ) {
			settype( $last_invoice_number, 'integer' );
		}
		if ( empty( $last_invoice_number ) ) {
			// Check if there is a option with the first invoice number.
			$first_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_first_invoice_number' );
			if ( empty( $first_invoice_number ) ) {
				$invoice_number = 1;
				update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
			} else {
				settype( $first_invoice_number, 'integer' );
				$invoice_number = $first_invoice_number;
				update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
			}
		} else {
			$invoice_number = ++$last_invoice_number;
			update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', $invoice_number );
		}
		if ( ! empty( $length_invoice_number ) && ( strlen( $invoice_number ) < $length_invoice_number ) ) {
			$invoice_number_long = str_pad( $invoice_number, $length_invoice_number, '0', STR_PAD_LEFT );
		} else {
			$invoice_number_long = $invoice_number;
		}
		$final_invoice_number = $prefix_invoice_number . $invoice_number_long . $postfix_invoice_number;
		WCRed()->update_order_meta( $order_id, '_invoice_order_redsys', $final_invoice_number );
	}
}
/**
 * Customer_email_invoice_number.
 *
 * @param int $order Order ID.
 */
function redsys_add_invoice_number_to_customer_email( $order ) {

	$invoice_number = redsys_check_add_invoice_number( $order );
	if ( empty( $invoice_number ) ) {
		printf( esc_html__( 'Order Number: %s', 'woocommerce-redsys' ), esc_html( $order ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
	} else {
		echo '<h2>';
		printf( esc_html__( 'Invoice Number: %s', 'woocommerce-redsys' ), esc_html( $invoice_number ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		echo '</h2>';
	}
}
/**
 * Customer_email_invoice_number.
 *
 * @param int $order Order ID.
 */
function redsys_check_add_invoice_number( $order ) {
	global $woocommerce, $post;

	$reset_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_reset_invoice_number' );
	if ( 'yes' === $reset_invoice_number ) {
		redsys_check_current_year();
	}
	$get_invoice_if_exist          = WCRed()->get_order_meta( $order, '_invoice_order_redsys', true );
	$last_invoice_number           = get_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number' );
	$last_invoice_number           = get_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number' );
	$before_prefix_invoice_number  = get_option( 'wc_settings_tab_redsys_sort_invoices_prefix_invoice_number' );
	$before_postfix_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_postfix_invoice_number' );
	$length_invoice_number         = get_option( 'wc_settings_tab_redsys_sort_invoices_length_invoice_number' );
	$prefix_invoice_number         = redsys_use_patterns( $before_prefix_invoice_number );
	$postfix_invoice_number        = redsys_use_patterns( $before_postfix_invoice_number );

	if ( ! empty( $last_invoice_number ) ) {
		settype( $last_invoice_number, 'integer' );
	}

	if ( empty( $last_invoice_number ) ) {
		// Check if there is a option with the first invoice number.
		$first_invoice_number = get_option( 'wc_settings_tab_redsys_sort_invoices_first_invoice_number' );
		if ( empty( $first_invoice_number ) ) {
			$invoice_number = 1;
		} else {
			settype( $first_invoice_number, 'integer' );
			$invoice_number = $first_invoice_number;
		}
	} else {
		$invoice_number = $last_invoice_number;
	}
	if ( ! empty( $length_invoice_number ) && ( strlen( $invoice_number ) < $length_invoice_number ) ) {
		$invoice_number_long = str_pad( $invoice_number, $length_invoice_number, '0', STR_PAD_LEFT );
	} else {
		$invoice_number_long = $invoice_number;
	}
	$final_invoice_number = $prefix_invoice_number . $invoice_number_long . $postfix_invoice_number;
	return $final_invoice_number;
}

/**
 * Customer_email_invoice_number.
 *
 * @param int $oldnumber Numer.
 * @param int $order Order ID.
 */
function redsys_show_invoice_number( $oldnumber, $order ) {
	$preorderprefix = get_option( 'wc_settings_tab_redsys_sort_invoices_prefix_order_number' );
	$preordersufix  = get_option( 'wc_settings_tab_redsys_sort_invoices_postfix_order_number' );
	$orderprefix    = redsys_use_patterns( $preorderprefix );
	$ordersufix     = redsys_use_patterns( $preordersufix );

	if ( empty( $ordersufix ) && empty( $orderprefix ) ) {
			$ordersufix = __( '-ORDER', 'woocommerce-redsys' );
	}

	$order = WCRed()->get_order_meta( $oldnumber, '_invoice_order_redsys', true );
	if ( empty( $order ) ) {
		$order = $orderprefix . $oldnumber . $ordersufix;
	}
	if ( is_checkout() ) {
		$order = $oldnumber;
	}
	return $order;
}

/**
 * Invoice Pattern.
 *
 * @param string $string String.
 */
function redsys_use_patterns( $string ) {
	$numericzero                   = preg_replace( '/(\{d\})/', date_i18n( 'd' ), $string );
	$numeric                       = preg_replace( '/(\{j\})/', date_i18n( 'j' ), $numericzero );
	$english_suffix                = preg_replace( '/(\{S\})/', date_i18n( 'S' ), $numeric );
	$full_name                     = preg_replace( '/(\{l\})/', date_i18n( 'l' ), $english_suffix );
	$three_letter                  = preg_replace( '/(\{D\})/', date_i18n( 'D' ), $full_name );
	$month_numericzero             = preg_replace( '/(\{m\})/', date_i18n( 'm' ), $three_letter );
	$month_numeric                 = preg_replace( '/(\{n\})/', date_i18n( 'n' ), $month_numericzero );
	$textual_full                  = preg_replace( '/(\{F\})/', date_i18n( 'F' ), $month_numeric );
	$textual_three                 = preg_replace( '/(\{M\})/', date_i18n( 'M' ), $textual_full );
	$year_numeric_four             = preg_replace( '/(\{Y\})/', date_i18n( 'Y' ), $textual_three );
	$year_numeric_two              = preg_replace( '/(\{y\})/', date_i18n( 'y' ), $year_numeric_four );
	$time_lowercase                = preg_replace( '/(\{a\})/', date_i18n( 'a' ), $year_numeric_two );
	$time_uppercase                = preg_replace( '/(\{A\})/', date_i18n( 'A' ), $time_lowercase );
	$hour_twelve_without_zero      = preg_replace( '/(\{g\})/', date_i18n( 'g' ), $time_uppercase );
	$hour_twelve_zero              = preg_replace( '/(\{h\})/', date_i18n( 'h' ), $hour_twelve_without_zero );
	$hour_twenty_four_without_zero = preg_replace( '/(\{G\})/', date_i18n( 'G' ), $hour_twelve_zero );
	$hour_twenty_four_zero         = preg_replace( '/(\{H\})/', date_i18n( 'H' ), $hour_twenty_four_without_zero );
	$minutes                       = preg_replace( '/(\{i\})/', date_i18n( 'i' ), $hour_twenty_four_zero );
	$final                         = preg_replace( '/(\{s\})/', date_i18n( 's' ), $minutes );

	return $final;
}
/**
 * Copyright: (C) 2013 - 2023 José Conti
 */
function redsys_check_current_year() {
		$current_year = date_i18n( 'Y' );
		$saved_year   = get_option( 'redsys_saved_year' );
		settype( $saved_year, 'integer' );

	if ( empty( $saved_year ) ) {
		add_option( 'redsys_saved_year', $current_year );
	} else {
		if ( $current_year > $saved_year ) {
			update_option( 'redsys_saved_year', $current_year );
			update_option( 'wc_settings_tab_redsys_sort_invoices_first_invoice_number', '0' );
			update_option( 'wc_settings_tab_redsys_sort_invoices_last_invoice_number', '0' );
		}
	}
}
