<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of PREMIUM version of YWAR plugin
 *
 * @class   YITH_WooCommerce_Advanced_Reviews_Premium
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */
class YITH_WooCommerce_Advanced_Reviews_Premium extends YITH_WooCommerce_Advanced_Reviews {
    /**
     * @var int how many reviews should be showed
     */
    protected $reviews_to_show = 0;

    /**
     * @var bool show a link to load more reviews
     */
    protected $show_load_more = false;

    /**
     * @var int Choose if a "load more" should be shown, with textual link (value 2) or button link (value 3) or don't show it (value 1)
     */
    protected $load_more_type = 1;

    /**
     * @var string text to be used as a "show more" link caption
     */
    protected $load_more_text;

    /**
     * @var bool Let users to rate reviews
     */
    protected $enable_review_rating = false;

    /**
     * @var bool  Enable users to see user votes for every review
     */
    protected $show_peoples_vote = false;

    /**
     * @var bool Enable users to vote others reviews
     */
    protected $enable_review_vote = false;

    /**
     * @var bool Open reviews filtered by rating in a dialog box
     */
    protected $reviews_on_dialog = false;

    /**
     * @var bool  Enable the "reply to" option
     */
    protected $enable_reply = false;

    /**
     * @var bool let users report an inappropriate review
     */
    protected $enable_report_inappropriate = false;

    /**
     * @var bool enable the guests to vote
     */
    protected $enable_guest_vote = false;

    /**
     * @var bool show reviews when submitted without admin moderation
     */
    protected $review_moderation = false;

    /**
     * @var bool Limit "verified" customer to submit no more than one review
     */
    protected $limit_multiple_review = false;

    /**
     * @var bool Let the users to edit their review
     */
    protected $allow_edit_review = false;

    /**
     * @var bool Hide automatically a review when someone report it as inappropriate content
     */
    protected $hide_when_inappropriate = false;

    /**
     * @var int Number of users reporting a review as inappropriate content needed for hiding the review
     */
    protected $hide_inappropriate_review_threshold = 0;

    /**
     * @var bool show featured reviews
     */
    protected $show_featured_reviews = false;

    /**
     * @var int how much featured reviews to show
     */
    protected $featured_reviews_count = 0;

    /**
     * @var string custom column "upvoting" for custom post type
     */
    public $custom_column_upvoting = "upvoting";

    /**
     * @var string custom column "downvoting" for custom post type
     */
    public $custom_column_downvoting = "downvoting";

    /**
     * @var string custom column "inappropriate" for custom post type
     */
    public $custom_column_inappropriate = "inappropriate";

    /**
     * @var string custom column "featured"
     */
    public $custom_column_featured = "review-featured";

    /**
     * @var string custom column "reply status"
     */
    public $custom_column_reply_status = "reply-status";

    /**
     * @var string action name for "stop reply" review
     */
    protected $stop_reply_action = "stop-review-reply";

    /**
     * @var string action name for "open reply" review
     */
    protected $open_reply_action = "open-review-reply";

    /**
     * @var string action name for "set as featured" review
     */
    protected $add_featured_status_action = "add-featured-status";

    /**
     * @var string action name for "remove from featured" review
     */
    protected $remove_featured_status_action = "remove-featured-status";

    /**
     * @var bool enable the NoCaptcha system
     */
    public $recaptcha_enabled = false;

    /**
     * @var string the NoCaptcha secret key
     */
    public $recaptcha_secretkey = '';

    /**
     * @var string the NoCaptcha site key
     */
    public $recaptcha_sitekey = '';


    /**
     * Single instance of the class
     *
     * @since 1.0.0
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @since 1.0.0
     */
    public static function get_instance() {

        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor
     *
     * Initialize plugin and registers actions and filters to be used
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    protected function __construct() {

        parent::__construct();

        /**
         * Including the GDRP
         */
        add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

        // Creating the uniqueID for ywar_imported meta of the comments
        $this->create_ywar_meta_value_import_id();

        /**
         * Extends views for the reviews list
         */
        add_filter( 'yith_advanced_reviews_table_views', array( $this, 'add_review_table_views' ) );
        /**
         * Update current query, setting sorting by custom meta_key
         */
        add_filter( 'yith_advanced_reviews_column_sort', array( $this, 'advanced_reviews_column_sort' ), 10, 3 );

        /**
         * Set some custom column as sortable
         */
        add_filter( 'yith_advanced_reviews_sortable_custom_columns', array( $this, 'sortable_custom_columns' ) );

        /**
         * Add custom column to reviews post table
         */
        add_filter( 'yith_advanced_reviews_custom_column', array( $this, 'add_custom_column' ) );

        /**
         * Fill data for each custom column for Advanced Reviews post type
         */
        add_action( 'yith_advanced_reviews_show_advanced_reviews_columns', array(
            $this,
            'fill_advanced_reviews_columns',
        ), 10, 2 );

        add_filter( 'yith_advanced_reviews_approve_new_review', array( $this, 'set_approve_status' ) );

        add_action( 'ywar_after_summary', array( $this, 'show_filter_helpful_recent' ), 10, 2 );

        add_action( 'ywar_summary_row_prepend', array( $this, 'add_filtering_link' ), 10, 3 );
        add_action( 'ywar_summary_row_append', array( $this, 'close_filtering_link' ), 10, 3 );
        add_action( 'ywar_reviews_header', array( $this, 'add_review_header' ) );

        // register plugin for licence/update system
        add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
        add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );


        //  Add Ajax calls for yes/not review rating
        add_action( 'wp_ajax_vote_review', array( $this, 'vote_review_callback' ) );
        add_action( 'wp_ajax_nopriv_vote_review', array( $this, 'vote_review_callback' ) );

        //  Add Ajax calls for inappropriate content segnalations
        add_action( 'wp_ajax_report_inappropriate_review', array( $this, 'report_inappropriate_review_callback' ) );
        add_action( 'wp_ajax_nopriv_report_inappropriate_review', array(
            $this,
            'report_inappropriate_review_callback',
        ) );

        //  Add ajax callback handler for back-end operation
        add_action( 'wp_ajax_change_featured_status', array( $this, 'change_featured_status_callback' ) );
        add_action( 'wp_ajax_change_reply_status', array( $this, 'change_reply_status_callback' ) );

        /**
         * intercept approve and unapprove actions
         */
        add_action( "admin_action_{$this->open_reply_action}", array( $this, 'update_review_status' ) );
        add_action( "admin_action_{$this->stop_reply_action}", array( $this, 'update_review_status' ) );
        add_action( "admin_action_{$this->add_featured_status_action}", array( $this, 'update_review_status' ) );
        add_action( "admin_action_{$this->remove_featured_status_action}", array( $this, 'update_review_status' ) );

        /**
         * Add CSS classes to review table row
         */
        add_filter( 'yith_advanced_reviews_table_class', array( $this, 'add_table_css_class' ), 10, 2 );

        /**
         * Add pagination links if needed
         */
        add_action( 'yith_advanced_reviews_after_review_list', array( $this, 'show_reviews_pagination' ) );

        /**
         * Alter reviews shown on product reviews template
         */
        add_filter( 'yith_advanced_reviews_reviews_list', array( $this, 'show_reviews_list_args' ), 10, 2 );

        //  Add Ajax calls for asyncronous get filtered comments
        add_action( 'wp_ajax_get_ajax_comments', array( $this, 'get_ajax_comments_callback' ) );
        add_action( 'wp_ajax_nopriv_get_ajax_comments', array( $this, 'get_ajax_comments_callback' ) );

        //  Add Ajax calls for asyncronous editing of reviews
        add_action( 'wp_ajax_edit_review', array( $this, 'edit_review_callback' ) );

        //  Get product reviews of current user
        add_action( 'wp_ajax_get_customer_reviews', array( $this, 'get_customer_reviews_callback' ) );

        /**
         * Set the review parent in case the review is a reply to another review
         */
        add_filter( 'yith_advanced_reviews_post_parent', array( $this, 'set_post_parent' ) );

        /**
         * Add a tab to report tabs
         */
        add_filter( 'woocommerce_admin_reports', array( $this, 'advanced_reviews_report' ) );

        /**
         * Filter reviews on review status
         */
        add_filter( 'yith_advanced_reviews_filter_view', array( $this, 'filter_review' ), 10, 2 );

        /**
         * Add custom action to table bulk actions
         */
        add_filter( 'yith_advanced_reviews_bulk_actions', array( $this, 'add_bulk_actions' ) );

        /**
         * process custom bulk actions
         */
        add_action( 'yith_advanced_reviews_process_bulk_actions', array( $this, 'process_bulk_actions' ), 10, 2 );

        /**
         * Permit admins to show review form on product page
         */
        add_filter( 'yith_advanced_reviews_on_product_review_shown', array( $this, 'show_review_form' ), 10, 2 );

        /**
         * Intercept and deny review submitting under specific situation
         */
        add_filter( 'yith_advanced_reviews_on_product_review_shown_denied', array(
            $this,
            'deny_review_form'
        ), 10, 3 );

        add_filter( 'woocommerce_products_general_settings', array( $this, 'woocommerce_products_general_settings' ) );

        add_filter( 'ywar_product_reviews_submit_reviews_denied_text', array(
            $this,
            'ywar_product_reviews_submit_reviews_denied_text'
        ), 10, 2 );

        add_filter( 'comments_open', array( $this, 'reviews_open' ), 11, 2 );

        /**
         * Add recaptcha inside the comment form
         */

        if ( get_option( "ywar_enable_recaptcha" ) == "yes" && get_option( "ywar_recaptcha_site_key" ) != '' && get_option( "ywar_recaptcha_secret_key" ) != ''  ) {
            /** Check the recaptcha, if used */
            add_filter( 'comment_form_submit_button', array( $this, 'display_comment_recaptcha' ) );

            add_filter( 'preprocess_comment', array( $this, 'verify_comment_captcha' ),5 );
        }

        /**
         * Reviews shortcode
         */

        add_shortcode( 'yith_ywar_show_reviews' , array(
            $this,
            'yith_ywar_show_reviews'
        ) );


        add_action( 'yith_ywar_pagination_shortcode_nav', array( __CLASS__ , 'pagination_nav' ) );

        //  Elementor Widgets integration
        if ( defined('ELEMENTOR_VERSION') ) {
            require_once ( YITH_YWAR_DIR . 'includes/third-party/elementor/class-ywar-elementor.php' );
        }


    }

    /**
     * Including the GDRP
     */
    public function load_privacy() {

        if ( class_exists( 'YITH_Privacy_Plugin_Abstract' ) )
            require_once( YITH_YWAR_DIR . 'includes/class.yith-woocommerce-advanced-reviews-privacy.php' );

    }

    protected function create_ywar_meta_value_import_id(){

        // If the unique ID is not created we created
        // This unique ID is important for converting comments from other wordpress installation which were imported
        ( get_option( 'YITH_WC_ywar_meta_value_import_id' ) ? '' : update_option( 'YITH_WC_ywar_meta_value_import_id', uniqid() ) );

        // We are going to check if the plugin has already updated all the comments with new unique ID
        if ( get_option( 'YITH_WC_ywar_meta_value_import_id_updated' ) != '1' ){

            global $wpdb;

            $meta_value_import_id = get_option( 'YITH_WC_ywar_meta_value_import_id' );

            $query = "UPDATE {$wpdb->prefix}commentmeta SET meta_value = '$meta_value_import_id' WHERE meta_key = '_ywar_imported' and meta_value = '1'";

            $result = $wpdb->get_results( $query );

            if ( is_numeric( count( $result ) ) )
                update_option( 'YITH_WC_ywar_meta_value_import_id_updated', '1' );

        }

    }

    /**
     * If the comment is set as spam, sync the review status
     *
     * @param $comment_id
     */

