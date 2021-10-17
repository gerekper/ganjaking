<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Warranty_Install {

    public function __construct() {
        // update notice
        add_action( 'admin_print_styles', array( $this, 'add_notices' ) );

        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'admin_init', array( $this, 'check_version' ) );
        add_action( 'admin_init', array( $this, 'actions' ), 1  );
        register_activation_hook( __FILE__, array($this, 'install') );
    }

    /**
     * Register admin notices
     */
    public function add_notices() {
        if ( get_option( 'warranty_needs_update' ) == 1 ) {
            add_action( 'admin_notices', array( $this, 'install_notice' ) );
        }

        if ( !empty($_GET['warranty-data-updated']) ) {
            add_action( 'admin_notices', array( $this, 'updated_notice' ) );
        }
    }

    /**
     * Display a notice requiring a data update
     */
    public function install_notice() {
        // If we need to update, include a message with the update button
        if ( get_option( 'warranty_needs_update' ) == 1 ) {
            ?>
            <div id="message" class="updated">
                <p><?php _e( '<strong>WC Warranty Data Update Required</strong>', 'wc_warranty' ); ?></p>
                <p class="submit"><a href="<?php echo add_query_arg( 'warranty_update', 'true', admin_url( 'admin.php?page=warranties' ) ); ?>" class="warranty-update-now button-primary"><?php _e( 'Run the updater', 'wc_warranty' ); ?></a></p>
            </div>
            <script type="text/javascript">
	            jQuery( '.warranty-update-now' ).click( 'click', function() {
		            var answer = confirm( '<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'wc_warranty' ); ?>' );
		            return answer;
	            } );
            </script>
        <?php
        }
    }

    /**
     * Display a notice after the FUE data has been updated
     */
    public function updated_notice() {
        ?>
        <div id="message" class="updated">
            <p><?php _e('Data update have been successfully applied!', 'wc_warranty'); ?></p>
        </div>
    <?php
    }

    /**
     * Listens for button actions such as clicking on the 'Update Data' button
     */
    public function actions() {
        if ( ! empty( $_GET['warranty_update'] ) ) {
            $this->run_updates();

            // Update complete
            delete_option( 'warranty_needs_update' );

            wp_redirect( admin_url( 'index.php?page=warranties&warranty-updated=true' ) );
            exit;
        }

    }

    /**
     * Checks for changes in the version and prompt to update if necessary
     */
    public function check_version() {
        $db_version = get_option( 'warranty_db_version' );
        if ( ! defined( 'IFRAME_REQUEST' ) && $db_version != WooCommerce_Warranty::$db_version ) {
            $this->install();

            do_action( 'fue_updated' );
        }
    }

    /**
     * Register warranty_request post type and shop_warranty_status taxonomy
     */
    public function register_post_type() {
        global $wpdb;
        global $shipping_label_flag;  // Flag inserted in post_meta to determine the presence of a Shipping Label File
        $admin_only_query_var = ( is_admin() ) ? true : false;

        if (! taxonomy_exists( 'shop_warranty_status' ) ) {
            register_taxonomy( 'shop_warranty_status',
                array('warranty_request'),
                array(
                    'hierarchical'          => false,
                    'update_count_callback' => '_update_post_term_count',
                    'labels' => array(
                        'name'              => __( 'Warranty Statuses', 'wc_warranty'),
                        'singular_name'     => __( 'Warranty Status', 'wc_warranty'),
                        'search_items'      => __( 'Search Warranty Statuses', 'wc_warranty'),
                        'all_items'         => __( 'All Warranty Statuses', 'wc_warranty'),
                        'parent_item'       => __( 'Parent Warranty Status', 'wc_warranty'),
                        'parent_item_colon' => __( 'Parent Warranty Status:', 'wc_warranty'),
                        'edit_item'         => __( 'Edit Warranty Status', 'wc_warranty'),
                        'update_item'       => __( 'Update Warranty Status', 'wc_warranty'),
                        'add_new_item'      => __( 'Add New Warranty Status', 'wc_warranty'),
                        'new_item_name'     => __( 'New Warranty Status Name', 'wc_warranty')
                    ),
                    'show_ui'               => false,
                    'show_in_nav_menus'     => false,
                    'query_var'             => $admin_only_query_var,
                    'rewrite'               => false,
                )
            );
        }

        if (! post_type_exists( 'warranty_request' ) ) {
            register_post_type( 'warranty_request', array(
                    'label'                 => __('Warranty Requests', 'wc_warranty'),
                    'labels'                => array(
                        'name'              => __('Warranty Requests', 'wc_warranty'),
                        'singular_name'     => __('Warranty Request', 'wc_warranty'),
                        'all_items'         => __('All Requests', 'wc_warranty'),
                        'menu_name'         => __( 'Warranty', 'wc_warranty' ),
                        'not_found'         => __('No requests found', 'wc_warranty')
                    ),
                    'public'                => true,
                    'exclude_from_search'   => true,
                    'publicly_queryable'    => false,
                    'show_ui'               => false,
                    'capability_type'       => 'post',
                    'hierarchical'          => false,
                    'show_in_nav_menus'     => false,
                    'rewrite'               => false,
                    'query_var'             => true,
                    'supports'              => array( 'title', 'comments', 'custom-fields' ),
                    'has_archive'           => false,
                    'menu_icon'             => plugins_url( 'assets/images/icon-menu.png', __FILE__ )
                )
            );
        }

        /* Default data */
        $statuses = get_terms( 'shop_warranty_status', array('hide_empty' => false) );

        if ( empty($statuses) ) {
            $this->install_statuses();
        }

    }

    /**
     * Fired on plugin activation. Create the Warranty Request page if it doesn't exist
     */
    function install() {
        global $wp_roles;

        $db_version = get_option( 'warranty_db_version', 0 );

        // Add manage vendors cap to admins and shop managers
        if ( class_exists( 'WP_Roles' ) ) {
            if ( ! isset( $wp_roles ) ) {
                $wp_roles = new WP_Roles();
            }
        }

        if ( is_object( $wp_roles ) ) {
            $wp_roles->add_cap( 'shop_manager', 'manage_warranties' );
            $wp_roles->add_cap( 'administrator', 'manage_warranties' );
            $wp_roles->add_cap( 'wc_product_vendors_admin_vendor', 'manage_warranties' );
            $wp_roles->add_cap( 'wc_product_vendors_manager_vendor', 'manage_warranties' );
        }

        $this->create_warranty_request_page();
	    $this->create_options();
        $this->create_tables();

        if ( version_compare( $db_version, '20160506', '<' ) && null !== $db_version ) {
            update_option( 'warranty_needs_update', 1 );
        } else {
            update_option( 'warranty_db_version', WooCommerce_Warranty::$db_version );
        }

        $this->update_warranty_status_order();
        $this->migrate_warranty_form();

        flush_rewrite_rules();
    }

    /**
     * Run data updates from older versions of WC_Warranty
     */
    public function run_updates() {
        $db_version = get_option( 'warranty_db_version', 0 );

        if ( version_compare( $db_version, '1.1', '<' ) ) {
            include 'updates/update_1.1.php';
            update_option( 'warranty_db_version', '1.1' );
        }

        if ( version_compare( $db_version, '20160506', '<' ) ) {
            include 'updates/update_20160506.php';
            update_option( 'warranty_db_version', '20160506' );
        }
    }

    public function create_warranty_request_page() {

        if (! get_option('woocommerce_warranty_page_id') ) {
            // Create post object
            $parent_id = wc_get_page_id( 'myaccount' );

            if ( $parent_id ) {
                $_p = array();
                $_p['post_title'] = __('Request Warranty', 'wc_warranty');
                $_p['post_content'] = "[warranty_request]";
                $_p['post_status'] = 'publish';
                $_p['post_type'] = 'page';
                $_p['comment_status'] = 'closed';
                $_p['ping_status'] = 'closed';
                $_p['post_category'] = array(1);
                $_p['post_parent'] = $parent_id;

                // Insert the post into the database
                $page_id = wp_insert_post( $_p );

                update_option( 'woocommerce_warranty_page_id', $page_id );
            }

        }

        $page_id    = wc_get_page_id('warranty');
        $page       = get_post($page_id);

        if ($page_id == -1 || !$page) {
            // Create post object
            $parent_id = wc_get_page_id( 'myaccount' );

            if ( $parent_id ) {
                $_p = array();
                $_p['post_title'] = __('Request Warranty', 'wc_warranty');
                $_p['post_content'] = "[warranty_request]";
                $_p['post_status'] = 'publish';
                $_p['post_type'] = 'page';
                $_p['comment_status'] = 'closed';
                $_p['ping_status'] = 'closed';
                $_p['post_category'] = array(1);
                $_p['post_parent'] = $parent_id;

                // Insert the post into the database
                $page_id = wp_insert_post( $_p );

                update_option( 'woocommerce_warranty_page_id', $page_id );
            }
        }
    }

	public function create_options() {
		if ( false == get_option( 'warranty_show_rma_button', false ) ) {
			$options = array(
				'warranty_show_rma_button'              => 'yes',
				'warranty_show_tracking_field'          => 'yes',
				'warranty_request_order_note_statuses'  => array( 'new', 'processing', 'completed', 'rejected', 'revieweing' ),
				'warranty_returned_status'              => 'new',
				'warranty_button_text'                  => __( 'Request Warranty', 'wc_warranty' ),
				'view_warranty_button_text'             => __( 'View Warranty Status', 'wc_warranty' ),
				'warranty_reset_statuses'               => 'no',
				'warranty_enable_refund_requests'       => 'no',
				'warranty_enable_coupon_requests'       => 'no',
				'warranty_print_logo'                   => '',
				'warranty_print_url'                    => 'no',
				'warranty_rma_start'                    => 0,
				'warranty_rma_length'                   => 3,
				'warranty_rma_prefix'                   => '',
				'warranty_rma_suffix'                   => '',
			);

			foreach ( $options as $name => $value ) {
				update_option( $name, $value );
			}
		}
	}

    public function create_tables() {
        global $wpdb;

        $wpdb->hide_errors();

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            if ( ! empty($wpdb->charset ) ) {
                $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
            }
            if ( ! empty($wpdb->collate ) ) {
                $collate .= " COLLATE $wpdb->collate";
            }
        }

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $tables = "
        CREATE TABLE {$wpdb->prefix}wc_warranty_products (
          id bigint(20) NOT NULL AUTO_INCREMENT,
          request_id bigint(20) NOT NULL,
          product_id bigint(20) NOT NULL,
          order_item_index bigint(20) NOT NULL,
          quantity int(11) NOT NULL,
          KEY request_id (request_id),
          KEY product_id (product_id),
          KEY order_item_index (order_item_index),
          PRIMARY KEY  (id)
        ) $collate;";

        dbDelta( $tables );
    }

    private function install_statuses() {
        $warranty_status = apply_filters( 'wc_warranty_statuses', array(
            __('New', 'wc_warranty'),
            __('Reviewing', 'wc_warranty'),
            __('Processing', 'wc_warranty'),
            __('Completed', 'wc_warranty'),
            __('Rejected', 'wc_warranty')
        ));

        $default_slugs = array();

        foreach ( $warranty_status as $status ) {
            if ( ! get_term_by( 'name', $status, 'shop_warranty_status' ) ) {
                wp_insert_term( $status, 'shop_warranty_status' );

                $term = get_term_by( 'name', $status, 'shop_warranty_status' );
                $default_slugs[] = $term->slug;
            }
        }

        if (! empty($default_slugs) ) {
            update_option( 'wc_warranty_default_slugs', $default_slugs );
        }
    }

    private function update_warranty_status_order() {
        if (! get_option('wc_warranty_status_order', false) ) {
            $order  = apply_filters( 'wc_warranty_order_statuses', array(
                __('New', 'wc_warranty'),
                __('Processing', 'wc_warranty'),
                __('Completed', 'wc_warranty'),
                __('Rejected', 'wc_warranty')
            ));
            $option = array();
            foreach ( $order as $id => $status ) {
                $term = get_term_by( 'name', $status, 'shop_warranty_status' );
                $option[$id] = $term->slug;
            }

            update_option( 'wc_warranty_status_order', $option );
        }
    }

    private function migrate_warranty_form() {
        global $wpdb;

        if (false == get_option( 'wc_warranty_form_migrated', false ) ) {
            $used_keys  = array();
            $form       = array(
                'fields'    => array(),
                'inputs'    => array()
            );

            /* Warranty Reasons */
            do {
                $key = rand(100, 2147483647);
            } while ( in_array($key, $used_keys) );
            $used_keys[] = $key;

            $input = array(
                'key'   => $key,
                'type'  => 'select'
            );
            $field  = array(
                'name'      => __('Reason', 'wc_warranty'),
                'label'     => __('Select reason to request for warranty', 'wc_warranty'),
                'default'   => 'Defective Item',
                'options'   => get_option('warranty_reason', 'Defective Item')
            );

            $form['inputs'][] = $input;
            $form['fields'][$key] = $field;

            $wpdb->query(
                "UPDATE {$wpdb->prefix}postmeta
                    SET meta_key = '_field_". $key ."'
                    WHERE meta_key = '_reason'"
            );

            /* Warranty Question */
            do {
                $key = rand(100, 2147483647);
            } while ( in_array($key, $used_keys) );
            $used_keys[] = $key;

            $question = get_option( 'warranty_question', false );

            if ( $question ) {

                $input = array(
                    'key'   => $key,
                    'type'  => 'textarea'
                );
                $field  = array(
                    'name'      => __('Description', 'wc_warranty'),
                    'label'     => get_option( 'warranty_question', 'Describe the problem' ),
                    'default'   => '',
                    'required'  => 'yes',
                    'rows'      => 5,
                    'cols'      => 50
                );

                $form['inputs'][] = $input;
                $form['fields'][$key] = $field;
            }

            $wpdb->query(
                "UPDATE {$wpdb->prefix}postmeta
                    SET meta_key = '_field_". $key ."'
                    WHERE meta_key = '_answer'"
            );

            /* Warranty File Upload */
            do {
                $key = rand(100, 2147483647);
            } while ( in_array($key, $used_keys) );
            $used_keys[] = $key;

            $upload_file    = get_option( 'warranty_upload', 'no' );
            $required       = get_option( 'warranty_require_upload', 'no' );

            if ( $upload_file == 'yes' ) {
                $input = array(
                    'key'   => $key,
                    'type'  => 'file'
                );
                $field  = array(
                    'name'      => __('Attachment', 'wc_warranty'),
                    'label'     => get_option('warranty_upload_title', 'Attach File'),
                    'default'   => '',
                    'required'  => $required
                );

                $form['inputs'][] = $input;
                $form['fields'][$key] = $field;
            }

            $wpdb->query(
                "UPDATE {$wpdb->prefix}postmeta
                    SET meta_key = '_field_". $key ."'
                    WHERE meta_key = '_attachment'"
            );

            $form['inputs'] = wp_json_encode( $form['inputs'] );
            update_option('warranty_form', $form);

            update_option('wc_warranty_form_migrated', true);
        }
    }

}

new Warranty_Install();
