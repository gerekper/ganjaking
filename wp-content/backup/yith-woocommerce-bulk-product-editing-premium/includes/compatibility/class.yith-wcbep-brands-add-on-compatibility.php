<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Brands Add On Compatibility Class
 *
 * @class   YITH_WCBEP_Brands_Add_On_Compatibility
 * @package Yithemes
 * @since   1.1.3
 * @author  Yithemes
 *
 */
class YITH_WCBEP_Brands_Add_On_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCBEP_Brands_Add_On_Compatibility
     */
    protected static $_instance;

    /**
     * @var array
     */
    public $brands_array;

    /**
     * @type string
     */
    public $taxonomy_name;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCBEP_Brands_Add_On_Compatibility
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
        $this->taxonomy_name = YITH_WCBR::$brands_taxonomy;
        add_filter( 'yith_wcbep_default_columns', array( $this, 'add_column' ) );
        add_filter( 'yith_wcbep_manage_custom_columns', array( $this, 'manage_column' ), 10, 3 );
        add_filter( 'yith_wcbep_variation_not_editable_and_empty', array( $this, 'edit_not_editable_and_empty_in_variations' ) );
        //add_filter( 'yith_wcbep_td_extra_class_select', array( $this, 'add_extra_class_select_in_js' ) );

        add_filter( 'yith_wcbep_extra_obj_class_chosen', array( $this, 'add_chosen_in_js' ) );

        add_action( 'yith_wcbep_extra_custom_input', array( $this, 'extra_custom_input' ) );

        add_action( 'yith_wcbep_update_product', array( $this, 'save' ), 10, 4 );

        add_action( 'yith_wcbep_extra_general_bulk_fields', array( $this, 'add_extra_bulk_fields' ) );
        add_filter( 'yith_wcbep_extra_bulk_columns_chosen', array( $this, 'add_extra_bulk_columns_chosen' ) );


        // Filters
        add_action( 'yith_wcbep_filters_after_attribute_fields', array( $this, 'add_brand_field_in_filters' ) );
    }

    public function add_brand_field_in_filters() {
        $args   = array(
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
        );
        $brands = get_terms( $this->taxonomy_name, $args );

        if ( !empty( $brands ) ) {
            ?>
            <tr>
                <td class="yith-wcbep-filter-form-label-col">
                    <label><?php _e( 'Brands', 'yith-woocommerce-bulk-product-editing' ) ?></label>
                </td>
                <td class="yith-wcbep-filter-form-content-col">
                    <select id="yith-wcbep-brands-filter" name="yith-wcbep-brands-filter[]"
                            class="chosen is_resetable" multiple xmlns="http://www.w3.org/1999/html">
                        <?php
                        foreach ( $brands as $brand ) {
                            ?>
                            <option value="<?php echo $brand->term_id; ?>"><?php echo $brand->name; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php
        }
    }

    public function add_chosen_in_js( $classes ) {
        $classes[] = $this->taxonomy_name;

        return $classes;
    }

    public function extra_custom_input() {
        $brands_array = $this->get_brands_array();

        if ( !empty( $brands_array ) ) {
            ?>
            <div id="yith-wcbep-custom-input-<?php echo $this->taxonomy_name ?>" class="yith-wcbep-custom-input">
                <select id="yith-wcbep-custom-input-<?php echo $this->taxonomy_name ?>-select" class="chosen yith-wcbep-chosen" multiple xmlns="http://www.w3.org/1999/html">
                    <?php
                    foreach ( $brands_array as $b ) {
                        ?>
                        <option value="<?php echo $b->term_id; ?>"><?php echo $b->name; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <?php
        }
    }

    /**
     * @param $columns
     *
     * @return mixed
     */
    public function add_column( $columns ) {
        $columns[ $this->taxonomy_name ] = __( 'Brands', 'yith-wcbr' );

        return $columns;
    }

    /**
     * @param $value
     * @param $column_name
     * @param $post
     *
     * @return string
     */
    public function manage_column( $value, $column_name, $post ) {
        if ( $column_name == $this->taxonomy_name ) {
            $brands       = get_the_terms( $post->ID, $this->taxonomy_name );
            $brands       = !empty( $brands ) ? $brands : array();
            $brands_html  = '';
            $loop         = 0;
            $my_brands_id = array();
            foreach ( $brands as $b ) {
                $loop++;
                $brands_html .= $b->name;
                if ( $loop < count( $brands ) ) {
                    $brands_html .= ', ';
                }
                $my_brands_id[] = $b->term_id;
            }

            $value = '<div class="yith-wcbep-select-values">' . $brands_html . '</div> <input class="yith-wcbep-select-selected" type="hidden" value="' . json_encode( $my_brands_id ) . '">';
        }

        return $value;
    }

    /**
     * @param $values
     *
     * @return array
     */
    public function edit_not_editable_and_empty_in_variations( $values ) {
        $values[] = $this->taxonomy_name;

        return $values;
    }

    /**
     * @param $values
     *
     * @return array
     */
    public function add_extra_bulk_columns_chosen( $values ) {
        $values[] = $this->taxonomy_name;

        return $values;
    }

    /**
     * @param $extra_classes
     *
     * @return array
     */
    public function add_extra_class_select_in_js( $extra_classes ) {
        $extra_classes[] = 'td.yith_wcbm_badge';

        return $extra_classes;
    }

    /**
     * @param $product
     * @param $matrix_keys
     * @param $single_modify
     */
    public function save( $product, $matrix_keys, $single_modify, $is_variation ) {
        $index = array_search( $this->taxonomy_name, $matrix_keys );
        if ( !empty( $single_modify[ $index ] ) ) {
            if ( !$is_variation ) {
                $new_value = $single_modify[ $index ];
                $terms     = json_decode( $new_value );
                wp_set_post_terms( $product->get_id(), $terms, $this->taxonomy_name );
            }
        }

    }

    /**
     * @return array
     */
    public function get_brands_array() {
        if ( isset( $this->brands_array ) )
            return $this->brands_array;

        $this->brands_array = array();

        $cat_args           = array(
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        );
        $this->brands_array = get_terms( $this->taxonomy_name, $cat_args );

        return $this->brands_array;
    }

    public function add_extra_bulk_fields() {
        ?>
        <tr>
            <td class="yith-wcbep-bulk-form-label-col">
                <label><?php _e( 'Brands', 'yith-wcbr' ) ?></label>
            </td>
            <td class="yith-wcbep-bulk-form-content-col">
                <select id="yith-wcbep-<?php echo $this->taxonomy_name ?>-bulk-select" name="yith-wcbep-<?php echo $this->taxonomy_name ?>-bulk-select"
                        class="yith-wcbep-miniselect is_resetable">
                    <option value="add"><?php _e( 'Add', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                    <option value="rem"><?php _e( 'Remove', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                    <option value="new"><?php _e( 'Set new', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                </select>
                <?php
                $brands = $this->get_brands_array();
                if ( !empty( $brands ) ) {
                    ?>
                    <div class="yith-wcbep-bulk-chosen-wrapper">
                        <select id="yith-wcbep-<?php echo $this->taxonomy_name ?>-bulk-chosen" class="chosen yith-wcbep-chosen yith-wcbep-miniselect is_resetable" multiple
                                xmlns="http://www.w3.org/1999/html">
                            <?php
                            foreach ( $brands as $b ) {
                                ?>
                                <option value="<?php echo $b->term_id; ?>"><?php echo $b->name; ?></option>
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
}

/**
 * Unique access to instance of YITH_WCBEP_Brands_Add_On_Compatibility class
 *
 * @return YITH_WCBEP_Brands_Add_On_Compatibility
 * @since 1.1.3
 */
function YITH_WCBEP_Brands_Add_On_Compatibility() {
    return YITH_WCBEP_Brands_Add_On_Compatibility::get_instance();
}