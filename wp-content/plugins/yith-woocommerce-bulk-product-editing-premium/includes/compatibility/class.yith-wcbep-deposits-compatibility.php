<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Deposits Compatibility Class
 *
 * @class   YITH_WCBEP_Deposits_Compatibility
 * @package Yithemes
 * @since   1.1.2
 * @author  Yithemes
 *
 */
class YITH_WCBEP_Deposits_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCBEP_Deposits_Compatibility
     */
    protected static $_instance;

    /**
     * @var array
     */
    public $badge_array;

    public $fields = array();

    public $slug = 'yith-wcdp';

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCBEP_Deposits_Compatibility
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
        $this->fields = $this->get_fields();
        add_filter( 'yith_wcbep_default_columns', array( $this, 'add_columns' ) );
        add_filter( 'yith_wcbep_manage_custom_columns', array( $this, 'manage_columns' ), 10, 3 );
        add_filter( 'yith_wcbep_variation_not_editable_and_empty', array( $this, 'edit_not_editable_and_empty_in_variations' ) );
        add_filter( 'yith_wcbep_td_extra_class_select', array( $this, 'add_extra_class_select_in_js' ) );

        add_action( 'yith_wcbep_update_product', array( $this, 'save_meta' ), 10, 4 );
        add_action( 'yith_wcbep_extra_general_bulk_fields', array( $this, 'add_extra_bulk_fields' ) );
        add_filter( 'yith_wcbep_extra_bulk_columns_select', array( $this, 'add_extra_bulk_columns_select' ) );
    }

    public function get_fields() {
        $fields = array(
            'yith_wcdp_enable_deposit'        => array(
                'key'    => '_enable_deposit',
                'name'   => __( 'Enable deposit', 'yith-wcdp' ),
                'values' => array(
                    'default' => __( 'Default', 'yith-wcdp' ),
                    'yes'     => __( 'Yes', 'yith-wcdp' ),
                    'no'      => __( 'No', 'yith-wcdp' ),
                ),
            ),
            'yith_wcdp_force_deposit'         => array(
                'key'    => '_force_deposit',
                'name'   => __( 'Force deposit', 'yith-wcdp' ),
                'values' => array(
                    'default' => __( 'Default', 'yith-wcdp' ),
                    'yes'     => __( 'Force deposit', 'yith-wcdp' ),
                    'no'      => __( 'Allow deposit', 'yith-wcdp' ),
                ),
            ),
            'yith_wcdp_create_balance_orders' => array(
                'key'    => '_create_balance_orders',
                'name'   => __( 'Create balance orders', 'yith-wcdp' ),
                'values' => array(
                    'default' => __( 'Default', 'yith-wcdp' ),
                    'yes'     => __( 'Let users pay the balance online', 'yith-wcdp' ),
                    'no'      => __( 'Customers will pay the balance using other means', 'yith-wcdp' ),
                ),
            ),
        );

        return $fields;
    }

    /**
     * @param $columns
     *
     * @return mixed
     */
    public function add_columns( $columns ) {

        foreach ( $this->fields as $field_id => $field ) {
            $columns[ $field_id ] = $field[ 'name' ];
        }

        return $columns;
    }

    /**
     * @param $value
     * @param $column_name
     * @param $post
     *
     * @return string
     */
    public function manage_columns( $value, $column_name, $post ) {
        if ( in_array( $column_name, array_keys( $this->fields ) ) ) {
            $current_field = $this->fields[ $column_name ];

            $this_meta = get_post_meta( $post->ID, $current_field[ 'key' ], true );

            $value = '<select class="yith-wcbep-editable-select">';

            foreach ( $current_field[ 'values' ] as $field_id => $field_value ) {
                $value .= '<option value="' . $field_id . '" ' . selected( $field_id == $this_meta, true, false ) . '>' . $field_value . '</option>';
            }

            $value .= '</select>';
            $value .= '<input type="hidden" class="yith-wcbep-hidden-select-value" value="' . $this_meta . '"/>';
        }

        return $value;
    }

    /**
     * @param $values
     *
     * @return array
     */
    public function edit_not_editable_and_empty_in_variations( $values ) {
        $values = array_merge( $values, array_keys( $this->fields ) );

        return $values;
    }

    /**
     * @param $values
     *
     * @return array
     */
    public function add_extra_bulk_columns_select( $values ) {
        $values = array_merge( $values, array_keys( $this->fields ) );

        return $values;
    }

    /**
     * @param $extra_classes
     *
     * @return array
     */
    public function add_extra_class_select_in_js( $extra_classes ) {
        foreach ( array_keys( $this->fields ) as $field_id ) {
            $extra_classes[] = 'td.' . $field_id;
        }

        return $extra_classes;
    }

    /**
     * @param $product
     * @param $matrix_keys
     * @param $single_modify
     */
    public function save_meta( $product, $matrix_keys, $single_modify, $is_variation ) {
        foreach ( $this->fields as $field_id => $field ) {
            $field_index = array_search( $field_id, $matrix_keys );
            if ( $field_index && !empty( $single_modify[ $field_index ] ) ) {
                if ( !$is_variation ) {
                    $new_value = $single_modify[ $field_index ];
                    yit_save_prop( $product, $field[ 'key' ], $new_value );
                }
            }
        }
    }

    public function add_extra_bulk_fields() {
        foreach ( $this->fields as $field_id => $field ) {
            ?>
            <tr>
                <td class="yith-wcbep-bulk-form-label-col">
                    <label><?php echo $field[ 'name' ] ?></label>
                </td>
                <td class="yith-wcbep-bulk-form-content-col">
                    <select id="yith-wcbep-<?php echo $field_id ?>-bulk-select" name="yith-wcbep-<?php echo $field_id ?>-bulk-select"
                            class="yith-wcbep-miniselect is_resetable">
                        <option value="skip"></option>
                        <?php
                        foreach ( $field[ 'values' ] as $key => $value ) {
                            ?>
                            <option value="<?php echo $key ?>"><?php echo $value ?></option> <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php
        }
    }
}

/**
 * Unique access to instance of YITH_WCBEP_Badge_Management_Compatibility class
 *
 * @return YITH_WCBEP_Deposits_Compatibility
 * @since 1.0.11
 */
function YITH_WCBEP_Deposits_Compatibility() {
    return YITH_WCBEP_Deposits_Compatibility::get_instance();
}