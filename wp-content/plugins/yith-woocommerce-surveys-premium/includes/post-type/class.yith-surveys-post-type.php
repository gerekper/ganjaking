<?php
if (!defined('ABSPATH'))
    exit;

if (!class_exists('YITH_Surveys_Post_Type')) {

    class YITH_Surveys_Post_Type
    {
        /**
         * @var YITH_Surveys_Post_Type unique access
         */
        protected static $instance;
        /**
         * @var String, post type name
         */
        public $post_type_name;

        /**
         * __construct function
         */
        public function __construct()
        {

            $this->post_type_name = 'yith_wc_surveys';

            add_action('init', array($this, 'register_surveys_post_type'), 10);

        }

        /**
         * @return YITH_Surveys_Post_Type
         */
        public static function  get_instance()
        {

            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /** register surveys custom post type
         * @author YIThemes
         * @since 1.0.0
         */
        public function register_surveys_post_type()
        {
            $capability_type = 'survey';
            $caps = array(
                'edit_post' => "edit_{$capability_type}",
                'read_post' => "read_{$capability_type}",
                'delete_post' => "delete_{$capability_type}",
                'edit_posts' => "edit_{$capability_type}s",
                'edit_others_posts' => "edit_others_{$capability_type}s",
                'publish_posts' => "publish_{$capability_type}s",
                'read_private_posts' => "read_private_{$capability_type}s",
                'read' => "read",
                'delete_posts' => "delete_{$capability_type}s",
                'delete_private_posts' => "delete_private_{$capability_type}s",
                'delete_published_posts' => "delete_published_{$capability_type}s",
                'delete_others_posts' => "delete_others_{$capability_type}s",
                'edit_private_posts' => "edit_private_{$capability_type}s",
                'edit_published_posts' => "edit_published_{$capability_type}s",
                'create_posts' => "edit_{$capability_type}s",
                'manage_posts' => "manage_{$capability_type}s",
            );

            $args = apply_filters('yith_surveys_args_post_type', array(
                    'label' => $this->post_type_name,
                    'description' => __('YITH WooCommerce Surveys', 'yith-woocommerce-surveys'),
                    'labels' => $this->get_survey_taxonomy_label(),
                    'supports' => array('title'),
                    'hierarchical' => true,
                    'public' => false,
                    'show_ui' => ywcsur_is_premium_active(),
                    'show_in_menu' => ywcsur_is_premium_active(),
                    'show_in_nav_menus' => false,
                    'show_in_admin_bar' => false,
                    'menu_position' => 15,
                    'menu_icon' => 'dashicons-clipboard',
                    'can_export' => true,
                    'has_archive' => false,
                    'exclude_from_search' => true,
                    'publicly_queryable' => false,
                    'capability_type' => 'survey',
                    'capabilities' => $caps,
                )
            );

            register_post_type($this->post_type_name, $args);

            if (!ywcsur_is_premium_active())
                $this->create_free_survey();
        }

        /**
         * create a free survey
         * @author YIThemes
         * @since 1.0.0
         */
        private function create_free_survey()
        {

            $free_survey_id = get_option('yith_wc_free_survey_id', -1);
            $free_post = get_post($free_survey_id);

            if ($free_survey_id == -1 || is_null($free_post)) {

                $my_post = array(
                    'post_title' => '',
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_type' => 'yith_wc_surveys',
                    'comment_status' => 'closed'
                );
                $free_survey_id = wp_insert_post($my_post);

                if (is_wp_error($free_survey_id))
                    return;

                update_option('yith_wc_free_survey_id', $free_survey_id);
            }
        }

        /**
         * add free survey answer
         * @author YIThemes
         * @since 1.0.0
         * @param $post_parent
         * @param $post_title
         * @return bool|int|WP_Error
         */
        public function add_survey_child($post_parent, $post_title = '')
        {

            $my_post = array(
                'post_title' => $post_title,
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'yith_wc_surveys',
                'comment_status' => 'closed',
                'post_parent' => $post_parent
            );

            $child_id = wp_insert_post($my_post);
            return is_wp_error($child_id) ? false : $child_id;

        }

        /**
         * check is
         * @param $value
         */
        public function is_survey_child_exist($title, $post_parent)
        {

            global $wpdb;

            $post_title = wp_unslash(sanitize_post_field('post_title', $title, 0, 'db'));

            $query = "SELECT ID FROM $wpdb->posts INNER JOIN $wpdb->postmeta ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id WHERE 1=1 AND {$wpdb->posts}.post_title LIKE %s AND {$wpdb->posts}.post_type= %s AND {$wpdb->posts}.post_parent = %s AND {$wpdb->postmeta}.meta_key= %s AND {$wpdb->postmeta}.meta_value = %s";

            $args = array($post_title, $this->post_type_name, $post_parent, '_yith_answer_visible_in_survey', 'no');

            if (!empty ($args))
                return (int)$wpdb->get_var($wpdb->prepare($query, $args));

            return 0;
        }

        /**
         * Get the tab taxonomy label
         * @param   $arg string The string to return. Defaul empty. If is empty return all taxonomy labels
         * @author YIThemes
         * @since  1.0.0
         * @return Array taxonomy label
         *
         */
        protected function get_survey_taxonomy_label($arg = '')
        {

            $label = apply_filters('yith_surveys_taxonomy_label', array(
                    'name' => _x('YITH WooCommerce Surveys', 'post type general name', 'yith-woocommerce-surveys'),
                    'singular_name' => _x('Survey', 'post type singular name', 'yith-woocommerce-surveys'),
                    'menu_name' => __('Surveys', 'yith-woocommerce-surveys'),
                    'parent_item_colon' => __('Parent Item:', 'yith-woocommerce-surveys'),
                    'all_items' => __('All Surveys', 'yith-woocommerce-surveys'),
                    'view_item' => __('View Survey', 'yith-woocommerce-surveys'),
                    'add_new_item' => __('Add New Survey', 'yith-woocommerce-surveys'),
                    'add_new' => __('Add New Survey', 'yith-woocommerce-surveys'),
                    'edit_item' => __('Edit Survey', 'yith-woocommerce-surveys'),
                    'update_item' => __('Update Survey', 'yith-woocommerce-surveys'),
                    'search_items' => __('Search Surveys', 'yith-woocommerce-surveys'),
                    'not_found' => __('No surveys found', 'yith-woocommerce-surveys'),
                    'not_found_in_trash' => __('No surveys found in Trash', 'yith-woocommerce-surveys'),
                )
            );
            return !empty($arg) ? $label[$arg] : $label;
        }

        /** get all "Answer" by survey id
         * @author YIThemes
         * @since 1.0.0
         * @param int $post_parent
         * @return array
         */
        public function get_survey_children($extra = array())
        {
            $results = array();

            $default = array(
                'posts_per_page' => -1,
                'post_type' => 'yith_wc_surveys',
                'post_status' => 'publish',
                'fields' => 'ids',
                'suppress_filter' => false

            );

            $args = array_merge($default, $extra);
            
            $children = get_posts( $args );
            
            return $children;
        }

        /** get all "Surveys"
         * @author YIThemes
         * @since 1.0.0
         * @param array $extra_params
         * @return array
         */
        public function get_surveys($extra_params = array())
        {
            $orderby = get_option( 'ywcsur_orderby', 'date' );
            $order   = get_option( 'ywcsur_order', 'desc' );

            switch( $orderby ){

                case 'date':
                    $orderby = 'post_date';
                    break;
                case 'title':
                    $orderby = 'post_title';
                    break;
            }
            $default = array(
                'posts_per_page' => -1,
                'post_type' => 'yith_wc_surveys',
                'post_status' => 'publish',
                'post_parent' => 0,
                'orderby'   => $orderby,
                'order' => $order

            );

            $args = array_merge( $default, $extra_params);
            $results = array();

            $query = new WP_Query( $args );
            if( $query->have_posts() ) {

                while( $query->have_posts() ) {

                    $query->the_post();
                     $results[] = $query->post->ID;
                }
            }

            wp_reset_query();
            wp_reset_postdata();

            return $results;
        }

        /**
         * get all checkout surveys
         * @author YIThemes
         * @since 1.0.0
         * @param array $extra
         * @return array
         */
        public function get_checkout_surveys($handle_position = 'all')
        {

            $args = array(
                'meta_query' => array(
                    array(
                        'key' => '_yith_survey_visible_in',
                        'value' => 'checkout',
                        'compare' => '='
                    ),
                  )
            );

            if ($handle_position != 'all') {
                $args['meta_query'][] = array(
                    'key' => '_yith_survey_wc_handle',
                    'value' => $handle_position,
                    'compare' => '='
                );
              $args['meta_query']['relation'] = 'AND';
            }

            return $this->get_surveys($args);
        }


    }

}
/**
 * @return YITH_Surveys_Post_Type | YITH_Surveys_Post_Type_Premium
 */
function YITH_Surveys_Type()
{

    if (!ywcsur_is_premium_active())
        return YITH_Surveys_Post_Type::get_instance();

    return YITH_Surveys_Post_Type_Premium::get_instance();
}