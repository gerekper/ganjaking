<?php
defined('WYSIJA') or die('Restricted access');
/**
 * @class Wysija Engine Helper (PHP4 version)
 */
class WYSIJA_help_wj_engine extends WYSIJA_object {
	// debug mode
	var $_debug = false;

	// contains email data
	var $_email_data = null;

	// rendering context (editor, email)
	var $_context = 'editor';

	// toggles for vib & unsub
	var $_hide_viewbrowser = false;
	var $_hide_unsubscribe = false;

	// data holders
	var $_data = null;
	var $_styles = null;

	// language direction
	var $_is_rtl = false;

	// styles: defaults
	var $VIEWBROWSER_SIZES = array(7, 8, 9, 10, 11, 12, 13, 14);
	var $TEXT_SIZES = array(8, 9, 10, 11, 12, 13, 14, 16, 18, 24, 36, 48, 72);
	var $TITLE_SIZES = array(16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 36, 40, 44, 48, 54, 60, 66, 72);
	var $FONTS = array(
		'Arial' => "Arial, 'Helvetica Neue', Helvetica, sans-serif",
		'Comic Sans MS' => "'Comic Sans MS', 'Marker Felt-Thin', Arial, sans-serif",
		'Courier New' => "'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace",
		'Georgia' => "Georgia, Times, 'Times New Roman', serif",
		'Tahoma' => "Tahoma, Verdana, Segoe, sans-serif",
		'Times New Roman' => "'Times New Roman', Times, Baskerville, Georgia, serif",
		'Trebuchet MS' => "'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif",
		'Verdana' => "Verdana, Geneva, sans-serif"
	);

	/* Constructor */
	function __construct(){
	  parent::__construct();
	}

	/* i18n methods */
	function getTranslations() {
		return array(
			'dropHeaderNotice' => __('Drop your logo in this header.',WYSIJA),
			'dropFooterNotice' => __('Drop your footer image here.',WYSIJA),
			'dropBannerNotice' => __('If you leave this area empty, it will not display once you send your email',WYSIJA),
			'clickToEditText' => __('Click here to add a title or text.', WYSIJA),
			'alignmentLeft' =>  __('Align left',WYSIJA),
			'alignmentCenter' => __('Align center',WYSIJA),
			'alignmentRight' => __('Align right',WYSIJA),
			'addImageLink' => __('Add link / Alternative text',WYSIJA),
			'removeImageLink' => __('Remove link',WYSIJA),
			'removeImage' => __('Remove image',WYSIJA),
			'remove' => __('Remove', WYSIJA),
			'editText' => __( 'Edit text',WYSIJA),
			'removeText' => __('Remove text',WYSIJA),
			'textLabel' => __('Titles & text',WYSIJA),
			'dividerLabel' => __('Horizontal line',WYSIJA),
			'customDividerLabel' => __('Custom horizontal line',WYSIJA),
			'postLabel' => __('WordPress post',WYSIJA),
			'styleBodyLabel' => __('Text',WYSIJA),
			'styleViewbrowserLabel' => __('"View in browser"', WYSIJA),
			'styleH1Label' => __('Heading 1',WYSIJA),
			'styleH2Label' => __('Heading 2',WYSIJA),
			'styleH3Label' => __('Heading 3',WYSIJA),
			'styleLinksLabel' => __('Links',WYSIJA),
			'styleLinksDecorationLabel' => __('underline',WYSIJA),
			'styleFooterLabel' => __('Footer text',WYSIJA),
			'styleFooterBackgroundLabel' => __('Footer background',WYSIJA),
			'styleBodyBackgroundLabel' => __('Newsletter',WYSIJA),
			'styleHtmlBackgroundLabel' => __('Background', WYSIJA),
			'styleHeaderBackgroundLabel' => __('Header background', WYSIJA),
			'styleDividerLabel' => __('Horizontal line',WYSIJA),
			'styleUnsubscribeColorLabel' => __('Unsubscribe',WYSIJA),
			'articleSelectionTitle' => __('Post Selection', WYSIJA),
			'bookmarkSelectionTitle' => __('Social Bookmark Selection', WYSIJA),
			'dividerSelectionTitle' => __('Divider Selection', WYSIJA),
			'abouttodeletetheme' => __('You are about to delete the theme : %1$s. Do you really want to do that?', WYSIJA),
			'addLinkTitle' => __('Add Link & Alternative text', WYSIJA),
			'styleTransparent' => __('Check this box if you want transparency', WYSIJA),
			'ajaxLoading' => __('Loading...', WYSIJA),
			'customFieldsLabel' => __('Insert dynamic data about your subscribers, the newsletter, today\'s date, etc...', WYSIJA),
			'autoPostSettingsTitle' => __('Selection options', WYSIJA),
			'autoPostEditSettings' => __('Edit Automatic latest content', WYSIJA),
			'autoPostImmediateNotice' => __('You can only add one widget when designing a post notification sent immediately after an article is published', WYSIJA),
			'toggleImagesTitle' => __('Preview without images', WYSIJA),
			// Tags labels
			'tags_user' => __('Subscriber', WYSIJA),
			'tags_user_firstname' => __('First Name', WYSIJA),
			'tags_user_lastname' => __('Last Name', WYSIJA),
			'tags_user_email' => __('Email Address', WYSIJA),
			'tags_user_displayname' => __('WordPress user display name', WYSIJA),
			'tags_user_count' => __('Total of subscribers', WYSIJA),
			'tags_newsletter' => __('Newsletter', WYSIJA),
			'tags_newsletter_subject' => __('Newsletter Subject', WYSIJA),
			'tags_newsletter_autonl' => __('Post Notifications', WYSIJA),
			'tags_newsletter_total' => __('Total number of posts or pages', WYSIJA),
			'tags_newsletter_post_title' => __('Latest post title', WYSIJA),
			'tags_newsletter_number' => __('Issue number', WYSIJA),
			'tags_date' => __('Date', WYSIJA),
			'tags_date_d' => __('Current day of the month number', WYSIJA),
			'tags_date_dordinal' => __('Current day of the month in ordinal, ie. 2nd, 3rd, etc.', WYSIJA),
			'tags_date_dtext' => __('Full name of current day', WYSIJA),
			'tags_date_m' => __('Current month number', WYSIJA),
			'tags_date_mtext' => __('Full name of current month', WYSIJA),
			'tags_date_y' => __('Year', WYSIJA),
			'tags_global' => __('Links', WYSIJA),
			'tags_global_unsubscribe' => __('Unsubscribe link', WYSIJA),
			'tags_global_manage' => __('Edit subscription page link', WYSIJA),
			'tags_global_browser' => __('View in browser link', WYSIJA),
			'custom_fields_title' => __('Custom Fields', WYSIJA),
			'custom_fields_list' => WJ_Field::get_all_names(),
			// Themes specific labels
			'theme_setting_default' => __('Saving default style...', WYSIJA),
			'theme_saved_default' => __('Default style saved.', WYSIJA),
			'theme_save_as_default' => __('Set as default style.', WYSIJA),
			// Placeholders
			'drop_block_here' => __('Insert block here', WYSIJA)
		);
	}

