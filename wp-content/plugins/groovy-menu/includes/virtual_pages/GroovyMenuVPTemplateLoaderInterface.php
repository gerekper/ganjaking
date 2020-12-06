<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

interface GroovyMenuVPTemplateLoaderInterface {
	/**
	 * Setup loader for a page objects
	 *
	 * @param \GM\VirtualPagesPageInterface $page matched virtual page
	 */
	public function init( GroovyMenuVPPageInterface $page );

	/**
	 * Trigger core and custom hooks to filter templates,
	 * then load the found template.
	 */
	public function load();
}
