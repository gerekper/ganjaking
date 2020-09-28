<?php
namespace GroovyMenu;

use GroovyMenu\VirtualPagesTemplateLoaderInterface as VirtualPagesTemplateLoaderInterface;
use GroovyMenu\VirtualPagesPageInterface as VirtualPagesPageInterface;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


class VirtualPagesTemplateLoader implements VirtualPagesTemplateLoaderInterface {

	public function init( VirtualPagesPageInterface $page ) {
		$this->templates = wp_parse_args(
			array( 'page.php', 'index.php' ), (array) $page->getTemplate()
		);
	}

	public function load() {
		do_action( 'template_redirect' );
		$template = locate_template( array_filter( $this->templates ) );
		$filtered = apply_filters( 'template_include', apply_filters( 'virtual_page_template', $template ) );
		if ( empty( $filtered ) || file_exists( $filtered ) ) {
			$template = $filtered;
		}
		if ( ! empty( $template ) && file_exists( $template ) ) {
			require_once $template;
		}
	}

}
