<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<?php
$comment_query = new WP_Comment_Query(
	array(
		'type'    => 'wc_warranty_note',
		'post_id' => $request_id,
	)
);

$notes = $comment_query->comments;

if ( empty( $notes ) ) :
	?>
	<li><?php esc_html_e( 'There are no notes yet', 'wc_warranty' ); ?></li>
	<?php
else :
	foreach ( $notes as $note ) :
		$author      = new WP_User( $note->user_id );
		$pretty_date = date_i18n( WooCommerce_Warranty::get_datetime_format(), strtotime( $note->comment_date ) );
		?>
		<li class="note" rel="<?php echo esc_attr( $note->comment_ID ); ?>">
			<div class="note-content">
				<p><?php echo wp_kses_post( $note->comment_content ); ?></p>
			</div>
			<p class="meta">
				<?php
				printf(
					'added by %s on <abbr title="%s" class="exact-date">%s</abbr>',
					esc_html( $author->display_name ),
					esc_attr( $note->comment_date ),
					esc_html( $pretty_date ),
				);
				?>
				<a class="delete_note" href="#" data-request="<?php echo esc_attr( $request_id ); ?>" data-note_id="<?php echo esc_attr( $note->comment_ID ); ?>"><?php esc_html_e( 'Delete note', 'wc_warranty' ); ?></a>
			</p>
		</li>
		<?php
	endforeach;
endif;
?>
