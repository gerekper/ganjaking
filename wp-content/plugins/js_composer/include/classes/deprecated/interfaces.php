<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.3
 * @deprecated since 5.8
 * Interface for editors
 */
interface Vc_Editor_Interface {
	/**
	 * @return mixed
	 * @deprecated 5.8
	 * @since 4.3
	 */
	public function renderEditor();
}

/**
 * @since 4.3
 * @deprecated 5.8
 * Default render interface
 */
interface Vc_Render {
	/**
	 * @return mixed
	 * @deprecated 5.8
	 * @since 4.3
	 */
	public function render();
}

/**
 * @since 4.3
 * @deprecated 5.8
 * Interface for third-party plugins classes loader.
 */
interface Vc_Vendor_Interface {
	/**
	 * @return mixed
	 * @deprecated 5.8
	 * @since 4.3
	 */
	public function load();
}
