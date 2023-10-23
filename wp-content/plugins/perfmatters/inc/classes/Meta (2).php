<?php
namespace Perfmatters;

class Meta
{
	private static $meta_options;

	//initialize meta functions
    public static function init()
    {
    	if(!is_admin() || !current_user_can('manage_options')) {
    		return;
    	}

		global $pagenow;

		if(in_array($pagenow, ['post.php', 'new-post.php'])) {

			//setup meta options
			self::get_meta_options();

			//exclude specific woocommerce pages
            if(class_exists('WooCommerce') && !empty($_GET['post'])) {

            	$wc_pages = array_filter(array(
					get_option('woocommerce_cart_page_id'),
					get_option('woocommerce_checkout_page_id'),
					get_option('woocommerce_myaccount_page_id')
            	));

                if(in_array($_GET['post'], $wc_pages)) {
                	return;
                }
            }

			//meta actions
			add_action('add_meta_boxes', array('Perfmatters\Meta', 'add_meta_boxes'), 1);
        	add_action('save_post', array('Perfmatters\Meta', 'save_meta'), 1, 2);
		}
		add_action('wp_ajax_perfmatters_purge_meta', array('Perfmatters\Meta', 'purge_meta_ajax'));
    }

    //add meta boxes
    public static function add_meta_boxes() {
    	foreach(self::$meta_options as $id => $details) {
    		if($details['value']) {

    			//display meta box if at least one value is set
    			add_meta_box('perfmatters', 'Perfmatters', array('Perfmatters\Meta', 'load_meta_box'), get_post_types(array('public' => true)), 'side', 'high');
    			break;
    		}
    	}
    }

    //display meta box
	public static function load_meta_box() {

		global $post;

		//noncename needed to verify where the data originated
		echo '<input type="hidden" name="perfmatters_meta_noncename" id="perfmatters_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

		//print inputs
		foreach(self::$meta_options as $id => $details) {

			//existing meta value
			$meta = get_post_meta($post->ID, 'perfmatters_exclude_' . $id, true);

			//individual input
			echo '<div' . (!$details['value'] ? ' class="hidden"' : '') . '>';
				echo '<label for="perfmatters_exclude_' . $id . '">';
					echo '<input type="hidden" name="perfmatters_exclude_' . $id . '" value="1" />';
					echo '<input type="checkbox" name="perfmatters_exclude_' . $id . '" id="perfmatters_exclude_' . $id . '"' . (!$meta ? " checked" : "") . ' value="" class="widefat" />';
					echo $details['name'];
				echo '</label>';
			echo '</div>';
		}

		//clear used css
		if(!empty(Config::$options['assets']['remove_unused_css'])) {
			
			echo '<style>.perfmatters-clear-post-used-css{display:inline-block;margin-top:10px}.perfmatters-clear-post-used-css .spinner{display:none;float:none}.perfmatters-clear-post-used-css .spinner.is-active{display:inline-block}.perfmatters-clear-post-used-css .dashicons-yes{display:none;margin:2px 10px;color:green;font-size:26px}</style>';

			echo '<div class="perfmatters-clear-post-used-css">';
				echo '<a class="button button-secondary" id="perfmatters-clear-post-used-css" value="1">' . __('Clear Used CSS', 'perfmatters') . '</a>';
				echo '<span class="spinner"></span>';
				echo '<span class="dashicons dashicons-yes"></span>';
				echo wp_nonce_field('perfmatters_clear_post_used_css', 'perfmatters_clear_post_used_css', false, false);
			echo '</div>';

			echo '<script>jQuery(document).ready(function(s){s("#perfmatters-clear-post-used-css").click(function(t){if(t.preventDefault(),$button=s(this),$button.hasClass("disabled"))return!1;$button.addClass("disabled"),$button.siblings(".spinner").addClass("is-active");var e={action:"perfmatters_clear_post_used_css",nonce:$button.siblings("#perfmatters_clear_post_used_css").val(),post_id:parseInt(s("#post_ID").val())};s.post(ajaxurl,e,function(s){$button.siblings(".spinner").removeClass("is-active"),$button.siblings(".dashicons-yes").fadeIn().css("display","inline-block"),setTimeout(function(){$button.siblings(".dashicons-yes").fadeOut()},1e3),$button.removeClass("disabled")})})});</script>';
		}
	}

	//save meta box data
	public static function save_meta($post_id, $post) {
		
		//verify this came from the our screen and with proper authorization, because save_post can be triggered at other times
		if(empty($_POST['perfmatters_meta_noncename']) || !wp_verify_nonce($_POST['perfmatters_meta_noncename'], plugin_basename(__FILE__))) {
			return;
		}

		//dont save for revisions
		if($post->post_type == 'revision') {
			return;
		}
			
		//saved data
		$perfmatters_meta = array();
		foreach(self::$meta_options as $id => $details) {
			$key = 'perfmatters_exclude_' . $id;
			if(!empty($_POST[$key]) || get_post_meta($post->ID, $key, true) != false) {

				//update option in post meta
				update_post_meta($post->ID, $key, $_POST[$key] ?? "");
			}
		}
	}

	//populate meta options array for other functions
	private static function get_meta_options() {
		self::$meta_options = array(
			'defer_js'     => array(
				'name'     => __('Defer JavaScript', 'perfmatters'),
				'value'    => !empty(Config::$options['assets']['defer_js'])
			),
			'delay_js'     => array(
				'name'     => __('Delay JavaScript', 'perfmatters'),
				'value'    => !empty(Config::$options['assets']['delay_js'])
			),
			'unused_css'   => array(
				'name'     => __('Unused CSS', 'perfmatters'),
				'value'    => !empty(Config::$options['assets']['remove_unused_css'])
			),
			'lazy_loading' => array(
				'name'     => __('Lazy Loading', 'perfmatters'),
				'value'    => !empty(Config::$options['lazyload']['lazy_loading']) || !empty(Config::$options['lazyload']['lazy_loading_iframes'])
			),
			'instant_page' => array(
				'name'     => __('Instant Page', 'perfmatters'),
				'value'    => !empty(Config::$options['preload']['instant_page'])
			)
		);
	}

	//purge meta ajax action
	public static function purge_meta_ajax() {

		Ajax::security_check();

		parse_str(stripslashes($_POST['form']), $form);
		
		//no meta options selected
		if(empty($form['perfmatters_tools_temp']['purge_meta_options'])) {
			wp_send_json_error(array(
		    	'message' => __('No meta options selected.', 'perfmatters')
			));
		}

		global $wpdb;

		$purged = array();

		//delete selected options from postmeta table
		foreach($form['perfmatters_tools_temp']['purge_meta_options'] as $key => $meta_key) {

			$result = $wpdb->delete($wpdb->prefix . 'postmeta', array('meta_key' => $meta_key));

			if($result !== false) {
				$purged[] = $meta_key;
			}
		}

		//display message
		if(!empty($purged)) {
			wp_send_json_success(array(
		    	'message' => __('Meta options purged.', 'perfmatters')
			));
		}
		else {
			wp_send_json_error(array(
		    	'message' => __('Meta options not purged.', 'perfmatters')
			));
		}
	}
}