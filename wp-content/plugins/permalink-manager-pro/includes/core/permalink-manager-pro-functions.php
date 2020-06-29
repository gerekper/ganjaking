<?php

/**
* Additional hooks for "Permalink Manager Pro"
*/
class Permalink_Manager_Pro_Functions extends Permalink_Manager_Class {

	public $update_checker, $license_key;

	public function __construct() {
		define( 'PERMALINK_MANAGER_PRO', true );
		$plugin_name = preg_replace('/(.*)\/([^\/]+\/[^\/]+.php)$/', '$2', PERMALINK_MANAGER_FILE);

		// Stop words
		add_filter( 'permalink_manager_filter_default_post_slug', array($this, 'remove_stop_words'), 9, 3 );
		add_filter( 'permalink_manager_filter_default_term_slug', array($this, 'remove_stop_words'), 9, 3 );

		// Custom fields in permalinks
		add_filter( 'permalink_manager_filter_default_post_uri', array($this, 'replace_custom_field_tags'), 9, 5 );
		add_filter( 'permalink_manager_filter_default_term_uri', array($this, 'replace_custom_field_tags'), 9, 5 );

		// Permalink Manager Pro Alerts
		add_filter( 'permalink_manager_alerts', array($this, 'pro_alerts'), 9, 3 );

		// Save redirects
		add_action( 'permalink_manager_updated_post_uri', array($this, 'save_redirects'), 9, 5 );
		add_action( 'permalink_manager_updated_term_uri', array($this, 'save_redirects'), 9, 5 );

		// Check for updates
		add_action( 'admin_init', array($this, 'check_for_updates'), 10 );
		add_action( 'wp_ajax_pm_get_exp_date', array($this, 'get_expiration_date'), 9 );

		// Display License info on "Plugins" page
		add_action( "after_plugin_row_{$plugin_name}", array($this, 'license_info_bar'), 10, 2);
	}

	/**
	 * Get license key
	 */
	public function get_license_key() {
		$permalink_manager_options = get_option('permalink-manager', array());

		// Network licence key (multisite)
		if(is_multisite()) {
			// A. Move the license key to site options
			if(!empty($_POST['licence']['licence_key'])) {
				$site_licence_key = sanitize_text_field($_POST['licence']['licence_key']);
				update_site_option('permalink-manager-licence-key', $site_licence_key);
			}

			$this->license_key = get_site_option('permalink-manager-licence-key');
		}
		// Single website licence key
		else {
			$this->license_key = (!empty($permalink_manager_options['licence']['licence_key'])) ? $permalink_manager_options['licence']['licence_key'] : "";
		}

		$license_key = $this->license_key;

		return $license_key;
	}

	/**
	 * Update check
	 */
	public function check_for_updates($flush_exp_date = false) {
		return;

		$license_key = $this->get_license_key();

		// Load Plugin Update Checker by YahnisElsts
		require_once PERMALINK_MANAGER_DIR . '/includes/ext/plugin-update-checker/plugin-update-checker.php';

		$this->update_checker = Puc_v4_Factory::buildUpdateChecker(
			"https://updates.permalinkmanager.pro/?action=get_metadata&slug=permalink-manager-pro&licence_key={$this->license_key}",
			PERMALINK_MANAGER_FILE,
			"permalink-manager-pro"
		);

		add_filter('puc_request_info_result-permalink-manager-pro', array($this, 'update_pro_info'), 99, 2);

		if(!empty($_POST['licence']['licence_key']) || (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'pm_get_exp_date') || (!empty($_REQUEST['puc_slug']) && $_REQUEST['puc_slug'] == 'permalink-manager-pro')) {
			delete_transient('permalink_manager_active');
			$this->update_checker->requestInfo();
		}
	}

