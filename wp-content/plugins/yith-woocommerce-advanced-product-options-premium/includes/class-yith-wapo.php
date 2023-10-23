<?php
/**
 * WAPO Main Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO' ) ) {

	/**
	 * YITH_WAPO Class
	 */
	class YITH_WAPO {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WAPO
		 */
		public static $instance;

		/**
		 * Admin object
		 *
		 * @var YITH_WAPO_Admin
		 */
		public $admin;

		/**
		 * Front object
		 *
		 * @var YITH_WAPO_Front
		 */
		public $front;

		/**
		 * Cart object
		 *
		 * @var YITH_WAPO_Cart
		 */
		public $cart;

		/**
		 * DB object
		 *
		 * @var YITH_WAPO_DB
		 */
		public $db;
		/**
		 * WPML object
		 *
		 * @var YITH_WAPO_WPML
		 */
		public $wpml;

		/**
		 * Check if YITH Multi Vendor is installed
		 *
		 * @var boolean
		 * @since 1.0.0
		 */
		public static $is_vendor_installed;

		/**
		 * Check if WPML is installed
		 *
		 * @var boolean
		 * @since 1.0.0
		 */
		public static $is_wpml_installed;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WAPO|YITH_WAPO_Premium
		 */
		public static function get_instance() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

			return ! is_null( $self::$instance ) ? $self::$instance : $self::$instance = new $self();
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->version = YITH_WAPO_VERSION;

			global $sitepress;
			self::$is_wpml_installed   = ! empty( $sitepress );
			self::$is_vendor_installed = function_exists( 'YITH_Vendors' );

			if ( self::$is_wpml_installed ) {
				$this->wpml = YITH_WAPO_WPML::get_instance();
			}

			// Load Plugin Framework.
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            add_action( 'admin_init', array( $this, 'manage_actions' ) );

			// Admin.
			if ( is_admin() && ( ! isset( $_REQUEST['action'] ) || ( isset( $_REQUEST['action'] ) && 'yith_load_product_quick_view' !== $_REQUEST['action'] ) ) ) {
				$this->admin = YITH_WAPO_Admin();
			}

			// Front.
			$is_ajax_request = defined( 'DOING_AJAX' ) && DOING_AJAX;
            
			if ( ! is_admin() || $is_ajax_request ) {
				$this->front = YITH_WAPO_Front();
				$this->cart  = YITH_WAPO_Cart();
			}

			// Common
			$this->db = YITH_WAPO_DB();

            // HPOS Compatibility
            add_action( 'before_woocommerce_init', array( $this, 'declare_wc_features_support' ) );

        }

		/**
		 * Load Plugin Framework
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

        public function manage_actions() {
            // Actions.
            $nonce  = ! function_exists( 'wp_verify_nonce' ) || isset( $_REQUEST['nonce'] )
                && ( wp_verify_nonce( sanitize_key( $_REQUEST['nonce'] ), 'wapo_action' ) || wp_verify_nonce( sanitize_key( $_REQUEST['nonce'] ), 'wapo_admin' ) );
            $action = sanitize_key( $_REQUEST['wapo_action'] ?? '' );

            $save_block_button = isset( $_REQUEST['save-block-button'] ) ? 1 : 0;

            if ( $action && $nonce ) {
                $block_id = sanitize_key( $_REQUEST['block_id'] ?? '' );
                $addon_id = sanitize_key( $_REQUEST['addon_id'] ?? '' );
                if ( 'save-block' === $action && $save_block_button ) {
                    $this->save_block( $_REQUEST );
                } elseif ( 'duplicate-block' === $action ) {
                    $this->duplicate_block( $block_id );
                } elseif ( 'remove-block' === $action ) {
                    $this->remove_block( $block_id );
                } elseif ( 'save-addon' === $action ) {
                    $this->save_addon( $_REQUEST );
                } elseif ( 'duplicate-addon' === $action ) {
                    $this->duplicate_addon( $block_id, $addon_id );
                } elseif ( 'remove-addon' === $action ) {
                    $this->remove_addon( $block_id, $addon_id );
                } elseif ( 'db-check' === $action ) {
                    $this->db_check();
                } elseif ( 'control_debug_options' === $action ) {
                    $this->control_debug_options();
                }
            }
        }

		/**
		 * Get HTML types
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function get_html_types() {
			$html_types = array(
				array(
					'slug' => 'html_heading',
                    // translators: [ADMIN] Add-on name
					'name' => __( 'Heading', 'yith-woocommerce-product-add-ons' ),
				),
				array(
					'slug' => 'html_text',
                    // translators: [ADMIN] Add-on name
                    'name' => __( 'Text', 'yith-woocommerce-product-add-ons' ),
				),
				array(
					'slug' => 'html_separator',
                    // translators: [ADMIN] Add-on name
                    'name' => __( 'Separator', 'yith-woocommerce-product-add-ons' ),
				),
			);
			return $html_types;
		}

		/**
		 * Get addon types
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function get_addon_types() {
			$addon_types = array(
				'checkbox' => array(
					'slug'  => 'checkbox',
                    // translators: [ADMIN] Add-on name
                    'name'  => __( 'Checkbox', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
                    'label' => __( 'Checkbox', 'yith-woocommerce-product-add-ons' ),
				),
				'radio' => array(
					'slug'  => 'radio',
                    // translators: [ADMIN] Add-on name
                    'name'  => __( 'Radio', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
                    'label' => __( 'Radio button', 'yith-woocommerce-product-add-ons' ),
				),
                'text' => array(
					'slug'  => 'text',
                    // translators: [ADMIN] Add-on name
                    'name'  => __( 'Input text', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
                    'label' => __( 'Input field', 'yith-woocommerce-product-add-ons' ),
				),
                'textarea' => array(
					'slug'  => 'textarea',
                    // translators: [ADMIN] Add-on name
					'name'  => __( 'Textarea', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
					'label' => __( 'Textarea', 'yith-woocommerce-product-add-ons' ),
				),
                'color' => array(
					'slug'  => 'color',
                    // translators: [ADMIN] Add-on name
					'name'  => __( 'Color swatch', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
					'label' => __( 'Color swatch', 'yith-woocommerce-product-add-ons' ),
				),
                'number' => array(
					'slug'  => 'number',
                    // translators: [ADMIN] Add-on name
					'name'  => __( 'Number', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
					'label' => __( 'Number', 'yith-woocommerce-product-add-ons' ),
				),
                'select' => array(
					'slug'  => 'select',
                    // translators: [ADMIN] Add-on name
					'name'  => __( 'Select', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
					'label' => __( 'Select item', 'yith-woocommerce-product-add-ons' ),
				),
                'label' => array(
					'slug'  => 'label',
                    // translators: [ADMIN] Add-on name
					'name'  => __( 'Label or image', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
					'label' => __( 'Label or image', 'yith-woocommerce-product-add-ons' ),
				),
                'product' => array(
					'slug'  => 'product',
                    // translators: [ADMIN] Add-on name
					'name'  => __( 'Product', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
					'label' => __( 'Product', 'yith-woocommerce-product-add-ons' ),
				),
                'date' => array(
					'slug'  => 'date',
                    // translators: [ADMIN] Add-on name
					'name'  => __( 'Date', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
					'label' => __( 'Date', 'yith-woocommerce-product-add-ons' ),
				),
                'file' => array(
					'slug'  => 'file',
                    // translators: [ADMIN] Add-on name
					'name'  => __( 'File upload', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
					'label' => __( 'File uploader', 'yith-woocommerce-product-add-ons' ),
				),
                'colorpicker' => array(
					'slug'  => 'colorpicker',
                    // translators: [ADMIN] Add-on name
					'name'  => __( 'Color picker', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
					'label' => __( 'Color picker', 'yith-woocommerce-product-add-ons' ),
				),
                'html_heading' => array(
                    'slug'  => 'html_heading',
                    // translators: [ADMIN] Add-on name
                    'name'  => __( 'HTML Heading', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
                    'label' => __( 'HTML Heading', 'yith-woocommerce-product-add-ons' ),
                ),
                'html_text' => array(
                    'slug'  => 'html_text',
                    // translators: [ADMIN] Add-on name
                    'name'  => __( 'HTML Text', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
                    'label' => __( 'HTML Text', 'yith-woocommerce-product-add-ons' ),
                ),
                'html_separator' => array(
                    'slug'  => 'html_separator',
                    // translators: [ADMIN] Add-on name
                    'name'  => __( 'HTML Separator', 'yith-woocommerce-product-add-ons' ),
                    // translators: [ADMIN] Add-on name (option)
                    'label' => __( 'HTML Separator', 'yith-woocommerce-product-add-ons' ),
                ),
			);
			return $addon_types;
		}

        /**
         * Get add-on label by slug
         *
         * @param string $slug The slug of the add-on.
         *
         * @return string
         * @since 4.0.0
         */
        public function get_addon_label_by_slug( $slug ) {

            if ( empty( $slug ) ) {
                return '';
            }

            $label       = '';
            $addon_types = $this->get_addon_types();

            if ( isset( $addon_types[$slug] ) && isset( $addon_types[$slug]['label'] ) ) {
                $label = $addon_types[$slug]['label'];
            }

            return $label;
        }

        /**
         * Get add-on name by slug
         *
         * @param string $slug The slug of the add-on.
         *
         * @return string
         * @since 4.0.0
         */
        public function get_addon_name_by_slug( $slug ) {

            if ( empty( $slug ) ) {
                return '';
            }

            $name       = '';
            $addon_types = $this->get_addon_types();

            if ( isset( $addon_types[$slug] ) && isset( $addon_types[$slug]['name'] ) ) {
                $name = $addon_types[$slug]['name'];
            }

            return $name;
        }

		/**
		 * Get available addon types
		 *
		 * @return array
		 * @since 2.0.0
		 */
		public function get_available_addon_types() {
			return array( 'checkbox', 'radio', 'text', 'select' );
		}

        /**
         * Calculate the price with the tax included if necessary.
         *
         * @param float $price The price added.
         *
         * @return float|int|mixed
         */
        public function calculate_price_depending_on_tax( $price = 0 ) {

            $price = yith_wapo_calculate_price_depending_on_tax( $price );

            return $price;

        }

        /**
         * Split addon_id and option_id depending on key and value. (Example: 24-0 - addon_id => 24, option_id => 0 )
         *
         * @param string $key The key.
         * @param string $value The value.
         *
         * @return array
         */
        public function split_addon_and_option_ids( $key, $value ) {

            $values = array();

            if ( ! is_array( $value ) ) {
                $value = stripslashes( $value );
            }
            $explode = explode( '-', $key );
            if ( isset( $explode[1] ) ) {
                $addon_id  = $explode[0];
                $option_id = $explode[1];
            } else {
                $addon_id  = $key;
                $option_id = $value;
            }

            $values['addon_id']  = $addon_id;
            $values['option_id'] = $option_id;

            return $values;
        }

		/**
		 * Save Block
		 *
		 * @param array $request Request array.
		 * @return mixed
		 */
		public function save_block( $request ) {
			global $wpdb;

            $block_id = $request['block_id'] ?? '';

			if ( isset( $request['block_id'] ) ) {

                $show_in             = isset( $request['block_rule_show_in'] ) ? $request['block_rule_show_in'] : 'all';
                $excluded_categories = isset( $request['block_rule_exclude_products_categories'] ) ? $request['block_rule_exclude_products_categories'] : '';
                $show_to             = isset( $request['block_rule_show_to'] ) ? $request['block_rule_show_to'] : 'all';
                $show_to_user_roles  = isset( $request['block_rule_show_to_user_roles'] ) ? $request['block_rule_show_to_user_roles'] : '';
                $show_to_membership  = isset( $request['block_rule_show_to_membership'] ) ? $request['block_rule_show_to_membership'] : '';

                if ( 'products' === $show_in ) {
                    $excluded_categories = '';
                }
                if ( 'user_roles' !== $show_to ) {
                    $show_to_user_roles = '';
                }
                if ( 'membership' !== $show_to ) {
                    $show_to_membership = '';
                }

				$rules = array(
					'show_in'                     => $show_in,
					'show_in_products'            => isset( $request['block_rule_show_in_products'] ) ? $request['block_rule_show_in_products'] : '',
					'show_in_categories'          => isset( $request['block_rule_show_in_categories'] ) ? $request['block_rule_show_in_categories'] : '',
					'exclude_products'            => isset( $request['block_rule_exclude_products'] ) ? $request['block_rule_exclude_products'] : '',
					'exclude_products_products'   => isset( $request['block_rule_exclude_products_products'] ) ? $request['block_rule_exclude_products_products'] : '',
					'exclude_products_categories' => $excluded_categories,
					'show_to'                     => $show_to,
					'show_to_user_roles'          => $show_to_user_roles,
					'show_to_membership'          => $show_to_membership,
				);

				$settings = array(
					'name'     => isset( $request['block_name'] ) ? $request['block_name'] : '',
					'priority' => isset( $request['block_priority'] ) ? $request['block_priority'] : 1,
					'rules'    => $rules,
				);

				$data = array(
					'settings'            => serialize( $settings ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
					'priority'            => isset( $request['block_priority'] ) ? $request['block_priority'] : 1,
					'visibility'          => isset( $request['block_visibility'] ) ? $request['block_visibility'] : 1,
                    'name'                => isset( $request['block_name'] ) ? $request['block_name'] : '',
                    'product_association' => isset( $request['block_rule_show_in'] ) ? $request['block_rule_show_in'] : 'all',
                    'exclude_products'    => isset( $request['block_rule_exclude_products'] ) ? wc_string_to_bool( $request['block_rule_exclude_products'] ) : 0,
                    'user_association'    => isset( $request['block_rule_show_to'] ) ? $request['block_rule_show_to'] : 'all',
                    'exclude_users'       => 0 //TODO: Change if exclude specific user is added to the plugin.
                );

                $show_in_products    = $rules['show_in_products'] ?? array();
                $exclude_products    = $rules['exclude_products_products'] ?? array();

                if ( is_array( $show_in_products ) ) {
                    // If it is a variable product, add all available variation ids to the array.
                    foreach( $show_in_products as $product_id ) {
                        $product = wc_get_product( $product_id );
                        if ( $product instanceof WC_Product_Variable ) {
                            $variations    = $product->get_available_variations();
                            $variations_ids = wp_list_pluck( $variations, 'variation_id' );

                            if ( ! empty( $variations_ids ) ) {
                                $show_in_products = array_merge( $show_in_products, $variations_ids );
                            }
                        }
                    }
                }

                if ( is_array( $exclude_products ) ) {
                    // If it is a variable product, add all available variation ids to the array.
                    foreach( $exclude_products as $product_id ) {
                        $product = wc_get_product( $product_id );
                        if ( $product instanceof WC_Product_Variable ) {
                            $variations    = $product->get_available_variations();
                            $variations_ids = wp_list_pluck( $variations, 'variation_id' );

                            if ( ! empty( $variations_ids ) ) {
                                $exclude_products = array_merge( $exclude_products, $variations_ids );
                            }
                        }
                    }
                }

                $show_in_categories  = $rules['show_in_categories'] ?? array();
                $exclude_categories  = $rules['exclude_products_categories'] ?? array();
                $user_roles          = $rules['show_to_user_roles'] ?? array();
                $memberships         = isset( $rules['show_to_membership'] ) && ! empty( $rules['show_to_membership'] ) ? (array) $rules['show_to_membership'] : array();

                $assoc_objects = array(
                    'product'           => $show_in_products,
                    'category'          => $show_in_categories,
                    'excluded_product'  => $exclude_products,
                    'excluded_category' => $exclude_categories,
                    'user_role'         => $user_roles,
                    'membership'        => $memberships
                );

				if ( isset( $request['block_user_id'] ) && $request['block_user_id'] > 0 ) {
					$data['user_id'] = sanitize_text_field( $request['block_user_id'] );
				}

				/** YITH Multi Vendor integration. */
				$vendor_id = '';

				// migration.
				if ( isset( $request['block_vendor_id'] ) ) {
					$vendor_id = sanitize_text_field( $request['block_vendor_id'] );
					// v2.
				} elseif ( isset( $request['vendor_id'] ) ) {
					$vendor_id = sanitize_text_field( $request['vendor_id'] );
				}
				$data['vendor_id'] = $vendor_id;

				$table = $wpdb->prefix . 'yith_wapo_blocks';

				if ( 'new' === $request['block_id'] ) {

					if ( ! isset( $request['block_priority'] ) || 0 === $request['block_priority'] ) {
						$new_priority = 0;
						// Get max priority value.
						$max_priority = $wpdb->get_var( "SELECT MAX(priority) FROM {$wpdb->prefix}yith_wapo_blocks" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
						// Get number of blocks.
						$res_priority = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}yith_wapo_blocks" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
						$total_blocks = $wpdb->num_rows;
						// New priority value.
						if ( $max_priority > 0 && $total_blocks > 0 ) {
							$new_priority = $max_priority > $total_blocks ? $max_priority : $total_blocks;
						}
						$data['priority'] = $new_priority + 1;
					}

					$wpdb->insert( $table, $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
					$block_id = $wpdb->insert_id;

				} elseif ( $request['block_id'] > 0 ) {
					$block_id = $request['block_id'];
					$wpdb->update( $table, $data, array( 'id' => $block_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				}

                if ( is_numeric( $block_id ) ) {
                    $this->set_associations( $block_id, $assoc_objects );
                }

				if ( isset( $request['add_options_after_save'] ) ) {
                    wp_safe_redirect(
                        add_query_arg(
                            array(
                                'page' => 'yith_wapo_panel',
                                'tab'  => 'blocks',
                                'block_id' => $block_id,
                                'addon_id' => 'new'
                            ),
                            admin_url( '/admin.php' )
                        )
                    );
				} elseif ( isset( $request['wapo_action'] ) && 'save-block' === $request['wapo_action'] ) {
                    wp_safe_redirect(
                        add_query_arg(
                            array(
                                'page' => 'yith_wapo_panel',
                                'tab'  => 'blocks',
                                'block_id' => $block_id
                            ),
                            admin_url( '/admin.php' )
                        )
                    );
				} else {
					return $block_id;
				}
			}

		}

        /**
         * Insert or update in the database the associations.
         *
         * @param $block_id
         * @param $associations_obj
         * @return void
         */
        public function set_associations( $block_id, $associations_obj ) {
            global $wpdb;

            $associations_table = $wpdb->prefix . 'yith_wapo_blocks_assoc';

            $wpdb->delete( $associations_table, array( 'rule_id' => $block_id ) );

            foreach ( $associations_obj as $object_type => $object_array ) {
                if ( ! empty( $object_array ) && is_array( $object_array ) ) {
                    foreach ( $object_array as $obj_item ) {
                        if( ! empty( $obj_item ) ) {
                            $association_data = array(
                                'rule_id' => $block_id,
                                'object' => $obj_item,
                                'type' => $object_type
                            );
                            $wpdb->insert(
                                $associations_table,
                                $association_data
                            );
                        }
                    }
                }
            }
        }

		/**
		 * Duplicate Block
		 *
		 * @param int $block_id Block ID.
		 * @return void
		 */
		public function duplicate_block( $block_id ) {
			global $wpdb;

			if ( $block_id > 0 ) {

                $blocks_table       = $wpdb->prefix . YITH_WAPO_DB()::YITH_WAPO_BLOCKS;
                $addons_table       = $wpdb->prefix . YITH_WAPO_DB()::YITH_WAPO_ADDONS;
                $associations_table = $wpdb->prefix . YITH_WAPO_DB()::YITH_WAPO_BLOCKS_ASSOCIATIONS;

				$query_block        = "SELECT * FROM $blocks_table WHERE id='$block_id'";
				$query_addons       = "SELECT * FROM $addons_table WHERE block_id='$block_id' ";
				$query_assoc        = "SELECT * FROM $associations_table WHERE rule_id='$block_id' ";

				$queried_block_row  = $wpdb->get_row( $query_block ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				$queried_addons_row = $wpdb->get_results( $query_addons ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				$queried_assoc_row  = $wpdb->get_results( $query_assoc ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

				if ( isset( $queried_block_row ) && $queried_block_row->id === $block_id ) {

					$block_data   = array(
                        'vendor_id'  => $queried_block_row->vendor_id,
						'settings'   => $queried_block_row->settings,
						'priority'   => $queried_block_row->priority,
						'visibility' => $queried_block_row->visibility,
					);

                    // From 4.0.0 exists these new columns.
                    if ( isset( $queried_block_row->name ) &&
                        isset( $queried_block_row->product_association ) &&
                        isset( $queried_block_row->exclude_products ) &&
                        isset( $queried_block_row->user_association ) &&
                        isset( $queried_block_row->exclude_users )
                    ) {
                        $block_data['name'] = $queried_block_row->name;
                        $block_data['product_association'] = $queried_block_row->product_association;
                        $block_data['exclude_products'] = $queried_block_row->exclude_products;
                        $block_data['user_association'] = $queried_block_row->user_association;
                        $block_data['exclude_users'] = $queried_block_row->exclude_users;
                    }

					$wpdb->insert( $blocks_table, $block_data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
					$block_id = $wpdb->insert_id;

                    foreach ( $queried_assoc_row as $assoc_row ) {
                        $assoc_data = array(
                            'rule_id' => $block_id,
                            'object' => $assoc_row->object,
                            'type' => $assoc_row->type,
                        );

                        $wpdb->insert( $associations_table, $assoc_data );
                    }

					$settings_addons_old = array();
					$addons_new_ids      = array();

					foreach ( $queried_addons_row as $addons_row ) {
						$addons_data = array(
							'block_id'   => $block_id,
							'settings'   => $addons_row->settings,
							'options'    => $addons_row->options,
							'priority'   => $addons_row->priority,
							'visibility' => $addons_row->visibility,
						);

						$wpdb->insert( $addons_table, $addons_data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
						$addon_id = $wpdb->insert_id;

						if ( $addon_id ) { // Sync conditional logics with new data.
							$settings                               = unserialize( $addons_data['settings'] );
							$settings_addons_old[ $addons_row->id ] = $settings; // Save setting default addon.
							$addons_new_ids[ $addons_row->id ]      = $addon_id; // Create an array pair  default_addon => clone addon.

						}
					}

					if ( ! empty( $addons_new_ids ) ) {

						foreach ( $addons_new_ids as $old_id => $new_id ) {

							$conditional_rule_addon_old = $settings_addons_old[ $old_id ]['conditional_rule_addon'];

							if ( is_array( $conditional_rule_addon_old ) ) {

								$conditional_rule_addon_new = array();

								foreach ( $conditional_rule_addon_old as $id ) {

									if ( ! empty( $id ) ) {

										$split_addon = explode( '-', $id );

										if ( $split_addon ) {
											if ( 'v' !== $split_addon[0] ) { // Prevent change variations.
												$split_addon[0]               = $addons_new_ids[ $split_addon[0] ] ?? ''; // change new addon_id.
												$new_value                    = implode( '-', $split_addon );
												$conditional_rule_addon_new[] = $new_value;
											} else {
												$conditional_rule_addon_new[] = $id;
											}
										} else { // Simple addon only switch the value.
											$conditional_rule_addon_new[] = $settings_addons_old[ $id ];
										}
									}
								}
								if ( ! empty( $conditional_rule_addon_new ) ) {

									$settings_addons_old[ $old_id ]['conditional_rule_addon'] = $conditional_rule_addon_new;
									$update_settings_values                                   = serialize( $settings_addons_old[ $old_id ] );
									$wpdb->update( $addons_table, array( 'settings' => $update_settings_values ), array( 'id' => $new_id ) );
								}
							}
						}
					}

					wp_safe_redirect(
                        add_query_arg(
                            array(
                                'page' => 'yith_wapo_panel'
                            ),
                            admin_url( '/admin.php' )
                        ),
                    );
				}
			}

		}

		/**
		 * Remove Block
		 *
		 * @param int $block_id Block ID.
		 * @return void
		 */
		public function remove_block( $block_id ) {
			global $wpdb;

			if ( $block_id > 0 ) {
                $blocks_table       = $wpdb->prefix . YITH_WAPO_DB()::YITH_WAPO_BLOCKS;
                $addons_table       = $wpdb->prefix . YITH_WAPO_DB()::YITH_WAPO_ADDONS;
                $associations_table = $wpdb->prefix . YITH_WAPO_DB()::YITH_WAPO_BLOCKS_ASSOCIATIONS;

				$wpdb->delete( $blocks_table, array( 'id' => $block_id ) );
				$wpdb->delete( $addons_table, array( 'block_id' => $block_id ) );
				$wpdb->delete( $associations_table, array( 'rule_id' => $block_id ) );

                wp_safe_redirect(
                    add_query_arg(
                        array(
                            'page' => 'yith_wapo_panel'
                        ),
                        admin_url( '/admin.php' )
                    )
                );
			}

		}

		/**
		 * Save Addon
		 *
		 * @param array  $request Request array.
		 * @param string $method String to know that it comes from migration method.
		 * @return mixed
		 */
		public function save_addon( $request, $method = '' ) {
			global $wpdb;

			if ( isset( $request['block_id'] ) && 'new' === $request['block_id'] ) {
                $temp_request['block_id']                      = 'new';
                $temp_request['block_name']                    = $_REQUEST['block_name'] ?? '';
                $temp_request['block_priority']                = $_REQUEST['block_priority'] ?? '';
                $temp_request['block_rule_show_in']            = $_REQUEST['block_rule_show_in'] ?? '';
                $temp_request['block_rule_show_in_products']   = isset( $_REQUEST['block_rule_show_in_products'] ) ? unserialize( base64_decode( $_REQUEST['block_rule_show_in_products'] ) ) : '';
                $temp_request['block_rule_show_in_categories'] = isset( $_REQUEST['block_rule_show_in_categories'] ) ? unserialize( base64_decode( $_REQUEST['block_rule_show_in_categories'] ) ) : '';
                $temp_request['block_rule_exclude_products']   = $_REQUEST['block_rule_exclude_products'] ?? '';
                $temp_request['block_rule_exclude_products_products']   = isset( $_REQUEST['block_rule_exclude_products_products'] ) ? unserialize( base64_decode( $_REQUEST['block_rule_exclude_products_products'] ) ) : '';
                $temp_request['block_rule_exclude_products_categories'] = isset( $_REQUEST['block_rule_exclude_products_categories'] ) ? unserialize( base64_decode( $_REQUEST['block_rule_exclude_products_categories'] ) ) : '';
                $temp_request['block_rule_show_to']                     = $_REQUEST['block_rule_show_to'] ?? '';
                $temp_request['block_rule_show_to_user_roles']          = isset( $_REQUEST['block_rule_show_to_user_roles'] ) ? unserialize( base64_decode( $_REQUEST['block_rule_show_to_user_roles'] ) ) : '';

                $request['block_id'] = $this->save_block( $temp_request );
			}

			if ( isset( $request['addon_id'] ) && isset( $request['block_id'] ) && $request['block_id'] > 0 ) {

				$conditional_logic = array();

				$settings = array(

					// General.
					'type'                         => $request['addon_type'] ?? '',

					// Display options.
					'title'                        => isset( $request['addon_title'] ) ? stripslashes( str_replace( '"', '&quot;', $request['addon_title'] ) ) : '',
					'title_in_cart'                => isset( $request['addon_title_in_cart'] ) ? stripslashes( str_replace( '"', '&quot;', $request['addon_title_in_cart'] ) ) : '',
					'title_in_cart_opt'            => isset( $request['addon_title_in_cart_opt'] ) ? stripslashes( str_replace( '"', '&quot;', $request['addon_title_in_cart_opt'] ) ) : '',
					'description'                  => isset( $request['addon_description'] ) ? stripslashes( $request['addon_description'] ) : '',
					'required'                     => $request['addon_required'] ?? '',
					'show_image'                   => $request['addon_show_image'] ?? '',
					'image'                        => $request['addon_image'] ?? '',
					'image_replacement'            => $request['addon_image_replacement'] ?? '',
					'options_images_position'      => $request['addon_options_images_position'] ?? '',
					'show_as_toggle'               => $request['addon_show_as_toggle'] ?? '',
					'hide_options_images'          => $request['addon_hide_options_images'] ?? '',
					'hide_options_label'           => $request['addon_hide_options_label'] ?? '',
					'hide_options_prices'          => $request['addon_hide_options_prices'] ?? '',
					'hide_products_prices'         => $request['addon_hide_products_prices'] ?? '',
					'show_add_to_cart'             => $request['addon_show_add_to_cart'] ?? '',
					'show_sku'                     => $request['addon_show_sku'] ?? '',
					'show_stock'                   => $request['addon_show_stock'] ?? '',
					'show_quantity'                => $request['addon_show_quantity'] ?? '',
					'show_in_a_grid'               => $request['addon_show_in_a_grid'] ?? '',
					'options_per_row'              => $request['addon_options_per_row'] ?? '',
					'options_width'                => $request['addon_options_width'] ?? '',
					'select_width'                 => $request['addon_select_width'] ?? '',
					// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
					// 'show_quantity_selector'	=> isset( $request['addon_show_quantity_selector'] )	? $request['addon_show_quantity_selector']	: '',

					// Style settings.
					'image_position'               => $request['addon_image_position'] ?? '',
					'label_content_align'          => $request['addon_label_content_align'] ?? '',
					'image_equal_height'           => $request['addon_image_equal_height'] ?? '',
					'images_height'                => $request['addon_images_height'] ?? '',
					'label_position'               => $request['addon_label_position'] ?? '',
					'label_padding'                => $request['addon_label_padding'] ?? '',
					'description_position'         => $request['addon_description_position'] ?? '',
					'product_out_of_stock'         => $request['addon_product_out_of_stock'] ?? '',

					// Conditional logic.
					'enable_rules'                 => $request['addon_enable_rules'] ?? '',
					'enable_rules_variations'      => isset( $request['addon_enable_rules_variations'] ) && isset( $request['addon_conditional_rule_variations'] ) ? $request['addon_enable_rules_variations'] : '',
					'conditional_logic_display'    => $request['addon_conditional_logic_display'] ?? '',
					'conditional_rule_variations'  => $request['addon_conditional_rule_variations'] ?? '',
					'conditional_set_conditions'   => $request['addon_conditional_set_conditions'] ?? '',
					'conditional_logic_display_if' => $request['addon_conditional_logic_display_if'] ?? '',
					'conditional_rule_addon'       => $request['addon_conditional_rule_addon'] ?? '',
					'conditional_rule_addon_is'    => $request['addon_conditional_rule_addon_is'] ?? '',

					// Advanced options.
					'first_options_selected'       => $request['addon_first_options_selected'] ?? '',
					'first_free_options'           => $request['addon_first_free_options'] ?? '',
					'selection_type'               => $request['addon_selection_type'] ?? '',
					'enable_min_max'               => $request['addon_enable_min_max'] ?? '',
					'min_max_rule'                 => $request['addon_min_max_rule'] ?? '',
					'min_max_value'                => $request['addon_min_max_value'] ?? '',
					'sell_individually'            => isset( $request['addon_sell_individually'] ) && 'yes' === $request['addon_sell_individually'] ? 'yes' : 'no', // Sell individually addon.

                    'enable_min_max_numbers'       => $request['addon_enable_min_max_numbers'] ?? '',
                    'numbers_min'                  => $request['addon_number_min'] ?? '',
                    'numbers_max'                  => $request['addon_number_max'] ?? '',


                    // HTML elements.
					'text_content'                 => isset( $request['option_text_content'] ) ? str_replace( '"', '&quot;', $request['option_text_content'] ) : '',
					'heading_text'                 => isset( $request['option_heading_text'] ) ? str_replace( '"', '&quot;', $request['option_heading_text'] ) : '',
					'heading_type'                 => $request['option_heading_type'] ?? '',
					'heading_color'                => $request['option_heading_color'] ?? '',
					'separator_style'              => $request['option_separator_style'] ?? '',
					'separator_width'              => $request['option_separator_width'] ?? '',
					'separator_size'               => $request['option_separator_size'] ?? '',
					'separator_color'              => $request['option_separator_color'] ?? '',

					// Rules.
					'conditional_logic'            => $conditional_logic,
				);

                $request = $this->stripslashes_recursive( $request );

				$request  = $this->save_addon_enable_value_formatted( $request );
				$settings = $this->save_formatted_settings( $settings );

				$data = array(
					'block_id'   => $request['block_id'],
					// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
					'settings'   => serialize( $settings ),
					// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
					'options'    => serialize( stripslashes_deep( $request['options'] ?? '' ) ),
					'visibility' => 1,
				);

				// addon_priority from migration process ( it should keep the same order ).
				if ( isset( $request['addon_priority'] ) ) {
					$data['priority'] = $request['addon_priority'];
				}

				$table = $wpdb->prefix . 'yith_wapo_addons';

				if ( 'new' === $request['addon_id'] || 'migration' === $method ) {
					if ( 'migration' === $method ) {
						$addon_id = $request['addon_id'];
						if ( $request['addon_id'] > 0 ) {
							$data['id'] = $addon_id;
							$wpdb->insert( $table, $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
						}
					} else {
						$wpdb->insert( $table, $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
						$addon_id = $wpdb->insert_id;

						// New priority value.
						$priority_data = array( 'priority' => $addon_id );
						$wpdb->update( $table, $priority_data, array( 'id' => $addon_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
					}
				} elseif ( $request['addon_id'] > 0 ) {
					$addon_id = $request['addon_id'];
					$wpdb->update( $table, $data, array( 'id' => $addon_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				}

				if ( self::$is_wpml_installed ) {
					YITH_WAPO_WPML::register_option_type( $settings['title'], $settings['description'], $data['options'], $settings['text_content'], $settings['heading_text'] );
				}

				if ( isset( $request['wapo_action'] ) && 'save-addon' === $request['wapo_action'] ) {

                    wp_safe_redirect(
                        add_query_arg(
                            array(
                                'page' => 'yith_wapo_panel',
                                'tab'  => 'blocks',
                                'block_id' => $request['block_id']
                            ),
                            admin_url( '/admin.php' )
                        )
                    );

				} else {
					return $addon_id;
				}
			}

			return false;

		}

		/**
		 * Duplicate Addon
		 *
		 * @param int $block_id Block ID.
		 * @param int $addon_id Addon ID.
		 * @return void
		 */
		public function duplicate_addon( $block_id, $addon_id ) {
			global $wpdb;

			if ( $addon_id > 0 ) {

				$query = "SELECT * FROM {$wpdb->prefix}yith_wapo_addons WHERE id='$addon_id'";
				$row   = $wpdb->get_row( $query ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

				$settings = unserialize( $row->settings );
				if ( isset( $settings['title'] ) ) {
					$settings['title'] = $settings['title'] . ' - ' . _x( 'Copy', '[ADMIN] String added to the add-on title when is duplicated', 'yith-woocommerce-product-add-ons' );
				}

				$settings = serialize( $settings );

				$data = array(
					'block_id'   => $row->block_id,
					'settings'   => $settings,
					'options'    => $row->options,
					'priority'   => $row->priority,
					'visibility' => $row->visibility,
				);

				$table = $wpdb->prefix . 'yith_wapo_addons';
				$wpdb->insert( $table, $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
				$addon_id = $wpdb->insert_id;

                wp_safe_redirect(
                    add_query_arg(
                        array(
                            'page'     => 'yith_wapo_panel',
                            'tab'      => 'blocks',
                            'block_id' => $block_id
                        ),
                        admin_url( '/admin.php' )
                    )
                );

			}

		}

		/**
		 * Remove Addon
		 *
		 * @param int $block_id Block ID.
		 * @param int $addon_id Addon ID.
		 * @return void
		 */
		public function remove_addon( $block_id, $addon_id ) {
			global $wpdb;

			if ( $addon_id > 0 ) {

				$wpdb->delete( $wpdb->prefix . 'yith_wapo_addons', array( 'id' => $addon_id ) );

                wp_safe_redirect(
                    add_query_arg(
                        array(
                            'page'     => 'yith_wapo_panel',
                            'tab'      => 'blocks',
                            'block_id' => $block_id
                        ),
                        admin_url( '/admin.php' )
                    )
                );
			}

		}

		/**
		 * Save addon attributes formatted.
		 *
		 * @param array $request The array of the request.
		 *
		 * @return mixed
		 */
		public function save_addon_enable_value_formatted( $request ) {

			$excluded_addon_types = array(
				'html_heading',
				'html_separator',
				'html_text',
			);

			if ( ! in_array( $request['addon_type'], $excluded_addon_types, true ) ) {
				$options      = &$request['options'];
				$addons_count = isset( $options['label'] ) ? count( $options['label'] ) : 0;

				for ( $i = 0; $i < $addons_count; $i ++ ) {
                    $options['label'][ $i ]         = isset( $options['label'][ $i ] ) && ! empty( $options['label'][ $i ] ) ? stripslashes( $options['label'][ $i ] ) : '';
                    $options['description'][ $i ]   = isset( $options['description'][ $i ] ) && ! empty( $options['description'][ $i ] ) ? stripslashes( $options['description'][ $i ] ) : '';
                    $options['addon_enabled'][ $i ] = isset( $options['addon_enabled'][ $i ] ) && 'yes' === $options['addon_enabled'][ $i ] ? 'yes' : 'no';
					$options['show_image'][ $i ]    = isset( $options['show_image'][ $i ] ) && 'yes' === $options['show_image'][ $i ] ? 'yes' : 'no';
					$options['default'][ $i ]       = isset( $options['default'][ $i ] ) && 1 === intval( $options['default'][ $i ] ) ? 'yes' : 'no';
					$options['label_in_cart'][ $i ] = isset( $options['label_in_cart'][ $i ] ) && 1 === intval( $options['label_in_cart'][ $i ] ) ? 'yes' : 'no';
					$options['price'][ $i ]         = isset( $options['price'][ $i ] ) ? trim( $options['price'][ $i ] ) : '';
					$options['price_sale'][ $i ]    = isset( $options['price_sale'][ $i ] ) ? trim( $options['price_sale'][ $i ] ) : '';
				}

            }
			return $request;
		}

        /**
         *
         * Recursive stripslashes for entire array ($variable)
         *
         * @param array|string $variable
         * @return mixed|string
         */
        private function stripslashes_recursive( $variable )
        {
            if ( is_string( $variable ) )
                return stripslashes( $variable );
            if ( is_array( $variable ) )
                foreach( $variable as $i => $value )
                    $variable[ $i ] = $this->stripslashes_recursive( $value ) ;

            return $variable;
        }

        /**
         * Save settings with right values.
         *
         * @param array $settings The array of settings.
         *
         * @return mixed
         */
        public function save_formatted_settings( $settings ) {

            $settings['title_in_cart'] = isset( $settings['title_in_cart'] ) && wc_string_to_bool( $settings['title_in_cart'] ) ? $settings['title_in_cart'] : 'no';
            $settings['text_content'] = isset( $settings['text_content'] ) ? html_entity_decode( stripslashes( $settings['text_content'] ) ) : '';

            return $settings;
        }


		/**
		 * Database check
		 *
		 * @return void
		 */
		public function db_check() {
			update_option( 'yith_wapo_db_version', '0' );
			wp_safe_redirect( admin_url( '/admin.php?page=yith_wapo_panel&tab=debug' ) );
		}

		/**
		 * Restart db options / Remove columns/ Remove tables
		 *
		 * @return void
		 */
		public function control_debug_options() {
			global $wpdb;

			$option = isset( $_REQUEST['option'] ) ? $_REQUEST['option'] : ''; //phpcs:ignore

			switch ( $option ) {
				case 'create_tables':
					YITH_WAPO_Install::get_instance()->create_tables();
					break;
				case 'db_options':
					delete_option( 'yith_wapo_db_update_scheduled_for' );
					delete_option( 'yith_wapo_db_version_option' );
					break;
				case 'remove_column':
					$wpdb->query("ALTER TABLE {$wpdb->prefix}yith_wapo_groups DROP IF EXISTS imported"); // phpcs:ignore
					$wpdb->query("ALTER TABLE {$wpdb->prefix}yith_wapo_types DROP IF EXISTS imported"); // phpcs:ignore
					break;
				case 'clear_tables':
					$wpdb->query( "DELETE FROM {$wpdb->prefix}yith_wapo_blocks" );
					$wpdb->query( "DELETE FROM {$wpdb->prefix}yith_wapo_addons" );
					break;
				case 'restore_addons':
					$wpdb->query( "INSERT INTO {$wpdb->prefix}yith_wapo_blocks SELECT * FROM {$wpdb->prefix}yith_wapo_blocks_backup" );
					$wpdb->query( "INSERT INTO {$wpdb->prefix}yith_wapo_addons SELECT * FROM {$wpdb->prefix}yith_wapo_addons_backup" );
					break;
				case 'remove_schedulers':
					$wpdb->query( "DELETE FROM {$wpdb->prefix}actionscheduler_actions WHERE hook = 'yith_wapo_run_update_callback'" );
					break;
                case 'rerun_v4_action':
                    update_option( 'yith_wapo_db_update_scheduled_for', '3.2.0' );
                    update_option( 'yith_wapo_db_version_option', '3.2.0' );
                    break;
			}

			wp_safe_redirect( admin_url( '/admin.php?page=yith_wapo_panel&tab=debug' ) );
		}

		/**
		 *  Is Quick View
		 *
		 *  @return bool
		 */
		private function is_quick_view() {
			$ajax   = defined( 'DOING_AJAX' ) && DOING_AJAX;
			$action = isset( $_REQUEST['action'] ) ? sanitize_key( $_REQUEST['action'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $ajax && ( 'yit_load_product_quick_view' === $action || 'yith_load_product_quick_view' === $action || 'ux_quickview' === $action );
		}

        /**
         * Declare support for WooCommerce features.
         */
        public function declare_wc_features_support() {
            if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', YITH_WAPO_INIT, true );
            }
        }

		/**
		 * Get Current MultiVendor
		 *
		 * @return null|YITH_Vendor
		 */
		public static function get_current_multivendor() {
			if ( self::$is_vendor_installed && is_user_logged_in() ) {
				$vendor = yith_get_vendor( 'current', 'user' );
				if ( $vendor->is_valid() ) {
					return $vendor;
				}
			}
			return null;
		}

		/**
		 * Get MultiVendor by ID
		 *
		 * @param int    $id ID.
		 * @param string $obj Obj.
		 * @return null|YITH_Vendor
		 */
		public static function get_multivendor_by_id( $id, $obj = 'vendor' ) {
			if ( self::$is_vendor_installed ) {
				$vendor = yith_get_vendor( $id, $obj );
				if ( $vendor->is_valid() ) {
					return $vendor;
				}
			}
			return null;
		}

		/**
		 * Is Plugin Enabled for Vendors
		 *
		 * @return bool
		 */
		public function is_plugin_enabled_for_vendors() {
			return get_option( 'yith_wpv_vendors_option_advanced_product_options_management' ) === 'yes';
		}
	}
}

/**
 * Unique access to instance of YITH_WAPO class
 *
 * @return YITH_WAPO|YITH_WAPO_Premium
 * @since 1.0.0
 */
function YITH_WAPO() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WAPO::get_instance();
}
