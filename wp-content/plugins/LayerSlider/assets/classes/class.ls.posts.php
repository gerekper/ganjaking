<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

class LS_Posts {

	// Stores the last query results
	public $post = null;
	public $posts = null;

	/**
	 * Returns posts that matches the query params
	 * @param  array  	$args Array of WP_Query attributes
	 * @return bool           Success of the query
	 */
	public static function find($args = array()) {

		// Crate new instance
		$instance = new self;

		if($instance->posts = get_posts($args)) {
			$instance->post = $instance->posts[0];
		}
		return $instance;
	}

	public static function getPostTypes() {

		// Get post types
		$postTypes = get_post_types();

		// Remove some defalt post types
		if(isset($postTypes['revision'])) { unset($postTypes['revision']); }
		if(isset($postTypes['nav_menu_item'])) { unset($postTypes['nav_menu_item']); }

		// Convert names to plural
		foreach($postTypes as $key => $item) {
			if(!empty($item)) {
				$postTypes[$key] = array();
				$postTypes[$key]['slug'] = $item;
				$postTypes[$key]['obj'] = get_post_type_object($item);
				$postTypes[$key]['name'] = $postTypes[$key]['obj']->labels->name;
			}
		}

		return $postTypes;
	}


	public function getParsedObject() {

		if( ! $this->posts ) {
			return array();
		}

		foreach($this->posts as $key => $val) {
			$this->post = $val;
			$ret[$key]['post-id'] = $val->ID;
			$ret[$key]['post-slug'] = $val->post_name;
			$ret[$key]['post-url'] = get_permalink($val->ID);

			$ret[$key]['date-published'] = get_the_date('', $val->ID);
			$ret[$key]['time-published'] = get_the_time('', $val->ID);
			$ret[$key]['date-modified'] = get_the_modified_date('', $val->ID);
			$ret[$key]['time-modified'] = get_the_modified_time('', $val->ID);


			$ret[$key]['thumbnail'] = $this->getFeaturedImage( $val->ID, 'thumbnail' );
			$ret[$key]['thumbnail-url'] = $this->getFeaturedImageURL( $val->ID, 'thumbnail' );

			$ret[$key]['image'] = $this->getFeaturedImage( $val->ID );
			$ret[$key]['image-url'] = $this->getFeaturedImageURL( $val->ID );

			$ret[$key]['title'] = htmlspecialchars($this->getTitle());
			$ret[$key]['content'] = $this->getContent();
			$ret[$key]['excerpt'] = $this->getExcerpt();
			$ret[$key]['author'] = get_userdata($val->post_author)->user_nicename;
			$ret[$key]['author-name'] = get_userdata($val->post_author)->display_name;
			$ret[$key]['author-id'] = $val->post_author;
			$ret[$key]['author-avatar'] = $this->getAuthorImage($val);
			$ret[$key]['categories'] = $this->getCategoryList($val);
			$ret[$key]['tags'] = $this->getTagList($val);
			$ret[$key]['comments'] = $val->comment_count;
		}

		return $ret;
	}


