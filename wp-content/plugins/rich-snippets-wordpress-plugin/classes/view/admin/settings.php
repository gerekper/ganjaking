<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

settings_errors();
global $wp_version;
?>

<div class="wrap wpb-rs-settings">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <form action="<?php echo admin_url( 'options.php' ); ?>" method="post">

		<?php
		settings_fields( 'rich-snippets-settings' );
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		?>

		<?php if ( version_compare( $wp_version, '5', '<' ) ): ?>
            <div id="poststuff" class="metabox-holder has-right-sidebar">

                <div id="side-info-column" class="inner-sidebar">
					<?php
					do_meta_boxes( 'rich-snippets-settings', 'side', array() );
					?>
                </div>

                <div id="post-body" class="has-sidebar">
                    <div id="post-body-content" class="has-sidebar-content">
						<?php
						do_meta_boxes( 'rich-snippets-settings', 'normal', array() );
						?>
                    </div>
                </div>
            </div>
            <br class="clear"/>
		<?php else: ?>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
						<?php
						do_meta_boxes( 'rich-snippets-settings', 'normal', array() );
						?>
                    </div>
                    <div id="postbox-container-1">
						<?php
						do_meta_boxes( 'rich-snippets-settings', 'side', array() );
						?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
		<?php endif; ?>
    </form>
</div>
