<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Custom Fields Manager
 *
 * @class   YITH_WCBEP_Custom_Fields_Manager
 * @package Yithemes
 * @since   1.1.2
 * @author  Yithemes
 */
class YITH_WCBEP_Custom_Fields_Manager {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCBEP_Custom_Fields_Manager
     */
    protected static $_instance;

    /**
     * @var string
     */
    public $prefix = 'yith_wcbep_cf_';

    public $custom_fields = array();

    protected $allow_variations = false;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCBEP_Custom_Fields_Manager
     */
    public static function get_instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Constructor
     */
    protected function __construct() {

        $this->custom_fields    = self::get_custom_fields();
        $this->allow_variations = apply_filters( 'yith_wcbep_allow_editing_custom_fields_in_variations', false );

        // Add Tab for custom field Settings
        add_filter( 'yith_wcbep_settings_admin_tabs', array( $this, 'add_custom_field_tab' ) );
        add_action( 'yith_wcbep_custom_fields_tab', array( $this, 'render_custom_fields_tab' ) );
        add_action( 'wp_ajax_yith_wcbep_save_custom_fields', array( $this, 'ajax_save_custom_fields' ) );
        add_action( 'admin_init', array( $this, 'action_save_custom_fields' ) );

        add_filter( 'yith_wcbep_default_columns', array( $this, 'add_columns' ) );
        add_filter( 'yith_wcbep_manage_custom_columns', array( $this, 'manage_columns' ), 10, 3 );
        if ( !$this->allow_variations ) {
            add_filter( 'yith_wcbep_variation_not_editable_and_empty', array( $this, 'edit_not_editable_and_empty_in_variations' ) );
        }
        add_filter( 'yith_wcbep_td_extra_class_text', array( $this, 'add_extra_class_text_in_js' ) );

        add_action( 'yith_wcbep_update_product', array( $this, 'save_meta' ), 10, 4 );
        add_action( 'yith_wcbep_extra_bulk_custom_fields', array( $this, 'add_extra_bulk_fields' ) );
        add_filter( 'yith_wcbep_extra_bulk_columns_text', array( $this, 'add_extra_bulk_columns_text' ) );
    }

    public function add_custom_field_tab( $tabs ) {
        $tabs[ 'custom-fields' ] = __( 'Custom Fields', 'yith-woocommerce-bulk-product-editing' );

        return $tabs;
    }

    public function render_custom_fields_tab() {
        wc_get_template( 'custom-fields-tab.php', array(), '', YITH_WCBEP_TEMPLATE_PATH . '/premium/panel/' );
    }

    public function action_save_custom_fields() {
        if ( empty( $_POST[ 'yith_wcbep_nonce' ] ) || !wp_verify_nonce( $_POST[ 'yith_wcbep_nonce' ], 'yith_wcbep_save_custom_fields' ) ) {
            return;
        }

        if ( isset( $_POST[ 'yith-wcbep-custom-field' ] ) ) {
            $custom_fields = $_POST[ 'yith-wcbep-custom-field' ];
            self::save_custom_fields( $custom_fields );
        } else {
            self::save_custom_fields( array() );
        }
    }

    public function ajax_save_custom_fields() {
        if ( isset( $_POST[ 'custom_fields' ] ) ) {
            $custom_fields = $_POST[ 'custom_fields' ];

            if ( !!$custom_fields && is_array( $custom_fields ) ) {
                $custom_fields = array_map( 'sanitize_text_field', $custom_fields );
            } else {
                $custom_fields = array();
            }

            self::save_custom_fields( $custom_fields );
        } else {
            self::save_custom_fields( array() );
        }
        die();
    }

    public function create_field_name( $name ) {
        $name = $this->prefix . str_replace( '-', '_', sanitize_title( $name ) );

        return $name;
    }

    /**
     * @param $columns
     * @return mixed
     */
    public function add_columns( $columns ) {
        foreach ( $this->custom_fields as $field ) {
            $field_name             = $this->create_field_name( $field );
            $columns[ $field_name ] = $field;
        }

        return $columns;
    }