    public function sync_comment_as_spam( $comment_id ) {
        $params = array(
            'post_type'   => YITH_YWAR_POST_TYPE,
            'post_status' => 'any',
            'meta_query'  => array(
                array(
                    'key'     => YITH_YWAR_META_COMMENT_ID,
                    'value'   => $comment_id,
                    'compare' => '=',
                ),
            ),
        );
        /** @var WP_Post $review */
        $review = get_posts( $params );

        if ( $review ) {
            update_post_meta( $review->ID, YITH_YWAR_META_APPROVED, 'spam' );
        }
    }

    /**
     * Akismet notified that the current comment is a spam content,
     * set the review to spam too.
     */
    public function akismet_spam_caught() {
        $comment_id = Akismet::get_last_comment();
        $this->sync_comment_as_spam( $comment_id );
    }

    
    /**
     * Prevent the reviews from being hidden when comments_open is false
     *
     * @param bool        $open    Whether the current post is open for comments.
     * @param int|WP_Post $post_id The post ID or WP_Post object.
     *
     * @return bool
     */
    function reviews_open( $open, $post_id ) {

        $post = get_post( $post_id );

        if ( $post->post_type == 'product' ) {
            if ( isset( $post ) ) {
                $open = $post->comment_status;
            } else {
                if ( ! isset( $post_id ) ) {
                    global $product;
                    $open = get_post( yit_get_prop( $product, 'id' ) )->comment_status;
                }
            }
        }

        return 'open' == $open;
    }


    /**
     * process custom bulk actions
     */
    public function process_bulk_actions( $action, $reviews_list ) {
        switch ( $action ) {
            case 'add-featured' :
                foreach ( $reviews_list as $review_id ) {
                    update_post_meta( $review_id, YITH_YWAR_META_KEY_FEATURED, 1 );
                }

                break;

            case 'remove-featured' :
                foreach ( $reviews_list as $review_id ) {
                    update_post_meta( $review_id, YITH_YWAR_META_KEY_FEATURED, 0 );
                }

                break;

            case 'add-reply-blocked' :
                foreach ( $reviews_list as $review_id ) {
                    update_post_meta( $review_id, YITH_YWAR_META_STOP_REPLY, 1 );
                }

                break;

            case 'remove-reply-blocked' :
                foreach ( $reviews_list as $review_id ) {
                    update_post_meta( $review_id, YITH_YWAR_META_STOP_REPLY, 0 );
                }

                break;

        }
    }

    /**
     * Add custom action to table bulk actions
     */
    public function add_bulk_actions( $actions ) {

        $actions['add-featured']         = esc_html__( 'Add to featured', 'yith-woocommerce-advanced-reviews' );
        $actions['remove-featured']      = esc_html__( 'Remove from featured', 'yith-woocommerce-advanced-reviews' );
        $actions['add-reply-blocked']    = esc_html__( 'Block replies', 'yith-woocommerce-advanced-reviews' );
        $actions['remove-reply-blocked'] = esc_html__( 'Unblock replies', 'yith-woocommerce-advanced-reviews' );

        return $actions;
    }

    public function filter_review( $params, $arg ) {
        switch ( $arg ) {
            case 'inappropriate' :
                $params['meta_query'][] = array(
                    'key'     => YITH_YWAR_META_KEY_INAPPROPRIATE_COUNT,
                    'value'   => 0,
                    'compare' => '>',
                );

                break;

            case 'featured' :
                $params['meta_query'][] = array(
                    'key'     => YITH_YWAR_META_KEY_FEATURED,
                    'value'   => 1,
                    'compare' => '=',
                );

                break;

            case 'reply_blocked' :
                $params['meta_query'][] = array(
                    'key'     => YITH_YWAR_META_STOP_REPLY,
                    'value'   => 1,
                    'compare' => '=',
                );

                break;

            default :
                $params['meta_query'][] = array(
                    'key'     => $arg,
                    'value'   => 1,
                    'compare' => '=',
                );

                break;
        }

        return $params;
    }

    /**
     * Add additional views to review table
     *
     * @param $views current table views
     */
    public function add_review_table_views( $views ) {
        $views['inappropriate'] = esc_html__( 'Inappropriate content', 'yith-woocommerce-advanced-reviews' );
        $views['reply_blocked'] = esc_html__( 'Reply blocked', 'yith-woocommerce-advanced-reviews' );
        $views['featured']      = esc_html__( 'Featured', 'yith-woocommerce-advanced-reviews' );

        return $views;
    }

    public function advanced_reviews_report( $reports ) {
        $reports['yith_advanced_reviews'] = array(
            'title'   => esc_html__( 'Reviews', 'yith-woocommerce-advanced-reviews' ),
            'reports' => array(
                "most_commented" => array(
                    'title'       => esc_html__( 'By most commented', 'yith-woocommerce-advanced-reviews' ),
                    'description' => '',
                    'hide_title'  => true,
                    'callback'    => array( $this, 'get_report' ),
                ),
                "average_rating" => array(
                    'title'       => esc_html__( 'By average rate', 'yith-woocommerce-advanced-reviews' ),
                    'description' => '',
                    'hide_title'  => true,
                    'callback'    => array( $this, 'get_report' ),
                ),
                "min_rating"     => array(
                    'title'       => esc_html__( 'By min rate', 'yith-woocommerce-advanced-reviews' ),
                    'description' => '',
                    'hide_title'  => true,
                    'callback'    => array( $this, 'get_report' ),
                ),
                "max_rating"     => array(
                    'title'       => esc_html__( 'By max rate', 'yith-woocommerce-advanced-reviews' ),
                    'description' => '',
                    'hide_title'  => true,
                    'callback'    => array( $this, 'get_report' ),
                ),
            ),
        );

        return $reports;
    }

    public function get_report( $name ) {
        global $report_name;
        $report_name = sanitize_title( str_replace( '_', '-', $name ) );

        include_once( YITH_YWAR_TEMPLATES_DIR . '/reports/class.ywar-review-report.php' );

        $report = new YWAR_Review_Report( $name );
        $report->output_report();
    }

    /**
     * Set the review parent in case the review is a reply to another review
     *
     * @param $comment_parent the comment parent from the comment form
     */
    public function set_post_parent( $comment_parent ) {
        //  Find the parent review in case of a reply
        $review_parent_id = 0;
        if ( $comment_parent > 0 ) {
            //  Retrieve the post whose related comment id is the current  comment_parent
            $parent_review = $this->get_parent_review( $comment_parent );
            if ( count( $parent_review ) > 0 ) {
                return $parent_review[0]->ID;
            }
        }

        return $comment_parent;
    }

    /**
     * Retrieve the child review(replies) of a specific review
     *
     * @param $review the parent $review
     *
     * @return array
     */
    public function get_childs_review( $review ) {
        $product_id = get_post_meta( $review->ID, YITH_YWAR_META_KEY_PRODUCT_ID, true );

        $args = array(
            'numberposts'      => - 1,    //By default retrieve all reviews
            'offset'           => 0,
            'category'         => '',
            'category_name'    => '',
            'orderby'          => 'post_date',
            'order'            => 'DESC',
            'include'          => '',
            'exclude'          => '',
            'post_type'        => YITH_YWAR_POST_TYPE,
            'post_mime_type'   => '',
            'post_status'      => 'publish',
            'post_parent'      => $review->ID,
            'suppress_filters' => true,
            'meta_query'       => array(
                'relation' => 'AND',
                array(
                    'key'     => YITH_YWAR_META_KEY_PRODUCT_ID,
                    'value'   => $product_id,
                    'compare' => '=',
                ),
                array(
                    'key'     => YITH_YWAR_META_APPROVED,
                    'value'   => 1,
                    'compare' => '=',
                ),
            ),
        );

        return get_posts( $args );
    }

    /**
     * Retrieve the parent review starting from a WP_Comment object, using relation between them
     *
     * @param $comment_parent the WP_Comment parent for whose we are searching the related Advanced Reviews
     *
     * @return array
     */
    public function get_parent_review( $comment_parent ) {
        $args = array(
            'numberposts'      => 1,    //By default retrieve all reviews
            'offset'           => 0,
            'category'         => '',
            'category_name'    => '',
            'include'          => '',
            'exclude'          => '',
            'post_type'        => YITH_YWAR_POST_TYPE,
            'post_mime_type'   => '',
            'suppress_filters' => true,
            'meta_query'       => array(
                array(
                    'key'     => YITH_YWAR_META_COMMENT_ID,
                    'value'   => $comment_parent,
                    'compare' => '=',
                ),
            ),
        );

        return get_posts( $args );
    }

    /**
     * Show paginated reviews if the option is enabled
     *
     * @param $args current parameter list for get reviews
     */
    public function show_reviews_list_args( $args, $product_id ) {
        if ( ! $this->show_load_more ) {
            return $args;
        }

        if ( null == $args ) {
            $args = $this->default_query_args( $product_id );
        }

        //  Change numbers of reviews to show
        $args['numberposts'] = $this->reviews_to_show;

        return $args;
    }

    /**
     * Retrieve number of pages to shown when pagination is enabled
     *
     * @param WC_Product $product the product to use for calculate review pages
     */
    public function get_review_page_count( $product ) {
        if ( ! $this->show_load_more ) {
            return 1;
        }

        if ( $this->reviews_to_show < 1 ) {
            return 1;
        }

        $count = $this->get_reviews_count( yit_get_prop( $product, 'id') );

        return ceil( $count / $this->reviews_to_show );
    }

    /**
     * Check if pagination is needed for a product and show pagination link
     *
     * @param WC_Product $product
     */
    public function show_reviews_pagination( $product ) {
        if ( ! $this->show_load_more ) {

            return;
        }

        $current_page = get_query_var( "page" );
        if ( ! $current_page ) {
            $current_page = 1;
        }

        $max_page = $this->get_review_page_count( $product );
        if ( $current_page < $max_page ) {

            //  Open show_more div
            echo '<div class="ywar_show_more">';

            $return_path = '';
            if ( isset( $_POST["return_path"] ) ) {
                $return_path = $_POST["return_path"];
            } else {
                //  build url
                $args        = array(
                    'data-order'      => 'recent',
                    'data-page'       => 1,
                    'data-id_product' => yit_get_prop( $product, 'id' ),
                    'data-stars'      => 0,
                );
                $return_path = esc_url( add_query_arg( $args, get_permalink( yit_get_prop( $product, 'id' ) ) ) );

            }

            //  if set, show reviews ordered by date or by popularity
            $order = 'recent';
            if ( isset( $_POST['order'] ) ) {

                $order = $_POST['order'];

                if ( 'recent' == $order ) {
                    $comments_parameters['order'] = 'DESC';
                } else {
                    $this->most_recent = true;

                    $comments_parameters['meta_key'] = 'upvotes_count';
                    $comments_parameters['orderby']  = 'meta_value_num';
                }
            }

            $stars = 0;
            //  if "stars" post var is set, show results filtered by stars rating...
            if ( isset( $_POST['stars'] ) ) {
                $stars = $_POST['stars'];
                //  check if stars value is correct, with range from 1 to 5
                if ( ( ! isset( $stars ) ) || ( ! is_numeric( $stars ) ) || ( intval( $stars ) < 0 ) || ( intval( $stars ) > 5 ) ) {
                    wp_send_json( array(
                        - 1,
                        esc_html__( 'Impossible to filter by rate', 'yith-woocommerce-advanced-reviews' )
                    ) );
                }
                $stars = intval( $stars );
                if ( $stars > 0 ) {
                    $comments_parameters['meta_query'] = array( array( 'key' => 'rating', 'value' => $stars ) );
                }
            }

            echo $this->show_load_more_section( $return_path, $order, $current_page, yit_get_prop( $product, 'id' ), $stars );

            //Close show_more div
            echo '</div>';
        }
    }