	/* Data methods */
	function getData($type = null) {
		if($type !== null) {
			if(array_key_exists($type, $this->_data)) {
				return $this->_data[$type];
			} else {
				// return default value
				$defaults = $this->getDefaultData();
				return $defaults[$type];
			}
		}
		return $this->_data;
	}

	function setData($value = null, $decode = false) {
		if(!$value) {
			$this->_data = $this->getDefaultData();
		} else {
			$this->_data = $value;
			if($decode) {
				$this->_data = $this->getDecoded('data');
			}
		}
	}

	function getEmailData($key = null) {
		if($key === null) {
			return $this->_email_data;
		} else {
			if(array_key_exists($key, $this->_email_data)) {
				return $this->_email_data[$key];
			}
		}
		return null;
	}

	function setEmailData($value = null) {
		if($value !== null) {
			$this->_email_data = $value;
		}
	}

	function setLanguageDirection() {
		// right to left language property
		if(function_exists('is_rtl')) {
			$this->_is_rtl = is_rtl();
		}
	}

	function isRtl() {
		return $this->_is_rtl;
	}

	function getDefaultData() {
		$dividersHelper = WYSIJA::get('dividers', 'helper');
		return array(
			'header' => array(
				'alignment' => 'center',
				'type' => 'header',
				'static' => '1',
				'text' => null,
				'image' => array(
					'src' => null,
					'width' => 600,
					'height' => 86,
					'url' => null,
					'alignment' => 'center',
					'static' => '1'
				)
			),
			'body' => array(),
			'footer' => array(
				'alignment' => 'center',
				'type' => 'footer',
				'static' => '1',
				'text' => null,
				'image' => array(
					'src' => null,
					'width' => 600,
					'height' => 86,
					'url' => null,
					'alignment' => 'center',
					'static' => '1'
				)
			),
			'widgets' => array(
				'divider' => array_merge($dividersHelper->getDefault(), array('type' => 'divider'))
			)
		);
	}

	/* Styles methods */
	function getStyles($keys = null) {
		if($keys === null) return $this->_styles;

		if(!is_array($keys)) {
			$keys = array($keys);
		}
		$output = array();
		for($i=0; $i<count($keys);$i++) {
			if(isset($this->_styles[$keys[$i]])) {
				$output = array_merge($output, $this->_styles[$keys[$i]]);
			}
		}
		return $output;
	}

	function getStyle($key, $subkey) {
		$styles = $this->getStyles($key);
		return $styles[$subkey];
	}

	function setStyles($value = null, $decode = false) {
		if(!$value) {
			$this->_styles = $this->getDefaultStyles();
		} else {
			$this->_styles = $value;
			if($decode) {
				$this->_styles = $this->getDecoded('styles');
			}
		}
	}

	function getDefaultStyles() {

		$defaults = array(
			'html' => array(
				'background' => 'FFFFFF'
			),
			'header' => array(
				'background' => 'FFFFFF'
			),
			'body' => array(
				'color' => '000000',
				'family' => 'Arial',
				'size' => $this->TEXT_SIZES[5],
				'background' => 'FFFFFF'
			),
			'footer' => array(
				'color' => '000000',
				'family' => 'Arial',
				'size' => $this->TEXT_SIZES[5],
				'background' => 'cccccc'
			),
			'h1' => array(
				'color' => '000000',
				'family' => 'Arial',
				'size' => $this->TITLE_SIZES[6]
			),
			'h2' => array(
				'color' => '424242',
				'family' => 'Arial',
				'size' => $this->TITLE_SIZES[5]
			),
			'h3' => array(
				'color' => '424242',
				'family' => 'Arial',
				'size' => $this->TITLE_SIZES[4]
			),
			'a' => array(
				'color' => '4a91b0',
				'underline' => false
			),
			'unsubscribe' => array(
				'color' => '000000'
			),
			'viewbrowser' => array(
				'color' => '000000',
				'family' => 'Arial',
				'size' => $this->VIEWBROWSER_SIZES[4]
			)
		);

		// get default selected theme
		$model_config = WYSIJA::get('config', 'model');
		$default_theme = $model_config->getValue('newsletter_default_theme', 'default');

		if($default_theme === 'default') {
			return $defaults;
		} else {
			$helper_themes = WYSIJA::get('themes', 'helper');
			$stylesheet = $helper_themes->getStylesheet($default_theme);

			$styles = array();
			// look for each tags
			foreach($defaults as $tag => $values) {
				// look for css rules
				preg_match('/\.?'.$tag.'\s?{(.+)}/Ui', $stylesheet, $matches);
				if(isset($matches[1])) {
					// extract styles
					$styles[$tag] = $this->extractStyles($matches[1]);
				} else {
					// fallback to default
					$styles[$tag] = $defaults[$tag];
				}
			}

			return $styles;
		}
	}

	function getApplicationData() {
		$app = array();

		$app['domain'] = WJ_Utils::get_domain();

		return $app;
	}

	/* Editor methods */
	function renderEditor() {
		$this->setContext('editor');

		if($this->isDataValid() === false) {
			throw new Exception('data is not valid');
		} else {
			$helper_render_engine = WYSIJA::get('render_engine', 'helper');
			$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

			// get company addressfrom settings
			$config=WYSIJA::get("config","model");

			$data = array(
				'header' => $this->renderEditorHeader(),
				'body' => $this->renderEditorBody(),
				'footer' => $this->renderEditorFooter(),
				'unsubscribe' => $config->emailFooterLinks(true),
				'company_address' => nl2br($config->getValue('company_address')),
				'is_debug' => $this->isDebug(),
				'i18n' => $this->getTranslations()
			);

			$viewbrowser = $config->view_in_browser_link(true);
			if($viewbrowser) {
				$data['viewbrowser'] = $viewbrowser;
			}

			return $helper_render_engine->render($data, 'templates/newsletter/editor/editor_template.html');
		}
	}