    /**
     * @param $value
     * @param $column_name
     * @param $post
     * @return mixed
     */
    public function manage_columns( $value, $column_name, $post ) {
        foreach ( $this->custom_fields as $field ) {
            $field_name = $this->create_field_name( $field );
            if ( $column_name === $field_name ) {
                $value = get_post_meta( $post->ID, $field, true );
                if ( is_array( $value ) ) {
                    $value = implode( '|', $value );
                }
            }
        }

        return $value;
    }

    /**
     * @param $values
     * @return array
     */
    public function edit_not_editable_and_empty_in_variations( $values ) {
        foreach ( $this->custom_fields as $field ) {
            $field_name = $this->create_field_name( $field );
            $values[]   = $field_name;
        }

        return $values;
    }

    /**
     * @param $values
     * @return array
     */
    public function add_extra_bulk_columns_text( $values ) {
        foreach ( $this->custom_fields as $field ) {
            $field_name = $this->create_field_name( $field );
            $values[]   = $field_name;
        }

        return $values;
    }

    /**
     * @param $extra_classes
     * @return array
     */
    public function add_extra_class_text_in_js( $extra_classes ) {
        foreach ( $this->custom_fields as $field ) {
            $field_name      = $this->create_field_name( $field );
            $extra_classes[] = $field_name;
        }

        return $extra_classes;
    }

    /**
     * @param $product
     * @param $matrix_keys
     * @param $single_modify
     */
    public function save_meta( $product, $matrix_keys, $single_modify, $is_variation ) {
        foreach ( $this->custom_fields as $field ) {
            $field_name = $this->create_field_name( $field );
            $index      = array_search( $field_name, $matrix_keys );
            if ( isset( $single_modify[ $index ] ) && ( $this->allow_variations || !$is_variation ) ) {
                $value       = $single_modify[ $index ];
                $custom_hook = "yith_wcbep_save_custom_field_{$field}";
                if ( has_action( $custom_hook ) ) {
                    do_action( $custom_hook, $product, $value );
                } else {
                    $prev_value = get_post_meta( $product->get_id(), $field, true );
                    if ( is_array( $prev_value ) ) {
                        $value = !!$value ? explode( '|', $value ) : array();
                    }
                    update_post_meta( $product->get_id(), $field, $value );
                }
            }
        }
    }

    public function add_extra_bulk_fields() {
        foreach ( $this->custom_fields as $field ) {
            $field_name = $this->create_field_name( $field );
            $values[]   = $field_name;
            ?>
            <tr>
                <td class="yith-wcbep-bulk-form-label-col">
                    <label><?php echo $field; ?></label>
                </td>
                <td class="yith-wcbep-bulk-form-content-col">
                    <select id="yith-wcbep-<?php echo $field_name ?>-bulk-select" name="yith-wcbep-<?php echo $field_name ?>-bulk-select"
                            class="yith-wcbep-miniselect is_resetable">
                        <option value="new"><?php _e( 'Set new', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                        <option value="pre"><?php _e( 'Prepend', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                        <option value="app"><?php _e( 'Append', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                        <option value="rep"><?php _e( 'Replace', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                        <option value="del"><?php _e( 'Delete', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                    </select>
                    <input type="text" id="yith-wcbep-<?php echo $field_name ?>-bulk-value" name="yith-wcbep-<?php echo $field_name ?>-bulk-value"
                           class="yith-wcbep-minifield is_resetable">
                    <input type="text" id="yith-wcbep-<?php echo $field_name ?>-bulk-replace" name="yith-wcbep-<?php echo $field_name ?>-bulk-replace"
                           class="yith_wcbep_no_display yith-wcbep-minifield is_resetable" placeholder="With">

                </td>
            </tr>
            <?php
        }
    }

    public static function get_custom_fields() {
        return get_option( 'yith_wcbep_custom_fields', array() );
    }

    public static function save_custom_fields( $fields ) {

        if ( !!$fields && is_array( $fields ) ) {
            $fields = array_map( 'sanitize_text_field', array_filter( $fields ) );
        } else {
            $fields = array();
        }

        return update_option( 'yith_wcbep_custom_fields', $fields );
    }
}

/**
 * Unique access to instance of YITH_WCBEP_Badge_Management_Compatibility class
 *
 * @return YITH_WCBEP_Custom_Fields_Manager
 * @since 1.0.11
 */
function YITH_WCBEP_Custom_Fields_Manager() {
    return YITH_WCBEP_Custom_Fields_Manager::get_instance();
}