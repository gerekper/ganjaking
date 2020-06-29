<?php
/**
 * Frontend class
 *
 * @author YITH
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WRVP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WRVP_Frontend' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WRVP_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WRVP_Frontend
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WRVP_VERSION;

		/**
		 * Product list
		 *
		 * @var array
		 * @since 1.0.0
		 */
		protected $_products_list = array();

        /**
         * List of product processed in same execution
         *
         * @var array
         * @since 1.0.0
         */
        protected $_execution_done = array();

		/**
		 * Current user id
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_user_id = '';

		/**
		 * The name of cookie name
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_cookie_name = 'yith_wrvp_products_list';

		/**
		 * The name of meta products list
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_meta_products_list = 'yith_wrvp_products_list';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WRVP_Frontend
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'init' ), 1 );

			add_shortcode( 'yith_similar_products', array( $this, 'similar_products' ) );

			add_action( 'template_redirect', array( $this, 'track_user_viewed_produts' ), 99 );
			add_action( 'woocommerce_before_single_product', array( $this, 'track_user_viewed_produts' ), 99 );

			add_action( 'woocommerce_after_single_product_summary', array( $this, 'print_shortcode' ), 30 );
		}

		/**
		 * Init plugin
		 *
		 * @since 1.0.0
		 * @access public
		 * @author Francesco Licandro
		 */
		public function init(){
			$this->_user_id = get_current_user_id();

			// populate the list of products
			$this->populate_list();
		}

		/**
		 * Populate user list
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function populate_list(){

			if( ! $this->_user_id ) {
				$this->_products_list = isset( $_COOKIE[$this->_cookie_name] ) ? unserialize( $_COOKIE[ $this->_cookie_name ] ) : array();
			}
			else {
				$meta = get_user_meta( $this->_user_id, $this->_meta_products_list, true );
				$this->_products_list = ! empty( $meta ) ? $meta : array();
			}

			! is_array( $this->_products_list ) && $this->_products_list = array();
		}

		/**
		 * Track user viewed products
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function track_user_viewed_produts(){

			global $post;

			if( is_null( $post ) || $post->post_type != 'product' || ! is_product() || in_array( $post->ID, $this->_execution_done ) ) {
                return;
            }

            $product_id = intval( $post->ID );
            $product    = wc_get_product( $product_id );
            if ( ! $product || ( get_option( 'yith-wrvp-hide-out-of-stock' ) === 'yes' && ! $product->is_in_stock() ) ) {
                return;
            }

			// if product is in list, remove it
			if( ( $key = array_search( $product_id, $this->_products_list ) ) !== false ) {
				unset( $this->_products_list[$key] );
			}
			elseif( apply_filters( 'yith_wrvp_track_product_views', true, $this ) ) {
			    global $_wp_suspend_cache_invalidation;
			    $suspend_cache = empty( $_wp_suspend_cache_invalidation );
			    // suspend cache invalidation
                $suspend_cache && wp_suspend_cache_invalidation();

			    $views = $product->get_meta( '_ywrvp_views', true );
			    $views = ! $views ? 1 : intval( $views ) + 1;
			    $product->update_meta_data( '_ywrvp_views', $views );
			    $product->save();

			    // restore cache invalidation
                $suspend_cache && wp_suspend_cache_invalidation( false );
            }

			$timestamp = time();
			$this->_products_list[$timestamp]   = $product_id;
			$this->_execution_done[]            = $product_id;

			// set cookie and save meta
			$this->set_cookie_meta();
		}

		/**
		 * Set cookie and save user meta with products list
		 *
		 * @access protected
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function set_cookie_meta() {
		    $duration = get_option( 'yith-wrvp-cookie-time' );
			$duration = time() + (86400 * $duration);

			// if user also exists add meta with products list
			if( $this->_user_id ) {
				update_user_meta( $this->_user_id, $this->_meta_products_list, $this->_products_list );
			}
			else {
				// set cookie
				setcookie($this->_cookie_name, serialize( $this->_products_list ), $duration, COOKIEPATH, COOKIE_DOMAIN, false, true);
			}
		}

		/**
		 * Get list of similar products based on user chronology
		 *
		 * @access public
		 * @since 1.0.0
		 * @param array $cats_array
		 * @param string $similar_type
		 * @param array $products_list
		 * @return mixed
		 * @author Francesco Licandro
		 */
		public function get_similar_products( $cats_array = array(), $similar_type = '', $products_list = array() ) {
		    empty( $products_list ) && $products_list = $this->_products_list;
		    return YITH_WRVP_Helper::get_similar_products( $cats_array, $similar_type, $products_list );
		}

		/**
		 * Get products terms
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $term_name
		 * @param boolean $with_name
		 * @param array $products_list
		 * @return array
		 * @author Francesco Licandro
		 */
		protected function get_list_terms( $term_name, $with_name = false, $products_list = array() ){
		    empty( $products_list ) && $products_list = $this->_products_list;
		    return YITH_WRVP_Helper::get_list_terms( $term_name, $with_name, $products_list );
		}

		/**
		 * Query build for get similar products
		 *
		 * @access public
		 * @since 1.0.0
		 * @param $cats_array
		 * @param $tags_array
		 * @param $excluded
		 * @return array
		 * @author Francesco Licandro
		 */
		protected function build_query( $cats_array, $tags_array, $excluded ) {
		    return YITH_WRVP_Helper::build_query( $cats_array, $tags_array, $excluded );
		}

		/**
		 * Shortcode similar products
		 *
		 * @access public
		 * @since 1.0.0
		 * @param mixed $atts
		 * @return mixed
		 * @author Francesco Licandro
		 */
		public function similar_products( $atts ) {

			extract( shortcode_atts(array(
				'num_post' 	=> get_option( 'yith-wrvp-num-tot-products', '4' ),
				'order' 	=> 'rand',
				'title'		=> get_option( 'yith-wrvp-section-title' )
			), $atts ) );

			$similar_products = $this->get_similar_products();

			if( empty( $similar_products ) ) {
				return '';
			}

			$args = array(
				'post_type'            => 'product',
				'ignore_sticky_posts'  => 1,
				'no_found_rows'        => 1,
				'posts_per_page'       => $num_post,
				'orderby'              => $order,
				'post__in'             => $products
			);

			// set visibility query
            $args = yit_product_visibility_meta( $args );
            // then let's third part filter args array
            $args = apply_filters( 'yith_wrvp_similar_products_template_args', $args );

			$products = new WP_Query( $args );

			ob_start();

			if ( $products->have_posts() ) : ?>

				<div class="woocommerce yith-similar-products">

					<h2><?php echo esc_html( $title ); ?></h2>

					<?php woocommerce_product_loop_start(); ?>

					<?php while ( $products->have_posts() ) : $products->the_post(); ?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>

					<?php woocommerce_product_loop_end(); ?>

				</div>

			<?php endif;

			$content = ob_get_clean();

			wp_reset_postdata();

			return $content;
		}

		/**
		 * Print shortcode similar products on single product page based on user viewed products
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function print_shortcode() {

			if( get_option( 'yith-wrvp-show-on-single', 'yes' ) == 'yes' )
				echo do_shortcode('[yith_similar_products]');
		}
	}
}
/**
 * Unique access to instance of YITH_WRVP_Frontend class
 *
 * @return \YITH_WRVP_Frontend
 * @since 1.0.0
 */
function YITH_WRVP_Frontend(){
	return YITH_WRVP_Frontend::get_instance();
}