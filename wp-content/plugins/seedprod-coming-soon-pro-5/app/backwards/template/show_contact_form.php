<?php if ( ! empty( $settings->enable_cf_form ) ) : ?>
<div id="cspio-contact-form">
	<?php
	echo do_shortcode( '[seed_contact_form text="' . esc_attr( $settings->txt_contact_us ) . '"]' );
	?>
</div>
<?php endif; ?>
