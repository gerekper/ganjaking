<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="col-container" class="about-wrap">
	<?php
	echo '<div class="updated">' . wpautop( sprintf( __( 'See below for a list of the WooCommerce products in use on %s. You can %s, as well as our %s on how this works. %s', 'woothemes-updater' ), get_bloginfo( 'name' ), '<a href="https://woocommerce.com/my-account/my-subscriptions?utm_source=helper&utm_medium=product&utm_content=subscriptiontab">view your licenses here</a>', '<a href="http://docs.woocommerce.com/document/woothemes-helper/?utm_source=helper&utm_medium=product&utm_content=subscriptiontab">documentation</a>', '&nbsp;&nbsp;<a href="' . esc_url( add_query_arg( array( 'force-check' => '1' ), admin_url( 'update-core.php' ) ) ) . '" class="button">' . __( 'Check for Updates', 'woothemes-updater' ) . '</a>' ) ) . '</div>' . "\n";
	?>
		<div class="col-wrap">
			<form id="activate-products" method="post" action="" class="validate" data-connected="<?php echo $master_key_info ? 'true' : 'false'; ?>">
				<input type="hidden" name="action" value="activate-products" />
				<input type="hidden" name="page" value="<?php echo esc_attr( $this->page_slug ); ?>" />
				<?php
				require_once( $this->classes_path . 'class-woothemes-updater-licenses-table.php' );
				$this->list_table = new WooThemes_Updater_Licenses_Table();
				$this->list_table->data = $this->get_detected_products();
				$this->list_table->prepare_items();
				$this->list_table->display();
				?>
				<p class="submit woothemes-helper-submit-wrapper">
					<button type="submit" class="button button-primary"><span class="dashicons dashicons-admin-plugins"></span> <?php _e( 'Connect Subscriptions', 'woothemes-updater' ); ?></button>
					<?php echo '&nbsp;<a href="' . esc_url( $this->my_subscriptions_url ) . '" target="_blank" title="' . __( 'Manage my Subscriptions', 'woothemes-updater' ) . '" class="button manage-subscriptions-link"><span class="dashicons dashicons-admin-settings"></span> ' . __( 'Manage my Subscriptions', 'woothemes-updater' ) . '</a>' . "\n"; ?>
				</p><!--/.submit-->
				<p class="not-seeing-notice">
					<?php echo sprintf( __( 'Not seeing something you expect? %1$sBe sure your product is activated%2$s before adding a subscription key.', 'woothemes-updater' ), '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">', '</a>' ); ?>
				</p>
				<p class="enable-autorenew-notice">
					<?php echo sprintf( __( 'To make sure your subscriptions stay active, %1$sadd a saved card%2$s and %3$senable auto-renew%2$s on the subscriptions you’re continuing to enjoy.', 'woothemes-updater' ), '<a href="' . esc_url( $this->my_account_url ) . '">', '</a>', '<a href="' . esc_url( $this->my_subscriptions_url ) . '">' ); ?>
				</p>
				<?php wp_nonce_field( 'wt-helper-activate-license', 'wt-helper-nonce' ); ?>
			</form>
		</div><!--/.col-wrap-->
</div><!--/#col-container-->