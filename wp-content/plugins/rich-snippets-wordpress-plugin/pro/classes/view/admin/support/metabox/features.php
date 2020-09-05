<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>

<p><?php
	printf(
		__( 'Hey <strong>%s</strong>, here is Florian again. As you may know this plugin is maintained by me for <strong>%s</strong> now and I want it to grow even more. But I need your help. Please share your ideas and let me know what features you want to see for the next versions. Here are some ideas by other users. Don\'t be shy and vote for some of them if you think one of it could be interesting for you, too.', 'rich-snippets-schema' ),
		Helper_Model::instance()->get_current_user_firstname(),
		human_time_diff( 1353452400 )
	);
	?></p>

<?php
$post_id = defined( 'WPB_RS_REMOTE' ) ? 1 : 443;

$feature_requests = WPBuddy_Model::request(
	"/wp/v2/comments/?post={$post_id}&per_page=30&orderby=wpb-rs-feature-rating&order=desc&parent=0"
);
?>

<table class="wpb-rs-support-feature-requests wp-list-table widefat striped">
	<thead>
	<tr>
		<th><?php _e( 'Rating', 'rich-snippets-schema' ); ?></th>
		<th><?php _e( 'Feature Request / <a href="https://rich-snippets.io/feature-request/" target="_blank">See all feature requests</a>', 'rich-snippets-schema' ); ?></th>
		<th class="vote-column"><?php _e( 'Vote', 'rich-snippets-schema' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if ( is_wp_error( $feature_requests ) ) {
		printf( '<tr><td colspan="3">%s</td></tr>', __( 'Could not fetch feature requests.', 'rich-snippets-schema' ) );
	} else {
		foreach ( $feature_requests as $feature_request ) {
			printf( '<tr data-comment_id="%d">', esc_attr( $feature_request->id ) );

			printf(
				'<td>%s</td>',
				isset( $feature_request->wpb_rs_feature_rating ) ? floatval( $feature_request->wpb_rs_feature_rating ) : 0
			);

			printf(
				'<td>%s<div>%s</div></td>',
				wp_kses( $feature_request->content->rendered, wp_kses_allowed_html() ),
				esc_html( $feature_request->author_name )
			);
			?>
			<td class="vote-column">
				<nobr>
					<a data-direction="up" href="#" class="wpb-rs-support-feature-vote button button-small">
						<span class="dashicons dashicons-thumbs-up"></span>
					</a>
					<a href="#" data-direction="down" class="wpb-rs-support-feature-vote button button-small">
						<span class="dashicons dashicons-thumbs-down"></span>
					</a>
				</nobr>
				<p class="vote-errors"></p>
			</td>
			<?php
			print( '</tr>' );
		}
	}
	?>
	</tbody>
	<tfoot>
	<tr>
		<td></td>
		<td colspan="2">
			<p class="description"><?php _e( 'Please share your ideas and create a new feature request. Please do not share any private data and <u>do not use this form for support requests</u>.', 'rich-snippets-schema' ); ?></p>
			<p class="description gdpr">
				<input type="checkbox" value="1" id="gdpr"/><label
						for="gdpr"><?php _e( 'I understand the following: After hitting the send button the text will be sent to our servers and that it may create a new public comment on the <a href="https://rich-snippets.io/feature-request/" target="_blank">rich-snippets.io feature request page</a>. The comment will be created as the user the current purchase code belongs to. It will be saved as long as you tell me to delete it or when the feature landed in one of our future versions of the plugin. Find out more about in our <a href="https://wp-buddy.com/imprint/" target="_blank">privacy policy</a>.', 'rich-snippets-schema' ); ?></label>
			</p>
			<textarea class="wpb-rs-support-feature-text large-text" rows="4"></textarea>
			<a class="button wpb-rs-support-feature-text-send"><?php _e( 'Share this with the community', 'rich-snippets-schema' ); ?></a>
			<p class="vote-errors"></p>
			<p class="vote-success">
				<?php _e( 'Thanks for adding your feature request. It may take a while till it appears here.', 'rich-snippets-schema' ) ?>
			</p>
		</td>
	</tr>
	</tfoot>
</table>

