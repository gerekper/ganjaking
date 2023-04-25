<?php

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

class WC_MS_Admin {

    private $wcms;

    public function __construct(WC_Ship_Multiple $wcms) {
        $this->wcms = $wcms;

        // settings styles and scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'settings_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'edit_user_scripts' ) );

        // save settings handler
        add_action( 'admin_post_wcms_update', array( $this, 'save_settings' ) );

        add_filter( 'woocommerce_shipping_settings', array( $this, 'shipping_settings' ) );
        add_filter( 'woocommerce_account_settings', array( $this, 'account_settings' ) );

	    add_action( 'admin_notices', array( $this, 'show_shipping_address_notices' ) );
	    add_action( 'edit_user_profile', array( $this, 'add_customer_shipping_addresses_link' ), 21 );
	    add_action( 'show_user_profile', array( $this, 'add_customer_shipping_addresses_link' ), 21 );

	    // delete address request
	    add_action( 'admin_post_wcms_delete_address', array( $this, 'delete_user_shipping_address' ) );
	    add_action( 'wp_ajax_wcms_edit_user_address', array( $this, 'edit_user_shipping_address' ) );
    }

    public static function settings_scripts() {
        $screen = get_current_screen();

        if ( $screen->id != 'woocommerce_page_wc-settings' ) {
            return;
        }

        wp_enqueue_script( 'wcms-product-search', plugins_url( 'assets/js/product-search.min.js', WC_Ship_Multiple::FILE ), array('jquery'), WC_SHIPPING_MULTIPLE_ADDRESSES_VERSION, true );
        wp_enqueue_script( 'wcms-admin', plugins_url( 'assets/js/admin.min.js', WC_Ship_Multiple::FILE ), array('jquery'), WC_SHIPPING_MULTIPLE_ADDRESSES_VERSION, true );
        wp_localize_script( 'wcms-product-search', 'wcms_product_search', array(
            'security' => wp_create_nonce( 'search-products' ),
            'isLessThanWC27' => version_compare( WC_VERSION, '3.0', '<' ),
        ) );

    }

	public static function edit_user_scripts() {
		$screen = get_current_screen();

		if ( $screen->id != 'user-edit' && $screen->id != 'profile' ) {
			return;
		}

		wp_enqueue_script( 'wcms-country-select', plugins_url( 'assets/js/country-select.min.js', WC_Ship_Multiple::FILE ), array( 'jquery' ), WC_SHIPPING_MULTIPLE_ADDRESSES_VERSION, true );
		wp_localize_script( 'wcms-country-select', 'wcms_country_select_params', apply_filters( 'wc_country_select_params', array(
			'countries'              => wp_json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
			'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'wc_shipping_multiple_address' ),
		) ) );

		wp_enqueue_script( 'wcms-edit-user', plugins_url( 'assets/js/user-edit.min.js', WC_Ship_Multiple::FILE ), array( 'jquery' ), WC_SHIPPING_MULTIPLE_ADDRESSES_VERSION, true );
	}

    /**
     * unused
     */
    public function save_settings() {
        $settings       = array();
        $methods        = (isset($_POST['shipping_methods'])) ? $_POST['shipping_methods'] : array();
        $products       = (isset($_POST['products'])) ? $_POST['products'] : array();
        $categories     = (isset($_POST['categories'])) ? $_POST['categories'] : array();
        $duplication    = (isset($_POST['cart_duplication']) && $_POST['cart_duplication'] == 1) ? true : false;

        if ( isset($_POST['lang']) && is_array($_POST['lang']) ) {
            update_option( 'wcms_lang', $_POST['lang'] );
        }

        foreach ( $methods as $id => $method ) {
            $row_products   = (isset($products[$id])) ? $products[$id] : array();
            $row_categories = (isset($categories[$id])) ? $categories[$id] : array();

            // there needs to be at least 1 product or category per row
            if ( empty($row_categories) && empty($row_products) ) {
                continue;
            }

            $settings[] = array(
                'products'  => $row_products,
                'categories'=> $row_categories,
                'method'    => $method
            );
        }

        update_option( $this->wcms->meta_key_settings, $settings );
        update_option( '_wcms_cart_duplication', $duplication );

        wp_redirect( add_query_arg( 'saved', 1, 'admin.php?page=wc-ship-multiple-products' ) );
        exit;
    }

