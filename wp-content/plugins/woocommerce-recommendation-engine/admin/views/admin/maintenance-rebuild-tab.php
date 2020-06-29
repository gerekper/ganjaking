<h3><?php _e( 'Rebuild Recommendations', 'wc_recommender' ); ?></h3>
<form method="POST">

    <div id="wc-recommender-complete" style="display:none;">
        <p><?php _e( 'Rebuild of recommendations complete', 'wc_recommender' ); ?></p>
    </div>

    <div id="wc-recommender-start">
        <label fo="rebuild-recommendations-type"><?php _e( 'Rebuild recommendations for:', 'wc_recommender' ); ?></label><br />
        <select id="rebuild-recommendations-type" name="rebuild-recommendations-type">
            <option value="viewed"><?php _e( 'Also Viewed', 'wc_recommender' ); ?></option>
            <option value="purchased"><?php _e( 'Also Purchased', 'wc_recommender' ); ?></option>
            <option value="purchased-together"><?php _e( 'Purchased Together', 'wc_recommender' ); ?></option>
        </select>

        <br />
        <input class='button primary' id="rebuild-recommendations" type="button" value="<?php _e( 'Rebuild', 'wc_recommender' ); ?>"/>


    </div>

    <div id="wc-recommender-status" style="display:none;">
        <p><?php _e( 'Building Recommendations:', 'wc_recommender' ); ?> <span id="next_start">0</span> through
            <span id="through"></span> of <span id="total">...</span></p>
        <p><?php _e( 'Estimated Time Remaining:', 'wc_recommender' ); ?> <span id="remaining">...</p>
    </div>

</form>

<script type="text/javascript">


</script>
