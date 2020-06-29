<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Custom Taxonomies Manager
 *
 * @class   YITH_WCBEP_Custom_Taxonomies_Manager
 * @package Yithemes
 * @since   1.2.1
 * @author  Yithemes
 *
 */
class YITH_WCBEP_Custom_Taxonomies_Manager {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCBEP_Custom_Taxonomies_Manager
     */
    protected static $_instance;

    private static $_custom_taxonomies;

    private $_custom_taxonomies_terms = array();

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCBEP_Custom_Taxonomies_Manager
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
        // Add Tab for custom field Settings
        add_filter( 'yith_wcbep_settings_admin_tabs', array( $this, 'add_custom_field_tab' ) );
        add_action( 'yith_wcbep_custom_taxonomies_tab', array( $this, 'render_custom_fields_tab' ) );
        add_action( 'wp_ajax_yith_wcbep_save_custom_fields', array( $this, 'ajax_save_custom_fields' ) );
        add_action( 'admin_init', array( $this, 'action_save_custom_fields' ) );


        // Add custom taxonomies in BULK
        add_filter( 'yith_wcbep_default_columns', array( $this, 'add_column' ) );
        add_filter( 'yith_wcbep_manage_custom_columns', array( $this, 'manage_column' ), 10, 3 );
        add_filter( 'yith_wcbep_variation_not_editable_and_empty', array( $this, 'edit_not_editable_and_empty_in_variations' ) );

        add_filter( 'yith_wcbep_extra_obj_class_chosen', array( $this, 'add_chosen_in_js' ) );
        add_action( 'yith_wcbep_extra_custom_input', array( $this, 'extra_custom_input' ) );
        add_action( 'yith_wcbep_update_product', array( $this, 'save' ), 10, 4 );

        add_action( 'yith_wcbep_extra_attr_bulk_fields', array( $this, 'add_extra_bulk_fields' ) );
        add_filter( 'yith_wcbep_extra_bulk_columns_chosen', array( $this, 'add_extra_bulk_columns_chosen' ) );


