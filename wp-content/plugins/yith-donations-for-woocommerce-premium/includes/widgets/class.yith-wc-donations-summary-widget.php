<?php

if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'YITH_Donations_Summary_Widget' ) ){

    class YITH_Donations_Summary_Widget extends  WP_Widget{

        public function __construct()
        {
            parent::__construct(
                'yith_wc_donations_summary',
                __('YITH Donations for WooCommerce - Summary', 'yith-donations-for-woocommerce'),
                array('description' => __('Show users the number of donations made so far!', 'yith-donations-for-woocommerce'))
            );

        }


        public function form( $instance )
        {

            $title  =   isset( $instance['title'] ) ? $instance['title'] : '';
            $summary_from   =   isset( $instance['summary_from'] ) ? $instance['summary_from'] : 'week';
            $include_tax = isset( $instance['include_tax'] ) ?   $instance['include_tax'] : 'off';

            ?>

            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title'));?>"><?php _e('Title', 'yith-donations-for-woocommerce');?></label>
                <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>"
                       name="<?php echo esc_attr($this->get_field_name('title'));?>" value="<?php echo $title;?>"/>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('summary_from') );?>"><?php _e( 'Show donations of', 'yith-donations-for-woocommerce');?></label>
                <select id="<?php echo esc_attr( $this->get_field_id('summary_from') );?>" name="<?php echo esc_attr( $this->get_field_name('summary_from') );?>">
                    <option value="day" <?php  selected('day', $summary_from );?> ><?php _e('Today', 'yith-donations-for-woocommerce');?></option>
                    <option value="week" <?php  selected('week', $summary_from );?> ><?php _e('Last week', 'yith-donations-for-woocommerce');?></option>
                    <option value="last_month" <?php  selected('last_month', $summary_from );?> ><?php _e('Last month', 'yith-donations-for-woocommerce');?></option>
                    <option value="month" <?php  selected('month', $summary_from );?> ><?php _e('This month', 'yith-donations-for-woocommerce');?></option>
                    <option value="year" <?php  selected('year', $summary_from );?> ><?php _e('Last year', 'yith-donations-for-woocommerce');?></option>
                    <option value="always" <?php  selected('always', $summary_from );?> ><?php _e('Ever', 'yith-donations-for-woocommerce');?></option>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'include_tax' ) );?>"><?php _e( 'Include tax in total', 'yith-donations-for-woocommerce' );?></label>
                <input type="checkbox" <?php  checked( 'on', $include_tax );?> name="<?php echo esc_attr( $this->get_field_name('include_tax') );?>" id="<?php echo esc_attr( $this->get_field_id('include_tax') );?>">
            </p>
        <?php

        }


        public function update($new_instance, $old_instance) {

            $instance   =   array();
            $instance['title']  =   isset( $new_instance['title'] ) ? $new_instance['title'] : '';
            $instance['summary_from']   =   isset( $new_instance['summary_from'] ) ? $new_instance['summary_from'] : 'week';
            $instance['include_tax']   =   isset( $new_instance['include_tax'] ) ? $new_instance['include_tax'] : 'off';

            return $instance;

        }


        public function widget( $args, $instance ){

            $args_summ  =   array(
              'summary_from'    =>  $instance['summary_from'],
               'include_tax'   => $instance['include_tax']
            );

            $title  =   apply_filters( 'widget_title', $instance['title'] );
            echo $args['before_widget'];
            echo '<div class="ywcds_widget_summary">';
            echo $args['before_title'].$title.$args['after_title'];
            echo wc_get_template_html( 'summary-donations.php', $args_summ, YWCDS_TEMPLATE_PATH, YWCDS_TEMPLATE_PATH  );
            echo '</div>';
            echo $args['after_widget'];
        }
    }
}