    /**
     * Add CSS classes to review table row
     */
    public function add_table_css_class( $classes, $post_id ) {
        $stop_reply = get_post_meta( $post_id, YITH_YWAR_META_STOP_REPLY, true );

        if ( 1 == $stop_reply ) {
            $classes[] = "stop-reply";
        }

        return $classes;
    }

    /**
     * Intercept review action url and do the requested job
     */
    public function update_review_status() {

        if ( ! isset( $_GET["review_id"] ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        $review_id = $_GET["review_id"];

        $current_filter = current_filter();

        switch ( $current_filter ) {
            case "admin_action_{$this->open_reply_action}" :
                update_post_meta( $review_id, YITH_YWAR_META_STOP_REPLY, 0 );
                break;

            case "admin_action_{$this->stop_reply_action}" :
                update_post_meta( $review_id, YITH_YWAR_META_STOP_REPLY, 1 );
                break;

            case "admin_action_{$this->add_featured_status_action}" :
                update_post_meta( $review_id, YITH_YWAR_META_KEY_FEATURED, 1 );
                break;

            case "admin_action_{$this->remove_featured_status_action}" :
                update_post_meta( $review_id, YITH_YWAR_META_KEY_FEATURED, 0 );
                break;

        }
        wp_redirect( $_SERVER['HTTP_REFERER'] );
    }

    /**
     * Update current query, setting sorting by custom meta_key
     */
    public function advanced_reviews_column_sort( $params, $orderby ) {
        switch ( $orderby ) {
            case $this->custom_column_upvoting :
                $params['meta_key'] = YITH_YWAR_META_UPVOTES_COUNT;
                $params['orderby']  = 'meta_value_num';
                break;

            case $this->custom_column_downvoting :
                $params['meta_key'] = YITH_YWAR_META_DOWNVOTES_COUNT;
                $params['orderby']  = 'meta_value_num';
                break;

            case $this->custom_column_inappropriate :
                $params['meta_key'] = YITH_YWAR_META_KEY_INAPPROPRIATE_COUNT;
                $params['orderby']  = 'meta_value_num';
                break;

            case $this->custom_column_featured :
                $params['meta_key'] = YITH_YWAR_META_KEY_FEATURED;
                $params['orderby']  = 'meta_value_num';
                break;

            case $this->custom_column_reply_status :
                $params['meta_key'] = YITH_YWAR_META_STOP_REPLY;
                $params['orderby']  = 'meta_value_num';
                break;
        }

        return $params;
    }

    /**
     * Set some custom column as sortable
     */
    public function sortable_custom_columns( $columns ) {
        $columns[ $this->custom_column_upvoting ]      = array( $this->custom_column_upvoting, false );
        $columns[ $this->custom_column_downvoting ]    = array( $this->custom_column_downvoting, false );
        $columns[ $this->custom_column_inappropriate ] = array( $this->custom_column_inappropriate, false );
        $columns[ $this->custom_column_featured ]      = array( $this->custom_column_featured, false );
        $columns[ $this->custom_column_reply_status ]  = array( $this->custom_column_reply_status, false );

        return $columns;
    }

    /**
     * Add custom column to advanced reviews post table
     *
     * @param $columns columns shown
     */
    public function add_custom_column( $columns ) {
        $columns[ $this->custom_column_upvoting ]      = '';//esc_html__( 'Upvote count', 'yith-woocommerce-advanced-reviews' );
        $columns[ $this->custom_column_downvoting ]    = '';//esc_html__( 'Downvote count', 'yith-woocommerce-advanced-reviews' );
        $columns[ $this->custom_column_inappropriate ] = esc_html__( 'Inappropriate', 'yith-woocommerce-advanced-reviews' );
        $columns[ $this->custom_column_featured ]      = esc_html__( 'Featured', 'yith-woocommerce-advanced-reviews' );
        $columns[ $this->custom_column_reply_status ]  = esc_html__( 'Reply blocked', 'yith-woocommerce-advanced-reviews' );

        return $columns;
    }

    /**
     * Fill data for each custom column for Advanced Reviews post type
     *
     * @param $column_name    column to be filled
     * @param $review_id      review to be displayed
     */
    public function fill_advanced_reviews_columns( $column_name, $review_id ) {
        switch ( $column_name ) {
            case $this->custom_column_upvoting:
                $vote = get_post_meta( $review_id, YITH_YWAR_META_UPVOTES_COUNT, true );

                echo $vote ? $vote : 0;
                break;

            case $this->custom_column_downvoting:
                $vote = get_post_meta( $review_id, YITH_YWAR_META_DOWNVOTES_COUNT, true );

                echo $vote ? $vote : 0;

                break;
            case $this->custom_column_inappropriate:
                $segnalation = get_post_meta( $review_id, YITH_YWAR_META_KEY_INAPPROPRIATE_COUNT, true );

                $css_class = "inappropriate-review";
                if ( $segnalation ) {
                    $css_class .= " flag";
                }

                echo sprintf( '<img class="%s" title="%s" src="%s">',
                    $css_class,
                    esc_html__( 'Report for inappropriate content', 'yith-woocommerce-advanced-reviews' ),
                    $segnalation ? YITH_YWAR_ASSETS_URL . '/images/caution.png' : YITH_YWAR_ASSETS_URL . '/images/caution-disabled.png' );

                echo sprintf( '<span class="%s" title="%s">%s</span>',
                    $css_class,
                    esc_html__( 'Report for inappropriate content', 'yith-woocommerce-advanced-reviews' ),
                    $segnalation ? $segnalation : 0 );

                break;

            case $this->custom_column_featured :
                $featured = get_post_meta( $review_id, YITH_YWAR_META_KEY_FEATURED, true );

                $css_class = "featured-review";
                if ( $featured ) {
                    $css_class .= " selected";
                }

                echo sprintf( '<a class="%s" href="%s" title="%s" data-featured-status="%d" data-review-id="%d"></a>',
                    $css_class,
                    $featured ? esc_url( $this->remove_featured_review_url( $review_id ) ) : esc_url( $this->add_featured_review_url( $review_id ) ),
                   esc_html__( 'Select/Deselect this as a featured review', 'yith-woocommerce-advanced-reviews' ),
                    $featured ? 1 : 0,
                    $review_id );

                break;

            case $this->custom_column_reply_status :
                $stop_reply = get_post_meta( $review_id, YITH_YWAR_META_STOP_REPLY, true );

                $css_class = "reply-status";
                if ( $stop_reply ) {
                    $css_class .= " closed";
                }

                echo sprintf( '<a class="%s" href="%s" title="%s" data-stop-reply-status="%d" data-review-id="%d" rel="nofollow"></a>',
                    $css_class,
                    $stop_reply ? esc_url( $this->open_reply_review_url( $review_id ) ) : esc_url( $this->stop_reply_review_url( $review_id ) ),
                   esc_html__( 'Select/Deselect to block any reply of this review', 'yith-woocommerce-advanced-reviews' ),
                    $stop_reply ? 1 : 0,
                    $review_id );


                break;
        }
    }

    /**
     * Retrieve featured reviews for a product
     *
     * @param int $product_id product id for whose retrieve the reviews
     * @param int $count      how much reviews to be returned(picked randomly from all featured reviews). If not set, return all results.
     *
     * @return array
     */
    public function get_featured_product_reviews( $product_id, $count = 0 ) {
        $result = array();

        $args                 = $this->default_query_args( $product_id );

        $args['meta_query'][] = array(
            'key'     => YITH_YWAR_META_KEY_FEATURED,
            'value'   => 1,
            'compare' => '=',
        );

        $result = get_posts( $args );

        return $result;
    }

    /**
     * Build an "approve" action url
     *
     * @param $review_id the review on which the action is performed
     *
     * @return string|void action url
     */
    public function stop_reply_review_url( $review_id ) {
        return $this->review_action_url( $this->stop_reply_action, $review_id );
    }

    /**
     * Build an "unapprove" action url
     *
     * @param $review_id the review on which the action is performed
     *
     * @return string|void action url
     */
    public function open_reply_review_url( $review_id ) {
        return $this->review_action_url( $this->open_reply_action, $review_id );
    }

    /**
     * Build an "set featured" action url
     *
     * @param $review_id the review on which the action is performed
     *
     * @return string|void action url
     */
    public function add_featured_review_url( $review_id ) {
        return $this->review_action_url( $this->add_featured_status_action, $review_id );
    }

    /**
     * Build an "unset featured" action url
     *
     * @param $review_id the review on which the action is performed
     *
     * @return string|void action url
     */
    public function remove_featured_review_url( $review_id ) {
        return $this->review_action_url( $this->remove_featured_status_action, $review_id );
    }

    /**
     * Set the "approve" status for newly created reviews
     *
     * @param $approved current status
     *
     * @return string|void new status
     */
    public function set_approve_status( $approved ) {
        //approve if it doesn't need moderation before being shown or the current user has admin role
        return ! $this->review_moderation || current_user_can( 'manage_woocommerce' );
    }

    /**
     * Initialize plugin options
     *
     * @since  1.0
     * @access public
     * @return void
     * @author Lorenzo Giuffrida
     */
    public function initialize_settings() {

        parent::initialize_settings();

        $this->reviews_to_show = get_option( 'ywar_review_per_page' );
        update_option( 'default_comments_page', 'oldest' );

        $this->load_more_text       = get_option( 'ywar_load_more_text' );
        $this->enable_review_rating = get_option( 'woocommerce_enable_review_rating' ) === 'yes';
        $this->show_peoples_vote    = get_option( 'ywar_show_peoples_vote' ) === 'yes';
        $this->enable_review_vote   = get_option( 'ywar_enable_vote_system' ) === 'yes';
        $this->reviews_on_dialog    = get_option( 'ywar_reviews_dialog' ) === 'yes';

        $this->load_more_type = get_option( 'ywar_show_load_more' );
        switch ( $this->load_more_type ) {
            case 1 :
                $this->show_load_more = false;
                break;
            case 2:
            case 3:
                //  if a "load more" should be shown, check if the user set a number of reviews per page to be shown
                $this->show_load_more = ( $this->reviews_to_show > 0 );
                break;
        }

        $reply_option = get_option( 'ywar_reply_to_review' );
        //  $reply_option may be :
        //  1: Noone can reply
        //  2: only administrators can reply
        //  3: everyone can reply
        switch ( $reply_option ) {
            case 1 :
                $this->enable_reply = false;
                break;
            case 2 :
                if ( current_user_can( 'manage_woocommerce' ) ) {
                    /* A user with admin privileges */
                    $this->enable_reply = true;
                }
                break;
            case 3 :
                $this->enable_reply = true;
                break;
        }

        $this->enable_reply = apply_filters( 'ywar_enable_reply_for_review_filter', $this->enable_reply );

        $report_inappropriate_option = get_option( 'ywar_report_inappropriate_review' );
        //  0 : Don't show the button
        //  1 : only registered users can report an inappropriate review
        //  2 : everyone can report an inappropriate review
        switch ( $report_inappropriate_option ) {
            case 0 :
                //Don't show the button
                $this->enable_report_inappropriate = false;
                break;
            case 1:
                //only registered users can report an inappropriate review
                $this->enable_report_inappropriate = is_user_logged_in();
                break;
            case 2 :
                //  everyone can report an inappropriate review
                $this->enable_report_inappropriate = true;
                break;
        }

        $this->hide_inappropriate_review_threshold = get_option( 'ywar_hide_inappropriate_review' );
        $this->hide_when_inappropriate             = is_numeric( $this->hide_inappropriate_review_threshold ) && ( $this->hide_inappropriate_review_threshold > 0 );
        $this->featured_reviews_count              = get_option( 'ywar_featured_review' );
        $this->show_featured_reviews               = $this->featured_reviews_count > 0;
        $this->enable_guest_vote                   = ( 'yes' == get_option( 'ywar_enable_visitors_vote' ) ) ? true : false;
        $this->review_moderation                   = ( 'yes' == get_option( 'ywar_review_moderation' ) ) ? true : false;
        $this->limit_multiple_review               = ( 'yes' == get_option( 'ywar_limit_multiple_review' ) ) ? true : false;
        $this->allow_edit_review                   = ( 'yes' == get_option( 'ywar_edit_reviews' ) ) ? true : false;

        /**
         * Set reCaptcha settings
         */

        $this->recaptcha_enabled   = get_option( "ywar_enable_recaptcha", "no" );
        $this->recaptcha_sitekey   = get_option( "ywar_recaptcha_site_key" );
        $this->recaptcha_secretkey = get_option( "ywar_recaptcha_secret_key" );

    }

    /**
     * Check if the plugin has limited the customer to only one review and the WooCommerce settings for "only verified"
     * user is set.
     *
     * @return bool users can submit no more than one review
     */
    public function is_reviews_limited() {
        return $this->limit_multiple_review && get_option( 'woocommerce_review_rating_verification_required' ) === 'yes';
    }

    /**
     *  Show links to let users interact with a review
     *
     * @param WP_Post $review the product review on which write a reply
     *
     * @return string
     * */
    public function get_review_actions_section( $review ) {
        $result_div = '<div class="reply review-actions">';

        //  Check if should show a "report as inappropriate content" button
        if ( $this->enable_report_inappropriate && ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) ) {
            global $current_user;

            $inappropriate_segnalations = get_post_meta( $review->ID, YITH_YWAR_META_KEY_INAPPROPRIATE_LIST, true );

            $show_inappropriate_link = false;

            if ( ( $current_user->ID > 0 ) && ( ! isset( $inappropriate_segnalations[ $current_user->ID ] ) ) ) {
                $show_inappropriate_link = true;
            } else {
                if ( ! isset( $inappropriate_segnalations[ $this->get_ip_address() ] ) ) {
                    $show_inappropriate_link = true;
                }
            }
            $product_id = get_post_meta( $review->ID, YITH_YWAR_META_KEY_PRODUCT_ID, true );

            $result_div .= '<div class="review-inappropriate">';
            if ( $show_inappropriate_link ) {
                $result_div .= '<a class="review-inappropriate" href="' . esc_url( add_query_arg( array( "flag" => $review->ID ), get_permalink( $product_id ) ) ) . '" title="' .esc_html__( 'Flag as inappropriate content', 'yith-woocommerce-advanced-reviews' ) . '" data-id_review="' . $review->ID . '" rel="nofollow"></a>';
            }
            $result_div .= '</div>';
        }

        $result_div .= $this->get_reply_section( $review );

        $result_div .= '</div>';

        return $result_div;
    }

    /**
     * Check if current user can submit a review so the form could be shown
     *
     * @param $product_id int current product id
     * @param $review_id  int Optional review id if the review is a reply to a previous review
     *
     * @return bool
     */
    public function user_can_submit_review( $product_id, $review_id = 0 ) {

        /** Let's third part filter this function */
        if ( apply_filters( 'yith_advanced_reviews_user_can_submit_review_use_custom', false, $product_id, $review_id ) ) {
            return apply_filters( 'yith_advanced_reviews_user_can_submit_review_custom_value', false, $product_id, $review_id );
        }

        $can_submit =
            ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' ||
                wc_customer_bought_product( '', get_current_user_id(), $product_id ) || current_user_can('manage_woocommerce') ||
                apply_filters( 'yith_advanced_reviews_on_product_review_shown', false, $product_id ) ) &&
            ! apply_filters( 'yith_advanced_reviews_on_product_review_shown_denied', false, $product_id, $review_id );

        return $can_submit;
    }

