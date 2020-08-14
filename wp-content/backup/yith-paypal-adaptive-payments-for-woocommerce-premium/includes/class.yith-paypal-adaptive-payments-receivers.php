<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}
if(! class_exists( 'YITH_PADP_Receivers' ) ){

    class YITH_PADP_Receivers {

       protected $endpoint = '';
       protected static $instance;

        public function __construct(){

            $this->endpoint = yith_paypal_adaptive_payments_receivers_get_endpoint();
            
            add_action( 'woocommerce_account_'.$this->endpoint.'_endpoint', array( $this, 'show_user_commission' ) );
            add_filter( 'woocommerce_endpoint_' . $this->endpoint. '_title',  array( $this, 'endpoint_title' ), 10 );
            add_filter( 'woocommerce_account_menu_items', array( $this, 'receiver_commission_menu_items' ) );
            
            add_shortcode( 'yith_paypal_adaptive_payments_show_commission', array( $this, 'add_show_commission_shortcode' ) );

            //Compatibility with YITH Themes
            add_action('yith_myaccount_menu', array( $this, 'add_myaccount_menu' ) );
            add_filter('yit_get_myaccount_menu_icon_list', array( $this, 'receiver_account_menu_icon_list' ) );
            add_filter('yit_get_myaccount_menu_icon_list_fa', array( $this, 'receiver_account_menu_icon_list_fa' ) );
        }
        
        public static function get_instance(){
            
            if( is_null( self::$instance ) ){
                self::$instance = new self();
            }
            
            return self::$instance;
        }


        public function show_user_commission() {
            
            echo do_shortcode('[yith_paypal_adaptive_payments_show_commission pagination="yes"]');
        }

        /**
         * return receiver endpoint title
         * @author YITHEMES
         * @since 1.0.0
         * @return string
         */
        public function get_receiver_endpoint_title(){

            $title =  __( 'Your commissions', 'yith-paypal-adaptive-payments' );
            return apply_filters( 'yith_paypal_adaptive_payments_endpoint_title', $title );
        }

        /**
         * return endpoint title
         * @author YITHMES
         * @since 1.0.0
         * @param $title
         * @return string|void
         */
        public function endpoint_title( $title ){

            return $this->get_receiver_endpoint_title();
        }

        /**
         * @add receiver commission item into my-account menu
         * @author YITHEMES
         * @since 1.0.0
         * @param array $menu_items
         * @return array
         */
        public function receiver_commission_menu_items( $menu_items ){

            $menu_items[$this->endpoint] = $this->get_receiver_endpoint_title();

            return $menu_items;
        }
        
        public function add_show_commission_shortcode( $attr, $content = null ){

            $attr = shortcode_atts( array(
                'per_page' => apply_filters('yith_paypal_adaptive_payment',10 ),
                'pagination' => 'no' ), $attr
            );

            extract( $attr );
           
            $base_url = '';
            
            $my_account_page = wc_get_page_id('myaccount');
            $user_id = get_current_user_id();
            $endpoint_name = yith_paypal_adaptive_payments_receivers_get_endpoint();

            $tot_commission = YITH_PADP_Receiver_Commission()->count_commission( $user_id );
            $limit = $offset = $page_links = false;

            if( 'yes' == $pagination  && $tot_commission > 1 ){

                global $post;

                if(  $my_account_page == $post->ID ){

                    $base_url = esc_url( wc_get_page_permalink( 'myaccount' ) . $endpoint_name );

                    $base_url .=  '/paged=%#%';
                    $format = 'paged=%#%';
                    $url = $_SERVER['REQUEST_URI'];
                    $paged_curr = explode( '=', $url);
                    $paged_curr = isset( $paged_curr[1] ) ? preg_replace('/\D/', '', $paged_curr[1] ) : 1;
                    $current_page = max( 1 , $paged_curr );

                }else{

                    $base_url = esc_url( add_query_arg( array( 'paged' => '%#%' ), get_the_permalink( $post->ID ) ) );
                    $current_page = max( 1, get_query_var('paged'));
                    $format = '?paged=%#%';
                }


                $pages = ceil( $tot_commission / $per_page );

                if ( $current_page > $pages ) {
                    $current_page = $pages;
                }

                $offset = ( $current_page - 1 ) * $per_page;
                $limit = $per_page;

                if ( $pages > 1 ) {
                    $page_links = paginate_links( array(
                        'base' => $base_url ,
                        'format' => $format,
                        'current' => $current_page,
                        'total' => $pages,
                        'show_all' => true ) );
                }

            }

            $args['args'] = array( 'user_id' => $user_id, 'offset' => $offset, 'limit' => $limit, 'page_links' => $page_links );
            
            ob_start();
            wc_get_template( 'view-user-commission.php', $args, YITH_PAYPAL_ADAPTIVE_TEMPLATE_PATH, YITH_PAYPAL_ADAPTIVE_TEMPLATE_PATH );
            $template = ob_get_contents();
            ob_end_clean();

            return $template;
        }

	    /**
	     * @author YITHEMES
	     * @since 1.0.5
	     * @param $myaccount_url
	     */
	    public function add_myaccount_menu( $myaccount_url ){

		    global $wp;
		    if( is_user_logged_in() ){
			    $slug = yith_paypal_adaptive_payments_receivers_get_endpoint();
			    $name = $this->get_receiver_endpoint_title();
			    ?>
			    <li>
				    <span class="fa fa-money"></span>
				    <a style="display: inline-block;padding-left: 8px;" href="<?php echo wc_get_endpoint_url( $slug, '',  $myaccount_url ) ?>" title="<?php echo $name; ?>"<?php echo isset( $wp->query_vars[$slug] ) ? ' class="active"' : ''; ?>><?php echo $name; ?></a>
			    </li>
			    <?php
		    }
	    }

        /**
         * add menu items in my-account menu (WC 2.6)
         * @author YITEMES
         * @since 1.0.0
         * @param $menu_items
         * @return mixed
         */
        public function receiver_account_menu_items( $menu_items ){

            $slug = yith_paypal_adaptive_payments_receivers_get_endpoint();
            $name = $this->get_receiver_endpoint_title();
            $menu_items[$slug] = $name;


            return $menu_items;
        }

        /**
         * show icon in my account items
         * @author YITHEMES
         * @since 1.0.0
         * @param $icon_list
         * @return array
         */
        public function receiver_account_menu_icon_list( $icon_list ){

            $slug = yith_paypal_adaptive_payments_receivers_get_endpoint();

            $icon_list[$slug] = '&#xe04d;';


            return $icon_list;

        }

        /**
         * add fontawesome icon
         * @author YITHEMES
         * @since 1.0.0
         * @param $icon_list
         * @return mixed
         */
        public function receiver_account_menu_icon_list_fa( $icon_list ){
            $slug = yith_paypal_adaptive_payments_receivers_get_endpoint();

            $icon_list[$slug] = 'fa-money';

            return $icon_list;

        }
    }
}

function YITH_PADP_Receivers(){
    return YITH_PADP_Receivers::get_instance();
}