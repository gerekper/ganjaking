<?php
defined( 'ABSPATH' ) || exit;

/**
 * Multi Vendor Compatibility Class
 *
 * @class   YITH_WCMBS_Multivendor_Compatibility
 * @since   1.0.0
 */
class YITH_WCMBS_Multivendor_Compatibility {
	/**
	 * @var string The panel page.
	 */
	const PANEL_PAGE = 'yith_wcmbs_vendor_panel';

	/**
	 * Single instance of the class
	 *
	 * @var YITH_WCMBS_Multivendor_Compatibility
	 * @since 1.0.0
	 */
	protected static $instance;

	/**
	 * List of options that will be overridden.
	 *
	 * @var string[]
	 */
	private $_options_to_override = array( 'yith_wpv_enable_product_amount', 'yith_wpv_vendors_product_limit' );

	/**
	 * @var $panel YIT_Plugin_Panel_WooCommerce Object
	 */
	protected $panel;

	/**
	 * Returns single instance of the class
	 *
	 * @return YITH_WCMBS_Multivendor_Compatibility
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * YITH_WCMBS_Multivendor_Compatibility constructor.
	 */
	protected function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'manage_metaboxes' ), 11 );

		add_filter( 'yith_wcmbs_create_membership', array( $this, 'set_create_membership_value' ), 10, 4 );

		// Plan Meta-box Options.
		add_filter( 'yith_wcmbs_plan_meta_box_options', array( $this, 'add_multi_vendor_options_in_plan' ) );
		add_action( 'yith_wcmbs_process_plan_meta', array( $this, 'save_multi_vendor_options_in_plan' ) );
		$this->override_options();

		if ( 'yes' !== get_option( 'yith_wpv_vendors_option_membership_management', 'no' ) ) {
			return;
		}

		require_once 'multivendor-utils/class.yith-wcmbs-members-list-table.php';

		if ( is_admin() ) {
			add_filter( 'yith_wcmbs_plan_meta_box_options', array( $this, 'remove_options_from_plan' ) );

			// Vendor Membership Plans management.
			add_filter( 'request', array( $this, 'filter_plans_and_messages_list' ) );

			// Edit Vendor Metabox in Plans.
			add_action( 'add_meta_boxes', array( $this, 'single_value_taxonomy' ) );

			// Add Members for Vendors
			add_action( 'yith_wcmbs_vendor_render_members_tab', array( $this, 'render_vendor_members' ) );
			add_action( 'admin_menu', array( $this, 'register_vendor_panel' ), 5 );
			add_filter( 'yith_wcmv_admin_vendor_menu_items', array( $this, 'add_allowed_menu_items' ), 10, 1 );
			add_action( 'yit_plugin_panel_asset_loading', array( $this, 'panel_assets_loading' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 99 );
		}

		add_filter( 'yith_wcmv_vendors_factory_read_vendor_id', array( $this, 'filter_read_vendor_id' ), 10, 4 );

		// Filter not_allowed, members_only, non_members_only post ids for Vendors.
		add_filter( 'yith_wcmbs_not_allowed_post_ids', array( $this, 'filter_not_allowed_ids' ), 10, 2 );
		add_filter( 'yith_wcmbs_filter_allowed_by_vendor_plans', array( $this, 'filter_allowed_by_vendor_plans' ), 10, 2 );

		add_filter( 'yith_wcmbs_messages_widget_allowed_plans', array( $this, 'remove_vendor_plans_for_messages' ) );
		add_filter( 'yith_wcmbs_user_has_access_to_product', array( $this, 'vendor_is_owner_of_product' ), 10, 3 );

		add_filter( 'yith_wcmbs_add_products_in_plan_cat_tag_args', array( $this, 'filter_products_in_vendor_plan' ), 10, 3 );
		add_filter( 'yith_wcmbs_product_is_in_plans', array( $this, 'filter_product_plans_for_vendor_plan' ), 10, 3 );

		// Filter only vendor products for vendor plans (useful if vendors include products through categories and/or tags).
		add_filter( 'yith_wcmbs_plan_get_included_items_query_join', array( $this, 'included_items_query_join' ), 10, 4 );
		add_filter( 'yith_wcmbs_plan_get_included_items_query_where', array( $this, 'included_items_query_where' ), 10, 4 );
	}

	/**
	 * Get the vendor.
	 * Useful to support Multi Vendor 4.0 and backward compatibility.
	 *
	 * @param mixed  $vendor The vendor.
	 * @param string $obj    The object type.
	 *
	 * @return YITH_Vendor
	 * @since 1.13.0
	 */
	public static function get_vendor( $vendor = false, $obj = 'vendor' ) {
		return function_exists( 'yith_wcmv_get_vendor' ) ? yith_wcmv_get_vendor( $vendor, $obj ) : yith_get_vendor( $vendor, $obj );
	}

	/**
	 * Get the vendor ID.
	 * Useful to support Multi Vendor 4.0 and backward compatibility.
	 *
	 * @param YITH_Vendor $vendor The vendor.
	 *
	 * @return int
	 * @since 1.13.0
	 */
	public static function get_vendor_id( $vendor ) {
		$vendor_id = is_callable( array( $vendor, 'get_id' ) ) ? $vendor->get_id() : $vendor->id;

		return absint( $vendor_id );
	}

	/**
	 * Get the vendor taxonomy name.
	 * Useful to support Multi Vendor 4.0 and backward compatibility.
	 *
	 * @return string
	 * @since 1.13.0
	 */
	public static function get_vendor_taxonomy_name() {
		return defined( 'YITH_Vendors_Taxonomy::TAXONOMY_NAME' ) ? YITH_Vendors_Taxonomy::TAXONOMY_NAME : YITH_Vendors()->get_taxonomy_name();
	}

	/**
	 * Filter read vendor id in factory.
	 * To allow retrieving the vendor by membership objects through the `yith_wcmv_get_vendor` function.
	 *
	 * @param int                                                     $vendor_id   The vendor ID.
	 * @param int|WP_Post|YITH_WCMBS_Plan|YITH_WCMBS_Membership|false $object      The object.
	 * @param string                                                  $object_type The object type.
	 *
	 * @return int
	 * @see YITH_Vendors_Factory::read
	 */
	public function filter_read_vendor_id( $vendor_id, $object, $object_type ) {
		$type_class_map = array(
			YITH_WCMBS_Post_Types::$plan       => 'YITH_WCMBS_Plan',
			'membership_plan'                  => 'YITH_WCMBS_Plan',
			YITH_WCMBS_Post_Types::$membership => 'YITH_WCMBS_Membership',
			'membership'                       => 'YITH_WCMBS_Membership',
		);
		$object_class   = $type_class_map[ $object_type ] ?? '';

		if ( $object_class ) {
			if ( false === $object ) {
				global $post;
				$post_id = isset( $post ) ? $post->ID : 0;
			} elseif ( $object instanceof WP_Post ) {
				$post_id = $object->ID;
			} elseif ( is_a( $object, $object_class ) && is_callable( array( $object, 'get_id' ) ) ) {
				$post_id = $object->get_id();
			} elseif ( is_numeric( $object ) ) {
				$post_id = absint( $object );
			}

			$terms = ! empty( $post_id ) ? wp_get_post_terms( $post_id, self::get_vendor_taxonomy_name() ) : array();

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$vendor_term = array_shift( $terms );
				$vendor_id   = $vendor_term->term_id;
			}
		}

		return $vendor_id;
	}

	/**
	 * Retrieve the plan meta-box options (related to Multi Vendor)
	 *
	 * @return array
	 * @since 1.4.0
	 */
	public function get_plan_meta_box_options() {
		return include YITH_WCMBS_DIR . '/includes/compatibility/multivendor-utils/mv-plan-metabox-options.php';
	}

	/**
	 * add Multi Vendor settings in each plan
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 * @since 1.4.0
	 */
	public function add_multi_vendor_options_in_plan( $tabs ) {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$multi_vendor_options = $this->get_plan_meta_box_options();
			$tabs                 = array_merge( $tabs, $multi_vendor_options );
		}

		return $tabs;
	}

	/**
	 * Save Multi Vendor options in plan
	 *
	 * @param YITH_WCMBS_Plan $plan The plan.
	 *
	 * @since 1.4.0
	 */
	public function save_multi_vendor_options_in_plan( $plan ) {
		$multi_vendor_options = $this->get_plan_meta_box_options();

		foreach ( $multi_vendor_options as $key => $option ) {
			$type = $option['type'];
			if ( 'title' === $type ) {
				continue;
			}

			switch ( $type ) {
				case 'checkbox':
				case 'onoff':
					$value = isset( $_POST[ $key ] ) ? 'yes' : 'no';
					break;
				default:
					$value = $_POST[ $key ] ?? '';
			}

			$plan->update_meta_data( $key, $value );
		}
	}

	/**
	 * Override options.
	 *
	 * @since 1.3.10
	 */
	public function override_options() {
		foreach ( $this->_options_to_override as $option ) {
			add_filter( "pre_option_{$option}", array( $this, 'override_option_value' ), 10, 2 );
		}
	}

	/**
	 * override the option value
	 *
	 * @param $value
	 * @param $option
	 *
	 * @return mixed
	 * @since 1.3.10
	 */
	public function override_option_value( $value, $option ) {
		switch ( $option ) {
			case 'yith_wpv_enable_product_amount':

				$member      = YITH_WCMBS_Members()->get_member( get_current_user_id() );
				$memberships = $member->get_membership_plans( array( 'return' => 'complete' ) );
				if ( $memberships ) {
					foreach ( $memberships as $membership ) {
						$plan = $membership->get_plan();
						if ( $plan && yith_plugin_fw_is_true( $plan->get_meta_data( '_mv-override-multi-vendor-settings' ) ) ) {
							$value = yith_plugin_fw_is_true( $plan->get_meta_data( '_mv-vendors_product_amount_limit' ) ) ? 'yes' : 'no';
							if ( 'no' === $value ) {
								break;
							}
						}
					}
				}
				break;
			case 'yith_wpv_vendors_product_limit':
				$member      = YITH_WCMBS_Members()->get_member( get_current_user_id() );
				$memberships = $member->get_membership_plans( array( 'return' => 'complete' ) );
				if ( $memberships ) {
					$values = array();
					foreach ( $memberships as $membership ) {
						$plan = $membership->get_plan();
						if ( $plan && yith_plugin_fw_is_true( $plan->get_meta_data( '_mv-override-multi-vendor-settings' ) ) ) {
							if ( yith_plugin_fw_is_true( $plan->get_meta_data( '_mv-vendors_product_amount_limit' ) ) ) {
								$values[] = absint( $plan->get_meta_data( '_mv-vendors_product_amount' ) );
							}
						}
					}
					if ( $values ) {
						$value = max( $values );
					}
				}
				break;

		}

		return $value;
	}

	public function filter_product_plans_for_vendor_plan( $plan_ids, $product_id ) {
		if ( $plan_ids ) {
			$product_vendor    = self::get_vendor( $product_id, 'product' );
			$product_vendor_id = $product_vendor->is_valid() && $product_vendor->has_limited_access( $product_vendor->get_owner() ) ? self::get_vendor_id( $product_vendor ) : false;
			foreach ( $plan_ids as $key => $plan_id ) {
				$plan_vendor = self::get_vendor( $plan_id, 'product' );
				if ( $plan_vendor->is_valid() && $plan_vendor->has_limited_access( $plan_vendor->get_owner() ) ) {
					$plan_vendor_id = self::get_vendor_id( $plan_vendor );
					if ( ! $product_vendor_id || $plan_vendor_id !== $product_vendor_id ) {
						unset( $plan_ids[ $key ] );
					}

				}
			}
		}

		return $plan_ids;
	}

	public function filter_products_in_vendor_plan( $cat_tag_args, $plan_id ) {
		$vendor = self::get_vendor( $plan_id, 'product' );
		if ( $vendor->is_valid() && $vendor->has_limited_access( $vendor->get_owner() ) ) {
			$tax_query = array(
				'relation' => 'AND',
				array(
					'taxonomy' => self::get_vendor_taxonomy_name(),
					'field'    => 'id',
					'terms'    => self::get_vendor_id( $vendor ),
					'operator' => 'IN',
				),
			);

			$cat_tag_args['tax_query'] = array_merge( $tax_query, array( $cat_tag_args['tax_query'] ) );
		}

		return $cat_tag_args;
	}

	/**
	 * @param bool     $create_membership
	 * @param int      $id               the product id
	 * @param WC_Order $order            the order
	 * @param array    $plan_product_ids the plan product ids
	 *
	 * @return bool
	 */
	public function set_create_membership_value( $create_membership, $id, $order, $plan_product_ids ) {
		$product           = wc_get_product( $id );
		$vendor            = self::get_vendor( $product, 'product' );
		$is_vendor_product = $vendor->is_valid();
		$is_parent_order   = ! empty( YITH_Vendors()->orders ) && ! ! YITH_Vendors()->orders->get_suborder( $order->get_id() );

		if ( $is_parent_order ) {
			return ! $is_vendor_product;
		}

		return $create_membership;
	}

	/**
	 * Handle meta-boxes for vendors
	 */
	public function manage_metaboxes() {
		if ( 'yes' !== get_option( 'yith_wpv_vendors_option_membership_management', 'no' ) ) {
			$vendor = self::get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() && ! current_user_can( 'manage_users' ) ) {
				remove_meta_box( 'yith-wcmbs-membership-options', null, 'normal' );
			}
		}
	}

	public function remove_options_from_plan( $options ) {
		$vendor = self::get_vendor( 'current', 'user' );
		if ( $vendor->is_valid() && $vendor->has_limited_access() && ! current_user_can( 'manage_users' ) ) {
			$allowed_options = array(
				'options',
				'enable_purchasing',
				'target_products',
				'duration_enabled',
				'duration',
				'show_contents_in_membership_details',
				'download_limit_type',
				'credits_availability',
				'different_download_number_first_term_enabled',
				'download_number_first_term',
				'permissions',
				'products-title',
				'products',
				'product_categories',
				'product_tags',
				'product_sorting',
			);

			foreach ( $options as $key => $option ) {
				if ( ! in_array( $key, $allowed_options ) ) {
					unset( $options[ $key ] );
				}
			}

			if ( isset( $options['products']['deps'] ) ) {
				unset( $options['products']['deps'] );
			}
			if ( isset( $options['product_categories']['deps'] ) ) {
				unset( $options['product_categories']['deps'] );
			}
			if ( isset( $options['product_tags']['deps'] ) ) {
				unset( $options['product_tags']['deps'] );
			}
			if ( isset( $options['product_sorting']['deps'] ) ) {
				unset( $options['product_sorting']['deps'] );
			}

			if ( ! isset( $options['products']['data'] ) ) {
				$options['products']['data'] = array();
			}
			$vendor_product_ids = $vendor->get_products( array( 'yith_wcmbs_suppress_filter' => true ) );
			$vendor_product_ids = ! ! $vendor_product_ids ? $vendor_product_ids : array( 0 );

			$options['products']['data']['include'] = implode( ',', $vendor_product_ids );
		}

		return $options;
	}

	/**
	 * return true if vendor is owner of the product
	 *
	 * @param bool $return
	 * @param int  $user_id
	 * @param int  $product_id
	 *
	 * @return bool
	 * @since  1.0.0
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	public function vendor_is_owner_of_product( $return, $user_id, $product_id ) {
		$vendor = self::get_vendor( $user_id, 'user' );
		if ( $vendor->is_valid() ) {
			$products = $vendor->get_products();
			if ( ! empty( $products ) && in_array( $product_id, $products ) ) {
				return true;
			} else {
				return false;
			}
		}

		return $return;
	}

	/**
	 * Add allowed menu items for vendor.
	 *
	 * @param array $items Allowed menu items.
	 *
	 * @return array
	 * @since 1.13.0
	 */
	public function add_allowed_menu_items( $items ) {
		$items[] = self::PANEL_PAGE;

		return $items;
	}

	public function register_vendor_panel() {
		$vendor = self::get_vendor( 'current', 'user' );
		if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
			if ( ! empty( $this->panel ) ) {
				return;
			}

			$tabs = array(
				'vendor-membership-plans' => _x( 'Membership plans', 'Tab title in plugin settings panel', 'yith-woocommerce-membership' ),
				'vendor-members'          => _x( 'Members', 'Tab title in vendor plugin settings panel', 'yith-woocommerce-membership' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'class'            => yith_set_wrapper_class(),
				'page_title'       => 'WooCommerce Membership',
				'menu_title'       => 'Membership',
				'capability'       => 'edit_plans',
				'parent'           => '',
				'parent_page'      => '',
				'page'             => self::PANEL_PAGE,
				'admin-tabs'       => $tabs,
				'icon_url'         => 'dashicons-groups',
				'position'         => 30,
				'options-path'     => YITH_WCMBS_DIR . '/includes/compatibility/multivendor-utils/panel',
			);

			/* === Fixed: not updated theme  === */
			if ( ! class_exists( 'YIT_Plugin_Panel_WooCommerce' ) ) {
				require_once YITH_WCMBS_DIR . 'plugin-fw/lib/yit-plugin-panel-wc.php';
			}

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}
	}

	/**
	 * Is this the Vendor Membership panel?
	 *
	 * @return bool
	 */
	public function is_panel() {
		$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		$screen_id = $screen ? $screen->id : false;

		if ( $screen_id && strpos( $screen_id, self::PANEL_PAGE ) !== false ) {
			return true;
		}

		return false;
	}

	/**
	 * Load panel assets in vendor panel page
	 *
	 * @param bool $load it needs to be loaded?
	 *
	 * @return bool
	 */
	public function panel_assets_loading( $load ) {
		if ( $this->is_panel() ) {
			$load = true;
		}

		return $load;
	}

	/**
	 * Enqueue Admin Scripts and Styles
	 */
	public function admin_enqueue_scripts() {
		if ( $this->is_panel() ) {
			wp_enqueue_style( 'yith-wcmbs-admin-styles' );
			wp_enqueue_style( 'yith-wcmbs-membership-statuses' );
			wp_enqueue_script( 'yith-wcmbs-admin' );

			$css = 'span.yith-wcmbs-users-membership-info {padding: 5px 12px}';
			wp_add_inline_style( 'yith-wcmbs-membership-statuses', $css );
		}
	}

	/**
	 * Render vendor members WP List
	 */
	public function render_vendor_members() {
		$vendor = self::get_vendor( 'current', 'user' );
		if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
			echo '<div class="yith-plugin-fw-panel-custom-tab-container">';
			echo '<h2>' . esc_html__( 'Members', 'yith-woocommerce-membership' ) . '</h2>';

			$table = new YITH_WCMBS_Members_List_Table();
			$table->prepare_items();
			$table->display();

			echo '</div>';
		}
	}


	/**
	 * Remove the WooCommerce taxonomy Metabox and add a new Metabox for single taxonomy management in Membership Plans
	 *
	 * @return void
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since  1.0.0
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 */
	public function single_value_taxonomy() {

		$id              = 'tagsdiv-' . self::get_vendor_taxonomy_name();
		$taxonomy        = get_taxonomies( array( 'show_ui' => true ), 'object' );
		$product_vendors = $taxonomy[ self::get_vendor_taxonomy_name() ];
		$page            = 'yith-wcmbs-plan';
		$context         = 'side';

		// Handle backward compatibility with Multi Vendor < 4.0
		$callback      = is_callable( 'YITH_Vendors_Taxonomy::single_taxonomy_meta_box' ) ? 'YITH_Vendors_Taxonomy::single_taxonomy_meta_box' : array( YITH_Vendors()->admin, 'single_taxonomy_meta_box' );
		$callback_args = array( 'taxonomy' => self::get_vendor_taxonomy_name() );
		$priority      = 'default';

		remove_meta_box( $id, $page, $context );
		add_meta_box( $id, $product_vendors->labels->name, $callback, $page, $context, $priority, $callback_args );
	}

	/**
	 * Remove vendor plans from the ones allowed for showing the Messages widget
	 *
	 * @param array $plans_ids The plan IDs.
	 *
	 * @return array
	 */
	public function remove_vendor_plans_for_messages( $plans_ids ) {
		$allowed_plans_ids = array();

		if ( ! empty( $plans_ids ) ) {
			foreach ( $plans_ids as $plan_id ) {
				$post_vendor_term = wp_get_post_terms( $plan_id, self::get_vendor_taxonomy_name(), array( 'fields' => 'ids' ) );
				if ( ! $post_vendor_term ) {
					$allowed_plans_ids[] = $plan_id;
				}
			}
		}

		return $allowed_plans_ids;
	}

	/**
	 * Filter not allowed ids for vendors
	 * Allow vendors to see their products in frontend
	 *
	 * @param array $not_allowed array of not allowed post ids
	 * @param int   $user_id     the id of the vendor
	 *
	 * @return array
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	public function filter_not_allowed_ids( $not_allowed, $user_id ) {
		$vendor = self::get_vendor( $user_id, 'user' );

		if ( $vendor->is_valid() ) {
			$vendor_product_ids = $vendor->get_products( array( 'yith_wcmbs_suppress_filter' => true ) );
			$not_allowed        = array_diff( $not_allowed, $vendor_product_ids );
		}

		return $not_allowed;
	}

	/**
	 * Filter members only ids for vendors' members
	 *
	 * @param array $allowed_post_ids      array of members only post ids
	 * @param array $user_membership_plans array of membership plans ids
	 *
	 * @return array
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	public function filter_allowed_by_vendor_plans( $allowed_post_ids, $user_membership_plans ) {
		$allowed_post_ids_for_vendor = array();
		$new_allowed_post_ids        = array();

		if ( ! empty( $user_membership_plans ) && ! empty( $allowed_post_ids ) ) {

			foreach ( $allowed_post_ids as $post_id ) {
				$post_vendor_term = wp_get_post_terms( $post_id, self::get_vendor_taxonomy_name(), array( "fields" => "ids" ) );
				if ( ! empty( $post_vendor_term ) ) {
					foreach ( $post_vendor_term as $term_id ) {
						$allowed_post_ids_for_vendor[ $term_id ][] = $post_id;
					}
				} else {
					$allowed_post_ids_for_vendor[0][] = $post_id;
				}
			}

			foreach ( $user_membership_plans as $membership_id ) {
				$plan_vendor_term = wp_get_post_terms( $membership_id, self::get_vendor_taxonomy_name(), array( "fields" => "ids" ) );
				if ( ! empty( $plan_vendor_term ) ) {
					foreach ( $plan_vendor_term as $term_id ) {
						if ( isset( $allowed_post_ids_for_vendor[ $term_id ] ) ) {
							$new_allowed_post_ids = array_merge( $new_allowed_post_ids, $allowed_post_ids_for_vendor[ $term_id ] );
						}
					}
				} else {
					if ( isset( $allowed_post_ids_for_vendor[0] ) ) {
						$new_allowed_post_ids = array_merge( $new_allowed_post_ids, $allowed_post_ids_for_vendor[0] );
					}
				}
			}
		}

		return array_unique( $new_allowed_post_ids );
	}

	/**
	 * Only show vendor's plans
	 *
	 * @param array $request Current request
	 */
	public function filter_plans_and_messages_list( $request ) {
		global $typenow;

		$vendor = self::get_vendor( 'current', 'user' );

		if ( is_admin() && ! current_user_can( 'manage_woocommerce' ) && $vendor->is_user_admin() && in_array( $typenow, array( 'yith-wcmbs-plan', 'yith-wcmbs-thread' ) ) ) {
			// Handle backward compatibility with Multi Vendor < 4.0
			$request[ self::get_vendor_taxonomy_name() ] = is_callable( array( $vendor, 'get_slug' ) ) ? $vendor->get_slug() : $vendor->slug;

			return apply_filters( "yith_wcmv_{$typenow}_request", $request );
		}

		return $request;
	}

	/**
	 * @param array           $join
	 * @param string          $post_type
	 * @param array           $args
	 * @param YITH_WCMBS_Plan $plan
	 *
	 * @return array
	 */
	public function included_items_query_join( $join, $post_type, $args, $plan ) {
		global $wpdb;
		if ( 'product' === $post_type ) {
			$vendor = self::get_vendor( $plan->get_id(), 'product' );

			if ( $vendor->is_valid() && $vendor->has_limited_access( $vendor->get_owner() ) ) {
				$vendor_id = self::get_vendor_id( $vendor );

				if ( $vendor_id ) {
					$join[] = "LEFT JOIN {$wpdb->term_relationships} AS mv_terms ON ( posts.ID = mv_terms.object_id )";
				}
			}
		}

		return $join;
	}

	/**
	 * @param array           $where
	 * @param string          $post_type
	 * @param array           $args
	 * @param YITH_WCMBS_Plan $plan
	 *
	 * @return array
	 */
	public function included_items_query_where( $where, $post_type, $args, $plan ) {
		global $wpdb;
		if ( 'product' === $post_type ) {
			$vendor = self::get_vendor( $plan->get_id(), 'product' );

			if ( $vendor->is_valid() && $vendor->has_limited_access( $vendor->get_owner() ) ) {
				$vendor_id = self::get_vendor_id( $vendor );

				if ( $vendor_id ) {
					$where[] = $wpdb->prepare( 'AND mv_terms.term_taxonomy_id = %d', absint( $vendor_id ) );
				}
			}
		}

		return $where;
	}
}

/**
 * Unique access to instance of YITH_WCMBS_Multivendor_Compatibility class
 *
 * @return YITH_WCMBS_Multivendor_Compatibility
 * @since 1.0.0
 */
function YITH_WCMBS_Multivendor_Compatibility() {
	return YITH_WCMBS_Multivendor_Compatibility::get_instance();
}