	function renderEditorHeader($data = null) {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
		$helper_render_engine->setStripSpecialchars(true);

		if($data !== null) {
			$block = $data;
		} else {
			$block = $this->getData('header');
		}

		$data = array_merge($block, array('i18n' => $this->getTranslations()));
		return $helper_render_engine->render($data, 'templates/newsletter/editor/header_template.html');
	}

	function renderEditorBody() {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

		$blocks = $this->getData('body');
		if(empty($blocks)) return '';

		$body = '';
		foreach($blocks as $key => $block) {
			// generate block template
			$data = array_merge($block, array('i18n' => $this->getTranslations()));
			$body .= $helper_render_engine->render($data, 'templates/newsletter/editor/block_template.html');
		}

		return $body;
	}

	function renderEditorFooter($data = null)
	{
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

		if($data !== null) {
			$block = $data;
		} else {
			$block = $this->getData('footer');
		}

		$data = array_merge($block, array('i18n' => $this->getTranslations()));

		return $helper_render_engine->render($data, 'templates/newsletter/editor/footer_template.html');
	}

	function renderEditorBlock($block = array()) {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
		$helper_render_engine->setStripSpecialchars(true);

		$block['i18n'] = $this->getTranslations();

		return $helper_render_engine->render($block, 'templates/newsletter/editor/block_'.$block['type'].'.html');
	}

	/* render auto post */
	function renderEditorAutoPost($posts = array(), $params = array()) {
		$output = '';
		if(isset($params['post_ids'])) {
			$output .= '<input type="hidden" name="post_ids" value="'.$params['post_ids'].'" />';
		}

		// check if there are posts to display
		if(empty($posts)) {
			// set background color
			$background_color = '';
			if(isset($params['bgcolor1'])) {
				$background_color = $params['bgcolor1'];
			}

			// display a message stating that the latest content has been sent
			$block = array(
				'no-block' => true,
				'type' => 'content',
				'alignment' => 'center',
				'background_color' => $background_color,
				'image' => null,
				'text' => array('value' => base64_encode(__('Latest content already sent.', WYSIJA)))
			);
			$output .= $this->renderEditorBlock($block);
		} else {
			// otherwise, render all posts into blocks
			$output .= $this->renderPostsToBlocks($posts, $params, 'autopost');
		}

		return $output;
	}

	function renderEmailAutoPost($posts = array(), $params = array()) {
		if(empty($posts)) {
			return '';
		} else {
			return $this->renderPostsToBlocks($posts, $params, 'autopost');
		}
	}

	function renderEmailBlock($block = array()) {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
		$helper_render_engine->setStripSpecialchars(true);

		// set block background color
		$background_color = null;
		if(isset($block['background_color'])) {
			$background_color = $block['background_color'];
		}

		// set rtl mode
		$block['is_rtl'] = $this->isRtl();

		$blockHTML = $helper_render_engine->render($block, 'templates/newsletter/email/block_template.html');

		// convert lists
		$blockHTML = $this->convertLists($blockHTML);

		// apply inline styles
		$blockHTML = $this->applyInlineStyles('body', $blockHTML, array('background_color' => $background_color));

		return $blockHTML;
	}

	function renderPostsToBlocks($posts = array(), $params = array(), $mode = 'post') {
		$html = '';
		$context = $this->getContext();

		$helper_articles = WYSIJA::get('articles', 'helper');

		if($params['title_tag'] === 'list') {
			$list = '<ul class="align-'.$params['title_alignment'].'">';
		}

		// make sure empty bgcolors are set to transparent
		if(!isset($params['bgcolor1']) || (isset($params['bgcolor1']) && strlen($params['bgcolor1']) === 0)) {
			$params['bgcolor1'] = '';
		}
		if(!isset($params['bgcolor2']) || (isset($params['bgcolor2']) && strlen($params['bgcolor2']) === 0)) {
			$params['bgcolor2'] = '';
		}

		// BEGIN - posts
		for($i = 0, $count = count($posts); $i < $count; $i++) {
			$post = $posts[$i];
			$is_odd = (bool)($i % 2);
			$is_last = (bool)($i === ($count - 1));

			// set default background color to transparent
			$background_color = '';

			// set background color for each post
			if($is_odd === false) $background_color = $params['bgcolor1'];
			if($is_odd === true) $background_color = $params['bgcolor2'];

			if($params['image_alignment'] === 'alternate') {
				$image_alignment = ($is_odd ===  false) ? 'left' : 'right';
			} else if($params['image_alignment'] === 'none') {
				$image_alignment = 'left';
				$post['post_image'] = null;
			} else {
				$image_alignment = $params['image_alignment'];
			}

			// build basic block data
			$block = array(
				'position' => $i,
				'type' => 'content',
				'alignment' => $image_alignment,
				'background_color' => $background_color,
				'image' => null,
				'text' => null
			);

			// in case of autopost, we need to remove the "block" container because each block will be rendered within the autopost block
			if($mode === 'autopost') {
				$block['no-block'] = true;
			}

			// get title
			$title = $helper_articles->getPostTitle($post, $params);

			if(!isset($params['title_position'])) {
				$params['title_position'] = 'inside';
			}

			// if post content is title, force title position inside
			if($params['post_content'] === 'title') {
				$params['title_position'] = 'inside';
			}

			// only display titles as a list
			if($params['title_tag'] === 'list') {
				$list .= $title;
				continue;
			}

			// if the title is outside, generate its own block
			if($params['title_position'] === 'outside') {
				// generate title
				$title_block = array_merge($block, array(
					'alignment' => 'left',
					'text' => array(
						'value' => base64_encode($title)
					)
				));

				if($context === 'editor') {
					$html .= $this->renderEditorBlock($title_block);
				} else if($context === 'email') {
					$html .= $this->renderEmailBlock($title_block);
				}
			}

			// generate content
			$content_block = array_merge($block, $helper_articles->convertPostToBlock($post, array_merge($params, array('image_alignment' => $image_alignment))));

			if($context === 'editor') {
				$html .= $this->renderEditorBlock($content_block);
			} else if($context === 'email') {
				$html .= $this->renderEmailBlock($content_block);
			}

			// display divider if required
			if(isset($params['divider']) && ($params['divider'] !== null && $is_last === false)) {
				// display divider only if there is one and if it's not the last post
				$divider_block = array_merge(
					array(
						'type' => 'divider',
						'no-block' => ($mode === 'autopost')
					),
					$params['divider']
				);

				if($context === 'editor') {
					$html .= $this->renderEditorBlock($divider_block);
				} else if($context === 'email') {
					$html .= $this->renderEmailBlock($divider_block);
				}
			}
		}
		// END - Posts

		if($params['title_tag'] === 'list') {
			$list .= '</ul>';
			$list_block = array_merge($block, array(
				'alignment' => 'center',
				'type' => 'content',
				'text' => array(
					'value' => base64_encode($list)
				)
			));
			if($context === 'editor') {
				$html .= $this->renderEditorBlock($list_block);
			} else if($context === 'email') {
				$html .= $this->renderEmailBlock($list_block);
			}
		}

		return $html;
	}

