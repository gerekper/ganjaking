<div class="woocommerce_gpf_settings">
	<h3><?php _e( 'Settings for your store', 'woocommerce_gpf' ); ?></h3>
	<div style="position: relative;">
        <div class="woocommerce_gpf_intro_support_info">
            <h3>Support</h3>
            <hr>
            <ul class="ul-disc">
                <li><a href="https://docs.woocommerce.com/document/google-product-feed-setting-up-your-feed-google-merchant-centre">Set up guide</a></li>
                <li><a href="https://docs.woocommerce.com/document/google-product-feed-setting-product-data">Setting product data</a>
                </li>
                <li><a href="https://docs.woocommerce.com/document/google-product-feed-feed-generation-options">Feed generation options</a></li>
                <li><a href="https://docs.woocommerce.com/document/google-product-feed-customizations">Customizations</a></li>
            </ul>
            <br>
            <center>
                <a class="button button-primary" target="_blank" href="https://docs.woocommerce.com/document/google-product-feed-troubleshooting">Troubleshooting info</a><br>&mdash;<br>
                <a target="_blank" href="https://woocommerce.com/my-account/create-a-ticket/">Open support ticket</a>
            </center>
            <br>
        </div>
        {cache_status}
        <div">
            <p><?php _e( 'This page allows you to control what data is added to your product feeds.', 'woocommerce_gpf' ); ?></p>
            <p><?php _e( 'Choose the fields you want to include here, and also set store-wide defaults. You can also set defaults against categories, or provide information on each product page. You can choose to have the produt feed data prepopulated from existing taxonomies, or fields. If you add a new custom field then you may need to <a href="{refresh_fields_url}">refresh the field list</a> before it will be available for selection.', 'woocommerce_gpf' ); ?></p>
            <h4><?php _e( 'Notes about Google', 'woocommerce_gpf' ); ?></h4>
            <p><?php _e( "Depending on what you sell, and where you are selling it to Google apply different rules as to which information you should supply. You can find Google's list of what information is required on ", 'woocommerce_gpf' ); ?><a href="http://www.google.com/support/merchants/bin/answer.py?answer=188494" rel="nofollow"><?php _e( 'this page', 'woocommerce_gpf' ); ?></a></p>
            {enablement}
        </div>
    </div>
