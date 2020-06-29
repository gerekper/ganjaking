<?php
namespace GroovyMenu;

use GroovyMenu\VirtualPagesPageInterface as VirtualPagesPageInterface;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


class VirtualPagesPage implements VirtualPagesPageInterface {

	private $url;
	private $title;
	private $content;
	private $template;
	private $wp_post;

	function __construct( $url, $title = 'Untitled', $template = 'page.php' ) {
		$this->url = filter_var( $url, FILTER_SANITIZE_URL );
		$this->setTitle( $title );
		$this->setTemplate( $template );
	}

	function getUrl() {
		return $this->url;
	}

	function getTemplate() {
		return $this->template;
	}

	function getTitle() {
		return $this->title;
	}

	function setTitle( $title ) {
		$this->title = filter_var( $title, FILTER_SANITIZE_STRING );

		return $this;
	}

	function setContent( $content ) {
		$this->content = $content;

		return $this;
	}

	function setTemplate( $template ) {
		$this->template = $template;

		return $this;
	}

	function asWpPost() {
		if ( is_null( $this->wp_post ) ) {
			$post          = array(
				'ID'             => 0,
				'post_title'     => $this->title,
				'post_name'      => sanitize_title( $this->title ),
				'post_content'   => $this->content ? : '',
				'post_excerpt'   => '',
				'post_parent'    => 0,
				'menu_order'     => 0,
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'page_template'  => $this->template,
				'comment_count'  => 0,
				'post_password'  => '',
				'to_ping'        => '',
				'pinged'         => '',
				'guid'           => home_url( $this->getUrl() ),
				'post_date'      => current_time( 'mysql' ),
				'post_date_gmt'  => current_time( 'mysql', 1 ),
				'post_author'    => is_user_logged_in() ? get_current_user_id() : 0,
				'is_virtual'     => true,
				'gm_vp_flag_on'  => true,
				'filter'         => 'raw',
			);
			$this->wp_post = new \WP_Post( (object) $post );
		}

		return $this->wp_post;
	}
}
