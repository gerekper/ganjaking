<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php if ( $show_title ) : ?>
	<h2 id="support-conversations-title"><?php _e( 'My Support Conversations', 'woocommerce-help-scout' ); ?></h2>
<?php endif; ?>

<?php if ( 0 < count($conversations) ) : ?>

	<table id="support-conversations-table" class="shop_table <?php echo sanitize_html_class( get_template() ); ?>">
		<thead>
			<tr>
				<th class="ticket-number"><span class="nobr"><?php _e( 'Number', 'woocommerce-help-scout' ); ?></span></th>
				<th class="ticket-subject"><span class="nobr"><?php _e( 'Subject', 'woocommerce-help-scout' ); ?></span></th>
				<th class="ticket-date"><span class="nobr"><?php _e( 'Date', 'woocommerce-help-scout' ); ?></span></th>
				<th class="ticket-status"><span class="nobr"><?php _e( 'Status', 'woocommerce-help-scout' ); ?></span></th>
				<th class="ticket-actions">&nbsp;</th>
			</tr>
		</thead>

		<tbody>
			<?php
				foreach ( $conversations['_embedded']['conversations'] as $conversation ) :
					/*if ( $conversation['isDraft'] ) {
						continue;
					}*/

					$conversation_id = intval( $conversation['id'] );
					
					$subject         = esc_attr( $conversation['subject'] );
			?>
				<tr class="ticket">
					<td>#<?php echo intval( $conversation['number'] ); ?></td>
					<td><?php echo esc_html( $subject ); ?></td>
					<td>
					<?php
					// Help Scout returns date in UTC.
					// See http://developer.helpscout.net/help-desk-api/conversations/list/
					try {
						$modified_at = new DateTime( $conversation['userUpdatedAt'], new DateTimeZone( 'UTC' ) );
						$modified_at->setTimezone( new DateTimeZone( wc_timezone_string() ) );
						$modified_at = strtotime( $modified_at->format( 'Y-m-d H:i:s' ) );

						echo esc_html( date_i18n( $date_format, $modified_at ) );
					} catch ( Exception $e ) {
						// Nothing to output when the date/time string isn't a
						// valid date/time
					}
					?>
					</td>
					<td><?php echo esc_html( $integration->get_conversation_status( $conversation['status'] ) ); ?></td>
					<td style="text-align: right;"><a href="#" data-conversation-id="<?php echo esc_attr( $conversation_id ); ?>" class="button conversation-view"><?php _e( 'View', 'woocommerce-help-scout' ); ?></a> <a href="#" data-conversation-id="<?php echo esc_attr( $conversation_id ); ?>" data-subject="<?php echo esc_attr( $subject ); ?>" class="button conversation-reply"><?php _e( 'Reply', 'woocommerce-help-scout' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div id="support-conversations-navigation">

		<?php if ( 1 != $current_page ) : ?>
			<a class="button previous" href="<?php echo esc_url( $last_page ); ?>"><?php _e( 'Previous', 'woocommerce-help-scout' ); ?></a>
		<?php endif; ?>

		<?php if ( 1 < $conversations['page']['number'] ) : ?>
			<a class="button next" href="<?php echo esc_url( $next_page ); ?>"><?php _e( 'Next', 'woocommerce-help-scout' ); ?></a>
		<?php endif; ?>

	</div>

<?php else : ?>

	<p><?php _e( 'You have no support conversations.', 'woocommerce-help-scout' ); ?></p>

<?php endif; ?>

<div id="support-conversation-wrap"></div>
