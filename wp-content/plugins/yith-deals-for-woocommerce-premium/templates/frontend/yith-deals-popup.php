<?php

?>
<div class="yith-wcdls-popup yith-wcdls-style" id="yith-wcdls-popup">

<div class="yith-wcdls-overlay<?php echo ('inline' == $animation) ? '-inline' : ''  ?>"></div>

<div class="yith-wcdls-wrapper<?php echo ('inline' == $animation) ? '-inline' : ''  ?> woocommerce">

    <div class="yith-wcdls-main<?php echo ('inline' == $animation) ? '-inline' : ''  ?>">

        <div class="yith-wcdls-head">
            <a href="#" class="yith-wcdls-close">X <?php echo esc_html__('Close', 'yith-deals-for-woocommerce') ?></a>
        </div>

        <div class="yith-wcdls-content entry-content" data-offer_id="<?php echo $offer_id?>" data-animation="<?php echo $animation ?>">
            <?php echo $content ?>
        </div>

    </div>

</div>

</div>