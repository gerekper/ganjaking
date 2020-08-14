<?php
/**
 * Post Types class
 *
 * @author  Yithemes
 * @package YITH Google Product Feed for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCGPF_VERSION' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCGPF_Post_Types_Template_Feed' ) ) {
    /**
     * YITH Google Product Feed Template for create feeds
     *
     * @since 1.0.0
     */
    class YITH_WCGPF_Post_Types_Template_Feed {


        /**
         * Template feed post type
         *
         * @var string
         * @static
         */
        public static $template_feed = 'yith-wcgpf-template';

        /**
         * Hook in methods.
         */
        public function __construct() {
            add_action( 'init', array($this, 'register_post_types' ));
            add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ));
            add_action( 'edit_form_advanced', array( $this, 'add_return_to_list_button' ) );
            add_action('save_post',array($this,'save_post_data'));
        }

        /**
         * Register core post types.
         */
        public function register_post_types() {
            if ( post_type_exists( self::$template_feed ) ) {
                return;
            }

            do_action( 'yith_wcgpf_register_post_type_template_feed' );

            /*  Google Product Feed Templates  */

            $labels = array(
                'name'               => esc_html__( 'Feed template', 'yith-google-product-feed-for-woocommerce' ),
                'singular_name'      => esc_html__( 'Feed template', 'yith-google-product-feed-for-woocommerce' ),
                'add_new'            => esc_html__( 'Add feed template', 'yith-google-product-feed-for-woocommerce' ),
                'add_new_item'       => esc_html__( 'Add new feed template', 'yith-google-product-feed-for-woocommerce' ),
                'edit'               => esc_html__( 'Edit', 'yith-google-product-feed-for-woocommerce' ),
                'edit_item'          => esc_html__( 'Edit feed template', 'yith-google-product-feed-for-woocommerce' ),
                'new_item'           => esc_html__( 'New feed template', 'yith-google-product-feed-for-woocommerce' ),
                'view'               => esc_html__( 'View feed template', 'yith-google-product-feed-for-woocommerce' ),
                'view_item'          => esc_html__( 'View feed template', 'yith-google-product-feed-for-woocommerce' ),
                'search_items'       => esc_html__( 'Search feed template', 'yith-google-product-feed-for-woocommerce' ),
                'not_found'          => esc_html__( 'No feed template found', 'yith-google-product-feed-for-woocommerce' ),
                'not_found_in_trash' => esc_html__( 'No feed template in trash', 'yith-google-product-feed-for-woocommerce' ),
                'parent'             => esc_html__( 'Parent feed template', 'yith-google-product-feed-for-woocommerce' ),
                'menu_name'          => esc_html_x( 'YITH feed template', 'Admin menu name', 'yith-google-product-feed-for-woocommerce' ),
                'all_items'          => esc_html__( 'All feed templates', 'yith-google-product-feed-for-woocommerce' ),
            );

            $template_feed_post_type_args = array(
                'label'               => esc_html__( 'Feed templates', 'yith-google-product-feed-for-woocommerce' ),
                'labels'              => $labels,
                'description'         => esc_html__( 'This is where all feed templates are stored.', 'yith-google-product-feed-for-woocommerce' ),
                'public'              => false,
                'show_ui'             => true,
                'capability_type'     => 'product',
                'map_meta_cap'        => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'show_in_menu'        => false,
                'hierarchical'        => false,
                'show_in_nav_menus'   => false,
                'rewrite'             => false,
                'query_var'           => false,
                'supports'            => array( 'title' ),
                'has_archive'         => false,
                'menu_icon'           => 'dashicons-edit',
            );

            register_post_type( self::$template_feed, apply_filters( 'yith_wcgpf_register_post_type_template_feed', $template_feed_post_type_args ) );

        }

        /**
         * Add custom metaboxes.
         */
        public function add_metaboxes($post)
        {
            add_meta_box( 'yith_wcgpf_template_feed_custom',  esc_html__( 'Template feed configuration', 'yith-google-product-feed-for-woocommerce'
            ), array($this,'configuration_template_metabox'), 'yith-wcgpf-template', 'normal', 'high' );
        }
        /**
         * Add template metabox .
         */
        function configuration_template_metabox($post) {
            ?>
                <div class="wf-tab-content">
                <table class="yith_wcgpf_template_table widefat yith_wcgpf_lenght" id="yith-wcgpf-template-table">
                    <thead class="yith_wcgpf_template_table_thead">
                        <tr>
                            <th></th>
                            <th><?php esc_html_e('Attributes','yith-google-product-feed-for-woocommerce') ?></th>
                            <th><?php esc_html_e('Prefix','yith-google-product-feed-for-woocommerce') ?></th>
                            <th><?php esc_html_e('Value','yith-google-product-feed-for-woocommerce') ?></th>
                            <th><?php esc_html_e('Suffix','yith-google-product-feed-for-woocommerce') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody  class="yith_wcgpf_template_table_thead_tbody" >
                            <?php do_action('yith_wcgpf_get_body_template','google',$post); ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7">
                                <button type="button" class="button button-default" id="yith-wcgpf-add-new-row"><?php esc_html_e('Add new row','yith-google-product-feed-for-woocommerce')?></button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            <?php
        }
        /**
         * Add return to the template list button .
         */
        public function add_return_to_list_button() {
            global $post;

            if ( isset( $post ) && self::$template_feed === $post->post_type ) {
                $admin_url = admin_url( 'admin.php' );
                $params = array(
                    'page' => 'yith_wcgpf_panel',
                    'tab' => 'template-feed'
                );

                $list_url = apply_filters( 'yith_wcgpf_template_back_link', esc_url( add_query_arg( $params, $admin_url ) ) );
                $button = sprintf( '<a class="button-secondary" href="%s">%s</a>', $list_url,
                    esc_html__( 'Back to the list of templates',
                        'yith-google-product-feed-for-woocommerce' ) );
                echo $button;
            }
        }

        /**
         * Save post data.
         */
        public function save_post_data($post_id) {
            if(!isset($_POST['yith-wcgpf-attributes'])){
                return;
            }
            $attributes = isset($_POST['yith-wcgpf-attributes']) ? $_POST['yith-wcgpf-attributes'] : 0 ;
            $prefix = isset($_POST['yith_wcgpf_prexif']) ? $_POST['yith_wcgpf_prexif'] : '';
            $value = isset($_POST['yith-wcgpf-value']) ? $_POST['yith-wcgpf-value'] : '';
            $suffix = isset($_POST['yith_wcgpf_sufix']) ? $_POST['yith_wcgpf_sufix'] : '';
            $count  = count($attributes);
            for ($i = 0; $i < $count; $i++) {
                if( '' != $attributes[$i] ) {
                    $save_values[$i]['attributes'] = $attributes[$i];
                    $save_values[$i]['prefix'] = $prefix[$i];
                    $save_values[$i]['value'] = $value[$i];
                    $save_values[$i]['suffix'] = $suffix[$i];
                }
            }
            if ( !empty( $save_values )) {
                update_post_meta( $post_id, 'yith_wcgpf_save_template', $save_values );
            }
        }
    }
}

return new YITH_WCGPF_Post_Types_Template_Feed();