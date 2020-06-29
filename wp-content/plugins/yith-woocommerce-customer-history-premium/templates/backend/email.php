<?php

defined( 'ABSPATH' ) or exit;

/*
 *  Customers
 */

global $wpdb;

$sender_id = get_current_user_id();
$sender = get_user_by( 'id', $sender_id );

if ( isset( $_GET['act'] ) && $_GET['act'] == 'send' ) {

    $user_id = isset( $_GET['user_id'] ) ? $_GET['user_id'] : '';
    $subject = isset( $_GET['subject'] ) ? $_GET['subject'] : '';
    $content = isset( $_GET['content'] ) ? $_GET['content'] : '';

    YITH_WCCH_Email::send( $user_id, $subject, $content );

}

$sender_name = '';
$sender_email = '';
$user_name = '';
$user_email = '';
$email_subject = '';
$email_content = '';

$email_id = isset( $_GET['email_id'] ) ? $_GET['email_id'] : 0;
if ( $email_id > 0 ) {

    $email = new YITH_WCCH_Email( $email_id );
    $sender = ( isset( $email->sender_id ) && $email->sender_id > 0 ) ? get_user_by( 'id', $email->sender_id ) : null;
    $user = get_user_by( 'id', $email->user_id );

    $sender_name = is_object( $sender ) ? $sender->display_name : __( 'Not available', 'yith-woocommerce-customer-history' );
    $sender_email = is_object( $sender ) ? $sender->user_email : __( 'Not available', 'yith-woocommerce-customer-history' );
    $user_name = $user->display_name . ' (' . $user->user_email . ')';
    $email_subject = $email->subject;
    $email_content = $email->content;

}

?>

<div id="yith-woocommerce-customer-history">
    <div id="email" class="wrap">

        <h1>
            <?php echo __( 'Email', 'yith-woocommerce-customer-history' ); ?>
            <a href="admin.php?page=yith-wcch-email.php" class="page-title-action"><?php echo __( 'Send new email', 'yith-woocommerce-customer-history' ); ?></a>
        </h1>

        <!--<p><?php // echo __( 'Set default "Sender" name and email values in <a href="admin.php?page=yith-wcch-settings.php">plugin settings</a>.', 'yith-woocommerce-customer-history' ); ?></p>-->

        <?php if ( $email_id > 0 ) : ?>

            <hr />

            <p>
                <span style="display: inline-block; width: 100px;"><?php echo __( 'From', 'yith-woocommerce-customer-history' ); ?>:</span> <strong><?php echo $sender_name . ' (' . $sender_email . ')'; ?></strong><br />
                <span style="display: inline-block; width: 100px;"><?php echo __( 'To', 'yith-woocommerce-customer-history' ); ?>:</span> <strong><?php echo $user_name; ?></strong><br />
                <span style="display: inline-block; width: 100px;"><?php echo __( 'Subject', 'yith-woocommerce-customer-history' ); ?>:</span> <strong><?php echo $email_subject; ?></strong>
            </p>

            <p style="background-color: #fff; border: 1px solid #ddd; padding: 10px;"><?php echo nl2br( $email_content ); ?></p>

        <?php else : ?>

            <form id="group-form" action="admin.php" method="get">

                <input type="hidden" name="page" value="yith-wcch-email.php">
                <input type="hidden" name="act" value="send">

                <table class="form-table">
                    <tbody>
                        <!--
                        <tr>
                            <th scope="row"><label for="sender_name"><?php echo __( 'Sender Name', 'yith-woocommerce-customer-history' ); ?></label></th>
                            <td><input name="sender_name" type="text" class="regular-text" placeholder="<?php echo get_bloginfo('name'); ?>" value="<?php echo get_option( 'yith-wcch-default-sender-name' ); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="sender_email"><?php echo __( 'From', 'yith-woocommerce-customer-history' ); ?></label></th>
                            <td><input name="sender_email" type="text" class="regular-text" placeholder="<?php echo get_bloginfo('admin_email'); ?>" value="<?php echo get_option( 'yith-wcch-default-sender-email' ); ?>"></td>
                        </tr>
                        -->
                        <tr>
                            <th scope="row"><label for="sender_id"><?php echo __( 'From', 'yith-woocommerce-customer-history' ); ?></label></th>
                            <td>
                                <select name="sender_id" class="user-select2" disabled="disabled">
                                    <?php echo '<option value="' . $sender->ID . '" ' . ( isset( $_GET['customer_id'] ) && $_GET['customer_id'] == $sender->ID ? ' selected="selected"' : '' ) . '>' . $sender->display_name . ' (' . $sender->user_email . ')</option>'; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="user_id"><?php echo __( 'To', 'yith-woocommerce-customer-history' ); ?></label></th>
                            <td>
                                <select name="user_id" class="user-select2">
                                    <!-- <option value="0">-</option> -->
                                    <?php foreach ( get_users() as $key => $value ) { echo '<option value="' . $value->ID . '" ' . ( isset( $_GET['customer_id'] ) && $_GET['customer_id'] == $value->ID ? ' selected="selected"' : '' ) . '>' . $value->display_name . ' (' . $value->user_email . ')</option>'; } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="subject"><?php echo __( 'Subject', 'yith-woocommerce-customer-history' ); ?></label></th>
                            <td><input name="subject" type="text" value="" class="large-text" placeholder="Subject"></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="name"><?php echo __( 'Content', 'yith-woocommerce-customer-history' ); ?></label></th>
                            <td><textarea name="content" rows="20" style="width: 100%;"><?php echo $email_content; ?></textarea></td>
                        </tr>
                        <tr>
                            <th scope="row"></th>
                            <td><input type="submit" value="<?php echo __( 'Send', 'yith-woocommerce-customer-history' ); ?>" class="button button-primary button-large"></td>
                        </tr>
                    </tbody>
                </table>

            </form>

        <?php endif; ?>

    </div>
</div>

<script type="text/javascript">
    jQuery(".user-select2").select2();
    jQuery( document ).ready(function() { yit_open_admin_menu( 'toplevel_page_yith-wcch-customers' ); });
</script>
