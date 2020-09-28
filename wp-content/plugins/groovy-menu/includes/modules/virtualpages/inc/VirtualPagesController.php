<?php
namespace GroovyMenu;

use GroovyMenu\VirtualPagesControllerInterface as VirtualPagesControllerInterface;
use GroovyMenu\VirtualPagesTemplateLoaderInterface as VirtualPagesTemplateLoaderInterface;
use GroovyMenu\VirtualPagesPageInterface as VirtualPagesPageInterface;
use GroovyMenu\VirtualPagesPage as VirtualPagesPage;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


class VirtualPagesController implements VirtualPagesControllerInterface {

	private $pages;
	private $loader;
	private $matched;

	function __construct( VirtualPagesTemplateLoaderInterface $loader ) {
		$this->pages  = new \SplObjectStorage;
		$this->loader = $loader;
	}

	function init() {
		do_action( 'gm_add_virtual_page', $this );
	}

	function addPage( VirtualPagesPageInterface $page ) {
		$this->pages->attach( $page );

		return $page;
	}

	function dispatch( $bool, \WP $wp ) {
		if ( $this->checkRequest() && $this->matched instanceof VirtualPagesPage ) {
			$this->loader->init( $this->matched );
			$wp->virtual_page = $this->matched;
			do_action( 'parse_request', $wp );
			$this->setupQuery();
			do_action( 'wp', $wp );
			$this->loader->load();
			$this->handleExit();
		}

		return $bool;
	}

	private function checkRequest() {
		$this->pages->rewind();
		$path = trim( parse_url( $this->getPathInfo(), PHP_URL_PATH ), '/' );
		while ( $this->pages->valid() ) {
			if ( trim( $this->pages->current()->getUrl(), '/' ) === $path ) {
				$this->matched = $this->pages->current();

				return true;
			}
			$this->pages->next();
		}
	}

	private function getPathInfo() {
		$home_path = parse_url( home_url(), PHP_URL_PATH );

		return preg_replace( "#^/?{$home_path}/#", '/', add_query_arg( array() ) );
	}

	private function setupQuery() {
		global $wp_query;
		$wp_query->init();
		$wp_query->is_page        = true;
		$wp_query->is_singular    = true;
		$wp_query->is_home        = false;
		$wp_query->found_posts    = 1;
		$wp_query->post_count     = 1;
		$wp_query->max_num_pages  = 1;
		$posts                    = (array) apply_filters(
			'the_posts', array( $this->matched->asWpPost() ), $wp_query
		);
		$post                     = $posts[0];
		$wp_query->posts          = $posts;
		$wp_query->post           = $post;
		$wp_query->queried_object = $post;
		$GLOBALS['post']          = $post;
		$wp_query->virtual_page   = $post instanceof \WP_Post && isset( $post->is_virtual )
			? $this->matched
			: null;
	}

	public function handleExit() {
		exit();
	}

}