	public function getWithFormat($str, $textlength = 0) {

		if(!is_object($this->post)) {
			return $str;
		}

		// Post ID
		if(stripos($str, '[post-id]') !== false) {
			$str = str_replace('[post-id]', $this->post->ID, $str); }

		// Post slug
		if(stripos($str, '[post-slug]') !== false) {
			$str = str_replace('[post-slug]', $this->post->post_name, $str); }

		// Post URL
		if(stripos($str, '[post-url]') !== false) {
			$str = str_replace('[post-url]', get_permalink($this->post->ID), $str);
		}

		// Date published
		if(stripos($str, '[date-published]') !== false) {
			$str = str_replace('[date-published]', get_the_date('', $this->post->ID), $str);
		}

		// Time published
		if(stripos($str, '[time-published]') !== false) {
			$str = str_replace('[time-published]', get_the_time('', $this->post->ID), $str);
		}

		// Date modified
		if(stripos($str, '[date-modified]') !== false) {
			$str = str_replace('[date-modified]', get_the_modified_date('', $this->post->ID), $str);
		}

		// Time modified
		if(stripos($str, '[time-modified]') !== false) {
			$str = str_replace('[time-modified]', get_the_modified_time('', $this->post->ID), $str);
		}

		// Featured image
		if(stripos($str, '[image]') !== false) {
			$markup = $this->getFeaturedImage( $this->post->ID );
			$str = str_replace('[image]', $markup, $str);
		}

		// Featured image URL
		if(stripos($str, '[image-url]') !== false) {

			$url = $this->getFeaturedImageURL( $this->post->ID );
			$str = str_replace('[image-url]', $url, $str);
		}

		// Featured image thumbnail
		if(stripos($str, '[thumbnail]') !== false) {
			$markup = $this->getFeaturedImage( $this->post->ID, 'thumbnail' );
			$str = str_replace('[thumbnail]', $markup, $str);
		}

		// Featured image thumbnail URL
		if(stripos($str, '[thumbnail-url]') !== false) {

			$url = $this->getFeaturedImageURL( $this->post->ID, 'thumbnail' );
			$str = str_replace('[thumbnail-url]', $url, $str);
		}

		// Title
		if(stripos($str, '[title]') !== false) {
			$str = str_replace('[title]', $this->getTitle($textlength), $str);
		}

		// Content
		if(stripos($str, '[content]') !== false) {
			$str = str_replace('[content]', $this->getContent($textlength), $str); }

		// Excerpt
		if(stripos($str, '[excerpt]') !== false) {
			$str = str_replace('[excerpt]', $this->getExcerpt($textlength), $str);
		}

		// Author nickname
		if(stripos($str, '[author]') !== false) {
			$str = str_replace('[author]', $this->getAuthor(true), $str); }

		// Author display name
		if(stripos($str, '[author-name]') !== false) {
			$str = str_replace('[author-name]', $this->getAuthor(false), $str); }

		// Author avatar image
		if(stripos($str, '[author-avatar]') !== false) {
			$str = str_replace('[author-avatar]', $this->getAuthorImage( $this->post ), $str); }


		// Author ID
		if(stripos($str, '[author-id]') !== false) {
			$str = str_replace('[author-id]', $this->post->post_author, $str); }

		// Category list
		if(stripos($str, '[categories]') !== false) {
			$str = str_replace('[categories]', $this->getCategoryList(), $str);
		}

		// Tags list
		if(stripos($str, '[tags]') !== false) {
			$str = str_replace('[tags]', $this->getTagList(), $str);
		}

		// Number of comments
		if(stripos($str, '[comments]') !== false) {
			$str = str_replace('[comments]', $this->post->comment_count, $str); }

		// Meta
		if(stripos($str, '[meta:') !== false) {
			$matches = array();
			preg_match_all('/\[meta:\w(?:[-\w]*\w)?]/', $str, $matches);

			foreach($matches[0] as $match) {
				$meta = str_replace('[meta:', '', $match);
				$meta = str_replace(']', '', $meta);
				$meta = get_post_meta($this->post->ID, $meta, true);
				$str = str_replace($match, $meta, $str);
			}
		}

		return $str;
	}


	/**
	 * Returns the lastly selected post's title
	 * @return string The title of the post
	 */
	public function getTitle($length = 0) {

		if(!is_object($this->post)) { return false; }

		$title = $this->post->post_title;

		if( ! empty( $length ) ) {

			if( function_exists('mb_substr') ) {
				$title = mb_substr($title, 0, $length);
			} else {
				$title = substr($title, 0, $length);
			}
		}

		return $title;
	}


