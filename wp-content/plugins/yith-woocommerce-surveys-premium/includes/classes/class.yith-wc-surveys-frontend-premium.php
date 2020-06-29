<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WC_Surveys_Frontend_Premium' ) ){

    class YITH_WC_Surveys_Frontend_Premium extends  YITH_WC_Surveys_Frontend{

        protected  static $instance;

        public  function __construct(){
            parent::__construct();



            add_action( 'wp_enqueue_scripts', array( $this, 'include_survey_styles' ) );

            /*actions for show all checkout surveys */
            add_action( 'woocommerce_before_order_notes', array( $this, 'print_survey_in_checkout' ), 20 ,1 );
            add_action( 'woocommerce_before_checkout_billing_form'  , array( $this, 'print_survey_in_checkout' ), 20 ,1 );
            add_action( 'woocommerce_after_checkout_billing_form'   , array( $this, 'print_survey_in_checkout' ), 20 ,1 );
            add_action( 'woocommerce_before_checkout_shipping_form' , array( $this, 'print_survey_in_checkout' ), 20 ,1 );
            add_action( 'woocommerce_after_checkout_shipping_form'  , array( $this, 'print_survey_in_checkout' ), 20 ,1 );

            /*actions for show all product surveys*/
            add_action( 'woocommerce_after_single_product_summary', array( $this, 'print_survey_in_single_product' ), 25  );
            add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'print_survey_before_or_after_add_to_cart_form' ) );
            add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'print_survey_before_or_after_add_to_cart_form' ) );
        }

        /**
         * @author YIThemes
         * @since 1.0.0
         * @return YITH_WC_Surveys_Frontend_Premium
         */
        public static function  get_instance(){

            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }


        /**
         * include style in frontend
         * @author YIThemes
         * @since 1.0.0
         */
        public function include_survey_styles(){

            wp_register_style( 'surveys_style', YITH_WC_SURVEYS_ASSETS_URL.'css/yith_survey_frontend.css', array(), YITH_WC_SURVEYS_VERSION );
        }


        /**
         * print all survey after order notes in checkout
         * @author YIThemes
         * @since 1.0.0
         */
        public function print_survey_in_checkout( $checkout ){

            $current_action = current_action();
            $checkout_pos = '';

            switch( $current_action ){

                case 'woocommerce_before_order_notes':
                    $checkout_pos = 'before_order_notes';
                    break;
                case 'woocommerce_before_checkout_billing_form':
                    $checkout_pos = 'before_checkout_billing_form';
                    break;
                case 'woocommerce_after_checkout_billing_form':
                    $checkout_pos = 'after_checkout_billing_form';
                    break;
                case 'woocommerce_before_checkout_shipping_form':
                    $checkout_pos = 'before_checkout_shipping_form';
                    break;
                case 'woocommerce_after_checkout_shipping_form' :
                    $checkout_pos = 'after_checkout_shipping_form';
                    break;
                default:
                    $checkout_pos = 'after_order_notes';
                    break;
            }
            $all_survey =   YITH_Surveys_Type()->get_checkout_surveys( $checkout_pos );


            foreach( $all_survey as $survey ){

                $params = apply_filters( 'yith_wc_survey_checkout_form_args', array(
                    'survey_id' => $survey,
                ) );

                wc_get_template( 'surveys/survey_checkout_form.php', $params,'', YITH_WC_SURVEYS_TEMPLATE_PATH );
            }
        }

        /**
         * show product surveys after product summary
         * @author YIThemes
         * @since 1.0.0
         */
        public function print_survey_in_single_product(){

            $product_handle = 'after_single_product_summary';
            global $product;
            $product_id = yit_get_product_id( $product );
            $all_product_survey = YITH_Surveys_Type()->get_product_surveys( $product_handle, $product_id );

            if( count( $all_product_survey ) > 0 ){
               $params = apply_filters( 'yith_wc_survey_after_product_summary_args', array(
                   'all_surveys' => $all_product_survey,
                   'button_class' => 'yith_send_answer',
                   'select_class'  => 'yith_surveys_answers'
                ) );

                wc_get_template( 'surveys/surveys_after_product_summary.php', $params,'', YITH_WC_SURVEYS_TEMPLATE_PATH );
            }
        }

        /**
         * show product surveys before or after add to cart form
         * @author YIThemes
         * @since 1.0.0
         */
        public function print_survey_before_or_after_add_to_cart_form(){

            $product_action = current_action();
            $product_handle = '';

            global $product;

            if( 'woocommerce_before_add_to_cart_form' === $product_action )
                $product_handle = 'before_add_to_cart_form';
            elseif( 'woocommerce_after_add_to_cart_form' === $product_action )
                $product_handle = 'after_add_to_cart_form';

            if( !empty( $product_handle ) ){

                $product_id = yit_get_product_id( $product );
                $all_product_survey = YITH_Surveys_Type()->get_product_surveys( $product_handle, $product_id );

                if( count( $all_product_survey )> 0 ){

                    $params = apply_filters( 'yith_wc_survey_after_before_add_to_cart_args', array(
                        'all_surveys' => $all_product_survey,
                        'button_class' => 'yith_send_answer',
                        'select_class'  => 'yith_surveys_answers'
                    ) );

                    wc_get_template( 'surveys/surveys_after_before_add_to_cart.php', $params,'', YITH_WC_SURVEYS_TEMPLATE_PATH );
                }
            }
        }
    }
}