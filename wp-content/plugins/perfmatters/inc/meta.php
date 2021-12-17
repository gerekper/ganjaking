<?php
//add meta box
function perfmatters_add_meta_box() {

	//get plugin options
	$perfmatters_options = get_option('perfmatters_options');

	if(!empty($perfmatters_options['assets']['defer_js']) 
		|| !empty($perfmatters_options['assets']['delay_js']) 
		|| !empty($perfmatters_options['lazyload']['lazy_loading']) 
		|| !empty($perfmatters_options['lazyload']['lazy_loading_iframes']) 
		|| !empty($perfmatters_options['preload']['instant_page'])) 
	{

		//get public post types
		$post_types = get_post_types(array('public' => true));

	    add_meta_box('perfmatters', 'Perfmatters', 'perfmatters_load_meta_box', $post_types, 'side', 'high');
	}
}
add_action('add_meta_boxes', 'perfmatters_add_meta_box', 1);

//display meta box
function perfmatters_load_meta_box() {

	global $post;

	//get plugin options
	$perfmatters_options = get_option('perfmatters_options');

	//inline styles
	echo '<style>
		#perfmatters.postbox .postbox-header .hndle {
			border-bottom: none;
		}
	</style>';

	//noncename needed to verify where the data originated
	echo '<input type="hidden" name="perfmatters_meta_noncename" id="perfmatters_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
	
	//exclude defer js
	$exclude_defer_js = get_post_meta($post->ID, 'perfmatters_exclude_defer_js', true);
	echo "<div" . (empty($perfmatters_options['assets']['defer_js']) ? " class='hidden'" : "") . ">";
		echo "<label for='perfmatters_exclude_defer_js'>";
			echo "<input type='hidden' name='perfmatters_exclude_defer_js' value='1' />";
			echo "<input type='checkbox' name='perfmatters_exclude_defer_js' id='perfmatters_exclude_defer_js'" . (empty($exclude_defer_js) ? " checked" : "") . " value='' class='widefat' />";
			_e('Defer JavaScript', 'perfmatters');
		echo "</label>";
	echo "</div>";

	//exclude delay js
	$exclude_delay_js = get_post_meta($post->ID, 'perfmatters_exclude_delay_js', true);
	echo "<div" . (empty($perfmatters_options['assets']['delay_js']) ? " class='hidden'" : "") . ">";
		echo "<label for='perfmatters_exclude_delay_js'>";
			echo "<input type='hidden' name='perfmatters_exclude_delay_js' value='1' />";
			echo "<input type='checkbox' name='perfmatters_exclude_delay_js' id='perfmatters_exclude_delay_js'" . (empty($exclude_delay_js) ? " checked" : "") . " value='' class='widefat' />";
			_e('Delay JavaScript', 'perfmatters');
		echo "</label>";
	echo "</div>";

	//exclude lazy loading
	$exclude_lazy_loading = get_post_meta($post->ID, 'perfmatters_exclude_lazy_loading', true);
	echo "<div" . (empty($perfmatters_options['lazyload']['lazy_loading']) && empty($perfmatters_options['lazyload']['lazy_loading_iframes']) ? " class='hidden'" : "") . ">";
		echo "<label for='perfmatters_exclude_lazy_loading'>";
			echo "<input type='hidden' name='perfmatters_exclude_lazy_loading' value='1' />";
			echo "<input type='checkbox' name='perfmatters_exclude_lazy_loading' id='perfmatters_exclude_lazy_loading'" . (empty($exclude_lazy_loading) ? " checked" : "") . " value='' class='widefat' />";
			_e('Lazy Loading', 'perfmatters');
		echo "</label>";
	echo "</div>";

	//exclude instant page
	$exclude_instant_page = get_post_meta($post->ID, 'perfmatters_exclude_instant_page', true);
	echo "<div" . (empty($perfmatters_options['preload']['instant_page']) ? " class='hidden'" : "") . ">";
		echo "<label for='perfmatters_exclude_instant_page'>";
			echo "<input type='hidden' name='perfmatters_exclude_instant_page' value='1' />";
			echo "<input type='checkbox' name='perfmatters_exclude_instant_page' id='perfmatters_exclude_instant_page'" . (empty($exclude_instant_page) ? " checked" : "") . " value='' class='widefat' />";
			_e('Instant Page', 'perfmatters');
		echo "</label>";
	echo "</div>";
}

//save meta box data
function perfmatters_save_meta($post_id, $post) {
	
	//verify this came from the our screen and with proper authorization, because save_post can be triggered at other times
	if(empty($_POST['perfmatters_meta_noncename']) || !wp_verify_nonce($_POST['perfmatters_meta_noncename'], plugin_basename(__FILE__))) {
		return $post->ID;
	}

	//permissions check
	if(!current_user_can('edit_post', $post->ID)) {
		return $post->ID;
	}
		
	//saved data
	$perfmatters_meta = array();
	$perfmatters_meta['perfmatters_exclude_defer_js'] = (isset($_POST['perfmatters_exclude_defer_js']) ? $_POST['perfmatters_exclude_defer_js'] : "");
	$perfmatters_meta['perfmatters_exclude_delay_js'] = (isset($_POST['perfmatters_exclude_delay_js']) ? $_POST['perfmatters_exclude_delay_js'] : "");
	$perfmatters_meta['perfmatters_exclude_lazy_loading'] = (isset($_POST['perfmatters_exclude_lazy_loading']) ? $_POST['perfmatters_exclude_lazy_loading'] : "");
	$perfmatters_meta['perfmatters_exclude_instant_page'] = (isset($_POST['perfmatters_exclude_instant_page']) ? $_POST['perfmatters_exclude_instant_page'] : "");
	
	foreach($perfmatters_meta as $key => $value) {

		//dont save for revisions
		if($post->post_type == 'revision') {
			return;
		}

		//update post meta value
		if(!empty($value) || get_post_meta($post->ID, $key, true) != false) {
			update_post_meta($post->ID, $key, $value);
		}
	}
}
add_action('save_post', 'perfmatters_save_meta', 1, 2);