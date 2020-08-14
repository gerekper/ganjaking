<?php
if( !defined( 'ABSPATH' ) )
    exit;

if ( !class_exists( 'YITH_WC_Surveys_Table' ) ) {
    require_once( YITH_WC_SURVEYS_INC . 'classes/class.yith-wc-surveys-table.php' );
}
if ( !class_exists( 'YITH_WC_Surveys_List_Table' ) ) {
    require_once( YITH_WC_SURVEYS_INC . 'classes/class.yith-wc-surveys-list-table.php' );
}



if( !class_exists( 'YITH_WC_Surveys_Report' ) ){

    class YITH_WC_Surveys_Report{

        /**
         * Single instance of the class
         *
         * @var \YITH_WC_Surveys_Report
         * @since 1.0.0
         */
        protected static $instance;


        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Surveys_Report
         * @since 1.0.0
         */
        public static function get_instance() {

            if ( is_null( self::$instance ) ) {

                self::$instance = new self( $_REQUEST );

            }

            return self::$instance;
        }

        /**
         * Constructor
         *
         * @since   1.0.0
         * @return  mixed
         * @author  Yithemes
         */
        public function __construct() {

            add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
            add_action( 'current_screen', array( $this, 'add_options' ) );

        }


        /**
         * print table
         * @author Yithemes
         * @since 1.0.0
         *
         */
        public function output(){

            if( !isset( $_GET['survey_id'] ) )
               $table = new YITH_WC_Surveys_List_Table();
            else
            $table = new YITH_WC_Surveys_Table( $_GET['survey_id']);

            $table->prepare_items();

            $list_query_args = array(
                'page'    => $_GET['page'],
                //'tab'   => $_GET['tab']
            );


            $list_url = esc_url( add_query_arg( $list_query_args, admin_url( 'edit.php?post_type=yith_wc_surveys&page=survey-report' ) ) );

        ?>
            <div class="wrap">
                <div class="icon32 icon32-posts-post" id="icon-edit"><br/></div>
                <h2><?php _e('Report', 'yith-woocommerce-surveys');?></h2>
                <form id="custom-table" method="GET" action="<?php echo $list_url; ?>">
                    <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>"/>
                    <?php $table->display(); ?>

                <?php if(  isset( $_GET['survey_id'] )  && isset( $_GET['action'] ) ):?>
                        <a class="button-secondary" href="<?php echo $list_url; ?>"><?php _e( 'Return to rule list', 'yith-woocommerce-name-your-price' ); ?></a>
               <?php endif; ?>
                </form>
            </div>
        <?php


        }


        /**
         * Add screen options for list table template
         *
         * @since   1.0.0
         * @return  void
         * @author  Alberto Ruggiero
         */
        public function add_options() {


            if (  ( isset( $_GET['page'] ) && $_GET['page'] == 'survey-report' ) && ( isset( $_GET['tab'] ) && $_GET['tab'] == 'survey-reports' ) && ( !isset( $_GET['action'] ) || ( $_GET['action'] != 'edit' && $_GET['action'] != 'insert' ) ) ) {

                $option = 'per_page';

                $args = array(
                    'label'   => __( 'Surveys', 'yith-woocommerce-surveys' ),
                    'default' => 10,
                    'option'  => 'items_per_page'
                );

                add_screen_option( $option, $args );

            }

        }

        /**
         * Set screen options for list table template
         *
         * @since   1.0.0
         *
         * @param   $status
         * @param   $option
         * @param   $value
         *
         * @return  mixed
         * @author  Alberto Ruggiero
         */
        public function set_options( $status, $option, $value ) {

            return ( 'items_per_page' == $option ) ? $value : $status;

        }

    }
}
/**
 * return single instance
 * @author Yithemes
 * @since 1.0.0
 * @return YITH_WC_Surveys_Report
 */
function YITH_Surveys_Report(){

    return YITH_WC_Surveys_Report::get_instance();
}

