<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_WC_Surveys_Admin' ) ){

    class YITH_WC_Surveys_Admin{

        protected static $instance;

        public function __construct(){

            add_action( 'woocommerce_admin_field_survey_options', array( $this, 'survey_options' ), 10, 1 );
            add_action( 'init', array( $this, 'save_survey_post_meta' ) );
            add_action( 'init', array( $this, 'update_survey_post_title' ), 20 );

            add_action( 'admin_enqueue_scripts', array( $this, 'admin_add_scripts' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_add_styles' ) );

            //add ajax action for load admin template
            add_action( 'wp_ajax_add_new_survey_answer_admin', array( $this, 'add_new_survey_answer_admin' ) );
            add_action( 'wp_ajax_nopriv_add_new_survey_answer_admin', array( $this, 'add_new_survey_answer_admin' ) );

            add_action( 'wp_ajax_remove_survey_answer_admin', array( $this, 'remove_survey_answer_admin' ) );
            add_action( 'wp_ajax_nopriv_remove_survey_answer_admin', array( $this, 'remove_survey_answer_admin' ) );

            //add metaboxes in woocommerce order
            add_action( 'add_meta_boxes', array( $this, 'add_order_survey_meta_boxes' ) );

        }

        /**
         * @return YITH_WC_Surveys_Admin
         */
        public static function  get_instance(){

            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * add admin script
         * @author YIThemes
         * @since 1.0.0
         */
        public function admin_add_scripts(){


            $suffix = !( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.min' : '';
            $premium = ywcsur_is_premium_active() ? '_premium' : '';
            wp_register_script( 'yit_survey_script', YITH_WC_SURVEYS_ASSETS_URL.'js/yith_surveys_admin'.$premium.$suffix.'.js', array( 'jquery', 'jquery-ui-sortable'), YITH_WC_SURVEYS_VERSION, true );

            $yith_survey_params = array(
                'ajax_url'  =>  admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
                'actions'   => array(
                    'add_new_survey_answer' => 'add_new_survey_answer_admin',
                    'remove_survey_answer'  =>  'remove_survey_answer_admin'
                )
            );

            wp_localize_script( 'yit_survey_script', 'yith_survey_params', $yith_survey_params );

            $is_post_type = ( isset( $_GET['post_type'] ) && 'yith_wc_surveys' == $_GET['post_type'] ) ;

	        $is_post_type = $is_post_type || isset( $_GET['post'] )  && 'yith_wc_surveys' == get_post_type( $_GET['post'] );
            if( $is_post_type ){

	            wp_enqueue_script( 'yit_survey_script' );
            }
        }

        /**
         * add admin style
         */
        public function admin_add_styles(){

            wp_enqueue_style( 'yith_survey_style', YITH_WC_SURVEYS_ASSETS_URL.'css/yith_survey_admin.css', array(), YITH_WC_SURVEYS_VERSION );
        }

        /**
         * print custom field
         * @author YIThemes
         * @since 1.0.0
         * @param $value
         */
        public function survey_options( $value ){

            if( !ywcsur_is_premium_active() ){

                $args = array('value' => $value );
                wc_get_template('survey_options.php', $args, '', YITH_WC_SURVEYS_TEMPLATE_PATH . '/admin/' );
            }
        }

        /**
         *update post_title for free surveys
         * @author YIThemes
         * @since 1.0.1
         */
        public function update_survey_post_title(){

            if( !ywcsur_is_premium_active() ){

                $updated = get_option( 'yith_wc_survey_update', 'no' );

                if( 'no' == $updated ){

                    $free_survey_id = get_option( 'yith_wc_free_survey_id', -1 );

                    $free_survey_id = yith_wpml_get_translated_id( $free_survey_id, 'yith_wc_surveys' );
                    $survey_title = get_post_meta( $free_survey_id, '_yith_survey_title', true );
                    //update post meta for this survey
                    $args = array(
                        'ID' => $free_survey_id,
                        'post_status' => 'publish',
                        'post_title'  => $survey_title,
	                    'post_name' => sanitize_title( $survey_title )
                    );

                    wp_update_post($args);

                    delete_post_meta( $free_survey_id, '_yith_survey_title' );

                    $all_children = YITH_Surveys_Type()->get_survey_children( array( 'post_parent' => $free_survey_id ) );

                    foreach( $all_children as $child_id ){

                        //update post meta for this survey
                        $answer_title = get_post_meta( $child_id, '_yith_survey_title', true );
                        $args = array(
                            'ID' => $child_id,
                            'post_status' => 'publish',
                            'post_title'  => $answer_title,
                            'post_name' => sanitize_title( $answer_title )
                        );

                        wp_update_post($args);
                        delete_post_meta( $child_id, '_yith_survey_title' );
                    }
                    update_option( 'yith_wc_survey_update', 'yes' );
                }
            }
        }
        /**
         * save survey post meta
         * @author YIThemes
         * @since 1.0.0
         */
        public function save_survey_post_meta() {

           if( !ywcsur_is_premium_active() ){

               if( isset( $_REQUEST['page'] ) && 'yith_wc_surveys_panel' == $_REQUEST['page'] ){

                   if( isset( $_REQUEST['save_survey'] ) && 'yes' == $_REQUEST['save_survey']  ){

                       $survey_title = $_REQUEST['yith_survey_title'];
                       $survey_visible_in  = $_REQUEST['yith_survey_visible_in'];
                       $survey_handle = $_REQUEST['yith_survey_wc_handle'];
                       $free_survey_id = get_option( 'yith_wc_free_survey_id', -1 );


                       //update post meta for this survey
                       $args = array(
                           'ID' => $free_survey_id,
                           'post_status' => 'publish',
                           'post_title'  => $survey_title
                       );

                       wp_update_post($args);
                       update_post_meta($free_survey_id, '_yith_survey_visible_in', $survey_visible_in);
                       update_post_meta($free_survey_id, '_yith_survey_wc_handle', $survey_handle);

                      if( isset( $_REQUEST['yith_survey_answers'] ) ) {

                          $answers = $_REQUEST['yith_survey_answers'];
                          $children_ids = $_REQUEST['yith_survey_answer_post_ids'];
                          //update post meta for surveys answer
                          for ($i = 0; $i < count($children_ids); $i++) {

                              $child_id = $children_ids[$i];
                              $answer = $answers[$i];

                              if ( $child_id == -1 ) {

                                  //check if this answer already exsist
                                  $child_id = YITH_Surveys_Type()->is_survey_child_exist( $answer, $free_survey_id );
                                  //if not exsit, create it
                                  if( $child_id == 0 )
                                        $child_id = YITH_Surveys_Type()->add_survey_child( $free_survey_id, $answer );
                              }

                              $args = array(
                                  'ID' => $child_id,
                                  'post_status' => 'publish',
                                  'post_title'  => $answer
                              );

                              wp_update_post($args);

                              $position = $i;
                              update_post_meta( $child_id, '_yith_answer_visible_in_survey','yes' );
                              update_post_meta( $child_id, '_yith_survey_position', $position );
                          }
                      }
                   }
               }
           }
        }

        /**
         * add new survey answer in plugin settings
         * @author YIThemes
         * @since 1.0.0
         */
        public function add_new_survey_answer_admin(){

            if( isset( $_POST['ywcsur_loop'] ) ){

                $loop = $_POST['ywcsur_loop'];
                $params = array(
                    'loop' => $loop,
                );

                $params['params'] = $params;
                ob_start();
                wc_get_template( 'admin/surveys_answer.php', $params,'', YITH_WC_SURVEYS_TEMPLATE_PATH );
                $template = ob_get_contents();
                ob_end_clean();

                wp_send_json( array('result' =>   $template ) );

            }
        }

        /**
         * remove survey answer in plugin settings
         * @author YIThemes
         * @since 1.0.0
         */
        public function remove_survey_answer_admin(){

            if( isset( $_POST['ywcsur_answer_id'] ) ){

                $post_id = $_POST['ywcsur_answer_id'];

                update_post_meta( $post_id, '_yith_answer_visible_in_survey','no' );

                wp_send_json( array( 'result' => 'true' ) );
            }

        }

        /**
         * add order survey meta box
         * @author YIThemes
         * @since 1.0.0
         */
        public function add_order_survey_meta_boxes(){

            add_meta_box( 'yith-wc-order-surveys-metabox', __( 'Surveys', 'yith-woocommerce-surveys' ), array( $this, 'order_surveys_meta_box_content' ), 'shop_order', 'side', 'core' );
        }

        /**
         * print order survey meta_box
         * @author YIThemes
         * @since 1.0.0
         */
        public function order_surveys_meta_box_content(){

          wc_get_template( 'metaboxes/order_surveys_meta_box.php', array(), '', YITH_WC_SURVEYS_TEMPLATE_PATH );
        }




    }
}

/**
 * @return YITH_WC_Surveys_Admin | YITH_WC_Surveys_Admin_Premium
 */
function YITH_Surveys_Admin(){

    if( ! ywcsur_is_premium_active() )
        return  YITH_WC_Surveys_Admin::get_instance();

    return YITH_WC_Surveys_Admin_Premium::get_instance();
}