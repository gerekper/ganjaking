<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_articles extends WYSIJA_object {

	function __construct(){
	  parent::__construct();
	}

	function stripShortcodes($content) {
		if(strlen(trim($content)) === 0) {
			return '';
		}
		// remove captions
		$content = preg_replace("/\[caption.*?\](.*<\/a>)(.*?)\[\/caption\]/", '$1', $content);

		// remove other shortcodes
		$content = preg_replace('/\[[^\[\]]*\]/', '', $content);

		return $content;
	}

	function convertPostToBlock($post, $params = array()) {

		// defaults
		$defaults = array(
			'title_tag' => 'h1',
			'title_alignment' => 'left',
			'title_position' => 'inside',
			'image_alignment' => 'left',
			'image_width' => 325,
			'readmore' => __('Read online.', WYSIJA),
			'post_content' => 'full',
			'author_show' => 'no',
			'author_label' => '',
			'category_show' => 'no',
			'category_label' => ''
		);

		// merge params with default params
		$params = array_merge($defaults, $params);

		if($params['post_content'] === 'full') {
			$content = $post['post_content'];
		} else if($params['post_content'] === 'title') {
			$content = $this->getPostTitle($post, $params);
		} else {
			// get excerpt
			if(!empty($post['post_excerpt'])) {
				$content = $post['post_excerpt'];
			} else {
				// strip shortcodes before getting the excerpt
				$post['post_content'] = $this->stripShortcodes($post['post_content']);

				// if excerpt is empty then try to find the "more" tag
				$excerpts = explode('<!--more-->', $post['post_content']);
				if(count($excerpts) > 1){
					$content = $excerpts[0];
				}else{
					// finally get a made up excerpt if there is no other choice
					$helper_toolbox = WYSIJA::get('toolbox', 'helper');
					$content = $helper_toolbox->excerpt($post['post_content'], apply_filters('mpoet_excerpt_length', 60));
				}
			}
			// strip title tags from excerpt
			$content = preg_replace('/<([\/])?h[123456](.*?)>/', '<$1p$2>', $content);
		}

		// convert new lines into <p>
		$content = wpautop($content);

		// remove images
		$content = preg_replace('/<img[^>]+./','', $content);

		// strip shortcodes
		$content = $this->stripShortcodes($content);

		// remove wysija nl shortcode
		$content= preg_replace('/\<div class="wysija-register">(.*?)\<\/div>/','',$content);

		// convert embedded content if necessary
		$content = $this->convertEmbeddedContent($content);

		// convert h4 h5 h6 to h3
		$content = preg_replace('/<([\/])?h[456](.*?)>/', '<$1h3$2>', $content);

		// convert ol to ul
		$content = preg_replace('/<([\/])?ol(.*?)>/', '<$1ul$2>', $content);

		// convert currency signs
		$content = str_replace(array('$', '€', '£', '¥'), array('&#36;', '&euro;', '&pound;', '&#165;'), $content);

		// strip useless tags
		// TODO should we add table, tr, td and th into that list it could create issues in some cases
		$tags_not_being_stripped = array('<p>','<em>','<span>','<b>','<strong>','<i>','<h1>','<h2>','<h3>','<a>','<ul>','<ol>','<li>','<br>');

		// filter to modify that list
		$tags_not_being_stripped = apply_filters('mpoet_strip_tags_ignored',$tags_not_being_stripped);

		$content = strip_tags($content, implode('',$tags_not_being_stripped));

		// post meta (author, categories)
		$post_meta_above = '';
		// if the author or categories are displayed, open a new paragraph
		if($params['author_show'] === 'above' || $params['category_show'] === 'above') {
			$post_meta_above .= '<p>';
		}

		// author above
		if($params['author_show'] === 'above') {
			$post_meta_above .= $this->getPostAuthor($post, $params);
		}
		// categories above
		if($params['category_show'] === 'above') {
			// if there is an author already, we need to add an extra break
			if($params['author_show'] === 'above') {
				$post_meta_above .= '<br />';
			}
			// display post categories
			$post_meta_above .= $this->getPostCategories($post, $params);
		}

		// close the paragraph around author and categories
		if($params['author_show'] === 'above' || $params['category_show'] === 'above') {
			$post_meta_above .= '</p>';
		}

		if($params['post_content'] !== 'title') {
			if($params['title_position'] === 'inside') {
				// add title
				$content = $this->getPostTitle($post, $params).$post_meta_above.$content;
			} else {
				$content = $post_meta_above.$content;
			}
		} else {
			$content = $post_meta_above.$content;
		}

		if($params['post_content'] !== 'title') {
			// add read online link
			$content .= '<p><a href="'.get_permalink($post['ID']).'" target="_blank">'.stripslashes($params['readmore']).'</a></p>';
		}

		// post meta (author, categories) below
		$post_meta_below = '';

		// if the author or categories are displayed, open a new paragraph
		if($params['author_show'] === 'below' || $params['category_show'] === 'below') {
			$post_meta_below .= '<p>';
		}

		// author below
		if($params['author_show'] === 'below') {
			$post_meta_below .= $this->getPostAuthor($post, $params);
		}

		// categories below
		if($params['category_show'] === 'below') {
			// if there is an author already, we need to add an extra break
			if($params['author_show'] === 'below') {
				$post_meta_below .= '<br />';
			}
			$post_meta_below .= $this->getPostCategories($post, $params);
		}

		// close the paragraph around author and categories
		if($params['author_show'] === 'below' || $params['category_show'] === 'below') {
			$post_meta_below .= '</p>';
		}

		if($post_meta_below !== '') $content .= $post_meta_below;

		// set image/text alignment based on present data
		$post_image = null;

		if(($params['title_tag'] !== 'list') && isset($post['post_image'])) {
			$post_image = $post['post_image'];

			// set image alignment to match block's
			$post_image['alignment'] = $params['image_alignment'];

			// constrain size depending on alignment
			if(empty($post_image['height']) or $post_image['height'] === 0) {
				$post_image = null;
			} else {
				$ratio = round(($post_image['width'] / $post_image['height']) * 1000) / 1000;
				switch($params['image_alignment']) {
					case 'alternate':
					case 'left':
					case 'right':
						// constrain width to 325px max
						$post_image['width'] = min($params['image_width'], 325);
						break;
					case 'center':
						// constrain width to 564px max
						$post_image['width'] = min($params['image_width'], 564);
						break;
				}

				if($ratio > 0) {
					// if ratio has been calculated, deduce image height
					$post_image['height'] = (int)($post_image['width'] / $ratio);
				} else {
					// otherwise skip the image
					$post_image = null;
				}
			}
		}

		$position = 0;
		if(isset($params['position']) && (int)$params['position'] > 0) {
			$position = (int)$params['position'];
		}

		$block = array(
			'position' => $position,
			'type' => 'content',
			'text' => array(
				'value' => base64_encode($content)
			),
			'image' => $post_image,
			'alignment' => $params['image_alignment']
		);

		return $block;
	}

	public function getPostAuthor($post = array(), $params = array()) {
		$content = '';

		if(isset($post['post_author'])) {
			$author_name = get_the_author_meta('display_name', (int)$post['post_author']);

			// check if the user specified a label to be displayed before the author's name
			if(strlen(trim($params['author_label'])) > 0) {
				$author_name = stripslashes(trim($params['author_label'])).' '.$author_name;
			}
			$content .= $author_name;
		}

		return $content;
	}

	public function getPostCategories($post = array(), $params = array()) {
		$content = '';

		// get categories
		//$categories = get_the_category($post['ID']);
		$helper_wp_tools = WYSIJA::get('wp_tools', 'helper');
		$categories = $helper_wp_tools->get_post_categories($post);

		if(empty($categories) === false) {
			// check if the user specified a label to be displayed before the author's name
			if(strlen(trim($params['category_label'])) > 0) {
				$content = stripslashes($params['category_label']).' ';
			}

			$content .= join(', ', $categories);
		}

		return $content;
	}

	public function getPostTitle($post = array(), $params = array()) {
		$content = '';

		if(strlen(trim($post['post_title'])) > 0) {
			// cleanup post title and convert currency signs
			$post_title = trim(str_replace(array('$', '€', '£', '¥'), array('&#36;', '&euro;', '&pound;', '&#165;'), strip_tags($post['post_title'])));

			// open title tag
			if($params['title_tag'] === 'list') {
				$params['title_tag'] = 'li';
			}

			$content .= '<'.$params['title_tag'].' class="align-'.$params['title_alignment'].'">';
				// set title link
				$content .= '<a href="'.get_permalink($post['ID']).'" target="_blank">';
					// set title
					$content .= $post_title;
				// close title link
				$content .= '</a>';
			// close title tag
			$content .= '</'.$params['title_tag'].'>';

		}

		return $content;
	}

	function getImage($post) {
		$image_info = null;
		$post_image = null;

		// check if has_post_thumbnail function exists, if not, include wordpress class
		if(!function_exists('has_post_thumbnail')) {
			require_once(ABSPATH.WPINC.'/post-thumbnail-template.php');
		}

		// check for post thumbnail
		if(has_post_thumbnail($post['ID'])) {
			$post_thumbnail = get_post_thumbnail_id($post['ID']);

			// get attachment data (src, width, height)
			$image_info = wp_get_attachment_image_src($post_thumbnail, 'wysija-newsletters-max');

			// get alt text
			$altText = trim(strip_tags(get_post_meta($post_thumbnail, '_wp_attachment_image_alt', true)));
			if(strlen($altText) === 0) {
				// if the alt text is empty then use the post title
				$altText = trim(strip_tags($post['post_title']));
			}
		}

		if($image_info !== null) {
			$post_image = array(
				'src' => $image_info[0],
				'width' => $image_info[1],
				'height' => $image_info[2],
				'alt' => urlencode($altText)
			);
		} else {
			$matches = $matches2 = array();

			$output = preg_match_all('/<img.+src=['."'".'"]([^'."'".'"]+)['."'".'"].*>/i', $post['post_content'], $matches);

			if(isset($matches[0][0])){
				preg_match_all('/(src|height|width|alt)="([^"]*)"/i', $matches[0][0], $matches2);

				if(isset($matches2[1])){
					foreach($matches2[1] as $k2 => $v2) {
						if(in_array($v2, array('src', 'width', 'height', 'alt'))) {
							if($post_image === null) $post_image = array();

							if($v2 === 'alt') {
								// special case for alt text as it requireds url encoding
								$post_image[$v2] = urlencode($matches2[2][$k2]);
							} else {
								// otherwise simply get the value
								$post_image[$v2] = $matches2[2][$k2];
							}
						}
					}
				}
			}
		}

		$helper_images = WYSIJA::get('image','helper');
		$post_image = $helper_images->valid_image($post_image);

		if($post_image===null) return $post_image;
		return array_merge($post_image, array('url' => get_permalink($post['ID'])));
	}



	function convertEmbeddedContent($content = '') {
		// remove embedded video and replace with links
		$content = preg_replace('#<iframe.*?src=\"(.+?)\".*><\/iframe>#', '<a href="$1">'.__('Click here to view media.', WYSIJA).'</a>', $content);

		// replace youtube links
		$content = preg_replace('#http://www.youtube.com/embed/([a-zA-Z0-9_-]*)#Ui', 'http://www.youtube.com/watch?v=$1', $content);

		return $content;
	}

	function field_select_post_type( $params = array() ) {
		if ( array_key_exists( 'value', $params ) ) {
			$value = $params['value'];
		} else {
			return;
		}

		// make sure value is null if it's an empty string
		if ( $value !== null && strlen( trim( $value ) ) === 0 ){
			$value = null;
		}

		// get all post types
		$helper_wp_tools = WYSIJA::get( 'wp_tools', 'helper' );
		$post_types = $helper_wp_tools->get_post_types();

		// build post type selection
		$output = '<select name="post_type" data-placeholder="' . esc_attr__( 'Filter by type', WYSIJA ) . '" class="mailpoet-field-select2-simple" data-minimum-results-for-search="-1" style="margin-right: 5px;" width="150" id="post_type">';
		$output .= '<option></option>'; // This is require because of Select2 placeholding structure
		$output .= '<option value="post"' . ( ( $value === 'post' ) ? ' selected="selected"' : '' ) . '>' . __( 'Posts', WYSIJA ) . '</option>';
		$output .= '<option value="page"' . ( ( $value === 'page' ) ? ' selected="selected"' : '' ) . '>' . __( 'Pages', WYSIJA ) . '</option>';

		foreach ( $post_types as $key => $object_post_type ) {
			$selected = ($value === $key) ? ' selected="selected"' : '';
			$output .= '<option value="'.$key.'"'.$selected.'>'.$object_post_type->labels->name.'</option>';
		}
		$output .= '</select>';
		return $output;
	}

	function field_select_terms() {
		return '<input name="post_category" data-placeholder="' . esc_attr__( 'Categories and tags', WYSIJA ) . '" class="mailpoet-field-select2-terms post_category" style="margin-right: 5px; width: 180px"  width="180" value="" data-multilple="false" type="hidden">';
	}


	function field_select_status( $current_status = 'publish' ) {
		$output = '';

		$helper_wp_tools = WYSIJA::get( 'wp_tools', 'helper' );
		$statuses = $helper_wp_tools->get_post_statuses();

		$output .= '<select data-placeholder="' . esc_attr__( 'Filter by status', WYSIJA ) . '" class="mailpoet-field-select2-simple post_status" data-minimum-results-for-search="-1" id="post_status" name="post_status" width="150">';
		$output .= '<option></option>'; // This is require because of Select2 placeholding structure
		foreach ( $statuses as $key => $label ) {
			$is_selected = ( $current_status === $key ) ? 'selected="selected"' : '';
			$output .= '<option value="' . $key . '" ' . $is_selected . '>' . $label . '</option>';
		}
		$output .= '</select>';
		return $output;
	}
}
