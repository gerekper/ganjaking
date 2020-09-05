<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>

<button class="button button-big wpb-rs-overwrite-snippet">
	<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 1041 1013">
		<path fill="#555"
			  d="M332.672,244.708q-97.679,78.74-97.667,206.327,0,127.611,80.175,184.456,80.152,56.867,236.151,71.448v35a907.63,907.63,0,0,1-175.656-16.769Q290.4,708.42,249.582,687.984V1004.4l33.528,13.12q33.516,13.125,125.364,26.25t209.912,13.12q221.564,0,310.5-71.445,88.9-71.427,88.919-191.017,0-119.544-67.053-164.77-67.067-45.18-247.813-62.7v-35q196.793,0,295.918,51.035V218.462l-33.528-13.124q-34.984-13.124-127.551-26.246-92.587-13.124-207.725-13.124Q430.317,165.968,332.672,244.708Z"
			  transform="translate(-235 -165.969)"></path>
		<rect fill="#fff" x="16" y="973.031" width="1025" height="40"></rect>
	</svg>
	<?php _e( 'Edit global snippets', 'rich-snippets-schema' ); ?>
</button>
