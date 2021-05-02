<?php use AC\Type\Url\Documentation; ?>
<p>
	<?php _e( 'Select the filtered segment that you want as the default when users visit the table.', 'codepress-admin-columns' ); ?>
</p>
<p>
	<?php printf(
		__( 'Only saved filtered segments marked as %s are selectable.', 'codepress-admin-columns' ),
		sprintf( '<strong>%s</strong>', __( 'Public', 'codepress-admin-columns' ) )
	); ?>
</p>

<img src="<?= esc_url( $this->location->get_url() ); ?>assets/core/images/preferred-filters.png" alt=""/>

<p>
	<a href="<?= esc_url( Documentation::create_with_path( Documentation::ARTICLE_SAVED_FILTERS ) ); ?>" target="_blank">
		<?= __( 'How to create a filtered segment', 'codepress-admin-columns' ); ?> &raquo;
	</a>
</p>