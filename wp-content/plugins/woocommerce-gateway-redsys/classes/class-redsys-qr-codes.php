<?php
/**
 * Redsys QR Codes
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2013 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Redsys QR Codes
 */
class Redsys_QR_Codes {

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_settings_tab_redsys_qr', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_settings_tab_redsys_qr', __CLASS__ . '::update_settings' );
		add_action( 'woocommerce_admin_field_redsysradiopatern', __CLASS__ . '::redsys_radio_pattern' );
		add_action( 'woocommerce_admin_field_redsysradioborder', __CLASS__ . '::redsys_radio_border' );
		add_action( 'woocommerce_admin_field_redsysradiomcenter', __CLASS__ . '::redsys_radio_mcenter' );
		add_action( 'woocommerce_admin_field_redsysradioframe', __CLASS__ . '::redsys_radio_frame' );
		add_filter( 'upload_mimes', __CLASS__ . '::allow_svg' );
	}
	/**
	 *  Debug log
	 *
	 * @param string $log log.
	 */
	public function debug( $log ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$debug = new WC_Logger();
			$debug->add( 'redsys-qr', $log );
		}
	}

	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['settings_tab_redsys_qr'] = __( 'Redsys QR Codes', 'woocommerce-redsys' );
		return $settings_tabs;
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
	/**
	 * Add settings tab.
	 */
	public static function settings_tab() {
		WCRed()->return_help_notice(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		woocommerce_admin_fields( self::get_settings() );
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
	/**
	 * Update settings.
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}
	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public static function get_settings() {

		$settings = array(
			'title'                    => array(
				'name' => esc_html__( 'QR Codes (by José Conti)', 'woocommerce-redsys' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'redsys_qr_title',
			),
			'qr_is_active'             => array(
				'title'   => esc_html__( 'Enable QR Codes', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Enable QR Codes.', 'woocommerce-redsys' ),
				'default' => 'no',
				'desc'    => sprintf( esc_html__( 'Enable QR Codes, WooCommerce Redsys Gateway licence is needed', 'woocommerce-redsys' ) ),
				'id'      => 'redsys_qr_active',
			),
			'user_redsys_jc'           => array(
				'title'       => __( 'User redsys.joseconti.com', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'User redsys.joseconti.com', 'woocommerce-redsys' ),
				'default'     => '',
				'id'          => 'redsys_qr_user_redsys_jc',
			),
			'type'                     => array(
				'title'       => __( 'QR Code Pattern', 'woocommerce-redsys' ),
				'type'        => 'redsysradiopatern',
				'description' => __( 'Select QR Code pattern.', 'woocommerce-redsys' ),
				'default'     => 'default_type',
				'id'          => 'redsys_qr_type',
				'options'     => array(
					'default_type'              => 'default.svg',
					'circle_type'               => 'circle.svg',
					'dot_type'                  => 'dot.svg',
					'star_type'                 => 'star.svg',
					'diamond_type'              => 'diamond.svg',
					'sparkle_type'              => 'sparkle.svg',
					'danger_type'               => 'danger.svg',
					'cross_type'                => 'cross.svg',
					'plus_type'                 => 'plus.svg',
					'x_type'                    => 'x.svg',
					'heart_type'                => 'heart.svg',
					'shake_type'                => 'shake.svg',
					'blob_type'                 => 'blob.svg',
					'special-circle-orizz_type' => 'special-circle-orizz.svg',
					'special-circle-vert_type'  => 'special-circle-vert.svg',
					'special-circle_type'       => 'special-circle.svg',
					'special-diamond_type'      => 'special-diamond.svg',
					'ribbon_type'               => 'ribbon.svg',
					'oriental_type'             => 'oriental.svg',
					'ellipse_type'              => 'ellipse.svg',
				),
			),
			'border'                   => array(
				'title'       => __( 'QR Code Marker border', 'woocommerce-redsys' ),
				'type'        => 'redsysradioborder',
				'description' => __( 'Select QR Code Marker border.', 'woocommerce-redsys' ),
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
			'mcenter'                  => array(
				'title'       => __( 'QR Code Marker center', 'woocommerce-redsys' ),
				'type'        => 'redsysradiomcenter',
				'description' => __( 'Select QR Code Marker center.', 'woocommerce-redsys' ),
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
					'ropes-vertical_mcenter' => 'ropes-vertical.svg',
					'bruised_mcenter'        => 'bruised.svg',
				),
			),
			'frame'                    => array(
				'title'       => __( 'QR Code Frame', 'woocommerce-redsys' ),
				'type'        => 'redsysradioframe',
				'description' => __( 'Select QR Code Frame.', 'woocommerce-redsys' ),
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
			'framelabel'               => array(
				'title'       => __( 'Frame label', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Text to show in frame', 'woocommerce-redsys' ),
				'default'     => __( 'SCAN ME', 'woocommerce-redsys' ),
				'id'          => 'redsys_qr_framelabel',
			),
			'label_font'               => array(
				'title'       => __( 'Label Font', 'woocommerce-redsys' ),
				'type'        => 'select',
				'description' => __( 'Select Label Font', 'woocommerce-redsys' ),
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
			'backcolor'                => array(
				'title'       => __( 'Color Background', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Background #ffffff', 'woocommerce-redsys' ),
				'default'     => '#ffffff',
				'class'       => 'colorpick',
				'id'          => 'redsys_qr_backcolor',
			),
			'frontcolor'               => array(
				'title'       => __( 'Color Foreground', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Foreground #000000', 'woocommerce-redsys' ),
				'default'     => '#000000',
				'class'       => 'colorpick',
				'id'          => 'redsys_qr_frontcolor',
			),
			'qr_section_end'           => array(
				'type' => 'sectionend',
				'id'   => 'princial_section_end',
			),
			'logo_section'             => array(
				'name' => esc_html__( 'Logo', 'woocommerce-redsys' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'redsys_qr_logo_section',
			),
			'optionlogo'               => array(
				'title'       => __( 'Logo Background Image', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Logo Background Image', 'woocommerce-redsys' ),
				'default'     => '',
				'id'          => 'redsys_qr_optionlogo',
			),
			'logo_section_end'         => array(
				'type' => 'sectionend',
				'id'   => 'logo_section_end',
			),
			'gradient_section'         => array(
				'name' => esc_html__( 'Gradient', 'woocommerce-redsys' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'redsys_qr_gradient_section',
			),
			'gradient_is_active'       => array(
				'title'   => esc_html__( 'Enable Gradient Color', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Enable Gradient Color.', 'woocommerce-redsys' ),
				'default' => 'no',
				'id'      => 'redsys_qr_gradient_active',
			),
			'gradient_color'           => array(
				'title'       => __( 'Second color', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Second color #8900d5', 'woocommerce-redsys' ),
				'default'     => '#8900d5',
				'class'       => 'colorpick',
				'id'          => 'redsys_qr_gradient_color',
			),
			'gradient_section_end'     => array(
				'type' => 'sectionend',
				'id'   => 'gradient_section_end',
			),
			'marker_color_section'     => array(
				'name' => esc_html__( 'Marker Custom Color', 'woocommerce-redsys' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'redsys_qr_marker_color_section',
			),
			'marker_color_is_active'   => array(
				'title'   => esc_html__( 'Enable Marker Custom Color', 'woocommerce-redsys' ),
				'type'    => 'checkbox',
				'label'   => esc_html__( 'Enable Marker Custom Color.', 'woocommerce-redsys' ),
				'default' => 'no',
				'id'      => 'redsys_qr_marker_color_active',
			),
			'marker_out_color'         => array(
				'title'       => __( 'Marker border color', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Marker border color', 'woocommerce-redsys' ),
				'default'     => '#000000',
				'class'       => 'colorpick',
				'id'          => 'redsys_qr_marker_out_color',
			),
			'marker_in_color'          => array(
				'title'       => __( 'Marker center color', 'woocommerce-redsys' ),
				'type'        => 'text',
				'description' => __( 'Marker center color', 'woocommerce-redsys' ),
				'default'     => '#000000',
				'class'       => 'colorpick',
				'id'          => 'redsys_qr_marker_in_color',
			),
			'marker_color_section_end' => array(
				'type' => 'sectionend',
				'id'   => 'marker_color_section_end',
			),
		);
		return apply_filters( 'redsys_qr_settings', $settings );
	}
	/**
	 * Return qr active
	 */
	public function redsys_qr_is_active() {
		$redsys_qr_is_active = get_option( 'redsys_qr_active', 'no' );
		if ( 'yes' === $redsys_qr_is_active ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Return User redsys.joseconti.com
	 */
	public function user_redsys_jc() {
		$redsys_qr_jc = get_option( 'redsys_qr_user_redsys_jc' );
		return $redsys_qr_jc;
	}
	/**
	 * Return QR Type
	 */
	public function type() {
		$redsys_qr_type = get_option( 'redsys_qr_type' );
		return str_replace( '_type', '', $redsys_qr_type );
	}
	/**
	 * Return QR Borer
	 */
	public function border() {
		$redsys_qr_border = get_option( 'redsys_qr_border' );
		return str_replace( '_border', '', $redsys_qr_border );
	}
	/**
	 * Return mcenter
	 */
	public function mcenter() {
		$redsys_qr_mcenter = get_option( 'redsys_qr_mcenter' );
		return str_replace( '_mcenter', '', $redsys_qr_mcenter );
	}
	/**
	 * Return QR Frame
	 */
	public function frame() {
		$redsys_qr_frame = get_option( 'redsys_qr_frame' );
		return str_replace( '_frame', '', $redsys_qr_frame );
	}
	/**
	 * Return QR Frame Label
	 */
	public function framelabel() {
		$redsys_qr_framelabel = get_option( 'redsys_qr_framelabel' );
		return $redsys_qr_framelabel;
	}
	/**
	 * Return QR Frame Label Font
	 */
	public function label_font() {
		$redsys_qr_label_font = get_option( 'redsys_qr_label_font' );
		return $redsys_qr_label_font;
	}
	/**
	 * Return QR backcolor
	 */
	public function backcolor() {
		$redsys_qr_backcolor = get_option( 'redsys_qr_backcolor' );
		return $redsys_qr_backcolor;
	}
	/**
	 * Return QR frontcolor
	 */
	public function frontcolor() {
		$redsys_qr_frontcolor = get_option( 'redsys_qr_frontcolor' );
		return $redsys_qr_frontcolor;
	}
	/**
	 * Return QR logo
	 */
	public function optionlogo() {
		$redsys_qr_optionlogo = get_option( 'redsys_qr_optionlogo' );
		return $redsys_qr_optionlogo;
	}
	/**
	 * Check is gradient active.
	 */
	public function gradient_active() {
		$redsys_qr_gradient_active = get_option( 'redsys_qr_gradient_active' );
		if ( 'yes' === $redsys_qr_gradient_active ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Return QR gradient color
	 */
	public function gradient_color() {
		$redsys_qr_gradient_color = get_option( 'redsys_qr_gradient_color' );
		return $redsys_qr_gradient_color;
	}
	/**
	 * Check is marker color active.
	 */
	public function marker_color_active() {
		$redsys_qr_marker_color_active = get_option( 'redsys_qr_marker_color_active' );
		if ( 'yes' === $redsys_qr_marker_color_active ) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Return QR marker out color
	 */
	public function marker_out_color() {
		$redsys_qr_marker_out_color = get_option( 'redsys_qr_marker_out_color' );
		return $redsys_qr_marker_out_color;
	}
	/**
	 * Return QR marker in color
	 */
	public function marker_in_color() {
		$redsys_qr_marker_in_color = get_option( 'redsys_qr_marker_in_color' );
		return $redsys_qr_marker_in_color;
	}
	/**
	 * Return QR label redsys
	 */
	public function label_redsys() {
		$redsys_qr_label_redsys = get_option( 'redsys_qr_label_redsys' );
		return $redsys_qr_label_redsys;
	}
	/**
	 * Check if image exist
	 *
	 * @param string $path Path to image.
	 */
	public function check_image_exist( $path ) {

		$this->debug( 'check_image_exist()' );
		$this->debug( '$path : ' . $path );
		if ( file_exists( $path ) ) {
			$this->debug( 'return TRUE' );
			return true;
		}
		$this->debug( 'return FALSE' );
		return false;
	}
	/**
	 * Create image name
	 *
	 * @param string $product_id Product ID.
	 */
	public function create_name_image( $product_id ) {

		$this->debug( 'create_name_image()' );
		$this->debug( '$product_id : ' . $product_id );
		$upload_dir = wp_upload_dir();
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'];
			$url  = $upload_dir['url'];
		} else {
			$file = $upload_dir['basedir'];
			$url  = $upload_dir['url'];
		}
		$this->debug( '$file : ' . $file );
		$i = 0;
		for ( $i = 0; $i < 100; $i++ ) {
			$image_name = 'image_' . $product_id . '_' . $i . '.svg';
			$path       = $file . '/' . $image_name;
			$url_img    = $url . '/' . $image_name;
			$this->debug( '$image_name : ' . $image_name );
			$this->debug( '$path : ' . $path );
			if ( ! $this->check_image_exist( $path ) ) {
				return array(
					'image_name' => $image_name,
					'path'       => $path,
					'url_img'    => $url_img,
				);
			}
		}
	}
	/**
	 * Return QR label
	 *
	 * @param string $link Link to QR.
	 * @param string $type2 Type of QR.
	 * @param string $product_id Product ID.
	 */
	public function get_qr( $link, $type2, $product_id ) {
		global $wp_filesystem;

		$this->debug( 'get_qr()' );

		require_once ABSPATH . '/wp-admin/includes/file.php';

		WP_Filesystem();

		if ( ! WCRed()->check_product_key() ) {
			return 'Error-5';
		}
		$api_link            = 'https://api.joseconti.com/v1/qr/';
		$user_redsys_jc      = $this->user_redsys_jc();
		$type                = $this->type();
		$border              = $this->border();
		$mcenter             = $this->mcenter();
		$frame               = $this->frame();
		$framelabel          = $this->framelabel();
		$label_font          = $this->label_font();
		$backcolor           = $this->backcolor();
		$frontcolor          = $this->frontcolor();
		$optionlogo          = $this->optionlogo();
		$gradient_active     = $this->gradient_active();
		$gradient_color      = $this->gradient_color();
		$marker_color_active = $this->marker_color_active();
		$marker_out_color    = $this->marker_out_color();
		$marker_in_color     = $this->marker_in_color();

		$this->debug( '$api_link : ' . $api_link );
		$this->debug( '$user_redsys_jc : ' . $user_redsys_jc );
		$this->debug( '$type : ' . $type );
		$this->debug( '$border : ' . $border );
		$this->debug( '$mcenter : ' . $mcenter );
		$this->debug( '$frame : ' . $frame );
		$this->debug( '$framelabel : ' . $framelabel );
		$this->debug( '$label_font : ' . $label_font );
		$this->debug( '$backcolor : ' . $backcolor );
		$this->debug( '$frontcolor : ' . $frontcolor );
		$this->debug( '$optionlogo : ' . $optionlogo );
		$this->debug( '$gradient_active : ' . $gradient_active );
		$this->debug( '$gradient_color : ' . $gradient_color );
		$this->debug( '$marker_color_active : ' . $marker_color_active );
		$this->debug( '$marker_out_color : ' . $marker_out_color );
		$this->debug( '$marker_in_color : ' . $marker_in_color );
		$this->debug( '$type2 : ' . $type2 );

		if ( 1 === (int) $marker_color_active ) {
			$marker_color_active = 'on';
		} else {
			$marker_color_active = '';
		}

		if ( 1 === (int) $gradient_active ) {
			$gradient_active = 'on';
		} else {
			$gradient_active = '';
		}

		$data = array(
			'user'             => $user_redsys_jc,
			'link'             => $link,
			'section'          => $type2,
			'pattern'          => $type,
			'marker_out'       => $border,
			'marker_in'        => $mcenter,
			'outer_frame'      => $frame,
			'framelabel'       => $framelabel,
			'label_font'       => $label_font,
			'backcolor'        => $backcolor,
			'frontcolor'       => $frontcolor,
			'optionlogo'       => $optionlogo,
			'gradient'         => $gradient_active,
			'gradient_color'   => $gradient_color,
			'markers_color'    => $marker_color_active,
			'marker_out_color' => $marker_out_color,
			'marker_in_color'  => $marker_in_color,
		);
		$this->debug( print_r( $data, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$response = wp_remote_post(
			$api_link,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'httpversion' => '1.0',
				'user-agent'  => 'WooCommerce',
				'body'        => $data,
			)
		);

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		$result        = json_decode( $response_body, true );
		$decoded       = base64_decode( $result['content'] ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		if ( 'Error-1' === $decoded || 'Error-2' === $decoded || 'Error-3' === $decoded || ! isset( $result['content'] ) || empty( $decoded ) || 200 !== $response_code ) {
			if ( 200 !== $response_code ) {
				$decoded = 'Error-4';
			}
			return $decoded;
		}

		$upload_dir = wp_upload_dir();
		$image      = $this->create_name_image( $product_id );

		$this->debug( '$image_path : ' . print_r( $image, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$this->debug( 'get_qr()' );

		$file_name  = $image['image_name'];
		$file_path  = $image['path'];
		$file_image = $image['url_img'];

		$wp_filesystem->put_contents( $file_path, $decoded );
		$wp_filetype = wp_check_filetype( $file_name, null );
		$this->debug( '$file_name: ' . $file_name );
		$this->debug( '$filetype: ' . print_r( $wp_filetype, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$this->debug( '$upload_dir: ' . print_r( $upload_dir, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$this->debug( '$file_name: ' . $file_name );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $file_name ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		$this->debug( '$attachment: ' . print_r( $attachment, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$attach_id = wp_insert_attachment( $attachment, $file_path, $product_id );
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		return $file_image;
	}
	/**
	 * Allow SVG in uploads
	 *
	 * @param array $mimes Mimes.
	 */
	public static function allow_svg( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
}
Redsys_QR_Codes::init();