	/* render draggable images list */
	function renderImages($data = array()) {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

		return $helper_render_engine->render(array('images' => $data), 'templates/newsletter/editor/toolbar/images.html');
	}

	/* render themes list */
	function renderThemes() {
		$themes = array();
		$hThemes = WYSIJA::get('themes', 'helper');

		$installed = $hThemes->getInstalled();

		// get default selected theme
		$model_config = WYSIJA::get('config', 'model');
		$default_theme = $model_config->getValue('newsletter_default_theme', 'default');

		if(empty($installed)) {
			return '';
		} else {
			foreach($installed as $theme) {
				$theme_info = $hThemes->getInformation($theme);
				$theme_info['is_selected'] = (bool)($default_theme === $theme);
				$themes[] = $theme_info;
			}
		}

		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

		return $helper_render_engine->render(array('themes' => $themes, 'i18n' => $this->getTranslations()), 'templates/newsletter/editor/toolbar/themes.html');
	}

	function renderThemeStyles($theme = 'default') {
		$this->setContext('editor');

		$hThemes = WYSIJA::get('themes', 'helper');
		$stylesheet = $hThemes->getStylesheet($theme);

		if($stylesheet === NULL) {
			// load default settings
			$this->setStyles(null);
		} else {
			// a stylesheet has been found, let's extract styles
			$styles = array();
			$defaults = $this->getDefaultStyles();
			// look for each tags
			foreach($defaults as $tag => $values) {
				// look for css rules
				preg_match('/\.?'.$tag.'\s?{(.+)}/Ui', $stylesheet, $matches);
				if(isset($matches[1])) {
					// extract styles
					$styles[$tag] = $this->extractStyles($matches[1]);
				} else {
					// fallback to default
					$styles[$tag] = $defaults[$tag];
				}
			}
			$this->setStyles($styles);
		}

		return array(
			'css' => $this->renderStyles(),
			'form' => $this->renderStylesBar()
		);
	}

	function extractStyles($raw) {
		$rules = explode(';', $raw);
		$output = array();
		foreach($rules as $rule) {
			$sub_property = false;
			$combo = explode(':', $rule);
			if(count($combo) === 2) {
				list($property, $value) = $combo;
				// remove leading and trailing space
				$property = trim($property);
				$value = trim($value);
			} else {
				continue;
			}

			switch($property) {
				case 'background':
				case 'background-color':
					$property = 'background';
				case 'color':
					// remove # from color
					$value = str_replace('#', '', $value);
					// check if its a 3 chars color
					if(strlen($value) === 3) {
						$value = sprintf('%s%s%s%s%s%s', substr($value, 0, 1), substr($value, 0, 1), substr($value, 1, 1), substr($value, 1, 1), substr($value, 2, 1), substr($value, 2, 1));
					}
					break;
				case 'font-family':
					$property = 'family';
					$fonts = explode(',', $value);
					$value = array_shift($fonts);
					break;
				case 'font-size':
					$property = 'size';
				case 'height':
					$value = (int)$value;
					break;
				case 'text-decoration':
					$property = 'underline';
					$value = ($value === 'none') ? '-1' : '1';
					break;
				case 'border-color':
					// remove # from color
					$value = str_replace('#', '', $value);
					// check if its a 3 chars color
					if(strlen($value) === 3) {
						$value = sprintf('%s%s%s%s%s%s', substr($value, 0, 1), substr($value, 0, 1), substr($value, 1, 1), substr($value, 1, 1), substr($value, 2, 1), substr($value, 2, 1));
					}
					list($property, $sub_property) = explode('-', $property);
					break;
				case 'border-size':
					$value = (int)$value;
					list($property, $sub_property) = explode('-', $property);
					break;
				case 'border-style':
					list($property, $sub_property) = explode('-', $property);
					break;
			}

			if($sub_property !== FALSE) {
				$output[$property][$sub_property] = $value;
			} else {
				$output[$property] = $value;
			}
		}
		return $output;
	}

	function renderTheme($theme = 'default') {
		$output = array(
			'header' => null,
			'footer' => null,
			'divider' => null
		);

		$hThemes = WYSIJA::get('themes', 'helper');
		$data = $hThemes->getData($theme);

		if($data['header'] !== NULL) {
			$output['header'] = $this->renderEditorHeader($data['header']);
		}

		if($data['footer'] !== NULL) {
			$output['footer'] = $this->renderEditorFooter($data['footer']);
		}

		if($data['divider'] !== NULL) {
			$output['divider'] = $this->renderEditorBlock(array_merge(array('no-block' => true), $data['divider']));
			$output['divider_options'] = $data['divider'];
		}

		return $output;
	}

	/* render styles bar */
	function renderStylesBar() {
		$this->setContext('editor');

		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
		$helper_render_engine->setStripSpecialchars(true);

		$data = $this->getStyles();
		$data['i18n'] = $this->getTranslations();
		$data['TEXT_SIZES'] = $this->TEXT_SIZES;
		$data['VIEWBROWSER_SIZES'] = $this->VIEWBROWSER_SIZES;
		$data['TITLE_SIZES'] = $this->TITLE_SIZES;
		$data['FONTS'] = array_keys($this->FONTS);

		return $helper_render_engine->render($data, 'templates/newsletter/editor/toolbar/styles.html');
	}

	function formatStyles($styles = array()) {
		if(empty($styles)) return;

		$data = array();
		foreach($styles as $style => $value) {
			$stylesArray = explode('-', $style);
			if(count($stylesArray) === 2) {
				$data[$stylesArray[0]][$stylesArray[1]] = $value;
			} else if(count($stylesArray) === 3) {
				// handle transparent colors
				if($stylesArray[2] === 'transparent') {
					$data[$stylesArray[0]][$stylesArray[1]] = $stylesArray[2];
				} else {
					$data[$stylesArray[0]][$stylesArray[1]][$stylesArray[2]] = $value;
				}
			}
		}

		return $data;
	}

