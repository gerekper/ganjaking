<?php
/**
 * Conversation
 *
 * @package  conversation
 * Checks if WooCommerce is enabled.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php if ( $show_title ) : ?>
	<h2 id="support-conversations-title"><?php esc_html_e( 'My Support Conversations', 'woocommerce-help-scout' ); ?></h2>
<?php endif; ?>

<?php if ( 0 < count( $conversations ) ) : ?>

	<table id="support-conversations-table" class="shop_table <?php echo sanitize_html_class( get_template() ); ?>">
		<thead>
			<tr>
				<th class="ticket-number"><span class="nobr"><?php esc_html_e( 'Number', 'woocommerce-help-scout' ); ?></span></th>
				<th class="ticket-subject"><span class="nobr"><?php esc_html_e( 'Subject', 'woocommerce-help-scout' ); ?></span></th>
				<th class="ticket-date"><span class="nobr"><?php esc_html_e( 'Date', 'woocommerce-help-scout' ); ?></span></th>
				<th class="ticket-status"><span class="nobr"><?php esc_html_e( 'Status', 'woocommerce-help-scout' ); ?></span></th>
				<th class="ticket-actions"><span class="nobr">Actions</span></th>
			</tr>
		</thead>

		<tbody>
			<?php
			foreach ( $conversations['_embedded']['conversations'] as $conversation ) :
				$conversation_id = intval( $conversation['id'] );

				$subject         = esc_attr( $conversation['subject'] );
				$order_id = end( explode( ' ', trim( $subject ) ) );
				?>
				<tr class="ticket">
					<td>#<?php echo intval( $conversation['number'] ); ?></td>
					<td><?php echo esc_html( $subject ); ?></td>
					<td>
				<?php
				// Help Scout returns date in UTC.
				// See http://developer.helpscout.net/help-desk-api/conversations/list/.
				try {
					$modified_at = new DateTime( $conversation['userUpdatedAt'], new DateTimeZone( 'UTC' ) );
					$modified_at->setTimezone( new DateTimeZone( wc_timezone_string() ) );
					$modified_at = strtotime( $modified_at->format( 'Y-m-d H:i:s' ) );

					echo esc_html( date_i18n( $date_format, $modified_at ) );
				} catch ( Exception $e ) {
					echo 'Error writing to database: ';
					// Nothing to output when the date/time string isn't a.
					// valid date/time.
				}
				?>
					</td>
					<td><?php echo esc_html( $integration->get_conversation_status( $conversation['status'] ) ); ?></td>
					<td style="text-align: right;"><a href="#" data-conversation-id="<?php echo esc_attr( $conversation_id ); ?>" class="button conversation-view conversation-view-<?php echo esc_attr( $order_id ); ?>" data-subject="<?php echo esc_attr( $subject ); ?>" ><?php esc_attr_e( 'View', 'woocommerce-help-scout' ); ?></a> <a href="#" data-conversation-id="<?php echo esc_attr( $conversation_id ); ?>" data-subject="<?php echo esc_attr( $subject ); ?>" class="button conversation-reply"><?php esc_attr_e( 'Reply', 'woocommerce-help-scout' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div id="support-conversations-navigation">

		<?php if ( 1 !== $current_page ) : ?>
			<a class="button previous" href="<?php echo esc_url( $last_page ); ?>"><?php esc_html_e( 'Previous', 'woocommerce-help-scout' ); ?></a>
		<?php endif; ?>

		<?php if ( 1 < $conversations['page']['number'] ) : ?>
			<a class="button next" href="<?php echo esc_url( $next_page ); ?>"><?php esc_html_e( 'Next', 'woocommerce-help-scout' ); ?></a>
		<?php endif; ?>

	</div>

<?php else : ?>

	<p><?php esc_attr_e( 'You have no support conversations.', 'woocommerce-help-scout' ); ?></p>

<?php endif; ?>
<div id="support-conversation-wrap"></div>
<div id="support-conversation-wrap-reply" style="display:none;">
	<h3 id="support-conversation-thread-head-reply"></h3>
	<form id="support-conversation-reply" action="" method="post" enctype="multipart/form-data">
		<p class="form-row form-row-wide">
			<label for="conversation-message">Message <span class="required">*</span></label>
			<textarea id="conversation-message" class="conversation-field" name="conversation_message" cols="25" rows="15"></textarea>
		</p>
		<div id="plUploadArea">

		</div>
		<br>
		<p class="form-row">
			<input type="hidden" name="conversation_id" id="reply_conversation_id" value="">
			<input type="hidden" name="user_id" id="reply_user_id" value="">
			<input type="submit" id="reply_submit_btn" class="button alt" value="Send">
		</p>
	</form>
</div>
<div id="support-wrap-conversation"></div>
