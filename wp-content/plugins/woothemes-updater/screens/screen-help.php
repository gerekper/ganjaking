<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="about-wrap">
<div class="changelog">
<?php do_action( 'woothemes_helper_before' ); ?>
<div class="feature-section col three-col">
	<div id="column-left">
		<?php do_action( 'woothemes_helper_column_left' ); ?>
	</div><!--/#column-left-->
	<div id="column-middle">
		<?php do_action( 'woothemes_helper_column_middle' ); ?>
	</div><!--/#column-middle-->
	<div id="column-right" class="last-feature">
		<?php do_action( 'woothemes_helper_column_right' ); ?>
	</div>
</div><!--/#col-container .feature-section col three-col-->
<?php do_action( 'woothemes_helper_after' ); ?>
</div><!--/.changelog-->