	/**
	 * Returns the lastly selected post's excerpt
	 * @return string The excerpt of the post
	 */
	public function getExcerpt($textlength = 0) {

		global $post;
		$post = $this->post;

		setup_postdata($post);
		$excerpt = get_the_excerpt();
		wp_reset_postdata();

		if( ! empty( $excerpt ) && ! empty( $textlength ) ) {

			if( function_exists('mb_substr') ) {
				$excerpt = mb_substr( $excerpt, 0, $textlength );
			} else {
				$excerpt = substr( $excerpt, 0, $textlength );
			}
		}

		return $excerpt;
	}


	public function getAuthor($nick = true) {
		$key = $nick ? 'user_nicename' : 'display_name';
		if(is_object($this->post)) { return get_userdata($this->post->post_author)->$key; }
			else { return false; }
	}


	public function getAuthorImage( $post = null ) {

		if( ! empty( $post ) ) { $post = $this->post; }

		if( function_exists( 'get_avatar_url' ) ) {

			return '<img src="'.get_avatar_url( $post->post_author, array(
				'size' => 256
			)).'">';
		}

		return '';
	}


	public function getCategoryList( $post = null ) {

		if(!empty($post)) { $post = $this->post; }

		if(has_category(false, $this->post->ID)) {
			$cats = wp_get_post_categories($this->post->ID);
			foreach($cats as $val) {
				$cat = get_category($val);
				$list[] = '<a href="'.get_category_link($val).'">'.$cat->name.'</a>';
			}
			return '<div>'.implode(', ', $list).'</div>';
		} else {
			return '';
		}
	}


	public function getTagList( $post = null ) {

		if(!empty($post)) { $post = $this->post; }

		if(has_tag(false, $this->post->ID)) {
			$tags = wp_get_post_tags($this->post->ID);
			foreach($tags as $val) {
				$list[] = '<a href="/tag/'.$val->slug.'/">'.$val->name.'</a>';
			}
			return '<div>'.implode(', ', $list).'</div>';
		} else {
			return '';
		}
	}

	/**
	 * Returns a subset of the post's content,
	 * or the first paragraph if isn't specified
	 * @param  integer $length The subset's length
	 * @return string          The content
	 */
	public function getContent( $length = false ) {

		if( ! is_object( $this->post ) ) { return false; }

		$content = $this->post->post_content;
		if( ! empty( $length ) ) {

			if( function_exists( 'mb_substr' ) ) {
				$content = mb_substr( wp_strip_all_tags( $content ), 0, $length);
			} else {
				$content = substr( wp_strip_all_tags( $content ), 0, $length);
			}
		}

		return nl2br($content);
	}

	/**
	 * Returns the featured image URL for the specified post ID.
	 * Defaults to an empty GIF on error.
	 *
	 * @param  integer $postID  The ID of the post
	 * @param  string  $size    Attachment image size
	 * @return string			Featured image URL
	 */
	public function getFeaturedImageURL( $postID = 0, $size = 'full' ) {

		if( function_exists('get_post_thumbnail_id') ) {

			$attachmentID 	= get_post_thumbnail_id( $postID );
			$attachment 	= wp_get_attachment_image_src( $attachmentID, $size );

			if( ! empty( $attachment[0] ) ) {
				return $attachment[0];
			}
		}

		return LS_ROOT_URL . '/static/admin/img/blank.gif';
	}

	/**
	 * Returns the featured image HTML element markup for the specified post ID.
	 * Defaults to empty string on error.
	 *
	 * @param  integer $postID  The ID of the post
	 * @param  string  $size    Attachment image size
	 * @return string			<img> HTML markup or empty string on error
	 */
	public function getFeaturedImage( $postID = 0, $size = 'full' ) {

		if( function_exists('get_post_thumbnail_id') ) {

			$attachmentID 	= get_post_thumbnail_id( $postID );
			$attachment 	= wp_get_attachment_image( $attachmentID, $size );

			return $attachment;
		}

		return '';
	}
}
