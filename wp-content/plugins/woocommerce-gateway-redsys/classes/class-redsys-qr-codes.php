<?php

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
	}

	/**
	 * Add a new settings tab to the WooCommerce settings tabs array.
	 *
	 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	 * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function add_settings_tab( $settings_tabs ) {
		$settings_tabs['settings_tab_redsys_qr'] = __( 'Redsys QR Codes', 'woocommerce-redsys' );
		return $settings_tabs;
	}

	public static function redsys_radio_pattern( $value ) {
		$option_value = get_option( 'redsys_qr_type' );
		if ( ! $option_value ) {
			$option_value = 'default';
		}
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<fieldset>
					<?php echo $description; // WPCS: XSS ok. ?>
					<ul>
					<?php
					foreach ( $value['options'] as $key => $val ) {
						$image = REDSYS_PLUGIN_URL_P . 'assets/images/svg/pattern/' . $val;
						?>
							<input type="radio" class="radio_redsys" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $option_value ); ?>/>
							<label class="label_redsys" for="<?php echo esc_html( $key ); ?>"> <img src="<?php echo $image; ?>"></label>
						<?php
					}
					?>
					</ul>
				</fieldset>
			</td>
		</tr>
		<?php
	}

	public static function redsys_radio_border( $value ) {
		$option_value = get_option( 'redsys_qr_border' );
		if ( ! $option_value ) {
			$option_value = 'default';
		}
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<fieldset>
					<?php echo $description; // WPCS: XSS ok. ?>
					<ul>
					<?php
					foreach ( $value['options'] as $key => $val ) {
						$image = REDSYS_PLUGIN_URL_P . 'assets/images/svg/border/' . $val;
						?>
							<input type="radio" class="radio_redsys" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $option_value ); ?>/>
							<label class="label_redsys" for="<?php echo esc_html( $key ); ?>"> <img src="<?php echo $image; ?>"></label>
						<?php
					}
					?>
					</ul>
				</fieldset>
			</td>
		</tr>
		<?php
	}

	public static function redsys_radio_mcenter( $value ) {
		$option_value = get_option( 'redsys_qr_mcenter' );
		if ( ! $option_value ) {
			$option_value = 'default';
		}
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<fieldset>
					<?php echo $description; // WPCS: XSS ok. ?>
					<ul>
					<?php
					foreach ( $value['options'] as $key => $val ) {
						$image = REDSYS_PLUGIN_URL_P . 'assets/images/svg/mcenter/' . $val;
						?>
							<input type="radio" class="radio_redsys" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $option_value ); ?>/>
							<label class="label_redsys" for="<?php echo esc_html( $key ); ?>"> <img src="<?php echo $image; ?>"></label>
						<?php
					}
					?>
					</ul>
				</fieldset>
			</td>
		</tr>
		<?php
	}

	public static function redsys_radio_frame( $value ) {
		$option_value = get_option( 'redsys_qr_frame' );
		if ( ! $option_value ) {
			$option_value = 'none';
		}
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<fieldset>
					<?php echo $description; // WPCS: XSS ok. ?>
					<ul>
					<?php
					foreach ( $value['options'] as $key => $val ) {
						$image = REDSYS_PLUGIN_URL_P . 'assets/images/svg/frame/' . $val;
						?>
							<input type="radio" class="radio_redsys" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $option_value ); ?>/>
							<label class="label_redsys" for="<?php echo esc_html( $key ); ?>"> <img src="<?php echo $image; ?>"></label>
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
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses self::get_settings()
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function settings_tab() {
		echo WCRed()->return_help_notice(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		// echo '<p><strong>' . __( 'Check <a href="https://redsys.joseconti.com/guias/configurar-push-notifications/" target="new">The Guide</a> for configuring Push Notifications. ', 'woocommerce-redsys' ) . '</strong><p>';
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
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @uses woocommerce_update_options()
	 * @uses self::get_settings()
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
	 */
	public static function update_settings() {
		woocommerce_update_options( self::get_settings() );
	}

	/**
	 * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
	 *
	 * @return array Array of settings for @see woocommerce_admin_fields() function.
	 */
	/**
	 * Package: WooCommerce Redsys Gateway
	 * Plugin URI: https://woocommerce.com/es-es/products/redsys-gateway/
	 * Copyright: (C) 2013 - 2023 José Conti
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

	public function redsys_qr_is_active() {
		$redsys_qr_is_active = get_option( 'redsys_qr_active', 'no' );
		if ( 'yes' === $redsys_qr_is_active ) {
			return true;
		} else {
			return false;
		}
	}

	public function user_redsys_jc() {
		$redsys_qr_jc = get_option( 'redsys_qr_user_redsys_jc' );
		return $redsys_qr_jc;
	}

	public function type() {
		$redsys_qr_type = get_option( 'redsys_qr_type' );
		return str_replace( '_type', '', $redsys_qr_type );
	}

	public function border() {
		$redsys_qr_border = get_option( 'redsys_qr_border' );
		return str_replace( '_border', '', $redsys_qr_border );
	}

	public function mcenter() {
		$redsys_qr_mcenter = get_option( 'redsys_qr_mcenter' );
		return str_replace( '_mcenter', '', $redsys_qr_mcenter );
	}

	public function frame() {
		$redsys_qr_frame = get_option( 'redsys_qr_frame' );
		return str_replace( '_frame', '', $redsys_qr_frame );
	}

	public function framelabel() {
		$redsys_qr_framelabel = get_option( 'redsys_qr_framelabel' );
		return $redsys_qr_framelabel;
	}

	public function label_font() {
		$redsys_qr_label_font = get_option( 'redsys_qr_label_font' );
		return $redsys_qr_label_font;
	}

	public function backcolor() {
		$redsys_qr_backcolor = get_option( 'redsys_qr_backcolor' );
		return $redsys_qr_backcolor;
	}

	public function frontcolor() {
		$redsys_qr_frontcolor = get_option( 'redsys_qr_frontcolor' );
		return $redsys_qr_frontcolor;
	}

	public function optionlogo() {
		$redsys_qr_optionlogo = get_option( 'redsys_qr_optionlogo' );
		return $redsys_qr_optionlogo;
	}

	public function gradient_active() {
		$redsys_qr_gradient_active = get_option( 'redsys_qr_gradient_active' );
		if ( 'yes' === $redsys_qr_gradient_active ) {
			return true;
		} else {
			return false;
		}
	}

	public function gradient_color() {
		$redsys_qr_gradient_color = get_option( 'redsys_qr_gradient_color' );
		return $redsys_qr_gradient_color;
	}

	public function marker_color_active() {
		$redsys_qr_marker_color_active = get_option( 'redsys_qr_marker_color_active' );
		if ( 'yes' === $redsys_qr_marker_color_active ) {
			return true;
		} else {
			return false;
		}
	}

	public function marker_out_color() {
		$redsys_qr_marker_out_color = get_option( 'redsys_qr_marker_out_color' );
		return $redsys_qr_marker_out_color;
	}

	public function marker_in_color() {
		$redsys_qr_marker_in_color = get_option( 'redsys_qr_marker_in_color' );
		return $redsys_qr_marker_in_color;
	}
}
Redsys_QR_Codes::init();
