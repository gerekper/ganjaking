<?php
if ( !defined( 'ABSPATH' ) || !defined( 'YITH_WCPB_PREMIUM' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of FREE version of YITH WooCommerce Product Bundles
 *
 * @class   YITH_WCPB_Admin_Premium
 * @package YITH WooCommerce Product Bundles
 * @since   1.0.0
 * @author  Yithemes
 */

if ( !class_exists( 'YITH_WCPB_Admin_Premium' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCPB_Admin_Premium extends YITH_WCPB_Admin {

        /**
         * Single instance of the class
         *
         * @var YITH_WCPB_Admin_Premium
         * @since 1.0.0
         */
        protected static $_instance;

        public $bundle_product_version = '1.0.1';

        private $_bundled_order_items = false;

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        public function __construct() {
            parent::__construct();

            add_filter( 'yith_wcpb_settings_admin_tabs', array( $this, 'settings_premium_tabs' ) );
            add_filter( 'product_type_options', array( $this, 'product_type_options' ) );

            // register plugin to licence/update system
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            add_filter( 'woocommerce_reports_get_order_report_data_args', array( $this, 'woocommerce_reports_get_order_report_data_args' ) );

            /**
             * set the price for "per item pricing" bundles so price sorting works
             *
             * @since 1.1.7
             */
            add_action( 'woocommerce_process_product_meta_yith_bundle', array( $this, 'save_price_in_pip_bundles' ) );
            add_action( 'init', array( $this, 'sync_bundles' ) );

        }

        /**
         * set the price for "per item pricing" bundles so price sorting works
         *
         * @param $product_id
         * @since 1.1.7
         */
        public function save_price_in_pip_bundles( $product_id ) {
            /** @var WC_Product_Yith_Bundle $product */
            $product = wc_get_product( $product_id );
            if ( $product && $product->is_type( 'yith_bundle' ) ) {
                if ( $product->per_items_pricing ) {
                    $price = $product->get_per_item_price_tot();
                    $product->set_regular_price( $price );
                    $product->save();
                    update_post_meta( $product->get_id(), '_price', $price );
                }
            }
        }


        /**
         * Synchronize the bundle products
         *
         * @since 1.1.7
         */
        public function sync_bundles() {
            $current_bundle_product_version = get_option( 'yith_wcpb_bundle_product_version', '1.0.0' );
            $force_sync                     = isset( $_REQUEST[ 'yith_wcpb_force_sync_bundle_products' ] ) && isset( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'yith-wcpb-sync-pip-prices' );

            if ( $force_sync || version_compare( $this->bundle_product_version, $current_bundle_product_version, '>' ) ) {
                if ( $bundle_term = get_term_by( 'slug', 'yith_bundle', 'product_type' ) ) {
                    $product_ids = array_unique( (array) get_objects_in_term( $bundle_term->term_id, 'product_type' ) );
                    if ( sizeof( $product_ids ) > 0 ) {
                        foreach ( $product_ids as $product_id ) {
                            $this->save_price_in_pip_bundles( $product_id );
                        }
                    }
                }
                update_option( 'yith_wcpb_bundle_product_version', $this->bundle_product_version );

                if ( isset( $_REQUEST[ 'yith_wcpb_redirect' ] ) ) {
                    wp_safe_redirect( $_REQUEST[ 'yith_wcpb_redirect' ] );
                    exit;
                }
            }
        }

        /**
         * add Bundle Options Tab [in product wc-metabox]
         *
         * @access public
         * @since  1.0.24
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_product_data_tabs( $product_data_tabs ) {
            $product_data_tabs[ 'yith_bundled_items' ] = array(
                'label'  => __( 'Bundled Items', 'yith-woocommerce-product-bundles' ),
                'target' => 'yith_bundled_product_data',
                'class'  => array( 'show_if_bundle' ),
            );

            $product_data_tabs[ 'yith_bundled_options' ] = array(
                'label'  => __( 'Bundle Options', 'yith-woocommerce-product-bundles' ),
                'target' => 'yith_bundle_options',
                'class'  => array( 'show_if_bundle' ),
            );

            return $product_data_tabs;
        }

        /**
         * add panel for Bundle Options Tab [in product wc-metabox]
         *
         * @access public
         * @since  1.0.24
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_product_data_panels() {
            include YITH_WCPB_TEMPLATE_PATH . '/premium/admin/admin-bundle-options-tab.php';
        }

        /**
         * Hide/Show product in bundle in Reports count
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_reports_get_order_report_data_args( $args ) {
            $show = get_option( 'yith-wcpb-show-bundled-items-in-report' );
            if ( $show && $show == 'yes' )
                return $args;

            if ( isset( $args[ 'data' ][ '_qty' ] ) || isset( $args[ 'data' ][ '_line_total' ] ) ) {
                global $wpdb;
                $bundled_order_items_array = $this->get_bundled_order_item_ids();
                $bundled_order_items       = is_array( $bundled_order_items_array ) ? implode( ',', $bundled_order_items_array ) : '';
                if ( !!$bundled_order_items ) {
                    /*
                     * this NOT IN exclude products in bundle from selection
                     */
                    $not_in_bundled    = "NOT IN ($bundled_order_items) AND '1' =";
                    $args[ 'where' ][] = array(
                        'value'    => '1',
                        'key'      => 'order_items.order_item_id',
                        'operator' => $not_in_bundled
                    );
                }
            }

            return $args;
        }

        public function get_bundled_order_item_ids() {
            if ( $this->_bundled_order_items === false ) {
                global $wpdb;
                $query   = "SELECT oi.order_item_id FROM {$wpdb->prefix}posts AS posts LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi ON posts.ID = oi.order_id  INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta__b ON oi.order_item_id = order_item_meta__b.order_item_id AND order_item_meta__b.meta_key = '_bundled_by'";
                $results = $wpdb->get_results( $query );

                $this->_bundled_order_items = array();

                if ( !!$results ) {
                    foreach ( $results as $result ) {
                        if ( !empty( $result->order_item_id ) ) {
                            $this->_bundled_order_items[] = $result->order_item_id;
                        }
                    }
                }
            }

            return $this->_bundled_order_items;
        }


        /**
         * Hide meta in admin order
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_hidden_order_itemmeta( $hidden ) {
            return array_merge( $hidden, array(
                '_bundled_by',
                '_per_items_pricing',
                '_yith_bundle_cart_key',
                '_non_bundled_shipping',
                '_cartstamp',
                '_yith_wcpb_hidden'
            ) );
        }

        /**
         * Save Product Bandle Data
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_process_product_meta( $post_id ) {
            parent::woocommerce_process_product_meta( $post_id );

            $product = wc_get_product( $post_id );
            if ( !$product )
                return;

            // per items price
            $per_items_price = isset( $_POST[ '_yith_wcpb_per_item_pricing' ] ) ? 'yes' : 'no';
            yit_save_prop( $product, '_yith_wcpb_per_item_pricing', $per_items_price, true );

            // non bundled shipping
            $non_bundled_shipping = isset( $_POST[ '_yith_wcpb_non_bundled_shipping' ] ) ? 'yes' : 'no';
            yit_save_prop( $product, '_yith_wcpb_non_bundled_shipping', $non_bundled_shipping, true );

            /**
             * Advanced options
             *
             * @since 1.0.24
             */
            if ( isset( $_POST[ '_yith_wcpb_bundle_advanced_options' ] ) ) {
                $advanced_options = $_POST[ '_yith_wcpb_bundle_advanced_options' ];
                yit_save_prop( $product, '_yith_wcpb_bundle_advanced_options', $advanced_options, true );
            }
        }

        public function product_type_options( $options ) {

            $options[ 'yith_wcpb_per_item_pricing' ] = array(
                'id'            => '_yith_wcpb_per_item_pricing',
                'wrapper_class' => 'show_if_bundle',
                'label'         => __( 'Per Item Pricing', 'yith-woocommerce-product-bundles' ),
                'description'   => __( 'Check this option if you want your bundle priced per item, that is based on item prices and tax rates.', 'yith-woocommerce-product-bundles' ),
                'default'       => 'no'
            );

            $options[ 'yith_wcpb_non_bundled_shipping' ] = array(
                'id'            => '_yith_wcpb_non_bundled_shipping',
                'wrapper_class' => 'show_if_bundle',
                'label'         => __( 'Non-Bundled Shipping', 'yith-woocommerce-product-bundles' ),
                'description'   => __( 'Check this option if you would like that the bundle items will be shipped individually.', 'yith-woocommerce-product-bundles' ),
                'default'       => 'no'
            );

            return $options;
        }

        /**
         * bundle items data form
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function yith_wcpb_admin_product_bundle_data( $metabox_id, $product_id, $item_data, $post_id ) {
            global $filter_variations;
            if ( $b_prod = wc_get_product( $product_id ) ) {

                // free -> premium
                $default_quantity = 1;
                if ( !isset( $item_data[ 'bp_min_qty' ] ) && !isset( $item_data[ 'bp_max_qty' ] ) ) {
                    $bp_quantity      = isset( $item_data[ 'bp_quantity' ] ) ? $item_data[ 'bp_quantity' ] : 1;
                    $default_quantity = $bp_quantity;
                }

                $b_post              = get_post( $product_id );
                $default_title       = $b_post->post_title;
                $default_description = $b_post->post_excerpt;

                $bp_hide_item           = isset( $item_data[ 'bp_hide_item' ] ) ? true : false;
                $bp_hide_bundled_thumbs = isset( $item_data[ 'bp_hide_bundled_thumbs' ] ) ? true : false;
                $bp_min_qty             = isset( $item_data[ 'bp_min_qty' ] ) ? $item_data[ 'bp_min_qty' ] : $default_quantity;
                $bp_max_qty             = isset( $item_data[ 'bp_max_qty' ] ) ? $item_data[ 'bp_max_qty' ] : $default_quantity;
                $bp_title               = isset( $item_data[ 'bp_title' ] ) ? $item_data[ 'bp_title' ] : $default_title;
                $bp_description         = isset( $item_data[ 'bp_description' ] ) ? $item_data[ 'bp_description' ] : $default_description;
                $bp_optional            = isset( $item_data[ 'bp_optional' ] ) ? true : false;
                $bp_discount            = isset( $item_data[ 'bp_discount' ] ) ? $item_data[ 'bp_discount' ] : 0;

                $bp_filtered_variations = isset( $item_data[ 'bp_filtered_variations' ] ) ? $item_data[ 'bp_filtered_variations' ] : array();
                $default_attributes     = isset( $item_data[ 'bp_selection_overrides' ] ) ? $item_data[ 'bp_selection_overrides' ] : '';
                ?>

                <div class="options_group">
                    <?php if ( $b_prod->is_type( 'variable' ) ) { ?>
                        <p class="form-field">
                            <label><?php echo _ex( 'Filter Product Variations', 'Admin: filter variations of the bundled product if it is variable.', 'yith-woocommerce-product-bundles' ); ?></label>
                            <select multiple="multiple" name="_yith_wcpb_bundle_data[<?php echo $metabox_id; ?>][bp_filtered_variations][]" style="width: 95%;"
                                    data-placeholder="<?php _e( 'Choose variations&hellip;', 'yith-woocommerce-product-bundles' ); ?>" class="wc-enhanced-select"> <?php
                                $variations         = $b_prod->get_children();
                                $attribute_meta_key = $b_prod instanceof WC_Data ? '_attributes' : '_product_attributes';
                                $attributes         = maybe_unserialize( yit_get_prop( $b_prod, $attribute_meta_key, true ) );

                                $filtered_attributes = array();

                                foreach ( $variations as $variation ) {
                                    $description    = '';
                                    $variation_data = get_post_meta( $variation );

                                    foreach ( $attributes as $attribute ) {
                                        if ( !$attribute[ 'is_variation' ] )
                                            continue;

                                        $variation_selected_value = isset( $variation_data[ 'attribute_' . sanitize_title( $attribute[ 'name' ] ) ][ 0 ] ) ? $variation_data[ 'attribute_' . sanitize_title( $attribute[ 'name' ] ) ][ 0 ] : '';

                                        $description_name  = esc_html( wc_attribute_label( $attribute[ 'name' ] ) );
                                        $description_value = __( 'Any', 'woocommerce' ) . ' ' . $description_name;

                                        if ( $attribute[ 'is_taxonomy' ] ) {
                                            $post_terms = wp_get_post_terms( $product_id, $attribute[ 'name' ] );

                                            foreach ( $post_terms as $term ) {
                                                if ( $variation_selected_value == $term->slug ) {
                                                    $description_value = apply_filters( 'woocommerce_variation_option_name', esc_html( $term->name ) );
                                                }

                                                if ( $variation_selected_value == $term->slug || $variation_selected_value == '' ) {
                                                    if ( $filter_variations == 'yes' && is_array( $bp_filtered_variations ) && in_array( $variation, $bp_filtered_variations ) ) {
                                                        if ( !isset( $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
                                                            $filtered_attributes[ $attribute[ 'name' ] ] [] = $variation_selected_value;
                                                        } elseif ( !in_array( $variation_selected_value, $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
                                                            $filtered_attributes[ $attribute[ 'name' ] ] [] = $variation_selected_value;
                                                        }
                                                    }
                                                }
                                            }
                                        } else {
                                            $options = array_map( 'trim', explode( WC_DELIMITER, $attribute[ 'value' ] ) );

                                            foreach ( $options as $option ) {
                                                if ( sanitize_title( $variation_selected_value ) == sanitize_title( $option ) ) {
                                                    $description_value = esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) );
                                                }

                                                if ( sanitize_title( $variation_selected_value ) == sanitize_title( $option ) || $variation_selected_value == '' ) {
                                                    if ( $filter_variations == 'yes' && is_array( $bp_filtered_variations ) && in_array( $variation, $bp_filtered_variations ) ) {
                                                        if ( !isset( $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
                                                            $filtered_attributes[ $attribute[ 'name' ] ] [] = sanitize_title( $variation_selected_value );
                                                        } elseif ( !in_array( sanitize_title( $variation_selected_value ), $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
                                                            $filtered_attributes[ $attribute[ 'name' ] ] [] = sanitize_title( $variation_selected_value );
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $description .= $description_name . ': ' . $description_value . ', ';
                                    }

                                    if ( is_array( $bp_filtered_variations ) && in_array( $variation, $bp_filtered_variations ) ) {
                                        $selected = 'selected="selected"';
                                    } else {
                                        $selected = '';
                                    }
                                    echo '<option value="' . $variation . '" ' . $selected . '>#' . $variation . ' - ' . rtrim( $description, ', ' ) . '</option>';
                                }
                                ?></select>
                            <?php echo yith_wcpb_help_tip( __( 'Select the variations allowed for this item. To allow all variations leave it empty.', 'yith-woocommerce-product-bundles' ) ); ?>
                        </p>
                        <p class="form-field">
                            <label><?php echo _ex( 'Overwrite default selection', 'Admin: overwrite the default selection for attributes of variable product.', 'yith-woocommerce-product-bundles' ); ?></label>
                            <?php
                            foreach ( $attributes as $attribute ) {
                                if ( !$attribute[ 'is_variation' ] )
                                    continue;

                                $variation_selected_value = ( isset( $default_attributes[ sanitize_title( $attribute[ 'name' ] ) ] ) ) ? $default_attributes[ sanitize_title( $attribute[ 'name' ] ) ] : '';
                                echo '<select name="_yith_wcpb_bundle_data[' . $metabox_id . '][bp_selection_overrides][' . sanitize_title( $attribute[ 'name' ] ) . ']"><option value="">' . __( 'No default', 'woocommerce' ) . ' ' . wc_attribute_label( $attribute[ 'name' ] ) . '&hellip;</option>';

                                if ( $attribute[ 'is_taxonomy' ] ) {
                                    $post_terms = wp_get_post_terms( $product_id, $attribute[ 'name' ] );

                                    sort( $post_terms );
                                    foreach ( $post_terms as $term ) {
                                        if ( $filter_variations == 'yes' && isset( $filtered_attributes[ $attribute[ 'name' ] ] ) && !in_array( '', $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
                                            if ( !in_array( $term->slug, $filtered_attributes[ $attribute[ 'name' ] ] ) )
                                                continue;
                                        }
                                        echo '<option ' . selected( $variation_selected_value, $term->slug, false ) . ' value="' . esc_attr( $term->slug ) . '">' . apply_filters( 'woocommerce_variation_option_name', esc_html( $term->name ) ) . '</option>';
                                    }
                                } else {
                                    $options = array_map( 'trim', explode( WC_DELIMITER, $attribute[ 'value' ] ) );
                                    sort( $options );
                                    foreach ( $options as $option ) {
                                        if ( $filter_variations == 'yes' && isset( $filtered_attributes[ $attribute[ 'name' ] ] ) && !in_array( '', $filtered_attributes[ $attribute[ 'name' ] ] ) ) {
                                            if ( !in_array( $option, $filtered_attributes[ $attribute[ 'name' ] ] ) )
                                                continue;
                                        }
                                        // check also for sanitize_title for backward compatibility
                                        $_is_selected = $variation_selected_value === $option || sanitize_title( $variation_selected_value ) === sanitize_title( $option );
                                        echo '<option ' . selected( $_is_selected, true, false ) . ' value="' . esc_attr( $option ) . '">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
                                    }
                                }
                                echo '</select>';
                            }
                            ?>
                            <?php echo yith_wcpb_help_tip( __( 'Overwrite the default selection of the attribute for the variable product.', 'yith-woocommerce-product-bundles' ) ); ?>
                        </p>
                    <?php } ?>
                    <p class="form-field">
                        <label><?php echo _ex( 'Hide Product', 'Admin: hide item of the bundled product.', 'yith-woocommerce-product-bundles' ); ?></label>
                        <input type="checkbox" <?php checked( $bp_hide_item, true ); ?> name="_yith_wcpb_bundle_data[<?php echo $metabox_id; ?>][bp_hide_item]"
                               class="yith-wcpb-bp-hide-item yith-wcpb-bp">
                        <?php echo yith_wcpb_help_tip( __( 'Check this option if you would like to hide this product in the bundle.', 'yith-woocommerce-product-bundles' ) ); ?>
                    </p>
                    <p class="form-field">
                        <label><?php echo _ex( 'Hide thumbnail', 'Admin: hide the thumbnail of the bundled product.', 'yith-woocommerce-product-bundles' ); ?></label>
                        <input type="checkbox" <?php checked( $bp_hide_bundled_thumbs, true ); ?> name="_yith_wcpb_bundle_data[<?php echo $metabox_id; ?>][bp_hide_bundled_thumbs]"
                               class="yith-wcpb-bp-hide-bundled-thumbs yith-wcpb-bp">
                        <?php echo yith_wcpb_help_tip( __( 'Check this option if you would like to hide the thumbnail of this product.', 'yith-woocommerce-product-bundles' ) ); ?>
                    </p>
                    <p class="form-field">
                        <label><?php echo _ex( 'Optional', 'Admin: mark the bundled product as optional.', 'yith-woocommerce-product-bundles' ); ?></label>
                        <input type="checkbox" <?php checked( $bp_optional, true ); ?> name="_yith_wcpb_bundle_data[<?php echo $metabox_id; ?>][bp_optional]"
                               class="yith-wcpb-bp-optional yith-wcpb-bp">
                        <?php echo yith_wcpb_help_tip( __( 'Check this option if you would like to mark this product as optional.', 'yith-woocommerce-product-bundles' ) ); ?>
                    </p>
                    <p class="form-field">
                        <label><?php echo _ex( 'Min quantity', 'Admin: minimum quantity of the bundled product.', 'yith-woocommerce-product-bundles' ); ?></label>
                        <input type="number" min="0" name="_yith_wcpb_bundle_data[<?php echo $metabox_id; ?>][bp_min_qty]" class="yith-wcpb-bp-min-qty yith-wcpb-bp"
                               value="<?php echo $bp_min_qty; ?>">
                        <?php echo yith_wcpb_help_tip( __( 'Choose the minimum quantity for this product.', 'yith-woocommerce-product-bundles' ) ); ?>
                    </p>
                    <p class="form-field">
                        <label><?php echo _ex( 'Max quantity', 'Admin: maximum quantity of the bundled product.', 'yith-woocommerce-product-bundles' ); ?></label>
                        <input type="number" min="1" name="_yith_wcpb_bundle_data[<?php echo $metabox_id; ?>][bp_max_qty]" class="yith-wcpb-bp-max-qty yith-wcpb-bp"
                               value="<?php echo $bp_max_qty; ?>">
                        <?php echo yith_wcpb_help_tip( __( 'Choose the maximum quantity for this product.', 'yith-woocommerce-product-bundles' ) ); ?>
                    </p>
                    <p class="form-field">
                        <label><?php echo _ex( 'Discount %', 'Admin: discount for the bundled product.', 'yith-woocommerce-product-bundles' ); ?></label>
                        <input type="number" min="0" max="100" step="any" name="_yith_wcpb_bundle_data[<?php echo $metabox_id; ?>][bp_discount]" class="yith-wcpb-bp-discount yith-wcpb-bp"
                               value="<?php echo $bp_discount; ?>">
                        <?php echo yith_wcpb_help_tip( __( 'Choose the discount for this product. If a discount is applied on a bundled product on sale, its price will be the regular one discounted by the chosen percentage.', 'yith-woocommerce-product-bundles' ) ); ?>
                    </p>
                    <p class="form-field">
                        <label><?php echo _ex( 'Title', 'Admin: the title of the bundled product.', 'yith-woocommerce-product-bundles' ); ?></label>
                        <input type="text" name="_yith_wcpb_bundle_data[<?php echo $metabox_id; ?>][bp_title]" class="yith-wcpb-bp-name yith-wcpb-bp" value="<?php echo $bp_title; ?>">
                        <?php echo yith_wcpb_help_tip( __( 'Choose the title for this product.', 'yith-woocommerce-product-bundles' ) ); ?>
                    </p>
                    <p class="form-field">
                        <label><?php echo _ex( 'Description', 'Admin: the description of the bundled product.', 'yith-woocommerce-product-bundles' ); ?></label>
                        <textarea name="_yith_wcpb_bundle_data[<?php echo $metabox_id; ?>][bp_description]"
                                  class="yith-wcpb-bp-description yith-wcpb-bp"><?php echo $bp_description; ?></textarea>
                        <?php echo yith_wcpb_help_tip( __( 'Choose the description for this product.', 'yith-woocommerce-product-bundles' ) ); ?>
                    </p>
                </div> <!-- options_group -->
                <?php
            }
        }

        /**
         * Ajax Called in bundle_options_metabox.js
         * return the empty form for the item
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_product_in_bundle() {
            $metabox_id = intval( $_POST[ 'id' ] );
            $post_id    = intval( $_POST[ 'post_id' ] );
            $product_id = intval( $_POST[ 'product_id' ] );

            $title = get_the_title( $product_id );

            $product = wc_get_product( $product_id );

            $response = array();
            if ( $product->is_type( 'yith_bundle' ) ) {
                $response[ 'error' ] = __( 'You cannot add a bundle product', 'yith-woocommerce-product-bundles' );
                die();
            } else {
                ob_start();
                include YITH_WCPB_TEMPLATE_PATH . '/admin/admin-bundled-product-item.php';
                $response[ 'html' ] = ob_get_clean();
            }

            wp_send_json( $response );
        }

        /**
         * Add premium tabs
         *
         * @access public
         * @since  1.0.0
         */
        public function settings_premium_tabs( $tabs ) {
            unset( $tabs[ 'how-to' ] );
            unset( $tabs[ 'premium' ] );

            $tabs[ 'settings' ] = __( 'Settings', 'yith-woocommerce-product-bundles' );

            return $tabs;
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since 1.1.0
         */
        public function register_plugin_for_activation() {
            if ( function_exists( 'YIT_Plugin_Licence' ) ) {
                YIT_Plugin_Licence()->register( YITH_WCPB_INIT, YITH_WCPB_SECRET_KEY, YITH_WCPB_SLUG );
            }
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since 1.1.0
         */
        public function register_plugin_for_updates() {
            if ( function_exists( 'YIT_Upgrade' ) ) {
                YIT_Upgrade()->register( YITH_WCPB_SLUG, YITH_WCPB_INIT );
            }
        }
    }
}

/**
 * Unique access to instance of YITH_WCPB_Admin_Premium class
 *
 * @deprecated since 1.2.0 use YITH_WCPB_Admin() instead
 * @return YITH_WCPB_Admin_Premium
 * @since      1.0.0
 */
function YITH_WCPB_Admin_Premium() {
    return YITH_WCPB_Admin();
}