	function getContext() {
		return $this->_context;
	}

	function setContext($value = null) {
		if($value !== null) $this->_context = $value;
	}

	function isDebug() {
		return ($this->_debug === true);
	}

	function getEncoded($type = 'data') {
		return base64_encode(serialize($this->{'get'.ucfirst($type)}()));
	}

	function getDecoded($type = 'data') {
		return unserialize(base64_decode($this->{'get'.ucfirst($type)}()));
	}

	/* methods */
	function isDataValid() {
		return ($this->getData() !== null);
	}

	/* Styles methods */
	function renderStyles() {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
		$helper_render_engine->setStripSpecialchars(true);
		$helper_render_engine->setInline(true);

		$data = $this->getStyles();
		$data['context'] = $this->getContext();
		$data['is_rtl'] = $this->isRtl();

		switch($data['context']) {
			case 'editor':
				$helper_render_engine->setStripSpecialchars(false);
				$data['viewbrowser_container'] = '#wysija_viewbrowser';
				$data['wysija_container'] = '#wysija_wrapper';
				$data['header_container'] = '#wysija_header';
				$data['body_container'] = '#wysija_body';
				$data['text_container'] = '.wysija_editable';
				$data['footer_container'] = '#wysija_footer';
				$data['placeholder_container'] = '#wysija_block_placeholder';
				$data['unsubscribe_container'] = '#wysija_unsubscribe';
				return $helper_render_engine->render($data, 'styles/css-'.$data['context'].'.html');
			break;

			case 'email':
				$helper_render_engine->setStripSpecialchars(true);
				$data['wysija_container'] = '#wysija_wrapper';
				$data['viewbrowser_container'] = '.wysija_viewbrowser_container';
				$data['header'] = '.wysija_header';
				$data['header_container'] = '.wysija_header_container';
				$data['body_container'] = '.wysija_block';
				$data['text_container'] = '.wysija_text_container';
				$data['image_container'] = '.wysija_image_container';
				$data['image_placeholder'] = '.wysija_image_placeholder';
				$data['footer'] = '.wysija_footer';
				$data['footer_container'] = '.wysija_footer_container';
				$data['unsubscribe_container'] = '.wysija_unsubscribe_container';
				return $helper_render_engine->render($data, 'templates/newsletter/email/css.html');
			break;
		}
	}

	/* Email methods */
	function renderNotification($email = NULL) {
		$this->_hide_viewbrowser = true;
		$this->_hide_unsubscribe = true;
		return $this->renderEmail($email);
	}

	function renderEmail($email = NULL) {

		// fixes issue with pcre functions
		@ini_set('pcre.backtrack_limit', 1000000);

		$this->setContext('email');

		// set language direction
		$this->setLanguageDirection();

		if($this->isDataValid() === false) {
			throw new Exception('data is not valid');
		} else {
			// set email data for later use
			$this->setEmailData($email);

			// render header
			$data = array(
				'viewbrowser' => $this->renderEmailViewBrowser(),
				'header' => $this->renderEmailHeader(),
				'body' => $this->renderEmailBody(),
				'footer' => $this->renderEmailFooter(),
				'unsubscribe' => $this->renderEmailUnsubscribe(),
				'css' => $this->renderStyles(),
				'styles' => $this->getStyles(),
				'hide_viewbrowser' => $this->_hide_viewbrowser,
				'hide_unsubscribe' => $this->_hide_unsubscribe
			);

			// get language direction
			$data['is_rtl'] = $this->isRtl();

			// set email subject if specified
			$data['subject'] = $this->getEmailData('subject');

			$helper_render_engine = WYSIJA::get('render_engine', 'helper');
			$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
			$helper_render_engine->setStripSpecialchars(true);
			$helper_render_engine->setInline(true);

			try {
				$template = $helper_render_engine->render($data, 'templates/newsletter/email/email_template.html');
				$template = preg_replace('/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/', '', $template);

				return $template;
			} catch(Exception $e) {
				return '';
			}
		}
	}

	function renderEmailViewBrowser() {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
		$helper_render_engine->setStripSpecialchars(true);

		$config=WYSIJA::get('config','model');
		$data = $config->view_in_browser_link();
		if(!isset($data['link'])) {
			return '';
		} else {
			// generate block template
			$viewbrowser = $helper_render_engine->render($data, 'templates/newsletter/email/viewbrowser_template.html');

			// apply inline styles
			$viewbrowser = $this->applyInlineStyles('viewbrowser', $viewbrowser);

			return $viewbrowser;
		}
	}

	function renderEmailUnsubscribe() {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
		$helper_render_engine->setStripSpecialchars(true);

		$config = WYSIJA::get('config','model');

		$data = array(
			'unsubscribe' => $config->emailFooterLinks(),
			'company_address' => nl2br($config->getValue('company_address'))
		);

		// generate block template
		$unsubscribe = $helper_render_engine->render($data, 'templates/newsletter/email/unsubscribe_template.html');

		// apply inline styles
		$unsubscribe = $this->applyInlineStyles('unsubscribe', $unsubscribe);

		return $unsubscribe;
	}

	function renderEmailHeader() {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
		$helper_render_engine->setStripSpecialchars(true);

		$data = $this->getData('header');
		$data['styles'] = array('header' => $this->getStyles('header'));

		// check for emptyness
		if($data['text'] === NULL and $data['image']['static'] === TRUE) {
			return NULL;
		}

		// set header content width
		$data['block_width'] = 600;

		// generate block template
		$header = $helper_render_engine->render($data, 'templates/newsletter/email/header_template.html');

		// apply inline styles
		$header = $this->applyInlineStyles('header', $header);

		return $header;
	}

	function encodeParameters($params = array()) {
		// encode string parameters
		$keys_to_encode = array('author_label', 'category_label', 'readmore');
		foreach($keys_to_encode as $key) {
			if(isset($params[$key]) && strlen(trim($params[$key])) > 0) {
				$params[$key] = base64_encode(stripslashes($params[$key]));
			}
		}
		return $params;
	}

	function decodeParameters($params = array()) {
		// decode string parameters
		$keys_to_decode = array('author_label', 'category_label', 'readmore');
		foreach($keys_to_decode as $key) {
			if(isset($params[$key]) && strlen(trim($params[$key])) > 0) {
				$params[$key] = base64_decode($params[$key]);
			}
		}
		return $params;
	}

