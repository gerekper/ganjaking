<?php
/**
 * Muffin Builder | Ajax actions
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_Builder_Ajax {

	/**
	 * Constructor
	 */

	public function __construct() {

		// handle custom AJAX endpoint

		add_action( 'wp_ajax_mfn_builder_seo', array( $this, 'builder_seo' ) );
		add_action( 'wp_ajax_mfn_builder_export', array( $this, 'builder_export' ) );
		add_action( 'wp_ajax_mfn_builder_import', array( $this, 'builder_import' ) );
		add_action( 'wp_ajax_mfn_builder_template', array( $this, 'builder_template' ) );

	}

	/**
	 * Copy builder content to WP Editor where it is useful for SEO plugins like Yoast
	 */

	public function builder_seo() {

		// function verifies the AJAX request, to prevent any processing of requests which are passed in by third-party sites or systems

		check_ajax_referer( 'mfn-builder-nonce', 'mfn-builder-nonce' );

		if (key_exists('mfn-item-type', $_POST) && is_array($_POST['mfn-item-type'])) {

			$count = array();
			$tabs_count = array();

			$seo_content = '';

			foreach ($_POST['mfn-item-type'] as $type_k => $type) {

				$item = array();
				$item['type'] = $type;
				$item['uid'] = $_POST['mfn-item-id'][$type_k];
				$item['size'] = $_POST['mfn-item-size'][$type_k];

				// init count for specified item type

				if (! key_exists($type, $count)) {
					$count[$type] = 0;
				}

				// init count for specified tab type

				if (! key_exists($type, $tabs_count)) {
					$tabs_count[$type] = 0;
				}

				if (key_exists($type, $_POST['mfn-items'])) {
					foreach ((array) $_POST['mfn-items'][$type] as $attr_k => $attr) {

						if ($attr_k == 'tabs') {

							// accordion, FAQ & tabs

							$item['fields']['count'] = $attr['count'][$count[$type]];

							if ($item['fields']['count']) {
								for ($i = 0; $i < $item['fields']['count']; $i++) {

									$seo_val = trim($attr['title'][$tabs_count[$type]]);
									if ($seo_val && $seo_val != 1) {
										$seo_content .= $attr['title'][$tabs_count[$type]] .": ";
									}

									$seo_val = trim($attr['content'][$tabs_count[$type]]);
									if ($seo_val && $seo_val != 1) {
										$seo_content .= $attr['content'][$tabs_count[$type]] ."\n\n";
									}

									$tabs_count[$type]++;
								}
							}

						} else {

							$seo_val = trim($attr[$count[$type]]);

							if ($seo_val && $seo_val != 1) {
								if (in_array($attr_k, array( 'image', 'src' ))) {

									// image
									$seo_content .= '<img src="'. $seo_val .'" alt="'. mfn_get_attachment_data($seo_val, 'alt') .'"/>'."\n";

								} elseif ($attr_k == 'link') {

									// link
									$seo_content .= '<a href="'. $seo_val .'">'. $seo_val .'</a>'."\n";

								} else {

									$seo_content .= $seo_val ."\n";

								}
							}

						}
					}

					$seo_content .= "\n";
				}

				$count[$type] ++;
			}
		}

		$allowed_html = array(
			'a' => array(
				'href' => array(),
				'target' => array(),
				'title' => array(),
			),
			'h1' => array(),
			'h2' => array(),
			'h3' => array(),
			'h4' => array(),
			'h5' => array(),
			'h6' => array(),
			'img' => array(
				'src' => array(),
				'alt' => array(),
			),
		);

		echo wp_kses( $seo_content, $allowed_html );

		exit;

	}

	/**
	 * Export builder content as serialized string
	 * Accepts Muffin Builder items and converts it to serialized string
	 */

	public function builder_export(){

		// function verifies the AJAX request, to prevent any processing of requests which are passed in by third-party sites or systems

		check_ajax_referer( 'mfn-builder-nonce', 'mfn-builder-nonce' );

		// variables

		$mfn_items = array();
		$mfn_wraps = array();

		// LOOP sections

		if (key_exists('mfn-row-id', $_POST) && is_array($_POST['mfn-row-id'])) {

			foreach ($_POST['mfn-row-id'] as $sectionID_k => $sectionID) {

				$section = array();

				$section['uid'] = $_POST['mfn-row-id'][$sectionID_k];

				// $section['attr'] - section attributes

				if (key_exists('mfn-rows', $_POST) && is_array($_POST['mfn-rows'])) {
					foreach ($_POST['mfn-rows'] as $section_attr_k => $section_attr) {
						$section['attr'][$section_attr_k] = stripslashes($section_attr[$sectionID_k]);
					}
				}

				$section['wraps'] = ''; // $section['wraps'] - section wraps will be added in next loop

				$mfn_items[] = $section;
			}

			$row_IDs = $_POST['mfn-row-id'];
			$row_IDs_key = array_flip($row_IDs);
		}

		// LOOP wraps

		if (key_exists('mfn-wrap-id', $_POST) && is_array($_POST['mfn-wrap-id'])) {

			foreach ($_POST['mfn-wrap-id'] as $wrapID_k => $wrapID) {

				$wrap = array();

				$wrap['uid'] = $_POST['mfn-wrap-id'][$wrapID_k];
				$wrap['size'] = $_POST['mfn-wrap-size'][$wrapID_k];
				$wrap['items'] = ''; // $wrap['items'] - items will be added in the next loop

				// $wrap['attr'] - wrap attributes

				if (key_exists('mfn-wraps', $_POST) && is_array($_POST['mfn-wraps'])) {
					foreach ($_POST['mfn-wraps'] as $wrap_attr_k => $wrap_attr) {
						$wrap['attr'][$wrap_attr_k] = $wrap_attr[$wrapID_k];
					}
				}

				$mfn_wraps[$wrapID] = $wrap;
			}

			$wrap_IDs = $_POST['mfn-wrap-id'];
			$wrap_IDs_key = array_flip($wrap_IDs);
			$wrap_parents = $_POST['mfn-wrap-parent'];
		}

		// LOOP items

		if (key_exists('mfn-item-type', $_POST) && is_array($_POST['mfn-item-type'])) {

			$count = array();
			$tabs_count = array();

			foreach ($_POST['mfn-item-type'] as $type_k => $type) {

				$item = array();
				$item['type'] = $type;
				$item['uid'] = $_POST['mfn-item-id'][$type_k];
				$item['size'] = $_POST['mfn-item-size'][$type_k];

				// init count for specified item type

				if (! key_exists($type, $count)) {
					$count[$type] = 0;
				}

				// init count for specified tab type

				if (! key_exists($type, $tabs_count)) {
					$tabs_count[$type] = 0;
				}

				if (key_exists($type, $_POST['mfn-items'])) {
					foreach ((array) $_POST['mfn-items'][$type] as $attr_k => $attr) {

						if ($attr_k == 'tabs') {

							// accordion, FAQ & tabs

							$item['fields']['count'] = $attr['count'][$count[$type]];

							if ($item['fields']['count']) {
								for ($i = 0; $i < $item['fields']['count']; $i++) {

									$tab = array();
									$tab['title'] = stripslashes($attr['title'][$tabs_count[$type]]);
									$tab['content'] = stripslashes($attr['content'][$tabs_count[$type]]);

									$item['fields']['tabs'][] = $tab;

									$tabs_count[$type]++;
								}
							}

						} else {

							// all other items

							$item['fields'][$attr_k] = stripslashes($attr[$count[$type]]);

						}
					}

				}

				// increase count for specified item type

				$count[$type] ++;

				// parent wrap

				$parent_wrap_ID = $_POST['mfn-item-parent'][$type_k];

				if (! isset($mfn_wraps[ $parent_wrap_ID ]['items']) || ! is_array($mfn_wraps[ $parent_wrap_ID ]['items'])) {
					$mfn_wraps[ $parent_wrap_ID ]['items'] = array();
				}
				$mfn_wraps[ $parent_wrap_ID ]['items'][] = $item;
			}
		}

		// assign wraps with items to sections

		foreach ($mfn_wraps as $wrap_ID => $wrap) {

			$wrap_key = $wrap_IDs_key[ $wrap_ID ];
			$section_ID = $wrap_parents[ $wrap_key ];
			$section_key = $row_IDs_key[ $section_ID ];

			if (! isset($mfn_items[ $section_key ]['wraps']) || ! is_array($mfn_items[ $section_key ]['wraps'])) {
				$mfn_items[ $section_key ]['wraps'] = array();
			}
			$mfn_items[ $section_key ]['wraps'][] = $wrap;

		}

		// prepare data to save

		if ($mfn_items) {
			$mfn_items = call_user_func('base'.'64_encode', serialize($mfn_items));
			print_r($mfn_items);
		}

		exit;

	}

	/**
	 * Import builder content.
	 * Accepts serialized string and converts it to Muffin Builder items
	 */

	public function builder_import() {

		// function verifies the AJAX request, to prevent any processing of requests which are passed in by third-party sites or systems

		check_ajax_referer( 'mfn-builder-nonce', 'mfn-builder-nonce' );

		$import = htmlspecialchars(stripslashes($_POST['mfn-items-import']));

		if (! $import) {
			exit;
		}

		// unserialize received items data

		$mfn_items = unserialize(call_user_func('base'.'64_decode', $import));

		// get current builder uniqueIDs

		$uids_row = isset($_POST['mfn-row-id']) ? $_POST['mfn-row-id'] : array();
		$uids_wrap = isset($_POST['mfn-wrap-id']) ? $_POST['mfn-wrap-id'] : array();
		$uids_item = isset($_POST['mfn-item-id']) ? $_POST['mfn-item-id'] : array();

		$uids = array_merge($uids_row, $uids_wrap, $uids_item);

		// reset uniqueID

		$mfn_items = Mfn_Builder_Helper::unique_ID_reset($mfn_items, $uids);

		if (is_array($mfn_items)) {

			$builder = new Mfn_Builder_Admin();
			$builder->set_fields();

			foreach ($mfn_items as $section) {
				$uids = $builder->section($section, $uids);
			}

		}

		exit;

	}

	/**
	 * Import template
	 * Get builder content from target page and converts it to Muffin Builder items
	 */

	public function builder_template() {

		// function verifies the AJAX request, to prevent any processing of requests which are passed in by third-party sites or systems

		check_ajax_referer( 'mfn-builder-nonce', 'mfn-builder-nonce' );

		$id = intval($_POST['mfn-items-import-template'], 10);

		if (! $id) {
			exit;
		}

		// unserialize received items data

		$mfn_items = get_post_meta($id, 'mfn-page-items', true);

		if (! $mfn_items){
			exit;
		}

		if (! is_array($mfn_items)) {
			$mfn_items = unserialize(call_user_func('base'.'64_decode', $mfn_items));
		}

		// get current builder uniqueIDs

		$uids_row = isset($_POST['mfn-row-id']) ? $_POST['mfn-row-id'] : array();
		$uids_wrap = isset($_POST['mfn-wrap-id']) ? $_POST['mfn-wrap-id'] : array();
		$uids_item = isset($_POST['mfn-item-id']) ? $_POST['mfn-item-id'] : array();

		$uids = array_merge($uids_row, $uids_wrap, $uids_item);

		// reset uniqueID

		$mfn_items = Mfn_Builder_Helper::unique_ID_reset($mfn_items, $uids);

		if (is_array($mfn_items)) {

			$builder = new Mfn_Builder_Admin();
			$builder->set_fields();

			foreach ($mfn_items as $section) {
				$uids = $builder->section($section, $uids);
			}

		}

		exit;

	}

}

$mfn_builder_ajax = new Mfn_Builder_Ajax();
