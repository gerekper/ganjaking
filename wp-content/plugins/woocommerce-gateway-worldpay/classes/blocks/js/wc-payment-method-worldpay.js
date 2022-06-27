(function () { 

  // Imports
  const { __ }                            = wp.i18n;
  const { decodeEntities }                = wp.htmlEntities;
  const { getSetting }                    = wc.wcSettings;
  const { registerPaymentMethod }         = wc.wcBlocksRegistry;
  const { registerExpressPaymentMethod }  = wc.wcBlocksRegistry;
  const { applyFilters }                  = wp.hooks;

  // Data
  const settings      = getSetting('worldpay_data', {});
  const defaultLabel  = WorldpayLocale['Worldpay'];
  const label         = decodeEntities(settings.title) || defaultLabel;
  const iconsrc       = settings.iconsrc;
  const poweredbywp   = settings.poweredbywp;

  const iconOutput    = getWorldpayIcons( settings.iconsrc );
  

  const Content = () => {
    return decodeEntities( settings.description || '' );
  };

  const Label = props => {
        var label = null;

        if ( poweredbywp != '' ) {
            const icon = React.createElement('img', { alt: __( 'Powered by Worldpay', 'woocommerce_worlday' ), title: __( 'Powered by Worldpay', 'woocommerce_worlday' ), className: 'powered-by-worldpay', src:poweredbywp});
            label = icon;
        } else {
          const { PaymentMethodLabel } = props.components;
          label = React.createElement( PaymentMethodLabel, { text: label, icon: icon } );
        }

        return applyFilters( 'wc_checkout_label_worldpay', label, settings );
  };

  function getWorldpayIcons( iconsrc ){

    return Object.entries( iconsrc ).map(
      ( [ id, { src, alt } ] ) => {
        return {
          id,
          src,
          alt,
        };
      }
    );

  }

  function getIconOutput( index ) {
    return output;
  }

  const IconHTML = () => {
    return React.createElement('img', { alt: __( 'Powered by Worldpay', 'woocommerce_worlday' ), title: __( 'Powered by Worldpay', 'woocommerce_worlday' ), className: 'powered-by-worldpay', src:poweredbywp});
  };
  
  const WorldpayPaymentMethod = {
        name: 'worldpay',
        label: React.createElement( Label, null ),
        content: React.createElement( Content, null ),
        edit: React.createElement( Content, null ),
        placeOrderButtonLabel: WorldpayLocale['Proceed to Worldpay'],
        canMakePayment: () => true,
        ariaLabel: label
  };

  // Register Worldpay
  registerPaymentMethod( WorldpayPaymentMethod );

}());