<?php

/**
 * The template for displaying breach controller 'send email' action view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/admin folder
 *
 * @version 1.0
 *
 */

?>

<div class="ct-ultimate-gdpr-wrap">

	<?php if ( ct_ultimate_gdpr_get_value( 'ct-ultimate-gdpr-breach-send-submit', $_POST ) ) :
		echo esc_html__( "Emails were sent.", 'ct-ultimate-gdpr' );

		return;
	endif;

	?>

    <p>
		<?php echo esc_html__( "You will send emails to the following emails:", 'ct-ultimate-gdpr' ); ?>
    </p>

	<?php

	if ( empty( $options['recipients'] ) ) :
		return;
	endif;

	?>

    <form method="post">
		<?php

		submit_button(
			esc_html__( 'Send', 'ct-ultimate-gdpr' ),
			'primary',
			"ct-ultimate-gdpr-breach-send-submit",
			false
		);

		?>
    </form>

    <br>

    <var>

		<?php

		foreach ( $options['recipients'] as $email ) :
			echo sanitize_email($email);
			echo '<br>';
		endforeach;

		?>

    </var>

</div>