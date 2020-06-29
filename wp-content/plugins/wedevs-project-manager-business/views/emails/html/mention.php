<?php

$tpbk = config('frontend.assets_url') .'images/tpbk.png';

?>

<div style="width:600px;  background: #fff;">
    <div style="width: 600px;">
        <div style="height: 174px; width: 600px;">
            <div style="font-family: 'Lato', sans-serif; font-wight: bold; color: #333; font-size: 16px; padding-top: 20px; text-align: center; padding-right: 40px; padding-left: 40px;">
                <?php
                    _e( 'Hello', 'pm-pro' );
                    // echo ' <strong>' . $user . '</strong>. ';
                    echo '. ';
                    _e( 'You are mentioned in a comment by', 'pm-pro' );
                    echo ' <strong>' . $creator . '</strong>. ';
                    _e('Please click the link bellow to view the comment.', 'pm-pro' ); 
                ?>
            </div>
            <div style="font-family: 'Lato', sans-serif; font-wight: bold; color: #333; font-size: 16px; padding-top: 20px; text-align: center; padding-right: 40px; padding-left: 40px;">
                <?php echo $content; ?>
            </div>
            <div style="padding: 18px; margin: 30px auto 45px; border-radius: 30px; background: #00b1e7; width: 171px;text-align: center;">
                <a href="<?php echo $comment_link; ?>" style="font-family: lato; font-size: 16px; text-decoration: none; color: #fff;">
                    <?php _e( 'View Comment', 'pm-pro' ); ?>
                </a>

            </div>

        </div>
    </div>
</div>

