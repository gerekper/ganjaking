<?php
/**
 * The main class for managing the recommendation options. 
 */
class WC_Recommender_Admin_Settings {
	
	private static $instance;
	public static function register(){
		if (self::$instance == null){
			self::$instance = new WC_Recommender_Admin_Settings();
		}
	}


	/**
	 * Creates a new instance of the WC_Recommender_Admin class. 
	 */
	public function __construct() {

		$this->current_tab = ( isset($_GET['tab']) ) ? $_GET['tab'] : 'general';
		$this->settings_tabs = array(
		    'recommender_options' => __('Recommendations', 'wc_recommender')
		);

		add_action('woocommerce_settings_tabs', array(&$this, 'on_add_tab'), 10);

		// Run these actions when generating the settings tabs.
		foreach ($this->settings_tabs as $name => $label) {
			add_action('woocommerce_settings_tabs_' . $name, array(&$this, 'settings_tab_action'), 10);
			add_action('woocommerce_update_options_' . $name, array(&$this, 'save_settings'), 10);
		}

		// Add the settings fields to each tab.
		add_action('woocommerce_recommender_options_settings', array(&$this, 'add_settings_fields'), 10);
		add_action('woocommerce_admin_field_editor', array(&$this, 'on_editor_field'));
	}

	/*
	 * Admin Functions
	 */

	/** ----------------------------------------------------------------------------------- */
	/* Admin Tabs */
	/* ----------------------------------------------------------------------------------- */
	function on_add_tab() {
		
		$page =  WC_Recommender_Compatibility::is_wc_version_gte_2_2() ? 'wc-settings' : 'woocommerce';
		
		foreach ($this->settings_tabs as $name => $label) :
			$class = 'nav-tab';
			if ($this->current_tab == $name)
				$class .= ' nav-tab-active';
			echo '<a href="' . admin_url('admin.php?page=' . $page . '&tab=' . $name) . '" class="' . $class . '">' . $label . '</a>';
		endforeach;
	}

	/**
	 * settings_tab_action()
	 *
	 * Do this when viewing our custom settings tab(s). One function for all tabs.
	 */
	function settings_tab_action() {
		global $woocommerce_settings;

		// Determine the current tab in effect.
		$current_tab = $this->get_tab_in_view(current_filter(), 'woocommerce_settings_tabs_');

		// Hook onto this from another function to keep things clean.

		$links = array('<a href="#recommender_options">' . __('Recommendations', 'wc_recommender') . '</a>');
		//$links[] = '<a href="#recommender_recording_options">' . __('Activity to Track', 'wc_recommender') . '</a>';
		//echo '<div class="subsubsub_section"><ul class="subsubsub"><li>' . implode(' | </li><li>', $links) . '</li></ul><br class="clear" />';

		echo '<div class="section" id="recommender_options">';

		do_action('woocommerce_recommender_options_settings');

		// Display settings for this tab (make sure to add the settings to the tab).
		woocommerce_admin_fields($woocommerce_settings[$current_tab]);

		echo '</div>';

		//Include the subsections
		//echo '<div class="section" id="recommender_recording_options">';
		//include 'roles-table.php';
		//echo '</div>';

		//echo '</div>';
	}

	/**
	 * add_settings_fields()
	 *
	 * Add settings fields for each tab.
	 */
	function add_settings_fields() {
		global $woocommerce_settings;

		// Load the prepared form fields.
		$this->init_form_fields();

		if (is_array($this->fields)) :
			foreach ($this->fields as $k => $v) :
				$woocommerce_settings[$k] = $v;
			endforeach;
		endif;
	}

	/**
	 * get_tab_in_view()
	 *
	 * Get the tab current in view/processing.
	 */
	function get_tab_in_view($current_filter, $filter_base) {
		return str_replace($filter_base, '', $current_filter);
	}

