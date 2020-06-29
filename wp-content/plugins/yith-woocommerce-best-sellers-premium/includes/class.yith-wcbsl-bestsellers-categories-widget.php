<?php
/**
 * Best sellers categories Widget
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers Premium
 * @version 1.0.0
 */
if ( !defined( 'YITH_WCBSL' ) ) {
    exit;
} // Exit if accessed directly


if ( !class_exists( 'YITH_WCBSL_Bestsellers_Categories_Widget' ) ) {
    /**
     * Best Sellers Categories Widget
     *
     * @author   WooThemes
     * @category Widgets
     * @package  WooCommerce/Widgets
     * @version  2.3.0
     * @extends  WC_Widget
     */
    class YITH_WCBSL_Bestsellers_Categories_Widget extends WC_Widget {

        /**
         * Category ancestors
         *
         * @var array
         */
        public $cat_ancestors;

        /**
         * Current Category
         *
         * @var bool
         */
        public $current_cat;

        /**
         * Constructor
         */
        public function __construct() {
            $this->widget_cssclass    = 'yith_wcbsl_categories_widget';
            $this->widget_description = __( 'A list or dropdown of Best Seller categories.', 'yith-woocommerce-best-sellers' );
            $this->widget_id          = 'yith_wcbsl_categories_widget';
            $this->widget_name        = __( 'YITH WooCommerce Best Seller Categories', 'yith-woocommerce-best-sellers' );
            $this->settings           = array(
                'title'                => array(
                    'type'  => 'text',
                    'std'   => __( 'Best Seller Categories', 'yith-woocommerce-best-sellers' ),
                    'label' => __( 'Title', 'yith-woocommerce-best-sellers' )
                ),
                'orderby'              => array(
                    'type'    => 'select',
                    'std'     => 'name',
                    'label'   => __( 'Order by', 'woocommerce' ),
                    'options' => array(
                        'order' => __( 'Category Order', 'woocommerce' ),
                        'name'  => __( 'Name', 'woocommerce' )
                    )
                ),
                'hierarchical'         => array(
                    'type'  => 'checkbox',
                    'std'   => 1,
                    'label' => __( 'Show hierarchy', 'woocommerce' )
                ),
                'show_only_in_bs_page' => array(
                    'type'  => 'checkbox',
                    'std'   => 0,
                    'label' => __( 'Show only in Best Sellers Page', 'yith-woocommerce-best-sellers' )
                ),
                'categories'           => array(
                    'type'  => 'categories',
                    'std'   => array(),
                    'label' => __( 'Categories', 'yith-woocommerce-best-sellers' ),
                    'desc'  => __( 'Select Product Categories you want to display. Leave it empty for displaying all.', 'yith-woocommerce-best-sellers' )
                ),
            );

            parent::__construct();
        }

        /**
         * @see WP_Widget->form
         *
         * @param array $instance
         *
         * @return string|void
         */
        public function form( $instance ) {
            parent::form( $instance );

            if ( empty( $this->settings ) ) {
                return;
            }

            foreach ( $this->settings as $key => $setting ) {
                $value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting[ 'std' ];

                switch ( $setting[ 'type' ] ) {
                    case 'categories' :
                        ?>
                        <p>
                            <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
                            <?php
                            $cats = yith_wcbsl_get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => 0, 'orderby' => 'ASC' ) );
                            ?>
                            <select class="widefat wc-enhanced-select" multiple id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>[]">
                                <?php foreach ( $cats as $cat ): ?>
                                    <option value="<?php echo $cat->term_id ?>" <?php selected( true, in_array( $cat->term_id, $value ) ); ?>><?php echo esc_html( $cat->name ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label><?php echo $setting[ 'desc' ]; ?></label>
                        </p>
                        <script>
                            jQuery( 'select#<?php echo esc_attr( $this->get_field_id( $key ) ); ?>' ).select2();
                        </script>
                        <?php
                        break;
                }
            }
        }

        /**
         * update function.
         *
         * @see WP_Widget->update
         *
         * @param array $new_instance
         * @param array $old_instance
         *
         * @return array
         */
        public function update( $new_instance, $old_instance ) {

            $instance = $old_instance;

            if ( empty( $this->settings ) ) {
                return $instance;
            }

            foreach ( $this->settings as $key => $setting ) {

                if ( 'categories' === $setting[ 'type' ] ) {
                    $instance[ $key ] = isset( $new_instance[ $key ] ) ? $new_instance[ $key ] : array();
                } elseif ( isset( $new_instance[ $key ] ) ) {
                    $instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
                } elseif ( 'checkbox' === $setting[ 'type' ] ) {
                    $instance[ $key ] = 0;
                }
            }

            $this->flush_widget_cache();

            return $instance;
        }

        /**
         * widget function.
         *
         * @see WP_Widget
         *
         * @param array $args
         * @param array $instance
         */
        public function widget( $args, $instance ) {
            global $wp_query, $post;

            $show_only_in_bs_page = isset( $instance[ 'show_only_in_bs_page' ] ) ? $instance[ 'show_only_in_bs_page' ] : $this->settings[ 'show_only_in_bs_page' ][ 'std' ];
            if ( $show_only_in_bs_page ) {
                $bestsellers_page_id = get_option( 'yith-wcbsl-bestsellers-page-id' );
                if ( $bestsellers_page_id != get_the_ID() ) {
                    return;
                }
            }

            $selected_categories = isset( $instance[ 'categories' ] ) ? $instance[ 'categories' ] : $this->settings[ 'hierarchical' ][ 'std' ];

            $h             = isset( $instance[ 'hierarchical' ] ) ? $instance[ 'hierarchical' ] : $this->settings[ 'hierarchical' ][ 'std' ];
            $o             = isset( $instance[ 'orderby' ] ) ? $instance[ 'orderby' ] : $this->settings[ 'orderby' ][ 'std' ];
            $dropdown_args = array( 'hide_empty' => false );
            $list_args     = array( 'hierarchical' => $h, 'taxonomy' => 'product_cat', 'hide_empty' => false );

            // Menu Order
            $list_args[ 'menu_order' ] = false;
            if ( $o == 'order' ) {
                $list_args[ 'menu_order' ] = 'asc';
            } else {
                $list_args[ 'orderby' ] = 'title';
            }

            // Setup Current Category
            $this->current_cat   = false;
            $this->cat_ancestors = array();

            if ( is_tax( 'product_cat' ) ) {

                $this->current_cat   = $wp_query->queried_object;
                $this->cat_ancestors = get_ancestors( $this->current_cat->term_id, 'product_cat' );

            } elseif ( is_singular( 'product' ) ) {

                $product_category = wc_get_product_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent' ) );

                if ( $product_category ) {
                    $this->current_cat   = end( $product_category );
                    $this->cat_ancestors = get_ancestors( $this->current_cat->term_id, 'product_cat' );
                }

            }

            $this->widget_start( $args, $instance );

            $list_args[ 'walker' ]                     = new YITH_WCBSL_Walker_Category;
            $list_args[ 'title_li' ]                   = '';
            $list_args[ 'pad_counts' ]                 = 1;
            $list_args[ 'show_option_none' ]           = __( 'No product categories exist.', 'woocommerce' );
            $list_args[ 'current_category' ]           = ( $this->current_cat ) ? $this->current_cat->term_id : '';
            $list_args[ 'current_category_ancestors' ] = $this->cat_ancestors;

            // selected categories in widget settings tab
            if ( !empty( $selected_categories ) ) {
                $list_args[ 'include' ] = implode( ',', $selected_categories );
            }

            $bestsellers_page_id = get_option( 'yith-wcbsl-bestsellers-page-id' );
            if ( !$bestsellers_page_id )
                return;

            $bestsellers_page_permalink                = get_permalink( $bestsellers_page_id );
            $list_args[ 'bestsellers_page_permalink' ] = $bestsellers_page_permalink;

            echo '<ul class="yith-wcbsl-categories">';

            wp_list_categories( $list_args );

            echo '</ul>';

            $this->widget_end( $args );
        }
    }
}