    public function shipping_settings($settings) {
        $section_end = array_pop($settings);
        $shipping_table = array_pop($settings);
        $settings[] = array(
            'name'  =>  __( 'Multiple Shipping Addresses', 'wc_shipping_multiple_address' ),
            'desc'  => __( 'Page contents: [woocommerce_select_multiple_addresses] Parent: "Checkout"', 'wc_shipping_multiple_address' ),
            'id'    => 'woocommerce_multiple_addresses_page_id',
            'type'  => 'single_select_page',
            'std'   => true,
            'class' => 'chosen_select wc-enhanced-select',
            'css'   => 'min-width:300px;',
            'desc_tip' => false
        );
        $settings[] = $shipping_table;
        $settings[] = $section_end;

        return $settings;
    }

    public function account_settings($settings) {
        foreach ( $settings as $idx => $setting ) {
            if ( $setting['type'] == 'sectionend' && $setting['id'] == 'account_page_options' ){
                $front = array_slice( $settings, 0, $idx );
                $front[] = array(
                    'name'  =>  __( 'Account Shipping Addresses', 'wc_shipping_multiple_address' ),
                    'desc'  => __( 'Page contents: [woocommerce_account_addresses] Parent: "My Account"', 'wc_shipping_multiple_address' ),
                    'id'    => 'woocommerce_account_addresses_page_id',
                    'type'  => 'single_select_page',
                    'std'   => true,
                    'class' => 'chosen_select wc-enhanced-select',
                    'css'   => 'min-width:300px;',
                    'desc_tip' => false
                );
                array_splice( $settings, 0, $idx, $front );
                break;
            }
        }

        return $settings;
    }

	public function show_shipping_address_notices() {
		if ( isset( $_GET['wcms_address_deleted'] ) ) {
			echo '<div class="updated"><p>' . __('Shipping address deleted', 'wc_shipping_multiple_address' ) . '</p></div>';
		}
	}

	public function add_customer_shipping_addresses_link( $user ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		?>
		<h3><?php _e( 'Other Shipping Addresses', 'wc_shipping_multiple_address' ); ?></h3>

		<p>
			<a class="button view-addresses-table" href="#"><?php _e( 'View Addresses', 'wc_shipping_multiple_address' ); ?></a>
		</p>

		<div id="other_addresses_div" style="display: none;">
			<?php $this->render_user_addresses_table( $user ); ?>
		</div>
		<?php
	}

	public function render_user_addresses_table( $user ) {
		require 'wcms-admin-user-addresses-list-table.php';

		$table = new WC_MS_Admin_User_Addresses_List_Table( $user );
		$table->prepare_items();
		$table->display();
	}

	public function delete_user_shipping_address() {
		check_admin_referer( 'delete_shipping_address' );

		$user_id    = $_REQUEST['user_id'];
		$index      = $_REQUEST['index'];

		$user = new WP_User( $user_id );
		$addresses = $this->wcms->address_book->get_user_addresses( $user, false );

		if ( isset( $addresses[ $index ] ) ) {
			unset( $addresses[ $index ] );
		}

		$this->wcms->address_book->save_user_addresses( $user_id, $addresses );

		// redirect back to the profile page
		wp_safe_redirect( admin_url( 'user-edit.php?user_id=' . $user_id . '&wcms_address_deleted=1' ) );
		exit;
	}

	public function edit_user_shipping_address() {
		$address = array();
		parse_str( $_POST['data'], $address );
		$index      = $_POST['index'];
		$user_id    = $_POST['user'];
		$user       = new WP_User( $user_id );
		$addresses  = $this->wcms->address_book->get_user_addresses( $user, false );

		// store the same values without the shipping_ prefix
		foreach ( $address as $key => $value ) {
			$key = str_replace( 'wcms_', '', $key );
			$address[ $key ] = $value;
		}

		$addresses[ $index ] = $address;

		$this->wcms->address_book->save_user_addresses( $user_id, $addresses );

		die( wcms_get_formatted_address( $address ) );
	}
}