	/**
	 * init_form_fields()
	 *
	 * Prepare form fields to be used in the various tabs.
	 */
	function init_form_fields() {
		// Define settings			
		$this->fields['recommender_options'] = apply_filters('woocommerce_recommender_options_settings_fields', array(
		    array(
			'name' => __('Types of Recommendations to Show', 'wc_recommender'),
			'type' => 'title',
			'desc' => '',
			'id' => 'recommender_options'
		    ),
		    
		    array(
			'name' => __('Show Related Products by Purchase History', 'wc_recommender'),
			'desc' => '',
			'css' => 'min-width:300px;',
			'id' => 'wc_recommender_rbph_enabled',
			'type' => 'select',
			'std' => 'enabled',
			'default' => 'enabled',
			'class' => 'chosen_select',
			'options' => array('enabled' => 'Enabled', 'disabled' => 'Disabled')
		    ),
		    array(
			'name' => __('Show Related Products by Views', 'wc_recommender'),
			'desc' => '',
			'css' => 'min-width:300px;',
			'id' => 'wc_recommender_rbpv_enabled',
			'type' => 'select',
			'std' => 'enabled',
			'default' => 'enabled',
			'class' => 'chosen_select',
			'options' => array('enabled' => 'Enabled', 'disabled' => 'Disabled')
		    ),
		    array(
			'name' => __('Show Products Frequently Purchased Together', 'wc_recommender'),
			'desc' => '',
			'css' => 'min-width:300px;',
			'id' => 'wc_recommender_fpt_enabled',
			'type' => 'select',
			'std' => 'enabled',
			'default' => 'enabled',
			'class' => 'chosen_select',
			'options' => array('enabled' => 'Enabled', 'disabled' => 'Disabled')
		    ),
		    array(
			'name' => __('Built in Related Products', 'wc_recommender'),
			'desc' => '',
			'css' => 'min-width:300px;',
			'id' => 'wc_recommender_builtin_enabled',
			'type' => 'select',
			'std' => 'enabled',
			'default' => 'enabled',
			'class' => 'chosen_select',
			'options' => array('enabled' => 'Enabled', 'disabled' => 'Disabled')
		    ),
		    array(
			'name' => __('Number of Items to Show', 'wc_recommender'),
			'type' => 'text',
			'desc' => 'The total number of items you would like to show.',
			'css' => 'min-width:300px;',
			'default' => __('2', 'wc_recommender'),
			'id' => 'wc_recommender_item_count'
		    ),
		     array(
			'name' => __('Number of Columns', 'wc_recommender'),
			'type' => 'text',
			'desc' => 'The number of columns to use to display the items.',
			'css' => 'min-width:300px;',
			'default' => __('2', 'wc_recommender'),
			'id' => 'wc_recommender_column_count'
		    ),
		    
		    array('type' => 'sectionend', 'id' => 'recommender_options'),
		    
		    array(
			'name' => __('Titles / Labels', 'wc_recommender'),
			'type' => 'title',
			'desc' => '',
			'id' => 'recommender_labels'
		    ),
		    array(
			'name' => __('Recommended by Purchase History Label', 'wc_recommender'),
			'type' => 'text',
			'desc' => '',
			'css' => 'min-width:300px;',
			'default' => __('Customers also purchased these products', 'wc_recommender'),
			'id' => 'wc_recommender_label_rbph'
		    ),
		    array(
			'name' => __('Recommended by Views Label', 'wc_recommender'),
			'type' => 'text',
			'desc' => '',
			'css' => 'min-width:300px;',
			'default' => __('Customers who viewed this item also viewed these products', 'wc_recommender'),
			'id' => 'wc_recommender_label_rbpv'
		    ),
		    array(
			'name' => __('Frequently Purchased Together Label', 'wc_recommender'),
			'type' => 'text',
			'desc' => '',
			'css' => 'min-width:300px;',
			'default' => __('Frequently purchased together', 'wc_recommender'),
			'id' => 'wc_recommender_label_fpt'
		    ),
		    
		    array('type' => 'sectionend', 'id' => 'recommender_labels'),
		    
		    array(
			'name' => __('Position of enabled recommendations', 'wc_recommender'),
			'type' => 'title',
			'desc' => '',
			'id' => 'recommender_sorting'
		    ),
		    array(
			'name' => __('Related Products by Purchase History', 'wc_recommender'),
			'desc' => '',
			'css' => 'min-width:300px;',
			'id' => 'wc_recommender_rbph_sort',
			'type' => 'select',
			'std' => '21',
			'default' => '21',
			'class' => 'chosen_select',
			'options' => array(
			    '21' => __('First', 'wc_recommender'),
			    '22' => __('Second', 'wc_recommender'),
			    '23' => __('Third', 'wc_recommender'),
			)
		    ),
		    array(
			'name' => __('Related Products by Views', 'wc_recommender'),
			'desc' => '',
			'css' => 'min-width:300px;',
			'id' => 'wc_recommender_rbpv_sort',
			'type' => 'select',
			'std' => '22',
			'default' => '22',
			'class' => 'chosen_select',
			'options' => array(
			    '21' => __('First', 'wc_recommender'),
			    '22' => __('Second', 'wc_recommender'),
			    '23' => __('Third', 'wc_recommender'),
			)
		    ),
		    array(
			'name' => __('Products Frequently Purchased Together', 'wc_recommender'),
			'desc' => '',
			'css' => 'min-width:300px;',
			'id' => 'wc_recommender_fpt_sort',
			'type' => 'select',
			'std' => '23',
			'default' => '23',
			'class' => 'chosen_select',
			'options' => array(
			    '21' => __('First', 'wc_recommender'),
			    '22' => __('Second', 'wc_recommender'),
			    '23' => __('Third', 'wc_recommender'),
			)
		    ),
		    
		    array('type' => 'sectionend', 'id' => 'recommender_sorting')
		  )
		);
	}

	/**
	 * save_settings()
	 *
	 * Save settings in a single field in the database for each tab's fields (one field per tab).
	 */
	function save_settings() {
		global $woocommerce_settings;

		// Make sure our settings fields are recognised.
		$this->add_settings_fields();

		$current_tab = $this->get_tab_in_view(current_filter(), 'woocommerce_update_options_');


		woocommerce_update_options($woocommerce_settings[$current_tab]);
	}

	/** Helper functions ***************************************************** */
	/**
	 * Gets a setting
	 */
	public function setting($key) {
		return get_option($key);
	}

	/**
	 * Get the custom admin field: editor
	 */
	public function on_editor_field($value) {
		$content = get_option($value['id']);
		?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
			<td class="forminp">
		<?php wp_editor($content, $value['id']); ?>
			</td>
		</tr>
		<?php
	}
}

