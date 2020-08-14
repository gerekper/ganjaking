<?php
/**
 * Customer tab
 *
 * Show the list table for customer.
 *
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Points and Rewards
 */

/**
 * @var WC_Customer $user
 * @var string      $link
 * @var string      $type
 */


?>
<div class="wrap par-wrap">
	<h2><?php esc_html_e( 'Customers\' Points', 'yith-woocommerce-points-and-rewards' ); ?>
		<?php
		if ( isset( $_GET['action'] ) && isset( $link ) ) :
			?>
			<a href="<?php echo esc_url( $link ); ?>"
			class="add-new-h2"><?php esc_html_e( '< Back to list', 'yith-woocommerce-points-and-rewards' ); ?></a><?php endif ?>
	</h2>

	<?php
	if ( 'customer' === $type ) :
		$banned_users = (array) YITH_WC_Points_Rewards()->get_option( 'banned_users' );
		$user         = new WC_Customer( $user_id );
		$arg          = remove_query_arg( array( 'paged', 'orderby', 'order' ) );
		$name         = '';
		if ( $user->get_first_name() || $user->get_last_name() ) {
			$name = sprintf( _x( '#%1$d - %2$s %3$s', 'First placeholder: user id; second placeholder: user first name; third placeholder: user last name','yith-woocommerce-points-and-rewards' ), $user->get_id(), $user->get_first_name(), $user->get_last_name() );
		} else {
			$name = sprintf( _x( '#%1$d - %2$s', 'First placeholder: user id; second placeholder: user display name', 'yith-woocommerce-points-and-rewards' ), $user->get_id(), $user->get_display_name() );
		}
		$points = get_user_meta( $user_id, '_ywpar_user_total_points', true );
		?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="ywpar_user_info_wrapper">
						<div class="ywpar_user_info">
							<h2><?php echo esc_html( $name ); ?>
								<?php if ( in_array( $user_id, $banned_users ) ) : ?>
									<span
										class="ywpar_ban"><?php esc_html_e( 'banned', 'yith-woocommerce-points-and-rewards' ); ?></span>
								<?php endif; ?></h2>
							<p><?php echo wp_kses_post( sprintf( _x( 'Current points: <strong>%d</strong> ','Placeholder: number of points', 'yith-woocommerce-points-and-rewards' ), $points ) ); ?></p>
							<p><a href="
						<?php
								echo esc_url(
									add_query_arg(
										array(
											'action' => 'reset',
											'user'   => $user_id,
										),
										$arg
									)
								)
						?>
							" class="ywpar_update_points ywpar_reset_points button action button button-primary"
									data-username="<?php echo esc_attr( $user->get_display_name() ); ?>"><?php esc_html_e( 'Reset Points', 'yith-woocommerce-points-and-rewards' ); ?></a>
								<?php if ( ! in_array( $user_id, $banned_users ) ) : ?>
									<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'ban' ), $arg ) ); ?>"
										class="ywpar_update_points ywpar_ban_user button button-primary action"
										data-username="<?php echo esc_attr( $user->get_display_name() ); ?>"><?php esc_html_e( 'Ban the user', 'yith-woocommerce-points-and-rewards' ); ?></a>
								<?php else : ?>
									<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'unban' ), $arg ) ); ?>"
										class="ywpar_update_points ywpar_ban_user button action"
										data-username="<?php echo esc_attr( $user->get_display_name() ); ?>"><?php esc_html_e( 'Unban the user', 'yith-woocommerce-points-and-rewards' ); ?></a>
								<?php endif; ?></p>
						</div>
						<div class="ywpar_update_point">
							<form method="post" class="ywpar_update_point_form">
								<h2><?php esc_html_e( 'Update user points', 'yith-woocommerce-points-and-rewards' ); ?></h2>
								<p><?php esc_html_e( 'You can either add or remove points. Insert positive numbers to add points to the customer\'s balance or negative values to remove points.', 'yith-woocommerce-points-and-rewards' ); ?></p>
								<div class="ywpar-input-wrapper"><input type="number" value="" name="user_points"
										placeholder="0"/>
									<input type="text" name="description"
										placeholder="<?php esc_attr_e( 'Add a description', 'yith-woocommerce-points-and-rewards' ); ?>"/>
								</div>
								<input type="hidden" name="action" value="save"/>
								<input type="hidden" name="user_id" value="<?php echo esc_attr( $user_id ); ?>"/>
								<?php wp_nonce_field( 'update_points', 'ywpar_update_points' ); ?>
								<input type="submit" class="ywpar_update_points button button-primary action"
									value="<?php esc_attr_e( 'Update Points', 'yith-woocommerce-points-and-rewards' ); ?>"/>
							</form>
						</div>
					</div>
					<div class="history-table">
						<div class="meta-box-sortables ui-sortable">
							<h2><?php esc_html_e( 'Points history', 'yith-woocommerce-points-and-rewards' ); ?></h2>

							<?php
							$this->cpt_obj->prepare_items();
							$this->cpt_obj->display();
							?>

						</div>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	<?php else : ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<?php $this->cpt_obj->search_box( 'search', 'search_id' ); ?>
						<form method="post">
							<?php
							$this->cpt_obj->prepare_items();
							$this->cpt_obj->display();
							?>
						</form>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	<?php endif; ?>
</div>
