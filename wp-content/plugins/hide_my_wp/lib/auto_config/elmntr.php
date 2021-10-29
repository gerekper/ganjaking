<?php

global $wp, $parentObj, $elmntr_path;
$parentObj = $this;

if (!isset($_GET['elementor-preview'])) {
	$parentObj->top_replace_old[] = "elementor-";
	$parentObj->top_replace_new[] = "etr-";
	$parentObj->top_replace_old[] = "elementor";
	$parentObj->top_replace_new[] = "etr";

	$cdnPath = '';
	if (trim($parentObj->opt('cdn_path'), '/ ')) {
		$cdnPath = trim($parentObj->opt('cdn_path'), '/ ') . '/';
	}
	$elmntr_path = $cdnPath . trim($parentObj->opt('new_plugin_path'), ' /') . '/' . 'etr/assets';
	
	$wp->add_query_var('elmntr_wrapper_css');
	$wp->add_query_var('elmntr_wrapper_js');
}

add_action('before_global_assets_filter', 'elm_before_global_assets_filter', 10, 2);

function elm_before_global_assets_filter($parentObj, $login_query) {
	global $wp_query;
	if (!is_admin()) {
		$request_path = $filepath = '';
		$is_js = $is_css = false;
		if ($parentObj->opt('full_hide') && $parentObj->opt('admin_key')) {
			if (!isset($_GET[$parentObj->get_short_prefix() . $login_query]) || $_GET[$parentObj->get_short_prefix() . $login_query] != $parentObj->opt('admin_key')) {
				return false;
			}
		}
		if (isset($wp_query->query_vars['elmntr_wrapper_js']) && $wp_query->query_vars['elmntr_wrapper_js'] && $parentObj->is_permalink()) {
			$is_js = true;
			$request_path = str_replace('etr-', 'elementor-', $wp_query->query_vars['elmntr_wrapper_js']);
		}
		if (isset($wp_query->query_vars['elmntr_wrapper_css']) && $wp_query->query_vars['elmntr_wrapper_css'] && $parentObj->is_permalink()) {
			$is_css = true;
			$request_path = str_replace('etr-', 'elementor-', $wp_query->query_vars['elmntr_wrapper_css']);
		}
		if (!empty($request_path) && file_exists(ABSPATH . '/' . $request_path)) {
			$filepath = ABSPATH . '/' . $request_path;
			status_header(200);
			header("Pragma: public");
			$expires = 60 * 60 * 24 * 10;
			if ($is_js) {
				header('Content-type: application/javascript');
			} elseif ($is_css) {
				$days_to_expire = $parentObj->opt('style_expiry_days');
				if (!is_numeric($days_to_expire) || $days_to_expire <= 0) {
					$days_to_expire = 3;
				}
				$expires = 60 * 60 * 24 * $days_to_expire;
				header('Content-type: text/css');
			}
			header("Cache-Control: maxage=" . $expires);
			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

			$code = file_get_contents($filepath);
			$code = str_replace('elementor-', 'etr-', $code);

			echo $code;
			exit;
		}
	}
}

add_action('mod_rewrite_rules', 'elementor_add_rewrite_rules');

function elementor_add_rewrite_rules($rules) {
	global $wp_rewrite, $wp, $parentObj, $elmntr_path;
	if (!isset($_GET['elementor-preview'])) {
		$path = $elmntr_path;
		$cdnPath = '';
		if (trim($parentObj->opt('cdn_path'), '/ ')) {
			$cdnPath = trim($parentObj->opt('cdn_path'), '/ ') . '/';
		}
		if (trim($parentObj->opt('new_upload_path'), ' /')) {
			$new_upload_path = $cdnPath . trim($parentObj->opt('new_upload_path'), ' /') . '/etr/css/';
		} else {
			$new_upload_path = $cdnPath . 'wp-content/uploads/etr/css';
		}

		$trust_key = str_replace('?', '&', $parentObj->get_trust_key());
		$elmntr_rules = PHP_EOL . "#BEGIN - HMWP - Elementor Rules" . PHP_EOL
			. "<IfModule mod_rewrite.c>" . PHP_EOL
			. "RewriteEngine On" . PHP_EOL
			. "RewriteCond %{THE_REQUEST} ^GET\ /{$path}/" . PHP_EOL
			. "RewriteRule ^{$path}/(.*).js index.php?elmntr_wrapper_js=wp-content/plugins/elementor/assets/$1.js" . $trust_key . "" . PHP_EOL
			. "RewriteRule ^{$path}/(.*).css index.php?elmntr_wrapper_css=wp-content/plugins/elementor/assets/$1.css" . $trust_key . "" . PHP_EOL
			. "RewriteRule ^{$new_upload_path}/global\.css /index\.php?elmntr_wrapper_css=wp-content/uploads/elementor/css/global\.css" . $trust_key . PHP_EOL
			. "RewriteRule ^{$new_upload_path}/post-([0-9-_\\.]+)\.css /index\.php?elmntr_wrapper_css=wp-content/uploads/elementor/css/post-$1\.css" . $trust_key . PHP_EOL
			. "</IfModule>" . PHP_EOL
			. "#END - HMWP - Elementor Rules" . PHP_EOL . PHP_EOL;
		$rules = $elmntr_rules . $rules;
	}
	return $rules;
}
