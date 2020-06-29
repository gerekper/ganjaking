<?php

/**
 * The template for displaying rectification controller view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/admin folder
 *
 * @version 1.0
 *
 */

/** @var array $options */

?>

<?php if ( isset( $options['notices'] ) ) : ?>
	<?php foreach ( $options['notices'] as $notice ) : ?>

        <div class="ct-ultimate-gdpr notice-info notice">
			<?php echo esc_html( $notice ); ?>
        </div>

	<?php endforeach; endif; ?>

<div class="ct-ultimate-gdpr-wrap">

    <div class="ct-ultimate-gdpr-branding">
        <div class="ct-ultimate-gdpr-img">
            <img src="<?php echo ct_ultimate_gdpr_url() . '/assets/css/images/branding.jpg' ?>">
        </div>
        <div class="text">
            <div class="ct-ultimate-gdpr-plugin-name"><?php echo esc_html__( 'Ultimate GDPR', 'ct-ultimate-gdpr' ); ?></div>
            <div class="settings"><?php echo esc_html__( 'Settings', 'ct-ultimate-gdpr' ); ?></div>
        </div>
    </div>



    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url( 'admin.php?page=ct-ultimate-gdpr-forgotten' ); ?>" class="nav-tab">
			<?php echo esc_html__( 'Right To Be Forgotten', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="<?php echo admin_url( 'admin.php?page=ct-ultimate-gdpr-dataaccess' ); ?>" class="nav-tab">
			<?php echo esc_html__( 'Data Access', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="<?php echo admin_url( 'admin.php?page=ct-ultimate-gdpr-breach' ); ?>" class="nav-tab">
			<?php echo esc_html__( 'Data Breach', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="#" class="nav-tab nav-tab-active">
			<?php echo esc_html__( 'Data Rectification', 'ct-ultimate-gdpr' ); ?>
        </a>
    </h2>


    <form method="post" action="options.php">

		<?php

		// This prints out all hidden setting fields
		settings_fields( CT_Ultimate_GDPR_Controller_Rectification::ID );
		do_settings_sections( CT_Ultimate_GDPR_Controller_Rectification::ID );
		submit_button();

		?>

    </form>
</div>

<h2 class="ct-table-header"><?php echo esc_html__( "Data rectification requests list", 'ct-ultimate-gdpr' ); ?></h2>

<table class="wp-list-table widefat fixed striped">
    <thead>
    <tr>
        <th><?php echo esc_html__( 'User ID', 'ct-ultimate-gdpr' ); ?></th>
        <th><?php echo esc_html__( 'Email', 'ct-ultimate-gdpr' ); ?></th>
        <th><?php echo esc_html__( 'Date of request', 'ct-ultimate-gdpr' ); ?></th>
        <th><?php echo esc_html__( 'Date of data sent', 'ct-ultimate-gdpr' ); ?></th>
        <th><?php echo esc_html__( 'Select for action', 'ct-ultimate-gdpr' ); ?></th>
        <th><?php echo esc_html__( 'See details', 'ct-ultimate-gdpr' ); ?></th>
    </tr>
    </thead>
    <tbody>

	<?php

	if ( ! empty( $options['data'] ) ) :

		foreach ( $options['data'] as $datum ) : ?>

            <tr>

                <td><?php echo $datum['id'] ? esc_html( $datum['id'] ) : esc_html__( 'Not registered', 'ct-ultimate-gdpr' ); ?></td>
                <td><?php echo esc_html( $datum['user_email'] ); ?></td>
                <td><?php echo esc_html( $datum['date'] ); ?></td>
                <td><?php echo $datum['status'] ? esc_html( $datum['status'] ) : esc_html__( 'Not sent', 'ct-ultimate-gdpr' ); ?></td>

                <td><input type="checkbox" form="ct-ultimate-gdpr-rectification-form" name="emails[]"
                           value="<?php echo esc_html( $datum['user_email'] ); ?>"/></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="email" value="<?php echo esc_html( $datum['user_email'] ); ?>"/>
                        <input type="submit" class="button button-primary" name="ct-ultimate-gdpr-rectification-details"
                               value="<?php echo esc_html__( 'See rectified data', 'ct-ultimate-gdpr' ); ?>"/>
                    </form>
                </td>

            </tr>

		<?php

		endforeach;
	endif;

	?>

    </tbody>
    <tfoot>
    <tr>
        <td colspan="6">
            <form method="post" id="ct-ultimate-gdpr-rectification-form">
                <input type="submit" class="button button-primary" name="ct-ultimate-gdpr-rectification-send"
                       value="<?php echo esc_html__( 'Send data to selected emails', 'ct-ultimate-gdpr' ); ?>">
                <input type="submit" class="button button-secondary" name="ct-ultimate-gdpr-rectification-remove"
                       value="<?php echo esc_html__( 'Remove selected requests from list', 'ct-ultimate-gdpr' ); ?>">
                <input type="submit" class="button button-secondary" name="ct-ultimate-gdpr-rectification-remove-all-sent"
                       value="<?php echo esc_html__( 'Remove all sent requests from list', 'ct-ultimate-gdpr' ); ?>">
            </form>
        </td>
    </tr>
    </tfoot>
</table>