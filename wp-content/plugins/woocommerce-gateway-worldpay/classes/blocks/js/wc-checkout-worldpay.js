
(function () { 


// Imports
const { __ } = wp.i18n;
const { decodeEntities }  = wp.htmlEntities;
const { getSetting }  = wc.wcSettings;
const { registerPaymentMethod }  = wc.wcBlocksRegistry;
const { applyFilters } = wp.hooks;

//( 'hookName', content, arg1, arg2, ... )


// Data
const settings = getSetting('worldpay_data', {});
const defaultLabel = WorldpayLocale['Worldpay'];
const label = decodeEntities(settings.title) || defaultLabel;
const iconsrc = settings.iconsrc;


const Content = () => {
        var content = React.createElement(
		'div',
		null,
		decodeEntities(settings.description || '')
	);
       return applyFilters('wc_worldpay_checkout_description', content, settings);
};

const Label = props => {
        var label = null;
        if (iconsrc != '') {
            const icon = React.createElement('img', { alt: label, title: label, className: 'worldpay-payment-logo', src:iconsrc});
            label = icon;
        } else {
          // Just do a text label if no icon is passed (this is filterable) IOK 2020-08-10
	  const { PaymentMethodLabel } = props.components;
          label = React.createElement(PaymentMethodLabel, { text: label, icon: icon });
        }
        return applyFilters('wc_worldpay_checkout_label', label, settings);
};

const canMakePayment = (args) => {
 var candoit = true;
 return applyFilters('wc_worldpay_show_checkout_block', candoit, settings);
};

/**
 * Vipps  payment method config object.
 */
const WorldpayPaymentMethod = {
      name: 'worldpay',
      label: React.createElement(Label, null),
      content: React.createElement(Content, null),
      edit: React.createElement(Content, null),
      placeOrderButtonLabel: WorldpayLocale['Continue with Worldpay'],
      icons: null,
      canMakePayment: canMakePayment,
      ariaLabel: label
};

registerPaymentMethod( WorldpayPaymentMethod );

}());