    /**
     * Retrieve HTML content for reply to review.
     *
     * @param $comment_id int
     * @param $review     WP_Post
     * @param $product_id int
     *
     * @return bool|string
     */
    function get_review_reply_link( $comment_id, $review, $product_id ) {
        //  Comments are closed for this product, so reviews...
        if ( ! comments_open( $product_id ) ) {
            return false;
        }

        $result = '';

        //  Maybe submitting reviews is disabled for current user, so the same is for replies
        if ( apply_filters( 'yith_advanced_reviews_allow_reply_condition', false, $product_id ) || current_user_can('manage_woocommerce') || $this->user_can_submit_review( $product_id, $review->ID ) ) {
            $result .= sprintf( "<a class='comment-reply-link button' href='%s' onclick='%s' aria-label='%s' rel='nofollow'>%s</a>",
                esc_url( add_query_arg( 'replytocom', $comment_id, get_permalink( $product_id ) ) ) . "#" . 'respond',
                $this->get_form_on_click_script( $review ),
               esc_html__( 'Reply to a previous review', 'yith-woocommerce-advanced-reviews' ),
               esc_html__( 'Reply', 'yith-woocommerce-advanced-reviews' )
            );
        }


        return $result;
    }

    /**
     * Get javascript code to deal with comment form while editing or replying to a review
     *
     * @param $review
     *
     * @return string
     */
    public function get_form_on_click_script( $review ) {
        $product_id = get_post_meta( $review->ID, YITH_YWAR_META_KEY_PRODUCT_ID, true );
        $comment_id = get_post_meta( $review->ID, YITH_YWAR_META_COMMENT_ID, true );

        return sprintf( 'return addComment.moveForm( "%1$s", "%2$s", "%3$s", "%4$s" )',
            "comment-{$review->ID}", $comment_id, 'review_form', $product_id
        );
    }

    /**
     * Return html content for the reply section
     *
     * @param $review WP_Post the product review on which write a reply
     *
     * @return string|void
     * @author Lorenzo Giuffrida
     * @since  1.0.0
     */
    public function get_reply_section( $review ) {
        $reply_html = '';

        //  show reply link only on front end
        if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

            $product_id = get_post_meta( $review->ID, YITH_YWAR_META_KEY_PRODUCT_ID, true );
            $comment_id = get_post_meta( $review->ID, YITH_YWAR_META_COMMENT_ID, true );

            //  Build the reply section if replies are enabled
            if ( $this->enable_reply ) {


                //  Only for top level reviews, add reply to link
                if ( 0 == $review->post_parent ) {

                    //  Check if review is blocked for user's replies
                    $no_reply = get_post_meta( $review->ID, YITH_YWAR_META_STOP_REPLY, true );

                    if ( 1 != $no_reply ) {

                        $target = '';

                        $reply_html .= $this->get_review_reply_link( $comment_id, $review, $product_id );

                        if ( is_admin() ) {
                            $target = 'target="_blank"';
                        }

                        //  only authorizated user can stop replies
                        if ( current_user_can( 'manage_woocommerce' ) ) {
                            $reply_html .= '<a href="' . esc_url( $this->stop_reply_review_url( $review->ID ) ) . '" ' . $target . ' class="stop-reply button" title="' .esc_html__( 'This review cannot receive replies', 'yith-woocommerce-advanced-reviews' ) . '" data-id_review="' . $review->ID . '">' .esc_html__( "Block reply", 'yith-woocommerce-advanced-reviews' ) . '</a>';
                        }
                    } else {
                        $reply_html .= '<span style="color:red">' .esc_html__( "This review cannot receive comments.", 'yith-woocommerce-advanced-reviews' ) . '</span>';

                        if ( current_user_can( 'manage_woocommerce' ) ) {
                            $reply_html .= '<a href="' . esc_url( $this->open_reply_review_url( $review->ID ) ) . '" class="open-reply button" title="' .esc_html__( "Allow replies", 'yith-woocommerce-advanced-reviews' ) . '" data-id_review="' . $review->ID . '">' .esc_html__( "Allow reply", 'yith-woocommerce-advanced-reviews' ) . '</a>';
                        }
                    }


                }
            }

            $ywar_rating = ( isset( $review->_ywar_rating ) ? $review->_ywar_rating : $review->meta_value );

            if ( $this->user_can_edit_review( $review ) ) {
                $reply_html .= sprintf( "<a class='comment-edit-link button' href='%s' onclick='%s' aria-label='%s' data-review-id='%d' data-rating='%d' data-parent='%d' rel='nofollow'>%s</a>",
                    esc_url( add_query_arg( 'replytocom', $comment_id, get_permalink( $product_id ) ) ) . "#" . 'respond',
                    $this->get_form_on_click_script( $review ),
                   esc_html__( 'Edit to a previous review', 'yith-woocommerce-advanced-reviews' ),
                    $review->ID,
                    $ywar_rating,
                    $review->post_parent,
                   esc_html__( 'Edit', 'yith-woocommerce-advanced-reviews' )
                );
            }

            return '<div class="reply">' . $reply_html . '</div>';
        }

