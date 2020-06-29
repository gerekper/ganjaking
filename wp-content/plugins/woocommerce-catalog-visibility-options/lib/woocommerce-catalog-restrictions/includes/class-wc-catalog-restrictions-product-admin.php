<?php
/* Fire our meta box setup function on the post editor screen. */

class WC_Catalog_Restrictions_Product_Admin {

	public static $instance;

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new WC_Catalog_Restrictions_Product_Admin();
		}

		return self::$instance;
	}

	/* Meta box setup function. */

	public function __construct() {
		add_action( 'load-post.php', array( $this, 'post_meta_boxes_setup' ) );
		add_action( 'load-post-new.php', array( $this, 'post_meta_boxes_setup' ) );

		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'render_panel' ) );

	}

	public function add_tab() {
		?>
        <li class="wc_catalog_restrictions_tab wc_catalog_restrictions">
        <a href="#wc_catalog_restrictions"><span><?php _e( 'Restrictions', 'wc_catalog_restrictions' ); ?></span></a>
        </li><?php
	}


	function post_meta_boxes_setup() {
		global $woocommerce, $wc_catalog_restrictions;
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'wc-product-restrictions-admin', $wc_catalog_restrictions->plugin_url() . 'assets/css/admin.css', null, 1.1 );
		wp_enqueue_script( 'wc-product-restrictions-admin', $wc_catalog_restrictions->plugin_url() . 'assets/js/admin.js', array( 'jquery' ) );


		add_action( 'woocommerce_process_product_meta', array( $this, 'save_meta' ), 1, 2 );
	}


	/* Display the post meta box. */

	function render_panel() {
		global $woocommerce, $wc_catalog_restrictions, $wp_roles;
		global $post;
		$object = wc_get_product( $post );

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$all_roles = $wp_roles->roles;

        $current_role_restrictions_type = $object->get_meta('_wc_restrictions', true);
		$current_restrictions = $object->get_meta( '_wc_restrictions_allowed', false );
		if ( ! $current_restrictions ) {
			$current_restrictions = array();
		} elseif ( count( $current_restrictions ) ) {
			$temp = array();
			foreach ( $current_restrictions as $l ) {
				if ( is_object( $l ) ) {
					$temp[] = $l->value;
				} else {
					$temp[] = $l;
				}
			}
			$current_restrictions = $temp;
		}

		$current_locations = $object->get_meta( '_wc_restrictions_locations', false );
		if ( ! $current_locations ) {
			$current_locations = array();
		} elseif ( count( $current_locations ) ) {
			$temp = array();
			foreach ( $current_locations as $l ) {
				if ( is_object( $l ) ) {
					$temp[] = $l->value;
				} else {
					$temp[] = $l;
				}
			}
			$current_locations = $temp;
		}

		$current_purchase_restrictions_type = $object->get_meta( '_wc_restrictions_purchase', true );
		$current_price_restrictions_type    = $object->get_meta( '_wc_restrictions_price', true );

		$current_purchase_restrictions = $object->get_meta( '_wc_restrictions_purchase_roles', true );
		if ( ! $current_purchase_restrictions ) {
			$current_purchase_restrictions = array();
		}

		$current_purchase_location_restrictions = $object->get_meta( '_wc_restrictions_purchase_locations', true );
		if ( ! $current_purchase_location_restrictions ) {
			$current_purchase_location_restrictions = array();
		}

		$current_price_restrictions = $object->get_meta( '_wc_restrictions_price_roles', true );
		if ( ! $current_price_restrictions ) {
			$current_price_restrictions = array();
		}

		$current_price_location_restrictions = $object->get_meta( '_wc_restrictions_price_locations', true );
		if ( ! $current_price_location_restrictions ) {
			$current_price_location_restrictions = array();
		}

		$locations_enabled = $wc_catalog_restrictions->get_setting( '_wc_restrictions_locations_enabled', 'no' );

		include( dirname( __FILE__ ) . '/views/wc-metabox.php' );
	}

	function save_meta( $post_id, $post ) {
		global $wc_catalog_restrictions;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		$product = wc_get_product( $post_id );

		$restrictions_allowed = filter_input( INPUT_POST, '_wc_restrictions', FILTER_SANITIZE_STRIPPED );
		if ( $restrictions_allowed == 'inherit' ) {
			//Delete the post meta key on inherit so our taxonomy query will know what items it needs to exlude.
			$product->delete_meta_data( '_wc_restrictions' );
		} else {
			$product->update_meta_data( '_wc_restrictions', $restrictions_allowed );
		}
		$meta_key = '_wc_restrictions_allowed';

		//Clear out old roles
		$product->delete_meta_data( $meta_key );

		if ( $restrictions_allowed == 'restricted' ) {
			$wc_roles = ( isset( $_POST['wc_restrictions_allowed'] ) ? $_POST['wc_restrictions_allowed'] : '' );
			if ( $wc_roles && count( $wc_roles ) ) {
				foreach ( $wc_roles as $role ) {
					$product->add_meta_data( $meta_key, $role, false );
				}
			} else {
				$product->add_meta_data( $meta_key, '', false );
				//add an empty restriction so our query filter can filter this properly. 
			}
		}

		if ( $wc_catalog_restrictions->get_setting( '_wc_restrictions_locations_enabled', 'no' ) == 'yes' ) {
			$locations_allowed = filter_input( INPUT_POST, '_wc_restrictions_location' );
			if ( $locations_allowed == 'inherit' ) {
				$product->delete_meta_data( '_wc_restrictions_location' );
				$product->delete_meta_data( '_wc_restrictions_locations' );
			} else {
				$product->update_meta_data( '_wc_restrictions_location', $locations_allowed );
				$product->delete_meta_data( '_wc_restrictions_locations' );
				$wc_locations = isset( $_POST['wc_restrictions_locations'] ) ? $_POST['wc_restrictions_locations'] : array( '' );
				foreach ( $wc_locations as $location ) {
					$product->add_meta_data( '_wc_restrictions_locations', $location, false );
				}
			}
		}

		$purchase_roles_allowed = filter_input( INPUT_POST, '_wc_restrictions_purchase' );
		$product->update_meta_data( '_wc_restrictions_purchase', $purchase_roles_allowed );
		if ( $purchase_roles_allowed == 'inherit' ) {
			$product->delete_meta_data( '_wc_restrictions_purchase_roles' );
		} elseif ( $purchase_roles_allowed == 'restricted' ) {
			$proles = isset( $_POST['wc_restrictions_purchase_roles'] ) ? $_POST['wc_restrictions_purchase_roles'] : array( '' );
			$product->update_meta_data( '_wc_restrictions_purchase_roles', $proles );
			$product->delete_meta_data( '_wc_restrictions_purchase_locations' );
		} elseif ( $purchase_roles_allowed == 'locations_allowed' || $purchase_roles_allowed == 'locations_restricted' ) {
			$plocations = isset( $_POST['wc_restrictions_purchase_locations'] ) ? $_POST['wc_restrictions_purchase_locations'] : array( '' );
			$product->update_meta_data( '_wc_restrictions_purchase_locations', $plocations );
			$product->delete_meta_data( '_wc_restrictions_purchase_roles' );
		}


		$price_roles_allowed = filter_input( INPUT_POST, '_wc_restrictions_price' );
		$product->update_meta_data( '_wc_restrictions_price', $price_roles_allowed );
		if ( $price_roles_allowed == 'inherit' ) {
			$product->delete_meta_data( '_wc_restrictions_price_roles' );
		} elseif ( $price_roles_allowed == 'restricted' ) {
			$proles = isset( $_POST['wc_restrictions_price_roles'] ) ? $_POST['wc_restrictions_price_roles'] : array( '' );
			$product->update_meta_data( '_wc_restrictions_price_roles', $proles );
			$product->delete_meta_data( '_wc_restrictions_price_locations' );
		} elseif ( $price_roles_allowed == 'locations_allowed' || $price_roles_allowed == 'locations_restricted' ) {
			$plocations = isset( $_POST['wc_restrictions_price_locations'] ) ? $_POST['wc_restrictions_price_locations'] : array( '' );
			$product->update_meta_data( '_wc_restrictions_price_locations', $plocations );
			$product->delete_meta_data( '_wc_restrictions_price_roles' );
		}

		$product->save_meta_data();

	}

}