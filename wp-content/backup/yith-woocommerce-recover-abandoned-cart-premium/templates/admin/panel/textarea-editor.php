<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


extract( $args );

$editor_args = array(
	'wpautop'       => true, // use wpautop?
	'media_buttons' => true, // show insert/upload button(s)
	'textarea_name' => $id, // set the textarea name to something different, square brackets [] can be used here
	'textarea_rows' => 20, // rows="..."
	'tabindex'      => '',
	'editor_css'    => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
	'editor_class'  => '', // add extra class(es) to the editor textarea
	'teeny'         => false, // output the minimal editor config used in Press This
	'dfw'           => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
	'tinymce'       => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
	'quicktags'     => true, // load Quicktags, can be used to pass settings directly to Quicktags using an array()
);
?>
<div id="<?php echo esc_attr( $id ); ?>-container">
	<div class="editor"><?php wp_editor( $default, $id, $editor_args ); ?></div>
	<p><span class="desc"><?php echo wp_kses_post( $desc ); ?></span></p>
</div>
