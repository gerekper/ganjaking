<?php
namespace GroovyMenu;

use GroovyMenu\VirtualPagesPageInterface as VirtualPagesPageInterface;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


interface VirtualPagesTemplateLoaderInterface {
	/**
	 * Setup loader for a page objects
	 *
	 * @param VirtualPagesPageInterface $page matched virtual page
	 */
	public function init( VirtualPagesPageInterface $page );

	/**
	 * Trigger core and custom hooks to filter templates,
	 * then load the found template.
	 */
	public function load();
}
