<?php
/**
 * Settings: General
 *
 * @package WC_Instagram/Admin/Settings
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Instagram_Settings_API', false ) ) {
	include_once WC_INSTAGRAM_PATH . 'includes/abstracts/abstract-wc-instagram-settings-api.php';
}

if ( class_exists( 'WC_Instagram_Settings_General', false ) ) {
	return;
}

/**
 * WC_Instagram_Settings_General class.
 */
class WC_Instagram_Settings_General extends WC_Instagram_Settings_API {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->id               = 'settings';
		$this->form_title       = _x( 'Instagram', 'settings page title', 'woocommerce-instagram' );
		$this->form_description = _x( 'Connect your Instagram Business account.', 'settings page description', 'woocommerce-instagram' );
	}

	/**
	 * Enqueues the settings scripts.
	 *
	 * @since 3.0.0
	 */
	public function enqueue_scripts() {
		$suffix = wc_instagram_get_scripts_suffix();

		// Register the script for keeping backward compatibility but don't enqueue it if not necessary.
		wp_register_script( 'wc-instagram-settings', WC_INSTAGRAM_URL . "assets/js/admin/settings{$suffix}.js", array( 'jquery-ui-sortable', 'jquery-tiptip', 'wc-clipboard' ), WC_INSTAGRAM_VERSION, true );
		wp_localize_script(
			'wc-instagram-settings',
			'wc_instagram_settings_params',
			array(
				'unload_confirmation_msg' => __( 'Your changed data will be lost if you leave this page without saving.', 'woocommerce-instagram' ),
			)
		);

		wp_enqueue_script( 'wc-instagram-product-catalogs', WC_INSTAGRAM_URL . "assets/js/admin/product-catalogs{$suffix}.js", array( 'jquery', 'jquery-tiptip', 'wc-clipboard', 'wc-backbone-modal' ), WC_INSTAGRAM_VERSION, true );
		wp_localize_script(
			'wc-instagram-product-catalogs',
			'wc_instagram_product_catalogs_params',
			array(
				'confirmDelete' => __( 'Are you sure you want to delete this catalog?', 'woocommerce-instagram' ),
				'nonce'         => array(
					'delete'     => wp_create_nonce( 'wc_instagram_delete_product_catalog' ),
					'fileAction' => wp_create_nonce( 'wc_instagram_product_catalog_file_action' ),
				),
			)
		);
	}

	/**
	 * Initialise form fields.
	 *
	 * @since 3.0.0
	 */
	public function init_form_fields() {
		if ( wc_instagram_is_connected() ) {
			$form_fields = array(
				'account'                    => array(
					'type'        => 'account',
					'title'       => _x( 'Connected as', 'setting title', 'woocommerce-instagram' ),
					'desc_tip'    => _x( 'The account connected.', 'setting desc', 'woocommerce-instagram' ),
					'no_validate' => true,
				),
				'page_id'                    => array(
					'type'     => 'select',
					'title'    => _x( 'Facebook page', 'setting title', 'woocommerce-instagram' ),
					'desc_tip' => _x( 'Choose the Facebook Page connected with your Instagram Business account.', 'setting desc', 'woocommerce-instagram' ),
					'options'  => wc_instagram_get_user_pages_choices(),
				),
				'disconnect'                 => array(
					'type'        => 'authorization',
					'title'       => _x( 'Disconnect account', 'setting title', 'woocommerce-instagram' ),
					'label'       => _x( 'Disconnect', 'disconnect account button', 'woocommerce-instagram' ),
					'desc_tip'    => _x( 'Revoke WooCommerce access to your Instagram account.', 'setting desc', 'woocommerce-instagram' ),
					'class'       => 'button',
					'action'      => 'disconnect',
					'no_validate' => true,
				),
				'shopping_section'           => array(
					'type'        => 'title',
					'title'       => _x( 'Product catalogs', 'settings section title', 'woocommerce-instagram' ),
					'description' => _x( 'Sync your products with your Commerce Manager catalogs and sell on Facebook and Instagram.', 'settings section desc', 'woocommerce-instagram' ),
				),
				'product_catalogs'           => array(
					'type'     => 'product_catalogs',
					'title'    => _x( 'Catalogs', 'setting title', 'woocommerce-instagram' ),
					'desc_tip' => _x( 'Define the product catalogs to upload to Facebook.', 'setting desc', 'woocommerce-instagram' ),
					'value'    => wc_instagram_get_product_catalogs( array(), 'objects' ),
				),
				'product_catalog_permalink'  => array(
					'type'        => 'text',
					'title'       => _x( 'Catalog permalink', 'setting title', 'woocommerce-instagram' ),
					'desc_tip'    => _x( 'Customize the permalink used by the product catalogs.', 'setting desc', 'woocommerce-instagram' ),
					'description' => wc_instagram_get_product_catalog_url( 'xxx' ),
					'default'     => 'product-catalog/',
				),
				'generate_catalogs_interval' => array(
					'type'              => 'number',
					'title'             => _x( 'Update catalogs interval', 'setting title', 'woocommerce-instagram' ),
					'desc_tip'          => _x( 'Define, in hours, how often the catalogs are updated.', 'setting desc', 'woocommerce-instagram' ),
					'css'               => 'width:50px;',
					'default'           => 1,
					'custom_attributes' => array(
						'min'  => 1,
						'step' => 1,
					),
				),
			);

			$has_business_account = wc_instagram_has_business_account();

			$description = _x( 'Display product-related Instagram images on each individual product screen.', 'settings section desc', 'woocommerce-instagram' );

			if ( ! $has_business_account ) {
				$description .= sprintf(
					' <br/><br/><span class="wc-instagram-notice warning feature-not-available">%s</span>',
					__( 'This functionality requires a Facebook Page connected to your Instagram Business account.', 'woocommerce-instagram' )
				);
			}

			$form_fields['product_page_section'] = array(
				'type'        => 'title',
				'title'       => _x( 'Product images', 'settings section title', 'woocommerce-instagram' ),
				'description' => $description,
			);

			if ( $has_business_account ) {
				$form_fields['product_hashtag_images'] = array(
					'type'              => 'number',
					'title'             => _x( 'Number of images', 'setting title', 'woocommerce-instagram' ),
					'desc_tip'          => _x( 'This sets the number of images shown on product page.', 'setting desc', 'woocommerce-instagram' ),
					'css'               => 'width:50px;',
					'default'           => 8,
					'custom_attributes' => array(
						'min'  => 1,
						'step' => 1,
					),
				);

				$form_fields['product_hashtag_columns'] = array(
					'type'              => 'number',
					'title'             => _x( 'Number of columns', 'setting title', 'woocommerce-instagram' ),
					'desc_tip'          => _x( 'This sets the number of columns shown on product page.', 'setting desc', 'woocommerce-instagram' ),
					'css'               => 'width:50px;',
					'default'           => 4,
					'custom_attributes' => array(
						'min'  => 1,
						'max'  => 8,
						'step' => 1,
					),
				);

				$form_fields['product_hashtag_images_type'] = array(
					'type'        => 'select',
					'title'       => _x( 'Images to display', 'setting title', 'woocommerce-instagram' ),
					'desc_tip'    => _x( 'Choose the images to display on product page.', 'setting desc', 'woocommerce-instagram' ),
					'description' => _x( 'Products can overwrite this option individually.', 'product data setting desc', 'woocommerce-instagram' ),
					'default'     => 'recent_top',
					'options'     => array(
						'recent_top' => _x( 'Recent images + Top images', 'setting option', 'woocommerce-instagram' ),
						'recent'     => _x( 'Recent images', 'setting option', 'woocommerce-instagram' ),
						'top'        => _x( 'Top images', 'setting option', 'woocommerce-instagram' ),
					),
				);
			}
		} else {
			$description = sprintf(
				/* translators: 1: documentation link, 2: arial-label */
				_x( 'We strongly recommend you to read our <a href="%1$s" aria-label="%2$s" target="_blank">documentation</a> before connecting your account.', 'setting desc', 'woocommerce-instagram' ),
				esc_url( 'https://woocommerce.com/document/woocommerce-instagram/' ),
				esc_attr_x( 'View WooCommerce Instagram documentation', 'aria-label: documentation link', 'woocommerce-instagram' )
			);

			$form_fields = array(
				'connect' => array(
					'type'        => 'authorization',
					'title'       => _x( 'Connect Account', 'setting title', 'woocommerce-instagram' ),
					'label'       => _x( 'Login with Facebook', 'connect account button', 'woocommerce-instagram' ),
					'desc_tip'    => _x( 'Authorize WooCommerce to access your Instagram account.', 'setting desc', 'woocommerce-instagram' ),
					'description' => $description,
					'class'       => 'button button-primary',
					'action'      => 'connect',
					'no_validate' => true,
				),
			);
		}

		$this->form_fields = $form_fields;
	}

	/**
	 * Output the settings screen.
	 *
	 * @since 3.0.0
	 *
	 * @global bool $hide_save_button Hide the save button or not.
	 */
	public function admin_options() {
		global $hide_save_button;

		if ( ! wc_instagram_is_connected() ) {
			$hide_save_button = true;
		}

		// Forces the initialization of the form to refresh the conditional fields.
		$this->init_form_fields();

		parent::admin_options();
	}

	/**
	 * Before saving the form.
	 *
	 * @since 3.0.0
	 */
	public function before_save() {
		// Product catalogs permalink changed.
		if ( wc_instagram_get_setting( 'product_catalog_permalink', 'product-catalog/' ) !== $this->settings['product_catalog_permalink'] ) {
			flush_rewrite_rules();
		}

		// Product catalogs interval changed.
		if ( wc_instagram_get_setting( 'generate_catalogs_interval', 1 ) !== $this->settings['generate_catalogs_interval'] ) {
			WC_Instagram_Actions::clear( 'generate_catalogs' );
		}

		// Images type changed.
		if ( isset( $this->settings['product_hashtag_images_type'] ) && wc_instagram_get_setting( 'product_hashtag_images_type', 'recent_top' ) !== $this->settings['product_hashtag_images_type'] ) {
			wp_schedule_single_event( time(), 'wc_instagram_clear_product_hashtag_images_transients', array( 'images_type' => '' ) );
		}
	}

	/**
	 * Generates the HTML for the 'authorization' field.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key  The field key.
	 * @param mixed  $data The field data.
	 * @return string
	 */
	public function generate_authorization_html( $key, $data ) {
		$defaults = array(
			'action'            => '',
			'label'             => '',
			'class'             => 'button',
			'css'               => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();

		$this->output_field_start( $key, $data );

		printf(
			'<a href="%1$s" class="%2$s" style="%3$s"%4$s>%5$s</a>',
			esc_url( wc_instagram_get_authorization_url( $data['action'] ) ),
			esc_attr( $data['class'] ),
			esc_attr( $data['css'] ),
			wp_kses_post( $this->get_custom_attribute_html( $data ) ),
			esc_html( $data['label'] )
		);

		$this->output_field_end( $key, $data );

		return ob_get_clean();
	}

	/**
	 * Generates the HTML for the 'account' field.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key  The field key.
	 * @param mixed  $data The field data.
	 * @return string
	 */
	public function generate_account_html( $key, $data ) {
		$user_name = ( ! empty( $this->settings['user_name'] ) ? $this->settings['user_name'] : '-' );

		if ( ! $user_name ) {
			return '';
		}

		$defaults = array(
			'class'             => '',
			'css'               => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();

		$this->output_field_start( $key, $data );

		printf(
			'<label class="%1$s" style="%2$s"%3$s>%4$s</label>',
			esc_attr( $data['class'] ),
			esc_attr( $data['css'] ),
			wp_kses_post( $this->get_custom_attribute_html( $data ) ),
			esc_html( $user_name )
		);

		$this->output_field_end( $key, $data );

		return ob_get_clean();
	}

	/**
	 * Generates the HTML for the 'product_catalogs' field.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key  The field key.
	 * @param mixed  $data The field data.
	 * @return string
	 */
	public function generate_product_catalogs_html( $key, $data ) {
		$defaults = array(
			'class'             => '',
			'css'               => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		$field       = $data;
		$field['id'] = $key;

		ob_start();

		$this->output_field_start( $key, $data );

		$instance = new WC_Instagram_Admin_Field_Product_Catalogs( $field );
		$instance->output();

		$this->output_field_end( $key, $data );

		return ob_get_clean();
	}

	/**
	 * Validates the 'page_id' field.
	 *
	 * @since 3.0.0
	 * @throws Exception If the Facebook page doesn't have an Instagram Business Account connected to it.
	 *
	 * @param string $key Field key.
	 * @param string $value Posted Value.
	 * @return string
	 */
	public function validate_page_id_field( $key, $value ) {
		$this->settings['instagram_business_account'] = array();

		if ( $value ) {
			$account = wc_instagram_get_business_account_from_page( $value );

			if ( ! $account ) {
				throw new Exception( _x( "The selected Facebook Page doesn't have an Instagram Business Account connected to it.", 'settings error', 'woocommerce-instagram' ) );
			} elseif ( ! empty( $account ) ) {
				$this->settings['instagram_business_account'] = $account;
			}
		}

		return $value;
	}

	/**
	 * Validates the 'product_catalog_permalink' field.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key Field key.
	 * @param string $value Posted Value.
	 * @return string
	 */
	public function validate_product_catalog_permalink_field( $key, $value ) {
		$permalink = sanitize_title( $value );

		if ( empty( $permalink ) ) {
			$data      = $this->get_form_field( $key );
			$permalink = $data['default'];
		}

		return trailingslashit( $permalink );
	}

	/**
	 * Sanitizes the 'products_catalogs' field.
	 *
	 * @since 3.0.0
	 * @deprecated 4.0.0
	 *
	 * @param mixed $value The field value.
	 * @return array
	 */
	public function sanitize_product_catalogs( $value ) {
		wc_deprecated_function( __FUNCTION__, '4.0.0' );

		return $value;
	}

	/**
	 * Sanitize the settings before save the option.
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings The settings to sanitize.
	 * @return array
	 */
	public function sanitized_fields( $settings ) {
		unset( $settings['product_catalogs'] );

		return parent::sanitized_fields( $settings );
	}
}
