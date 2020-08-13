<?php

/**
 * The template for displaying data access controller 'user details' action view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/admin folder
 *
 * @version 1.0
 *
 */

/** @var array $options */

?>

<h3><?php printf( esc_html__( "Collected data for user: %s", 'ct-ultimate-gdpr' ), $options['email'] ); ?></h3>

<form method="post">
    <input type="submit" class="button button-primary" name=""
           value="<?php echo esc_html__( 'Go back', 'ct-ultimate-gdpr' ); ?>">
</form>

<?php foreach ( $options['data'] as $service_name => $output ) : ?>

    <h4><?php echo esc_html( $service_name ); ?></h4>
    <code><?php echo $output ? esc_html( $output ) : esc_html__( 'No data collected for this service', 'ct-ultimate-gdpr' ); ?></code>

<?php endforeach; ?>

<br/>
<br/>
<form method="post">
    <input type="submit" class="button button-primary" name=""
           value="<?php echo esc_html__( 'Go back', 'ct-ultimate-gdpr' ); ?>">
</form>