        return $reply_html;
    }

    /**
     * Display a customized comment content
     *
     * @param   WP_Post $review
     *
     * @return  string  customized comment content
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    public function show_expanded_review_content( $review ) {
        if ( ! is_product() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            return $review->post_content;
        }

        global $current_user;
        global $is_modal;

        $product_id               = get_post_meta( $review->ID, YITH_YWAR_META_KEY_PRODUCT_ID, true );
        $thumbnail_div            = '';
        $div_yes_not              = '';
        $div_report_inappropriate = '';
        $review_title             = '';
        $review_content           = '<span class="review_content">' . nl2br( $review->post_content ) . '</span>';

        if ( $this->enable_title && ( ! empty( $review->post_title ) ) ) {
            //  Add review title before review content text
            $review_title = '<span class="review_title">' . esc_attr( $review->post_title ) . '</span>';
        }

        if ( $this->enable_attachments ) {

            $review_thumbnails = get_post_meta( $review->ID, YITH_YWAR_META_THUMB_IDS, true );

            if ( isset ( $review_thumbnails ) && is_array( $review_thumbnails ) && ( count( $review_thumbnails ) > 0 ) ) {

                $thumbnail_div = '<div class="ywar-review-thumbnails review_thumbnail horizontalRule">';

                foreach ( $review_thumbnails as $thumb_id ) {

                    if ( is_int($thumb_id)) {

                        $file_url = wp_get_attachment_url($thumb_id);

                        $image_thumb = wp_get_attachment_image_src($thumb_id, array(100, 100), true);

                        $thumbnail_div .= "<a href='$file_url' data-rel=\"prettyPhoto[review-gallery-{$review->ID}]\"><img class=\"ywar_thumbnail\" src='{$image_thumb[0]}' width='70px' height='70px'></a>";
                    }
                    else{
                        $thumbnail_div = "<a></a>";
                    }
                }
                $thumbnail_div .= '</div>';

            }
        }

        //  Check if review vote system is enabled, only on frontend product page
        if ( $this->enable_review_vote && ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) ) {
            //  set specific css class for previously rated review
            $yes_class = 'ywar_votereview yes';
            $no_class  = 'ywar_votereview not';

            $vote_meta = get_post_meta( $review->ID, YITH_YWAR_META_VOTES, true );

            //  Check previous vote for users or guests...
            $current_user_vote = '';
            if ( $current_user->ID > 0 ) {
                if ( isset( $vote_meta[ $current_user->ID ] ) ) {
                    $current_user_vote = $vote_meta[ $current_user->ID ];
                }
            } else {
                if ( isset( $vote_meta[ $this->get_ip_address() ] ) ) {
                    $current_user_vote = $vote_meta[ $this->get_ip_address() ];
                }
            }

            if ( ! empty( $current_user_vote ) ) {
                if ( 1 == $current_user_vote ) {
                    $yes_class .= ' vote_selected';
                } else {
                    if ( - 1 == $current_user_vote ) {
                        $no_class .= ' vote_selected';
                    }
                }
            }

            //  build url to be used for voting a review
            $voting_yes_url = $this->get_voting_url( $review, 1 ); //  direct url
            $voting_no_url  = $this->get_voting_url( $review, - 1 ); //  direct url

            //  Add nonce security to these url
            $voting_yes_url = wp_nonce_url( $voting_yes_url, $this->get_wp_nonce_text( $product_id ) );
            $voting_no_url  = wp_nonce_url( $voting_no_url, $this->get_wp_nonce_text( $product_id ) );

            //  is user is not logged in and visitors can't vote, pass through login page before going to voting url
            if ( ! is_user_logged_in() && ! $this->enable_guest_vote ) {

                $voting_yes_url = apply_filters( 'yith_ywar_login_url', wp_login_url( $voting_yes_url ), $voting_yes_url, $review );
                $voting_no_url  = apply_filters( 'yith_ywar_login_url', wp_login_url( $voting_no_url ), $voting_no_url, $review );
            }

            $div_yes_not = '<div class="review_vote">
<span class="review_helpful">' .esc_html__( "Was this review helpful to you?", 'yith-woocommerce-advanced-reviews' ) . '</span>
<a id="vote_yes_' . $review->ID . '" class="' . $yes_class . '" href="' . $voting_yes_url . '" data-vote_review="1" data-id_review="' . $review->ID . '" title="' .esc_html__( "This reviews is helpful", 'yith-woocommerce-advanced-reviews' ) . '" rel="nofollow"></a>
<a id="vote_no_' . $review->ID . '" class="' . $no_class . '" href="' . $voting_no_url . '" data-vote_review="-1" data-id_review="' . $review->ID . '" title="' .esc_html__( "This reviews is not helpful", 'yith-woocommerce-advanced-reviews' ) . '" rel="nofollow"></a>';

            $div_helpfull_msg = '<span class="ywar_review_helpful">' . $this->get_review_is_helpful_text( $review->ID ) . '</span>';

            $div_yes_not .= $div_helpfull_msg;
            $div_yes_not .= '</div>';
        }


        /**
         * For modal view of reviews, disable reply section
         */
        $actions_section = '';
        if ( ! $is_modal ) {
            $actions_section = $this->get_review_actions_section( $review );
        }
        $review_content_elements = $review_title . $review_content . $thumbnail_div . $div_yes_not . $actions_section;

        return apply_filters( "yith_advanced_reviews_review_content_elements", $review_content_elements, $review_title, $review_content, $thumbnail_div, $div_yes_not, $actions_section, $review );
    }

    /**
     * Show links for ordering by most helpful or most recent reviews
     *
     * @param $product_id
     */
    public function show_filter_helpful_recent( $product_id, $review_stats ) {
        $message_recent_reviews  =esc_html__( "Most recent reviews", 'yith-woocommerce-advanced-reviews' );
        $message_helpful_reviews =esc_html__( "Most helpful reviews", 'yith-woocommerce-advanced-reviews' );

        $message_recent_class  = "ywar_filter_order";
        $message_helpful_class = "ywar_filter_order";

        if ( get_option('ywar_featured_review_tab_first', 'no') == 'yes' ) {
            $order = 'helpful';

            if ( 'helpful' === $order ) {
                $message_helpful_class .= " active";
            } else {
                $message_recent_class .= " active";
            }
        } else {
            //  By default set active css class to most recent reviews link
            $message_recent_class .= " active";
        }

        if ( apply_filters('yith_ywar_show_filter_helpful_recent', true, $review_stats) && get_option( 'ywar_enable_vote_system', 'yes' ) == 'yes' && $review_stats['total'] > 0) {
            echo '<div id="reviews_order">
				<a style="border-bottom: 2px solid ' . get_option('ywar_tab_bottom_border_color') . '" id="most_recent_reviews" title="' . $message_recent_reviews . '" href="' . add_query_arg(array("order" => "recent")) . '" class="' . $message_recent_class . '" data-id_product="' . $product_id . '" data-order="recent" rel="nofollow">' . $message_recent_reviews . '</a>
				<a style="border-bottom: 2px solid ' . get_option('ywar_tab_bottom_border_color') . '" id="most_helpful_reviews" title="' . $message_helpful_reviews . '" href="' . add_query_arg(array("order" => "helpful")) . '" class="' . $message_helpful_class . '" data-id_product="' . $product_id . '" data-order="helpful" rel="nofollow">' . $message_helpful_reviews . '</a>
			</div>';
        }
        else{
            echo '<div id="reviews_order" class="reviews_order_no_vote">
				<a id="ywar_filter_no_vote" href="' . add_query_arg(array("order" => "recent")) . '" class="ywar_filter_order active" data-id_product="' . $product_id . '" data-order="recent" rel="nofollow"></a>
			</div>';
        }

    }

    /**
     * Add link to filter reviews.
     *
     * Add a link on every summary review item, to be used for filtering review based on users rating
     *
     * @param   int $stars      number of stars for current item.
     * @param   int $product_id product id the review is referred to.
     *
     * @return  void
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    public function add_filtering_link( $stars, $product_id, $perc ) {
        if ( isset( $perc ) && ( $perc > 0 ) ) {
            $query_url = apply_filters('yith_ywar_add_filtering_link',add_query_arg( array( "stars" => $stars ) ),$stars,$product_id );

            echo '<a title="' . sprintf( _n( 'Reviews with %s star', 'Reviews with %s stars', $stars, 'yith-woocommerce-advanced-reviews' ), $stars ) . '" href="' . $query_url . '" class="ywar_filter_reviews" data-dialog="' . $this->reviews_on_dialog . '" data-id_product="' . $product_id . '" data-stars="' . $stars . '">';
        }
    }

    public function close_filtering_link( $stars, $product_id, $perc ) {
        if ( isset( $perc ) && ( $perc > 0 ) ) {
            echo '</a>';
        }
    }

    /**
     * Add an empty div to be used to contain dynamically created review header
     *
     * @return void
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    public function add_review_header( $review_stats ) {
        if ( ! isset( $_GET['stars'] ) ) {
            return;
        }

        if ( ! is_numeric( $_GET['stars'] ) ) {
            return;
        }

        $stars = intval( $_GET['stars'] );
        global $product;

        if ( $this->enable_review_rating && ( $count = $product->get_rating_count() ) ) {
            $str_stars         = _n( 'star', 'stars', $stars, 'yith-woocommerce-advanced-reviews' );
            $reviews_list_text = sprintf(esc_html__( '%d reviews with rating of %s %s (%s of %s)', 'yith-woocommerce-advanced-reviews' ), $review_stats[ $stars ], $stars, $str_stars, $review_stats[ $stars ], $review_stats['total'] );

            echo '<H3 class="ywar_review_list">' . $reviews_list_text . '</H3><a href="' . remove_query_arg( "stars" ) . '" class="ywar_filter_reviews" data-id_product="' . yit_get_prop( $product, 'id' ) . '" data-stars="0">' .esc_html__( '(Show unfiltered results)', 'yith-woocommerce-advanced-reviews' ) . '</a><hr>';
        }
    }

    /**
     * Register plugins for activation tab
     *
     * @return void
     * @since    2.0.0
     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
     */
    public function register_plugin_for_activation() {
        if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
            require_once YITH_YWAR_DIR . '/plugin-fw/licence/lib/yit-licence.php';
            require_once YITH_YWAR_DIR . '/plugin-fw/licence/lib/yit-plugin-licence.php';
        }
        YIT_Plugin_Licence()->register( YITH_YWAR_INIT, YITH_YWAR_SECRET_KEY, YITH_YWAR_SLUG );
    }

    /**
     * Register plugins for update tab
     *
     * @return void
     * @since    2.0.0
     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
     */
    public function register_plugin_for_updates() {
        if ( ! class_exists( 'YIT_Upgrade' ) ) {
            require_once 'plugin-fw/lib/yit-upgrade.php';
        }
        YIT_Upgrade()->register( YITH_YWAR_SLUG, YITH_YWAR_INIT );
    }

    /**
     * Add scripts
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    public function enqueue_resource_frontend() {

        if ( ! is_product() && ! apply_filters( 'yith_advanced_reviews_enqueue_public_resources', false ) ) {
            return;
        }

        //  register and enqueue ajax calls related script file
        wp_register_script( "ywar-attachments-script",
            yith_maybe_script_minified_path( YITH_YWAR_URL . 'assets/js/ywar-attachments' ), array(
                'jquery',
                'comment-reply',
            ), false, true );

        wp_localize_script( 'ywar-attachments-script',
            'attach',
            array(
                'limit_multiple_upload'         => $this->attachments_limit,
                'too_many_attachment_selected'  => _x( 'Too many files selected!', 'The user tried to upload more files than allowed.', 'yith-woocommerce-advanced-reviews' ),
                'attachments_failed_validation' => _x( 'One or more file was not accepted because do not match the expected requirements', 'The user tried to upload files with extension or size not allowed.', 'yith-woocommerce-advanced-reviews' ),
                'no_attachment_selected'        => _x( 'No Files Selected', 'No attachment is selected', 'yith-woocommerce-advanced-reviews' ),
                'allowed_extensions'            => strtolower( $this->attachment_allowed_type ),
                'allowed_max_size'              => $this->attachment_max_size,
            ) );

        wp_enqueue_script( 'ywar-attachments-script' );

        wp_enqueue_style( 'ywar-frontend', YITH_YWAR_ASSETS_URL . '/css/ywar-frontend.css' );

        global $post;

        //  register and enqueue ajax calls related script file
        wp_register_script( "ywar-script",
            apply_filters( 'yith_wc_advance_reviews_ywar_script', yith_maybe_script_minified_path( YITH_YWAR_URL . 'assets/js/ywar-script' ) ),
            array( 'jquery', 'wc-single-product' ),
            YITH_YWAR_VERSION,
            true );

        wp_localize_script( 'ywar-script',
            'ywar',
            array(
                'nonce_value'           => wp_create_nonce( $this->get_wp_nonce_text( $post->ID ) ),
                'review_section_anchor' => apply_filters( 'yith_ywar_review_section_id', '#reviews_summary' ),
                'loader'                => apply_filters( 'yith_advanced_reviews_loader_gif', YITH_YWAR_ASSETS_URL . '/images/loading.gif' ),
                'recaptcha'             => $this->recaptcha_enabled,
                'empty_review_content'  =>esc_html__( 'Please, write a review', 'yith-woocommerce-advanced-reviews' ),
                'missing_rating_review' =>esc_html__( 'Please, select a rating before submitting your review', 'yith-woocommerce-advanced-reviews' ),
                'edit_review_title'     =>esc_html__( 'Edit review', 'yith-woocommerce-advanced-reviews' ),
                'reply_to_review_title' =>esc_html__( 'Reply to review', 'yith-woocommerce-advanced-reviews' ),
                'add_review_title'      =>esc_html__( 'Add a review', 'yith-woocommerce-advanced-reviews' ),
                'is_rating_required'    => ( 'yes' === get_option( 'woocommerce_enable_review_rating' ) ) &&
                    ( 'yes' === get_option( 'woocommerce_review_rating_required' ) ),
                'tab_selector'          => get_option( 'ywar_tab_selector', '#tab-reviews' ),
            ) );

        if ( $this->recaptcha_enabled === 'yes' ) {
            //  Enqueue reCaptcha script on need
            wp_enqueue_script(
                "ywar-recaptcha",
                '//www.google.com/recaptcha/api.js',
                array(),
                false,
                true );

            $localize_params['recaptcha_sitekey'] = $this->recaptcha_sitekey;
        }

        wp_enqueue_script( 'ywar-script' );

        //  Add prettyphoto functionalities
        $assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

        wp_enqueue_script(
            'prettyPhoto',
            $assets_path . 'js/prettyPhoto/jquery.prettyPhoto.min.js',
            array( 'jquery' ),
            '3.1.5',
            true );

        wp_enqueue_script( 'prettyPhoto-init',
            $assets_path . 'js/prettyPhoto/jquery.prettyPhoto.init.min.js',
            array(
                'jquery',
                'prettyPhoto',
            ),
            WC_VERSION,
            true );

        wp_enqueue_style( 'woocommerce_prettyPhoto_css', $assets_path . 'css/prettyPhoto.css' );

    }

    /**
     * Change the featured status for a review, for ajax calls
     */
    public function change_featured_status_callback() {
        $review_id       = intval( $_POST['review_id'] );
        $featured_status = intval( $_POST['featured_status'] );

        //  toggle current featured status
        update_post_meta( $review_id, YITH_YWAR_META_KEY_FEATURED, ! $featured_status );
        wp_send_json( array( "value" => $featured_status ? 0 : 1 ) );   //return toggled status
    }

    /**
     * Check it current user can edit reviews
     *
     * @return bool
     * @author Lorenzo Giuffrida
     * @since  1.0.0
     */
    public function reviews_edit_enabled() {
        $current_user_id = get_current_user_id();

        //  Only logged in user can edit reviews
        if ( ! $current_user_id ) {

            return false;
        }

        //  Admins can always edit reviews
        if ( current_user_can( 'manage_woocommerce' ) ) {

            return true;
        }

        //  If the plugin options is not set, noone can edit reviews
        return $this->allow_edit_review;
    }

    /**
     * Check it current user can edit a specific review
     *
     * @param $review int|WP_Post The review to be edited
     *
     * @return bool
     * @author Lorenzo Giuffrida
     * @since  1.0.0
     */
    public function user_can_edit_review( $review ) {
        //  Only logged in user can edit reviews
        if ( ! $this->reviews_edit_enabled() ) {
            return false;
        }

        if ( is_numeric( $review ) ) {
            $review = get_post( $review );
        }

        if ( ! $review ) {
            return false;
        }

        $is_edit_blocked = get_post_meta( $review->ID, YITH_YWAR_META_REVIEW_BLOCK_EDIT, true ) == 'yes';

        return current_user_can( 'manage_woocommerce' ) || ( ! $is_edit_blocked && ( get_current_user_id() == $review->_ywar_review_user_id ) );
    }

    /**
     * Change the featured status for a review, for ajax calls
     */
    public function edit_review_callback() {
        $review_id = intval( $_POST['review_id'] );

        if ( ! $this->user_can_edit_review( $review_id ) ) {
            return;
        }

        $review_title   = sanitize_text_field( $_POST['review_title'] );
        $review_content = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['review_content'] ) ) ); // nl2br(sanitize_text_field($_POST['review_content']));
        $review_rating  = intval( $_POST['review_rating'] );

        //  update custom post type
        $args = array(
            'ID'           => $review_id,
            'post_title'   => $review_title,
            'post_content' => $review_content,
        );
        wp_update_post( $args );

        update_post_meta( $review_id, YITH_YWAR_META_KEY_RATING, $review_rating );

        // Notify that the review was updated
        yith_ywar_notify_review_update( $review_id );

        $review = get_post( $review_id );
        ob_start();
        $this->show_review( $review );
        $result = ob_get_contents();
        ob_end_clean();


        if ( get_option( "ywar_enable_recaptcha" ) == "yes" && get_option( "ywar_recaptcha_site_key" ) != '' && get_option( "ywar_recaptcha_secret_key" ) != '' && (!is_admin()) ) {
            /** Check the recaptcha, if used */
            add_filter( 'comment_form_submit_button', array( $this, 'display_comment_recaptcha' ) );

            add_filter( 'preprocess_comment', array( $this, 'verify_comment_captcha' ),5 );
        }



        wp_send_json( array(
                "code"    => 1,
                "content" => $result
            )
        );

    }


    /**
     * Change the stop-reply status for a review, for ajax calls
     */
    public function change_reply_status_callback() {
        $review_id         = intval( $_POST['review_id'] );
        $stop_reply_status = intval( $_POST['stop_reply_status'] );

        //  toggle current featured status
        update_post_meta( $review_id, YITH_YWAR_META_STOP_REPLY, ! $stop_reply_status );
        wp_send_json( array( "value" => $stop_reply_status ? 0 : 1 ) );   //return toggled status
    }

    /**
     * Manage an ajax call for reviews voting system, checking if the request came from a user logged in
     *
     * @return mixed return an json array of type ("code" => $code, "value" => $value) where code is 1 for succes and
     *               value is a string to show or code is -1 for user not logged in and value is a url to be redirect.
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    public function vote_review_callback() {
        // Check if user made the call
        $current_user = wp_get_current_user();

        $id_review    = intval( $_POST['id_review'] );
        $review_value = intval( $_POST['review_value'] );
        $return_path  = $_POST['return_path'];
        $wp_nonce     = $_POST['_wpnonce'];

        if ( ! is_user_logged_in() && ! $this->enable_guest_vote ) {
            // Add vars to querystring, building a redirect url to go after logins succesfull completed
            $redirect_to = add_query_arg( 'vote', $review_value, $return_path );
            $redirect_to = add_query_arg( 'review_id', $id_review, $redirect_to );
            $redirect_to = add_query_arg( '_wpnonce', $wp_nonce, $redirect_to );

            $goto_url = apply_filters( 'yith_ywar_login_url',  wp_login_url( $redirect_to ) );
            wp_send_json( array( "code" => - 1, "value" => $goto_url ) );
        }

        // Check for nonce validity
        $review     = get_post( $id_review );
        $product_id = get_post_meta( $id_review, YITH_YWAR_META_KEY_PRODUCT_ID, true );

        if ( apply_filters('yith_ywar_check_ajax_wp_nonce_vote_review_callback', true) &&  ! wp_verify_nonce( $wp_nonce, $this->get_wp_nonce_text( $product_id ) ) ) {
            return;
        }

        // Get yes/not rating from post_meta as array and as total count
        $meta_votes = get_post_meta( $review->ID, YITH_YWAR_META_VOTES, true );

        if ( $current_user->ID > 0 ) {
            // set rate for current review (it's a array of (user_id, value) where value is 1 for positive and -1 for negative rating
            $previous_user_rate = 0;
            if ( isset( $meta_votes[ $current_user->ID ] ) ) {
                $previous_user_rate = $meta_votes[ $current_user->ID ];
            }

            //  if user had a rate for this review, remove it from review total count  before adding the new one
            $this->add_remove_rating_to_review( $review->ID, $previous_user_rate, - 1 );

            $meta_votes[ $current_user->ID ] = $review_value;
        } else {
            $previous_user_rate = 0;
            if ( isset( $meta_votes[ $this->get_ip_address() ] ) ) {
                $previous_user_rate = $meta_votes[ $this->get_ip_address() ];
            }

            //  if user had a rate for this review, remove it from review total count  before adding the new one
            $this->add_remove_rating_to_review( $review->ID, $previous_user_rate, - 1 );

            $meta_votes[ $this->get_ip_address() ] = $review_value;
        }

        update_post_meta( $review->ID, YITH_YWAR_META_VOTES, $meta_votes );

        //  Add user rate to total count of upvotes or downvotes
        $this->add_remove_rating_to_review( $review->ID, $review_value, 1 );

        wp_send_json( array( "code" => 1, "value" => $this->get_review_is_helpful_text( $review->ID ) ) );
    }

    /**
     * Manage an ajax call for reviews voting system, checking if the request came from a user logged in
     *
     * @return mixed return an json array of type ("code" => $code, "value" => $value) where code is 1 for succes and
     *               value is a string to show or code is -1 for user not logged in and value is a url to be redirect.
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    public function report_inappropriate_review_callback() {
        // Check if user made the call
        $current_user = wp_get_current_user();


        $id_review = intval( $_POST['id_review'] );
        $wp_nonce  = $_POST['_wpnonce'];

        // Check for nonce validity
        $review     = get_post( $id_review );
        $product_id = get_post_meta( $id_review, YITH_YWAR_META_KEY_PRODUCT_ID, true );

        if ( ! wp_verify_nonce( $wp_nonce, $this->get_wp_nonce_text( $product_id ) ) ) {
            return;
        }

        // Get yes/not rating from post_meta as array and as total count
        $segnalations    = get_post_meta( $review->ID, YITH_YWAR_META_KEY_INAPPROPRIATE_LIST, true );
        $add_segnalation = false;
        $user_key        = '';

        if ( $current_user->ID > 0 ) {
            $user_key = $current_user->ID;

            if ( ! isset( $segnalations[ $user_key ] ) ) {
                $add_segnalation = true;
            }
        } else {
            $user_key = $this->get_ip_address();

            if ( ! isset( $segnalations[ $user_key ] ) ) {
                $add_segnalation = true;
            }
        }

        if ( $add_segnalation ) {
            $segnalations[ $user_key ] = 1;

            update_post_meta( $review->ID, YITH_YWAR_META_KEY_INAPPROPRIATE_LIST, $segnalations );
            $segnalations_count = get_post_meta( $review->ID, YITH_YWAR_META_KEY_INAPPROPRIATE_COUNT, true );
            update_post_meta( $review->ID, YITH_YWAR_META_KEY_INAPPROPRIATE_COUNT, ++ $segnalations_count );

            //  Check if hide on user reporting a inappropriate content is enabled
            if ( $this->hide_when_inappropriate ) {
                if ( $segnalations_count >= $this->hide_inappropriate_review_threshold ) {
                    //  Set as unapproved
                    $this->set_approved_status( $review->ID, false );
                }
            }
        }

        wp_send_json( array(
            "code"  => 1,
            "value" =>esc_html__( "Thanks for your message, we will check the content of the review.", 'yith-woocommerce-advanced-reviews' ),
        ) );
    }

    /**
     * General nonce text
     *
     * build a custom string to be used for nonce check in case of voting a post
     *
     * @param   int $product_id
     *
     * @return  string  nonce text
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    private function get_wp_nonce_text( $product_id ) {
        return 'voting-for-post' . $product_id;
    }

    /**
     * return a formatted url to be used for voting system.
     *
     * @param   object $review the review the vote is applied to.
     * @param   int    $value  pass 1 for positive vote, -1 for negative vote
     *
     * @return  string url to a product with vote requested
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    private function get_voting_url( $review, $value ) {

        //  prepare the url
        $product_id  = get_post_meta( $review->ID, YITH_YWAR_META_KEY_PRODUCT_ID, true );
        $return_path = get_permalink( $product_id );

        //  Add vars to querystring, building a redirect url
        $return_path = add_query_arg( 'vote', $value, $return_path );
        $return_path = add_query_arg( 'review_id', $review->ID, $return_path );

        return $return_path;
    }

    /**
     * return a formatted text stating how many user found a review helpful
     *
     * Giving a comment id, return a string like, for example, 2 of 9 people found this review helpful
     *
     * @param   int $review_id review id the text belong
     *
     * @return  string
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    public function get_review_is_helpful_text( $review_id ) {
        //  check if related setting is enabled
        if ( ! $this->show_peoples_vote || ! $this->enable_review_vote ) {
            return '';
        }

        $vote = $this->get_voting_stats( $review_id );

        return sprintf(esc_html__( '%s of %s people found this review helpful', 'yith-woocommerce-advanced-reviews' ), $vote["yes"], $vote["yes"] + $vote["not"] );
    }

    /**
     * Giving a comment id, retrieve how much positive and negative vote users had submitted.
     *
     * @param   int $review_id review id the text belong
     *
     * @return  array   array of positive and negative vote count
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    public function get_voting_stats( $review_id ) {
        $yes_votes = 0;
        $no_votes  = 0;

        $vote_comment_meta = get_post_meta( $review_id, YITH_YWAR_META_VOTES, true );

        //  get total amount of people that click yes to the comment review
        if ( isset( $vote_comment_meta ) && is_array( $vote_comment_meta ) ) {
            $votes_grouped = array_count_values( array_values( $vote_comment_meta ) );
            $yes_votes     = isset( $votes_grouped['1'] ) && is_numeric( $votes_grouped['1'] ) ? $votes_grouped['1'] : 0;
            $no_votes      = isset( $votes_grouped['-1'] ) && is_numeric( $votes_grouped['-1'] ) ? $votes_grouped['-1'] : 0;
        }

        return array( "yes" => $yes_votes, "not" => $no_votes );
    }

    /**
     * return comments based on stars rating passed as GET var
     *
     * @return mixed return an json array of type ("review_title" => $reviews_title, "comment_list" => $review_list)
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    public function get_ajax_comments_callback() {

        global $is_modal;

        $is_modal      = isset( $_POST["is_modal"] ) ? $_POST["is_modal"] : false;
        $reviews_title = '';

        $return_path = $_POST["return_path"];

        //  if product id is not provide, it can't load product reviews
        if ( ! isset( $_POST['product_id'] ) ) {
            wp_send_json( array( - 1,esc_html__( 'Product ID is not valid', 'yith-woocommerce-advanced-reviews' ) ) );
        }

        $product_id = $_POST['product_id'];
        //  Product id must be provided and a valid numeric value
        if ( ( ! isset( $product_id ) ) || ( ! is_numeric( $product_id ) ) || ( intval( $product_id ) < 0 ) ) {
            wp_send_json( array(
                - 1,
               esc_html__( 'Product ID is in an invalid format', 'yith-woocommerce-advanced-reviews' )
            ) );
        }

//        //  Product id must match this security check
//        if ( apply_filters('yith_ywar_check_ajax_wp_nonce', true) && ! check_ajax_referer( $this->get_wp_nonce_text( $product_id ) ) ) {
//            wp_send_json( array() );
//        }

        $unfiltered_reviews_count = $this->get_reviews_count( $product_id );


        /**
         * Check for filtering by rating
         */
        $stars = 0;
        //  if "stars" post var is set, show results filtered by stars rating...
        if ( isset( $_POST['stars'] ) ) {
            $stars = $_POST['stars'];
            //  check if stars value is correct, with range from 1 to 5
            if ( ( ! isset( $stars ) ) || ( ! is_numeric( $stars ) ) || ( intval( $stars ) < 0 ) || ( intval( $stars ) > 5 ) ) {
                wp_send_json( array(
                    - 1,
                   esc_html__( 'Impossible to filter by rate', 'yith-woocommerce-advanced-reviews' )
                ) );
            }
            $stars = intval( $stars );
        }

        /**
         * Check for order type
         */
        //  if set, show reviews ordered by date or by popularity
        $show_helpful = isset( $_POST['order'] ) && ( 'helpful' == $_POST['order'] );
        $order        = $show_helpful ? "helpful" : "recent";

        /**
         * Check for current page to show
         */
        $current_page = 1;
        $offset       = 0;
        if ( isset( $_POST['data_page'] ) ) {
            $current_page = intval( $_POST['data_page'] );
            $current_page ++;
            $offset = ( $current_page * $this->reviews_to_show ) - $this->reviews_to_show;
        }

        //  **********************************************************************
        //  Prepare parameters before retrieving the product reviews    **********
        //  **********************************************************************
        $args                = $this->default_query_args( $product_id );
        $args['offset']      = $offset;
        $args['numberposts'] = $this->reviews_to_show;

        $args['meta_key'] = YITH_YWAR_META_KEY_FEATURED;
        $args['orderby']  = 'meta_value_num post_date';
        $args['order']    = 'DESC';


        if ( $show_helpful ) {
            $args['meta_key'] = YITH_YWAR_META_UPVOTES_COUNT;
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
        } else {
            $args['order'] = 'DESC';
        }

        if ( $stars > 0 ) {
            $args['meta_query'][] = array(
                'key'     => YITH_YWAR_META_KEY_RATING,
                'value'   => $stars,
                'compare' => '=',
            );
        }


        ob_start();
        $this->reviews_list($product_id, apply_filters('yith_advanced_reviews_reviews_list', $args, $product_id), $this->show_featured_reviews);
        $review_list = ob_get_contents();
        ob_end_clean();

        $filtered_reviews_count = $unfiltered_reviews_count;

        if ( isset( $stars ) && ( $stars > 0 ) ) {
            $str_stars              = _n( 'star', 'stars', $stars, 'yith-woocommerce-advanced-reviews' );

            $filtered_reviews_count = $this->get_product_reviews_by_rating( $product_id, $stars );
            $reviews_title          = '<h3 class="ywar_review_list">' . apply_filters(
                    'yith_advanced_reviews_reviews_title_text',
                    sprintf(esc_html__( '%d reviews with rate of %d %s (%d of %d)', 'yith-woocommerce-advanced-reviews' ), $filtered_reviews_count, $stars, $str_stars, $filtered_reviews_count, $unfiltered_reviews_count ),
                    array(
                        "stars" => $stars,
                        "str_stars" => $str_stars,
                        "filtered_reviews_count" => $filtered_reviews_count,
                        "unfiltered_reviews_count" => $unfiltered_reviews_count
                    )
                ) . '</h3>';

            $reviews_title .= "<a href=\"$return_path\" class=\"ywar_filter_reviews\" data-id_product=\"$product_id\" data-stars=\"0\">" .esc_html__( '(Show unfiltered results)', 'yith-woocommerce-advanced-reviews' ) . "</a><hr>";
        }

        $show_more_link = '';
        if ( $this->show_load_more ) {
            //  if the are reviews to be showed on current_page, show a load more link
            if ( $filtered_reviews_count > $current_page * $this->reviews_to_show ) {

                $show_more_link = $this->show_load_more_section( $return_path, $order, $current_page, $product_id, $stars );
            }
        }

        $response = array(
            "review_title" => $reviews_title,
            "comment_list" => $review_list,
            "load_more"    => $show_more_link,
        );
        wp_send_json( $response );
    }

    /**
     * return comments based on stars rating passed as GET var
     *
     * @return mixed return an json array of type ("review_title" => $reviews_title, "comment_list" => $review_list)
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    public function get_customer_reviews_callback() {
        $product_id = $_POST['product_id'];
        //  Product id must be provided and a valid numeric value
        if ( ( ! isset( $product_id ) ) || ( ! is_numeric( $product_id ) ) || ( intval( $product_id ) < 0 ) ) {
            wp_send_json( array(
                - 1,
               esc_html__( 'Product ID is in an invalid format', 'yith-woocommerce-advanced-reviews' )
            ) );
        }

        $args = array(

            'author'           => get_current_user_id(),
            'post_type'        => YITH_YWAR_POST_TYPE,
            'suppress_filters' => true,
            'post_parent'      => 0,

            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'     => YITH_YWAR_META_APPROVED,
                    'value'   => 1,
                    'compare' => '=',
                ),
                array(
                    'key'     => YITH_YWAR_META_KEY_PRODUCT_ID,
                    'value'   => $product_id,
                    'compare' => '=',
                ),
            )
        );

        ob_start();
        $this->reviews_list( $product_id, apply_filters( 'ywar_get_customer_reviews', $args, $product_id ) );

        $review_list = ob_get_contents();

        ob_end_clean();

        $response = array(
            "comment_list" => $review_list,
        );
        wp_send_json( $response );
    }

    /**
     * Show the reviews for a specific product
     *
     * @param int $product_id
     * @param array $args
     * @param bool $show_featured
     */
    public function reviews_list( $product_id, $args = null, $show_featured = false ) {


        $args['numberposts'] = isset($args['numberposts']) && $args['numberposts'] != 0 ? $args['numberposts'] : -1;
        $args[ 'offset' ]    = isset($args[ 'offset' ]) ? $args[ 'offset' ] : '';
        $args['orderby']     = isset($args['orderby']) ? $args['orderby'] : '';
        $args['order']       = isset($args['order']) ? $args['order'] : '';
        $args['meta_key']    = isset($args['meta_key']) ? $args['meta_key'] : '';
        $args['meta_query']  = isset($args['meta_query']) ? $args['meta_query'] : '';

        $args = apply_filters( 'yith_advanced_reviews_query_review_list_args', array(
            'posts_per_page'   => $args['numberposts'],
            'offset'           => $args[ 'offset' ],
            'orderby'          => $args['orderby'],
            'order'            => $args['order'],
            'meta_key'         => $args['meta_key'],
            'post_type'        => YITH_YWAR_POST_TYPE,
            'post_parent'      => 0,
            'meta_query'       => $args['meta_query'],
        ) );

        $reviews_aux = get_posts( $args);
        $args['offset'] = !empty($args['offset']) ? $args['offset'] : 0;

        $featured_reviews = array();
        $no_featured_reviews = array();

        foreach ( $reviews_aux as $review_featured ) {
            $featured_postmeta = get_post_meta( $review_featured->ID, '_ywar_featured', true );
            if ( $featured_postmeta == '1'){
                $featured_reviews[] =  $review_featured;
            }
            else{
                $no_featured_reviews[] =  $review_featured;
            }
        }

        $array_reviews_in_order = array();

        if ( empty( $featured_reviews ) )
            $array_reviews_in_order = $no_featured_reviews;
        else
        {
            $count_featured_reviews = 0;
            foreach ( $featured_reviews as $featured_review ) {
                $array_reviews_in_order[] = $featured_review;
                ++$count_featured_reviews;
                if ( $count_featured_reviews >= $this->featured_reviews_count ){
                    $count_featured_reviews = 0;
                    for ( $i=0; $i < ( $this->reviews_to_show - $this->featured_reviews_count ) ; $i++ ) {
                        $array_reviews_in_order[] = array_shift( $no_featured_reviews );
                    }
                }
            }
            foreach ( $no_featured_reviews as $no_featured_review ) {
                $array_reviews_in_order[] = $no_featured_review;
            }
        }

        $index_reviews = 1;
        foreach ( $array_reviews_in_order as $review ) {

            if ( ($index_reviews <= ( $args[ 'offset' ] + $this->reviews_to_show ) ) || $this->reviews_to_show == 0 ){
                if ( in_array( $review, $featured_reviews ) ){
                    $this->show_review( $review, true, true );
                }
                else{
                    $this->show_review( $review, false, true );
                }
            }
            if ( ( $index_reviews > ( $args[ 'offset' ] + $this->reviews_to_show ) ) && $this->reviews_to_show != 0 ){
                break;
            }
            ++$index_reviews;
        }

    }

    /**
     * Add or remove upvote or downvote to a product review
     *
     * @param int $review_id  review id
     * @param int $rate_value 1 for upvotes, -1 for downvotes
     * @param int $add_value  1 for adding a vote to total count of votes of type $rate_value, -1 for removing a vote
     *                        from total count
     *
     * @return void
     *
     * @since  1.0
     * @author Lorenzo Giuffrida
     */
    private function add_remove_rating_to_review( $review_id, $rate_value, $add_value ) {
        //  if user had a rate for this review, remove it from comment total count  before adding the new one
        if ( 1 == $rate_value ) {
            $count = get_post_meta( $review_id, YITH_YWAR_META_UPVOTES_COUNT, true );
            if ( ! isset( $count ) ) {
                $count = 0;
            }

            $count += $add_value;
            update_post_meta( $review_id, YITH_YWAR_META_UPVOTES_COUNT, $count > 0 ? $count : 0 );
        } else if ( - 1 == $rate_value ) {
            $count = get_post_meta( $review_id, YITH_YWAR_META_DOWNVOTES_COUNT, true );
            if ( ! isset( $count ) ) {
                $count = 0;
            }

            $count += $add_value;
            update_post_meta( $review_id, YITH_YWAR_META_DOWNVOTES_COUNT, $count > 0 ? $count : 0 );
        }
    }

    /**
     * show a load more link on frontend, if reviews pagination is enabled and there is some reviews to show.
     *
     * @param string $return_path  the link to be used for anchor element
     * @param string $order        set current order type (recent or helpful)
     * @param int    $current_page the number of the next page to be showed
     * @param int    $product_id   the product id from which the reviews are retrieved
     * @param int    $stars        filter by rating(1 to 5 stars, or 0 for unfiltered result)
     *
     * @return string
     */
    protected function show_load_more_section( $return_path, $order, $current_page, $product_id, $stars ) {
        //  if pagination option is not enable, don't show anything
        if ( ! $this->show_load_more ) {
            return '';
        }

        //  if return path is not set, use the product permalink
        $return_path = ( is_null( $return_path ) ? get_permalink( $product_id ) : $return_path );
        //  if order is not specified, use "recent" mode
        $order = ( is_null( $order ) ? 'recent' : $order );
        //  if next page is not specified, we set it to 2
        $current_page = ( is_null( $current_page ) ? 1 : $current_page );
        //  if stars is not set, show all reviews (stars = 0)
        $stars = ( is_null( $stars ) ? 0 : $stars );

        //check if reviews are shown on dialog windows
        global $is_modal;

        $show_more_link_class = 'alt ywar_show_more';
        if ( 3 == $this->load_more_type ) {
            $show_more_link_class .= ' button ywar_button';
        }

        $show_more_html = '<a href="' . $return_path . '" class="' . $show_more_link_class . '" data-order="' . $order . '" data-page="' . $current_page . '" data-id_product="' . $product_id . '" data-dialog="' . $is_modal . '" data-stars="' . $stars . '" rel="nofollow">' . $this->load_more_text . '</a>';

        return $show_more_html;
    }

    private function get_ip_address() {
        return getenv( 'HTTP_CLIENT_IP' ) ?:
            getenv( 'HTTP_X_FORWARDED_FOR' ) ?:
                getenv( 'HTTP_X_FORWARDED' ) ?:
                    getenv( 'HTTP_FORWARDED_FOR' ) ?:
                        getenv( 'HTTP_FORWARDED' ) ?:
                            getenv( 'REMOTE_ADDR' );
    }

    /**
     * Change the view status of the review form for a specific product
     *
     * @param $show       bool current view status
     * @param $product_id int The product id being shown
     *
     * @return bool if the review form should be shown or not
     */
    public function show_review_form( $show, $product_id ) {
        //  Admins can always see the review submitting form
        if ( $this->enable_reply && ! YITH_WooCommerce_Advanced_Reviews_Premium::get_instance()->is_reviews_limited() && is_user_logged_in() ) {
            $show = true;
        }
        else{
            $show = false;
        }

        return $show;
    }

    /**
     * Change the view status of the review form for a specific product
     *
     * @param $show       bool current view status
     * @param $product_id int The product id being shown
     * @param $review_id  int Optional review id in case the review is a reply to another review
     *
     * @return bool if the review form should be shown or not
     */
    public function deny_review_form( $show, $product_id, $review_id ) {
        //  Admins can always see and submit reviews
        if ( current_user_can( 'manage_woocommerce' ) ) {
            return false;
        }

        if ( ! $this->is_reviews_limited() ) {
            return false;
        }

        //  If reviews are allowed only for verified customers, avoid multiple review for the same product
        if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'yes' ) {

            if ( wc_customer_bought_product( '', get_current_user_id(), $product_id ) ) {
                //  if a review still exists from this customer, for the current product, avoid to let
                //  the customer submitting another one.
                return $this->customer_reviews_for_product( $product_id, $review_id );
            }
        }

        return $show;
    }

    /**
     * Check if current user wrote a review for the product
     *
     * @param $product_id int the product id for which the customer is going to write a review
     * @param $review_id  int Optional review id in case of reply to another review
     *
     * @return bool
     */
    public function customer_reviews_for_product( $product_id, $review_id = 0 ) {
        global $wpdb;

        $result = $wpdb->query( $wpdb->prepare( "select p.ID, p.post_content, p.post_title, p.post_status, p.post_parent, m2.meta_value as approved, m3.meta_value as rating
                from {$wpdb->posts} p
                left join {$wpdb->postmeta} m on p.ID = m.post_id
                left join {$wpdb->postmeta} m2 on p.ID = m2.post_id
                left join {$wpdb->postmeta} m3 on p.ID = m3.post_id
                where p.post_type = %s
                and p.post_author = %d
                and m.meta_key = '_ywar_product_id' and m.meta_value = %s
                and m2.meta_key = '_ywar_approved'
                and m3.meta_key = '_ywar_rating'
                and p.post_parent = %d",
            YITH_YWAR_POST_TYPE,
            get_current_user_id(),
            $product_id,
            $review_id ) );

        return $result;
    }

    /**
     * Add an option on WooCommerce product section, enabling the admin to limit to one the number of reviews
     * that a verified customer can write.
     *
     * @param $settings array current WooCommerce product settings
     *
     * @return array the new WooCommerce product settings
     */
    public function woocommerce_products_general_settings( $settings ) {
        $index = 0;

        //Put an option after woocommerce_review_rating_verification_required
        foreach ( $settings as $key => $setting ) {
            $index ++;
            if ( 'woocommerce_review_rating_verification_required' == $setting["id"] ) {
                $settings[ $key ]["checkboxgroup"] = '';  //  The last element of the checkbox group is the element being added
                $before                            = array_slice( $settings, 0, $index, false );
                $new_element                       = array(
                    "ywar_limit_multiple_review" => array(
                        "desc"            =>esc_html__( 'Only allow one review for a product. Require "Only allow reviews from verified owners" to be set to work properly', 'yith-woocommerce-advanced-reviews' ),
                        "id"              => "ywar_limit_multiple_review",
                        "default"         => 'no',
                        "type"            => 'checkbox',
                        "checkboxgroup"   => 'end',
                        "show_if_checked" => 'yes',
                    )
                );

                $after = array_slice( $settings, $index, count( $settings ) - $index, false );

                return array_merge( $before, $new_element, $after );
            }
        }

        return $settings;
    }

    /**
     * Set the text to be shown when the user can't submit a review for a product because there is a previously sent
     * review waiting for approval or in trash
     *
     * @param $message    string current message being shown
     * @param $product_id int the product id current shown
     *
     * @return string
     */
    public function ywar_product_reviews_submit_reviews_denied_text( $message, $product_id ) {
        if ( $this->is_reviews_limited() && $this->customer_reviews_for_product( $product_id ) ) {

            if ( $this->allow_edit_review )
                return esc_html__( "You can't add more than one review for this product. Would you like to edit the review you have already written?", 'yith-woocommerce-advanced-reviews' );
            else
                return esc_html__( "You can't add more than one review for this product.", 'yith-woocommerce-advanced-reviews' );
        }

        return $message;
    }


    /**
     *  Display recaptcha
     */
    public function display_comment_recaptcha($submit_button) {

        global $post;

        $product = wc_get_product($post->ID);

        if( $product ) {

            return '<div><input type="hidden" id="yith_ywar_recaptcha_product" name="yith_ywar_recaptcha_product" value="'.$post->ID.'"> </div><div class="g-recaptcha" data-sitekey="' . $this->recaptcha_sitekey . '" ></div> ' . $submit_button;

        } else {
            return $submit_button;
        }
    }


    /**
     * Verify that comment is not a spam
     * @param $commentdata
     * @return null
     */
    public function verify_comment_captcha($commentdata)
    {

        if (isset($_POST['yith_ywar_recaptcha_product']) && $commentdata['comment_type'] == 'review') {

            if (isset($_POST['g-recaptcha-response'])) {

                $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=" . $this->recaptcha_secretkey . "&response=" . $_POST['g-recaptcha-response']);
                $response = json_decode($response["body"], true);
                if (true == $response["success"]) {
                    return $commentdata;
                } else {
                    wp_die(get_option('ywar_recaptcha_message_error'), 'captcha_error');
                    return null;
                }
            } else {
                echo esc_html__("Bots are not allowed to submit comments. If you are not a bot then please enable JavaScript in browser.");
                return null;
            }
        }
        return $commentdata;
    }

    /**
     * Shortcode to display the reviews
     */
    function yith_ywar_show_reviews( $atts, $content ){

        ob_start();

        $pages = 1;
        $review_number = isset($atts['reviews_number']) ? $atts['reviews_number'] : -1;
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 0;
        $offset = $paged * $review_number;
        $offset = ( $offset > 0 ) ? $offset -1 : 0;

        if (isset ($atts['product_id'])){

            $args = $this->default_query_args($atts['product_id']);

            $args['posts_per_page'] = !isset($atts['pagination']) ? $review_number : -1;

            $reviews_aux = get_posts( $args);

            if( $review_number > 0 && isset($atts['pagination'] ) ) {

                $review_number_of_reviews = count($reviews_aux);
                $pages = floor($review_number_of_reviews/$review_number);
                $reviews_aux = array_slice($reviews_aux,$offset,$review_number);
            }

            foreach ( $reviews_aux as $review ) {
                $this->show_review_shortcode( $review, false, true );
            }
        }
        else{

            $args = array(
                'posts_per_page'   => !isset($atts['pagination']) ? $review_number : -1,
                'offset'           => '',
                'orderby'          => '',
                'order'            => '',
                'meta_key'         => '',
                'post_type'        => YITH_YWAR_POST_TYPE,
                'post_parent'      => 0,
                'meta_query'       => '',
                'paged'            => ( get_query_var('paged') ) ? get_query_var('paged') : 1,
            );


            $reviews_aux = get_posts( $args);

            if( $review_number > 0 && isset($atts['pagination'] ) ) {

                $review_number_of_reviews = count($reviews_aux);
                $pages = floor($review_number_of_reviews/$review_number);
                $reviews_aux = array_slice($reviews_aux,$offset,$review_number);
            }

            foreach ( $reviews_aux as $review ) {
                if (apply_filters('yith_ywar_show_review_shortcode_without_product_id', true, $review)){
                    $this->show_review_shortcode( $review, false, true );
                }
            }
            
        }

        if ( isset($atts['pagination']) ) {
            do_action( 'yith_ywar_pagination_shortcode_nav', $pages );
        }

        return ob_get_clean();

    }

    public function show_review_shortcode( $review, $featured = false, $show_childs = false ) {
        global $ywar_review;
        $ywar_review = $review;

        wc_get_template( 'ywar-review-shortcode.php',
            array(
                'review'      => $review,
                'featured'    => $featured,
                'classes'     => $featured ? 'review-featured' : '',
                'rating'      => YITH_YWAR()->get_meta_value_rating( $review->ID ),
                'approved'    => YITH_YWAR()->get_meta_value_approved( $review->ID ),
                'product_id'  => YITH_YWAR()->get_meta_value_product_id( $review->ID ),
                'review_date' => version_compare( WC()->version, '2.5', '<' ) ?
                    mysql2date( get_option( 'date_format' ), $review->post_date ) :
                    mysql2date( wc_date_format(), $review->post_date ),
            ),
            '',
            YITH_YWAR_TEMPLATES_DIR );

        if ( $show_childs ) {
            $review_childs = $this->get_childs_review( $review );
            if ( count( $review_childs ) > 0 ) {

                echo '<ul class="children">';
                foreach ( $review_childs as $review ) {
                    $this->show_review( $review );
                }

                echo '</ul>';
            }
        }
    }

    /**
     * Action links
     *
     *
     * @return void
     * @since    1.2.3
     * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
     */
    public function action_links( $links ) {
        $links = yith_add_action_links( $links, $this->_panel_page, true );
        return $links;
    }
    /**
     * Plugin Row Meta
     *
     *
     * @return void
     * @since    1.2.3
     * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
     */
    public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWAR_INIT' ) {
        $new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

        if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
            $new_row_meta_args['is_premium'] = true;
        }

        return $new_row_meta_args;
    }

    /**
     * Prints template for displaying navigation panel for pagination
     *
     * @param $max_num_pages
     */
    public static function pagination_nav( $max_num_pages ) {
        ob_start();
        wc_get_template( 'frontend/yith-ywar-pagination-nav.php', array( 'max_num_pages' => $max_num_pages ), '', YITH_YWAR_TEMPLATES_DIR );
        echo ob_get_clean();
    }
}