	function renderEmailBody() {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
		$helper_render_engine->setStripSpecialchars(true);

		$blocks = $this->getData('body');
		$styles = array('body' => $this->getStyles('body'));

		// default newsletter type
		$newsletter_type = 'default';

		// check if we need to interpret shortcodes
		$model_config = WYSIJA::get('config', 'model');
		$interpret_shortcode = (bool)$model_config->getValue('interp_shortcode');

		$body = '';

		// check if we are dealing with an auto newsletter
		$email = $this->getEmailData();
		if(isset($email['params']['autonl']) && !empty($email['params']['autonl'])) {
			// set newsletter type to automattic newsletter
			$newsletter_type = 'automatic';

			// posts data
			$first_subject = null;
			$post_count = 0;
			$post_ids = array();

		}

		foreach($blocks as $key => $block) {
			// reset block HTML so as to avoid duplicates
			$blockHTML = '';

                        // reset category_ids
                        $include_category_ids = array();
                        $exclude_category_ids = array();
                        $category_ids = array();

			// specific background color
			$block_background_color = null;

			// get background color if specified
			if(isset($block['background_color']) && strlen($block['background_color']) === 6) {
				$block_background_color = $block['background_color'];
			}

			// block width
			$block['block_width'] = 600;

			if($block['type'] === 'auto-post') {
				// special case for auto post, we need to fetch posts, taking previously sent posts into account

				// get email data
				//$email = $this->getEmailData();

				// get block params
				$blockParams = $block['params'];

				// format parameters
				$params = array();

				$category_ids = array();
				$category_condition = 'include';

				foreach($blockParams as $pairs) {
					// store category_ids in email for better performance on immediate sending of WP Posts.
					if($pairs['key'] === 'category_ids') {
						$pair_value = trim($pairs['value']);
						if(!empty($pair_value)) {
							$category_ids = array_map('intval', explode(',', trim($pairs['value'])));
						}
					}
					// store category condition (include / exclude) for same above reason.
					if($pairs['key'] === 'category_condition') {
						$category_condition = (in_array(trim($pairs['value']), array('include', 'exclude'))) ? trim($pairs['value']) : 'include';
					}
					$params[$pairs['key']] = $pairs['value'];
				}

				// make sure we store category_ids
				if(!empty($category_ids)) {
					if($category_condition === 'include') {
						$include_category_ids = array_unique($category_ids);
					}
					if($category_condition === 'exclude') {
						$exclude_category_ids = array_unique($category_ids);
					}
				}

				// make sure empty bgcolors are set to transparent
				if(isset($params['bgcolor1']) && strlen($params['bgcolor1']) === 0) {
					$params['bgcolor1'] = 'transparent';
				}
				if(isset($params['bgcolor2']) && strlen($params['bgcolor2']) === 0) {
					$params['bgcolor2'] = 'transparent';
				}

				// make sure the default params are set
				if(array_key_exists('autonl', $email['params']) === false) {
					$email['params']['autonl'] = array();
				}
				if(array_key_exists('articles', $email['params']['autonl']) === false) {
					$email['params']['autonl']['articles'] = array(
						'ids' => array(),
						'count' => 0,
						'first_subject' => ''
					);
				}

				// exclude already sent post ids from selection
				if(!empty($email['params']['autonl']['articles']['ids'])) {
					$params['exclude'] = $email['params']['autonl']['articles']['ids'];
				} else {
					$email['params']['autonl']['articles']['ids'] = array();
				}

				//we set the post_date to filter articles only older than that one
				if(isset($email['params']['autonl']['firstSend'])){
					$params['post_date'] = $email['params']['autonl']['firstSend'];
				}

				 // if immediate let it know to the get post
				if(isset($email['params']['autonl']['articles']['immediatepostid'])){
					$params['include'] = (int)$email['params']['autonl']['articles']['immediatepostid'];
					$params['post_limit'] = 1;
				}else{
					//we set the post_date to filter articles only older than the last time we sent articles
					if(isset($email['params']['autonl']['lastSend'])){
						$params['post_date'] = $email['params']['autonl']['lastSend'];
					}else{
						//get the latest child newsletter sent_at value
						$mEmail=WYSIJA::get('email','model');
						$mEmail->reset();
						$mEmail->orderBy('email_id','DESC');
						$lastEmailSent=$mEmail->getOne(false,array('campaign_id'=>$email['campaign_id'],'type'=>'1'));

						if(!empty($lastEmailSent)) $params['post_date'] = $lastEmailSent['sent_at'];
					}
				}

				// decode specific keys
				$params = $this->decodeParameters($params);

				// include/exclude category_ids
				$params['include_category_ids'] = $include_category_ids;
				$params['exclude_category_ids'] = $exclude_category_ids;

				// check for already inserted posts
				if(!empty($post_ids)) {
					$params['exclude'] = $post_ids;
				}

				// get posts for this block
				$model_wp_posts = WYSIJA::get('wp_posts','model');
				$posts = $model_wp_posts->get_posts($params);

				// check if we have posts to display
				if(!empty($posts)) {
					// get divider if necessary
					if(isset($params['show_divider']) && $params['show_divider'] === 'yes') {
						if(isset($email['params']['divider'])) {
							// either from the email params
							$params['divider'] = $email['params']['divider'];
						} else {
							// get default divider otherwise
							$helper_dividers = WYSIJA::get('dividers', 'helper');
							$params['divider'] = $helper_dividers->getDefault();
						}
					}

					$helper_articles = WYSIJA::get('articles', 'helper');

					foreach($posts as $key => $post) {
						// assign first post title to autonl parameters (this is used to display the [newsletter:post_title] shortcode in the subject)
						if($first_subject === null && strlen(trim($posts[$key]['post_title'])) > 0) {
							$first_subject = trim($posts[$key]['post_title']);
						}

						// check if shortcodes should be interpreted (value comes from WP options)
						if($interpret_shortcode === true) {
							// interpret shortcodes
							$posts[$key]['post_content'] = apply_filters('the_content', $post['post_content']);
						}
						if($params['image_alignment'] !== 'none') {
							// get thumbnail
							$posts[$key]['post_image'] = $helper_articles->getImage($post);
						}
						$post_ids[] = (int)$post['ID'];
						$post_count++;
					}
					// render html from post data and params
					$blockHTML = $this->renderEmailAutoPost($posts, $params);
				}

				$this->setEmailData($email);
			} else {
				// set styles
				$block['styles'] = $styles;

				// set rtl
				$block['is_rtl'] = $this->isRtl();

				// generate block template
				$blockHTML = $helper_render_engine->render($block, 'templates/newsletter/email/block_template.html');

				if($block['type'] !== 'raw') {
					// convert lists in block
					$blockHTML = $this->convertLists($blockHTML);

					// apply inline styles
					$blockHTML = $this->applyInlineStyles('body', $blockHTML, array('background_color' => $block_background_color));
				}
			}

			// append generated html to body
			if($blockHTML !== '') {
				// render each block
				$body .= $blockHTML;
			}
		}

		if($newsletter_type === 'automatic') {
			$email = $this->getEmailData();
			// set auto newsletter parameters
			$email['params']['autonl']['articles']['count'] = $post_count;
			$email['params']['autonl']['articles']['first_subject'] = $first_subject;
			// merge post ids
			if(!isset( $email['params']['autonl']['articles']['ids'])) {
				$email['params']['autonl']['articles']['ids'] = array();
			}
			$email['params']['autonl']['articles']['ids'] = array_unique(array_merge(
				(array)$email['params']['autonl']['articles']['ids'],
				(array)$post_ids
			));

			$this->setEmailData($email);
		}

		return $body;
	}

