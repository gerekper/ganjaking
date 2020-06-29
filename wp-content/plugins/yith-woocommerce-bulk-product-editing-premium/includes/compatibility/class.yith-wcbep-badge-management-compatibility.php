<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * WPML Compatibility Class
 *
 * @class   YITH_WCBEP_Badge_Management_Compatibility
 * @package Yithemes
 * @since   1.1.2
 * @author  Yithemes
 *
 */
class YITH_WCBEP_Badge_Management_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCBEP_Badge_Management_Compatibility
     */
    protected static $_instance;

    /**
     * @var array
     */
    public $badge_array;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCBEP_Badge_Management_Compatibility
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
        add_filter( 'yith_wcbep_default_columns', array( $this, 'add_badge_column' ) );
        add_filter( 'yith_wcbep_manage_custom_columns', array( $this, 'manage_badge_column' ), 10, 3 );
        add_filter( 'yith_wcbep_variation_not_editable_and_empty', array( $this, 'add_badge_class' ) );

        add_action( 'yith_wcbep_update_product', array( $this, 'save_badge_meta' ), 10, 4 );

        add_filter( 'yith_wcbep_extra_obj_class_chosen', array( $this, 'add_badge_class' ) );
        add_action( 'yith_wcbep_extra_custom_input', array( $this, 'extra_custom_input' ) );

        add_action( 'yith_wcbep_extra_general_bulk_fields', array( $this, 'add_extra_bulk_field' ) );
        add_filter( 'yith_wcbep_extra_bulk_columns_chosen', array( $this, 'add_badge_class' ) );

    }

    /**
     * @param $columns
     *
     * @return mixed
     */
    public function add_badge_column( $columns ) {
        $columns[ 'yith_wcbm_badge' ] = __( 'Badge', 'yith-woocommerce-badge-management' );

        return $columns;
    }

    /**
     * @param $value
     * @param $column_name
     * @param $post
     *
     * @return string
     */
    public function manage_badge_column( $value, $column_name, $post ) {
        if ( $column_name == 'yith_wcbm_badge' ) {
            $badge_info   = yith_wcbm_get_product_badge_info( $post->ID );
            $badge_ids    = array_filter( array_map( 'absint', $badge_info[ 'badge_ids' ] ) );
            $badge_titles = !!$badge_ids ? implode( ', ', array_map( 'get_the_title', $badge_ids ) ) : '';

            $value = '<div class="yith-wcbep-select-values">' . $badge_titles . '</div> <input class="yith-wcbep-select-selected" type="hidden" value="' . json_encode( $badge_ids ) . '">';
        }

        return $value;
    }

    /**
     * @param $values
     *
     * @return array
     */
    public function add_badge_class( $values ) {
        $values[] = 'yith_wcbm_badge';

        return $values;
    }

    public function extra_custom_input() {
        ?>
        <div id="yith-wcbep-custom-input-yith_wcbm_badge" class="yith-wcbep-custom-input">
            <select id="yith-wcbep-custom-input-yith_wcbm_badge-select" class="chosen yith-wcbep-chosen" multiple xmlns="http://www.w3.org/1999/html">
                <?php
                $badges = yith_wcbm_get_badges( array( 'suppress_filters' => false ) );
                foreach ( $badges as $badge_id ) {
                    ?>
                    <option value="<?php echo $badge_id; ?>"><?php echo get_the_title( $badge_id ); ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <?php
    }

    public function add_extra_bulk_field() {
        ?>
        <tr>
            <td class="yith-wcbep-bulk-form-label-col">
                <label><?php _e('Badge', 'yith-woocommerce-badge-management') ?></label>
            </td>
            <td class="yith-wcbep-bulk-form-content-col">
                <select id="yith-wcbep-yith_wcbm_badge-bulk-select" name="yith-wcbep-yith_wcbm_badge-bulk-select"
                        class="yith-wcbep-miniselect is_resetable">
                    <option value="add"><?php _e( 'Add', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                    <option value="rem"><?php _e( 'Remove', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                    <option value="new"><?php _e( 'Set new', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                </select>
                <?php
                $badges = yith_wcbm_get_badges( array( 'suppress_filters' => false ) );
                if ( !empty( $badges ) ) {
                    ?>
                    <div class="yith-wcbep-bulk-chosen-wrapper">
                        <select id="yith-wcbep-yith_wcbm_badge-bulk-chosen" class="chosen yith-wcbep-chosen yith-wcbep-miniselect is_resetable" multiple
                                xmlns="http://www.w3.org/1999/html">
                            <?php
                            foreach ( $badges as $badge_id ) {
                                ?>
                                <option value="<?php echo $badge_id; ?>"><?php echo get_the_title( $badge_id ); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <?php
                } ?>
            </td>
        </tr>
        <?php

    }

    /**
     * @param $product
     * @param $matrix_keys
     * @param $single_modify
     */
    public function save_badge_meta( $product, $matrix_keys, $single_modify, $is_variation ) {
        $badge_index = array_search( 'yith_wcbm_badge', $matrix_keys );
        if ( isset( $single_modify[ $badge_index ] ) ) {
            if ( !$is_variation ) {
                $badges                     = json_decode( $single_modify[ $badge_index ] );
                $product_meta               = yit_get_prop( $product, '_yith_wcbm_product_meta', true );
                $product_meta               = !!$product_meta && is_array( $product_meta ) ? $product_meta : array();
                $product_meta[ 'id_badge' ] = $badges;

                yit_save_prop( $product, '_yith_wcbm_product_meta', $product_meta );
            }
        }

    }

}

/**
 * Unique access to instance of YITH_WCBEP_Badge_Management_Compatibility class
 *
 * @return YITH_WCBEP_Badge_Management_Compatibility
 * @since 1.0.11
 */
function YITH_WCBEP_Badge_Management_Compatibility() {
    return YITH_WCBEP_Badge_Management_Compatibility::get_instance();
}