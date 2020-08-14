<?php
if( !defined('ABSPATH')){
    exit;
}

if( !class_exists('YITH_YWF_Make_a_Deposit_Widget')){

    class YITH_YWF_Make_a_Deposit_Widget extends WP_Widget{
        /**
         * YITH_YWF_Make_a_Deposit_Widget constructor.
         */
        public function __construct()
        {
            parent::__construct( 'yith_ywf_make_a_deposit_widget', __('YITH Account Funds: Make a deposit widget','yith-woocommerce-account-funds'), array(
                'description' => __('Shows the form to let customers deposit funds', 'yith-woocommerce-account-funds') ) );

        }

        /**
         * @author YITHEMES
         * @since 1.0.0
         * @param array $instance
         */
        public function form( $instance )
        {
            $title  = isset( $instance['title'] ) ? $instance['title'] : '';
            ?>
            <p>
                <label for="<?php echo $this->get_field_id("title");?>"><?php _e('Title','yith-woocommerce-account-funds');?></label>
                <input type="text" id="<?php echo $this->get_field_id("title");?>" name="<?php echo $this->get_field_name("title");?>" value="<?php echo $title;?>"/>
            </p>
        <?php
        }

        /**
         * @author YITHEMES
         * @since 1.0.0
         * @param array $new_instance
         * @param array $old_instance
         * @return array
         */
        public function update( $new_instance, $old_instance )
        {
           $instance = array();
            $instance['title'] = isset( $new_instance['title'] ) ? $new_instance['title'] : '';

            return $instance;
        }

        /**
         * @author YITHEMES
         * @since 1.0.0
         * @param array $args
         * @param array $instance
         */
        public function widget( $args, $instance )
        {
            echo $args['before_widget'];
            if( isset( $instance['title'] ) ){
                echo $args['before_title'].$instance['title'].$args['after_title'];
            }
            echo do_shortcode('[yith_ywf_make_a_deposit_form]');
            echo $args['after_widget'];
        }
    }
}