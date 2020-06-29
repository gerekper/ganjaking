<div class="invoice-container">
    <div class="invoice-heading">
        <h2 class="invoice-number"><?php _ex( 'Invoice {{number}}', 'Invoice template', 'yith-woocommerce-affiliates' ) ?></h2>
        <h1>{{title}}</h1>
    </div>

    <div class="invoice-date">
        <?php _ex( 'Date: {{current_date}}', 'Invoice template', 'yith-woocommerce-affiliates' ) ?>
    </div>

    <div class="invoice-addresses">
        <table class="addresses">
            <tr>
                <td class="affilliate-address">
                    <h3><?php _ex( 'Affiliate', 'Invoice template', 'yith-woocommerce-affiliates' ) ?></h3>

                    {{first_name}} {{last_name}}<br/>
                    {{company}}<br/>
                    {{billing_address_1}}, {{billing_city}} {{billing_postcode}}<br/>
                    {{billing_state}} {{billing_country}}<br/>
                    {{cif}}<br/>
                    {{vat}}<br/>

                </td>

                <td class="company-details">
                    <h3><?php _ex( 'Client', 'Invoice template', 'yith-woocommerce-affiliates' ) ?></h3>
                    {{company_section}}
                </td>
            </tr>
        </table>
    </div>

    <div class="invoice-content">
        <p>
            <?php _ex( 'Description: {{description}}', 'Invoice template', 'yith-woocommerce-affiliates' ) ?>
        </p>
        <p>
            <?php
                _ex(
                    "In reference to {{affiliate_program}} ({{affiliate_landing}}), 
                    I ask for a payment of {{withdraw_amount}} for commissions earned on {{site_url}} from {{start_date}} to {{end_date}}", 'Invoice template', 'yith-woocommerce-affiliates' )
            ?>
        </p>
        <p>
            <?php
                _ex( 'With the proving documentation of the due payment attachment, the following document represents a valid invoice for fiscal purposes.', 'Invoice template', 'yith-woocommerce-affiliates' )
            ?>
        </p>
    </div>

    <div class="invoice-totals">
        <table class="totals">
            <tr>
                <td class="total-description"><?php _ex( 'Commissions on {{site_url}}', 'Invoice template', 'yith-woocommerce-affiliates' ) ?></td>
                <td class="total">{{withdraw_amount}}</td>
            </tr>
            <tr class="grand-total">
                <td class="total-description"><?php _ex( 'Total', 'Invoice template', 'yith-woocommerce-affiliates' )?></td>
                <td class="total">{{withdraw_amount}}</td>
            </tr>
        </table>
    </div>
</div>