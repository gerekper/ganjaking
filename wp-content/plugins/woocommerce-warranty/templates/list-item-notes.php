<?php
$comment_query = new WP_Comment_Query(array(
    'type'      => 'wc_warranty_note',
    'post_id'   => $request_id
));

$notes = $comment_query->comments;

if ( empty( $notes ) ):
?>
    <li><?php _e('There are no notes yet', 'wc_warranty'); ?></li>
<?php
else:
    $datetime_format = get_option( 'date_format' ) .' '. get_option( 'time_format' );
    foreach( $notes as $note ):
        $author = new WP_User( $note->user_id );
        $pretty_date = date_i18n( $datetime_format, strtotime( $note->comment_date ) );
        ?>
        <li class="note" rel="<?php echo esc_attr( $note->comment_ID ); ?>">
            <div class="note-content">
                <p><?php echo wp_kses_post( $note->comment_content ); ?></p>
            </div>
            <p class="meta">
                <?php
                printf(
                    'added by %s on <abbr title="%s" class="exact-date">%s</abbr>',
                    $author->display_name,
                    $note->comment_date,
                    $pretty_date
                );
                ?>
                <a class="delete_note" href="#" data-request="<?php echo $request_id; ?>" data-note_id="<?php _e($note->comment_ID); ?>"><?php _e('Delete note', 'wc_warranty'); ?></a>
            </p>
        </li>
    <?php
    endforeach;
endif;
?>