        // Filters
        add_action( 'yith_wcbep_filters_after_attribute_fields', array( $this, 'add_fields_in_filters' ) );
    }

    public function add_custom_field_tab( $tabs ) {
        $tabs[ 'custom-taxonomies' ] = __( 'Custom Taxonomies', 'yith-woocommerce-bulk-product-editing' );

        return $tabs;
    }

    public function render_custom_fields_tab() {
        $template_url = YITH_WCBEP_TEMPLATE_PATH . '/premium/panel/custom-taxonomies-tab.php';
        file_exists( $template_url ) && include( $template_url );
    }

    public function action_save_custom_fields() {
        if ( empty( $_POST[ 'yith_wcbep_nonce' ] ) || !wp_verify_nonce( $_POST[ 'yith_wcbep_nonce' ], 'yith_wcbep_save_custom_taxonomies' ) ) {
            return;
        }

        if ( isset( $_POST[ 'yith-wcbep-custom-taxonomies' ] ) ) {
            $custom_fields = $_POST[ 'yith-wcbep-custom-taxonomies' ];
            self::save_custom_taxonomies( $custom_fields );
        } else {
            self::save_custom_taxonomies( array() );
        }
    }

    public static function save_custom_taxonomies( $fields ) {

        if ( !!$fields && is_array( $fields ) ) {
            $fields = array_map( 'sanitize_text_field', array_filter( $fields ) );
        } else {
            $fields = array();
        }

        return update_option( 'yith_wcbep_custom_taxonomies', $fields );
    }

    public static function get_custom_taxonomies() {
        if ( !isset( self::$_custom_taxonomies ) ) {
            self::$_custom_taxonomies = apply_filters( 'yith_wcbep_get_custom_taxonomies', get_option( 'yith_wcbep_custom_taxonomies', array() ) );
        }

        return self::$_custom_taxonomies;
    }


    /**
     * Utils getter
     */

    /**
     * retrieve the taxonomy name
     *
     * @param $slug
     *
     * @return mixed
     */
    public function get_taxonomy_name( $taxonomy_slug ) {
        $name = $taxonomy_slug;
        if ( $tax = get_taxonomy( $taxonomy_slug ) ) {
            $labels = get_taxonomy_labels( $tax );
            $name   = isset( $labels->name ) ? $labels->name : $taxonomy_slug;
        }

        return $name;
    }

    /**
     * @param       $taxonomy_slug
     *
     * @param array $args
     *
     * @return array
     */
    public function get_tax_array( $taxonomy_slug, $args = array() ) {
        if ( !isset( $this->_custom_taxonomies_terms[ $taxonomy_slug ] ) ) {
            $default_args = array(
                'taxonomy'   => $taxonomy_slug,
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            );

            $args                                             = wp_parse_args( $args, $default_args );
            $this->_custom_taxonomies_terms[ $taxonomy_slug ] = yith_wcbep_get_terms( $args );
        }

        return $this->_custom_taxonomies_terms[ $taxonomy_slug ];
    }

    /**
     *  Add Custom Taxonomies in Bulk
     */

    public function add_fields_in_filters() {
        foreach ( self::get_custom_taxonomies() as $taxonomy_slug ) {
            if ( !taxonomy_exists( $taxonomy_slug ) )
                continue;

            $terms = $this->get_tax_array( $taxonomy_slug, array( 'hide_empty' => true ) );

            if ( !empty( $terms ) ) {
                ?>
                <tr>
                    <td class="yith-wcbep-filter-form-label-col">
                        <label><?php echo $this->get_taxonomy_name( $taxonomy_slug ) ?></label>
                    </td>
                    <td class="yith-wcbep-filter-form-content-col">
                        <select id="yith-wcbep-<?php echo $taxonomy_slug ?>-filter" data-taxonomy="<?php echo $taxonomy_slug ?>"
                                class="chosen is_resetable yith-wcbep-custom-taxonomy-filter" multiple xmlns="http://www.w3.org/1999/html">
                            <?php
                            foreach ( $terms as $term ) {
                                ?>
                                <option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php
            }
        }
    }

    public function add_chosen_in_js( $classes ) {
        foreach ( self::get_custom_taxonomies() as $taxonomy_slug ) {
            if ( !taxonomy_exists( $taxonomy_slug ) )
                continue;

            $classes[] = $taxonomy_slug;
        }

        return $classes;
    }

    public function extra_custom_input() {
        foreach ( self::get_custom_taxonomies() as $taxonomy_slug ) {
            if ( !taxonomy_exists( $taxonomy_slug ) )
                continue;

            $terms = $this->get_tax_array( $taxonomy_slug );

            if ( !empty( $terms ) ) {
                ?>
                <div id="yith-wcbep-custom-input-<?php echo $taxonomy_slug ?>" class="yith-wcbep-custom-input">
                    <select id="yith-wcbep-custom-input-<?php echo $taxonomy_slug ?>-select" class="chosen yith-wcbep-chosen" multiple xmlns="http://www.w3.org/1999/html">
                        <?php
                        foreach ( $terms as $b ) {
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
    }

    /**
     * @param $columns
     *
     * @return mixed
     */
    public function add_column( $columns ) {
        foreach ( self::get_custom_taxonomies() as $taxonomy_slug ) {
            if ( !taxonomy_exists( $taxonomy_slug ) )
                continue;
            $columns[ $taxonomy_slug ] = $this->get_taxonomy_name( $taxonomy_slug );
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
    public function manage_column( $value, $column_name, $post ) {
        $new_value = '';
        foreach ( self::get_custom_taxonomies() as $taxonomy_slug ) {
            if ( !taxonomy_exists( $taxonomy_slug ) )
                continue;

            if ( $column_name == $taxonomy_slug ) {
                $terms       = get_the_terms( $post->ID, $taxonomy_slug );
                $terms       = !empty( $terms ) ? $terms : array();
                $terms_html  = '';
                $loop        = 0;
                $my_term_ids = array();
                foreach ( $terms as $term ) {
                    $loop++;
                    $terms_html .= $term->name;
                    if ( $loop < count( $terms ) ) {
                        $terms_html .= ', ';
                    }
                    $my_term_ids[] = $term->term_id;
                }

                $new_value .= '<div class="yith-wcbep-select-values">' . $terms_html . '</div> <input class="yith-wcbep-select-selected" type="hidden" value="' . json_encode( $my_term_ids ) . '">';
            }
        }

        return !!$new_value ? $new_value : $value;
    }

    /**
     * @param $values
     *
     * @return array
     */
    public function edit_not_editable_and_empty_in_variations( $values ) {
        foreach ( self::get_custom_taxonomies() as $taxonomy_slug ) {
            if ( !taxonomy_exists( $taxonomy_slug ) )
                continue;

            $values[] = $taxonomy_slug;
        }

        return $values;
    }

    /**
     * @param $values
     *
     * @return array
     */
    public function add_extra_bulk_columns_chosen( $values ) {
        foreach ( self::get_custom_taxonomies() as $taxonomy_slug ) {
            if ( !taxonomy_exists( $taxonomy_slug ) )
                continue;

            $values[] = $taxonomy_slug;
        }

        return $values;
    }

    /**
     * @param $product
     * @param $matrix_keys
     * @param $single_modify
     */
    public function save( $product, $matrix_keys, $single_modify, $is_variation ) {
        foreach ( self::get_custom_taxonomies() as $taxonomy_slug ) {
            if ( !taxonomy_exists( $taxonomy_slug ) )
                continue;


            $index = array_search( $taxonomy_slug, $matrix_keys );
            if ( !empty( $single_modify[ $index ] ) ) {
                if ( !$is_variation ) {
                    $new_value = $single_modify[ $index ];
                    $terms     = json_decode( $new_value );
                    wp_set_post_terms( $product->get_id(), $terms, $taxonomy_slug );
                }
            }
        }

    }

    public function add_extra_bulk_fields() {
        foreach ( self::get_custom_taxonomies() as $taxonomy_slug ) {
            if ( !taxonomy_exists( $taxonomy_slug ) )
                continue;

            ?>
            <tr>
                <td class="yith-wcbep-bulk-form-label-col">
                    <label><?php echo $this->get_taxonomy_name( $taxonomy_slug ) ?></label>
                </td>
                <td class="yith-wcbep-bulk-form-content-col">
                    <select id="yith-wcbep-<?php echo $taxonomy_slug ?>-bulk-select" name="yith-wcbep-<?php echo $taxonomy_slug ?>-bulk-select"
                            class="yith-wcbep-miniselect is_resetable">
                        <option value="add"><?php _e( 'Add', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                        <option value="rem"><?php _e( 'Remove', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                        <option value="new"><?php _e( 'Set new', 'yith-woocommerce-bulk-product-editing' ) ?></option>
                    </select>
                    <?php
                    $terms = $this->get_tax_array( $taxonomy_slug );
                    if ( !empty( $terms ) ) {
                        ?>
                        <div class="yith-wcbep-bulk-chosen-wrapper">
                            <select id="yith-wcbep-<?php echo $taxonomy_slug ?>-bulk-chosen" class="chosen yith-wcbep-chosen yith-wcbep-miniselect is_resetable" multiple
                                    xmlns="http://www.w3.org/1999/html">
                                <?php
                                foreach ( $terms as $term ) {
                                    ?>
                                    <option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>
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
}

/**
 * Unique access to instance of YITH_WCBEP_Badge_Management_Compatibility class
 *
 * @return YITH_WCBEP_Custom_Taxonomies_Manager
 * @since 1.2.1
 */
function YITH_WCBEP_Custom_Taxonomies_Manager() {
    return YITH_WCBEP_Custom_Taxonomies_Manager::get_instance();
}