<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
 
class WCC_Switcher extends WP_Widget {

 
	public function __construct() {
		parent::__construct(
			'wcc_switcher_widget', // Base ID
			__('WCC Switcher Widget', 'wccs'), // Name
			array(
			'description' => __('Currency Switcher Widget', 'wccs'), 
			) // Args
		);
	}
 
	public function widget( $args, $instance ) {
		global $WCCS;
		extract($args);
		/**
		 * Filter
		 * 
		 * @since 1.0.0
		 */
		$title = apply_filters('wcc_switcher_widget_title', $instance['title']);
 
		echo wp_kses_post($before_widget);
		if (! empty($title) ) {
			echo wp_kses_post($before_title) . wp_kses_post($title) . wp_kses_post($after_title);
		}
		
		if (isset($instance['style']) && in_array($instance['style'], array( 'style_01', 'style_02', 'style_03', 'style_04' )) ) {
			$style = $instance['style'];
		} else {
			$style = get_option('wccs_shortcode_style', 'style_01');
		}
		
		$variables = array();
		$variables['class'] = '';
		$variables['default_currency'] = $WCCS->wccs_get_default_currency();
		$variables['default_currency_flag'] = $WCCS->wccs_get_default_currency_flag();
		$variables['default_label'] = wccs_get_currency_label($variables['default_currency']);
		$variables['default_symbol'] = get_woocommerce_currency_symbol($variables['default_currency']);
		$variables['currencies'] = $WCCS->wccs_get_currencies();
		$variables['currency'] = $WCCS->wccs_get_currency();
		$variables['show_currency'] = get_option('wccs_show_currency', 1);
		$variables['show_flag'] = get_option('wccs_show_flag', 1);
			
		$WCCS->render_template(WCCS_PLUGIN_PATH . 'templates/' . $style . '.php', $variables);
		
		echo wp_kses_post($after_widget);
	}
 
	public function form( $instance ) {
		$title = '';
		$style = '';
		if (isset($instance[ 'title' ]) ) {
			$title = $instance[ 'title' ];
		}
		if (isset($instance[ 'style' ]) ) {
			$style = $instance[ 'style' ];
		}
		?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_name('title')); ?>"><?php echo esc_html('Title:', 'wccs'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_name('style')); ?>"><?php echo esc_html('Style:', 'wccs'); ?></label>
			<select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>">
				<option value=""><?php echo esc_html('Select style', 'wccs'); ?></option>
				<option value="style_01"<?php if ('style_01'==$style ) { ?> 
				selected<?php } ?>><?php echo esc_html('Style 1', 'wccs'); ?></option>
				<option value="style_02"<?php if ('style_02'==$style ) { ?> 
				selected<?php } ?>><?php echo esc_html('Style 2', 'wccs'); ?></option>
				<option value="style_03"<?php if ('style_03'==$style ) { ?> 
				selected<?php } ?>><?php echo esc_html('Style 3', 'wccs'); ?></option>
				<option value="style_04"<?php if ('style_04'==$style ) { ?> 
				selected<?php } ?>><?php echo esc_html('Style 4', 'wccs'); ?></option>
			</select>
		</p>
		<?php
	}
 
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( !empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
		$instance['style'] = ( !empty($new_instance['style']) ) ? strip_tags($new_instance['style']) : '';
 
		return $instance;
	}
}
