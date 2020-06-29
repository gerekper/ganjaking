<?php
/**
 * Hooks
 *
 * @package Betheme
 * @author Muffin group
 * @link http://muffingroup.com
 */


/* ---------------------------------------------------------------------------
 * Hook | Top
 * --------------------------------------------------------------------------- */
if( ! function_exists( 'mfn_hook_top' ) )
{
	function mfn_hook_top()
	{
		echo '<!-- mfn_hook_top -->';
			echo do_shortcode( mfn_opts_get( 'hook-top' ) );
		echo '<!-- mfn_hook_top -->';
	}
}
add_action( 'mfn_hook_top', 'mfn_hook_top' );


/* ---------------------------------------------------------------------------
 * Hook | Content before
 * --------------------------------------------------------------------------- */
if( ! function_exists( 'mfn_hook_content_before' ) )
{
	function mfn_hook_content_before()
	{
		echo '<!-- mfn_hook_content_before -->';
			echo do_shortcode( mfn_opts_get( 'hook-content-before' ) );
		echo '<!-- mfn_hook_content_before -->';
	}
}
add_action( 'mfn_hook_content_before', 'mfn_hook_content_before' );


/* ---------------------------------------------------------------------------
 * Hook | Content after
 * --------------------------------------------------------------------------- */
if( ! function_exists( 'mfn_hook_content_after' ) )
{
	function mfn_hook_content_after()
	{
		echo '<!-- mfn_hook_content_after -->';
			echo do_shortcode( mfn_opts_get( 'hook-content-after' ) );
		echo '<!-- mfn_hook_content_after -->';
	}
}
add_action( 'mfn_hook_content_after', 'mfn_hook_content_after' );


/* ---------------------------------------------------------------------------
 * Hook | Bottom
 * --------------------------------------------------------------------------- */
if( ! function_exists( 'mfn_hook_bottom' ) )
{
	function mfn_hook_bottom()
	{
		echo '<!-- mfn_hook_bottom -->';
		echo do_shortcode( mfn_opts_get( 'hook-bottom' ) );
		echo '<!-- mfn_hook_bottom -->';
	}
}
add_action( 'mfn_hook_bottom', 'mfn_hook_bottom' );
