<?php
/**
 * Admin class
 *
 * @author  Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCPSC' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCPSC_Admin_Premium' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCPSC_Admin_Premium extends YITH_WCPSC_Admin {

        /**
         * Single instance of the class
         *
         * @var YITH_WCPSC_Admin_Premium
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        protected function __construct() {
            parent::__construct();
            YITH_WCPSC_Free_To_Premium_Importer::get_instance();

            // add Editor support to Size Chart Post Type
            add_action( 'init', array( $this, 'add_editor_to_size_charts' ) );

            // add Premium options for size charts
            add_filter( 'yith_wcpsc_tabs_metabox_chart_options', array( $this, 'add_chart_options_metabox' ) );

            // modify settings tab to add premium feature
            add_filter( 'yith_wcpsc_settings_admin_tabs', array( $this, 'premium_settings_tabs' ) );

            if ( current_user_can( 'edit_size_charts' ) ) {
                // add Metabox to Product
                add_action( 'init', array( $this, 'add_product_metabox' ), 16 );
                // Add shortcode button in TinyMCE
                add_action( 'init', array( $this, 'add_shortcode_btn_mce' ) );
                // Add quick edit for charts assigned to a product
                add_filter( 'manage_product_posts_columns', array( $this, 'add_columns' ) );
                add_action( 'manage_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
                add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_render' ), 10, 2 );
                add_action( 'save_post', array( $this, 'save_quick_edit' ) );
                add_action( 'bulk_edit_custom_box', array( $this, 'quick_edit_render' ), 10, 2 );
                add_action( 'wp_ajax_save_bulk_edit_product', array( $this, 'save_bulk_edit' ) );
            }

            add_action( 'wp_ajax_yith_wcpsc_get_shortcode', array( $this, 'get_shortcode_form' ) );

            add_action( 'widgets_init', array( $this, 'register_widgets' ) );

            add_action( 'woocommerce_admin_field_multiinput', array( $this, 'render_settings_multi_input' ) );

            /**
             * Duplicate Product Size Chart
             *
             * @since 1.1.1
             */
            add_action( 'admin_action_duplicate_product_size_chart', array( $this, 'duplicate_product_size_chart' ) );
            add_filter( 'post_row_actions', array( $this, 'add_duplicate_action_for_product_size_charts' ), 10, 2 );

            // register plugin to licence/update system
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );


            /** add shortcode metabox @since 1.1.3 */
            add_action( 'add_meta_boxes', array( $this, 'add_shortcode_meta_box' ) );
        }

        /**
         * Add the shortcode metabox
         *
         * @since 1.1.3
         */
        public function add_shortcode_meta_box() {
            add_meta_box( 'yith-wcpsc-shortcode-metabox', __( 'Shortcode', 'yith-product-size-charts-for-woocommerce' ), array( $this, 'print_shortcode_meta_box' ), 'yith-wcpsc-wc-chart', 'side', 'default' );
        }

        /**
         * Print the shortcode metabox
         *
         * @since 1.1.3
         * @param WP_Post $post
         */
        public function print_shortcode_meta_box( $post ) {
            echo "<input type='text' disabled value='[sizecharts id={$post->ID}]' />";
        }

        /**
         * Add Duplicate action link in Product Size Charts LIST
         *
         * @param array   $actions An array of row action links. Defaults are
         *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
         *                         'Delete Permanently', 'Preview', and 'View'.
         * @param WP_Post $post    The post object.
         * @since       1.1.1
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         * @return array
         */
        public function add_duplicate_action_for_product_size_charts( $actions, $post ) {
            if ( $post->post_type == 'yith-wcpsc-wc-chart' && $post->post_status == 'publish' ) {
                $args        = array(
                    'action' => 'duplicate_product_size_chart',
                    'id'     => $post->ID
                );
                $link        = add_query_arg( $args, admin_url() );
                $action_name = __( 'Duplicate', 'yith-product-size-charts-for-woocommerce' );

                $actions[ 'duplicate_product_size_chart' ] = "<a href='{$link}'>{$action_name}</a>";
            }

            return $actions;
        }

        /**
         * Do actions duplicate_product_size_chart
         *
         * @since       1.1.1
         * @author      Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function duplicate_product_size_chart() {
            if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] = 'duplicate_product_size_chart' && isset( $_REQUEST[ 'id' ] ) ) {
                $post_id = absint( $_REQUEST[ 'id' ] );
                $post    = get_post( $post_id );

                if ( !$post || $post->post_type != 'yith-wcpsc-wc-chart' )
                    return;

                $new_post = array(
                    'post_status'  => $post->post_status,
                    'post_type'    => 'yith-wcpsc-wc-chart',
                    'post_title'   => $post->post_title . ' - ' . __( 'Copy', 'yith-product-size-charts-for-woocommerce' ),
                    'post_content' => $post->post_content
                );

                $meta_to_save = array(
                    '_table_meta',
                    'display_as',
                    'show_in_widget',
                    'title_of_desc_tab',
                    'button_text',
                    'tab_priority',
                    'tab_title',
                );

                $new_post_id = wp_insert_post( $new_post );

                foreach ( $meta_to_save as $key ) {
                    $value = wp_slash( get_post_meta( $post_id, $key, true ) );
                    update_post_meta( $new_post_id, $key, $value );
                }

                $admin_edit_url = admin_url( 'edit.php?post_type=yith-wcpsc-wc-chart' );
                wp_redirect( $admin_edit_url );
            }
        }

        /**
         * add Metabox to Product
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_product_metabox() {
            $args      = array(
                'posts_per_page'   => -1,
                'post_type'        => 'yith-wcpsc-wc-chart',
                'post_status'      => 'publish',
                'orderby'          => 'title',
                'order'            => 'ASC',
                'fields'           => 'ids',
                'suppress_filters' => false,
            );
            $chart_ids = get_posts( $args );

            $charts_select_options = array();

            if ( $chart_ids ) {
                foreach ( $chart_ids as $chart_id ) {
                    $charts_select_options[ $chart_id ] = get_the_title( $chart_id );
                }
            }

            $args    = array(
                'label'    => _x( 'Product Size Charts', 'Admin:title of Metabox', 'yith-product-size-charts-for-woocommerce' ),
                'pages'    => 'product',
                'context'  => 'normal',
                'priority' => 'default',
                'tabs'     => apply_filters( 'yith_wcpsc_tabs_product_metabox_charts', array(
                    'settings' => array(
                        'label'  => _x( 'Product Size Charts', 'Admin:title of Metabox tab', 'yith-product-size-charts-for-woocommerce' ),
                        'fields' => array(
                            'yith_wcpsc_product_charts' => array(
                                'label'    => _x( 'Product Size Charts', 'Admin:title of Metabox field', 'yith-product-size-charts-for-woocommerce' ),
                                'desc'     => __( 'Select size charts you want show in this product', 'yith-product-size-charts-for-woocommerce' ),
                                'type'     => 'select',
                                'class'    => 'wc-enhanced-select',
                                'options'  => $charts_select_options,
                                'multiple' => true,
                                'private'  => false
                            )
                        )
                    )
                ) )
            );
            $metabox = YIT_Metabox( 'yith-wcpsc-product-size-charts-metabox' );
            $metabox->init( $args );
        }

        /**
         * add Editor support to Size Chart Post Type
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_editor_to_size_charts() {
            add_post_type_support( 'yith-wcpsc-wc-chart', 'editor' );
        }

        /**
         * add Premium options for size charts
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_chart_options_metabox( $tabs ) {
            $tabs[ 'settings' ][ 'fields' ] = array(
                'display_as'        => array(
                    'label'   => __( 'Display as', 'yith-product-size-charts-for-woocommerce' ),
                    'desc'    => __( 'Select how you want display the chosen Product Size Chart.', 'yith-product-size-charts-for-woocommerce' ),
                    'type'    => 'select',
                    'private' => false,
                    'options' => array(
                        'tab'          => __( 'Tab', 'yith-product-size-charts-for-woocommerce' ),
                        'popup'        => __( 'Popup', 'yith-product-size-charts-for-woocommerce' ),
                        'tabbed_popup' => __( 'Tabbed Popup', 'yith-product-size-charts-for-woocommerce' ),
                    ),
                    'std'     => 'tab'
                ),
                'show_in_widget'    => array(
                    'label'   => __( 'Show in widget', 'yith-product-size-charts-for-woocommerce' ),
                    'desc'    => __( 'Check it if you want to display the Product Size Chart in a Widget.', 'yith-product-size-charts-for-woocommerce' ),
                    'type'    => 'checkbox',
                    'private' => false,
                    'std'     => true
                ),
                'title_of_desc_tab' => array(
                    'label'   => __( 'Title of description tab', 'yith-product-size-charts-for-woocommerce' ),
                    'desc'    => __( 'Enter here the text you want display as title of the description tab of the selected Product Size Chart.', 'yith-product-size-charts-for-woocommerce' ),
                    'type'    => 'text',
                    'private' => false,
                    'std'     => __( 'Description', 'yith-product-size-charts-for-woocommerce' )
                ),
                'button_text'       => array(
                    'label'   => __( 'Button text', 'yith-product-size-charts-for-woocommerce' ),
                    'desc'    => __( 'Enter here the text you want display for the Product Size Chart button. Leave ematy to use the title.', 'yith-product-size-charts-for-woocommerce' ),
                    'type'    => 'text',
                    'private' => false,
                    'std'     => ''
                ),
                'tab_priority'      => array(
                    'label'   => __( 'Tab position', 'yith-product-size-charts-for-woocommerce' ),
                    'desc'    => __( 'Use this field to choose the position of the tab. Please consider the following WooCommerce priority order: 10 -> Description tab; 20 -> Additional Information tab; 30 -> Reviews tab', 'yith-product-size-charts-for-woocommerce' ),
                    'type'    => 'number',
                    'min'     => '0',
                    'private' => false,
                    'std'     => 99,
                ),
                'tab_title'         => array(
                    'label'   => __( 'Tab Title', 'yith-product-size-charts-for-woocommerce' ),
                    'desc'    => __( 'Choose the tab title. If not set, it will be the Size Chart title', 'yith-product-size-charts-for-woocommerce' ),
                    'type'    => 'text',
                    'private' => false,
                    'std'     => '',
                )
            );

            return $tabs;
        }

        /**
         * delete free tabs and add premium ones
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function premium_settings_tabs( $tabs ) {
            $premium_tabs = array(
                'settings'         => __( 'Settings', 'yith-product-size-charts-for-woocommerce' ),
                'advanced-display' => __( 'Advanced Display', 'yith-product-size-charts-for-woocommerce' ),
            );

            return $premium_tabs;
        }


        public function add_shortcode_btn_mce() {
            // Add js for tinymce button
            add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_plugin' ) );
            // Add button in tinymce
            add_filter( 'mce_buttons_2', array( $this, 'register_shortcode_button' ) );
        }

        /**
         * Add js for tinymce button
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_shortcode_plugin( $plugin_array ) {
            $plugin_array[ 'yith_wcpsc' ] = YITH_WCPSC_ASSETS_URL . '/js/shortcode-mce.js';

            return $plugin_array;
        }

        /**
         * Add button in tinymce
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function register_shortcode_button( $buttons ) {
            array_push( $buttons, 'add_size_chart' );

            return $buttons;
        }


        /**
         * Get shortcode form for tinymce button click
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function get_shortcode_form() {
            $args   = array(
                'posts_per_page' => -1,
                'post_type'      => 'yith-wcpsc-wc-chart',
                'post_status'    => 'publish',
                'orderby'        => 'title',
                'order'          => 'ASC',
                'fields'         => 'ids'
            );
            $charts = get_posts( $args );

            $charts_sel = array();

            echo '<div class="yith-wcpsc-shortcode-form-container">';
            echo '<p>' . __( 'Select the Product Size Chart you want to add', 'yith-product-size-charts-for-woocommerce' ) . '</p>';
            echo '<select class="yith-wcpsc-shortcode-select">';
            if ( $charts ) {
                foreach ( $charts as $chart_id ) {
                    $current_chart_title = get_the_title( $chart_id );
                    echo "<option value='{$chart_id}'>{$current_chart_title}</option>";
                }
            }
            echo '</select>';
            echo '<div class="yith-wcpsc-shortcode-form-buttons-container">';
            echo '<input type="button" class="yith-wcpsc-shortcode-add button button-primary button-large" value="' . __( 'Add', 'yith-product-size-charts-for-woocommerce' ) . '" />';
            echo '<input type="button" class="yith-wcpsc-shortcode-cancel button button-secondary button-large" value="' . __( 'Cancel', 'yith-product-size-charts-for-woocommerce' ) . '" />';

            echo '</div>';
            echo '</div>';

            die();
        }

        /**
         * Add column in product table list
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_columns( $columns ) {
            $columns[ 'yith_wcpsc_product_size_charts' ] = _x( 'Product Size Charts', 'Admin:title of column in products table', 'yith-product-size-charts-for-woocommerce' );

            return $columns;
        }

        /**
         * Add content in custom column in product table list
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function custom_columns( $column, $post_id ) {
            if ( $column == 'yith_wcpsc_product_size_charts' ) {
                $charts           = get_post_meta( $post_id, 'yith_wcpsc_product_charts', true );
                $charts           = !!$charts ? (array) $charts : array();
                $charts_array     = array();
                $charts_array_ids = array();
                $html_return      = '';
                foreach ( $charts as $chart_id ) {
                    $current_chart_title = get_the_title( $chart_id );
                    if ( $current_chart_title ) {
                        $charts_array[]     = $current_chart_title;
                        $charts_array_ids[] = $chart_id;
                    }
                }
                if ( !!$charts_array ) {
                    $html_return = implode( ', ', $charts_array );
                }
                $json_charts = json_encode( $charts_array_ids );
                $html_return .= "<input type=hidden class='yith-wcpsc-hidden' value='{$json_charts}'>";

                echo $html_return;
            }
        }

        /**
         * Add quick edit for charts assigned to a product
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function quick_edit_render( $column_name, $post_type ) {
            $enabled = apply_filters( 'yith_wcpsc_quick_bulk_edit_enabled', true );
            if ( $enabled && 'product' === $post_type && 'yith_wcpsc_product_size_charts' === $column_name ) {
                static $printNonce = true;
                if ( $printNonce ) {
                    $printNonce = false;
                    wp_nonce_field( YITH_WCPSC_INIT, 'charts_edit_nonce' );
                }
                switch ( $column_name ) {
                    case 'yith_wcpsc_product_size_charts':
                        ?>
                        <fieldset class="inline-edit-col-center">
                            <div class="inline-edit-col">
                                <span class="title inline-edit-charts-label"><?php _e( 'Product Size Charts', 'yith-product-size-charts-for-woocommerce' ); ?></span>
                                <ul class="charts-checklist product_charts-checklist cat-checklist product_cat-checklist">
                                    <?php
                                    $args   = array(
                                        'posts_per_page' => -1,
                                        'post_type'      => 'yith-wcpsc-wc-chart',
                                        'post_status'    => 'publish',
                                        'orderby'        => 'title',
                                        'order'          => 'ASC',
                                        'fields'         => 'ids'
                                    );
                                    $charts = get_posts( $args );
                                    if ( !!$charts ) {
                                        foreach ( $charts as $chart_id ) {
                                            $current_chart_title = get_the_title( $chart_id );
                                            echo "<li id='chart-{$chart_id}'><label class='selectit'><input value='{$chart_id}'
                                                                                   name='yith_wcpsc_product_charts[]'
                                                                                   id='in-chart-{$chart_id}'
                                                                                   type='checkbox'>{$current_chart_title}</label>";
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </fieldset>
                        <?php

                        break;
                }
            }
        }

        /**
         * Save charts for quick edit
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function save_quick_edit( $post_id ) {
            $slug = 'product';
            if ( isset( $_POST[ 'post_type' ] ) && $slug !== $_POST[ 'post_type' ] ) {
                return;
            }
            if ( !current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
            if ( !isset( $_POST[ "charts_edit_nonce" ] ) || !wp_verify_nonce( $_POST[ "charts_edit_nonce" ], YITH_WCPSC_INIT ) ) {
                return;
            }

            if ( isset( $_REQUEST[ 'yith_wcpsc_product_charts' ] ) ) {
                update_post_meta( $post_id, 'yith_wcpsc_product_charts', $_REQUEST[ 'yith_wcpsc_product_charts' ] );
            } else {
                update_post_meta( $post_id, 'yith_wcpsc_product_charts', false );
            }
        }

        /**
         * Save charts for bulk edit [AJAX]
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function save_bulk_edit() {
            $post_ids = ( !empty( $_POST[ 'post_ids' ] ) ) ? $_POST[ 'post_ids' ] : array();
            $charts   = ( !empty( $_POST[ 'yith_wcpsc_product_charts' ] ) ) ? $_POST[ 'yith_wcpsc_product_charts' ] : null;

            if ( !empty( $post_ids ) && is_array( $post_ids ) ) {
                foreach ( $post_ids as $post_id ) {
                    if ( !empty( $charts ) ) {
                        $old_charts = get_post_meta( $post_id, 'yith_wcpsc_product_charts', true );
                        $old_charts = !empty( $old_charts ) ? $old_charts : array();
                        $new_charts = array_merge( $old_charts, array_diff( $charts, $old_charts ) );
                        update_post_meta( $post_id, 'yith_wcpsc_product_charts', $new_charts );
                    }
                }
            }
            die();
        }


        public function render_settings_multi_input( $value ) {
            $type          = $value[ 'options' ][ 'input_type' ];
            $option_values = get_option( $value[ 'id' ], $value[ 'default' ] );
            $fields        = $value[ 'options' ][ 'fields' ];

            $field_description = WC_Admin_Settings::get_field_description( $value );
            extract( $field_description );

            // Custom attribute handling
            $custom_attributes = array();

            if ( !empty( $value[ 'custom_attributes' ] ) && is_array( $value[ 'custom_attributes' ] ) ) {
                foreach ( $value[ 'custom_attributes' ] as $attribute => $attribute_value ) {
                    $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
                }
            }

            if ( $type == 'color' ) {
                $type             = 'text';
                $value[ 'class' ] .= 'colorpick';
                $description      .= '<div id="colorPickerDiv_' . esc_attr( $value[ 'id' ] ) . '" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>';
            }

            ?>
            <tr valign="top">
            <th scope="row" class="titledesc">
                <label
                        for="<?php echo esc_attr( $value[ 'id' ] ); ?>"><?php echo esc_html( $value[ 'title' ] ); ?></label>
                <?php echo $tooltip_html; ?>
            </th>
            <td class="forminp forminp-<?php echo sanitize_title( $value[ 'type' ] ) ?>">
                <?php $loop = 0; ?>
                <?php if ( !empty( $fields ) ): ?>
                    <table class="yith-wcpsc-multi-input-table">
                        <tr>
                            <?php foreach ( $fields as $field ) : ?>
                                <td>
                                    <?php
                                    $option_value = isset( $option_values[ $loop ] ) ? $option_values[ $loop ] : '';
                                    $placeholder  = isset( $value[ 'placeholder' ][ $loop ] ) ? $value[ 'placeholder' ][ $loop ] : '';
                                    if ( 'color' == $type ) {
                                        echo '<span class="colorpickpreview" style="background: ' . esc_attr( $option_value ) . ';"></span>';
                                    }
                                    ?>
                                    <input
                                            name="<?php echo esc_attr( $value[ 'id' ] ); ?>[]"
                                            id="<?php echo esc_attr( $value[ 'id' ] ) . '-' . sanitize_title( $field ); ?>"
                                            type="<?php echo esc_attr( $type ); ?>"
                                            style="<?php echo esc_attr( $value[ 'css' ] ); ?>"
                                            value="<?php echo esc_attr( $option_value ); ?>"
                                            class="<?php echo esc_attr( $value[ 'class' ] ); ?>"
                                            placeholder="<?php echo esc_attr( $placeholder ); ?>"
                                        <?php echo implode( ' ', $custom_attributes ); ?>
                                    />
                                    <?php $loop++; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <?php foreach ( $fields as $field ) : ?>
                                <th><?php echo $field ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </table>
                <?php endif; ?>
                <?php echo $description; ?>
            </td>
            </tr><?php

        }

        /**
         * register Widget for Charts
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function register_widgets() {
            register_widget( 'YITH_WCPSC_Product_Size_Charts_Widget' );
        }

        public function admin_enqueue_scripts() {
            parent::admin_enqueue_scripts();
            wp_enqueue_script( 'jquery-blockui' );
            wp_enqueue_script( 'yith_wcpsc_popup_js', YITH_WCPSC_ASSETS_URL . '/js/yith_wcpsc_popup.js', array( 'jquery' ), '1.0.0', true );

            $screen = get_current_screen();

            $is_panel = strpos( $screen->id, '_page_yith_wcpsc_panel' ) > -1;
            if ( $is_panel ) {
                wp_enqueue_script( 'yith_wcpsc_panel_js', YITH_WCPSC_ASSETS_URL . '/js/panel.js', array( 'jquery', 'jquery-blockui' ), '1.0.0', true );
                wp_localize_script( 'yith_wcpsc_panel_js', 'yith_wcpsc_params', array(
                    'wc_3_0' => version_compare( WC()->version, '3.0.0', '>=' )
                ) );
            }

            if ( 'edit-product' === $screen->id ) {
                wp_enqueue_script( 'yith_wcpsc_admin_edit_js', YITH_WCPSC_ASSETS_URL . '/js/admin_edit.js', array( 'jquery' ), '1.0.0', true );
            }
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since 2.0.0
         */
        public function register_plugin_for_activation() {
            if ( function_exists( 'YIT_Plugin_Licence' ) ) {
                YIT_Plugin_Licence()->register( YITH_WCPSC_INIT, YITH_WCPSC_SECRET_KEY, YITH_WCPSC_SLUG );
            }
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since 2.0.0
         */
        public function register_plugin_for_updates() {
            if ( function_exists( 'YIT_Upgrade' ) ) {
                YIT_Upgrade()->register( YITH_WCPSC_SLUG, YITH_WCPSC_INIT );
            }
        }
    }
}

/**
 * Unique access to instance of YITH_WCPSC_Admin_Premium class
 *
 * @deprecated since 1.1.0 use YITH_WCPSC_Admin() instead
 * @return YITH_WCPSC_Admin_Premium
 * @since      1.0.0
 */
function YITH_WCPSC_Admin_Premium() {
    return YITH_WCPSC_Admin();
}