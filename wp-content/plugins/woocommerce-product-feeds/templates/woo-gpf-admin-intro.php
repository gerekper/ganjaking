<div class="woocommerce_gpf_settings">
	<h3><?php esc_html_e( 'Settings for your store', 'woocommerce_gpf' ); ?></h3>
	<div style="position: relative;">
        <div class="woocommerce_gpf_intro_support_info">
            <h3>Support</h3>
            <hr>
            <ul class="ul-disc">
                <li><a target="_black" rel="noreferrer noopener" href="https://woocommerce.com/document/google-product-feed-setting-up-your-feed-google-merchant-centre/"><?php esc_html_e( 'Set up guide', 'woocommerce_gpf' ); ?></a></li>
                <li><a target="_black" rel="noreferrer noopener" href="https://woocommerce.com/document/google-product-feed-setting-product-data/"><?php esc_html_e( 'Setting product data', 'woocommerce_gpf' ); ?></a>
                </li>
                <li><a target="_black" rel="noreferrer noopener" href="https://woocommerce.com/document/google-product-feed-feed-generation-options/"><?php esc_html_e( 'Feed generation options', 'woocommerce_gpf' ); ?></a></li>
                <li><a target="_black" rel="noreferrer noopener" href="https://woocommerce.com/document/google-product-feed-customizations/"><?php esc_html_e( 'Customizations', 'woocommerce_gpf' ); ?></a></li>
                <li><a target="_black" rel="noreferrer noopener" href="https://woocommerce.com/feature-requests/google-product-feed/"><?php esc_html_e( 'Feature requests', 'woocommerce_gpf' ); ?></a></li>
            </ul>
            <br>
            <center>
                <a class="button button-primary" target="_blank" href="https://woocommerce.com/document/google-product-feed-troubleshooting/"><?php esc_html_e( 'Troubleshooting info', 'woocommerce_gpf' ); ?></a><br>&mdash;<br>
                <a target="_blank" href="https://woocommerce.com/my-account/create-a-ticket/"><?php esc_html_e( 'Open support ticket', 'woocommerce_gpf' ); ?></a>
            </center>
            <br>
        </div>
        {cache_status}
        <div">
            <p><?php esc_html_e( 'This page allows you to control what data is added to your product feeds.', 'woocommerce_gpf' ); ?></p>
            <p><?php printf( esc_html( __( 'Choose the fields you want to include here, and also set store-wide defaults. You can also set defaults against categories, or provide information on each product page. You can choose to have the product feed data prepopulated from existing taxonomies, or fields. If you add a new custom field then you may need to %1$srefresh the field list%2$s before it will be available for selection.', 'woocommerce_gpf' ) ), '<a href="{refresh_fields_url}">', '</a>' ); ?></p>
            <h4><?php esc_html_e( 'Notes about Google', 'woocommerce_gpf' ); ?></h4>
            <p><?php esc_html_e( "Depending on what you sell, and where you are selling it to Google apply different rules as to which information you should supply. You can find Google's list of what information is required on ", 'woocommerce_gpf' ); ?><a href="http://www.google.com/support/merchants/bin/answer.py?answer=188494" rel="nofollow"><?php esc_html_e( 'this page', 'woocommerce_gpf' ); ?></a></p>
            {active_feeds}
        </div>
    </div>