	function renderEmailFooter() {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
		$helper_render_engine->setStripSpecialchars(true);

		$data = $this->getData('footer');
		$data['styles'] = array('footer' => $this->getStyles('footer'));

		// check for emptyness
		if($data['text'] === NULL and $data['image']['static'] === TRUE) {
			return NULL;
		}

		// set footer content width
		$data['block_width'] = 600;

		// generate block template
		$footer = $helper_render_engine->render($data, 'templates/newsletter/email/footer_template.html');

		// apply inline styles
		$footer = $this->applyInlineStyles('footer', $footer);

		return $footer;
	}

	/**
	 * area : header, body, footer, unsubscribe, viewbrowser
	 * block : raw html
	 */
	function applyInlineStyles($area, $block, $extra = array()) {
		$helper_render_engine = WYSIJA::get('render_engine', 'helper');
		$helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);
		$helper_render_engine->setInline(true);

		// common tags
		$tags = array(
			'table' => array(
				'border-collapse' => 'collapse',
				'mso-table-space' => '0pt',
				'clear' => 'both'
			),
			'td' => array(
				'border-collapse' => 'collapse'
			)
		);
		// common classes
		$classes = array();

		// set block background color
		if(array_key_exists('background_color', $extra) and $extra['background_color'] !== null) {
			$background_color = $extra['background_color'];
		} else {
			switch ($area) {
				case 'header':
				case 'footer':
				case 'body':
					$background_color = $this->getStyle($area, 'background');
					break;
				case 'unsubscribe':
					$background_color = $this->getStyle('html', 'background');
				break;
				default:
					$background_color = $this->getStyle('html', 'background');
				break;
			}
		}

		// set common styles
		$styles = array(
			'titles' => array(
				'padding' => '0',
				'margin' => '0',
				'font-style' => 'normal',
				'font-weight' => 'normal',
				'line-height' => '125%',
				'letter-spacing' => 'normal'
			),
			'images' => array(
				'margin' => '0',
				'padding' => '0',
				'display' => 'block',
				'border' => array(
					'size' => '1px',
					'style' => 'solid',
					'color' => $this->formatColor($background_color)
				)
			)
		);

		// area specific classes/tags
		switch($area) {
			case 'header':
			case 'footer':
				$classes = array(
					'wysija_'.$area => array('min-width' => '100%'),
					'wysija_'.$area.'_container' => $this->getStyles($area),
					'wysija_content_container' => array_merge($this->getStyles($area), array('padding' => '0')),
					// images alignments
					'wysija_image_container center' => array_merge($styles['images'], array('margin' => '0 auto 0 auto', 'text-align' => 'center', 'border' => '0')),
					'wysija_image_container left' => array_merge($styles['images'], array('border' => '0')),
					'wysija_image_container right' => array_merge($styles['images'], array('border' => '0')),
					// image placeholder
					'wysija_image_placeholder' => array('padding' => '0 0 0 0', 'margin' => '0 0 0 0', 'display' => 'block'),

					// links around images
					'wysija_image_link' => array('outline' => '0', 'border' => '0', 'margin' => '0', 'padding' => '0', 'display' => 'block'),

					// images
					'image_fix' => array('display' => 'block', 'outline' => '0', 'text-decoration' => 'none', 'interpolation' => 'bicubic')
				);
			break;

			case 'body':
				$tags = array_merge($tags, array(
					'h1' => array_merge($styles['titles'], $this->getStyles('h1')),
					'h2' => array_merge($styles['titles'], $this->getStyles('h2')),
					'h3' => array_merge($styles['titles'], $this->getStyles('h3')),
					'p' => array_merge($this->getStyles('body'), array('word-wrap' => true, 'padding' => '0', 'margin' => '1em 0', 'line-height' => '1.5em', 'vertical-align' => 'top', 'letter-spacing' => 'normal')),
					'li' => array_merge($this->getStyles('body'), array('word-wrap' => true, 'padding' => '0', 'margin' => '0', 'line-height' => '1.5em', 'vertical-align' => 'top', 'letter-spacing' => 'normal')),
					'a' => array_merge($this->getStyles('a'), array('word-wrap' => true)),
					'br' => array('margin' => '0', 'padding' => '0', 'line-height' => '150%')
				));

				$classes = array(
					// blocks
					'wysija_block' => array('min-width' => '100%'),
					'wysija_content_container' => array('padding' => '10px 17px 10px 17px'),
					'wysija_divider_container' => array('padding' => '15px 17px 15px 17px'),
					'wysija_gallery_container' => array('padding' => '10px 17px 10px 17px'),

					// text
					// 'wysija_text_container' => array('margin' => '1em 0'),
					'align-left' => array('text-align' => 'left'),
					'align-center' => array('text-align' => 'center'),
					'align-right' => array('text-align' => 'right'),
					'align-justify' => array('text-align' => 'justify'),

					// links around images
					'wysija_image_link' => array('outline' => '0', 'border' => '0', 'margin' => '0', 'padding' => '0', 'display' => 'block'),

					// images
					'image_fix' => array('display' => 'block', 'outline' => '0', 'text-decoration' => 'none', 'interpolation' => 'bicubic'),

					// images alignments
					'wysija_image_table center' => array('margin' => '0 auto 0 auto', 'text-align' => 'center'),

					'wysija_image_container center' => array_merge($styles['images'], array('padding' => '0 0 0 0', 'margin' => '0 auto 0 auto', 'text-align' => 'left')),
					'wysija_image_container left' => array_merge($styles['images'], array('padding' => '0 10px 0 0')),
					'wysija_image_container right' => array_merge($styles['images'], array('padding' => '0 0 0 10px')),

					'wysija_image_placeholder left' => array('padding' => '0 0 0 0', 'margin' => '0 10px 0 0', 'display' => 'block'),
					'wysija_image_placeholder right' => array('padding' => '0 0 0 0', 'margin' => '0 0 0 10px', 'display' => 'block'),

					// gallery
					'wysija_gallery_table center' => array('margin' => '0 auto 0 auto', 'text-align' => 'center'),
					'wysija_cell_container' => array('border' => $styles['images']['border']),

					// lists
					'wysija_list_item' => array('margin' => '0')
				);
			break;

			case 'viewbrowser':
				$tags = array_merge($tags, array(
					'a' => $this->getStyles('viewbrowser')
				));

				$classes = array(
					'wysija_viewbrowser_container' => array_merge(
						$this->getStyles('html'),
						$this->getStyles('viewbrowser'),
						array(
							'text-align' => 'center',
							'padding' => '8px'
						)
					)
				);
			break;

			case 'unsubscribe':
				$tags = array_merge($tags, array(
					'a' => $this->getStyles('unsubscribe')
				));

				$classes = array(
					'wysija_unsubscribe_container' => array_merge(
						$this->getStyles('html'),
						$this->getStyles('unsubscribe'),
						array(
							'family' => 'Verdana',
							'size' => '12',
							'text-align' => 'center',
							'padding' => '8px'
						)
					)
				);
			break;
		}

