<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

global $wp_version;
?>

<div class="wrap wpb-rs-support">
    <h1><?php echo get_admin_page_title(); ?></h1>

	<?php if ( version_compare( $wp_version, '5', '<' ) ): ?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">

        <div id="side-info-column" class="inner-sidebar">
			<?php
			do_meta_boxes( 'rich-snippets-support', 'side', array() );
			?>
        </div>

        <div id="post-body" class="has-sidebar">
            <div id="post-body-content" class="has-sidebar-content">
				<?php
				do_meta_boxes( 'rich-snippets-support', 'normal', array() );
				?>
            </div>
        </div>
        <br class="clear"/>
		<?php else: ?>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
						<?php
						do_meta_boxes( 'rich-snippets-support', 'normal', array() );
						?>
                    </div>
                    <div id="postbox-container-1">
						<?php
						do_meta_boxes( 'rich-snippets-support', 'side', array() );
						?>
                    </div>
                </div>
                <div class="clear"/>
            </div>
		<?php endif; ?>
    </div>
</div>
