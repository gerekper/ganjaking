<?php
/*
 * Template for Metabox User Info
 */
?>

<table class="yith-wcmbs-message-user-info">
    <tr>
        <th><?php _e( 'Username' ) ?></th>
        <td><?php echo $username; ?></td>
    </tr>
    <tr>
        <th><?php _e( 'First Name' ) ?></th>
        <td><?php echo $firstname; ?></td>
    </tr>
    <tr>
        <th><?php _e( 'Last Name' ) ?></th>
        <td><?php echo $lastname; ?></td>
    </tr>
    <tr>
        <th><?php _e( 'E-mail' ) ?></th>
        <td><?php echo $email; ?></td>
    </tr>
    <tr>
        <th><?php _e( 'Membership', 'yith-woocommerce-membership' ) ?></th>
        <td><?php echo $plans; ?></td>
    </tr>
</table>