		$tags_to_check = array('p', 'a', 'ul', 'li', 'h1', 'h2', 'h3');
		$classes_to_check = array('wysija_content_container', 'wysija_image_container', 'wysija_divider_container', 'wysija_gallery_container', 'wysija_cell_container', 'wysija_list_item');

		// check tags and set custom background color
		foreach($tags_to_check as $tag) {
			if(isset($tags[$tag])) {
				$tags[$tag]['background'] = $background_color;
			}
		}

		// check classes and set custom background color
		foreach($classes_to_check as $class) {
			if(isset($classes[$class])) {
				$classes[$class]['background'] = $background_color;
			}
		}

		if(empty($tags) === FALSE) {

			foreach($tags as $tag => $styles) {
				$styles = $this->splitSpacing($styles);
				$inlineStyles = $helper_render_engine->renderCSS($styles);
				$tags['#< *'.$tag.'((?:(?!style).)*)>#Ui'] = '<'.$tag.' style="'.$inlineStyles.'"$1>';
				unset($tags[$tag]);
			}

			$block = preg_replace(array_keys($tags), $tags, $block);
		}

		if(empty($classes) === FALSE) {
			foreach($classes as $class => $styles) {
				// split spacing styles
				$styles = $this->splitSpacing($styles);
				$inlineStyles = $helper_render_engine->renderCSS($styles);

				// build regexp for this class
				$classes['#<([^ /]+) ((?:(?!>|style).)*)(?:style="([^"]*)")?((?:(?!>|style).)*)class="([^"]*)'.$class.'([^"]*)"((?:(?!>|style).)*)(?:style="([^"]*)")?((?:(?!>|style).)*)>#Ui'] = '<$1 $2$4$7 class="$5'.$class.'$6" style="$3$8'.$inlineStyles.'" $9>';

				unset($classes[$class]);
			}

			// apply styles
			$styledBlock = preg_replace(array_keys($classes), $classes, $block);
			// cleanup output (fixes rendering issues)
			$styledBlock = preg_replace('/(\t+)/', ' ', $styledBlock);
			$styledBlock = preg_replace('/( +)/', ' ', $styledBlock);

			// Check if the preg_replace worked. Otherwise we simply return the original block
			if(strlen(trim($styledBlock)) > 0) {
				$block = $styledBlock;
			}
		}

		return $block;
	}

	function splitSpacing($styles) {
		foreach($styles as $property => $value) {
			if($property === 'margin' or $property === 'padding') {
				// extract multi-values
				$values = explode(' ', $value);

				// split values depending on values count
				switch(count($values)) {
					case 1:
						$styles[$property.'-top'] = $values[0];
						$styles[$property.'-right'] = $values[0];
						$styles[$property.'-bottom'] = $values[0];
						$styles[$property.'-left'] = $values[0];
					break;
					case 2:
						$styles[$property.'-top'] = $values[0];
						$styles[$property.'-right'] = $values[1];
						$styles[$property.'-bottom'] = $values[0];
						$styles[$property.'-left'] = $values[1];
					break;
					case 4:
						$styles[$property.'-top'] = $values[0];
						$styles[$property.'-right'] = $values[1];
						$styles[$property.'-bottom'] = $values[2];
						$styles[$property.'-left'] = $values[3];
					break;
				}

				// unset original value
				unset($styles[$property]);
			}
		}
		return $styles;
	}

	function formatColor($color) {
		if(strlen(trim($color)) === 0 or $color === 'transparent') {
			return 'transparent';
		} else {
			return '#'.$color;
		}
	}

	// converts lists (ul, ol, li) into paragraphs for email compatibility
	function convertList($matches) {
		$output = $matches[5];
		// get alignment from ul tag and make sure it's a valid value
		$alignment = (isset($matches[3]) && in_array($matches[3], array('align-left', 'align-center', 'align-right'))) ? trim($matches[3]) : 'align-left';
		// convert opening li tag to paragraph
		$output = preg_replace('#<li ?((?:(?!>|class).)*)(?:class="([^"]*)")?((?:(?!>|class).)*)>#Uis', "\n".'<p class="wysija_list_item '.$alignment.'">&bull;&nbsp;', $output);
		// replace all closing li tags by p
		$output = str_replace('</li>', "</p>\n", $output);
		return $output;
	}

	function convertLists($html) {
		$model_config = WYSIJA::get('config', 'model');
		if((bool)$model_config->getValue('disable_list_conversion') === true) {
			return $html;
		}
		// define maximum recursion level
		$max_recursion = 100;
		$recursion_count = 0;
		// as long as there are ul tags in the content and we haven't reached the maximum recursion value
		while(strpos($html, '<ul') !== false && $recursion_count < $max_recursion) {
			$html = preg_replace_callback(
				'#(<ul ?((?:(?!>|class).)*)(?:class="([^"]*)")?((?:(?!>|class).)*)>((?:(?!<ul).)*)<\/ul>)#Uis',
				array($this, 'convertList'),
				$html
			);
			$recursion_count++;
		}

		return $html;
	}
}
