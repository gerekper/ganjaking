<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/**
* Functions to register client-side assets (scripts and stylesheets) for the
* Gutenberg block.
*
* @package memberpress-courses
*/

/**
* Registers all block assets so that they can be enqueued through Gutenberg in
* the corresponding context.
*
* @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
*/
function progress_bar_block_init() {
  $dir = dirname( __FILE__ );

  $block_js = 'progress-bar/block.js';
  \wp_register_script(
    'progress-bar-block-editor',
    plugins_url( $block_js, __FILE__ ),
    array(
      'wp-blocks',
      'wp-i18n',
      'wp-element',
    ),
    filemtime( "$dir/$block_js" )
  );

  $editor_css = 'progress-bar/editor.css';
  \wp_register_style(
    'progress-bar-block-editor',
    plugins_url( $editor_css, __FILE__ ),
    array(
      'wp-blocks',
    ),
    filemtime( "$dir/$editor_css" )
  );

  $style_css = 'progress-bar/style.css';
  \wp_register_style(
    'progress-bar-block',
    plugins_url( $style_css, __FILE__ ),
    array(
      'wp-blocks',
    ),
    filemtime( "$dir/$style_css" )
  );

  register_block_type( 'memberpress-courses/progress-bar', array(
    'editor_script' => 'progress-bar-block-editor',
    'editor_style'  => 'progress-bar-block-editor',
    'style'         => 'progress-bar-block',
  ) );
}
add_action( 'init', 'progress_bar_block_init' );