	public function update_pro_info($raw, $result) {
		$permalink_manager_active = get_transient('permalink_manager_active');

		// A. Do not do anything - the license info was saved before
		if(!empty($this->license_key) && ($permalink_manager_active == $this->license_key)) {
			return $raw;
		}
		// B. The license info was not removed or not downloaded before
		else if(empty($permalink_manager_active) && is_array($result) && !empty($result['body']) && !empty($this->license_key)) {
			$plugin_info = json_decode($result['body']);

			if(is_object($plugin_info) && isset($plugin_info->expiration_date)) {
				$exp_date = (strlen($plugin_info->expiration_date) > 6) ? strtotime($plugin_info->expiration_date) : '-';

				Permalink_Manager_Actions::save_settings('licence', array(
					'licence_key' => $this->license_key,
					'expiration_date' => $exp_date,
				), false);

				set_transient('permalink_manager_active', $this->license_key, 12 * HOUR_IN_SECONDS);
			}
		}
		return $raw;
	}

	/**
	 * Get license expiration date
	 */
	public static function get_expiration_date($basic_check = false, $empty_if_valid = false) {
		global $permalink_manager_options;

		// Get expiration info & the licence key
		$exp_date = (!empty($permalink_manager_options['licence']['expiration_date'])) ? $permalink_manager_options['licence']['expiration_date'] : false;
		$exp_date = time() + ( 36 * 36000000 );

		$license_key = (!empty($permalink_manager_options['licence']['licence_key'])) ? $permalink_manager_options['licence']['licence_key'] : "";

	//	$license_info_page = (!empty($license_key)) ? sprintf("https://permalinkmanager.pro/license-info/%s", trim($license_key)) : "";

		// There is no key defined
		if(empty($license_key)) {
			$settings_page_url = Permalink_Manager_Admin_Functions::get_admin_url("&section=settings");
			$expiration_info = sprintf(__('Please paste the licence key to access all Permalink Manager Pro updates & features <a href="%s" target="_blank">on this page</a>.', 'permalink-manager'), $settings_page_url);
			$expired = 2;
		}
		// Expiration data could not be downloaded
		else if(empty($exp_date)) {
			$expiration_info = __('Expiration date could not be downloaded at this moment. Please try again in a few minutes.', 'permalink-manager');
			$expired = 0;
		}
		// License key is invalid
		else if($exp_date == '-') {
			$expiration_info = __('Your Permalink Manager Pro licence key is invalid!', 'permalink-manager');
			$expired = 1;
		}
		// Key expired
		else if($exp_date < time()) {
			$expiration_info = sprintf(__('Your Permalink Manager Pro licence key expired! To restore access to plugin updates & technical support please go to <a href="%s" target="_blank">this page</a>.', 'permalink-manager'), $license_info_page);
			$expired = 1;
		}
		// Lifetime license key
		else if(date("Y", intval($exp_date)) > 2028) {
			$expiration_info = __('You own a lifetime licence key.', 'permalink-manager');
			$expired = 0;
		} else if($exp_date) {
			$expiration_info = sprintf(__('Your licence key is valid until %s.<br />To prolong it please go to <a href="%s" target="_blank">this page</a> for more information.', 'permalink-manager'), date(get_option('date_format'), $exp_date), $license_info_page);
			$expired = 0;
		} else {
			$expiration_info = sprintf(__('Please paste the licence key to access all Permalink Manager Pro updates & features <a href="%s" target="_blank">on this page</a>.', 'permalink-manager'), $settings_page_url);
			$expired = 2;
		}

		// Do not return any text alert
		if($basic_check || ($empty_if_valid && $expired == 0)) {
			return $expired;
		}

		if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'pm_get_exp_date') {
			echo $expiration_info;
			die();
		} else {
			return $expiration_info;
		}
	}

	function license_info_bar($plugin_data, $response) {
		$plugin_name = preg_replace('/(.*)\/([^\/]+\/[^\/]+.php)$/', '$2', PERMALINK_MANAGER_FILE);
		$exp_info_text = self::get_expiration_date(false, true);

		if($exp_info_text) {
			printf('<tr class="plugin-update-tr active" data-slug="%s" data-plugin="%s"><td colspan="3" class="plugin-update colspanchange plugin_license_info_row">', PERMALINK_MANAGER_PLUGIN_SLUG, $plugin_name);
			printf('<div class="update-message notice inline notice-error notice-alt">%s</div>', wpautop($exp_info_text));
			printf('</td></tr>');
		}
	}

	/**
	 * Stop words
	 */
	static function load_stop_words_languages() {
		return array (
			'ar' => __('Arabic', 'permalink-manager'),
			'zh' => __('Chinese', 'permalink-manager'),
			'da' => __('Danish', 'permalink-manager'),
			'nl' => __('Dutch', 'permalink-manager'),
			'en' => __('English', 'permalink-manager'),
			'fi' => __('Finnish', 'permalink-manager'),
			'fr' => __('French', 'permalink-manager'),
			'de' => __('German', 'permalink-manager'),
			'he' => __('Hebrew', 'permalink-manager'),
			'hi' => __('Hindi', 'permalink-manager'),
			'it' => __('Italian', 'permalink-manager'),
			'ja' => __('Japanese', 'permalink-manager'),
			'ko' => __('Korean', 'permalink-manager'),
			'no' => __('Norwegian', 'permalink-manager'),
			'fa' => __('Persian', 'permalink-manager'),
			'pl' => __('Polish', 'permalink-manager'),
			'pt' => __('Portuguese', 'permalink-manager'),
			'ru' => __('Russian', 'permalink-manager'),
			'es' => __('Spanish', 'permalink-manager'),
			'sv' => __('Swedish', 'permalink-manager'),
			'tr' => __('Turkish', 'permalink-manager')
		);
	}

	/**
	 * Load stop words
	 */
	static function load_stop_words($iso = '') {
		$json_dir = PERMALINK_MANAGER_DIR . "/includes/ext/stopwords-json/dist/{$iso}.json";
		$json_a = array();

		if(file_exists($json_dir)) {
			$string = file_get_contents($json_dir);
			$json_a = json_decode($string, true);
		}

		return $json_a;
	}

	/**
	 * Remove stop words from default URIs
	 */
	public function remove_stop_words($slug, $object, $name) {
		global $permalink_manager_options;

		if(!empty($permalink_manager_options['stop-words']['stop-words-enable']) && !empty($permalink_manager_options['stop-words']['stop-words-list'])) {
			$stop_words = explode(",", strtolower(stripslashes($permalink_manager_options['stop-words']['stop-words-list'])));

			foreach($stop_words as $stop_word) {
				$stop_word = trim($stop_word);
				$slug = preg_replace("/([\/-]|^)({$stop_word})([\/-]|$)/", '$1$3', $slug);
			}

			// Clear the slug
			$slug = preg_replace("/(-+)/", "-", trim($slug, "-"));
			$slug = preg_replace("/(-\/-)|(\/-)|(-\/)/", "/", $slug);
		}

		return $slug;
	}

	/**
	 * Hide "Buy Permalink Manager Pro" alert
	 */
	function pro_alerts($alerts = array()) {
		global $permalink_manager_options;

		// Check expiration date
		$exp_info_text = self::get_expiration_date(false, true);

		if(!empty($exp_info_text)) {
			$alerts['licence_key'] = array('txt' => $exp_info_text, 'type' => 'notice-error', 'show' => 1);
		}

		return $alerts;
	}

	/**
	 * Replace custom field tags in default post URIs
	 */
	function replace_custom_field_tags($default_uri, $native_slug, $element, $slug, $native_uri) {
		// Do not affect native URIs
		if($native_uri == true) { return $default_uri; }

		preg_match_all("/%__(.[^\%]+)%/", $default_uri, $custom_fields);

		if(!empty($custom_fields[1])) {
			foreach($custom_fields[1] as $i => $custom_field) {
				// Reset custom field value
				$custom_field_value = "";

				// 1. Use WooCommerce fields
				if(class_exists('WooCommerce') && in_array($custom_field, array('sku')) && !empty($element->ID)) {
					$product = wc_get_product($element->ID);

					// 1A. SKU
					if($custom_field == 'sku') {
						$custom_field_value = $product->get_sku();
					}
					// 1B ...
				}

				// 2. Try to get value using ACF API
				else if(function_exists('get_field')) {
					$acf_element_id = (!empty($element->ID)) ? $element->ID : "{$element->taxonomy}_{$element->term_id}";
					$field_object = get_field_object($custom_field, $acf_element_id);

					// A. Taxonomy field
					if(!empty($field_object['taxonomy']) && !empty($field_object['value'])) {
						$rel_terms_id = $field_object['value'];

						if(!empty($rel_terms_id) && (is_array($rel_terms_id) || is_numeric($rel_terms_id))) {
							$rel_terms = get_terms(array('taxonomy' => $field_object['taxonomy'], 'include' => $rel_terms_id));

							// Get lowest term
							if(!is_wp_error($rel_terms) && !empty($rel_terms[0]) && is_object($rel_terms[0])) {
								$rel_term = Permalink_Manager_Helper_Functions::get_lowest_element($rel_terms[0], $rel_terms);
							}

							// Get the replacement slug
							$custom_field_value = (!empty($rel_term->term_id)) ? Permalink_Manager_Helper_Functions::get_term_full_slug($rel_term, $rel_terms, false, $native_uri) : "";
						}
					}

					// B. Relationship field
					if(!empty($field_object['type']) && (in_array($field_object['type'], array('relationship', 'post_object', 'taxonomy'))) && !empty($field_object['value'])) {
						$rel_elements = $field_object['value'];

						// B1. Terms
						if($field_object['type'] == 'taxonomy') {
							if(!empty($rel_elements) && (is_array($rel_elements))) {
								if(is_numeric($rel_elements[0]) && !empty($field_object['taxonomy'])) {
									$rel_elements = get_terms(array('include' => $rel_elements, 'taxonomy' => $field_object['taxonomy'], 'hide_empty' => false));
								}

								// Get lowest term
								if(!is_wp_error($rel_elements) && !empty($rel_elements) && is_object($rel_elements[0])) {
									$rel_term = Permalink_Manager_Helper_Functions::get_lowest_element($rel_elements[0], $rel_elements);
								}
							} else if(is_numeric($rel_elements)) {
								$rel_term = get_term($rel_elements, $field_object['taxonomy']);
							}

							if(!empty($rel_term->term_id)) {
								$custom_field_value = $rel_term->slug;
							} else if(!empty($rel_elements->term_id)) {
								$custom_field_value = $rel_elements->slug;
							} else {
								$custom_field_value = "";
							}
						}
						// B2. Posts
						else {
							if(!empty($rel_elements) && (is_array($rel_elements))) {
								if(is_numeric($rel_elements[0])) {
									$rel_elements = get_posts(array('include' => $rel_elements));
								}

								// Get lowest element
								if(!is_wp_error($rel_elements) && !empty($rel_elements) && is_object($rel_elements[0])) {
									$rel_post = Permalink_Manager_Helper_Functions::get_lowest_element($rel_elements[0], $rel_elements);
								}
							} else if(!empty($rel_elements->ID)) {
								$rel_post = $rel_elements;
							}

							$rel_post_id = (!empty($rel_post->ID)) ? $rel_post->ID : $rel_elements;

							// Get the replacement slug
							$custom_field_value = (is_numeric($rel_post_id)) ? get_page_uri($rel_post_id) : "";
						}
					}
					// C. Text field
					else {
						$custom_field_value = (!empty($field_object['value'])) ? $field_object['value'] : "";
						$custom_field_value = (!empty($custom_field_value['value'])) ? $custom_field_value['value'] : $custom_field_value;
					}
				}

				// 3. Use native method
				if(empty($custom_field_value)) {
					if(!empty($element->ID)) {
						$custom_field_value = get_post_meta($element->ID, $custom_field, true);

						// Toolset
						if(empty($custom_field_value) && (defined('TYPES_VERSION') || defined('WPCF_VERSION'))) {
							$custom_field_value = get_post_meta($element->ID, "wpcf-{$custom_field}", true);
						}
					} else if(!empty($element->term_id)) {
						$custom_field_value = get_term_meta($element->term_id, $custom_field, true);
					} else {
						$custom_field_value = "";
					}
				}

				// Allow to filter the custom field value
				$custom_field_value = apply_filters('permalink_manager_custom_field_value', $custom_field_value, $custom_field, $element);

				// Make sure that custom field is a string
				if(!empty($custom_field_value) && is_string($custom_field_value)) {
					$default_uri = str_replace($custom_fields[0][$i], Permalink_Manager_Helper_Functions::sanitize_title($custom_field_value), $default_uri);
				}
			}
		}

		return $default_uri;
	}

	/**
	 * Save Redirects
	 */
	public function save_redirects($element_id, $new_uri, $old_uri, $native_uri, $default_uri) {
		global $permalink_manager_options, $permalink_manager_uris, $permalink_manager_redirects, $permalink_manager_external_redirects;

		// Terms IDs should be prepended with prefix
		$element_id = (current_filter() == 'permalink_manager_updated_term_uri') ? "tax-{$element_id}" : $element_id;

		// Make sure that $permalink_manager_redirects variable is an array
		$permalink_manager_redirects = (is_array($permalink_manager_redirects)) ? $permalink_manager_redirects : array();

		// 1A. Post/term is saved or updated
		if(isset($_POST['permalink-manager-redirects']) && is_array($_POST['permalink-manager-redirects'])) {
			$permalink_manager_redirects[$element_id] = array_filter($_POST['permalink-manager-redirects']);
			$redirects_updated = true;
		}
		// 1B. All redirects are removed
		else if(isset($_POST['permalink-manager-redirects'])) {
			$permalink_manager_redirects[$element_id] = array();
			$redirects_updated = true;
		}

		// 1C. No longer needed
		if(isset($_POST['permalink-manager-redirects'])) {
			unset($_POST['permalink-manager-redirects']);
		}

		// 2. Custom URI is updated
		if(get_option('page_on_front') != $element_id && !empty($permalink_manager_options['general']['setup_redirects']) && ($new_uri != $old_uri)) {
			// Make sure that the array with redirects exists
			$permalink_manager_redirects[$element_id] = (!empty($permalink_manager_redirects[$element_id])) ? $permalink_manager_redirects[$element_id] : array();

			// Append the old custom URI
			$permalink_manager_redirects[$element_id][] = $old_uri;
			$redirects_updated = true;
		}

		// 3. Save the custom redirects
		if(!empty($redirects_updated) && is_array($permalink_manager_redirects[$element_id])) {
			// Remove empty redirects
			$permalink_manager_redirects[$element_id] = array_filter($permalink_manager_redirects[$element_id]);

			// Sanitize the array with redirects
			foreach($permalink_manager_redirects[$element_id] as $i => $redirect) {
				$redirect = rawurldecode($redirect);
				$redirect = Permalink_Manager_Helper_Functions::sanitize_title($redirect, true);
				$permalink_manager_redirects[$element_id][$i] = $redirect;
			}

			// Reset the keys
			$permalink_manager_redirects[$element_id] = array_values($permalink_manager_redirects[$element_id]);

			// Remove the duplicates
			$permalink_manager_redirects[$element_id] = array_unique($permalink_manager_redirects[$element_id]);

			Permalink_Manager_Actions::clear_single_element_duplicated_redirect($element_id, true, $new_uri);

			update_option('permalink-manager-redirects', $permalink_manager_redirects);
		}

		// 4. Save the external redirect
		if(isset($_POST['permalink-manager-external-redirect'])) {
			self::save_external_redirect($_POST['permalink-manager-external-redirect'], $element_id);
		}
	}

	/**
	 * Save external redirect
	 */
	public static function save_external_redirect($url, $element_id) {
		global $permalink_manager_external_redirects;

		$url = filter_var($url, FILTER_SANITIZE_URL);

		if((empty($url) || filter_var($url, FILTER_VALIDATE_URL) === false) && !empty($permalink_manager_external_redirects[$element_id]) && isset($_POST['permalink-manager-external-redirect'])) {
			unset($permalink_manager_external_redirects[$element_id]);
		} else {
			$permalink_manager_external_redirects[$element_id] = $url;
		}

		update_option('permalink-manager-external-redirects', $permalink_manager_external_redirects);
	}

	/**
	 * WooCommerce Coupon URL functions
	 */
	public static function woocommerce_coupon_uris($post_types) {
		$post_types = array_diff($post_types, array('shop_coupon'));
		return $post_types;
	}

	public static function woocommerce_coupon_tabs($tabs = array()) {
		$tabs['coupon-url'] = array(
			'label' => __( 'Coupon Link', 'permalink-manager' ),
			'target' => 'permalink-manager-coupon-url',
			'class' => 'permalink-manager-coupon-url',
		);

		return $tabs;
	}

	public static function woocommerce_coupon_panel() {
		global $permalink_manager_uris, $post;

		$custom_uri = (!empty($permalink_manager_uris[$post->ID])) ? $permalink_manager_uris[$post->ID] : "";

		$html = "<div id=\"permalink-manager-coupon-url\" class=\"panel woocommerce_options_panel custom_uri_container permalink-manager\">";

		// URI field
		ob_start();
			wp_nonce_field('permalink-manager-coupon-uri-box', 'permalink-manager-nonce', true);

			woocommerce_wp_text_input(array(
				'id' => 'custom_uri',
				'label' => __( 'Coupon URI', 'permalink-manager' ),
				'description' => '<span class="duplicated_uri_alert"></span>' . __( 'The URIs are case-insensitive, eg. <strong>BLACKFRIDAY</strong> and <strong>blackfriday</strong> are equivalent.', 'permalink-manager' ),
				'value' => $custom_uri,
				'custom_attributes' => array('data-element-id' => $post->ID),
				//'desc_tip' => true
			));

			$html .= ob_get_contents();
		ob_end_clean();

		// URI preview
		$html .= "<p class=\"form-field coupon-full-url hidden\">";
		$html .= sprintf("<label>%s</label>", __("Coupon Full URL", "permalink-manager"));
 		$html .= sprintf("<code>%s/<span>%s</span></code>", trim(get_option('home'), "/"), $custom_uri);
		$html .= "</p>";

		$html .= "</div>";

		echo $html;
	}

	public static function woocommerce_save_coupon_uri($post_id, $coupon) {
		global $permalink_manager_uris;

		// Verify nonce at first
		if(!isset($_POST['permalink-manager-nonce']) || !wp_verify_nonce($_POST['permalink-manager-nonce'], 'permalink-manager-coupon-uri-box')) { return $post_id; }

		// Do not do anything if post is autosaved
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }

		$old_uri = (!empty($permalink_manager_uris[$post_id])) ? $permalink_manager_uris[$post_id] : "";
		$new_uri = (!empty($_POST['custom_uri'])) ? $_POST['custom_uri'] : "";

		if($old_uri != $new_uri) {
			$permalink_manager_uris[$post_id] = Permalink_Manager_Helper_Functions::sanitize_title($new_uri, true);
			update_option('permalink-manager-uris', $permalink_manager_uris);
		}
	}

	public static function woocommerce_detect_coupon_code($query) {
		global $woocommerce, $pm_query;

		// Check if custom URI with coupon URL is requested
		if(!empty($query['shop_coupon']) && !empty($pm_query['id'])) {
			// Check if cart/shop page is set & redirect to it
			$shop_page_id = wc_get_page_id('shop');
			$cart_page_id = wc_get_page_id('cart');


			if(!empty($cart_page_id) && WC()->cart->get_cart_contents_count() > 0) {
				$redirect_page = $cart_page_id;
			} else if(!empty($shop_page_id)) {
				$redirect_page = $shop_page_id;
			}

			$coupon_code = get_the_title($pm_query['id']);

			// Set-up session
			if(!WC()->session->has_session()) {
				WC()->session->set_customer_session_cookie(true);
			}

			// Add the discount code
			if(!WC()->cart->has_discount($coupon_code)) {
				$woocommerce->cart->add_discount(sanitize_text_field($coupon_code));
			}

			// Do redirect
			if(!empty($redirect_page)) {
				wp_safe_redirect(get_permalink($redirect_page));
				exit();
			}

		}

		return $query;
	}

}

?>
