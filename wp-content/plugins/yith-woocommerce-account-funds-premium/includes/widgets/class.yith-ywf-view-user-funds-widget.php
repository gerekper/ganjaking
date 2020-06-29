<?php
if( !defined('ABSPATH')){
    exit;
}

if( !class_exists('YITH_YWF_View_User_Funds_Widget')){

    class YITH_YWF_View_User_Funds_Widget extends WP_Widget{
        /**
         * YITH_YWF_Make_a_Deposit_Widget constructor.
         */
        public function __construct()
        {
            parent::__construct( 'yith_ywf_view_user_funds_widget', __('YITH Account Funds: View user funds widget','yith-woocommerce-account-funds'), array(
                'description' => __('Shows customer\'s available funds', 'yith-woocommerce-account-funds') ) );

        }

        /**
         * @author YITHEMES
         * @since 1.0.0
         * @param array $instance
         */
        public function form( $instance )
        {
            $title  = isset( $instance['title'] ) ? $instance['title'] : '';
            $text_align = isset( $instance['text_align'] ) ? $instance['text_align'] : 'left';
            $font_weight = isset( $instance['font_weight'] ) ? $instance['font_weight'] : 'normal';
            
            ?>
            <p>
                <label for="<?php echo $this->get_field_id("title");?>"><?php _e('Title','yith-woocommerce-account-funds');?></label>
                <input type="text" id="<?php echo $this->get_field_id("title");?>" name="<?php echo $this->get_field_name("title");?>" value="<?php echo $title;?>"/>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id("text_align");?>"><?php _e('Text alignment','yith-woocommerce-account-funds');?></label>
                <select id="<?php echo $this->get_field_id("text_align");?>" name="<?php echo $this->get_field_name("text_align");?>">
                    <option value="left" <?php selected('left', $text_align );?>><?php _e('Left','yith-woocommerce-account-funds');?></option>
                    <option value="center" <?php selected('center', $text_align );?>><?php _e('Center','yith-woocommerce-account-funds');?></option>
                    <option value="right" <?php selected('right', $text_align );?>><?php _e('Right','yith-woocommerce-account-funds');?></option>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id("font_weight");?>"><?php _e('Font weight','yith-woocommerce-account-funds');?></label>
                <select id="<?php echo $this->get_field_id("font_weight");?>" name="<?php echo $this->get_field_name("font_weight");?>">
                    <option value="normal" <?php selected('normal', $font_weight );?>><?php _e('Normal','yith-woocommerce-account-funds');?></option>
                    <option value="bold" <?php selected('bold', $font_weight );?>><?php _e('Bold','yith-woocommerce-account-funds');?></option>
                </select>
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
            $instance['text_align'] = isset( $new_instance['text_align'] ) ? $new_instance['text_align'] : 'left';
            $instance['font_weight'] = isset( $new_instance['font_weight'] )?$new_instance['font_weight'] : 'normal';

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
            echo do_shortcode('[yith_ywf_show_user_fund  text_align="'.$instance['text_align'].'" font_weight="'.$instance['font_weight'].'"]');
            echo $args['after_widget'];
        }
    }
}