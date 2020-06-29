<?php
/**
 * The Page Sidebar containing the widget area.
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

$sidebar = mfn_sidebar();

if( isset($sidebar['sidebar']['first']) ){
	echo '<div class="mcb-sidebar sidebar sidebar-1 four columns '. esc_attr(mfn_opts_get('sidebar-lines')) .'">';
		echo '<div class="widget-area">';
			echo '<div class="inner-wrapper-sticky clearfix">';
				dynamic_sidebar($sidebar['sidebar']['first']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

if( isset($sidebar['sidebar']['second']) ){
	echo '<div class="mcb-sidebar sidebar sidebar-2 four columns '. esc_attr(mfn_opts_get('sidebar-lines')) .'">';
		echo '<div class="widget-area">';
			echo '<div class="inner-wrapper-sticky clearfix">';
				dynamic_sidebar($sidebar['sidebar']['second']);
				echo '</div>';
		echo '</div>';
	echo '</div>';
}
