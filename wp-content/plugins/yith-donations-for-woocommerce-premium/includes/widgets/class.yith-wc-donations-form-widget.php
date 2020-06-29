<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Donations_Form_Widget' ) ) {

	class YITH_Donations_Form_Widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
				'yith_wc_donations_form',
				__( 'YITH Donations for WooCommerce - Form', 'yith-donations-for-woocommerce' ),
				array( 'description' => __( 'Add a simple form to let your customers add donations to the cart!', 'yith-donations-for-woocommerce' ) )
			);

		}


		public function form( $instance ) {

			$title        = isset( $instance['title'] ) ? $instance['title'] : '';
			$multi_amount = isset( $instance['multi_amount'] ) ? $instance['multi_amount'] : '';
			$style        = isset( $instance['multi_amount_style'] ) ? $instance['multi_amount_style'] : 'label';
			$show_donation_reference = isset( $instance['show_donation_reference'] ) ? $instance['show_donation_reference'] : 'off';
			$text_extra_field = isset( $instance['text_extra_field'] ) ? $instance['text_extra_field'] : '';
			$styles = array(
				'radio' => __( 'Radio Button', 'yith-donations-for-woocommerce' ),
				'label'    => __( 'Label', 'yith-donations-for-woocommerce' ),
				'select' => __('Select','yith-donations-for-woocommerce')
			);
			?>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'yith-donations-for-woocommerce' ); ?></label>
                <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                       value="<?php echo $title; ?>"/>
            </p>
            <p>
                <label for="<?php esc_attr_e( $this->get_field_id( 'multi_amount' ) ); ?>"><?php _e( 'Donation pre-set amounts', 'yith-donations-for-woocommerce' ); ?></label>
                <input type="text" class="widefat" id="<?php esc_attr_e( $this->get_field_id( 'multi_amount' ) ); ?>"
                       name="<?php esc_attr_e( $this->get_field_name( 'multi_amount' ) ); ?>"
                       value="<?php echo $multi_amount; ?>">
                <span class="desc" style="font-style: italic;"><?php _e( 'Enter the available donation amounts that your users can choose from. Separate values with | .', 'yith-donations-for-woocommerce' ); ?></span>
            </p>
            <p>
                <label for="<?php esc_attr_e( $this->get_field_id( 'multi_amount_style' ) );?>"><?php _e( 'Style', 'yith-donations-for-woocommece' ); ?></label>
                <select id="<?php esc_attr_e( $this->get_field_id( 'multi_amount_style' ) );?>" name=<?php esc_attr_e( $this->get_field_name( 'multi_amount_style' ) );?>" class="widefat">
                    <?php foreach( $styles as $key=>$type ):?>
                    <option value="<?php echo $key;?>" <?php selected( $key, $style ); ?>><?php echo $type;?></option>
                    <?php endforeach;?>
                </select>
                <span class="desc" style="font-style: italic;"><?php _e('If you\'ve entered pre-set amounts, choose how to display them, either with labels or radio buttons.','yith-donations-for-woocommerce' );?></span>
            </p>
            <p>
                <label for="<?php esc_attr_e( $this->get_field_id( 'show_donation_reference' ) );?>"><?php _e('Show an extra field in the donation form','yith-donations-for-woocommerce' );?></label>
                <input type="checkbox" class="ywcds_show_reference" id="<?php esc_attr_e( $this->get_field_id( 'show_donation_reference' ) );?>" name="<?php esc_attr_e( $this->get_field_name( 'show_donation_reference' ) );?>"<?php checked('on', $show_donation_reference );?>>

            </p>


            <p <?php echo 'off' === $show_donation_reference ? 'style="display: none;"' : '' ;?> >
                <label for="<?php esc_attr_e( $this->get_field_id( 'text_extra_field' ) );?>"><?php _e( 'Extra field label', 'yith-donations-for-woocommerce' );?></label>
                <input type="text" class="widefat ywcds_label_reference" name="<?php esc_attr_e( $this->get_field_name( 'text_extra_field' ) );?>" id="<?php esc_attr_e( $this->get_field_id( 'text_extra_field' ) );?>" value="<?php echo $text_extra_field;?>">
                <span class="desc" style="font-style: italic;"><?php _e('This text appears before the extra field', 'yith-donations-for-woocommerce' );?></span>
            </p>

            <script>
                jQuery(document).ready(function($){
                   $(document).on('change','.ywcds_show_reference',function(e){

                       var widget_content = $(this).parent().parent(),
                           extra_field = widget_content.find('.ywcds_label_reference').parent();

                       if( $(this).is(':checked')){
                           extra_field.show();
                       }else{
                           extra_field.hide();
                       }
                   }) ;
                });
            </script>
			<?php

		}


		public function update( $new_instance, $old_instance ) {

			$instance = array();

			$instance['title'] = isset( $new_instance['title'] ) ? $new_instance['title'] : '';
			$instance['multi_amount'] = isset( $new_instance['multi_amount'] ) ? $new_instance['multi_amount'] : '';
			$instance['multi_amount_style'] = isset( $new_instance['multi_amount_style'] ) ? $new_instance['multi_amount_style'] : 'label';
			$instance['show_donation_reference'] = isset( $new_instance['show_donation_reference'] ) ? 'on' : 'off';
			$instance['text_extra_field'] = isset( $new_instance['text_extra_field'] ) ? wp_strip_all_tags( $new_instance['text_extra_field'] ): '';
			$instance['text_extra_field'] = str_replace( ':','', $instance['text_extra_field'] );

			return $instance;

		}


		public function widget( $args, $instance ) {

			$title = apply_filters( 'widget_title', $instance['title'] );
			$multi_amount =  isset( $instance['multi_amount'] ) ? $instance['multi_amount'] :'';
			$style =  isset( $instance['multi_amount_style'] ) ? $instance['multi_amount_style'] : '';
			$show_donation_reference = isset( $instance['show_donation_reference'] ) ? $instance['show_donation_reference'] : '';
			$text_extra_field = isset( $instance['text_extra_field'] ) ? $instance['text_extra_field'] : '';
			$text_extra_field = apply_filters( 'widget_title',$text_extra_field );


			$extra_args = '';
			if( !empty( $multi_amount ) ){

			    $extra_args = 'donation_amount= "'.$multi_amount.'" donation_amount_style="'.$style.'"';
            }

            if( 'on' == $show_donation_reference ){
                $extra_args.= " show_extra_desc='".$show_donation_reference."' extra_desc_label='".$text_extra_field."'";
            }


			echo $args['before_widget'];
			echo $args['before_title'] . $title . $args['after_title'];
			echo do_shortcode( '[yith_wcds_donations '.$extra_args.' ]' );
			echo $args['after_widget'];
		}
	}
}
